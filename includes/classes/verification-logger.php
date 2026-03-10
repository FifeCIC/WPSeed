<?php
/**
 * WPSeed Verification Logger
 * Specialized logging for verification process debugging
 *
 * @package WPSeed
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Verification_Logger {
    
    private static $instance = null;
    private $verification_steps = array();
    private $data_counts = array();
    
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Log verification step with data counts
     */
    public function log_step($step_name, $input_count, $output_count, $details = array()) {
        if (!wpseed_is_developer_mode()) return;
        
        $step_data = array(
            'step' => $step_name,
            'input_count' => $input_count,
            'output_count' => $output_count,
            'data_loss' => $input_count > $output_count,
            'loss_amount' => $input_count - $output_count,
            'timestamp' => microtime(true),
            'details' => $details
        );\n        
        $this->verification_steps[] = $step_data;
        $this->data_counts[$step_name] = $output_count;
        
        // Log to unified logger
        if (class_exists('WPSeed_Unified_Logger')) {
            WPSeed_Unified_Logger::instance()->php_trace($step_name, $input_count, $output_count, $details);
        }
        
        // Immediate output for data loss
        if ($step_data['data_loss']) {
            error_log("WPSeed_Verification: DATA LOSS in {$step_name}: {$input_count} → {$output_count} (lost {$step_data['loss_amount']})");
            if (!empty($details)) {
                error_log("WPSeed_Verification: Details: " . json_encode($details));
            }
        }
        
        error_log("WPSeed_Verification: {$step_name}: {$input_count} → {$output_count}");
    }
    
    /**
     * Log JavaScript data transmission
     */
    public function log_js_transmission($operation, $data_count, $payload_size = null) {
        if (!wpseed_is_developer_mode()) return;
        
        $details = array('operation' => $operation, 'count' => $data_count);
        if ($payload_size !== null) {
            $details['payload_size'] = $payload_size;
        }
        
        error_log("WPSeed_Verification: JS_TRACE - {$operation}: {$data_count} items" . 
                 ($payload_size ? " ({$payload_size} bytes)" : ""));
        
        if (class_exists('WPSeed_Unified_Logger')) {
            WPSeed_Unified_Logger::instance()->js_trace($operation, $data_count, $details);
        }
    }
    
    /**
     * Log file processing loops
     */
    public function log_file_loop($loop_id, $file_path, $result_count, $details = array()) {
        if (!wpseed_is_developer_mode()) return;
        
        $loop_details = array_merge($details, array(
            'file' => basename($file_path),
            'result_count' => $result_count
        ));
        
        if (class_exists('WPSeed_Unified_Logger')) {
            WPSeed_Unified_Logger::instance()->loop_trace($loop_id, $loop_details);
        }
    }
    
    /**
     * Log hash comparison results
     */
    public function log_hash_comparison($file_path, $old_hash, $new_hash, $changed) {
        if (!wpseed_is_developer_mode()) return;
        
        $details = array(
            'file' => basename($file_path),
            'old_hash' => $old_hash,
            'new_hash' => $new_hash,
            'changed' => $changed
        );
        
        if (class_exists('WPSeed_Unified_Logger')) {
            WPSeed_Unified_Logger::instance()->trace('HASH_COMPARE', 
                $changed ? 'File changed' : 'File unchanged', $details);
        }
    }
    
    /**
     * Log filter/exclusion operations
     */
    public function log_filter($filter_name, $input_count, $output_count, $criteria = '') {
        if (!wpseed_is_developer_mode()) return;
        
        $this->log_step("FILTER_{$filter_name}", $input_count, $output_count, array(
            'filter' => $filter_name,
            'criteria' => $criteria
        ));
    }
    
    /**
     * Log AJAX payload extraction
     */
    public function log_payload_extraction($payload_key, $expected_count, $actual_count, $raw_data = null) {
        if (!wpseed_is_developer_mode()) return;
        
        $details = array(
            'payload_key' => $payload_key,
            'expected' => $expected_count,
            'actual' => $actual_count
        );
        
        if ($raw_data !== null) {
            $details['raw_data_type'] = gettype($raw_data);
            $details['raw_data_size'] = is_array($raw_data) ? count($raw_data) : strlen($raw_data);
        }
        
        error_log("WPSeed_Verification: EXTRACT_PAYLOAD - {$payload_key}: expected {$expected_count}, got {$actual_count}");
        
        if (class_exists('WPSeed_Unified_Logger')) {
            WPSeed_Unified_Logger::instance()->trace('EXTRACT_PAYLOAD', 
                "Payload {$payload_key}: {$expected_count} → {$actual_count}", $details);
        }
    }
    
    /**
     * Get verification summary
     */
    public function get_verification_summary() {
        $summary = array(
            'total_steps' => count($this->verification_steps),
            'data_loss_steps' => 0,
            'total_data_loss' => 0,
            'final_count' => 0,
            'steps' => array()
        );
        
        foreach ($this->verification_steps as $step) {
            if ($step['data_loss']) {
                $summary['data_loss_steps']++;
                $summary['total_data_loss'] += $step['loss_amount'];
            }
            
            $summary['steps'][] = array(
                'step' => $step['step'],
                'input' => $step['input_count'],
                'output' => $step['output_count'],
                'loss' => $step['loss_amount']
            );
        }
        
        if (!empty($this->verification_steps)) {
            $summary['final_count'] = end($this->verification_steps)['output_count'];
        }
        
        return $summary;
    }
    
    /**
     * Output verification summary to log
     */
    public function output_summary() {
        if (!wpseed_is_developer_mode()) return;
        
        $summary = $this->get_verification_summary();
        
        error_log("WPSeed_Verification: SUMMARY - {$summary['total_steps']} steps, {$summary['data_loss_steps']} with data loss");
        error_log("WPSeed_Verification: SUMMARY - Total data loss: {$summary['total_data_loss']}, Final count: {$summary['final_count']}");
        
        foreach ($summary['steps'] as $step) {
            $loss_indicator = $step['loss'] > 0 ? " ❌ LOST {$step['loss']}" : " ✅";
            error_log("WPSeed_Verification: STEP - {$step['step']}: {$step['input']} → {$step['output']}{$loss_indicator}");
        }
    }
    
    /**
     * Clear verification logs
     */
    public function clear_logs() {
        $this->verification_steps = array();
        $this->data_counts = array();
    }
}

// Global helper functions
function wpseed_verification_log() {
    return WPSeed_Verification_Logger::instance();
}

function wpseed_log_verification_step($step_name, $input_count, $output_count, $details = array()) {
    WPSeed_Verification_Logger::instance()->log_step($step_name, $input_count, $output_count, $details);
}

function wpseed_log_js_data($operation, $count, $size = null) {
    WPSeed_Verification_Logger::instance()->log_js_transmission($operation, $count, $size);
}