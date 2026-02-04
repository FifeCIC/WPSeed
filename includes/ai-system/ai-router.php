<?php
/**
 * WPSeed AI Router
 *
 * @package WPSeed/AI
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_AI_Router {
    
    private static $task_assignments = array(
        'code_analysis' => array('primary' => 'amazonq', 'fallback' => 'gemini'),
        'documentation' => array('primary' => 'gemini', 'fallback' => 'amazonq'),
        'general' => array('primary' => 'gemini', 'fallback' => 'amazonq'),
        'debugging' => array('primary' => 'amazonq', 'fallback' => 'gemini')
    );
    
    public static function get_provider_for_task($task_type) {
        $assignments = get_option('wpseed_ai_task_assignments', self::$task_assignments);
        
        if (!isset($assignments[$task_type])) {
            $task_type = 'general';
        }
        
        $providers = array($assignments[$task_type]['primary']);
        if (!empty($assignments[$task_type]['fallback'])) {
            $providers[] = $assignments[$task_type]['fallback'];
        }
        
        // Reorder: available first, rate-limited last
        $available = array();
        $rate_limited = array();
        
        foreach ($providers as $provider) {
            if (WPSeed_AI_Usage_Tracker::is_likely_rate_limited($provider)) {
                $rate_limited[] = $provider;
            } else {
                $available[] = $provider;
            }
        }
        
        $ordered = array_merge($available, $rate_limited);
        
        foreach ($ordered as $provider) {
            // Skip rate-limited unless it's the last option
            if (WPSeed_AI_Usage_Tracker::is_likely_rate_limited($provider) && count($ordered) > 1) {
                continue;
            }
            
            return $provider;
        }
        
        return null;
    }
    
    public static function get_task_assignments() {
        return get_option('wpseed_ai_task_assignments', self::$task_assignments);
    }
    
    public static function update_task_assignment($task_type, $primary, $fallback) {
        $assignments = self::get_task_assignments();
        $assignments[$task_type] = array('primary' => $primary, 'fallback' => $fallback);
        update_option('wpseed_ai_task_assignments', $assignments);
    }
}
