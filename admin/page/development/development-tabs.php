<?php
/**
 * WPSeed Development Views
 *
 * @package WPSeed/Admin/Views
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WPSeed_Admin_Development_Page Class
 *
 * @since   1.0.0
 * @version 2.0.0
 */
class WPSeed_Admin_Development_Page {

    /**
     * Return the nonce action string used to sign development-page tab URLs.
     *
     * Centralised so the same action is used when creating and verifying the
     * nonce, avoiding any mismatch between tabs() and get_current_tab().
     *
     * @since  2.0.0
     * @return string Nonce action slug.
     */
    private static function nonce_action() {
        return 'wpseed_dev_tab_navigation';
    }

    /**
     * Retrieve the current tab from the URL.
     *
     * Verifies the tab-navigation nonce before reading $_GET['tab'] so that
     * the NonceVerification.Recommended standard is satisfied. Falls back to
     * $default when the nonce is absent or invalid (e.g. direct URL access
     * without the nonce query arg), which simply renders the default tab.
     *
     * @since   1.0.0
     * @version 2.0.0
     *
     * @param string $default Default tab slug if none is provided or nonce fails.
     * @return string Sanitised tab slug.
     */
    private static function get_current_tab( $default = 'assets' ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return $default;
        }

        // Verify the tab-navigation nonce before trusting the GET parameter.
        // wp_verify_nonce() returns false/0 on failure, so the default tab is
        // shown safely when the nonce is missing or stale.
        $raw_nonce = isset( $_GET['_wpnonce'] ) ? sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ) : '';
        if ( ! wp_verify_nonce( $raw_nonce, self::nonce_action() ) ) {
            return $default;
        }

        if ( isset( $_GET['tab'] ) ) {
            // sanitize_key() is correct for a tab slug — strips anything that
            // is not a lowercase alphanumeric character or hyphen/underscore.
            return sanitize_key( wp_unslash( $_GET['tab'] ) );
        }

        return $default;
    }
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
            'roadmap' => array('title' => __('Roadmap', 'wpseed'), 'code' => 'RMP01'),
        );
    }

    /**
     * Enqueue required assets
     */
    private static function enqueue_assets() {
        wp_enqueue_style('wpseed-admin-styles', WPSEED_PLUGIN_URL . 'assets/css/admin-styles.css', array(), WPSEED_VERSION);
        wp_enqueue_style('wpseed-roadmap', WPSEED_PLUGIN_URL . 'assets/css/components/roadmap.css', array(), WPSEED_VERSION);
        wp_enqueue_style('wpseed-architecture', WPSEED_PLUGIN_URL . 'assets/css/components/architecture.css', array(), WPSEED_VERSION);
        wp_enqueue_script('wpseed-roadmap', WPSEED_PLUGIN_URL . 'assets/js/admin/roadmap.js', array('jquery'), WPSEED_VERSION, true);
    }
    
    /**
     * Development view wrapper start
     */
    private static function view_wrapper_start() {
        $current_tab = self::get_current_tab( 'assets' );
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
     * Display the tab navigation bar.
     *
     * Each tab URL includes a nonce so that get_current_tab() can verify it
     * before reading $_GET['tab'], satisfying NonceVerification.Recommended.
     * The nonce is added via wp_nonce_url() using the shared nonce action.
     *
     * @since   1.0.0
     * @version 2.0.0
     * @return void
     */
    private static function tabs() {
        if ( ! function_exists( 'wpverifier_header' ) ) {
            require_once WP_PLUGIN_DIR . '/WPVerifier/includes/helper-functions.php';
        }

        $current_tab = self::get_current_tab( 'theme_info' );
        $tabs        = self::get_tabs();
        ?>
        <h2 class="nav-tab-wrapper">
            <?php
            foreach ( $tabs as $tab_id => $tab_data ) {
                $active_class = ( $current_tab === $tab_id ) ? 'nav-tab-active' : '';
                $title        = isset( $tab_data['title'] ) ? $tab_data['title'] : $tab_data;
                $code         = isset( $tab_data['code'] ) ? $tab_data['code'] : '';

                // Include a nonce in every tab URL so get_current_tab() can
                // verify it before reading the tab GET parameter.
                $tab_url = wp_nonce_url(
                    add_query_arg( 'tab', $tab_id ),
                    self::nonce_action()
                );
                ?>
                <a href="<?php echo esc_url( $tab_url ); ?>" class="nav-tab <?php echo esc_attr( $active_class ); ?>">
                    <?php wpverifier_header( $title, $code ); ?>
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
        $current_tab = self::get_current_tab( 'assets' );
        $tab_dir = WPSEED_PLUGIN_DIR_PATH . 'templates/tabs/development/';
        
        switch ($current_tab) {
            case 'assets':
                require_once $tab_dir . 'tab-assets.php';
                break;
            case 'performance':
                if (!class_exists('WPSeed_Admin_Development_Performance')) {
                    require_once $tab_dir . 'tab-performance.php';
                }
                WPSeed_Admin_Development_Performance::output();
                break;
            case 'theme_info':
                if (!class_exists('WPSeed_Admin_Development_UI_Library')) {
                    require_once $tab_dir . 'tab-theme.php';
                }
                WPSeed_Admin_Development_UI_Library::output();
                break;
            case 'debug_log':
                if (!class_exists('WPSeed_Admin_Development_Debug_Log')) {
                    require_once $tab_dir . 'tab-debug-log.php';
                }
                WPSeed_Admin_Development_Debug_Log::output();
                break;
            case 'database':
                if (!class_exists('WPSeed_Admin_Development_Database')) {
                    require_once $tab_dir . 'tab-database.php';
                }
                WPSeed_Admin_Development_Database::output();
                break;
            case 'phpinfo':
                if (!class_exists('WPSeed_Admin_Development_PHPInfo')) {
                    require_once $tab_dir . 'tab-phpinfo.php';
                }
                WPSeed_Admin_Development_PHPInfo::output();
                break;
            case 'tasks':
                if (!class_exists('WPSeed_Admin_Development_Tasks_Monitor')) {
                    require_once $tab_dir . 'tab-tasks-monitor.php';
                }
                WPSeed_Admin_Development_Tasks_Monitor::output();
                break;
            case 'libraries':
                require_once $tab_dir . 'tab-libraries.php';
                break;
            case 'credits':
                if (!class_exists('WPSeed_Admin_Development_Credits')) {
                    require_once $tab_dir . 'tab-credits.php';
                }
                WPSeed_Admin_Development_Credits::output();
                break;
            case 'docs':
                if (!class_exists('WPSeed_Admin_Development_Docs')) {
                    require_once $tab_dir . 'tab-docs.php';
                }
                WPSeed_Admin_Development_Docs::output();
                break;
            case 'dev_checklist':
                if (!class_exists('WPSeed_Admin_Development_Checklist')) {
                    require_once $tab_dir . 'tab-checklist.php';
                }
                WPSeed_Admin_Development_Checklist::output();
                break;
            case 'layouts':
                if (!class_exists('WPSeed_Admin_Development_Layouts')) {
                    require_once $tab_dir . 'tab-layouts.php';
                }
                WPSeed_Admin_Development_Layouts::output();
                break;
            case 'diagrams':
                if (!class_exists('WPSeed_Admin_Development_Diagrams')) {
                    require_once $tab_dir . 'tab-diagrams.php';
                }
                WPSeed_Admin_Development_Diagrams::output();
                break;
            case 'architecture':
                require_once $tab_dir . 'tab-architecture.php';
                break;
            case 'roadmap':
                require_once $tab_dir . 'tab-roadmap.php';
                break;
            default:
                if (!class_exists('WPSeed_Admin_Development_UI_Library')) {
                    require_once $tab_dir . 'tab-theme.php';
                }
                WPSeed_Admin_Development_UI_Library::output();
                break;
        }
    }
}
