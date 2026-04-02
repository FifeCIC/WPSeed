# WPSeed Cloning Guide

> How to create a new EvolveWP plugin from WPSeed. Follow every step in order.
> This document is also the specification for the automated `wp wpseed clone`
> command (Task 6.2).

---

## Before You Start

You need:
- The WPSeed plugin directory (the `Composer` branch for new plugins)
- Your new plugin's details:
  - **Slug** — lowercase, hyphens, no spaces. e.g. `evolvewp-verifier`
  - **Name** — human-readable. e.g. `EvolveWP.Verifier`
  - **Prefix** — short lowercase, used for functions/options. e.g. `evolvewp_verifier`
  - **Namespace** — PascalCase. e.g. `EvolveWP\Verifier`
  - **Constant prefix** — uppercase. e.g. `EVOLVEWP_VERIFIER`
  - **Text domain** — same as slug. e.g. `evolvewp-verifier`

---

## Step 1 — Copy the directory

Copy the entire WPSeed plugin folder to a new folder named after your slug:

```
wp-content/plugins/wpseed/          ← source
wp-content/plugins/evolvewp-verifier/  ← destination
```

Do **not** rename or delete the WPSeed source — it stays as the master boilerplate.

---

## Step 2 — Rename the main plugin file

Inside the new folder, rename `wpseed.php` to match your slug:

```
evolvewp-verifier/wpseed.php  →  evolvewp-verifier/evolvewp-verifier.php
```

---

## Step 3 — Find-and-replace all prefix variants

Run these replacements **case-sensitively** in the following order. Use your
editor's project-wide find-and-replace (not case-insensitive — order matters).

| Find | Replace with | Notes |
|---|---|---|
| `WordPressPluginSeed` | `EvolveWP_Verifier` | Main class name in loader.php |
| `WPSEED_` | `EVOLVEWP_VERIFIER_` | All constants |
| `WPSeed\\` | `EvolveWP\\Verifier\\` | Namespace in PHP files |
| `WPSeed_` | `EvolveWP_Verifier_` | Legacy global class prefixes |
| `wpseed_` | `evolvewp_verifier_` | Functions, options, hooks |
| `wpseed-` | `evolvewp-verifier-` | CSS/JS handles, text domain in some places |
| `wpseed` | `evolvewp-verifier` | Text domain, remaining slug references |

**Check after each replacement** that you haven't broken any string that should
not have been changed (e.g. comments referencing WPSeed by name for attribution).

---

## Step 4 — Update the plugin file header

Open `evolvewp-verifier.php` and update the WordPress plugin header:

```php
/**
 * Plugin Name: EvolveWP.Verifier
 * Plugin URI:  https://evolvewp.dev/plugins/verifier
 * Description: WordPress plugin code verification tool.
 * Version:     1.0.0
 * Author:      Ryan Bayne
 * Author URI:  https://evolvewp.dev
 * Text Domain: evolvewp-verifier
 * Domain Path: /i18n/languages/
 */
```

---

## Step 5 — Update composer.json

Open `composer.json` and update:

```json
{
    "name": "evolvewp/evolvewp-verifier",
    "description": "EvolveWP.Verifier — WordPress plugin code verification.",
    "autoload": {
        "psr-4": {
            "EvolveWP\\Verifier\\": "includes/"
        }
    }
}
```

Run `composer install --no-dev` to regenerate `vendor/autoload.php` with the
new namespace. If Composer is not available, manually update
`vendor/composer/autoload_psr4.php`:

```php
return array(
    'EvolveWP\\Verifier\\' => array( $baseDir . '/includes' ),
);
```

---

## Step 6 — Delete example/demo files

Remove files marked **Yes** in the `Delete on clone?` column of
`docs/FILE-INVENTORY.md`. At minimum:

```
includes/classes/rest-example.php
includes/classes/unified-feature.php
includes/admin/mainviews/default-advanced.php
includes/admin/mainviews/default-items.php
includes/admin/mainviews/listtable-demo-advanced.php
includes/admin/mainviews/listtable-demo.php
includes/admin/mainviews/team-advanced.php
includes/admin/mainviews/team-items.php
includes/admin/presentation/barchart.php
includes/admin/settings/settings-example.php
includes/admin/settings/settings-repeater-example.php
```

Also remove any `include_once` references to these files from `loader.php`.

---

## Step 7 — Update URL constants

In `loader.php` (inside `define_constants()`), update the support and author
URL constants to point to your plugin's own resources:

```php
define( 'EVOLVEWP_VERIFIER_HOME',   'https://evolvewp.dev/plugins/verifier' );
define( 'EVOLVEWP_VERIFIER_GITHUB', 'https://github.com/evolvewp/evolvewp-verifier' );
define( 'EVOLVEWP_VERIFIER_DOCS',   'https://evolvewp.dev/docs/verifier' );
```

---

## Step 8 — Update the ecosystem self-registration

In `loader.php`, find the `wpseed_ecosystem_register` action and update the
plugin registration to use your new slug and details:

```php
add_action( 'wpseed_ecosystem_register', function() {
    wpseed_ecosystem()->register_plugin( 'evolvewp-verifier', array(
        'name'    => 'EvolveWP.Verifier',
        'version' => EVOLVEWP_VERIFIER_VERSION,
        'path'    => EVOLVEWP_VERIFIER_PLUGIN_DIR_PATH,
        'url'     => plugins_url( '/', EVOLVEWP_VERIFIER_PLUGIN_FILE ),
    ) );
} );
```

---

## Step 9 — Activation check

1. Activate the plugin from the WordPress Plugins screen
2. Confirm no PHP errors in `wp-content/debug.log`
3. Confirm the plugin menu appears in wp-admin
4. Confirm the ecosystem registry recognises the new plugin

If activation fails, check:
- All prefix replacements completed (Step 3)
- `composer.json` namespace matches PHP namespace declarations (Step 5)
- No references to deleted example files remain in `loader.php` (Step 6)

---

## Checklist

- [ ] Directory copied and renamed
- [ ] Main plugin file renamed
- [ ] All 8 find-and-replace passes completed
- [ ] Plugin file header updated
- [ ] `composer.json` updated and autoloader regenerated
- [ ] Example files deleted
- [ ] URL constants updated
- [ ] Ecosystem self-registration updated
- [ ] Plugin activates without errors
- [ ] Plugin appears in wp-admin menu
