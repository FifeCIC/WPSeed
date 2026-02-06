<?php
/**
 * WPSeed Admin Menu Configuration
 *
 * @package WPSeed/Admin
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register WPSeed admin menus
 */
function wpseed_register_admin_menus() {
    // Main menu
    add_menu_page(
        __('WPSeed', 'wpseed'),
        __('WPSeed', 'wpseed'),
        'manage_options',
        'wpseed',
        'wpseed_main_page',
        'dashicons-admin-generic',
        30
    );

    // Development submenu
    add_submenu_page(
        'wpseed',
        __('Development', 'wpseed'),
        __('Development', 'wpseed'),
        'manage_options',
        'wpseed_development',
        'wpseed_development_page'
    );
    
    // jQuery UI Gallery
    add_submenu_page(
        'wpseed',
        __('jQuery UI Gallery', 'wpseed'),
        __('jQuery UI Gallery', 'wpseed'),
        'manage_options',
        'wpseed-jquery-ui',
        'wpseed_jquery_ui_page'
    );
    
    // Component Library
    add_submenu_page(
        'wpseed',
        __('Component Library', 'wpseed'),
        __('Component Library', 'wpseed'),
        'manage_options',
        'wpseed-components',
        'wpseed_components_page'
    );
}
add_action('admin_menu', 'wpseed_register_admin_menus');

/**
 * Main page callback
 */
function wpseed_main_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('WPSeed', 'wpseed'); ?></h1>
        <p><?php _e('Welcome to WPSeed - Your WordPress Plugin Boilerplate', 'wpseed'); ?></p>
        <div class="card">
            <h2><?php _e('Getting Started', 'wpseed'); ?></h2>
            <p><?php _e('This is a boilerplate plugin with developer tools and examples.', 'wpseed'); ?></p>
            <ul>
                <li><?php _e('Visit the Development page to access debugging tools', 'wpseed'); ?></li>
                <li><?php _e('Check the code examples in the plugin directory', 'wpseed'); ?></li>
                <li><?php _e('Customize this plugin to build your own WordPress solution', 'wpseed'); ?></li>
            </ul>
        </div>
    </div>
    <?php
}

/**
 * Development page callback
 */
function wpseed_development_page() {
    if (!class_exists('WPSeed_Admin_Development_Page')) {
        require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/development-tabs.php';
    }
    WPSeed_Admin_Development_Page::output();
}

/**
 * jQuery UI Gallery page callback
 */
function wpseed_jquery_ui_page() {
    require_once WPSEED_PLUGIN_DIR_PATH . 'includes/admin/settings/settings-jquery-ui.php';
    wpseed_render_jquery_ui_gallery();
}

/**
 * Component Library page callback
 */
function wpseed_components_page() {
    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/component-library/component-library.php';
    wpseed_render_component_library();
}
