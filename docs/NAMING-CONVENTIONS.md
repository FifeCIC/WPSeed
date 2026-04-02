# WPSeed Naming Conventions

> This document is the single source of truth for naming patterns across WPSeed
> and every plugin cloned from it. AI assistants use this to predict file paths
> and class names without guessing. Developers use it to stay consistent.
>
> When cloning WPSeed, replace every occurrence of the boilerplate prefix with
> your plugin's own prefix throughout all files and in `composer.json`.

---

## Boilerplate Prefixes (replace when cloning)

| Type | WPSeed prefix | Example after cloning to EvolveWP.Verifier |
|---|---|---|
| PHP constants | `WPSEED_` | `EVOLVEWP_VERIFIER_` |
| PHP functions | `wpseed_` | `evolvewp_verifier_` |
| Global classes | `WPSeed_` | `EvolveWP_Verifier_` |
| Namespace root | `WPSeed\` | `EvolveWP\Verifier\` |
| Text domain | `wpseed` | `evolvewp-verifier` |
| Option prefix | `wpseed_` | `evolvewp_verifier_` |
| Hook prefix | `wpseed_` | `evolvewp_verifier_` |

---

## File Naming

| Type | Convention | Example |
|---|---|---|
| Legacy global classes | `kebab-case.php` | `includes/classes/install.php` |
| Namespaced classes | `PascalCase.php` matching the class name | `includes/Core/Install.php` |
| Templates | `admin-page-{tab-name}.php` | `templates/admin-page-settings.php` |
| Partials | `partial-{name}.php` | `templates/partials/partial-notice.php` |
| Functions files | `kebab-case.php` | `includes/functions/validate.php` |
| Assets (CSS/JS) | `kebab-case.css` / `kebab-case.js` | `assets/css/admin.css` |

---

## Class Naming

| Type | Convention | Example |
|---|---|---|
| Legacy global class | `WPSeed_Category_Name` | `WPSeed_Admin_Settings` |
| Namespaced class | `PascalCase` (no prefix — namespace provides context) | `WPSeed\Core\Install` → class `Install` |
| Abstract class | `Abstract_Name` or just the name with docblock noting it is abstract | `WPSeed\API\REST_Controller` |
| Interface | `Name_Interface` | `WPSeed\Core\Logger_Interface` |
| Trait | `Name_Trait` | `WPSeed\Core\Singleton_Trait` |

---

## Namespace Directory Map

The PSR-4 root `WPSeed\` maps to `includes/`. Subdirectory = sub-namespace.

```
WPSeed\Core\       → includes/Core/        Core classes loaded on every request
WPSeed\Admin\      → includes/Admin/       Admin-only classes
WPSeed\Ecosystem\  → includes/Ecosystem/   Cross-plugin registry and bridges
WPSeed\Utilities\  → includes/Utilities/   Stateless helper classes
WPSeed\API\        → includes/API/         REST controllers and API base classes
WPSeed\CLI\        → includes/CLI/         WP-CLI command classes
```

**File path from fully-qualified class name:**
`WPSeed\Core\Install` → `includes/Core/Install.php`
`WPSeed\Ecosystem\Registry` → `includes/Ecosystem/Registry.php`

**When cloning:** replace `WPSeed\\` with your namespace in both PHP files and
in the `composer.json` autoload section.

---

## Method Naming

| Convention | Example |
|---|---|
| `snake_case` for all methods | `get_plugin_count()` |
| Getters: `get_{noun}` | `get_registered_plugins()` |
| Setters: `set_{noun}` | `set_menu_location()` |
| Boolean checks: `is_{state}` or `has_{thing}` | `is_ecosystem_mode()`, `has_logging()` |
| Actions: `{verb}_{noun}` | `register_plugin()`, `detect_ecosystem()` |
| Static factories: `instance()` for singletons | `Registry::instance()` |

---

## Constant Naming

```
WPSEED_VERSION          Plugin version string
WPSEED_PLUGIN_FILE      Absolute path to main plugin file (__FILE__)
WPSEED_PLUGIN_DIR_PATH  Absolute path to plugin root directory (trailing slash)
WPSEED_PLUGIN_URL       Plugin root URL (trailing slash)
WPSEED_LOG_DIR          Absolute path to log directory
WPSEED_DEV_MODE         Boolean — true enables developer tooling
```

All constants guarded with `if ( ! defined( 'WPSEED_CONSTANT_NAME' ) )`.

---

## Hook Naming

```
Pattern:   {prefix}_{noun}_{verb}
Examples:  wpseed_plugin_registered
           wpseed_ecosystem_loaded
           wpseed_settings_saved

Filter:    {prefix}_{noun}           (returns a value)
Examples:  wpseed_menu_location
           wpseed_registered_plugins

AJAX:      {prefix}_{action}
Examples:  wpseed_save_settings
           wpseed_get_status
```

---

## Option Naming

```
Pattern:   {prefix}_{setting_name}
Examples:  wpseed_version
           wpseed_db_version
           wpseed_ecosystem_mode
           wpseed_ecosystem_plugins
```

---

## Nonce Actions

```
Pattern:   {prefix}_{action_description}
Examples:  wpseed_save_settings
           wpseed_do_update
           wpseed_force_update
```

---

## Database Table Naming

```
Pattern:   {wpdb->prefix}{plugin_prefix}_{table_name}
Examples:  wp_wpseed_api_calls
           wp_wpseed_debug_logs
           wp_wpseed_notifications
```

---

## CSS / JS Handle Naming

```
Pattern:   {plugin-slug}-{asset-name}
Examples:  wpseed-admin
           wpseed-roadmap
           wpseed-ecosystem
```
