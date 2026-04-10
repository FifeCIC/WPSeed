# WPSeed — Development Roadmap

> WPSeed is a boilerplate. Its job is to give every EvolveWP plugin a clean,
> consistent, AI-navigable starting point. Development here is about standards,
> structure, and reusable patterns — not features.
>
> All `wpseed` / `WPSEED_` / `WPSeed_` strings are placeholders. When a new
> plugin is created from WPSeed, these are mass-replaced with the plugin's
> own prefix via `docs/CLONING-GUIDE.md`.

---

## Completed Phases

### Phase 0: Composer and Namespace Foundation ✅

Established PSR-4 autoloading via Composer. Namespace `WPSeed\` maps to
`includes/`. Created directory structure: `Core/`, `Admin/`, `Ecosystem/`,
`Utilities/`, `API/`, `CLI/`. Removed hand-rolled SPL autoloader.

### Phase 1: Structure — includes/ ✅

Documented load order in `loader.php` (11 groups). Classified all files in
`docs/FILE-INVENTORY.md`. Migrated 11 Tier 1 files to namespaced locations:

| Old location | New location | Namespace |
|---|---|---|
| `classes/ecosystem-registry.php` | `Ecosystem/Registry.php` | `WPSeed\Ecosystem` |
| `classes/ecosystem-menu-manager.php` | `Ecosystem/Menu_Manager.php` | `WPSeed\Ecosystem` |
| `classes/ecosystem-installer.php` | `Ecosystem/Installer.php` | `WPSeed\Ecosystem` |
| `classes/install.php` | `Core/Install.php` | `WPSeed\Core` |
| `classes/ajax.php` | `Core/AJAX_Handler.php` | `WPSeed\Core` |
| `classes/unified-logger.php` | `Core/Logger.php` | `WPSeed\Core` |
| `classes/enhanced-logger.php` | `Core/Enhanced_Logger.php` | `WPSeed\Core` |
| `classes/task-scheduler.php` | `Core/Task_Scheduler.php` | `WPSeed\Core` |
| `classes/dashboard-widgets.php` | `Admin/Dashboard_Widgets.php` | `WPSeed\Admin` |
| `classes/rest-controller.php` | `API/REST_Controller.php` | `WPSeed\API` |
| `api/base-api.php` | `API/Base_API.php` | `WPSeed\API` |

Also migrated: `Notification_Bell.php`, `Uninstall_Feedback.php` to `Admin/`.

### Phase 2: Structure — templates/ ✅

Moved all templates to `templates/` with three-level hierarchy:
`pages/`, `tabs/development/`, `partials/ui-library/`. Created roadmap tab
(accordion phases, localStorage persistence) and architecture tab (data
storage display, key functions table, data flow diagram).

### Phase 3: Structure — assets/ ✅

Asset Manager and Asset Queue classes already existed and work. Created
component CSS for roadmap and architecture tabs. Enqueued via
`development-tabs.php`.

### Phase 4: AI-Readable Code Standards ✅

Created `docs/FILE-HEADER-TEMPLATE.md` with 10 role types. Applied standard
headers to all `Core/`, `Ecosystem/`, and namespaced `Admin/` files. Created
`includes/Hook_Registry.php` as reference. Created `docs/NAMING-CONVENTIONS.md`.

### Phase 5: Class Docblock Standard (partial)

All newly created classes (v3.1.0) have full docblocks with `@since`,
`@version`, `@param`, `@return`, `@throws`, `@see` tags. Legacy classes
in `includes/classes/` will be updated when migrated.

### Phase 6: Clone Tooling ✅

Created `docs/CLONING-GUIDE.md` with 9-step manual process and checklist.
WP-CLI clone command (Task 6.2) deferred — manual process works.

### Phase 7: CSS & JavaScript Audit ✅

- **7.1** ✅ Created `docs/ASSET-INVENTORY.md` classifying all 144 CSS and 19 JS files
- **7.2** ✅ Replaced all `--tp-` → `--wpseed-` (46 CSS), `.tp-` → `.wpseed-` (6 CSS, 1 JS, 7 PHP)
- **7.3** ✅ Cleaned `variables.css`: removed aliases, trading vars, duplicates. Added focus-ring, accent tokens
- **7.4** ✅ Deleted 61 TradePress/dead files (144 → 83 CSS files, 42% reduction)
- **7.5** ✅ Removed invalid `@extend` from 27 files. Removed duplicate button defs from forms.css. Removed TradePress sections from forms.css. Scoped `reset.css` and `typography.css` to `.wpseed-admin-wrap`
- **7.6** ✅ JS audit — replaced `tp-` refs in `development-tabs.js`, all files clean
- **7.7** ✅ Rewrote `style-assets.php` — all 83 files registered with page targeting and dependencies. Fixed dependency prefix bug in `queue-assets.php`
- **7.8** ✅ Created `docs/ASSET-GUIDE.md` — variable system, naming convention, page-based loading, cloning guide

### New Boilerplate Capabilities (v3.1.0) ✅

- **Connector Interface** (`API/Connector_Interface.php`) — `test_connection()`, `get_capabilities()`, `execute()`. `Base_API` implements it. API Directory supports runtime registration. API Factory validates against interface. Multi-account credential support. Docs: `docs/CONNECTORS.md`
- **Capability Manager** (`Core/Capability_Manager.php`) — Registration with metadata, install/uninstall lifecycle, `wpseed_user_can()` with filter hook. 7 default caps. Install.php delegates to it. Docs: `docs/CAPABILITIES.md`
- **REST Bridge** (`API/REST_Bridge.php`) — Central endpoint registry, capability-based permissions, auto-generated connector routes, opt-in response helpers. REST_Controller integration via `register_endpoint()`. Docs: `docs/REST-BRIDGE.md`
- **Dependency Version Checking** — `requires_core_version` in Ecosystem Registry. Version comparison on `plugins_loaded`. Admin notice on mismatch.

---

## Global Accessor Functions (functions.php)

| Function | Returns | Since |
|---|---|---|
| `WPSeed()` | Main plugin instance | 1.0.0 |
| `wpseed_ecosystem()` | Ecosystem Registry singleton | 3.0.0 |
| `wpseed_log()` | Logger singleton | 3.0.0 |
| `wpseed_trace()` | Records a trace entry | 3.0.0 |
| `wpseed_connector()` | API connector instance | 3.1.0 |
| `wpseed_user_can()` | Capability check (bool) | 3.1.0 |
| `wpseed_rest_endpoints()` | Registered REST endpoint metadata | 3.1.0 |

---

## Items Deferred to EvolveWP Core

| Item | Why deferred | Where planned |
|---|---|---|
| Typed Settings Manager | Runtime feature, not boilerplate | EvolveWP Core Stage B |
| Database Abstraction (Table + Query) | Needed by plugins with custom tables | EvolveWP Core Stage B |
| Frontend Template System | Needed by plugins with public UI | EvolveWP Core Stage D |
| Frontend REST API | Public-facing endpoints for portals | EvolveWP Core Stage D |
| Cloud Services Foundation | Backup, storage, hack detection | EvolveWP Core Stage D |
| Webhook System | Event notifications to external services | EvolveWP Core Stage D |

## Items Deferred to Future WPSeed Pass

| Item | Notes |
|---|---|
| Asset Manager namespacing | Existing classes work, low risk to defer |
| Legacy admin file migration | `includes/admin/*.php` procedural files |
| Centralised hook registration | Moving `add_action` from constructors to Hook_Registry |
| Unmigrated class references | `WPSeed_Developer_Mode`, `WPSeed_Notifications`, `WPSeed_API_Logging` |
| WP-CLI clone command (Task 6.2) | Manual process works, automate later |
