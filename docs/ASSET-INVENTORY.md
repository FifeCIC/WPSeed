# WPSeed Asset Inventory

> Classification of every CSS and JS file in `assets/`. Used by Phase 7 of
> ROADMAP-WPSEED.md to determine what to keep, fix, or delete before cloning.
>
> Classifications:
> - **core** — every plugin needs this
> - **development** — only needed on the Development admin page
> - **feature** — needed by a specific optional feature (keep, but document)
> - **tradepress** — TradePress legacy, DELETE on clone
> - **dead** — not referenced anywhere, DELETE

---

## assets/css/base/

| File | Classification | --tp- refs? | Notes |
|---|---|---|---|
| `variables.css` | core | Yes (aliases) | Design tokens. Remove --tp- aliases, trading vars, duplicate swatches |
| `reset.css` | core | Yes | All --tp- refs need replacing with --wpseed- |
| `typography.css` | core | Yes | All --tp- refs need replacing with --wpseed- |

---

## assets/css/components/

| File | Classification | --tp- refs? | Notes |
|---|---|---|---|
| `accordion.css` | core | No | Accordion expand/collapse |
| `alerts.css` | core | Yes | Alert/notice boxes |
| `animations.css` | development | No | CSS animation showcase |
| `architecture.css` | development | No | Architecture tab styles |
| `badges.css` | core | Yes | Status badges |
| `buttons.css` | core | Yes | Also has .tp- class names — needs rename |
| `candlesticks.css` | **tradepress** | No | Trading candlestick charts |
| `cards.css` | core | Yes | Card UI elements |
| `charts.css` | development | Yes | Chart visualization |
| `code-blocks.css` | core | Yes | Code display blocks |
| `content-sections.css` | core | Yes | Content section layouts |
| `controls.css` | core | Yes | Control components |
| `data-analysis.css` | development | Yes | Data analysis components |
| `data-explorer.css` | development | Yes | Data explorer UI |
| `data-filters.css` | development | Yes | Data filter components |
| `diagnostics.css` | development | Yes | Also has .tp- class names |
| `experiments.css` | **tradepress** | Yes | Trading experiments |
| `filters.css` | core | Yes | Filter components |
| `form-controls.css` | core | Yes | Form control elements |
| `forms-wizard.css` | core | No | Multi-step form wizard |
| `forms.css` | core | Yes | Form elements |
| `heatmaps.css` | **tradepress** | No | Trading heatmaps |
| `indicators.css` | **tradepress** | No | Trading indicators |
| `lists.css` | core | Yes | List components |
| `log-viewer.css` | development | Yes | Log viewer UI |
| `meta-data.css` | core | Yes | Metadata display |
| `metrics.css` | core | Yes | Metric cards/displays |
| `modals.css` | core | Yes | Modal dialogs |
| `mode-indicators.css` | **tradepress** | No | Trading mode indicators |
| `notices.css` | core | Yes | Admin notices |
| `pagination.css` | core | Yes | Pagination controls |
| `pointers.css` | development | No | WordPress pointer styles |
| `progress.css` | core | No | Progress bars/indicators |
| `roadmap.css` | development | No | Roadmap tab styles |
| `status-indicators.css` | core | Yes | Status dot/badge indicators |
| `status-messages.css` | core | Yes | Status message boxes |
| `status.css` | core | Yes | General status styles |
| `steps.css` | core | No | Step indicators |
| `switches.css` | core | No | Toggle switches. Has .tp- class names |
| `tables.css` | core | Yes | Data tables |
| `task-details.css` | development | No | Task detail panels |
| `task-items.css` | development | Yes | Task list items |
| `task-selection.css` | development | No | Task selection UI |
| `tooltips.css` | core | Yes | Tooltip system |
| `working-notes.css` | development | No | Working notes display |

---

## assets/css/layouts/

| File | Classification | --tp- refs? | Notes |
|---|---|---|---|
| `admin.css` | core | Yes | Admin page layout |
| `api.css` | core | Yes | API management layout |
| `automation.css` | **tradepress** | Yes | Trading automation layout |
| `database.css` | feature | Yes | Database admin layout |
| `features.css` | core | Yes | Feature toggle layout |
| `grids.css` | core | Yes | CSS grid utilities |
| `layouts.css` | core | Yes | General layout utilities |
| `research.css` | **tradepress** | No | Trading research layout |
| `responsive.css` | core | Yes | Responsive breakpoints |
| `shortcodes.css` | feature | Yes | Shortcode output layout |
| `tabs.css` | core | Yes | Tab navigation layout |
| `tradingplatforms.css` | **tradepress** | No | Trading platform layout |

---

## assets/css/pages/

| File | Classification | Notes |
|---|---|---|
| `alert-decoder.css` | **tradepress** | Trading alert decoder |
| `analysis.css` | **tradepress** | Trading analysis page |
| `api-discord.css` | **tradepress** | Discord-specific API page |
| `api-management.css` | core | API management page — keep |
| `assets.css` | development | Assets tracker page |
| `configure-directives.css` | **tradepress** | Trading directives config |
| `dashboard.css` | core | Main dashboard page |
| `data-elements.css` | **tradepress** | Trading data elements |
| `data.css` | **tradepress** | Trading data page |
| `development-assets.css` | development | Development assets tab |
| `development-current-task.css` | development | Current task display |
| `development-tasks.css` | development | Tasks tab |
| `development.css` | development | Development page base |
| `direct-api-test.css` | **tradepress** | Direct API testing |
| `directives-status.css` | **tradepress** | Trading directives status |
| `directives-testing.css` | **tradepress** | Trading directives testing |
| `discord-settings.css` | **tradepress** | Discord settings |
| `discord-simple-admin.css` | **tradepress** | Discord admin |
| `earnings.css` | **tradepress** | Earnings calendar |
| `economic-calendar.css` | **tradepress** | Economic calendar |
| `education-dashboard.css` | **tradepress** | Education dashboard |
| `education-pointers.css` | **tradepress** | Education pointers |
| `focus-advisor.css` | **tradepress** | Focus advisor |
| `jquery-ui.css` | core | jQuery UI overrides |
| `market-correlations.css` | **tradepress** | Market correlations |
| `news-feed.css` | **tradepress** | News feed |
| `price-forecast.css` | **tradepress** | Price forecast |
| `research-earnings-tab.css` | **tradepress** | Research earnings |
| `research-news-feed.css` | **tradepress** | Research news |
| `research-social-networks.css` | **tradepress** | Research social |
| `sandbox.css` | **tradepress** | Sandbox/testing |
| `scoring-directives-logs.css` | **tradepress** | Scoring logs |
| `scoring-directives-overview.css` | **tradepress** | Scoring overview |
| `scoring-directives.css` | **tradepress** | Scoring directives |
| `scoring-strategies.css` | **tradepress** | Scoring strategies |
| `sector-rotation.css` | **tradepress** | Sector rotation |
| `sees-demo.css` | **tradepress** | SEES demo |
| `settings-database.css` | feature | Database settings |
| `settings-general.css` | core | General settings page |
| `settings-shortcodes.css` | feature | Shortcode settings |
| `settings-tab-features.css` | core | Feature tab settings |
| `setup.css` | core | Setup wizard page |
| `socialplatforms-discord-settings.css` | **tradepress** | Social discord |
| `socialplatforms-settings.css` | **tradepress** | Social settings |
| `socialplatforms-stocktwits.css` | **tradepress** | StockTwits |
| `socialplatforms-switches.css` | **tradepress** | Social switches |
| `socialplatforms-twitter.css` | **tradepress** | Twitter |
| `stockvip.css` | **tradepress** | StockVIP |
| `tasks.css` | development | Tasks page |
| `trading-create-strategy.css` | **tradepress** | Create strategy |
| `trading-portfolio.css` | **tradepress** | Portfolio |
| `trading-strategies.css` | **tradepress** | Strategies |
| `trading.css` | **tradepress** | Trading main |
| `tradingplatforms-alphavantage.css` | **tradepress** | Alpha Vantage |
| `tradingplatforms-api-switches.css` | **tradepress** | API switches |
| `tradingplatforms-comparisons-toggles.css` | **tradepress** | Comparison toggles |
| `tradingplatforms-comparisons.css` | **tradepress** | Comparisons |
| `tradingplatforms-config-data-only.css` | **tradepress** | Config data |
| `tradingplatforms-config-trading.css` | **tradepress** | Config trading |
| `tradingplatforms-diagnostic-buttons.css` | **tradepress** | Diagnostic buttons |
| `tradingplatforms-endpoints-table.css` | **tradepress** | Endpoints table |
| `tradingplatforms-endpoints.css` | **tradepress** | Endpoints |
| `tradingplatforms-tradingapi.css` | **tradepress** | Trading API |
| `ui-library.css` | development | UI library showcase |
| `watchlists-active-symbols.css` | **tradepress** | Active symbols |
| `watchlists-create-watchlist.css` | **tradepress** | Create watchlist |
| `watchlists-user-watchlists.css` | **tradepress** | User watchlists |

---

## assets/css/dark/

| File | Classification | --tp- refs? | Notes |
|---|---|---|---|
| `admin.css` | core | No | Dark mode admin overrides |
| `tabs.css` | core | No | Dark mode tab overrides |
| `variables.css` | core | Yes | Dark mode variable overrides |

---

## assets/css/templates/

| File | Classification | Notes |
|---|---|---|
| `symbol.css` | **tradepress** | Trading symbol template |

---

## assets/css/ (root level)

| File | Classification | Notes |
|---|---|---|
| `accordion-table.css` | core | Accordion table hybrid |
| `activation.css` | core | Activation page styles |
| `activation.scss` | **dead** | SCSS source — no build pipeline |
| `admin.css` | core | Main admin stylesheet |
| `admin.scss` | **dead** | SCSS source — no build pipeline |
| `credits.css` | development | Credits tab |
| `developer-checklist.css` | development | Developer checklist |
| `ecosystem-installer.css` | core | Ecosystem installer page |
| `license-manager.css` | feature | License management page |
| `notification-center.css` | core | Notification center |
| `settings-import-export.css` | feature | Settings import/export |
| `settings-repeater.css` | core | Repeater field styles |
| `tooltips.css` | core | Root-level tooltip styles (duplicate of components?) |
| `uninstall-feedback.css` | core | Uninstall feedback modal |
| `wpseed-setup.css` | core | Setup wizard |
| `wpseed-setup.scss` | **dead** | SCSS source — no build pipeline |

---

## assets/js/

| File | Classification | Notes |
|---|---|---|
| `js/admin/roadmap.js` | development | Roadmap accordion + localStorage |
| `js/admin/settings-repeater.js` | core | Repeater field JS |
| `js/admin/wpseed-enhanced-select.js` | core | Select2 wrapper |
| `js/admin/wpseed-enhanced-select.min.js` | core | Minified version |
| `js/admin/wpseed-faq.js` | feature | FAQ accordion |
| `js/admin/wpseed-faq.min.js` | feature | Minified version |
| `js/admin/wpseed-setup.js` | core | Setup wizard JS |
| `js/admin/wpseed-setup.min.js` | core | Minified version |
| `js/jquery-blockui/jquery.blockUI.js` | core | jQuery BlockUI library |
| `js/jquery-blockui/jquery.blockUI.min.js` | core | Minified version |
| `js/select2/select2.js` | core | Select2 library |
| `js/select2/select2.min.js` | core | Minified version |
| `js/accordion-table.js` | core | Accordion table JS |
| `js/development-tabs.js` | development | Development tab switching |
| `js/ecosystem-installer.js` | core | Ecosystem installer AJAX |
| `js/notification-center.js` | core | Notification center JS |
| `js/tooltips.js` | core | Tooltip initialisation |
| `js/uninstall-feedback.js` | core | Uninstall feedback modal |
| `js/verification-logger.js` | development | Verification logger UI |

---

## Summary

| Classification | CSS files | JS files | Action |
|---|---|---|---|
| **core** | ~35 | ~13 | ✅ Fixed --tp- refs. Review quality next. |
| **development** | ~18 | ~3 | ✅ Fixed --tp- refs where present. |
| **feature** | ~5 | ~2 | Keep. Document as optional. |
| **tradepress** | ~~55~~ 0 | 0 | ✅ **DELETED.** 61 files removed. |
| **dead** | ~~3~~ 0 | 0 | ✅ **DELETED.** 3 .scss files removed. |

## Completed Cleanup (Phase 7)

- **Task 7.1** ✅ — Asset inventory created (this document)
- **Task 7.2** ✅ — All `--tp-` variable refs replaced with `--wpseed-` (46 CSS files)
- **Task 7.2** ✅ — All `.tp-` class names replaced with `.wpseed-` (6 CSS, 1 JS, 7 PHP templates)
- **Task 7.3** ✅ — `variables.css` cleaned: removed self-referencing aliases, trading vars,
  duplicate swatches. Added `--wpseed-focus-ring` and `--wpseed-color-accent`.
- **Task 7.4** ✅ — 61 TradePress/dead files deleted (55 TradePress CSS, 3 SCSS, 3 layout/template)
- **144 → 83 CSS files** (42% reduction)
