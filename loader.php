<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main WPSeed Class.
 *
 * @class WPSeed
 * @version 1.0.0
 */
final class WordPressPluginSeed {
    
    /**
     * WPSeed version.
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Minimum WP version.
     *
     * @var string
     */
    public $min_wp_version = '4.4';
    
    /**
     * The single instance of the class.
     *
     * @var WPSeed
     * @since 2.1
     */
    protected static $_instance = null;

    /**
     * Session instance.
     *
     * @var WPSeed_Session
     */
    public $session = null; 
        
    /**
     * Main WPSeed Instance.
     *
     * Ensures only one instance of WPSeed is loaded or can be loaded.
     *
     * @since 1.0
     * @static
     * @see WordPressSeed()
     * @return WPSeed - Main instance.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }                    
        return self::$_instance;
    }

    /**
     * Cloning WPSeed is forbidden.
     * @since 1.0
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, __( 'Your not allowed to do that!', 'wpseed' ), '1.0' );
    }

    /**
     * Unserializing instances of this class is forbidden.
     * @since 1.0
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Your not allowed to do that!', 'wpseed' ), '1.0' );
    }

    /**
     * Auto-load in-accessible properties on demand.
     * @param mixed $key
     * @return mixed
     */
    public function __get( $key ) {
        if ( in_array( $key, array( 'mailer' ) ) ) {
            return $this->$key();
        }
    }   
    
    /**
     * WPSeed Constructor.
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();

        do_action( 'wpseed_loaded' );
    }

    /**
     * Hook into actions and filters.
     * @since  1.0
     */
    private function init_hooks() {
        register_activation_hook( __FILE__, array( 'WPSeed_Install', 'install' ) );
        // Do not confuse deactivation of a plugin with deletion of a plugin - two very different requests.
        register_deactivation_hook( __FILE__, array( 'WPSeed_Install', 'deactivate' ) );
        add_action( 'init', array( $this, 'init' ), 0 );
    }

    /**
     * Define WPSeed Constants.
     */
    private function define_constants() {
        
        $upload_dir = wp_upload_dir();

        if ( ! defined( 'WPSEED_MIN_WP_VERSION' ) ) { define( 'WPSEED_MIN_WP_VERSION', $this->min_wp_version ); }
        
        // Main (package) constants.
        if ( ! defined( 'WPSEED_LOG_DIR' ) ) { define( 'WPSEED_LOG_DIR', $upload_dir['basedir'] . '/wpseed-logs/' ); }
        if ( ! defined( 'WPSEED_SESSION_CACHE_GROUP' ) ) { define( 'WPSEED_SESSION_CACHE_GROUP', 'wpseed_session_id' ); }
        if ( ! defined( 'WPSEED_DEV_MODE' ) ) { define( 'WPSEED_DEV_MODE', false ); }
        if ( ! defined( 'WPSEED_WORDPRESSORG_SLUG' ) ) { define( 'WPSEED_WORDPRESSORG_SLUG', false ); }
        if ( ! defined( 'WPSEED_MARKETPLACE' ) ) { define( 'WPSEED_MARKETPLACE', false ); }
        if ( ! defined( 'WPSEED_MARKETPLACE_ID' ) ) { define( 'WPSEED_MARKETPLACE_ID', false ); }
                                      
        // Support (project) constants.                                         
        if ( ! defined( 'WPSEED_HOME' ) ) { define( 'WPSEED_HOME', 'https://github.com/RyanBayne/wordpresspluginseed' ); }
        if ( ! defined( 'WPSEED_FORUM' ) ) { define( 'WPSEED_FORUM', 'https://wpseed.slack.com/' ); }
        if ( ! defined( 'WPSEED_TWITTER' ) ) { define( 'WPSEED_TWITTER', false ); }
        if ( ! defined( 'WPSEED_DONATE' ) ) { define( 'WPSEED_DONATE', 'https://www.patreon.com/ryanbayne' ); }
        if ( ! defined( 'WPSEED_SKYPE' ) ) { define( 'WPSEED_SKYPE', 'https://join.skype.com/bVtDaGHd9Nnl' ); }
        if ( ! defined( 'WPSEED_GITHUB' ) ) { define( 'WPSEED_GITHUB', 'https://github.com/RyanBayne/wordpresspluginseed' ); }
        if ( ! defined( 'WPSEED_SLACK' ) ) { define( 'WPSEED_SLACK', 'https://wpseed.slack.com/' ); }
        if ( ! defined( 'WPSEED_DOCS' ) ) { define( 'WPSEED_DOCS', 'https://github.com/RyanBayne/wpseed/wiki' ); }
        if ( ! defined( 'WPSEED_FACEBOOK' ) ) { define( 'WPSEED_FACEBOOK', 'https://www.facebook.com/WordPress-Plugin-Seed-704154249757165/' ); }
       
        // Author (social) constants - can act as default when support constants are false.                                                                                                              
        if ( ! defined( 'WPSEED_AUTHOR_HOME' ) ) { define( 'WPSEED_AUTHOR_HOME', 'https://ryanbayne.wordpress.com' ); }
        if ( ! defined( 'WPSEED_AUTHOR_FORUM' ) ) { define( 'WPSEED_AUTHOR_FORUM', false ); }
        if ( ! defined( 'WPSEED_AUTHOR_TWITTER' ) ) { define( 'WPSEED_AUTHOR_TWITTER', 'http://www.twitter.com/Ryan_R_Bayne' ); }
        if ( ! defined( 'WPSEED_AUTHOR_FACEBOOK' ) ) { define( 'WPSEED_AUTHOR_FACEBOOK', 'https://www.facebook.com/ryanrbayne' ); }
        if ( ! defined( 'WPSEED_AUTHOR_DONATE' ) ) { define( 'WPSEED_AUTHOR_DONATE', 'https://www.patreon.com/ryanbayne' ); }
        if ( ! defined( 'WPSEED_AUTHOR_SKYPE' ) ) { define( 'WPSEED_AUTHOR_SKYPE', 'https://join.skype.com/gNuxSa4wnQTV' ); }
        if ( ! defined( 'WPSEED_AUTHOR_GITHUB' ) ) { define( 'WPSEED_AUTHOR_GITHUB', 'https://github.com/RyanBayne' ); }
        if ( ! defined( 'WPSEED_AUTHOR_LINKEDIN' ) ) { define( 'WPSEED_AUTHOR_LINKEDIN', 'https://www.linkedin.com/in/ryanrbayne/' ); }
        if ( ! defined( 'WPSEED_AUTHOR_DISCORD' ) ) { define( 'WPSEED_AUTHOR_DISCORD', 'https://discord.gg/xBNYA7Q' ); }
        if ( ! defined( 'WPSEED_AUTHOR_SLACK' ) ) { define( 'WPSEED_AUTHOR_SLACK', 'https://ryanbayne.slack.com/threads/team/' ); }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {
        
        include_once( 'includes/functions/core.php' );
        include_once( 'includes/classes/debug.php' );    
        include_once( 'includes/classes/autoloader.php' );
        include_once( 'includes/functions/validate.php' );
        include_once( 'includes/logging-helper.php' );
        include_once( 'includes/api-logging.php' );
        include_once( 'includes/functions/database.php' );
        include_once( 'includes/classes/asset-manager.php' );
        include_once( 'includes/classes/install.php' );
        include_once( 'includes/classes/ajax.php' );
        
        // Background Processing & Advanced Features
        include_once( 'includes/classes/async-request.php' );
        include_once( 'includes/classes/background-process.php' );
        include_once( 'includes/classes/object-registry.php' );
        include_once( 'includes/classes/data-freshness-manager.php' );
        include_once( 'includes/classes/developer-flow-logger.php' );
        
        // Action Scheduler Library
        if (file_exists(plugin_dir_path(__FILE__) . 'includes/libraries/action-scheduler/action-scheduler.php')) {
            require_once plugin_dir_path(__FILE__) . 'includes/libraries/action-scheduler/action-scheduler.php';
            include_once( 'includes/classes/task-scheduler.php' );
        }
        
        // Carbon Fields Library
        if (file_exists(plugin_dir_path(__FILE__) . 'includes/libraries/carbon-fields/core/Carbon_Fields.php')) {
            require_once plugin_dir_path(__FILE__) . 'includes/libraries/carbon-fields/core/Carbon_Fields.php';
            include_once( 'includes/classes/carbon-fields-integration.php' );
        }
        
        // Request Listener
        include_once( 'includes/classes/listener.php' );
        
        // Dashboard Widgets
        include_once( 'includes/classes/dashboard-widgets.php' );
        
        // Unified Feature Example
        include_once( 'includes/classes/unified-feature.php' );
        
        // New Boilerplate Features
        include_once( 'includes/classes/developer-mode.php' );
        include_once( 'includes/classes/rest-controller.php' );
        include_once( 'includes/classes/rest-example.php' );
        include_once( 'includes/classes/rest-education.php' );
        include_once( 'includes/classes/i18n.php' );
        include_once( 'includes/classes/dependencies.php' );
        include_once( 'includes/classes/multisite.php' );
        include_once( 'includes/classes/education.php' );
        include_once( 'includes/classes/github-sync.php' );
        include_once( 'includes/functions/github-sync-ajax.php' );
        include_once( 'includes/classes/settings-import-export.php' );
        include_once( 'includes/classes/license-manager.php' );
        include_once( 'includes/classes/extension-installer.php' );
        include_once( 'includes/classes/library-manager.php' );
        include_once( 'includes/classes/enhanced-logger.php' );
        include_once( 'includes/classes/notification-bell.php' );
        
        // Ecosystem Framework
        include_once( 'includes/classes/ecosystem-registry.php' );
        include_once( 'includes/classes/ecosystem-menu-manager.php' );
        include_once( 'includes/classes/ecosystem-installer.php' );
        
        // WP-CLI Commands
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            include_once( 'includes/classes/cli-commands.php' );
        }
        
        // REST API
        add_action( 'rest_api_init', function() {
            $controller = new WPSeed_REST_Example_Controller();
            $controller->register_routes();
            
            $education_controller = new WPSeed_REST_Education_Controller();
            $education_controller->register_routes();
        });
        
        // API System
        include_once( 'api/base-api.php' );
        include_once( 'api/api-directory.php' );
        include_once( 'api/api-factory.php' );
        
        if ( $this->is_request( 'admin' ) ) {
            include_once( 'includes/admin/admin.php' );
            include_once( 'includes/admin/admin-main-views.php' );
            include_once( 'admin/config/admin-menus.php' );
            include_once( 'admin/notifications/notifications.php' );
            include_once( 'toolbars/toolbars.php' );
            include_once( 'includes/classes/uninstall-feedback.php' );
        }

        if ( $this->is_request( 'frontend' ) ) {
            $this->frontend_includes();
        }
        
        include_once( 'shortcodes/shortcodes.php' );
    }

    /**
     * Include required frontend files.
     */
    public function frontend_includes() {
        include_once( 'includes/classes/frontend-scripts.php' );  
    }

    /**
     * Initialise WordPress Plugin Seed when WordPress Initialises.
     */
    public function init() {                     
        // Before init action.
        do_action( 'before_wpseed_init' );

        // Init action.
        do_action( 'wpseed_init' );
    }

    /**
     * Get the plugin url.
     * @return string
     */
    public function plugin_url() {                
        return untrailingslashit( plugins_url( '/', __FILE__ ) );
    }

    /**
     * Get the plugin path.
     * @return string
     */
    public function plugin_path() {              
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * Get Ajax URL (this is the URL to WordPress core ajax file).
     * @return string
     */
    public function ajax_url() {                
        return admin_url( 'admin-ajax.php', 'relative' );
    }

    /**
     * What type of request is this?
     *
     * Functions and constants are WordPress core. This function will allow
     * you to avoid large operations or output at the wrong time.
     * 
     * @param  string $type admin, ajax, cron or frontend.
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined( 'DOING_AJAX' );
            case 'cron' :
                return defined( 'DOING_CRON' );
            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }    
}

if( !function_exists( 'WPSeed' ) ) {
    /**
     * Main instance of WordPress Plugin Seed.
     *
     * Returns the main instance of WPSeed to prevent the need to use globals.
     *
     * @since  1.0
     * @return WPSeed
     */
    function WPSeed() {
        return WordPressPluginSeed::instance();
    }

    // Global for backwards compatibility.
    global $GLOBALS;
    $GLOBALS['wpseed'] = WPSeed();
}
