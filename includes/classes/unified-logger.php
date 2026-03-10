<?php
/**
 * WPSeed Unified Logger
 * Enhanced logging with loop counting and streamlined traces
 *
 * @package WPSeed
 * @version 2.0.0
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Unified_Logger {
    
    private static $instance = null;
    private $logs = array();
    private $loop_counters = array();
    private $current_context = '';
    private $start_time;
    
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->start_time = microtime(true);
    }
    
    /**
     * Start a new logging context
     */
    public function start_context($context) {
        $this->current_context = $context;
        $this->loop_counters = array();
        $this->trace("CONTEXT_START", "Starting {$context}");
    }
    
    /**
     * Enhanced trace with loop counting
     */
    public function trace($type, $message, $data = array()) {
        if (!wpseed_is_developer_mode()) return;
        
        $trace_key = $type . ':' . $message;
        
        // Count occurrences for loop detection
        if (!isset($this->loop_counters[$trace_key])) {
            $this->loop_counters[$trace_key] = 0;
        }
        $this->loop_counters[$trace_key]++;
        
        $log_entry = array(
            'timestamp' => microtime(true),
            'context' => $this->current_context,
            'type' => $type,
            'message' => $message,
            'count' => $this->loop_counters[$trace_key],
            'data' => $data,
            'memory' => memory_get_usage()
        );
        
        $this->logs[] = $log_entry;
        
        // Output immediately for critical traces
        if (in_array($type, array('ERROR', 'CRITICAL', 'DATA_LOSS'))) {
            $this->output_trace($log_entry);
        }
    }
    
    /**
     * Log JavaScript data transmission
     */
    public function js_trace($operation, $data_count, $details = array()) {
        $this->trace('JS_TRACE', "{$operation}: {$data_count} items", array_merge($details, array(
            'operation' => $operation,
            'count' => $data_count
        )));
    }
    
    /**
     * Log PHP data processing
     */
    public function php_trace($operation, $input_count, $output_count, $details = array()) {
        $this->trace('PHP_TRACE', "{$operation}: {$input_count} → {$output_count}", array_merge($details, array(
            'operation' => $operation,
            'input_count' => $input_count,
            'output_count' => $output_count,
            'data_loss' => $input_count > $output_count
        )));
        
        // Flag potential data loss
        if ($input_count > $output_count) {
            $this->trace('DATA_LOSS', "Potential data loss: {$input_count} → {$output_count}", $details);
        }
    }
    
    /**
     * Log loop iterations with smart counting
     */
    public function loop_trace($loop_id, $iteration_data = array()) {
        $loop_key = "LOOP:{$loop_id}";
        
        if (!isset($this->loop_counters[$loop_key])) {
            $this->loop_counters[$loop_key] = 0;
            $this->trace('LOOP_START', "Starting loop: {$loop_id}");
        }
        
        $this->loop_counters[$loop_key]++;
        
        // Only log every 10th iteration for large loops, or specific items
        if ($this->loop_counters[$loop_key] % 10 === 0 || !empty($iteration_data['force_log'])) {
            $this->trace('LOOP_ITERATION', "Loop {$loop_id} iteration {$this->loop_counters[$loop_key]}", $iteration_data);
        }
    }
    
    /**
     * End loop and summarize
     */
    public function loop_end($loop_id, $summary = array()) {
        $loop_key = "LOOP:{$loop_id}";
        $total_iterations = isset($this->loop_counters[$loop_key]) ? $this->loop_counters[$loop_key] : 0;
        
        $this->trace('LOOP_END', "Loop {$loop_id} completed: {$total_iterations} iterations", $summary);
    }
    
    /**
     * Get loop summary for debugging
     */
    public function get_loop_summary() {
        $summary = array();
        foreach ($this->loop_counters as $key => $count) {
            if (strpos($key, 'LOOP:') === 0) {
                $loop_id = substr($key, 5);
                $summary[$loop_id] = $count;
            }
        }
        return $summary;
    }
    
    /**
     * Output trace immediately
     */
    private function output_trace($log_entry) {
        if (!wpseed_is_developer_mode()) return;
        
        $elapsed = number_format(($log_entry['timestamp'] - $this->start_time) * 1000, 2);
        $memory = number_format($log_entry['memory'] / 1024 / 1024, 2);
        
        $output = sprintf(
            "[%s] %s (+%sms, %sMB): %s",
            $log_entry['type'],
            $log_entry['context'],
            $elapsed,
            $memory,
            $log_entry['message']
        );
        
        if ($log_entry['count'] > 1) {
            $output .= " (×{$log_entry['count']})";
        }
        
        if (!empty($log_entry['data'])) {
            $output .= " | " . json_encode($log_entry['data']);
        }
        
        error_log("WPSeed_Trace: " . $output);
    }
    
    /**
     * End context and output summary
     */
    public function end_context($summary = array()) {
        $this->trace('CONTEXT_END', "Ending {$this->current_context}", $summary);
        
        if (wpseed_is_developer_mode()) {
            $this->output_summary();
        }
        
        $this->current_context = '';
    }
    
    /**
     * Output complete summary
     */
    private function output_summary() {
        $total_logs = count($this->logs);
        $duration = (microtime(true) - $this->start_time) * 1000;
        
        error_log("WPSeed_Summary: Context '{$this->current_context}' - {$total_logs} logs in " . number_format($duration, 2) . "ms");
        
        // Count by type
        $type_counts = array();
        foreach ($this->logs as $log) {
            $type = $log['type'];
            if (!isset($type_counts[$type])) {
                $type_counts[$type] = 0;
            }
            $type_counts[$type]++;
        }
        
        foreach ($type_counts as $type => $count) {
            error_log("WPSeed_Summary: {$type}: {$count}");
        }
        
        // Loop summary
        $loop_summary = $this->get_loop_summary();
        if (!empty($loop_summary)) {
            error_log("WPSeed_Summary: Loops: " . json_encode($loop_summary));
        }
    }
    
    /**
     * Get all logs for debugging
     */
    public function get_logs() {
        return $this->logs;
    }
    
    /**
     * Clear logs
     */
    public function clear_logs() {
        $this->logs = array();
        $this->loop_counters = array();
    }
}

// JavaScript logging helper
function wpseed_js_log_helper() {
    if (!wpseed_is_developer_mode()) return;
    ?>
    <script>
    window.WPSeedLogger = {
        context: '',
        loopCounters: {},
        
        startContext: function(context) {
            this.context = context;
            this.loopCounters = {};
            console.log(`WPSeed_JS: CONTEXT_START - ${context}`);
        },
        
        trace: function(type, message, data = {}) {
            const timestamp = performance.now();
            const logKey = `${type}:${message}`;
            
            if (!this.loopCounters[logKey]) {
                this.loopCounters[logKey] = 0;
            }
            this.loopCounters[logKey]++;
            
            const output = `WPSeed_JS: [${type}] ${this.context} (+${timestamp.toFixed(2)}ms): ${message}`;
            
            if (this.loopCounters[logKey] > 1) {
                console.log(`${output} (×${this.loopCounters[logKey]})`);
            } else {
                console.log(output);
            }
            
            if (Object.keys(data).length > 0) {
                console.log('WPSeed_JS_Data:', data);
            }
        },
        
        loopTrace: function(loopId, iterationData = {}) {
            const loopKey = `LOOP:${loopId}`;
            
            if (!this.loopCounters[loopKey]) {
                this.loopCounters[loopKey] = 0;
                this.trace('LOOP_START', `Starting loop: ${loopId}`);
            }
            
            this.loopCounters[loopKey]++;
            
            // Only log every 10th iteration or forced logs
            if (this.loopCounters[loopKey] % 10 === 0 || iterationData.forceLog) {
                this.trace('LOOP_ITERATION', `Loop ${loopId} iteration ${this.loopCounters[loopKey]}`, iterationData);
            }
        },
        
        loopEnd: function(loopId, summary = {}) {
            const loopKey = `LOOP:${loopId}`;
            const totalIterations = this.loopCounters[loopKey] || 0;
            
            this.trace('LOOP_END', `Loop ${loopId} completed: ${totalIterations} iterations`, summary);
        },
        
        endContext: function(summary = {}) {
            this.trace('CONTEXT_END', `Ending ${this.context}`, summary);
            
            // Output summary
            const loopSummary = {};
            Object.keys(this.loopCounters).forEach(key => {
                if (key.startsWith('LOOP:')) {
                    const loopId = key.substring(5);
                    loopSummary[loopId] = this.loopCounters[key];
                }
            });
            
            if (Object.keys(loopSummary).length > 0) {
                console.log('WPSeed_JS_Summary: Loops:', loopSummary);
            }
            
            this.context = '';
        }
    };
    </script>
    <?php
}

// Initialize
add_action('admin_footer', 'wpseed_js_log_helper');

// Global helper functions
function wpseed_log() {
    return WPSeed_Unified_Logger::instance();
}

function wpseed_trace($type, $message, $data = array()) {
    WPSeed_Unified_Logger::instance()->trace($type, $message, $data);
}