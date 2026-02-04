<?php
/**
 * WPSeed Development Views
 *
 * @package WPSeed/Admin/Views
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WPSeed_Admin_Development_Page Class
 */
class WPSeed_Admin_Development_Page {
    /**
     * Output the development view
     */
    public static function output() {
        self::enqueue_assets();
        self::view_wrapper_start();
        self::tabs();
        self::active_tab_content();
        self::view_wrapper_end();
    }
    
    /**
     * Get the tabs array
     */
    public static function get_tabs() {
        return array(
            'assets' => __('Assets', 'wpseed'),
            'theme_info' => __('Theme', 'wpseed'),
            'debug_log' => __('Debug Log', 'wpseed'),
            'database' => __('Database', 'wpseed'),
            'phpinfo' => __('PHP Info', 'wpseed'),
            'ai_assistant' => __('AI Assistant', 'wpseed'),
            'dev_checklist' => __('Dev Checklist', 'wpseed'),
            'tasks' => __('Tasks', 'wpseed'),
            'layouts' => __('Layouts', 'wpseed'),
            'diagrams' => __('Diagrams', 'wpseed'),
        );
    }

    /**
     * Enqueue required assets
     */
    private static function enqueue_assets() {
        wp_enqueue_style(
            'wpseed-development', 
            plugins_url('assets/css/admin.css', WPSEED_PLUGIN_FILE), 
            array(), 
            WPSEED_VERSION
        );
    }
    
    /**
     * Development view wrapper start
     */
    private static function view_wrapper_start() {
        $current_tab = isset($_GET['tab']) ? sanitize_title($_GET['tab']) : 'theme_info';
        $tabs = self::get_tabs();
        $tab_title = isset($tabs[$current_tab]) ? $tabs[$current_tab] : '';
        
        ?>
        <div class="wrap wpseed-development-wrap">
            <h1>
                <?php esc_html_e('WPSeed Development', 'wpseed'); ?>
                <?php if (!empty($tab_title)) : ?>
                    <span class="dashicons dashicons-arrow-right-alt2" style="font-size: 0.8em; vertical-align: middle; margin: 0 5px;"></span>
                    <?php echo esc_html($tab_title); ?>
                <?php endif; ?>
            </h1>
        <?php
    }
    
    /**
     * Development view wrapper end
     */
    private static function view_wrapper_end() {
        ?>
        </div><!-- .wrap -->
        <?php
    }
    
    /**
     * Display the tabs
     */
    private static function tabs() {
        $current_tab = isset($_GET['tab']) ? sanitize_title($_GET['tab']) : 'theme_info';
        $tabs = self::get_tabs();
        ?>
        <h2 class="nav-tab-wrapper">
            <?php
            foreach ($tabs as $tab_id => $tab_title) {
                $active_class = ($current_tab === $tab_id) ? 'nav-tab-active' : '';
                printf(
                    '<a href="%s" class="nav-tab %s">%s</a>',
                    esc_url(add_query_arg('tab', $tab_id)),
                    esc_attr($active_class),
                    esc_html($tab_title)
                );
            }
            ?>
        </h2>
        <?php
    }
    
    /**
     * Display the active tab content
     */
    private static function active_tab_content() {
        $current_tab = isset($_GET['tab']) ? sanitize_title($_GET['tab']) : 'assets';
        
        switch ($current_tab) {
            case 'assets':
                if (!class_exists('WPSeed_Admin_Development_Assets')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/assets-tracker.php';
                }
                WPSeed_Admin_Development_Assets::output();
                break;
            case 'theme_info':
                if (!class_exists('WPSeed_Admin_Development_Theme_Info')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/theme-info.php';
                }
                WPSeed_Admin_Development_Theme_Info::output();
                break;
            case 'debug_log':
                if (!class_exists('WPSeed_Admin_Development_Debug_Log')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/debug-log.php';
                }
                WPSeed_Admin_Development_Debug_Log::output();
                break;
            case 'database':
                if (!class_exists('WPSeed_Admin_Development_Database')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/database.php';
                }
                WPSeed_Admin_Development_Database::output();
                break;
            case 'phpinfo':
                if (!class_exists('WPSeed_Admin_Development_PHPInfo')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/phpinfo.php';
                }
                WPSeed_Admin_Development_PHPInfo::output();
                break;
            case 'ai_assistant':
                if (!class_exists('WPSeed_Admin_Development_AI_Assistant')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/ai-assistant.php';
                }
                WPSeed_Admin_Development_AI_Assistant::output();
                break;
            case 'dev_checklist':
                if (!class_exists('WPSeed_Admin_Development_Checklist')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/dev-checklist.php';
                }
                WPSeed_Admin_Development_Checklist::output();
                break;
            case 'tasks':
                if (!class_exists('WPSeed_Admin_Development_Tasks')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/tasks.php';
                }
                WPSeed_Admin_Development_Tasks::output();
                break;
            case 'layouts':
                if (!class_exists('WPSeed_Admin_Development_Layouts')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/layouts.php';
                }
                WPSeed_Admin_Development_Layouts::output();
                break;
            case 'diagrams':
                if (!class_exists('WPSeed_Admin_Development_Diagrams')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/diagrams.php';
                }
                WPSeed_Admin_Development_Diagrams::output();
                break;
            default:
                if (!class_exists('WPSeed_Admin_Development_Theme_Info')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/theme-info.php';
                }
                WPSeed_Admin_Development_Theme_Info::output();
                break;
        }
    }
}
