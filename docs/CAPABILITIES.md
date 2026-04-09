# Capability Manager

> How to register, check, and manage custom capabilities in WPSeed-based plugins.
>
> The Capability Manager provides a standard way to declare permissions with
> metadata, install them into WordPress roles on activation, and check them
> at runtime. Every permission check in the plugin goes through one function:
> `wpseed_user_can()`.

---

## Why Not Just Use current_user_can()?

You can. WordPress capabilities work fine on their own. The Capability Manager
adds three things:

1. **Metadata** — each capability has a label, description, and group. This
   powers the admin UI permissions panel without hardcoding cap names in templates.

2. **Lifecycle management** — capabilities are installed into roles on activation
   and removed on uninstall automatically. No manual role manipulation needed.

3. **Override hook** — the `wpseed_user_can` filter lets EvolveWP Core intercept
   permission checks across all ecosystem plugins for cross-plugin permission
   management, two-person authorisation, and audit logging.

---

## Quick Start

### Register a capability

Call this during plugin init or earlier. Registration just declares the cap —
it doesn't add it to WordPress roles until activation.

```php
use WPSeed\Core\Capability_Manager;

Capability_Manager::register( 'wpseed_manage_settings', array(
    'label'       => __( 'Manage Settings', 'wpseed' ),
    'description' => __( 'Access and modify the plugin settings page.', 'wpseed' ),
    'grant_to'    => array( 'administrator' ),
    'group'       => 'core',
) );
```

### Check a capability

```php
// Using the global accessor (recommended).
if ( wpseed_user_can( 'wpseed_manage_settings' ) ) {
    // show settings page
}

// Check for a specific user.
if ( wpseed_user_can( 'wpseed_view_logs', $user_id ) ) {
    // show logs for this user
}

// Using the class directly.
if ( Capability_Manager::user_can( 'wpseed_manage_settings' ) ) {
    // same thing
}
```

### Register multiple capabilities at once

```php
Capability_Manager::register_many( array(
    'myplugin_manage_projects' => array(
        'label'       => 'Manage Projects',
        'description' => 'Create, edit, and delete projects.',
        'grant_to'    => array( 'administrator', 'editor' ),
        'group'       => 'projects',
    ),
    'myplugin_view_reports' => array(
        'label'       => 'View Reports',
        'description' => 'Access the reporting dashboard.',
        'grant_to'    => array( 'administrator' ),
        'group'       => 'reports',
    ),
) );
```

---

## Default Capabilities

WPSeed registers these capabilities automatically:

| Capability | Group | Default Roles | Description |
|---|---|---|---|
| `manage_wpseed` | core | administrator | Full administrative access (legacy) |
| `code_wpseed` | development | administrator | Developer-level features (legacy) |
| `wpseed_manage_settings` | core | administrator | Access the settings page |
| `wpseed_view_development` | development | administrator | Access development tools |
| `wpseed_manage_connectors` | api | administrator | Configure API credentials |
| `wpseed_execute_connectors` | api | administrator | Run connector actions |
| `wpseed_view_logs` | development | administrator | View logs and debug output |

When cloning WPSeed to a new plugin, these are renamed with the plugin's prefix
(e.g. `evolvewp_core_manage_settings`).

---

## Lifecycle

### On Activation

`Install::create_roles()` calls `Capability_Manager::install()`, which adds
every registered capability to its configured default roles.

### On Uninstall

`Install::remove_roles()` calls `Capability_Manager::uninstall()`, which
removes every registered capability from every role.

### Adding Capabilities After Initial Activation

If you register new capabilities in a plugin update, they won't be installed
until the next activation. To handle this, check the plugin version in
`Install::check_version()` and call `Capability_Manager::install()` when
the version changes. WPSeed already does this — `Install::install()` runs
whenever the stored version differs from the package version.

---

## Querying Capabilities

### Get all registered capabilities

```php
$all = Capability_Manager::get_all();
// Returns: array( 'wpseed_manage_settings' => array( 'label' => ..., ... ), ... )
```

### Get capabilities by group

```php
$api_caps = Capability_Manager::get_by_group( 'api' );
// Returns only caps where group === 'api'
```

### Get a single capability's metadata

```php
$cap = Capability_Manager::get( 'wpseed_manage_settings' );
// Returns: array( 'label' => 'Manage Settings', 'description' => ..., ... )
// Returns null if not registered.
```

### Get all group keys

```php
$groups = Capability_Manager::get_groups();
// Returns: array( 'core', 'development', 'api' )
```

---

## Filters

### wpseed_user_can

Override any capability check. Return a non-null boolean to short-circuit
the default WordPress check.

```php
// Example: grant all capabilities to a specific user.
add_filter( 'wpseed_user_can', function( $result, $capability, $user_id ) {
    if ( 42 === $user_id ) {
        return true; // User 42 can do anything.
    }
    return $result; // null = use default check.
}, 10, 3 );
```

This filter is how EvolveWP Core will implement:
- Cross-plugin permission management
- Two-person authorisation for sensitive operations
- Permission audit logging

---

## Actions

### wpseed_capabilities_installed

Fires after capabilities are added to roles during activation.

```php
add_action( 'wpseed_capabilities_installed', function( $capabilities ) {
    // Log which capabilities were installed.
    error_log( 'Installed ' . count( $capabilities ) . ' capabilities.' );
} );
```

### wpseed_capabilities_uninstalled

Fires after capabilities are removed from roles during uninstall.

---

## When Cloning to a New Plugin

After running the WPSeed cloning process:

1. The class is renamed (e.g. `EvolveWP\Core\Capability_Manager`).
2. The global accessor becomes `evolvewp_core_user_can()`.
3. All default capability names are renamed (e.g. `evolvewp_core_manage_settings`).
4. Update `register_defaults()` to declare your plugin's own capabilities.
5. Remove any WPSeed-specific caps you don't need.

---

## File Reference

| File | Purpose |
|---|---|
| `includes/Core/Capability_Manager.php` | Registration, installation, checking |
| `includes/Core/Install.php` | Calls install() on activation, uninstall() on removal |
| `functions.php` | `wpseed_user_can()` global accessor |
| `includes/Hook_Registry.php` | Documents all capability-related hooks |
