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
            'assets' => array('title' => __('Assets', 'wpseed'), 'code' => 'AST01'),
            'performance' => array('title' => __('Performance', 'wpseed'), 'code' => 'PRF01'),
            'theme_info' => array('title' => __('Theme', 'wpseed'), 'code' => 'THM01'),
            'debug_log' => array('title' => __('Debug Log', 'wpseed'), 'code' => 'DBG01'),
            'database' => array('title' => __('Database', 'wpseed'), 'code' => 'DBS01'),
            'phpinfo' => array('title' => __('PHP Info', 'wpseed'), 'code' => 'PHP01'),
            'tasks' => array('title' => __('Tasks', 'wpseed'), 'code' => 'TSK01'),
            'libraries' => array('title' => __('Libraries', 'wpseed'), 'code' => 'LIB01'),
            'credits' => array('title' => __('Credits', 'wpseed'), 'code' => 'CRD01'),
            'docs' => array('title' => __('Documentation', 'wpseed'), 'code' => 'DOC01'),
            'dev_checklist' => array('title' => __('Dev Checklist', 'wpseed'), 'code' => 'CHK01'),
            'layouts' => array('title' => __('Layouts', 'wpseed'), 'code' => 'LAY01'),
            'diagrams' => array('title' => __('Diagrams', 'wpseed'), 'code' => 'DGM01'),
            'architecture' => array('title' => __('Architecture', 'wpseed'), 'code' => 'ARC01'),
        );
    }

    /**
     * Enqueue required assets
     */
    private static function enqueue_assets() {
        wp_enqueue_style('wpseed-admin-styles', WPSEED_PLUGIN_URL . 'assets/css/admin-styles.css', array(), WPSEED_VERSION);
    }
    
    /**
     * Development view wrapper start
     */
    private static function view_wrapper_start() {
        $current_tab = isset($_GET['tab']) ? sanitize_title(wp_unslash($_GET['tab'])) : 'assets';
        $tabs = self::get_tabs();
        $tab_data = isset($tabs[$current_tab]) ? $tabs[$current_tab] : array();
        $tab_title = isset($tab_data['title']) ? $tab_data['title'] : '';
        
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
        if (!function_exists('wpverifier_header')) {
            require_once WP_PLUGIN_DIR . '/WPVerifier/includes/helper-functions.php';
        }
        
        $current_tab = isset($_GET['tab']) ? sanitize_title(wp_unslash($_GET['tab'])) : 'theme_info';
        $tabs = self::get_tabs();
        ?>
        <h2 class="nav-tab-wrapper">
            <?php
            foreach ($tabs as $tab_id => $tab_data) {
                $active_class = ($current_tab === $tab_id) ? 'nav-tab-active' : '';
                $title = isset($tab_data['title']) ? $tab_data['title'] : $tab_data;
                $code = isset($tab_data['code']) ? $tab_data['code'] : '';
                ?>
                <a href="<?php echo esc_url(add_query_arg('tab', $tab_id)); ?>" class="nav-tab <?php echo esc_attr($active_class); ?>">
                    <?php wpverifier_header($title, $code); ?>
                </a>
                <?php
            }
            ?>
        </h2>
        <?php
    }
    
    /**
     * Display the active tab content
     */
    private static function active_tab_content() {
        $current_tab = isset($_GET['tab']) ? sanitize_title(wp_unslash($_GET['tab'])) : 'assets';
        
        switch ($current_tab) {
            case 'assets':
                require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/assets-tracker.php';
                break;
            case 'performance':
                if (!class_exists('WPSeed_Admin_Development_Performance')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/performance.php';
                }
                WPSeed_Admin_Development_Performance::output();
                break;
            case 'theme_info':
                if (!class_exists('WPSeed_Admin_Development_UI_Library')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/theme-info.php';
                }
                WPSeed_Admin_Development_UI_Library::output();
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
            case 'tasks':
                if (!class_exists('WPSeed_Admin_Development_Tasks_Monitor')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/tasks-monitor.php';
                }
                WPSeed_Admin_Development_Tasks_Monitor::output();
                break;
            case 'libraries':
                require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/libraries.php';
                break;
            case 'credits':
                if (!class_exists('WPSeed_Admin_Development_Credits')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/credits.php';
                }
                WPSeed_Admin_Development_Credits::output();
                break;
            case 'docs':
                if (!class_exists('WPSeed_Admin_Development_Docs')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/docs.php';
                }
                WPSeed_Admin_Development_Docs::output();
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
            case 'architecture':
                require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/architecture.php';
                break;
            default:
                if (!class_exists('WPSeed_Admin_Development_UI_Library')) {
                    require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/view/theme-info.php';
                }
                WPSeed_Admin_Development_UI_Library::output();
                break;
        }
    }
}
