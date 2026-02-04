<?php
/**
 * WPSeed API Factory
 *
 * @package WPSeed/API
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_API_Factory {
    
    public static function create($provider_id, $args = array()) {
        $provider = WPSeed_API_Directory::get_provider($provider_id);
        
        if (!$provider) {
            return new WP_Error('unknown_provider', 'Unknown API provider: ' . $provider_id);
        }
        
        $class_file = WPSEED_PLUGIN_DIR_PATH . 'api/' . $provider['class_path'];
        
        if (!file_exists($class_file)) {
            return new WP_Error('missing_class_file', 'API class file not found');
        }
        
        require_once $class_file;
        
        if (!class_exists($provider['class_name'])) {
            return new WP_Error('missing_class', 'API class not found');
        }
        
        try {
            $instance = new $provider['class_name']($provider_id, $args);
            
            if (!($instance instanceof WPSeed_Base_API)) {
                return new WP_Error('invalid_base_class', 'API class must extend WPSeed_Base_API');
            }
            
            return $instance;
            
        } catch (Exception $e) {
            return new WP_Error('instantiation_error', $e->getMessage());
        }
    }
    
    public static function create_from_settings($provider_id) {
        $args = array(
            'api_key' => get_option("wpseed_api_{$provider_id}_key", ''),
            'api_secret' => get_option("wpseed_api_{$provider_id}_secret", ''),
            'base_url' => get_option("wpseed_api_{$provider_id}_url", '')
        );
        
        return self::create($provider_id, $args);
    }
}
