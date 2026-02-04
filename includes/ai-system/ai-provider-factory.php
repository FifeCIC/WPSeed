<?php
/**
 * WPSeed AI Provider Factory
 *
 * @package WPSeed/AI
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_AI_Provider_Factory {
    
    public static function get_provider($provider_name = null, $task_type = 'general') {
        // Use router if no provider specified
        if ($provider_name === null) {
            $provider_name = WPSeed_AI_Router::get_provider_for_task($task_type);
            
            if ($provider_name === null) {
                return null; // Rate limit exceeded
            }
        }
        
        $class_name = 'WPSeed_AI_Provider_' . ucfirst($provider_name);
        
        if (class_exists($class_name)) {
            return new $class_name();
        }
        
        return null;
    }
}
