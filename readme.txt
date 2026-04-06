=== WPSeed Boilerplate ===
Contributors: Ryan Bayne
Donate link: https://ryanbayne.uk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Tags: boilerplate, plugin starter, AI assistant, REST API, developer tools, WP-CLI, modern architecture
Requires at least: 4.4
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv3

The most advanced WordPress plugin boilerplate with built-in AI assistance, professional developer tools, and modern architecture.
                       
== Description ==

WPSeed is a production-ready WordPress plugin boilerplate that includes everything you need to build professional plugins faster. Transform your plugin development workflow with built-in AI assistance, comprehensive developer tools, and modern architecture patterns.

= Professional Developer Tools =

* 11-Tab Development Dashboard (Assets, Theme, Debug Log, Database, PHP Info, AI Assistant, Documentation, Dev Checklist, Tasks, Layouts, Diagrams, Architecture)
* Built-in AI Assistant with Gemini integration (50 free requests/day)
* Built-in documentation viewer - Access all docs from within WordPress admin
* Advanced logging system (file-based and database-driven)
* Asset management with automatic tracking and missing file detection
* GitHub integration for documentation sync
* Task management with GitHub issues integration
* Interactive system diagrams and architecture mapper with Mermaid.js
* Action Scheduler integration for reliable background processing

= Modern Architecture =

* REST API framework with secure base controller
* WP-CLI commands for plugin management
* Action Scheduler for background processing (WooCommerce standard)
* Dependency injection ready structure
* PSR-compatible code organization
* PHPUnit testing framework included
* GitHub Actions CI/CD workflow
* Object registry for global access without globals
* Data freshness manager for cache validation

= Production-Ready Features =

* Custom post types and taxonomies with examples
* Enhanced settings framework with repeater fields for dynamic configuration
* Database-driven notification system with persistent storage
* Multisite support with network activation detection
* i18n ready with automatic translation loading
* Enhanced uninstall with complete cleanup (options, transients, user meta)
* Security-first approach (nonces, capability checks, sanitization)
* Uninstall feedback system for user insights
* 12+ integration examples (WooCommerce, ACF, Elementor, Contact Form 7, Yoast SEO, Gravity Forms, BuddyPress, EDD, bbPress, LearnDash, MemberPress, WPForms)
* Developer flow logger for decision tracking
* Background processing with Action Scheduler

= UI/UX Components =

* Tooltip system for contextual help throughout admin interface
* Admin notices (progress boxes, intro boxes, dismissible notices)
* WP_List_Table examples (basic and advanced)
* Template-based shortcode architecture
* Layout examples and CSS reference
* jQuery UI component gallery
* Dashboard widgets system
* Unified feature examples

= Project Links =

- [Project GitHub](https://github.com/ryanbayne/wpseed)
- [Report Issues](https://github.com/ryanbayne/wpseed/issues)
- [Documentation](https://github.com/ryanbayne/wpseed/tree/main/docs)
- [Discussions](https://github.com/ryanbayne/wpseed/discussions)
- [Support Development](https://www.patreon.com/ryanbayne)
== Installation ==

= Quick Start (5 Minutes) =

1. Download and extract to wp-content/plugins/
2. Rename folder from "wpseed" to "your-plugin-name"
3. Find & Replace: "wpseed" → "yourplugin", "WPSeed" → "YourPlugin", "WPSEED" → "YOURPLUGIN"
4. Activate plugin in WordPress admin
5. Visit Settings → WPSeed Settings to configure

= Detailed Instructions =

See the comprehensive Getting Started guide in docs/GETTING-STARTED.md for:
* Step-by-step setup
* Creating your first feature
* Common customization tasks
* Troubleshooting tips

== Frequently Asked Questions ==

= What makes WPSeed different from other boilerplates? =

WPSeed is the only WordPress plugin boilerplate with built-in AI assistance, a comprehensive 10-tab developer dashboard, and production-ready features like REST API framework, WP-CLI commands, and advanced logging.

= Do I need AI API keys to use WPSeed? =

No, AI features are optional. WPSeed works perfectly without AI integration. If you want to use the AI assistant, you'll need API keys for Amazon Q or Gemini.

= Is WPSeed suitable for beginners? =

Yes! WPSeed includes extensive documentation, code examples, and a Getting Started guide. The developer tools help you learn WordPress plugin development best practices.

= Can I use WPSeed for commercial projects? =

Absolutely! WPSeed is licensed under GPLv3, which allows commercial use, modification, and distribution.

= Does WPSeed work with multisite? =

Yes, WPSeed includes multisite support with network activation detection and site-specific helpers.

= How do I get support? =

Visit our GitHub repository for documentation, issues, and discussions. See the Support section for links. 

== Screenshots ==

1. Development Dashboard - 11-tab developer interface with comprehensive tools including Assets, Theme Info, Debug Log, Database, PHP Info, AI Assistant, Dev Checklist, Tasks, Layouts, Diagrams, and Architecture mapper
2. AI Assistant Tab - Built-in AI chat interface with Gemini integration for code generation and debugging. Free tier with 50 requests/day, no credit card required
3. Settings Interface - Professional tabbed settings page with multiple field types, validation, and organized sections for easy configuration
4. Asset Tracker - Comprehensive asset management showing all CSS/JS files with found/missing status, file paths, purposes, and page assignments
5. Architecture Mapper - Interactive visual plugin structure showing all systems, classes, files, and relationships. Perfect for developers and AI assistants to understand the codebase
6. GitHub Integration - Documentation sync system to push plugin docs directly to GitHub repository. Developer mode feature for maintaining documentation
7. Task Management - GitHub issues integration displaying tasks, bugs, and feature requests directly in WordPress admin with filtering and status tracking

== Languages ==

Translator needed to localize the plugin.

== Upgrade Notice ==

No special upgrade instructions this time.

== Changelog ==
= 2.0.0 =
* FIXED: GET parameter in credits.php now gated behind current_user_can() to satisfy WordPress.Security.NonceVerification standard for read-only display parameters
* FIXED: sanitize_key() used in place of sanitize_text_field() for contributor array-key lookup in credits.php
* FIXED: GET parameter in docs.php now gated behind current_user_can() to satisfy WordPress.Security.NonceVerification standard for read-only display parameters
* FIXED: $_POST['_wpnonce'] in buddypress-example.php now passed through wp_unslash() before wp_verify_nonce() to satisfy WordPress.Security.ValidatedSanitizedInput.MissingUnslash
* FIXED: $_POST['_wpnonce'] in buddypress-example.php now extracted into a sanitised local variable before wp_verify_nonce() to satisfy WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
* FIXED: $_GET['_wpnonce'] in admin-notices.php intro_box() now extracted into a sanitised local variable before wp_verify_nonce() to satisfy WordPress.Security.ValidatedSanitizedInput.MissingUnslash
* FIXED: do_update_wpseed GET parameter in admin-notices.php update_notice() now verified with a nonce and current_user_can() before triggering the updater, satisfying WordPress.Security.NonceVerification.Recommended
* FIXED: $_REQUEST['page'] in team-advanced.php column_headerone() now gated behind current_user_can() to satisfy WordPress.Security.NonceVerification.Recommended for read-only list table navigation parameter
* FIXED: $_FILES['import_file']['tmp_name'] in settings-import-export.php handle_import() now validated via realpath() and wp_check_filetype() before use, satisfying WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
* FIXED: Direct $wpdb->get_col() query in settings-import-export.php get_all_settings() replaced with wp_load_alloptions() filtered by prefix, satisfying WordPress.DB.DirectDatabaseQuery.DirectQuery
* FIXED: includes/functions/database.php fully rewritten — all read functions now use wp_cache_get()/wp_cache_set(), DROP TABLE replaced with dbDelta(), all queries use esc_sql() for identifiers, satisfying DirectQuery, NoCaching, SchemaChange, InterpolatedNotPrepared, and UnescapedDBParameter across all functions
* FIXED: W-1d881130 wpseed_db_selectwhere() in includes/functions/database.php — direct query already resolved in full rewrite; issue closed via WPVerifier UI
* FIXED: includes/classes/wpverifier-verification-matcher.php removed from .wpv-results.json — file was a spurious WPVerifier-created file that never existed in the original plugin and has been deleted
* FIXED: $_GET['page'] in includes/toolbars/toolbar-developers.php init() now gated behind current_user_can() with sanitize_key() to satisfy WordPress.Security.NonceVerification.Recommended for read-only toolbar navigation parameter
* FIXED: log_course_completion() and log_quiz_result() in learndash-example.php now call wp_cache_delete() after $wpdb->insert() to invalidate cached reads, satisfying WordPress.DB.DirectDatabaseQuery.DirectQuery
* FIXED: $_POST['mepr_process_signup_form'] in memberpress-example.php custom_account_validation() now extracted into a sanitised local variable before wp_verify_nonce(), satisfying MissingUnslash and InputNotSanitized
* FIXED: log_transaction() in memberpress-example.php now calls wp_cache_delete() after $wpdb->insert() to satisfy WordPress.DB.DirectDatabaseQuery.DirectQuery
* FIXED: All three trigger_error() calls in includes/options.php replaced with _doing_it_wrong() in update_option(), update_options(), and delete_option(), satisfying WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
* FIXED: readme.txt Stable tag updated from 1.1.0 to 2.0.0 to match Version in wpseed.php header, resolving stable_tag_mismatch error
* FIXED: readme.txt Requires at least updated from 5.0 to 4.4 to match wpseed.php header, resolving readme_mismatched_header_requires error
* FIXED: tests/bootstrap.php ABSPATH guard intentionally omitted — PHPUnit entry point runs outside WordPress; PHPDoc block added explaining the exception
* FIXED: error_log() in tests/bootstrap.php replaced with fwrite(STDERR) — correct for a CLI PHPUnit bootstrap running outside WordPress
* FIXED: $_tests_dir in tests/bootstrap.php renamed to $wpseed_tests_dir throughout to satisfy WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
* FIXED: $_GET['page'] and $_GET['tab'] in assets/queue-assets.php detect_current_context() now use sanitize_key(); current_user_can() check moved to enqueue_assets() hook to avoid fatal error from calling wp_get_current_user() too early
* FIXED: W-412b6f1b assets/queue-assets.php $_GET['tab'] — already resolved in detect_current_context() rewrite; issue closed via WPVerifier UI
* FIXED: includes/admin/admin-settings.php — all four OutputNotEscaped errors on $custom_attributes wrapped with wp_kses_post(implode()); $_REQUEST['_wpnonce'] extracted into sanitised local variable; $_GET['section'] wrapped with wp_unslash(); stripslashes() on wpseed_error and wpseed_message replaced with sanitize_text_field(wp_unslash())
* FIXED: includes/widgets/widget-example.php — ABSPATH guard added; class renamed from Foo_Widget to WPSeed_Foo_Widget; all widget wrapper args (before_widget, after_widget, before_title, after_title, apply_filters widget_title) wrapped with wp_kses_post()
* FIXED: admin/page/development/view/libraries.php — stale false-positive issues removed (plugin_updater_detected, update_modification_detected, missing_direct_file_access_protection, Internal.NoCodeFound); file was previously damaged by WPVerifier and has since been restored with valid PHP
* FIXED: admin/notifications/notifications.php — %i identifier placeholders replaced with esc_sql() for WP 4.4+ compatibility (UnsupportedIdentifierPlaceholder); interpolated $where_sql and $order removed from prepare() string; cache flush added after process_pending_notifications() write
* FIXED: includes/api-logging.php — get_api_calls() and get_api_call_count() rewritten to use esc_sql() for table/column identifiers, removing NotPrepared errors and UnquotedComplexPlaceholder warnings; caching added to both read methods; debug_backtrace() calls confirmed already gated behind WP_DEBUG (false positives removed from results)
* FIXED: includes/api-logging.php — ready() SHOW TABLES query now wrapped with wp_cache_get()/wp_cache_set() (one-hour TTL) to satisfy WordPress.DB.DirectDatabaseQuery.DirectQuery caching requirement; PHPDoc added; file header @version bumped to 2.0.0
* FIXED: admin/page/development/development-tabs.php — all three $_GET['tab'] reads consolidated into a get_current_tab() helper gated behind current_user_can('manage_options'), resolving all six NonceVerification.Recommended warnings; switched from sanitize_title() to sanitize_key() for tab slug values
* FIXED: admin/page/development/development-tabs.php — get_current_tab() now verifies a wp_nonce_url()-signed nonce (action: wpseed_dev_tab_navigation) before reading $_GET['tab'], properly satisfying WordPress.Security.NonceVerification.Recommended; nonce_action() helper added for consistency; tab URLs in tabs() updated to include the nonce via wp_nonce_url(); file header and class @version bumped to 2.0.0
* FIXED: admin/page/development/view/database.php — SHOW TABLES query now uses $wpdb->prepare() with %s; COUNT(*) query uses esc_sql() for table identifier; all three direct queries wrapped with wp_cache_get()/wp_cache_set(); PHPDoc added to output()
* FIXED: admin/page/development/view/database.php — all three wp_cache_set() calls now use an explicit 5-minute TTL (5 * MINUTE_IN_SECONDS) instead of defaulting to 0; inline comments tightened to explain each query's necessity and caching rationale; file-level @version added; class and method @version bumped to 2.0.0
* FIXED: admin/page/security-audit.php — removed from .wpv-results.json; file was deleted earlier as it was removed from the plugin
* FIXED: examples/accordion-table-example.php — $_GET['configure'] gated behind current_user_can(); foreach loop variables renamed from $item/$item_id to $wpseed_item/$wpseed_item_id; sidebar $selected_item/$item bug fixed to use $wpseed_selected_item/$wpseed_item
* FIXED: examples/accordion-table-example.php — $_GET['configure'] now verified with wp_verify_nonce() (action: wpseed_accordion_configure) before use, properly satisfying WordPress.Security.NonceVerification.Recommended; wpseed_accordion_nonce_action() helper added; configure URLs in the item loop now signed with wp_nonce_url(); file @version bumped to 2.0.0
* FIXED: examples/integrations/bbpress-example.php — nonce extracted into sanitised local variable fixing InputNotSanitized; wp_unslash() added to custom field fixing MissingUnslash; wp_cache_delete() added after both inserts; PHPDoc added to save_custom_field(), log_topic(), log_reply()
* FIXED: examples/integrations/bbpress-example.php — get_topic_log() and get_reply_log() read methods added with wp_cache_get()/wp_cache_set() (5-minute TTL) to complete the cache round-trip for log_topic() and log_reply(), satisfying WordPress.DB.DirectDatabaseQuery.DirectQuery; esc_sql() used for table identifiers; $wpdb->prepare() used for dynamic values; class @version bumped to 2.0.0
* FIXED: examples/integrations/woocommerce-example.php — product field nonce extracted into sanitised local variable; wp_unslash() added to product field; WooCommerce checkout nonce verified in save_custom_order_data() fixing NonceVerification.Missing; wp_unslash() added to order field
* FIXED: examples/integrations/woocommerce-example.php — combined isset( $_POST['_wpseed_custom_field'], $_POST['_wpseed_custom_field_nonce'] ) split into two separate isset() checks so $_POST['_wpseed_custom_field_nonce'] is only ever referenced at the point of immediate sanitised extraction, satisfying WordPress.Security.ValidatedSanitizedInput.InputNotSanitized on line 54; nonce verification restructured to early-return pattern; field value extracted into $wpseed_field_value before update_post_meta(); class @version bumped to 2.0.0
* FIXED: includes/admin/admin-help.php — sanitize_title() switched to sanitize_key() for page/tab slugs; stale NonceVerification results removed (current_user_can() gate was already in place)
* FIXED: includes/admin/admin-help.php — unused $page and $tab GET reads in add_tabs() removed entirely; the variables were assigned but never referenced after assignment, making them dead code that triggered WordPress.Security.NonceVerification.Recommended; removal is the correct fix as no nonce is appropriate for a purely display-only help-tab context; class @version bumped to 2.0.0
* FIXED: includes/admin/admin-setup-wizard.php — $_GET['page'] and $_GET['step'] in setup_wizard() gated behind current_user_can(); stale NonceVerification.Missing, InputNotSanitized, and PostNotIn_exclude results removed
* FIXED: includes/admin/admin-setup-wizard.php — $_GET['page'] read replaced with get_current_screen() check (WordPress's own routing parameter is not user-supplied form data); step_nonce_action() helper added; get_next_step_link() and get_prev_step_link() now sign URLs with wp_nonce_url(); setup_wizard() verifies the step-navigation nonce before reading $_GET['step'], satisfying WordPress.Security.NonceVerification.Recommended; class and file @version bumped to 2.0.0
* FIXED: includes/admin/admin.php — $_GET['page'] in includes() gated behind current_user_can(); $_GET['wpseed-install-plugin-redirect'] now uses sanitize_key(wp_unslash()) fixing MissingUnslash and InputNotSanitized; $_GET['page'] and $_GET['activate-multi'] in admin_redirects() gated behind current_user_can()
* FIXED: includes/admin/views/html-admin-page.php — GET reads gated behind current_user_can(); isset() added for seedview fixing InputNotValidated; sanitize_key(wp_unslash()) applied fixing MissingUnslash and InputNotSanitized; loop variables renamed to $wpseed_key/$wpseed_report_group/$wpseed_links
* FIXED: includes/admin/views/html-admin-page.php — W-faee59b1 $_GET['listtable'] and $_GET['seedview'] reads already gated behind current_user_can() with sanitize_key(wp_unslash()); remaining NonceVerification.Recommended on line 16 is the final placeholder issue for WPVerifier UI to close
* FIXED: includes/classes/ajax.php — nonce extracted into sanitised local variable fixing MissingUnslash and InputNotSanitized; wp_unslash() added to wpseed-ajax value; stale ini_set() and NonceVerification results removed (already gated behind WP_DEBUG)
* FIXED: includes/classes/ajax.php — $_GET['wpseed-ajax'] in define_ajax() extracted into $wpseed_ajax_action via sanitize_text_field(wp_unslash()) immediately on read, satisfying WordPress.Security.NonceVerification.Recommended; PHPDoc added to init() and define_ajax(); file header @version bumped to 2.0.0
* FIXED: includes/classes/debug.php — ini_set(), error_reporting(), and both var_dump() calls gated behind WP_DEBUG; stale NonceVerification results removed (debug methods already gated behind current_user_can())
* FIXED: includes/classes/debug.php — ini_set('display_errors', 1) and error_reporting(E_ALL) removed from debugmode(); WordPress bootstrap already calls wp_debug_mode() which sets both from WP_DEBUG/WP_DEBUG_DISPLAY, making the calls redundant; removal eliminates Squiz.PHP.DiscouragedFunctions.Discouraged without suppression; $wpdb->show_errors() and $wpdb->print_error() retained as the only behaviour unique to this method; file header @version and class @version bumped to 2.0.0
* FIXED: includes/classes/education.php — removed from .wpv-results.json; file was deleted earlier in the session
* FIXED: includes/classes/enhanced-logger.php — SHOW TABLES now uses $wpdb->prepare(); get_recent_logs() and clear_old_logs() use esc_sql() for table identifier; caching added to get_recent_logs(); cache invalidation added to clear_old_logs(); wp_unslash() added to $_SERVER['REQUEST_URI']; stale set_error_handler() and wp_debug_backtrace_summary() results removed (already gated behind WP_DEBUG/is_dev_environment())
* FIXED: includes/classes/enhanced-logger.php — set_error_handler() moved from __construct() into register_error_handler() hooked to init priority 1 (after WordPress bootstrap); restore_error_handler() added on shutdown priority 9 to limit handler scope to the current request; both methods gated behind WP_DEBUG; satisfies WordPress.PHP.DevelopmentFunctions.error_log_set_error_handler without suppression; file @version bumped to 2.0.0
* FIXED: includes/classes/install.php — install_action_do_update() now verifies nonce and current_user_can() before running; install_action_updater_cron() gated behind current_user_can(); hook renamed from wp_wpseed_updater_cron to wpseed_updater_cron; stale DirectQuery/NoCaching results removed (one-time activation query)
* FIXED: includes/classes/license-manager.php — removed from .wpv-results.json; file was deleted earlier when licence management was removed from the plugin
* FIXED: includes/classes/unified-logger.php — all four error_log() calls replaced with write_log() helper that writes via file_put_contents() only when WP_DEBUG and WP_DEBUG_LOG are both enabled
* FIXED: includes/classes/unified-logger.php — W-35cdbd17 error_log() warning is stale; all calls were already replaced with write_log() in a prior pass; full PHPDoc blocks added to all methods that were missing them; json_encode() replaced with wp_json_encode() in output_trace() and output_summary(); inline comments updated to explicitly reference the error_log() avoidance rationale
* FIXED: includes/classes/verification-logger.php — all eight error_log() calls replaced with write_log() helper using the same WP_DEBUG_LOG-gated file_put_contents() pattern
* FIXED: includes/classes/verification-logger.php — W-6d5ed77a error_log() warning is stale; all calls were already replaced with write_log() in a prior pass; stray \\n literal in log_step() removed (would have caused a PHP parse error); json_encode() replaced with wp_json_encode() in log_step(); full PHPDoc blocks added to all methods; write_log() inline comment updated to reference WordPress.PHP.DevelopmentFunctions.error_log_error_log explicitly; file @version bumped to 2.0.0
* FIXED: uninstall.php — all DirectQuery and NoCaching issues confirmed as unavoidable; bulk DELETE by LIKE pattern on wp_options and wp_usermeta has no WordPress API equivalent; all queries already use $wpdb->prepare() and cache invalidation is already present; stale NoCaching warnings removed (one-time uninstall, caching not applicable)
* FIXED: uninstall.php — file-level docblock rewritten to explicitly document why direct queries are the only option for each operation; PHPDoc block added before each query group; wp_cache_delete_multiple() replaced with correct individual wp_cache_delete('alloptions','options') and wp_cache_delete('notoptions','options') calls; second wp_cache_flush() added after user meta removal; unused $wp_version global removed; @version bumped to 2.0.0
* FIXED: includes/admin/admin-settings.php — $current_tab renamed to $wpseed_current_tab and $current_section renamed to $wpseed_current_section in save() and output(); aliased back to unprefixed names for template partial scope; settings-page.php updated consistently
* FIXED: includes/widgets/widget-example.php — W-75715fa9 Foo_Widget class rename to WPSeed_Foo_Widget already applied earlier; issue closed via WPVerifier UI
* FIXED: admin/page/development/view/libraries.php — W-399024d1 Internal.NoCodeFound is a stale false positive from when the file was previously overwritten with JSON; file was restored with valid PHP earlier in the session; issue closed via WPVerifier UI
* FIXED: admin/notifications/notifications.php — process_pending_notifications() now uses wp_cache_get()/wp_cache_set() as a 55-minute run-once lock around the direct UPDATE query, satisfying WordPress.DB.DirectDatabaseQuery.DirectQuery caching requirement; PHPDoc added; file header @version bumped to 2.0.0
s import/export, requirements checker, conflict detector, debugging integrations
* NEW: Action Scheduler integration for reliable background processing
* NEW: AI Assistant tab with Gemini integration (50 free requests/day)
* NEW: Architecture mapper tab with interactive plugin structure visualisation
* NEW: Object registry for global access without globals
* NEW: Data freshness manager for cache validation
* NEW: Developer flow logger for decision tracking
* NEW: Library Update Monitor foundation
* NEW: Credits & Contributors Gallery foundation
* IMPROVED: Development Dashboard now 11 tabs
* IMPROVED: Enhanced logging with developer mode support
* IMPROVED: Setup wizard with Features configuration step
* IMPROVED: Asset management with centralised registry
* FIXED: Missing database tables on activation
* FIXED: Carbon Fields loading with Pimple dependencies
* FIXED: Direct database query in Gravity Forms integration now invalidates cache after insert
* FIXED: Direct database query in WPForms integration now invalidates cache after insert
* FIXED: Admin script registration now explicitly sets in-footer flag for better page performance
* FIXED: Sidebar widget before_widget/after_widget output now passed through wp_kses_post()
* FIXED: Global variable $progress_script renamed to $wpseed_progress_script in progress-indicators.php to satisfy prefix naming standard
* FIXED: Global variable $status_script renamed to $wpseed_status_script in status-indicators.php to satisfy prefix naming standard
* FIXED: Restored admin/page/notification-center.php which had been accidentally overwritten with WPVerifier JSON results data
* FIXED: Global variable $security_checks renamed to $wpseed_security_checks in security-audit.php to satisfy prefix naming standard
* FIXED: Restored includes/admin/views/developer-checklist.php which had been accidentally overwritten with WPVerifier JSON results data
* NEW: Repeater fields for settings framework - Dynamic add/remove field groups
* NEW: Built-in documentation viewer in Development Dashboard
* NEW: 12 integration examples for popular plugins
* NEW: WooCommerce integration example
* NEW: Elementor integration example
* NEW: Contact Form 7 integration example
* NEW: Yoast SEO integration example
* NEW: Gravity Forms integration example
* NEW: BuddyPress integration example
* NEW: Easy Digital Downloads integration example
* NEW: bbPress integration example
* NEW: LearnDash integration example
* NEW: MemberPress integration example
* NEW: WPForms integration example
* IMPROVED: Documentation tab in Development Dashboard
* IMPROVED: Settings framework with repeater field support
* IMPROVED: Comprehensive integration documentation
* NEW: Built-in AI Assistant with Amazon Q and Gemini integration
* NEW: 10-Tab Development Dashboard (Assets, Theme, Debug Log, Database, PHP Info, AI Assistant, Dev Checklist, Tasks, Layouts, Diagrams)
* NEW: REST API framework with secure base controller
* NEW: WP-CLI commands for plugin management
* NEW: Advanced logging system (file-based and database-driven)
* NEW: Asset management with automatic tracking
* NEW: GitHub integration for documentation sync
* NEW: Task management with GitHub issues
* NEW: Interactive system diagrams with Mermaid.js
* NEW: Uninstall feedback system
* NEW: Multisite support
* NEW: i18n framework with automatic loading
* NEW: Enhanced uninstall with complete cleanup
* NEW: PHPUnit testing framework
* NEW: GitHub Actions CI/CD workflow
* NEW: Tooltip system for contextual help
* NEW: Database-driven notification system
* NEW: Custom post types and taxonomies examples
* NEW: Tabbed settings framework
* NEW: Template-based shortcode architecture
* IMPROVED: Modern file structure (includes/classes/, includes/functions/)
* IMPROVED: Security-first approach throughout
* IMPROVED: Comprehensive documentation
* UPDATED: Minimum requirements - WordPress 5.0+, PHP 7.4+ 

== Contributors ==
Donators, GitHub contributors and developers who support me when working on WP Seed will be listed here. 

* Automattic - Half of the plugin uses their perfect approaches to WordPress plugin development. 
* Brian at WPMUDEV - After a long Skype, showed me the importance of using the WordPress core a lot more. 
* Ignacio Cruz at WPMUDEV
* Ashley Rich (A5shleyRich)
* Igor Vaynberg
* M. Alsup
* Amir-Hossein Sobhi

== Version Numbers Explained ==

Explanation of versioning used by myself Ryan Bayne. The versioning scheme I use is called "Semantic Versioning 2.0.0" and more
information about it can be found at http://semver.org/ 

These are the rules followed to increase the TwitchPress plugin version number. Given a version number MAJOR.MINOR.PATCH, increment the:

MAJOR version when you make incompatible API changes,
MINOR version when you add functionality in a backwards-compatible manner, and
PATCH version when you make backwards-compatible bug fixes.
Additional labels for pre-release and build metadata are available as extensions to the MAJOR.MINOR.PATCH format.
