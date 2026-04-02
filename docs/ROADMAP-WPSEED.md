# WPSeed — Development Roadmap

> WPSeed is a boilerplate. Its job is to give every EvolveWP plugin a clean, consistent,
> AI-navigable starting point. Development here is about standards, structure, and
> reusable patterns — not features. Features belong in the plugins built from WPSeed.
>
> All `wpseed` / `WPSEED_` / `WPSeed_` strings are placeholders. When a new plugin is
> created from WPSeed, these are mass-replaced with the plugin's own prefix. Classes
> that are genuinely generic (no plugin-specific logic) may keep a neutral name that
> does not need replacing at all.

---

## PHASE 0: Composer and Namespace Foundation

**Why this comes first:** Every subsequent phase creates new files. Those files need
to know which namespace convention to follow and whether to use `require_once` or
Composer autoloading. Establishing this foundation before any files are moved or
created means every new file is correct from the start rather than needing retrofitting.

WPSeed currently has no `composer.json` and uses a hand-rolled SPL autoloader
(`includes/classes/autoloader.php`) that maps class name prefixes to directories.
This works but is fragile — it only handles `WPSeed_Admin*` and `WPSeed_Shortcode_*`
classes, leaving everything else to manual `include_once` calls in `loader.php`.

### Namespace Analysis — Where Namespaces Add the Most Value

Not every file in WPSeed needs a namespace immediately. The highest-value targets are
files that will be:
- **Moved** as part of Phase 1 structural work (they need rewriting anyway)
- **Extended** by plugins cloned from WPSeed (namespaces make `use` statements clean)
- **Consumed by Composer** (REST controllers, API classes, abstract bases)
- **Shared across the ecosystem** (ecosystem registry, logger, task scheduler)

**Tier 1 — Namespace immediately (new files and files being moved in Phase 1):**

| File | Current class | Namespaced class |
|---|---|---|
| `includes/Ecosystem/Registry.php` | `WPSeed_Ecosystem_Registry` | `WPSeed\Ecosystem\Registry` |
| `includes/Ecosystem/Menu_Manager.php` | `WPSeed_Ecosystem_Menu_Manager` | `WPSeed\Ecosystem\Menu_Manager` |
| `includes/Ecosystem/Installer.php` | `WPSeed_Ecosystem_Installer` | `WPSeed\Ecosystem\Installer` |
| `includes/Core/Install.php` | `WPSeed_Install` | `WPSeed\Core\Install` |
| `includes/Core/AJAX_Handler.php` | `WPSeed_AJAX` | `WPSeed\Core\AJAX_Handler` |
| `includes/Core/Logger.php` | `WPSeed_Unified_Logger` | `WPSeed\Core\Logger` |
| `includes/Core/Enhanced_Logger.php` | `WPSeed_Enhanced_Logger` | `WPSeed\Core\Enhanced_Logger` |
| `includes/Core/Task_Scheduler.php` | `WPSeed_Task_Scheduler` | `WPSeed\Core\Task_Scheduler` |
| `includes/Admin/Dashboard_Widgets.php` | `WPSeed_Dashboard_Widgets` | `WPSeed\Admin\Dashboard_Widgets` |
| `api/Base_API.php` | `WPSeed_Base_API` | `WPSeed\API\Base_API` |
| `api/REST_Controller.php` | `WPSeed_REST_Controller` | `WPSeed\API\REST_Controller` |

**Tier 2 — Namespace when touched (not worth moving just for namespacing):**

| File | Reason to wait |
|---|---|
| `includes/classes/install.php` | Being moved in Phase 1 — namespace then |
| `includes/classes/ajax.php` | Being moved in Phase 1 — namespace then |
| `includes/admin/admin.php` | Being moved in Phase 1 — namespace then |
| `includes/classes/dependencies.php` | Low complexity, touch when needed |
| `includes/classes/multisite.php` | Low complexity, touch when needed |

**Tier 3 — Keep as global (intentionally not namespaced):**

| File | Reason |
|---|---|
| `functions.php` | Global helper functions — namespacing functions is unusual in WP |
| `includes/functions/core.php` | Same — procedural helpers stay global |
| `includes/functions/validate.php` | Same |
| `wpseed.php` | Plugin entry point — must be global |
| `loader.php` | Bootstrap file — must be global |

**The global accessor functions** (`wpseed_ecosystem()`, `wpseed_log()`, etc.) stay
global even when their backing classes are namespaced. This is the standard WordPress
pattern — the class lives in a namespace, the convenience function is global:

```php
// Class is namespaced:
namespace WPSeed\Ecosystem;
class Registry { ... }

// Global accessor stays global (in functions.php):
function wpseed_ecosystem() {
    return \WPSeed\Ecosystem\Registry::instance();
}
```

### Task 0.1 — Create composer.json

Create `composer.json` in the WPSeed root with PSR-4 autoloading for the `WPSeed\`
namespace pointing to `includes/`:

```json
{
    "name": "evolvewp/wpseed",
    "description": "WPSeed WordPress plugin boilerplate",
    "type": "wordpress-plugin",
    "license": "GPL-3.0-or-later",
    "require": {
        "php": ">=7.4"
    },
    "autoload": {
        "psr-4": {
            "WPSeed\\": "includes/"
        }
    },
    "config": {
        "optimize-autoloader": true,
        "sort-packages": true
    }
}
```

Run `composer install` to generate the `vendor/autoload.php` file.

Add to `.gitignore`:
```
vendor/
```

Note: Action Scheduler and Carbon Fields are bundled directly in `includes/libraries/`
rather than via Composer because WordPress.org requires self-contained plugins. They
stay as manual includes. Composer autoloading is for WPSeed's own classes only.

**CHANGE LOG — Task 0.1:**
- Created `composer.json` with PSR-4 map: `WPSeed\` → `includes/`
- Composer not installed globally on this machine (WAMP64/Windows). Autoloader
  files generated manually instead:
  - `vendor/autoload.php` — entry point, requires autoload_real.php
  - `vendor/composer/autoload_real.php` — bootstraps ClassLoader with PSR-4 map
  - `vendor/composer/autoload_psr4.php` — returns the namespace→path array
  - `vendor/composer/ClassLoader.php` — PSR-4 class resolution engine
- Version bumped to 3.0.0 in `wpseed.php` to mark the Composer branch start
- **ISSUE ENCOUNTERED:** `Composer\Autoload\ClassLoader` fatal error on activation.
  WPVerifier loads its own Composer autoloader first, declaring the class.
  WPSeed's `ClassLoader.php` then tried to declare it again — fatal.
  **FIX 1 (insufficient):** `autoload_real.php` checked `class_exists` before
  requiring `ClassLoader.php`, but still created a second `ClassLoader` instance
  and registered it via `spl_autoload_register`. This caused WPVerifier's
  autoloader to re-include already-loaded files, breaking namespace declarations
  that must be the first statement in a file.
  **FIX 2 (correct):** `autoload_real.php` now calls `spl_autoload_functions()`
  to find the already-registered `ClassLoader` instance and calls `addPsr4()` on
  it directly. WPSeed's `WPSeed\` prefix is added to the existing loader with no
  second registration. Only if no loader exists (WPSeed loads first) is a new
  instance created. `ClassLoader.php` reduced to a comment stub.
- **Next AI:** Run `composer install --no-dev` when Composer is available to
  regenerate these files from `composer.json`. The manual files are functionally
  equivalent but will be replaced by the official generated versions.

---

### Task 0.2 — Load Composer autoloader in loader.php

Add the Composer autoloader require at the very top of `loader.php`, before any
other includes:

```php
// Composer autoloader — handles all namespaced WPSeed\ classes.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}
```

This means any class with `namespace WPSeed\...` will be autoloaded automatically
without needing an explicit `include_once`. The existing manual `include_once` calls
for non-namespaced files remain until those files are migrated in Phase 1.

**CHANGE LOG — Task 0.2:**
- Added Composer autoloader require to `wpseed.php` immediately after constants,
  before any other includes:
  `require_once WPSEED_PLUGIN_DIR_PATH . 'vendor/autoload.php';`
- Guarded with `file_exists()` so the plugin degrades gracefully if vendor/
  is missing (e.g. fresh clone before composer install).
- Existing manual `include_once` calls in `loader.php` are unchanged — they
  continue to load non-namespaced legacy files until those files are migrated.

---

### Task 0.3 — Document the namespace convention

Add to `docs/NAMING-CONVENTIONS.md` (created in Phase 4) the namespace rules:

```
Namespace root:    WPSeed\ (replace with plugin namespace when cloning)
Directory map:     WPSeed\Core\       → includes/Core/
                   WPSeed\Admin\      → includes/Admin/
                   WPSeed\Ecosystem\ → includes/Ecosystem/
                   WPSeed\Utilities\ → includes/Utilities/
                   WPSeed\API\        → includes/API/
                   WPSeed\CLI\        → includes/CLI/

File naming:       PascalCase.php matching the class name
                   WPSeed\Core\Install → includes/Core/Install.php

Global functions:  Stay in functions.php, no namespace
Global accessors:  Stay global even when backing class is namespaced

When cloning:      Replace WPSeed\\ with MyPlugin\\ throughout
                   Replace "WPSeed\\\\" in composer.json autoload section
```

**CHANGE LOG — Task 0.3:**
- Created `includes/Core/`, `includes/Admin/`, `includes/Ecosystem/`,
  `includes/Utilities/`, `includes/API/`, `includes/CLI/` directories.
- Added `index.php` (silence is golden) to each directory.
- `includes/Admin/index.php` was already created; others created fresh.
- These directories are the PSR-4 targets. Any class placed here with the
  correct namespace declaration will be autoloaded with no further configuration.

---

### Task 0.4 — Add namespace to the first moved file as a proof of concept

When the first file is moved in Phase 1 (Task 1.5 — `ecosystem-registry.php` →
`includes/Ecosystem/Registry.php`), add the namespace declaration at the same time:

```php
<?php
/**
 * Ecosystem plugin registry.
 *
 * ROLE: ecosystem-bridge
 * ...
 */

namespace WPSeed\Ecosystem;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Registry {
    // class body unchanged
}
```

Update the global accessor in `functions.php`:

```php
function wpseed_ecosystem() {
    return \WPSeed\Ecosystem\Registry::instance();
}
```

Update any `new WPSeed_Ecosystem_Registry()` or `WPSeed_Ecosystem_Registry::instance()`
calls to use either the global accessor or a `use WPSeed\Ecosystem\Registry;` statement.

Verify the plugin activates cleanly. This proves the Composer autoloader works
end-to-end before committing to the pattern for all subsequent files.

**CHANGE LOG — Task 0.4:**
- Created `includes/Ecosystem/Registry.php` with `namespace WPSeed\Ecosystem;`
- Class renamed from `WPSeed_Ecosystem_Registry` to `Registry` (namespace
  provides the context, prefix no longer needed).
- Full standard file header added (ROLE, DEPENDS ON, CONSUMED BY, DATA FLOW).
- Full docblocks added to all public methods with @param/@return tags.
- Updated `functions.php` (was empty) with the global accessor:
  `function wpseed_ecosystem() { return \WPSeed\Ecosystem\Registry::instance(); }`
- Updated `loader.php`: removed `include_once ecosystem-registry.php`, replaced
  with `wpseed_ecosystem()` boot call and self-registration via action hook.
- The old `includes/classes/ecosystem-registry.php` is kept untouched for
  reference during the rebuild. It will be deleted in Task 0.5.
- **Verification:** The class is autoloaded via Composer PSR-4. No manual
  include_once needed. Plugin should activate cleanly — test before proceeding.

---

### Task 0.5 — Remove the hand-rolled SPL autoloader

Once all files that the SPL autoloader was responsible for have been moved and
namespaced (end of Phase 1), delete `includes/classes/autoloader.php` and remove
its `include_once` from `loader.php`. Composer handles autoloading from this point.

**Acceptance:** `autoloader.php` deleted. `loader.php` no longer references it.
Plugin activates cleanly.

**CHANGE LOG — Task 0.5:**
- Deleted `includes/classes/autoloader.php`.
- Removed its `include_once` from `loader.php` GROUP 3.
- Composer PSR-4 autoloader (loaded in `wpseed.php`) now handles all class
  resolution. No SPL autoloader remains.
- **Verify:** Plugin activates cleanly.

---


**Problem:** `loader.php` contains 40+ flat `include_once` calls with no grouping or
explanation. Admin files, library files, REST registration, and frontend files are all
loaded in the same block. An AI reading this file cannot determine what loads when,
what depends on what, or what is safe to remove when cloning.

`includes/classes/` has 30+ files with no sub-organisation. There is no way to predict
what a file does from its name alone without opening it.

**Goal:** A developer or AI should be able to read `loader.php` and immediately
understand the full load order and why each group exists. The `includes/` directory
should be organised so that the category of any file is obvious from its path.

### Task 1.1 — Document the current load order

Before changing anything, add a comment block at the top of `loader.php` that lists
every `include_once` call grouped by category, with a one-line note on why each group
exists and when it loads. This is a read-only documentation task — no code changes.

```php
/**
 * Load order for WordPressPluginSeed.
 *
 * GROUP 1 — Core functions (always loaded, no class dependencies)
 *   includes/functions/core.php
 *   includes/functions/validate.php
 *   includes/functions/database.php
 *
 * GROUP 2 — Core classes (loaded before admin, required everywhere)
 *   includes/classes/debug.php
 *   includes/classes/autoloader.php
 *   ...
 *
 * GROUP 3 — Optional libraries (guarded by file_exists)
 *   includes/libraries/action-scheduler/
 *   includes/libraries/carbon-fields/
 *
 * GROUP 4 — Admin only (guarded by is_request('admin'))
 *   includes/admin/admin.php
 *   ...
 */
```

**Acceptance:** `loader.php` has a comment block that accurately describes every
include, grouped and annotated. No functional changes.

**CHANGE LOG — Task 1.1:**
- Added 11-group load order comment block to the top of `loader.php`.
- No functional changes — includes are unchanged.
- Groups documented: Composer autoloader, core functions, core classes, optional
  libraries, ecosystem framework, feature classes, WP-CLI, API system, admin-only,
  frontend-only, shortcodes.

---

### Task 1.2 — Identify which includes/ files are truly core vs optional

Go through every file in `includes/classes/` and classify each one as:

- **Core** — must load on every request (e.g. `install.php`, `ajax.php`, `ecosystem-registry.php`)
- **Admin-only** — only needed on admin requests (e.g. `dashboard-widgets.php`, `notification-bell.php`)
- **Optional** — only needed when a specific feature is enabled (e.g. `github-sync.php`, `multisite.php`)
- **Example/demo** — should be removed when cloning (e.g. `rest-example.php`, `unified-feature.php`)

Record this classification in a new file: `docs/FILE-INVENTORY.md`. One row per file,
columns: path, classification, description, safe-to-delete-on-clone (yes/no).

**Acceptance:** `docs/FILE-INVENTORY.md` exists and covers every file in `includes/`.
No code changes.

**CHANGE LOG — Task 1.2:**
- Created `docs/FILE-INVENTORY.md`.
- Classified all 34 files in `includes/classes/`, all files in `includes/admin/`
  and its subdirectories, all files in `includes/functions/`, and all files in `api/`.
- Identified 11 files marked safe-to-delete on clone (example/demo files).
- Added target migration locations table showing destination path and namespace
  for every Tier 1 file from the namespace analysis.

---

### Task 1.3 — Group the includes in loader.php by classification

Using the classification from Task 1.2, reorganise the `include_once` calls in
`loader.php` into clearly labelled groups matching the comment block from Task 1.1.
No files are moved yet — only the order and grouping of the `include_once` calls
changes, with a comment header above each group.

**Acceptance:** `loader.php` loads the same files as before, in the same logical order,
but grouped with comment headers. Verify the plugin still activates cleanly.

**CHANGE LOG — Task 1.3:**
- Rewrote `includes()` method in `loader.php` with 11 clearly labelled group headers.
- Same files loaded, same functional order. No files moved, no logic changed.
- `unified-logger.php` was missing from the original includes — added to Group 3.
- Assets moved to their own Group 3b with a note they will be replaced in Task 3.1.
- Inline comments on example files (`rest-example.php`, `unified-feature.php`) now
  explicitly say "EXAMPLE — delete when cloning".

---

### Task 1.4 — Create includes/Admin/, includes/Core/, includes/Utilities/ subdirectories

Create the target directory structure. Do not move any files yet — just create the
directories and add an `index.php` (blank, with ABSPATH guard) in each:

```
includes/
  Admin/        ← will hold admin-only classes
  Core/         ← will hold classes that load on every request
  Utilities/    ← will hold stateless helper classes
  Ecosystem/    ← will hold ecosystem-registry and related classes
```

**Acceptance:** Directories exist. No files moved. Plugin still activates.

**CHANGE LOG — Task 1.4:**
- Completed as part of Task 0.3. All six directories created:
  `includes/Core/`, `includes/Admin/`, `includes/Ecosystem/`,
  `includes/Utilities/`, `includes/API/`, `includes/CLI/`.
- Each has an `index.php` silence guard.

---

### Task 1.5 — Move files one at a time, updating loader.php each time

Move files from `includes/classes/` into the new subdirectories one at a time,
following the classification from Task 1.2. After each move:

1. Update the `include_once` path in `loader.php`
2. Verify the plugin activates without errors
3. Commit before moving the next file

Move order (safest first):
1. `ecosystem-registry.php` → `includes/Ecosystem/Registry.php`
2. `ecosystem-menu-manager.php` → `includes/Ecosystem/Menu_Manager.php`
3. `ecosystem-installer.php` → `includes/Ecosystem/Installer.php`
4. `install.php` → `includes/Core/Install.php`
5. `ajax.php` → `includes/Core/AJAX_Handler.php`
6. `unified-logger.php` → `includes/Core/Logger.php`
7. `enhanced-logger.php` → `includes/Core/Enhanced_Logger.php`
8. `dashboard-widgets.php` → `includes/Admin/Dashboard_Widgets.php`
9. `notification-bell.php` → `includes/Admin/Notification_Bell.php`
10. Remaining admin-only files → `includes/Admin/`
11. Remaining utility files → `includes/Utilities/`

**Acceptance:** All files moved, `loader.php` updated, plugin activates cleanly after
each individual move.

**CHANGE LOG — Task 1.5 (in progress):**

**File 1: ecosystem-menu-manager.php → includes/Ecosystem/Menu_Manager.php**
- Created `includes/Ecosystem/Menu_Manager.php` with `namespace WPSeed\Ecosystem;`
- Class renamed from `WPSeed_Ecosystem_Menu_Manager` to `Menu_Manager`.
- Full standard file header added (ROLE: admin-ui).
- Removed `return new WPSeed_Ecosystem_Menu_Manager();` from bottom of old file —
  instantiation now happens in `loader.php` via `new \WPSeed\Ecosystem\Menu_Manager();`.
- Updated `loader.php` GROUP 5: removed `include_once ecosystem-menu-manager.php`,
  added `new \WPSeed\Ecosystem\Menu_Manager();` after Registry boot.
- Old file `includes/classes/ecosystem-menu-manager.php` kept for reference.
- **Verify:** Plugin activates cleanly. Ecosystem menus render correctly.

**File 2: ecosystem-installer.php → includes/Ecosystem/Installer.php**
- Created `includes/Ecosystem/Installer.php` with `namespace WPSeed\Ecosystem;`
- Class renamed from `WPSeed_Ecosystem_Installer` to `Installer`.
- Full standard file header added (ROLE: admin-ui).
- WordPress core classes (`Plugin_Upgrader`, `WP_Ajax_Upgrader_Skin`) now
  referenced with leading backslash since they are in the global namespace.
- Removed `return new WPSeed_Ecosystem_Installer();` from bottom of old file.
- Updated `loader.php` GROUP 5: removed last `include_once`, added
  `new \WPSeed\Ecosystem\Installer();`. GROUP 5 now has zero include_once calls.
- Old file `includes/classes/ecosystem-installer.php` kept for reference.
- **Verify:** Plugin activates cleanly. Installer page renders correctly.

**Ecosystem migration complete.** All three Ecosystem classes are now namespaced
and autoloaded: `Registry`, `Menu_Manager`, `Installer`. Zero `include_once`
calls remain in GROUP 5.

**File 3: install.php → includes/Core/Install.php**
- Created `includes/Core/Install.php` with `namespace WPSeed\Core;`
- Class renamed from `WPSeed_Install` to `Install`.
- Full standard file header added (ROLE: data-model).
- All WordPress core class references (`WP_Roles`, `WP_Filesystem`) prefixed
  with leading backslash for global namespace resolution.
- References to unmigrated classes (`WPSeed_Admin_Notices`, `WPSeed_Admin_Settings`)
  kept as `\WPSeed_Admin_Notices` etc. — will be updated when those classes migrate.
- `WPSeed()` global function calls prefixed with `\WPSeed()` for namespace clarity.
- Removed `class_exists` guard and `WPSeed_Install::init()` self-boot from bottom.
- Updated `loader.php`:
  - Removed `include_once install.php` from GROUP 3.
  - Updated `register_activation_hook` to `'\WPSeed\Core\Install'`.
  - Updated `register_deactivation_hook` to `'\WPSeed\Core\Install'`.
  - Added `\WPSeed\Core\Install::init()` call in `init_hooks()`.
- Old file `includes/classes/install.php` kept for reference.
- **Verify:** Plugin activates cleanly. Deactivation works. Version check runs.

**File 4: ajax.php → includes/Core/AJAX_Handler.php**
- Created `includes/Core/AJAX_Handler.php` with `namespace WPSeed\Core;`
- Class renamed from `WPSeed_AJAX` to `AJAX_Handler`.
- Full standard file header added (ROLE: ajax-handler).
- Removed `WPSeed_AJAX::init()` self-boot from bottom of old file.
- Updated `loader.php`:
  - Removed `include_once ajax.php` from GROUP 3.
  - Added `\WPSeed\Core\AJAX_Handler::init()` in `init_hooks()`.
- No other files reference `WPSeed_AJAX` directly.
- Old file `includes/classes/ajax.php` kept for reference.
- **Verify:** Plugin activates cleanly. AJAX endpoints respond.

**File 5: unified-logger.php → includes/Core/Logger.php**
- Created `includes/Core/Logger.php` with `namespace WPSeed\Core;`
- Class renamed from `WPSeed_Unified_Logger` to `Logger`.
- Full standard file header added (ROLE: utility).
- JS logging helper (`WPSeedLogger`) moved into the class file as an
  anonymous `admin_footer` hook (was a standalone function in old file).
- Global accessor functions `wpseed_log()` and `wpseed_trace()` moved to
  `functions.php` pointing to `\WPSeed\Core\Logger::instance()`.
- Updated `loader.php`: removed `include_once unified-logger.php` from GROUP 3.
- **Note:** `enhanced-logger.php` and `verification-logger.php` reference
  `WPSeed_Unified_Logger` via `class_exists()` guards. Those checks will now
  return false (class name changed). The code gracefully skips the calls.
  These files will be updated when they are migrated.
- Old file `includes/classes/unified-logger.php` kept for reference.
- **Verify:** Plugin activates cleanly. Developer mode trace logging works.

**File 6: enhanced-logger.php → includes/Core/Enhanced_Logger.php**
- Created `includes/Core/Enhanced_Logger.php` with `namespace WPSeed\Core;`
- Class renamed from `WPSeed_Enhanced_Logger` to `Enhanced_Logger`.
- Full standard file header added (ROLE: utility).
- References to `WPSeed_Unified_Logger` replaced with `Logger` (same namespace).
- `WPSeed_Developer_Mode` referenced as `\WPSeed_Developer_Mode` (not yet migrated).
- `register_activation_hook` at bottom updated to use `__NAMESPACE__ . '\\Enhanced_Logger'`.
- Updated `performance.php`: all 5 references to `WPSeed_Enhanced_Logger` replaced
  with `WPSeed\Core\Enhanced_Logger`.
- Updated `loader.php`: removed `include_once enhanced-logger.php` from GROUP 3.
- Old file `includes/classes/enhanced-logger.php` kept for reference.
- **Verify:** Plugin activates cleanly. Performance page renders correctly.

**File 7: task-scheduler.php → includes/Core/Task_Scheduler.php**
- Created `includes/Core/Task_Scheduler.php` with `namespace WPSeed\Core;`
- Class renamed from `WPSeed_Task_Scheduler` to `Task_Scheduler`.
- Full standard file header added (ROLE: utility).
- `ActionScheduler_Store` referenced as `\ActionScheduler_Store` (global class).
- Removed `WPSeed_Task_Scheduler::instance()` self-boot from bottom of old file.
- Updated `loader.php` GROUP 4: replaced `include_once task-scheduler.php` with
  `\WPSeed\Core\Task_Scheduler::instance()` inside the Action Scheduler guard.
- `examples/task-scheduler-examples.php` still references old class name —
  intentional, it's an example file marked delete-on-clone.
- Old file `includes/classes/task-scheduler.php` kept for reference.
- **Verify:** Plugin activates cleanly. Scheduled tasks work.

**Core migration complete.** All four Core classes are now namespaced and
autoloaded: `Install`, `AJAX_Handler`, `Logger`, `Enhanced_Logger`, `Task_Scheduler`.

**File 8: dashboard-widgets.php → includes/Admin/Dashboard_Widgets.php**
- Created `includes/Admin/Dashboard_Widgets.php` with `namespace WPSeed\Admin;`
- Class renamed from `WPSeed_Dashboard_Widgets` to `Dashboard_Widgets`.
- Full standard file header added (ROLE: admin-ui).
- Updated `loader.php` GROUP 6: replaced `include_once dashboard-widgets.php`
  with `new \WPSeed\Admin\Dashboard_Widgets();`.
- No external references to old class name.
- Old file kept for reference.
- **Verify:** Plugin activates cleanly. Dashboard widgets render.

**File 9: rest-controller.php → includes/API/REST_Controller.php**
- Created `includes/API/REST_Controller.php` with `namespace WPSeed\API;`
- Class renamed from `WPSeed_REST_Controller` to `REST_Controller`.
- Extends `\WP_REST_Controller` (global namespace).
- Full standard file header added (ROLE: api-endpoint).
- Updated `loader.php` GROUP 6: removed `include_once rest-controller.php`.
- Updated `rest-example.php`: now extends `\WPSeed\API\REST_Controller`.
- Old file kept for reference.

**File 10: base-api.php → includes/API/Base_API.php**
- Created `includes/API/Base_API.php` with `namespace WPSeed\API;`
- Class renamed from `WPSeed_Base_API` to `Base_API`.
- `WPSeed_API_Logging` referenced as `\WPSeed_API_Logging` (not yet migrated).
- Full standard file header added (ROLE: api-endpoint).
- Updated `loader.php` GROUP 8: removed `include_once base-api.php`.
- Updated `api-factory.php`: `instanceof` check now uses `\WPSeed\API\Base_API`.
- Old file kept for reference.
- **Verify:** Plugin activates cleanly. REST API endpoints respond.

**Tier 1 migration complete.** All 11 files migrated to namespaced locations.

---

### Task 1.6 — Consolidate the three admin file locations

Currently admin files live in three places:
- `admin/` (top-level)
- `includes/admin/`
- `includes/Admin/` (new, from Task 1.5)

Decide on one location (`includes/Admin/`) and move the remaining files there, updating
all `include_once` paths. Do this one file at a time with activation checks between each.

**Acceptance:** All admin files are under `includes/Admin/`. The `admin/` and
`includes/admin/` directories are empty and can be deleted.

**CHANGE LOG — Task 1.6 (in progress):**

**File 1: notification-bell.php → includes/Admin/Notification_Bell.php**
- Created `includes/Admin/Notification_Bell.php` with `namespace WPSeed\Admin;`
- Class renamed from `WPSeed_Notification_Bell` to `Notification_Bell`.
- `WPSeed_Notifications` referenced as `\WPSeed_Notifications` (not yet migrated).
- Updated `loader.php` load_admin_files(): replaced `include_once` with
  `\WPSeed\Admin\Notification_Bell::init();`
- Old file kept for reference.

**File 2: uninstall-feedback.php → includes/Admin/Uninstall_Feedback.php**
- Created `includes/Admin/Uninstall_Feedback.php` with `namespace WPSeed\Admin;`
- Class renamed from `WPSeed_Uninstall_Feedback` to `Uninstall_Feedback`.
- Updated `loader.php` load_admin_files(): replaced `include_once` with
  `new \WPSeed\Admin\Uninstall_Feedback();`
- Old file kept for reference.

**Remaining in load_admin_files():** `includes/admin/admin.php`,
`includes/admin/admin-main-views.php`, `admin/config/admin-menus.php`,
`admin/notifications/notifications.php`, `admin/page/development/view/credits.php`,
`toolbars/toolbars.php` — these are procedural files and templates that will be
consolidated in Phase 2 (template migration). They are not class files and do not
benefit from namespacing.
- **Verify:** Plugin activates cleanly.

---

## PHASE 2: Structure — templates/

**Problem:** Templates live in `admin/page/`, `includes/admin/views/`, and `templates/`.
There is no consistent naming convention. An AI cannot predict where a template file
lives or what it renders from its name alone.

**Goal:** All templates in `templates/` with the naming pattern
`admin-page-{tab-name}.php`. Partials (reusable fragments) in `templates/partials/`.

### Task 2.1 — Inventory all template files

List every file that outputs HTML across `admin/page/`, `includes/admin/views/`,
`includes/admin/mainviews/`, `includes/admin/presentation/`, and `templates/`.
Add them to `docs/FILE-INVENTORY.md` with their current path and intended destination.

**Acceptance:** `FILE-INVENTORY.md` updated with all template files.

**CHANGE LOG — Task 2.1:**
- Updated `docs/FILE-INVENTORY.md` with complete template inventory.
- Mapped all 40+ template files from 5 source locations to the target structure.
- Established three-level hierarchy: `templates/pages/` (full admin pages),
  `templates/tabs/` (tab content within pages), `templates/partials/` (reusable
  fragments). This structure lets any AI predict the file path from the tab name:
  "performance tab" → `templates/tabs/development/tab-performance.php`.
- Created target directories: `templates/pages/`, `templates/tabs/`,
  `templates/tabs/development/`, `templates/partials/`,
  `templates/partials/ui-library/`.
- Identified 7 example/demo files in `includes/admin/mainviews/` to delete on clone.
- Identified 1 duplicate (`developer-checklist.php` exists in two locations).

---

### Task 2.2 — Move templates one at a time to templates/

Move each template file to `templates/admin-page-{name}.php`, updating all references
(menu registrations, `include` calls) after each move. Verify the admin page renders
correctly after each individual move.

**Acceptance:** All templates under `templates/`. Old directories empty.

**CHANGE LOG — Task 2.2:**
- Copied all 15 development tab view files from `admin/page/development/view/`
  to `templates/tabs/development/tab-{name}.php`.
- Copied all 17 UI library partial files from
  `admin/page/development/partials/ui-library/` to
  `templates/partials/ui-library/`.
- Updated `development-tabs.php` `active_tab_content()`: all 15 `require_once`
  paths now point to `templates/tabs/development/tab-{name}.php` via a
  `$tab_dir` variable. Removed duplicate `tasks` case.
- Updated `loader.php` `load_admin_files()`: `credits.php` path updated to
  `templates/tabs/development/tab-credits.php`.
- Updated `tab-theme.php`: partials path updated to
  `templates/partials/ui-library/main-container.php`.
- Updated `main-container.php`: `$wpseed_partials_dir` updated to
  `templates/partials/ui-library/`.
- Old files in `admin/page/development/view/` and
  `admin/page/development/partials/` kept for reference until verified.
- **Verify:** Plugin activates cleanly. All development tabs render correctly.

---

### Task 2.3 — Add admin-page-roadmap.php scaffold

Create `templates/admin-page-roadmap.php` as a working example roadmap tab, ported
from WPVerifier's `templates/admin-page-roadmap.php`. Strip all WPVerifier-specific
content and replace with WPSeed's own phases as placeholder content.

This is the template every plugin cloned from WPSeed will start with and populate
with its own roadmap data.

Reference: `WPVerifier/templates/admin-page-roadmap.php`

**Acceptance:** A roadmap tab renders in WPSeed's admin with accordion phases,
two-column task/architecture layout, and priority badges.

---

### Task 2.4 — Add admin-page-architecture.php scaffold

Create `templates/admin-page-architecture.php` as a working example architecture tab,
ported from WPVerifier's `templates/admin-page-architecture.php`. Replace
WPVerifier-specific content with WPSeed's own architecture (ecosystem registry,
hook system, load order).

Include the three reusable UI patterns from WPVerifier:
- Data storage display (JSON structure + annotations)
- Button behaviour table (action → handler → effect)
- Data flow diagram (numbered steps with split branches)

Reference: `WPVerifier/templates/admin-page-architecture.php`

**Acceptance:** An architecture tab renders in WPSeed's admin showing WPSeed's own
architecture using the three UI patterns.

---

## PHASE 3: Structure — assets/

**Problem:** Asset registration is split across `assets/manage-assets.php` and
`assets/queue-assets.php` as procedural files. There is no component-level CSS
organisation. Roadmap and architecture CSS does not exist yet in WPSeed.

### Task 3.1 — Convert asset files to a class

Create `assets/Asset_Manager.php` as a class with `register()` and `enqueue()`
methods, replacing the two procedural files. Update `loader.php` to instantiate
the class instead of including the old files.

Pattern to follow: `WPVerifier/assets/Asset_Manager.php`

**Acceptance:** Assets load identically. Old procedural files deleted.

---

### Task 3.2 — Create assets/css/components/ directory

Create the directory and add three component CSS files extracted from WPVerifier:

- `assets/css/components/roadmap.css` — all `.wpseed-roadmap-*` classes
  (ported from WPVerifier's `.wpv-roadmap-*` classes)
- `assets/css/components/flow-diagram.css` — `.wpseed-flow-*` classes for
  data flow diagrams
- `assets/css/components/action-docs.css` — `.wpseed-action-*` classes for
  button behaviour documentation panels

These are registered by `Asset_Manager` and enqueued only on admin pages that
use them.

**Acceptance:** Component CSS files exist and are enqueued on the roadmap and
architecture tabs.

---

### Task 3.3 — Create assets/js/admin/roadmap.js

Port WPVerifier's `assets/js/admin-roadmap.js` to WPSeed, replacing all
`wpv-` prefixes with `wpseed-`. This provides:
- Accordion expand/collapse with localStorage state persistence
- Task checkbox state persistence per plugin (keyed by plugin slug)

**Acceptance:** Roadmap tab accordions expand/collapse and remember their state
across page loads.

---

## PHASE 4: AI-Readable Code Standards

**Goal:** Any AI assistant should be able to open any WPSeed file and immediately
understand its role, dependencies, and data flow without reading the full implementation.

### Task 4.1 — Create docs/FILE-HEADER-TEMPLATE.md

Write a reference document with copy-paste file header examples for each role type.
This is the standard every WPSeed file and every plugin built from WPSeed must follow.

The ROLE tag is the most important field — it classifies the file without requiring
the AI to read its contents:

| Role | Description |
|---|---|
| `bootstrap` | Plugin entry point, constants, initial requires |
| `admin-ui` | Renders an admin page or tab template |
| `ajax-handler` | Handles `wp_ajax_*` actions |
| `data-model` | Reads/writes persistent data (DB, JSON, options) |
| `utility` | Stateless helper functions or classes |
| `hook-registration` | Registers actions/filters only, no logic |
| `template` | Pure HTML output, minimal PHP logic |
| `cli` | WP-CLI command handler |
| `api-endpoint` | REST API controller |
| `ecosystem-bridge` | Cross-plugin communication and registration |

Standard header format:

```php
<?php
/**
 * [One-line description of what this file does]
 *
 * ROLE: [role from table above]
 *
 * DEPENDS ON:
 *   - ClassName in path/to/file.php
 *   - global function_name() in path/to/file.php
 *
 * CONSUMED BY:
 *   - ClassName::method() in path/to/file.php
 *   - Hook: action_name / filter_name
 *
 * DATA FLOW:
 *   Input  → [POST | option | transient | DB table | none]
 *   Output → [option | transient | DB table | JSON response | template var | none]
 *
 * @package  WPSeed
 * @category [Admin | Core | Utilities | Ecosystem | API | CLI]
 * @since    1.0.0
 */
```

**Acceptance:** `docs/FILE-HEADER-TEMPLATE.md` exists with a complete example for
each role type.

**CHANGE LOG — Task 4.1:**
- Created `docs/FILE-HEADER-TEMPLATE.md`.
- Contains role reference table and copy-paste header examples for all 10 role types:
  bootstrap, admin-ui, ajax-handler, data-model, utility, hook-registration,
  template, cli, api-endpoint, ecosystem-bridge.
- `includes/Ecosystem/Registry.php` (Task 0.4) already follows this standard
  and serves as the live reference implementation.

---

### Task 4.2 — Apply standard headers to all Core/ files

Apply the standard header from Task 4.1 to every file in `includes/Core/`.
These are the most-read files in any plugin cloned from WPSeed, so they set the
standard for the rest.

Do one file at a time. No logic changes — headers only.

Files:
- `includes/Core/Install.php`
- `includes/Core/AJAX_Handler.php`
- `includes/Core/Logger.php`
- `includes/Core/Enhanced_Logger.php`

**Acceptance:** All Core/ files have correct ROLE, DEPENDS ON, CONSUMED BY, and
DATA FLOW fields.

---

### Task 4.3 — Apply standard headers to all Ecosystem/ files

Same as Task 4.2 for `includes/Ecosystem/`:
- `includes/Ecosystem/Registry.php`
- `includes/Ecosystem/Menu_Manager.php`
- `includes/Ecosystem/Installer.php`

**Acceptance:** All Ecosystem/ files have correct headers.

---

### Task 4.4 — Apply standard headers to all Admin/ files

Same as Task 4.2 for `includes/Admin/`. Do one file at a time.

**Acceptance:** All Admin/ files have correct headers.

---

### Task 4.5 — Create includes/Hook_Registry.php

Create a single file that lists every `add_action` and `add_filter` call in WPSeed,
grouped by category. This replaces the scattered hook registrations currently spread
across `loader.php`, `includes/admin/admin.php`, and individual class constructors.

```php
<?php
/**
 * Hook Registry — complete list of all actions and filters registered by WPSeed.
 *
 * ROLE: hook-registration
 *
 * Single source of truth for hook registration. Read this file to understand
 * the full event surface of the plugin without scanning every class.
 *
 * @package  WPSeed
 * @category Core
 * @since    2.1.0
 */

// --- Lifecycle ---
register_activation_hook( WPSEED_PLUGIN_FILE, array( 'WPSeed_Install', 'install' ) );
register_deactivation_hook( WPSEED_PLUGIN_FILE, array( 'WPSeed_Install', 'deactivate' ) );

// --- Admin ---
add_action( 'admin_menu', ... );
add_action( 'admin_enqueue_scripts', ... );

// --- AJAX ---
add_action( 'wp_ajax_wpseed_example', ... );

// --- Ecosystem ---
add_action( 'wpseed_ecosystem_register', ... );
```

**Acceptance:** `includes/Hook_Registry.php` exists and is the only place hooks are
registered. All hook registrations removed from class constructors and `loader.php`.

---

### Task 4.6 — Create docs/NAMING-CONVENTIONS.md

Document the exact naming patterns used across WPSeed so AI assistants can predict
file and class names without guessing:

```
Boilerplate prefix (replace when cloning):
  Constants:  WPSEED_
  Functions:  wpseed_
  Classes:    WPSeed_

File naming:       kebab-case.php
Class naming:      PascalCase with prefix: WPSeed_Class_Name
Method naming:     snake_case
Constant naming:   SCREAMING_SNAKE_CASE: WPSEED_CONSTANT_NAME
Hook naming:       {prefix}_{noun}_{verb}    e.g. wpseed_plugin_registered
AJAX actions:      {prefix}_{action}         e.g. wpseed_save_config
Option names:      {prefix}_{setting}        e.g. wpseed_version
Nonce actions:     {prefix}_{action}         e.g. wpseed_save_settings
```

**Acceptance:** `docs/NAMING-CONVENTIONS.md` exists and covers all naming patterns.

**CHANGE LOG — Task 4.6:**
- Created `docs/NAMING-CONVENTIONS.md`.
- Covers: boilerplate prefix replacement table, file naming, class naming,
  namespace directory map, method naming, constant naming, hook naming,
  option naming, nonce action naming, DB table naming, CSS/JS handle naming.
- Includes worked examples for cloning to EvolveWP.Verifier throughout.

---

## PHASE 5: Class Docblock Standard

**Goal:** Every class and every public method has a docblock that describes its
single responsibility and contract. This is what allows AI to navigate any class
without reading its full implementation.

### Task 5.1 — Update class docblocks in includes/Core/

For each class in `includes/Core/`, ensure the class docblock states:
- Single responsibility (one sentence)
- What it does NOT do (prevents scope creep)
- Package and since tags

For each public method, ensure the docblock states:
- What the method does
- Why it exists (what problem it solves)
- What callers should expect (return value, side effects)
- Full `@param` and `@return` tags

Do one class at a time. No logic changes.

---

### Task 5.2 — Update class docblocks in includes/Ecosystem/

Same as Task 5.1 for `includes/Ecosystem/`.

---

### Task 5.3 — Update class docblocks in includes/Admin/

Same as Task 5.1 for `includes/Admin/`.

---

## PHASE 6: Clone Tooling

**Goal:** Creating a new plugin from WPSeed should take minutes, not hours.
The mass find-and-replace of `wpseed`/`WPSEED`/`WPSeed_` is currently manual
and error-prone.

### Task 6.1 — Document the manual clone process

Before automating anything, write `docs/CLONING-GUIDE.md` that documents every
step required to create a new plugin from WPSeed manually. This becomes the
specification for the automated command in Task 6.2.

Steps to document:
1. Copy the WPSeed directory
2. Rename the main plugin file
3. Find-and-replace all prefix variants (case-sensitive list)
4. Update plugin header (Name, Description, Text Domain, Domain Path)
5. Update `composer.json` package name
6. Delete example/demo files (list from FILE-INVENTORY.md)
7. Update GitHub URL constants
8. Run activation check

**Acceptance:** `docs/CLONING-GUIDE.md` exists with a complete, tested manual process.

**CHANGE LOG — Task 6.1:**
- Created `docs/CLONING-GUIDE.md`.
- Documents all 9 steps: copy directory, rename main file, 8 find-and-replace
  passes (in correct case-sensitive order), update plugin header, update
  composer.json, delete example files, update URL constants, update ecosystem
  self-registration, activation check.
- Includes a final checklist for verification.
- The find-and-replace order is critical — `WordPressPluginSeed` before `WPSeed_`
  before `wpseed_` to avoid partial replacements.

---

### Task 6.2 — Add WP-CLI clone command

Add a `WPSeed_CLI_Clone_Command` class to `includes/classes/cli-commands.php`
implementing:

```bash
wp wpseed clone --slug=my-plugin --name="My Plugin" --prefix=mp
```

The command follows the steps documented in Task 6.1 exactly.

**Acceptance:** Running the command produces a working plugin skeleton with all
`wpseed` references replaced, verified by activating the cloned plugin.

---

## Task Priority Summary

| Task | Phase | Effort | Risk | Do First? |
|---|---|---|---|---|
| 1.1 Document load order | 1 | 30 min | None | ✅ Yes |
| 1.2 Classify includes/ files | 1 | 1 hour | None | ✅ Yes |
| 4.1 FILE-HEADER-TEMPLATE.md | 4 | 30 min | None | ✅ Yes |
| 4.6 NAMING-CONVENTIONS.md | 4 | 30 min | None | ✅ Yes |
| 6.1 CLONING-GUIDE.md | 6 | 1 hour | None | ✅ Yes |
| 1.3 Group includes in loader.php | 1 | 1 hour | Low | After 1.2 |
| 1.4 Create subdirectories | 1 | 15 min | None | After 1.3 |
| 1.5 Move files one at a time | 1 | 2-3 hours | Medium | After 1.4 |
| 4.2–4.4 Apply headers | 4 | 2 hours | None | After 1.5 |
| 4.5 Hook_Registry.php | 4 | 1 hour | Low | After 1.5 |
| 2.1–2.2 Move templates | 2 | 1-2 hours | Medium | After 1.6 |
| 3.1 Asset_Manager class | 3 | 1 hour | Low | After 2.2 |
| 2.3 Roadmap tab | 2 | 2 hours | Low | After 3.1 |
| 2.4 Architecture tab | 2 | 2 hours | Low | After 2.3 |
| 3.2–3.3 Component CSS/JS | 3 | 1 hour | None | After 2.4 |
| 5.1–5.3 Class docblocks | 5 | 3 hours | None | Any time |
| 6.2 CLI clone command | 6 | 3 hours | Low | After 6.1 |

**Start with the documentation tasks** (1.1, 1.2, 4.1, 4.6, 6.1) — they cost nothing,
introduce no risk, and produce the reference material needed for every subsequent task.
