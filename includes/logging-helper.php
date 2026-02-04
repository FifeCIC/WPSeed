<?php
/**
 * WPSeed Logging Helper
 * 
 * Simple static methods for logging to specific files
 * 
 * @package WPSeed/Core
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Logging_Helper {
    
    /**
     * Log general application events
     */
    public static function log_app($message, $context = array()) {
        if (get_option('wpseed_logging_enabled', 'yes') !== 'yes') {
            return;
        }
        
        if (!empty($context)) {
            $message .= ' | Context: ' . json_encode($context);
        }
        
        self::write_log('app.log', $message);
    }
    
    /**
     * Log API activities
     */
    public static function log_api($action, $endpoint, $details = array()) {
        if (get_option('wpseed_logging_api', 'yes') !== 'yes') {
            return;
        }
        
        $message = sprintf('API: %s %s', strtoupper($action), $endpoint);
        if (!empty($details)) {
            $message .= ' | ' . json_encode($details);
        }
        
        self::write_log('api.log', $message);
    }
    
    /**
     * Log errors
     */
    public static function log_error($error_code, $error_message, $context = array()) {
        if (get_option('wpseed_logging_errors', 'yes') !== 'yes') {
            return;
        }
        
        $message = sprintf('ERROR [%s]: %s', $error_code, $error_message);
        if (!empty($context)) {
            $message .= ' | Context: ' . json_encode($context);
        }
        
        self::write_log('errors.log', $message);
    }
    
    /**
     * Log debug information
     */
    public static function log_debug($message, $data = null) {
        if (get_option('wpseed_logging_debug', 'no') !== 'yes') {
            return;
        }
        
        if ($data !== null) {
            $message .= ' | Data: ' . print_r($data, true);
        }
        
        self::write_log('debug.log', $message);
    }
    
    /**
     * Write to log file
     */
    private static function write_log($filename, $message) {
        $log_dir = defined('WPSEED_LOG_DIR') ? WPSEED_LOG_DIR : WP_CONTENT_DIR . '/wpseed-logs/';
        
        if (!is_dir($log_dir)) {
            wp_mkdir_p($log_dir);
        }
        
        $file_path = $log_dir . $filename;
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = sprintf('[%s] %s%s', $timestamp, $message, PHP_EOL);
        
        error_log($log_entry, 3, $file_path);
    }
    
    /**
     * Clear a specific log file
     */
    public static function clear_log($filename) {
        $log_dir = defined('WPSEED_LOG_DIR') ? WPSEED_LOG_DIR : WP_CONTENT_DIR . '/wpseed-logs/';
        $file_path = $log_dir . $filename;
        
        if (file_exists($file_path)) {
            file_put_contents($file_path, '');
            return true;
        }
        
        return false;
    }
    
    /**
     * Get log file contents
     */
    public static function get_log($filename, $lines = 100) {
        $log_dir = defined('WPSEED_LOG_DIR') ? WPSEED_LOG_DIR : WP_CONTENT_DIR . '/wpseed-logs/';
        $file_path = $log_dir . $filename;
        
        if (!file_exists($file_path)) {
            return '';
        }
        
        $content = file_get_contents($file_path);
        $all_lines = explode("\n", $content);
        $last_lines = array_slice($all_lines, -$lines);
        
        return implode("\n", $last_lines);
    }
}
