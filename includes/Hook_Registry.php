<?php
/**
 * Hook Registry — reference list of all actions and filters registered by WPSeed.
 *
 * ROLE: hook-registration
 *
 * This file is the single source of truth for understanding the full event
 * surface of the plugin. AI assistants and developers read this file to know
 * every hook without scanning every class.
 *
 * NOTE: Hook registrations currently live inside their respective class
 * constructors and init() methods. This file is a REFERENCE — not the
 * actual registration point. Moving registrations here is a future task
 * that requires refactoring every class.
 *
 * @package  WPSeed
 * @category Core
 * @since    3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * ==========================================================================
 * LIFECYCLE HOOKS (loader.php → init_hooks())
 * ==========================================================================
 *
 * register_activation_hook   → \WPSeed\Core\Install::install()
 * register_deactivation_hook → \WPSeed\Core\Install::deactivate()
 * register_activation_hook   → \WPSeed\Core\Enhanced_Logger::create_table()
 *
 * ==========================================================================
 * INIT HOOKS (priority 0-5)
 * ==========================================================================
 *
 * init (0)  → \WPSeed\Core\AJAX_Handler::define_ajax()
 *             Detects WPSeed AJAX requests, sets DOING_AJAX constant.
 *
 * init (0)  → WordPressPluginSeed::init()
 *             Fires before_wpseed_init and wpseed_init actions.
 *
 * init (5)  → \WPSeed\Core\Install::check_version()
 *             Runs install routine if stored version differs from package version.
 *
 * init      → \WPSeed\Core\Task_Scheduler::init()
 *             Fires wpseed_task_scheduler_init action.
 *
 * ==========================================================================
 * PLUGINS_LOADED
 * ==========================================================================
 *
 * plugins_loaded (5) → \WPSeed\Ecosystem\Registry::detect_ecosystem()
 *                       Fires wpseed_ecosystem_register, stores ecosystem state.
 *
 * ==========================================================================
 * ADMIN HOOKS
 * ==========================================================================
 *
 * admin_init       → \WPSeed\Core\Install::install_actions()
 *                     Handles manual update and forced update actions.
 *
 * admin_init (1)   → WPSeed_Admin::buffer()
 *                     Starts output buffering for admin redirects.
 *
 * admin_init       → WPSeed_Admin::admin_redirects()
 *                     Handles setup wizard redirect after activation.
 *
 * admin_menu       → wpseed_register_admin_menus()  [admin/config/admin-menus.php]
 *                     Registers main WPSeed menu and all submenus.
 *
 * admin_menu (999) → \WPSeed\Ecosystem\Menu_Manager::register_menus()
 *                     Registers shared ecosystem menus (Tools/Settings) when
 *                     2+ plugins are active.
 *
 * admin_menu       → \WPSeed\Ecosystem\Installer::add_installer_page()
 *                     Registers the ecosystem plugin installer submenu.
 *
 * admin_bar_menu (999) → \WPSeed\Admin\Notification_Bell::add_notification_bell()
 *                         Adds notification bell to admin toolbar.
 *
 * admin_enqueue_scripts → \WPSeed\Admin\Notification_Bell::enqueue_assets()
 *                          Inline CSS for notification bell.
 *
 * admin_enqueue_scripts → \WPSeed\Admin\Uninstall_Feedback::enqueue_assets()
 *                          CSS/JS for deactivation feedback modal (plugins.php only).
 *
 * admin_footer     → \WPSeed\Admin\Uninstall_Feedback::render_modal()
 *                     Renders feedback modal HTML (plugins.php only).
 *
 * admin_footer     → \WPSeed\Core\Logger (anonymous)
 *                     Outputs WPSeedLogger JS helper (dev mode only).
 *
 * wp_dashboard_setup → \WPSeed\Admin\Dashboard_Widgets::add_widgets()
 *                       Registers WPSeed dashboard widgets.
 *
 * ==========================================================================
 * AJAX HOOKS
 * ==========================================================================
 *
 * wp_ajax_wpseed_install_plugin    → \WPSeed\Ecosystem\Installer::ajax_install_plugin()
 * wp_ajax_wpseed_uninstall_feedback → \WPSeed\Admin\Uninstall_Feedback::handle_feedback()
 *
 * template_redirect (0) → \WPSeed\Core\AJAX_Handler::do_wpseed_ajax()
 *                          Custom AJAX endpoint handler (?wpseed-ajax=action).
 *
 * ==========================================================================
 * LOGGING HOOKS (dev mode only)
 * ==========================================================================
 *
 * query            → \WPSeed\Core\Enhanced_Logger::log_query()       [filter]
 * all              → \WPSeed\Core\Enhanced_Logger::log_hook()        [action]
 * pre_http_request → \WPSeed\Core\Enhanced_Logger::log_http_request() [filter]
 * init (1)         → \WPSeed\Core\Enhanced_Logger::register_error_handler()
 * shutdown (9)     → \WPSeed\Core\Enhanced_Logger::restore_error_handler()
 * shutdown         → \WPSeed\Core\Enhanced_Logger::save_logs()
 *
 * ==========================================================================
 * PLUGIN FILTERS
 * ==========================================================================
 *
 * plugin_action_links_{basename} → \WPSeed\Core\Install::plugin_action_links()
 *                                   Adds "Settings" link on Plugins screen.
 *
 * plugin_row_meta                → \WPSeed\Core\Install::plugin_row_meta()
 *                                   Adds Docs/Support/Donate links on Plugins screen.
 *
 * admin_footer_text              → WPSeed_Admin::admin_footer_text()
 *                                   Custom footer text on WPSeed admin pages.
 *
 * in_plugin_update_message       → \WPSeed\Core\Install::in_plugin_update_message()
 *                                   Shows upgrade notice from WordPress.org readme.
 *
 * ==========================================================================
 * CUSTOM ACTIONS (fired by WPSeed, consumed by other plugins)
 * ==========================================================================
 *
 * wpseed_loaded                → Fired after main class constructor completes.
 * before_wpseed_init           → Fired before wpseed_init on WordPress init.
 * wpseed_init                  → Fired during WordPress init.
 * wpseed_installed             → Fired after install routine completes.
 * wpseed_ecosystem_register    → Fired on plugins_loaded — plugins register here.
 * wpseed_ecosystem_plugin_registered → Fired when a plugin registers with ecosystem.
 * wpseed_task_scheduler_init   → Fired when Task_Scheduler initialises.
 * wpseed_updater_cron          → Fired by forced update action.
 *
 * ==========================================================================
 * CUSTOM FILTERS (fired by WPSeed, consumed by other plugins)
 * ==========================================================================
 *
 * wpseed_ajax_get_endpoint          → Filters the custom AJAX endpoint URL.
 * wpseed_ecosystem_available_plugins → Filters the list of installable plugins.
 * wpseed_enable_setup_wizard        → Controls whether setup wizard runs on install.
 * wpseed_docs_url                   → Filters the documentation URL.
 * wpseed_support_url                → Filters the support URL.
 * wpseed_donate_url                 → Filters the donation URL.
 *
 * ==========================================================================
 * CONNECTOR SYSTEM FILTERS (since 3.1.0)
 * ==========================================================================
 *
 * wpseed_api_providers       → Filters the list of registered API connector providers.
 *                               Add, remove, or modify providers at runtime.
 *                               Fired by WPSeed_API_Directory::get_all_providers().
 *
 * wpseed_connector_credentials → Filters credentials before creating a connector.
 *                                 Allows injecting credentials from environment
 *                                 variables, secrets managers, or other sources.
 *                                 Fired by WPSeed_API_Factory::create_from_settings().
 *                                 Parameters: $args, $provider_id, $account_id.
 *
 * wpseed_connector_request_args → Filters wp_remote_request() arguments before
 *                                  an API call is made. Allows modifying headers,
 *                                  timeout, or body for specific providers.
 *                                  Fired by WPSeed\API\Base_API::make_request().
 *                                  Parameters: $args, $url, $provider_id, $endpoint.
 *
 * wpseed_user_can               → Filters the capability check result. Return a
 *                                  non-null bool to override the default WordPress
 *                                  capability check. Used by EvolveWP Core to
 *                                  implement cross-plugin permission logic.
 *                                  Fired by WPSeed\Core\Capability_Manager::user_can().
 *                                  Parameters: $result (null), $capability, $user_id.
 *
 * ==========================================================================
 * CAPABILITY SYSTEM ACTIONS (since 3.1.0)
 * ==========================================================================
 *
 * wpseed_capabilities_installed   → Fired after all capabilities have been added
 *                                    to WordPress roles during plugin activation.
 *                                    Parameter: $capabilities (all registered caps).
 *
 * wpseed_capabilities_uninstalled → Fired after all capabilities have been removed
 *                                    from WordPress roles during plugin uninstall.
 *                                    Parameter: $capabilities (all registered caps).
 */
