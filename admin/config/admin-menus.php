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
        'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12,22C12,22 11,17 11,13C11,9 13,6 17,4C17,4 16,8 16,11C16,14 17,17 17,17M7,18C7,18 6,14 8,11C10,8 13,7 13,7C13,7 12,10 11,12C10,14 10,18 10,18" /></svg>'),
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
    
    // Notifications
    add_submenu_page(
        'wpseed',
        __('Notifications', 'wpseed'),
        __('Notifications', 'wpseed'),
        'manage_options',
        'wpseed-notifications',
        'wpseed_notifications_page'
    );
    
    // Scheduled Actions (Action Scheduler)
    if (function_exists('as_enqueue_async_action')) {
        add_submenu_page(
            'wpseed',
            __('Scheduled Actions', 'wpseed'),
            __('Scheduled Actions', 'wpseed'),
            'manage_options',
            'wpseed-scheduled-actions',
            'wpseed_scheduled_actions_page'
        );
    }
}
add_action('admin_menu', 'wpseed_register_admin_menus');

/**
 * Main page callback
 */
function wpseed_main_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('WPSeed', 'wpseed'); ?></h1>
        <p><?php esc_html_e('Welcome to WPSeed - Your WordPress Plugin Boilerplate', 'wpseed'); ?></p>
        <div class="card">
            <h2><?php esc_html_e('Getting Started', 'wpseed'); ?></h2>
            <p><?php esc_html_e('This is a boilerplate plugin with developer tools and examples.', 'wpseed'); ?></p>
            <ul>
                <li><?php esc_html_e('Visit the Development page to access debugging tools', 'wpseed'); ?></li>
                <li><?php esc_html_e('Check the code examples in the plugin directory', 'wpseed'); ?></li>
                <li><?php esc_html_e('Customize this plugin to build your own WordPress solution', 'wpseed'); ?></li>
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
 * Enqueue jQuery UI styles for gallery page
 */
function wpseed_jquery_ui_enqueue_assets($hook) {
    if ($hook !== 'wpseed_page_wpseed-jquery-ui') {
        return;
    }
    
    // Enqueue jQuery UI scripts
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jquery-ui-slider');
    wp_enqueue_script('jquery-ui-progressbar');
    wp_enqueue_script('jquery-ui-autocomplete');
    wp_enqueue_script('jquery-ui-accordion');
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('jquery-ui-spinner');
    
    // Enqueue WordPress jQuery UI styles
    wp_enqueue_style('wp-jquery-ui-dialog');
}
add_action('admin_enqueue_scripts', 'wpseed_jquery_ui_enqueue_assets');

/**
 * Notifications page callback
 */
function wpseed_notifications_page() {
    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/notification-center.php';
}

/**
 * Scheduled Actions page callback
 */
function wpseed_scheduled_actions_page() {
    if (!class_exists('ActionScheduler_AdminView')) {
        wp_die(esc_html__('Action Scheduler is not available.', 'wpseed'));
    }
    
    $admin_view = ActionScheduler_AdminView::instance();
    $admin_view->render_admin_ui();
}
