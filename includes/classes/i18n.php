<?php
/**
 * Internationalization Helper
 *
 * @package WPSeed/i18n
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_i18n {
    
    public function __construct() {
        add_action('init', array($this, 'load_plugin_textdomain'));
    }
    
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'wpseed',
            false,
            dirname(plugin_basename(WPSEED_PLUGIN_FILE)) . '/languages/'
        );
    }
    
    public static function is_rtl() {
        return is_rtl();
    }
}

return new WPSeed_i18n();
