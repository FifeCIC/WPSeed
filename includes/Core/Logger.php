<?php
/**
 * Structured trace logger with loop detection and data-loss tracking.
 *
 * ROLE: utility
 *
 * Single responsibility: Record structured trace entries during a request,
 * detect loops and data loss, and output summaries to the debug log. The JS
 * counterpart (inline script) is registered via the admin_footer hook.
 * Does NOT handle query logging or performance metrics — that is Enhanced_Logger.
 *
 * DEPENDS ON:
 *   - global wpseed_is_developer_mode() in includes/functions/core.php
 *
 * CONSUMED BY:
 *   - functions.php: wpseed_log() and wpseed_trace() global accessors
 *   - WPSeed_Enhanced_Logger (via class_exists guard — will update when migrated)
 *   - WPSeed_Verification_Logger (via class_exists guard)
 *
 * DATA FLOW:
 *   Input  → trace() calls from any code path
 *   Output → debug.log via write_log() when WP_DEBUG + WP_DEBUG_LOG enabled
 *
 * @package  WPSeed\Core
 * @since    1.0.0
 */

namespace WPSeed\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Structured trace logger with loop counting and data-loss detection.
 *
 * Single responsibility: In-memory trace recording and debug log output.
 * Does NOT handle query monitoring or HTTP request logging.
 *
 * @since 1.0.0
 */
class Logger {

	/** @var Logger|null */
	private static $instance = null;

	/** @var array Recorded log entries. */
	private $logs = array();

	/** @var array<string, int> Occurrence counters keyed by type:message. */
	private $loop_counters = array();

	/** @var string Current logging context label. */
	private $current_context = '';

	/** @var float Request start time. */
	private $start_time;

	/**
	 * @since  1.0.0
	 * @return Logger
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->start_time = microtime( true );
	}

	/**
	 * Start a new logging context.
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
	 * Record a trace entry.
	 *
	 * ERROR, CRITICAL, and DATA_LOSS entries are written to the debug log
	 * immediately. All others are stored in memory only.
	 *
	 * @since  1.0.0
	 * @param  string $type    Trace type (e.g. 'ERROR', 'LOOP_START').
	 * @param  string $message Human-readable description.
	 * @param  array  $data    Optional structured data.
	 * @return void
	 */
	public function trace( $type, $message, $data = array() ) {
		if ( ! wpseed_is_developer_mode() ) {
			return;
		}

		$trace_key = $type . ':' . $message;

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

		if ( in_array( $type, array( 'ERROR', 'CRITICAL', 'DATA_LOSS' ), true ) ) {
			$this->output_trace( $log_entry );
		}
	}

	/**
	 * Log a JavaScript data-transmission operation.
	 *
	 * @since  1.0.0
	 * @param  string $operation  Label.
	 * @param  int    $data_count Number of items.
	 * @param  array  $details    Optional context.
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
	 * @since  1.0.0
	 * @param  string $operation    Label.
	 * @param  int    $input_count  Items entering.
	 * @param  int    $output_count Items leaving.
	 * @param  array  $details      Optional context.
	 * @return void
	 */
	public function php_trace( $operation, $input_count, $output_count, $details = array() ) {
		$this->trace( 'PHP_TRACE', "{$operation}: {$input_count} → {$output_count}", array_merge( $details, array(
			'operation'    => $operation,
			'input_count'  => $input_count,
			'output_count' => $output_count,
			'data_loss'    => $input_count > $output_count,
		) ) );

		if ( $input_count > $output_count ) {
			$this->trace( 'DATA_LOSS', "Potential data loss: {$input_count} → {$output_count}", $details );
		}
	}

	/**
	 * Record a loop iteration, throttled to every 10th call.
	 *
	 * @since  1.0.0
	 * @param  string $loop_id        Unique loop identifier.
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

		if ( $this->loop_counters[ $loop_key ] % 10 === 0 || ! empty( $iteration_data['force_log'] ) ) {
			$this->trace( 'LOOP_ITERATION', "Loop {$loop_id} iteration {$this->loop_counters[ $loop_key ]}", $iteration_data );
		}
	}

	/**
	 * Record a loop end with total iteration count.
	 *
	 * @since  1.0.0
	 * @param  string $loop_id Unique loop identifier.
	 * @param  array  $summary Optional summary data.
	 * @return void
	 */
	public function loop_end( $loop_id, $summary = array() ) {
		$loop_key         = "LOOP:{$loop_id}";
		$total_iterations = $this->loop_counters[ $loop_key ] ?? 0;
		$this->trace( 'LOOP_END', "Loop {$loop_id} completed: {$total_iterations} iterations", $summary );
	}

	/**
	 * Return loop iteration counts keyed by loop ID.
	 *
	 * @since  1.0.0
	 * @return array<string, int>
	 */
	public function get_loop_summary() {
		$summary = array();
		foreach ( $this->loop_counters as $key => $count ) {
			if ( strpos( $key, 'LOOP:' ) === 0 ) {
				$summary[ substr( $key, 5 ) ] = $count;
			}
		}
		return $summary;
	}

	/**
	 * Close the current context and write a summary to the debug log.
	 *
	 * @since  1.0.0
	 * @param  array $summary Optional summary data.
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
	 * Return all recorded log entries.
	 *
	 * @since  1.0.0
	 * @return array
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

	/**
	 * Write a message to the WordPress debug log.
	 *
	 * @since  2.0.0
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
		$log_path = is_string( WP_DEBUG_LOG ) ? WP_DEBUG_LOG : WP_CONTENT_DIR . '/debug.log';
		file_put_contents( $log_path, gmdate( '[d-M-Y H:i:s e]' ) . ' ' . $message . PHP_EOL, FILE_APPEND | LOCK_EX );
	}

	/**
	 * Format and write a single trace entry to the debug log.
	 *
	 * @since  1.0.0
	 * @param  array $log_entry Entry array from trace().
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

		$this->write_log( 'WPSeed_Trace: ' . $output );
	}

	/**
	 * Write a context summary to the debug log.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	private function output_summary() {
		$total_logs = count( $this->logs );
		$duration   = ( microtime( true ) - $this->start_time ) * 1000;

		$this->write_log( "WPSeed_Summary: Context '{$this->current_context}' - {$total_logs} logs in " . number_format( $duration, 2 ) . 'ms' );

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

		$loop_summary = $this->get_loop_summary();
		if ( ! empty( $loop_summary ) ) {
			$this->write_log( 'WPSeed_Summary: Loops: ' . wp_json_encode( $loop_summary ) );
		}
	}
}

// Register the JavaScript logging helper in admin footer.
add_action( 'admin_footer', function () {
	if ( ! wpseed_is_developer_mode() ) {
		return;
	}
	?>
	<script>
	window.WPSeedLogger = {
		context: '',
		loopCounters: {},
		startContext: function(context) {
			this.context = context;
			this.loopCounters = {};
			console.log('WPSeed_JS: CONTEXT_START - ' + context);
		},
		trace: function(type, message, data) {
			data = data || {};
			var timestamp = performance.now();
			var logKey = type + ':' + message;
			if (!this.loopCounters[logKey]) { this.loopCounters[logKey] = 0; }
			this.loopCounters[logKey]++;
			var output = 'WPSeed_JS: [' + type + '] ' + this.context + ' (+' + timestamp.toFixed(2) + 'ms): ' + message;
			if (this.loopCounters[logKey] > 1) { output += ' (x' + this.loopCounters[logKey] + ')'; }
			console.log(output);
			if (Object.keys(data).length > 0) { console.log('WPSeed_JS_Data:', data); }
		},
		loopTrace: function(loopId, iterationData) {
			iterationData = iterationData || {};
			var loopKey = 'LOOP:' + loopId;
			if (!this.loopCounters[loopKey]) { this.loopCounters[loopKey] = 0; this.trace('LOOP_START', 'Starting loop: ' + loopId); }
			this.loopCounters[loopKey]++;
			if (this.loopCounters[loopKey] % 10 === 0 || iterationData.forceLog) { this.trace('LOOP_ITERATION', 'Loop ' + loopId + ' iteration ' + this.loopCounters[loopKey], iterationData); }
		},
		loopEnd: function(loopId, summary) {
			summary = summary || {};
			var loopKey = 'LOOP:' + loopId;
			var totalIterations = this.loopCounters[loopKey] || 0;
			this.trace('LOOP_END', 'Loop ' + loopId + ' completed: ' + totalIterations + ' iterations', summary);
		},
		endContext: function(summary) {
			summary = summary || {};
			this.trace('CONTEXT_END', 'Ending ' + this.context, summary);
			var loopSummary = {};
			var self = this;
			Object.keys(this.loopCounters).forEach(function(key) { if (key.indexOf('LOOP:') === 0) { loopSummary[key.substring(5)] = self.loopCounters[key]; } });
			if (Object.keys(loopSummary).length > 0) { console.log('WPSeed_JS_Summary: Loops:', loopSummary); }
			this.context = '';
		}
	};
	</script>
	<?php
} );
