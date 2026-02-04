<?php
/**
 * Developer Mode Detection
 * Determines if plugin is running in development environment
 *
 * @package WPSeed/Core
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Developer_Mode {
    
    public static function is_dev_environment() {
        // Check if explicitly enabled
        if (defined('WPSEED_DEV_MODE') && WPSEED_DEV_MODE) {
            return true;
        }
        
        // Check localhost
        if (self::is_localhost()) {
            return true;
        }
        
        // Check allowed domains
        $allowed_domains = get_option('wpseed_dev_domains', array());
        if (!empty($allowed_domains) && self::is_allowed_domain($allowed_domains)) {
            return true;
        }
        
        return false;
    }
    
    private static function is_localhost() {
        $server_name = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
        $localhost_patterns = array('localhost', '127.0.0.1', '::1', '.local', '.test', '.dev');
        
        foreach ($localhost_patterns as $pattern) {
            if (strpos($server_name, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    private static function is_allowed_domain($allowed_domains) {
        $current_domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        return in_array($current_domain, $allowed_domains);
    }
}
