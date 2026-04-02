# WPSeed File Header Template

> Every PHP file in WPSeed (and every plugin cloned from it) opens with a standard
> docblock. This file contains copy-paste examples for each role type.
>
> The **ROLE** tag is the most important field. It lets any developer or AI classify
> the file instantly without reading its contents.

---

## Role Reference

| Role | What it does |
|---|---|
| `bootstrap` | Plugin entry point — defines constants, requires files, boots the main class |
| `admin-ui` | Renders an admin page or tab (PHP + HTML mixed) |
| `ajax-handler` | Handles one or more `wp_ajax_*` actions |
| `data-model` | Reads and/or writes persistent data (DB, JSON files, options, transients) |
| `utility` | Stateless helper — pure functions or a class with no side effects on load |
| `hook-registration` | Registers `add_action` / `add_filter` calls only — no logic |
| `template` | Pure HTML output with minimal PHP — no business logic |
| `cli` | WP-CLI command handler |
| `api-endpoint` | REST API controller — extends `WP_REST_Controller` |
| `ecosystem-bridge` | Cross-plugin communication, registry, or feature detection |

---

## bootstrap

```php
<?php
/**
 * Plugin entry point.
 *
 * ROLE: bootstrap
 *
 * DEPENDS ON: nothing — this file runs before anything else
 *
 * CONSUMED BY: WordPress plugin loader
 *
 * DATA FLOW:
 *   Input  → none
 *   Output → defines WPSEED_* constants, requires vendor/autoload.php and loader.php
 *
 * @package  WPSeed
 * @since    1.0.0
 */
```

---

## admin-ui

```php
<?php
/**
 * Settings page — general options tab.
 *
 * ROLE: admin-ui
 *
 * DEPENDS ON:
 *   - WPSeed_Admin_Settings in includes/admin/admin-settings.php
 *   - global wpseed_get_screen_ids() in includes/admin/admin-functions.php
 *
 * CONSUMED BY:
 *   - Hook: admin_menu (registered in includes/admin/admin-menus.php)
 *
 * DATA FLOW:
 *   Input  → get_option( 'wpseed_settings' )
 *   Output → HTML rendered to screen
 *
 * @package  WPSeed
 * @category Admin
 * @since    1.0.0
 */
```

---

## ajax-handler

```php
<?php
/**
 * AJAX handler — save plugin settings.
 *
 * ROLE: ajax-handler
 *
 * DEPENDS ON:
 *   - WPSeed\Core\AJAX_Handler (base class) in includes/Core/AJAX_Handler.php
 *
 * CONSUMED BY:
 *   - Hook: wp_ajax_wpseed_save_settings
 *
 * DATA FLOW:
 *   Input  → $_POST['nonce'], $_POST['settings'] (JSON)
 *   Output → wp_send_json_success() or wp_send_json_error()
 *
 * @package  WPSeed
 * @category Core
 * @since    1.0.0
 */
```

---

## data-model

```php
<?php
/**
 * Plugin settings storage — reads and writes the wpseed_settings option.
 *
 * ROLE: data-model
 *
 * DEPENDS ON: WordPress options API
 *
 * CONSUMED BY:
 *   - WPSeed_Admin_Settings::save() in includes/admin/admin-settings.php
 *   - WPSeed_Admin_Settings::get() in includes/admin/admin-settings.php
 *
 * DATA FLOW:
 *   Input  → get_option( 'wpseed_settings' )
 *   Output → update_option( 'wpseed_settings', $data )
 *
 * @package  WPSeed
 * @category Core
 * @since    1.0.0
 */
```

---

## utility

```php
<?php
/**
 * Input validation helpers.
 *
 * ROLE: utility
 *
 * Stateless functions — no side effects on load, no database access.
 * Safe to include on any request type.
 *
 * DEPENDS ON: nothing
 *
 * CONSUMED BY:
 *   - Any file that needs input validation
 *
 * DATA FLOW:
 *   Input  → raw values passed as function arguments
 *   Output → sanitised/validated values returned
 *
 * @package  WPSeed
 * @category Utilities
 * @since    1.0.0
 */
```

---

## hook-registration

```php
<?php
/**
 * Hook registry — all actions and filters registered by WPSeed.
 *
 * ROLE: hook-registration
 *
 * Single source of truth for hook registration. No logic lives here —
 * only add_action() and add_filter() calls pointing to class methods.
 * Read this file to understand the full event surface of the plugin.
 *
 * DEPENDS ON:
 *   - All handler classes must be loaded before this file is included
 *
 * CONSUMED BY: loader.php
 *
 * DATA FLOW:
 *   Input  → none
 *   Output → registers callbacks with WordPress hook system
 *
 * @package  WPSeed
 * @category Core
 * @since    3.0.0
 */
```

---

## template

```php
<?php
/**
 * Admin page template — dashboard overview.
 *
 * ROLE: template
 *
 * Pure HTML output. No business logic. Data is prepared by the calling
 * controller and passed as local variables before this file is included.
 *
 * DEPENDS ON:
 *   - $data array prepared by WPSeed_Admin_Dashboard::render()
 *
 * CONSUMED BY:
 *   - WPSeed_Admin_Dashboard::render() in includes/Admin/Dashboard.php
 *
 * DATA FLOW:
 *   Input  → $data (local variable set by caller)
 *   Output → HTML rendered to screen
 *
 * @package  WPSeed
 * @category Admin
 * @since    1.0.0
 */
```

---

## cli

```php
<?php
/**
 * WP-CLI commands for WPSeed.
 *
 * ROLE: cli
 *
 * DEPENDS ON:
 *   - WP_CLI class (WordPress CLI)
 *
 * CONSUMED BY:
 *   - WP-CLI when `wp wpseed` commands are invoked
 *
 * DATA FLOW:
 *   Input  → CLI arguments and flags
 *   Output → CLI output via WP_CLI::success() / WP_CLI::error()
 *
 * @package  WPSeed
 * @category CLI
 * @since    1.0.0
 */
```

---

## api-endpoint

```php
<?php
/**
 * REST API controller — plugin settings endpoint.
 *
 * ROLE: api-endpoint
 *
 * DEPENDS ON:
 *   - WPSeed\API\REST_Controller (base class) in includes/API/REST_Controller.php
 *
 * CONSUMED BY:
 *   - Hook: rest_api_init
 *   - Route: GET/POST /wp-json/wpseed/v1/settings
 *
 * DATA FLOW:
 *   Input  → WP_REST_Request object
 *   Output → WP_REST_Response object (JSON)
 *
 * @package  WPSeed
 * @category API
 * @since    1.0.0
 */
```

---

## ecosystem-bridge

```php
<?php
/**
 * Ecosystem plugin registry.
 *
 * ROLE: ecosystem-bridge
 *
 * Single responsibility: Track which WPSeed-based plugins are active and
 * expose a shared API for feature detection, menu placement, and resource
 * sharing. Does NOT handle menu rendering or plugin installation.
 *
 * DEPENDS ON:
 *   - WordPress functions: add_action, do_action, update_option, get_option
 *
 * CONSUMED BY:
 *   - functions.php  wpseed_ecosystem() global accessor
 *   - Hook: wpseed_ecosystem_register
 *
 * DATA FLOW:
 *   Input  → wpseed_ecosystem_register action (plugin registration calls)
 *   Output → wpseed_ecosystem_mode option, wpseed_ecosystem_plugins option
 *
 * @package  WPSeed\Ecosystem
 * @since    3.0.0
 */
```
