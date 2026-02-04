<?php
/**
 * Multisite Support
 *
 * @package WPSeed/Multisite
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Multisite {
    
    public function __construct() {
        if (is_multisite()) {
            add_action('network_admin_menu', array($this, 'network_admin_menu'));
        }
    }
    
    public function network_admin_menu() {
        // Add network admin menu items
    }
    
    public static function is_network_activated() {
        if (!is_multisite()) {
            return false;
        }
        
        $plugins = get_site_option('active_sitewide_plugins');
        return isset($plugins[plugin_basename(WPSEED_PLUGIN_FILE)]);
    }
    
    public static function get_sites() {
        if (!is_multisite()) {
            return array();
        }
        
        return get_sites(array('number' => 999));
    }
}

return new WPSeed_Multisite();
