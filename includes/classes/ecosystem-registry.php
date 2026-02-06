<?php
/**
 * Plugin Ecosystem Registry
 * Manages communication between multiple Ryan Bayne plugins
 *
 * @package WPSeed/Ecosystem
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Ecosystem_Registry {
    
    private static $instance = null;
    private $registered_plugins = array();
    private $shared_resources = array();
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('plugins_loaded', array($this, 'detect_ecosystem'), 5);
    }
    
    /**
     * Register a plugin in the ecosystem
     */
    public function register_plugin($slug, $args = array()) {
        $defaults = array(
            'name' => '',
            'version' => '1.0.0',
            'path' => '',
            'url' => '',
            'has_logging' => false,
            'has_cron' => false,
            'has_background_tasks' => false,
            'shared_settings' => array(),
        );
        
        $this->registered_plugins[$slug] = wp_parse_args($args, $defaults);
        
        do_action('wpseed_ecosystem_plugin_registered', $slug, $this->registered_plugins[$slug]);
    }
    
    /**
     * Get all registered plugins
     */
    public function get_plugins() {
        return $this->registered_plugins;
    }
    
    /**
     * Check if plugin is registered
     */
    public function is_registered($slug) {
        return isset($this->registered_plugins[$slug]);
    }
    
    /**
     * Get plugin count
     */
    public function get_plugin_count() {
        return count($this->registered_plugins);
    }
    
    /**
     * Check if ecosystem mode (2+ plugins)
     */
    public function is_ecosystem_mode() {
        return $this->get_plugin_count() >= 2;
    }
    
    /**
     * Detect ecosystem plugins
     */
    public function detect_ecosystem() {
        // Allow other plugins to register
        do_action('wpseed_ecosystem_register');
        
        // Store ecosystem status
        update_option('wpseed_ecosystem_mode', $this->is_ecosystem_mode());
        update_option('wpseed_ecosystem_plugins', $this->registered_plugins);
    }
    
    /**
     * Register shared resource (logging, cron viewer, etc.)
     */
    public function register_shared_resource($type, $callback, $priority = 10) {
        if (!isset($this->shared_resources[$type])) {
            $this->shared_resources[$type] = array();
        }
        
        $this->shared_resources[$type][] = array(
            'callback' => $callback,
            'priority' => $priority,
        );
        
        // Sort by priority
        usort($this->shared_resources[$type], function($a, $b) {
            return $a['priority'] - $b['priority'];
        });
    }
    
    /**
     * Get shared resources of a type
     */
    public function get_shared_resources($type) {
        return isset($this->shared_resources[$type]) ? $this->shared_resources[$type] : array();
    }
    
    /**
     * Get menu location preference
     */
    public function get_menu_location($default = 'plugin') {
        if ($this->is_ecosystem_mode()) {
            return get_option('wpseed_ecosystem_menu_location', 'shared');
        }
        return $default;
    }
    
    /**
     * Should use shared menu location?
     */
    public function use_shared_menu() {
        return $this->get_menu_location() === 'shared';
    }
}

/**
 * Global accessor
 */
function wpseed_ecosystem() {
    return WPSeed_Ecosystem_Registry::instance();
}

// Initialize
wpseed_ecosystem();

// Register this plugin
add_action('wpseed_ecosystem_register', function() {
    wpseed_ecosystem()->register_plugin('wpseed', array(
        'name' => 'WPSeed',
        'version' => WPSEED_VERSION,
        'path' => WPSEED_PLUGIN_DIR_PATH,
        'url' => plugins_url('/', WPSEED_PLUGIN_FILE),
        'has_logging' => true,
        'has_cron' => true,
        'has_background_tasks' => true,
        'shared_settings' => array('logging', 'cron', 'background_tasks'),
    ));
});
