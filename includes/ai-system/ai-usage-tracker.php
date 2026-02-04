<?php
/**
 * WPSeed AI Usage Tracker
 *
 * @package WPSeed/AI
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_AI_Usage_Tracker {
    
    public static function track_call($provider, $task_type, $success = true, $tokens = 0) {
        $today = date('Y-m-d');
        $usage_key = "wpseed_ai_usage_{$provider}_{$today}";
        
        $usage = get_option($usage_key, array(
            'total_calls' => 0,
            'successful_calls' => 0,
            'failed_calls' => 0,
            'tasks' => array(),
            'tokens_used' => 0,
            'last_call' => null,
            'rate_limited' => false
        ));
        
        $usage['total_calls']++;
        if ($success) {
            $usage['successful_calls']++;
        } else {
            $usage['failed_calls']++;
        }
        
        if (!isset($usage['tasks'][$task_type])) {
            $usage['tasks'][$task_type] = 0;
        }
        $usage['tasks'][$task_type]++;
        $usage['tokens_used'] += $tokens;
        $usage['last_call'] = current_time('mysql');
        
        update_option($usage_key, $usage);
    }
    
    public static function mark_rate_limited($provider, $reset_time = null) {
        $today = date('Y-m-d');
        $usage_key = "wpseed_ai_usage_{$provider}_{$today}";
        
        $usage = get_option($usage_key, array());
        $usage['rate_limited'] = true;
        $usage['rate_limit_time'] = current_time('mysql');
        if ($reset_time) {
            $usage['rate_limit_reset'] = $reset_time;
        }
        
        update_option($usage_key, $usage);
    }
    
    public static function is_likely_rate_limited($provider) {
        $today = date('Y-m-d');
        $usage_key = "wpseed_ai_usage_{$provider}_{$today}";
        $usage = get_option($usage_key, array(
            'total_calls' => 0,
            'rate_limited' => false,
            'rate_limit_time' => null
        ));
        
        if (!empty($usage['rate_limited']) && !empty($usage['rate_limit_time'])) {
            $limit_time = strtotime($usage['rate_limit_time']);
            $cooling_period = self::get_cooling_period($provider);
            
            if (time() - $limit_time < $cooling_period) {
                return true;
            }
            
            $usage['rate_limited'] = false;
            $usage['rate_limit_time'] = null;
            update_option($usage_key, $usage);
        }
        
        $limits = get_option('wpseed_ai_rate_limits', array(
            'amazonq' => array('free' => 100, 'premium' => 1000),
            'gemini' => array('free' => 50, 'premium' => 500)
        ));
        
        $tier = get_option('wpseed_ai_tier', 'free');
        $limit = isset($limits[$provider][$tier]) ? $limits[$provider][$tier] : 50;
        
        return $usage['total_calls'] >= $limit;
    }
    
    private static function get_cooling_period($provider) {
        $periods = array(
            'amazonq' => 3600,
            'gemini' => 1800
        );
        return $periods[$provider] ?? 1800;
    }
    
    public static function get_usage_stats($provider, $days = 7) {
        $stats = array();
        
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $usage_key = "wpseed_ai_usage_{$provider}_{$date}";
            $usage = get_option($usage_key, array(
                'total_calls' => 0,
                'successful_calls' => 0,
                'failed_calls' => 0,
                'tasks' => array(),
                'tokens_used' => 0
            ));
            
            $stats[$date] = $usage;
        }
        
        return $stats;
    }
}
