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

## PHASE 1: Structure — loader.php and the includes/ directory

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

---

### Task 1.3 — Group the includes in loader.php by classification

Using the classification from Task 1.2, reorganise the `include_once` calls in
`loader.php` into clearly labelled groups matching the comment block from Task 1.1.
No files are moved yet — only the order and grouping of the `include_once` calls
changes, with a comment header above each group.

**Acceptance:** `loader.php` loads the same files as before, in the same logical order,
but grouped with comment headers. Verify the plugin still activates cleanly.

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

---

### Task 2.2 — Move templates one at a time to templates/

Move each template file to `templates/admin-page-{name}.php`, updating all references
(menu registrations, `include` calls) after each move. Verify the admin page renders
correctly after each individual move.

**Acceptance:** All templates under `templates/`. Old directories empty.

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
