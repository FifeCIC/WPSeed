# REST Bridge

> How to register, discover, and use REST API endpoints in WPSeed-based plugins.
>
> The REST Bridge wraps `register_rest_route()` with a central registry,
> capability-based permissions via the Capability Manager, and automatic
> route generation for API connectors. Every REST endpoint should go through
> the Bridge so the ecosystem can discover what's available.

---

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                  Your Controller                             │
│                                                              │
│   $this->register_endpoint( '/projects', array(              │
│       'method'     => 'GET',                                 │
│       'callback'   => array( $this, 'get_projects' ),        │
│       'capability' => 'wpseed_manage_settings',              │
│       'label'      => 'List Projects',                       │
│   ) );                                                       │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌──────────────────────────────────────────────────────────────┐
│                    REST_Bridge                                │
│                                                              │
│   Static registry ──────────── get_registered_endpoints()    │
│   Permission factory ───────── wpseed_user_can() checks      │
│   Connector route generator ── register_connector_routes()   │
│   Response helpers ─────────── success() / error()           │
│                                                              │
│   Deferred registration ────── rest_api_init hook            │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌──────────────────────────────────────────────────────────────┐
│              WordPress register_rest_route()                  │
└──────────────────────────────────────────────────────────────┘
```

---

## Quick Start

### Register an endpoint from a controller

```php
class My_Controller extends \WPSeed\API\REST_Controller {

    protected $rest_base = 'projects';

    public function register_routes() {
        $this->register_endpoint( '/projects', array(
            'method'      => 'GET',
            'callback'    => array( $this, 'get_projects' ),
            'capability'  => 'wpseed_manage_settings',
            'label'       => 'List Projects',
            'description' => 'Returns all projects.',
        ) );

        $this->register_endpoint( '/projects', array(
            'method'      => 'POST',
            'callback'    => array( $this, 'create_project' ),
            'capability'  => 'wpseed_manage_settings',
            'label'       => 'Create Project',
            'description' => 'Creates a new project.',
            'args'        => array(
                'title' => array(
                    'required' => true,
                    'type'     => 'string',
                ),
            ),
        ) );
    }
}
```

### Register an endpoint without a controller

```php
use WPSeed\API\REST_Bridge;

REST_Bridge::register( 'wpseed/v1', '/status', array(
    'method'      => 'GET',
    'callback'    => function() {
        return REST_Bridge::success( array(
            'version' => WPSEED_VERSION,
            'php'     => PHP_VERSION,
        ) );
    },
    'capability'  => 'wpseed_manage_settings',
    'label'       => 'Plugin Status',
    'description' => 'Returns plugin version and environment info.',
) );
```

### Public endpoints (no authentication)

```php
REST_Bridge::register( 'wpseed/v1', '/public/info', array(
    'method'     => 'GET',
    'callback'   => function() { return array( 'name' => 'WPSeed' ); },
    'capability' => 'public',
    'label'      => 'Public Info',
) );
```

---

## Connector Routes

The REST Bridge can auto-generate REST endpoints for any registered connector.
This is how Amazon Q (or any external tool) talks to external APIs through
WordPress.

### Register connector routes

```php
use WPSeed\API\REST_Bridge;

REST_Bridge::register_connector_routes( 'wpseed/v1', 'github' );
```

This generates three endpoints:

| Method | Route | What it does |
|---|---|---|
| `GET` | `/connector/github/test` | Calls `test_connection()` |
| `GET` | `/connector/github/capabilities` | Calls `get_capabilities()` |
| `POST` | `/connector/github/{action}` | Calls `execute( $action, $params )` |

### Use connector routes

```bash
# Test connection
curl -X GET https://example.com/wp-json/wpseed/v1/connector/github/test \
  -H "X-WP-Nonce: {nonce}"

# List capabilities
curl -X GET https://example.com/wp-json/wpseed/v1/connector/github/capabilities \
  -H "X-WP-Nonce: {nonce}"

# Execute an action
curl -X POST https://example.com/wp-json/wpseed/v1/connector/github/create_issue \
  -H "X-WP-Nonce: {nonce}" \
  -H "Content-Type: application/json" \
  -d '{"repo": "evolvewp/evolvewp-core", "title": "Bug fix", "body": "Details..."}'
```

### Amazon Q integration

Q calls the REST API via curl, never external APIs directly:

```
Q → curl → /wp-json/wpseed/v1/connector/github/create_issue
         → REST_Bridge → Connector_Interface::execute()
         → Base_API::make_request() → GitHub API
```

---

## Response Helpers

The Bridge provides opt-in response helpers. Callbacks are NOT required to
use them — they can return any data shape they want.

### Success response

```php
return REST_Bridge::success( $data );
// { "success": true, "data": {...} }

return REST_Bridge::success( $data, 201 );
// Same, with HTTP 201 Created
```

### Error response

```php
return REST_Bridge::error( 'not_found', 'Project not found.', 404 );
// { "success": false, "error": { "code": "not_found", "message": "Project not found." } }

// From a WP_Error:
$result = $connector->execute( 'create_issue', $params );
if ( is_wp_error( $result ) ) {
    return REST_Bridge::error( $result );
}
```

---

## Endpoint Discovery

### List all registered endpoints

```php
$all = wpseed_rest_endpoints();

// Filter by source
$manual    = wpseed_rest_endpoints( 'manual' );
$connector = wpseed_rest_endpoints( 'connector' );
```

Each endpoint entry contains:

```php
array(
    'namespace'   => 'wpseed/v1',
    'route'       => '/projects',
    'method'      => 'GET',
    'capability'  => 'wpseed_manage_settings',
    'label'       => 'List Projects',
    'description' => 'Returns all projects.',
    'source'      => 'manual',  // or 'connector'
    'args'        => array( ... ),
)
```

This powers:
- Admin UI endpoint catalogue (development tabs)
- EvolveWP Core Feature Gate (knows what's available)
- AI tools (discover actions without hardcoding)

---

## Hooks

### wpseed_rest_bridge_before_register

Fires before routes are registered with WordPress. Last chance to call
`REST_Bridge::register()` or `REST_Bridge::register_connector_routes()`.

```php
add_action( 'wpseed_rest_bridge_before_register', function() {
    REST_Bridge::register( 'wpseed/v1', '/late-endpoint', array( ... ) );
} );
```

### wpseed_rest_bridge_registered

Fires after all routes are registered. Receives the full endpoint metadata array.

```php
add_action( 'wpseed_rest_bridge_registered', function( $endpoints ) {
    // Log how many endpoints were registered.
} );
```

---

## File Reference

| File | Purpose |
|---|---|
| `includes/API/REST_Bridge.php` | Registry, connector routing, response helpers |
| `includes/API/REST_Controller.php` | Base controller with `register_endpoint()` |
| `functions.php` | `wpseed_rest_endpoints()` global accessor |
| `includes/Hook_Registry.php` | Documents all REST Bridge hooks |

---

## When Cloning to a New Plugin

After running the WPSeed cloning process:

1. The REST Bridge class is renamed with your plugin's prefix.
2. The namespace changes (e.g. `wpseed/v1` → `evolvewp-core/v1`).
3. The `wpseed_rest_endpoints()` function becomes `yourprefix_rest_endpoints()`.
4. Connector route capabilities change to your prefix.
5. Register your plugin's own endpoints via `register_endpoint()` in controllers.
