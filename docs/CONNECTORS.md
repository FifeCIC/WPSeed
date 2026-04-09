# Connector System

> How to build, register, and use API connectors in WPSeed-based plugins.
>
> The connector system provides a standard way to integrate with external APIs.
> Every connector implements the same three-method interface, making them
> interchangeable from the perspective of the Factory, Directory, and REST Bridge.

---

## Architecture Overview

```
┌─────────────────────────────────────────────────────────┐
│                    Your Plugin Code                      │
│                                                          │
│   $github = wpseed_connector( 'github' );               │
│   $result = $github->execute( 'create_issue', $params ); │
└──────────────┬───────────────────────────────────────────┘
               │
               ▼
┌──────────────────────┐     ┌──────────────────────────┐
│   WPSeed_API_Factory │────▶│  WPSeed_API_Directory    │
│                      │     │                          │
│  - Loads credentials │     │  - Provider registry     │
│  - Instantiates class│     │  - Static + runtime      │
│  - Validates interface│    │  - Capability discovery  │
│  - Caches instances  │     └──────────────────────────┘
└──────────┬───────────┘
           │
           ▼
┌──────────────────────────────────────────────────────────┐
│              Connector_Interface                          │
│                                                          │
│   test_connection()  → Can I reach this API?             │
│   get_capabilities() → What actions do I support?        │
│   execute($action)   → Run a named action                │
└──────────────────────────────────────────────────────────┘
           │
           ▼
┌──────────────────────────────────────────────────────────┐
│                    Base_API                               │
│                                                          │
│   - HTTP request handling (GET/POST/PUT/PATCH/DELETE)    │
│   - Bearer token authentication                          │
│   - API call logging via WPSeed_API_Logging              │
│   - Error handling with WP_Error                         │
│   - Connection test helpers                              │
└──────────────────────────────────────────────────────────┘
           │
           ▼
┌──────────────────────────────────────────────────────────┐
│              Your_Connector extends Base_API              │
│                                                          │
│   - Implements test_connection()                         │
│   - Implements get_capabilities()                        │
│   - Implements execute() with action routing             │
│   - Optionally overrides get_headers(), get_timeout()    │
└──────────────────────────────────────────────────────────┘
```

---

## Quick Start: Building a Connector

### 1. Create the connector class

Create a new file in your plugin's `api/` directory. The class extends
`\WPSeed\API\Base_API` and implements the three interface methods.

```php
<?php
/**
 * GitHub API connector.
 *
 * ROLE: api-endpoint
 *
 * @package  MyPlugin\API
 * @since    1.0.0
 */

namespace MyPlugin\API;

use WPSeed\API\Base_API;

class GitHub_Connector extends Base_API {

    /**
     * Test the GitHub API connection.
     *
     * @since  1.0.0
     * @return array { success: bool, message: string, data: array }
     */
    public function test_connection() {
        $result = $this->make_request( '/user' );

        if ( is_wp_error( $result ) ) {
            return $this->connection_failure( $result->get_error_message() );
        }

        return $this->connection_success(
            'Connected as ' . $result['login'],
            array( 'username' => $result['login'] )
        );
    }

    /**
     * Declare supported actions.
     *
     * @since  1.0.0
     * @return array<string, array>
     */
    public function get_capabilities() {
        return array(
            'list_repos'   => array(
                'label'       => 'List Repositories',
                'description' => 'Retrieve repositories for the authenticated user.',
                'method'      => 'GET',
            ),
            'get_issues'   => array(
                'label'       => 'Get Issues',
                'description' => 'List issues for a repository.',
                'method'      => 'GET',
                'params'      => array( 'repo', 'state' ),
            ),
            'create_issue' => array(
                'label'       => 'Create Issue',
                'description' => 'Create a new issue in a repository.',
                'method'      => 'POST',
                'params'      => array( 'repo', 'title', 'body', 'labels' ),
            ),
        );
    }

    /**
     * Execute a named action.
     *
     * @since  1.0.0
     *
     * @param  string $action Action name.
     * @param  array  $params Action parameters.
     * @return array|\WP_Error
     */
    public function execute( $action, $params = array() ) {
        switch ( $action ) {
            case 'list_repos':
                return $this->make_request( '/user/repos', array(
                    'sort'      => $params['sort'] ?? 'updated',
                    'direction' => 'desc',
                    'per_page'  => $params['per_page'] ?? 30,
                ) );

            case 'get_issues':
                return $this->make_request(
                    '/repos/' . $params['repo'] . '/issues',
                    array( 'state' => $params['state'] ?? 'open' )
                );

            case 'create_issue':
                return $this->make_request(
                    '/repos/' . $params['repo'] . '/issues',
                    array(
                        'title'  => $params['title'],
                        'body'   => $params['body'] ?? '',
                        'labels' => $params['labels'] ?? array(),
                    ),
                    'POST'
                );

            default:
                return parent::execute( $action, $params );
        }
    }
}
```

### 2. Register the connector

In your plugin's init, register the connector with the API Directory:

```php
add_action( 'init', function() {
    WPSeed_API_Directory::register( 'github', array(
        'name'        => 'GitHub',
        'description' => 'GitHub repository and issue management.',
        'url'         => 'https://github.com',
        'api_doc_url' => 'https://docs.github.com/en/rest',
        'class_name'  => 'MyPlugin\\API\\GitHub_Connector',
        'class_path'  => '',  // Empty — autoloaded via Composer.
        'auth_type'   => 'bearer',
        'icon'        => 'dashicons-editor-code',
    ) );
} );
```

If the class is NOT autoloaded via Composer, provide the `class_path` relative
to the plugin's `api/` directory:

```php
'class_path' => 'github/github-connector.php',
```

### 3. Use the connector

```php
// Get a connector instance (credentials loaded from wp_options).
$github = wpseed_connector( 'github' );

if ( is_wp_error( $github ) ) {
    // Provider not found or class missing.
    return;
}

// Test the connection.
$test = $github->test_connection();
if ( ! $test['success'] ) {
    error_log( 'GitHub connection failed: ' . $test['message'] );
    return;
}

// Execute an action.
$issues = $github->execute( 'get_issues', array(
    'repo'  => 'evolvewp/evolvewp-core',
    'state' => 'open',
) );

if ( is_wp_error( $issues ) ) {
    error_log( 'Failed to get issues: ' . $issues->get_error_message() );
    return;
}

// $issues is now a parsed JSON array of GitHub issues.
```

---

## Credential Storage

Credentials are stored in `wp_options` using a standard naming convention:

```
wpseed_api_{provider_id}_key       → API key / access token
wpseed_api_{provider_id}_secret    → API secret (if needed)
wpseed_api_{provider_id}_url       → Base URL (if configurable)
```

### Multi-Account Support

When a connector supports multiple accounts (e.g. two GitHub tokens for
different organisations), an account ID is inserted:

```
wpseed_api_{provider_id}_{account_id}_key
wpseed_api_{provider_id}_{account_id}_secret
wpseed_api_{provider_id}_{account_id}_url
```

Usage:

```php
// Default account.
$github = wpseed_connector( 'github' );

// Named account.
$github_org = wpseed_connector( 'github', 'my-org' );
```

### Injecting Credentials from Other Sources

Use the `wpseed_connector_credentials` filter to load credentials from
environment variables, a secrets manager, or any other source:

```php
add_filter( 'wpseed_connector_credentials', function( $args, $provider_id, $account_id ) {
    if ( 'github' === $provider_id ) {
        $env_token = getenv( 'GITHUB_TOKEN' );
        if ( $env_token ) {
            $args['api_key']  = $env_token;
            $args['base_url'] = 'https://api.github.com';
        }
    }
    return $args;
}, 10, 3 );
```

---

## Customising Authentication

The default `get_headers()` sends a Bearer token. Override it for APIs that
use different authentication:

```php
// Basic Auth
protected function get_headers() {
    return array(
        'Content-Type'  => 'application/json',
        'Accept'        => 'application/json',
        'Authorization' => 'Basic ' . base64_encode( $this->api_key . ':' . $this->api_secret ),
    );
}

// Custom header
protected function get_headers() {
    return array(
        'Content-Type' => 'application/json',
        'Accept'       => 'application/json',
        'X-API-Key'    => $this->api_key,
    );
}
```

---

## Capability Discovery

The API Directory can query any connector's capabilities without executing
actions. This powers the admin UI feature matrix and the REST Bridge's
action validation.

```php
// From the Directory (no credentials needed for capability listing).
$caps = WPSeed_API_Directory::get_provider_capabilities( 'github' );

// From an instance.
$github = wpseed_connector( 'github' );
$caps   = $github->get_capabilities();

// $caps = array(
//     'list_repos'   => array( 'label' => 'List Repositories', ... ),
//     'create_issue' => array( 'label' => 'Create Issue', ... ),
// )
```

---

## Filters Reference

### wpseed_api_providers

Filter the complete list of registered connector providers.

```php
add_filter( 'wpseed_api_providers', function( $providers ) {
    // Remove a provider.
    unset( $providers['discord'] );

    // Add a provider.
    $providers['slack'] = array(
        'name'       => 'Slack',
        'class_name' => 'My_Slack_Connector',
        // ...
    );

    return $providers;
} );
```

### wpseed_connector_credentials

Filter credentials before a connector is instantiated.

Parameters: `$args` (array), `$provider_id` (string), `$account_id` (string).

See "Injecting Credentials from Other Sources" above.

### wpseed_connector_request_args

Filter the `wp_remote_request()` arguments before every API call.

```php
add_filter( 'wpseed_connector_request_args', function( $args, $url, $provider_id, $endpoint ) {
    if ( 'github' === $provider_id ) {
        $args['headers']['X-GitHub-Api-Version'] = '2022-11-28';
    }
    return $args;
}, 10, 4 );
```

---

## File Reference

| File | Purpose |
|---|---|
| `includes/API/Connector_Interface.php` | Interface contract (test, capabilities, execute) |
| `includes/API/Base_API.php` | Abstract base class implementing the interface |
| `api/api-directory.php` | Provider registry (static + runtime) |
| `api/api-factory.php` | Instance creation with credential injection |
| `functions.php` | `wpseed_connector()` global accessor |
| `includes/api-logging.php` | API call logging (used by Base_API) |
| `includes/Hook_Registry.php` | Documents all connector-related hooks |

---

## When Cloning to a New Plugin

After running the WPSeed cloning process (see `docs/CLONING-GUIDE.md`):

1. The interface, base class, directory, and factory are all renamed with
   your plugin's prefix automatically.
2. Remove the default providers (custom_api, discord) from the Directory's
   `get_default_providers()` method.
3. Create your plugin's own connectors extending the renamed Base_API.
4. Register them via the renamed Directory's `register()` method.

The `wpseed_connector()` function becomes `yourprefix_connector()` and
all option names change from `wpseed_api_*` to `yourprefix_api_*`.
