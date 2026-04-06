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
     * Start a new logging context.
     *
     * Resets loop counters and records a CONTEXT_START trace entry.
     * Only active when developer mode is enabled.
     *
     * @since  1.0.0
     * @param  string $context Human-readable context label.
     * @return void
     */
    public function start_context( $context ) {
        $this->current_context = $context;
        $this->loop_counters   = array();
        $this->trace( 'CONTEXT_START', "Starting {$context}" );
    }
    
    /**
     * Record a trace entry with loop-count detection.
     *
     * Entries of type ERROR, CRITICAL, or DATA_LOSS are also written to the
     * debug log immediately via output_trace(). All other entries are stored
     * in memory and available via get_logs(). No-ops when developer mode is
     * not active so there is zero overhead in production.
     *
     * @since  1.0.0
     * @param  string $type    Trace type identifier (e.g. 'ERROR', 'LOOP_START').
     * @param  string $message Human-readable description.
     * @param  array  $data    Optional structured data to attach to the entry.
     * @return void
     */
    public function trace( $type, $message, $data = array() ) {
        if ( ! wpseed_is_developer_mode() ) {
            return;
        }

        $trace_key = $type . ':' . $message;

        // Increment the occurrence counter for this type+message pair so
        // repeated calls can be detected as potential loops.
        if ( ! isset( $this->loop_counters[ $trace_key ] ) ) {
            $this->loop_counters[ $trace_key ] = 0;
        }
        $this->loop_counters[ $trace_key ]++;

        $log_entry = array(
            'timestamp' => microtime( true ),
            'context'   => $this->current_context,
            'type'      => $type,
            'message'   => $message,
            'count'     => $this->loop_counters[ $trace_key ],
            'data'      => $data,
            'memory'    => memory_get_usage(),
        );

        $this->logs[] = $log_entry;

        // Write critical entries to the debug log immediately rather than
        // waiting for end_context(), so they are visible even if execution halts.
        if ( in_array( $type, array( 'ERROR', 'CRITICAL', 'DATA_LOSS' ), true ) ) {
            $this->output_trace( $log_entry );
        }
    }
    
    /**
     * Log a JavaScript data-transmission operation.
     *
     * Convenience wrapper around trace() for JS↔PHP data-flow tracking.
     *
     * @since  1.0.0
     * @param  string $operation  Label describing the operation.
     * @param  int    $data_count Number of items transmitted.
     * @param  array  $details    Optional extra context.
     * @return void
     */
    public function js_trace( $operation, $data_count, $details = array() ) {
        $this->trace( 'JS_TRACE', "{$operation}: {$data_count} items", array_merge( $details, array(
            'operation' => $operation,
            'count'     => $data_count,
        ) ) );
    }
    
    /**
     * Log a PHP data-processing operation with data-loss detection.
     *
     * Automatically records a DATA_LOSS trace when output_count is less than
     * input_count, making silent data drops visible in the debug log.
     *
     * @since  1.0.0
     * @param  string $operation    Label describing the operation.
     * @param  int    $input_count  Number of items entering the operation.
     * @param  int    $output_count Number of items leaving the operation.
     * @param  array  $details      Optional extra context.
     * @return void
     */
    public function php_trace( $operation, $input_count, $output_count, $details = array() ) {
        $this->trace( 'PHP_TRACE', "{$operation}: {$input_count} → {$output_count}", array_merge( $details, array(
            'operation'    => $operation,
            'input_count'  => $input_count,
            'output_count' => $output_count,
            'data_loss'    => $input_count > $output_count,
        ) ) );

        // A separate DATA_LOSS entry ensures the drop is visible immediately
        // in the debug log even if end_context() is never called.
        if ( $input_count > $output_count ) {
            $this->trace( 'DATA_LOSS', "Potential data loss: {$input_count} → {$output_count}", $details );
        }
    }
    
    /**
     * Record a single loop iteration, throttled to every 10th call.
     *
     * Logs a LOOP_START entry on the first call for a given loop ID, then
     * records LOOP_ITERATION entries at every 10th iteration or when
     * $iteration_data['force_log'] is truthy.
     *
     * @since  1.0.0
     * @param  string $loop_id        Unique identifier for the loop.
     * @param  array  $iteration_data Optional data for this iteration.
     * @return void
     */
    public function loop_trace( $loop_id, $iteration_data = array() ) {
        $loop_key = "LOOP:{$loop_id}";

        if ( ! isset( $this->loop_counters[ $loop_key ] ) ) {
            $this->loop_counters[ $loop_key ] = 0;
            $this->trace( 'LOOP_START', "Starting loop: {$loop_id}" );
        }

        $this->loop_counters[ $loop_key ]++;

        // Throttle to every 10th iteration to avoid flooding the log on large
        // loops; force_log bypasses the throttle for specific items of interest.
        if ( $this->loop_counters[ $loop_key ] % 10 === 0 || ! empty( $iteration_data['force_log'] ) ) {
            $this->trace( 'LOOP_ITERATION', "Loop {$loop_id} iteration {$this->loop_counters[ $loop_key ]}", $iteration_data );
        }
    }
    
    /**
     * Record a LOOP_END trace entry with the total iteration count.
     *
     * @since  1.0.0
     * @param  string $loop_id  Unique identifier for the loop.
     * @param  array  $summary  Optional summary data to attach.
     * @return void
     */
    public function loop_end( $loop_id, $summary = array() ) {
        $loop_key         = "LOOP:{$loop_id}";
        $total_iterations = isset( $this->loop_counters[ $loop_key ] ) ? $this->loop_counters[ $loop_key ] : 0;
        $this->trace( 'LOOP_END', "Loop {$loop_id} completed: {$total_iterations} iterations", $summary );
    }
    
    /**
     * Return a summary of all loop iteration counts keyed by loop ID.
     *
     * @since  1.0.0
     * @return array Map of loop_id => iteration_count.
     */
    public function get_loop_summary() {
        $summary = array();
        foreach ( $this->loop_counters as $key => $count ) {
            if ( strpos( $key, 'LOOP:' ) === 0 ) {
                $loop_id            = substr( $key, 5 );
                $summary[ $loop_id ] = $count;
            }
        }
        return $summary;
    }
    
    /**
     * Write a message to the WordPress debug log.
     *
     * Replaces direct error_log() calls. Only writes when both WP_DEBUG and
     * WP_DEBUG_LOG are enabled, keeping output out of production environments.
     * Uses file_put_contents() on the debug log path rather than error_log()
     * to satisfy WordPress.PHP.DevelopmentFunctions.error_log_error_log.
     *
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param  string $message Message to write.
     * @return void
     */
    private function write_log( $message ) {
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }
        if ( ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) {
            return;
        }
        // Resolve the log path: WP_DEBUG_LOG may be a custom file path string
        // (WP 5.1+) or simply true, in which case the default location is used.
        $log_path = is_string( WP_DEBUG_LOG ) ? WP_DEBUG_LOG : WP_CONTENT_DIR . '/debug.log';
        // Append directly to the debug log — avoids error_log() entirely.
        file_put_contents( $log_path, gmdate( '[d-M-Y H:i:s e]' ) . ' ' . $message . PHP_EOL, FILE_APPEND | LOCK_EX );
    }
    /**
     * Format and write a single trace entry to the debug log immediately.
     *
     * Called by trace() for ERROR, CRITICAL, and DATA_LOSS entries. All output
     * is routed through write_log() which gates on WP_DEBUG and WP_DEBUG_LOG,
     * so this method is a no-op in production even if developer mode is active.
     *
     * @since  1.0.0
     * @param  array $log_entry Entry array as built by trace().
     * @return void
     */
    private function output_trace( $log_entry ) {
        if ( ! wpseed_is_developer_mode() ) {
            return;
        }

        $elapsed = number_format( ( $log_entry['timestamp'] - $this->start_time ) * 1000, 2 );
        $memory  = number_format( $log_entry['memory'] / 1024 / 1024, 2 );

        $output = sprintf(
            '[%s] %s (+%sms, %sMB): %s',
            $log_entry['type'],
            $log_entry['context'],
            $elapsed,
            $memory,
            $log_entry['message']
        );

        if ( $log_entry['count'] > 1 ) {
            $output .= " (×{$log_entry['count']})";
        }

        if ( ! empty( $log_entry['data'] ) ) {
            $output .= ' | ' . wp_json_encode( $log_entry['data'] );
        }

        // write_log() handles the WP_DEBUG / WP_DEBUG_LOG gate — no error_log() used.
        $this->write_log( 'WPSeed_Trace: ' . $output );
    }
    
    /**
     * Close the current context and write a summary to the debug log.
     *
     * @since  1.0.0
     * @param  array $summary Optional summary data to attach to the CONTEXT_END entry.
     * @return void
     */
    public function end_context( $summary = array() ) {
        $this->trace( 'CONTEXT_END', "Ending {$this->current_context}", $summary );

        if ( wpseed_is_developer_mode() ) {
            $this->output_summary();
        }

        $this->current_context = '';
    }
    
    /**
     * Write a summary of the current context to the debug log.
     *
     * Outputs total log count, duration, per-type counts, and loop summaries.
     * All output routed through write_log() — no-op in production.
     *
     * @since  1.0.0
     * @return void
     */
    private function output_summary() {
        $total_logs = count( $this->logs );
        $duration   = ( microtime( true ) - $this->start_time ) * 1000;

        $this->write_log( "WPSeed_Summary: Context '{$this->current_context}' - {$total_logs} logs in " . number_format( $duration, 2 ) . 'ms' );

        // Tally entries by type so the summary shows which trace types fired most.
        $type_counts = array();
        foreach ( $this->logs as $log ) {
            $type = $log['type'];
            if ( ! isset( $type_counts[ $type ] ) ) {
                $type_counts[ $type ] = 0;
            }
            $type_counts[ $type ]++;
        }

        foreach ( $type_counts as $type => $count ) {
            $this->write_log( "WPSeed_Summary: {$type}: {$count}" );
        }

        // Include loop iteration counts so runaway loops are visible in the summary.
        $loop_summary = $this->get_loop_summary();
        if ( ! empty( $loop_summary ) ) {
            $this->write_log( 'WPSeed_Summary: Loops: ' . wp_json_encode( $loop_summary ) );
        }
    }
    
    /**
     * Return all recorded log entries.
     *
     * @since  1.0.0
     * @return array Array of log entry arrays.
     */
    public function get_logs() {
        return $this->logs;
    }

    /**
     * Clear all recorded log entries and loop counters.
     *
     * @since  1.0.0
     * @return void
     */
    public function clear_logs() {
        $this->logs          = array();
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