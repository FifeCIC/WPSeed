# WPSeed File Inventory

> Classification of every file in `includes/`. Used by Task 1.3 (grouping loader.php)
> and Task 1.5 (deciding move order). Updated as files are migrated to new locations.
>
> Classifications:
> - **core** — must load on every request
> - **admin-only** — only needed on admin requests
> - **optional** — only needed when a specific feature is enabled
> - **example** — demo/boilerplate code, delete when cloning
> - **migrated** — moved to new location, old file is a stub or deleted

---

## includes/classes/

| File | Classification | Description | Delete on clone? |
|---|---|---|---|
| `ajax.php` | core | AJAX endpoint registration and nonce handling | No |
| `architecture-mapper.php` | admin-only | Maps plugin architecture for the Architecture tab | No |
| `autoloader.php` | core | Legacy SPL autoloader — to be removed in Task 0.5 | No (replaced by Composer) |
| `carbon-fields-integration.php` | optional | Carbon Fields library initialisation | No |
| `cli-commands.php` | optional | WP-CLI command definitions | No |
| `dashboard-widgets.php` | admin-only | WordPress dashboard widget registration | No |
| `data-freshness-manager.php` | core | Tracks cache freshness for data sources | No |
| `debug.php` | core | Debug utilities and helpers | No |
| `dependencies.php` | optional | Plugin dependency checker and admin notices | No |
| `developer-flow-logger.php` | core | Dev-mode flow logging (no-op in production) | No |
| `developer-mode.php` | core | Developer mode detection and environment checks | No |
| `ecosystem-installer.php` | optional | One-click ecosystem plugin installer UI | No |
| `ecosystem-menu-manager.php` | optional | Shared menu placement in ecosystem mode | No |
| `ecosystem-registry.php` | core | **MIGRATED** → `includes/Ecosystem/Registry.php` | No |
| `enhanced-logger.php` | core | Query Monitor-style request logger | No |
| `extension-installer.php` | optional | Extension/add-on installer | No |
| `frontend-scripts.php` | optional | Frontend asset enqueueing | No |
| `github-sync.php` | optional | GitHub repository sync | Yes |
| `i18n.php` | core | Internationalisation / text domain loading | No |
| `install.php` | core | Activation, DB tables, roles, version checking | No |
| `library-manager.php` | optional | Bundled library management | No |
| `library-update-monitor.php` | optional | Monitors bundled library versions | No |
| `license-client.php` | optional | Premium licence validation client | No |
| `listener.php` | core | Request listener for custom endpoints | No |
| `multisite.php` | optional | WordPress multisite support | No |
| `notification-bell.php` | admin-only | Admin toolbar notification bell | No |
| `object-registry.php` | core | Generic singleton object registry | No |
| `rest-controller.php` | core | Abstract base class for REST API controllers | No |
| `rest-example.php` | example | Example REST controller — delete when cloning | **Yes** |
| `settings-import-export.php` | admin-only | Settings import/export UI and handlers | No |
| `task-scheduler.php` | optional | Action Scheduler wrapper (loaded with library) | No |
| `unified-feature.php` | example | Example unified feature pattern — delete when cloning | **Yes** |
| `unified-logger.php` | core | Structured trace logger with loop detection | No |
| `uninstall-feedback.php` | admin-only | Uninstall feedback form | No |
| `verification-logger.php` | optional | Verification-specific logging | No |

---

## includes/admin/

| File | Classification | Description | Delete on clone? |
|---|---|---|---|
| `admin.php` | admin-only | Main admin class — hooks, includes, redirects | No |
| `admin-assets.php` | admin-only | Admin asset enqueueing | No |
| `admin-dashboard.php` | admin-only | Dashboard page handler | No |
| `admin-functions.php` | admin-only | Admin helper functions | No |
| `admin-help.php` | admin-only | Help tab registration | No |
| `admin-main-views.php` | admin-only | Main admin view routing | No |
| `admin-menus.php` | admin-only | Menu registration | No |
| `admin-notices.php` | admin-only | Admin notice management | No |
| `admin-pointers.php` | admin-only | WordPress pointer tooltips | No |
| `admin-settings.php` | admin-only | Settings page handler | No |
| `admin-setup-wizard.php` | admin-only | Setup wizard | No |
| `mainviews/default-advanced.php` | example | Example advanced list table view | **Yes** |
| `mainviews/default-items.php` | example | Example list table items | **Yes** |
| `mainviews/listtable-demo-advanced.php` | example | Advanced list table demo | **Yes** |
| `mainviews/listtable-demo.php` | example | List table demo | **Yes** |
| `mainviews/team-advanced.php` | example | Team list table demo | **Yes** |
| `mainviews/team-items.php` | example | Team items demo | **Yes** |
| `notices/custom.php` | admin-only | Custom notice template | No |
| `notices/install.php` | admin-only | Install notice template | No |
| `notices/update.php` | admin-only | Update notice template | No |
| `notices/updated.php` | admin-only | Updated notice template | No |
| `notices/updating.php` | admin-only | Updating notice template | No |
| `presentation/barchart.php` | example | Bar chart presentation example | **Yes** |
| `settings/class-settings-repeater.php` | admin-only | Settings repeater field class | No |
| `settings/settings-example.php` | example | Settings example — delete when cloning | **Yes** |
| `settings/settings-github.php` | optional | GitHub settings section | Yes |
| `settings/settings-jquery-ui.php` | admin-only | jQuery UI settings section | No |
| `settings/settings-license.php` | optional | Licence settings section | No |
| `settings/settings-page.php` | admin-only | Settings page renderer | No |
| `settings/settings-repeater-example.php` | example | Repeater field example | **Yes** |
| `settings/settings-sections.php` | admin-only | Settings section definitions | No |
| `settings/settings-tools.php` | admin-only | Tools settings section | No |
| `views/developer-checklist.php` | admin-only | Developer checklist view | No |
| `views/github-sync.php` | optional | GitHub sync view | Yes |
| `views/html-admin-page.php` | admin-only | Main admin page HTML | No |
| `views/html-admin-settings.php` | admin-only | Settings page HTML | No |

---

## includes/functions/

| File | Classification | Description | Delete on clone? |
|---|---|---|---|
| `core.php` | core | Global helper functions | No |
| `database.php` | core | Database helper functions | No |
| `github-sync-ajax.php` | optional | GitHub sync AJAX handlers | Yes |
| `validate.php` | core | Input validation helpers | No |

---

## api/

| File | Classification | Description | Delete on clone? |
|---|---|---|---|
| `base-api.php` | core | Abstract base class for external API integrations | No |
| `api-directory.php` | core | API provider directory | No |
| `api-factory.php` | core | API instance factory | No |

---

## Template files — current locations and target destinations

Target structure:
```
templates/
  pages/          ← full admin pages (one file per menu item)
  tabs/           ← tab content within pages (one file per tab)
    development/  ← tabs within the development page
  partials/       ← reusable HTML fragments
    ui-library/   ← UI component showcase partials
```

Naming convention: `admin-page-{name}.php` for pages, `tab-{name}.php` for tabs,
`partial-{name}.php` for partials.

### Pages (full admin pages)

| Current path | Target path | Description |
|---|---|---|
| `includes/admin/views/html-admin-page.php` | `templates/pages/admin-page-main.php` | Main plugin page with tab/subtab routing |
| `includes/admin/views/html-admin-settings.php` | `templates/pages/admin-page-settings.php` | Settings page |
| `admin/page/development/development-tabs.php` | `templates/pages/admin-page-development.php` | Development page wrapper + tab nav |
| `admin/page/notification-center.php` | `templates/pages/admin-page-notifications.php` | Notification center |
| `admin/page/license-management.php` | `templates/pages/admin-page-license.php` | License management |

### Tabs (development page sub-views)

| Current path | Target path | Description |
|---|---|---|
| `admin/page/development/view/assets-tracker.php` | `templates/tabs/development/tab-assets.php` | Assets tracker |
| `admin/page/development/view/performance.php` | `templates/tabs/development/tab-performance.php` | Performance monitor |
| `admin/page/development/view/theme-info.php` | `templates/tabs/development/tab-theme.php` | Theme info / UI library |
| `admin/page/development/view/debug-log.php` | `templates/tabs/development/tab-debug-log.php` | Debug log viewer |
| `admin/page/development/view/database.php` | `templates/tabs/development/tab-database.php` | Database info |
| `admin/page/development/view/phpinfo.php` | `templates/tabs/development/tab-phpinfo.php` | PHP info |
| `admin/page/development/view/tasks-monitor.php` | `templates/tabs/development/tab-tasks-monitor.php` | Tasks monitor |
| `admin/page/development/view/tasks.php` | `templates/tabs/development/tab-tasks.php` | Tasks |
| `admin/page/development/view/libraries.php` | `templates/tabs/development/tab-libraries.php` | Libraries |
| `admin/page/development/view/credits.php` | `templates/tabs/development/tab-credits.php` | Credits |
| `admin/page/development/view/docs.php` | `templates/tabs/development/tab-docs.php` | Documentation |
| `admin/page/development/view/dev-checklist.php` | `templates/tabs/development/tab-checklist.php` | Developer checklist |
| `admin/page/development/view/layouts.php` | `templates/tabs/development/tab-layouts.php` | Layouts |
| `admin/page/development/view/diagrams.php` | `templates/tabs/development/tab-diagrams.php` | Diagrams |
| `admin/page/development/view/architecture.php` | `templates/tabs/development/tab-architecture.php` | Architecture |

### Partials (reusable fragments)

| Current path | Target path | Description |
|---|---|---|
| `admin/page/development/partials/ui-library/accordion-components.php` | `templates/partials/ui-library/accordion-components.php` | Accordion UI components |
| `admin/page/development/partials/ui-library/animation-showcase.php` | `templates/partials/ui-library/animation-showcase.php` | Animation showcase |
| `admin/page/development/partials/ui-library/button-components.php` | `templates/partials/ui-library/button-components.php` | Button components |
| `admin/page/development/partials/ui-library/chart-visualization.php` | `templates/partials/ui-library/chart-visualization.php` | Chart visualization |
| `admin/page/development/partials/ui-library/color-palette.php` | `templates/partials/ui-library/color-palette.php` | Color palette |
| `admin/page/development/partials/ui-library/controls-actions.php` | `templates/partials/ui-library/controls-actions.php` | Controls and actions |
| `admin/page/development/partials/ui-library/data-analysis-components.php` | `templates/partials/ui-library/data-analysis-components.php` | Data analysis components |
| `admin/page/development/partials/ui-library/filters-search.php` | `templates/partials/ui-library/filters-search.php` | Filters and search |
| `admin/page/development/partials/ui-library/form-components.php` | `templates/partials/ui-library/form-components.php` | Form components |
| `admin/page/development/partials/ui-library/main-container.php` | `templates/partials/ui-library/main-container.php` | Main container |
| `admin/page/development/partials/ui-library/modal-components.php` | `templates/partials/ui-library/modal-components.php` | Modal components |
| `admin/page/development/partials/ui-library/notice-components.php` | `templates/partials/ui-library/notice-components.php` | Notice components |
| `admin/page/development/partials/ui-library/pagination-controls.php` | `templates/partials/ui-library/pagination-controls.php` | Pagination controls |
| `admin/page/development/partials/ui-library/pointers.php` | `templates/partials/ui-library/pointers.php` | Pointer tooltips |
| `admin/page/development/partials/ui-library/progress-indicators.php` | `templates/partials/ui-library/progress-indicators.php` | Progress indicators |
| `admin/page/development/partials/ui-library/status-indicators.php` | `templates/partials/ui-library/status-indicators.php` | Status indicators |
| `admin/page/development/partials/ui-library/tooltips.php` | `templates/partials/ui-library/tooltips.php` | Tooltips |

### Other admin views

| Current path | Target path | Description |
|---|---|---|
| `includes/admin/views/developer-checklist.php` | `templates/tabs/development/tab-checklist.php` | Duplicate of dev-checklist — consolidate |
| `includes/admin/views/github-sync.php` | `templates/tabs/development/tab-github-sync.php` | GitHub sync view |
| `includes/admin/mainviews/default-advanced.php` | **EXAMPLE — delete on clone** | List table demo |
| `includes/admin/mainviews/default-items.php` | **EXAMPLE — delete on clone** | List table demo |
| `includes/admin/mainviews/listtable-demo-advanced.php` | **EXAMPLE — delete on clone** | List table demo |
| `includes/admin/mainviews/listtable-demo.php` | **EXAMPLE — delete on clone** | List table demo |
| `includes/admin/mainviews/team-advanced.php` | **EXAMPLE — delete on clone** | List table demo |
| `includes/admin/mainviews/team-items.php` | **EXAMPLE — delete on clone** | List table demo |
| `templates/example.php` | **EXAMPLE — delete on clone** | Template example |

| Current path | Target path | Namespace |
|---|---|---|
| `includes/classes/ecosystem-registry.php` | `includes/Ecosystem/Registry.php` ✅ done | `WPSeed\Ecosystem` |
| `includes/classes/ecosystem-menu-manager.php` | `includes/Ecosystem/Menu_Manager.php` | `WPSeed\Ecosystem` |
| `includes/classes/ecosystem-installer.php` | `includes/Ecosystem/Installer.php` | `WPSeed\Ecosystem` |
| `includes/classes/install.php` | `includes/Core/Install.php` | `WPSeed\Core` |
| `includes/classes/ajax.php` | `includes/Core/AJAX_Handler.php` | `WPSeed\Core` |
| `includes/classes/unified-logger.php` | `includes/Core/Logger.php` | `WPSeed\Core` |
| `includes/classes/enhanced-logger.php` | `includes/Core/Enhanced_Logger.php` | `WPSeed\Core` |
| `includes/classes/task-scheduler.php` | `includes/Core/Task_Scheduler.php` | `WPSeed\Core` |
| `includes/classes/dashboard-widgets.php` | `includes/Admin/Dashboard_Widgets.php` | `WPSeed\Admin` |
| `includes/classes/notification-bell.php` | `includes/Admin/Notification_Bell.php` | `WPSeed\Admin` |
| `api/base-api.php` | `includes/API/Base_API.php` | `WPSeed\API` |
| `includes/classes/rest-controller.php` | `includes/API/REST_Controller.php` | `WPSeed\API` |
