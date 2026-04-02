<?php
/**
 * Query Monitor-style request logger — tracks queries, hooks, HTTP requests, and errors.
 *
 * ROLE: utility
 *
 * Single responsibility: Capture per-request performance data (DB queries, hook
 * calls, HTTP requests, PHP errors) and persist it to a database table for the
 * developer tools UI. Only active when developer mode is enabled.
 * Does NOT handle structured trace logging — that is Core\Logger.
 *
 * DEPENDS ON:
 *   - WPSeed_Developer_Mode::is_dev_environment() in includes/classes/developer-mode.php
 *   - WPSeed\Core\Logger (optional — used for cross-referencing trace data)
 *   - WordPress globals: $wpdb
 *
 * CONSUMED BY:
 *   - admin/page/development/view/performance.php (reads logs and metrics)
 *   - Hook: shutdown (saves logs to DB)
 *
 * DATA FLOW:
 *   Input  → WordPress query, hook, HTTP, and error events during a request
 *   Output → {prefix}wpseed_debug_logs DB table
 *
 * @package  WPSeed\Core
 * @since    1.0.0
 */

namespace WPSeed\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Captures per-request performance data and persists to the debug_logs table.
 *
 * Single responsibility: Request-level performance monitoring. Does NOT handle
 * structured trace logging (that is Logger) or admin UI rendering.
 *
 * @since 1.0.0
 */
class Enhanced_Logger {

	/** @var Enhanced_Logger|null */
	private static $instance = null;

	/** @var array Captured DB queries. */
	private $queries = array();

	/** @var array<string, array{count: int, callbacks: array}> Hook call counts. */
	private $hooks = array();

	/** @var array Captured HTTP requests. */
	private $http_requests = array();

	/** @var array Captured PHP errors. */
	private $errors = array();

	/** @var float Request start time. */
	private $start_time;

	/** @var int Memory usage at start. */
	private $start_memory;

	/**
	 * @since  1.0.0
	 * @return Enhanced_Logger
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		if ( ! \WPSeed_Developer_Mode::is_dev_environment() ) {
			return;
		}

		$this->start_time   = microtime( true );
		$this->start_memory = memory_get_usage();

		add_filter( 'query',            array( $this, 'log_query' ) );
		add_action( 'all',              array( $this, 'log_hook' ) );
		add_filter( 'pre_http_request', array( $this, 'log_http_request' ), 10, 3 );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			add_action( 'init',     array( $this, 'register_error_handler' ), 1 );
			add_action( 'shutdown', array( $this, 'restore_error_handler' ), 9 );
		}

		add_action( 'shutdown', array( $this, 'save_logs' ) );
	}

	/**
	 * Register the custom PHP error handler.
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function register_error_handler() {
		set_error_handler( array( $this, 'log_error' ) );
	}

	/**
	 * Restore the previous PHP error handler.
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function restore_error_handler() {
		restore_error_handler();
	}

	/**
	 * Log a database query.
	 *
	 * @since  1.0.0
	 * @param  string $query SQL query string.
	 * @return string Unmodified query (filter passthrough).
	 */
	public function log_query( $query ) {
		global $wpdb;

		$query_hash = md5( $query );
		$query_type = strtoupper( strtok( trim( $query ), ' ' ) );

		$this->queries[] = array(
			'query'      => $query,
			'query_hash' => $query_hash,
			'query_type' => $query_type,
			'time'       => $wpdb->timer_stop(),
			'backtrace'  => wp_debug_backtrace_summary( null, 3 ),
			'timestamp'  => microtime( true ),
		);

		if ( class_exists( __NAMESPACE__ . '\\Logger' ) ) {
			Logger::instance()->trace( 'DB_QUERY', "{$query_type} query", array(
				'query_hash'     => $query_hash,
				'execution_time' => $wpdb->timer_stop(),
			) );
		}

		return $query;
	}

	/**
	 * Log a hook execution.
	 *
	 * @since  1.0.0
	 * @param  string $hook Hook name.
	 * @return string Unmodified hook (filter passthrough).
	 */
	public function log_hook( $hook ) {
		if ( ! isset( $this->hooks[ $hook ] ) ) {
			$this->hooks[ $hook ] = array( 'count' => 0, 'callbacks' => array() );
		}
		$this->hooks[ $hook ]['count']++;

		if ( class_exists( __NAMESPACE__ . '\\Logger' ) && $this->hooks[ $hook ]['count'] % 50 === 0 ) {
			Logger::instance()->trace( 'HOOK_FREQUENT', "Hook {$hook} called {$this->hooks[ $hook ]['count']} times" );
		}

		return $hook;
	}

	/**
	 * Log an HTTP request.
	 *
	 * @since  1.0.0
	 * @param  false|array|\WP_Error $response Response (passthrough).
	 * @param  array                 $args     Request arguments.
	 * @param  string                $url      Request URL.
	 * @return false|array|\WP_Error Unmodified response.
	 */
	public function log_http_request( $response, $args, $url ) {
		$this->http_requests[] = array(
			'url'    => $url,
			'method' => $args['method'] ?? 'GET',
			'start'  => microtime( true ),
			'args'   => $args,
		);
		return $response;
	}

	/**
	 * Log a PHP error.
	 *
	 * @since  1.0.0
	 * @param  int    $errno   Error level.
	 * @param  string $errstr  Error message.
	 * @param  string $errfile File where error occurred.
	 * @param  int    $errline Line where error occurred.
	 * @return false Always returns false to allow normal error handling.
	 */
	public function log_error( $errno, $errstr, $errfile, $errline ) {
		$this->errors[] = array(
			'type'      => $errno,
			'message'   => $errstr,
			'file'      => $errfile,
			'line'      => $errline,
			'timestamp' => microtime( true ),
		);
		return false;
	}

	/**
	 * Return query statistics with loop detection.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_query_stats() {
		$total_time     = 0;
		$slow_queries   = array();
		$query_patterns = array();

		foreach ( $this->queries as $query ) {
			$total_time += $query['time'];

			$hash = $query['query_hash'];
			if ( ! isset( $query_patterns[ $hash ] ) ) {
				$query_patterns[ $hash ] = array(
					'count'        => 0,
					'type'         => $query['query_type'],
					'total_time'   => 0,
					'sample_query' => substr( $query['query'], 0, 100 ),
				);
			}
			$query_patterns[ $hash ]['count']++;
			$query_patterns[ $hash ]['total_time'] += $query['time'];

			if ( $query['time'] > 0.05 ) {
				$slow_queries[] = $query;
			}
		}

		$repeated_queries = array_filter( $query_patterns, function ( $pattern ) {
			return $pattern['count'] > 5;
		} );

		return array(
			'total'            => count( $this->queries ),
			'total_time'       => $total_time,
			'slow_queries'     => $slow_queries,
			'repeated_queries' => $repeated_queries,
			'unique_patterns'  => count( $query_patterns ),
			'avg_time'         => count( $this->queries ) > 0 ? $total_time / count( $this->queries ) : 0,
		);
	}

	/**
	 * Return hook statistics.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_hook_stats() {
		$total       = 0;
		$most_called = array();

		foreach ( $this->hooks as $hook => $data ) {
			$total += $data['count'];
			$most_called[ $hook ] = $data['count'];
		}

		arsort( $most_called );

		return array(
			'total_hooks' => count( $this->hooks ),
			'total_calls' => $total,
			'most_called' => array_slice( $most_called, 0, 10, true ),
		);
	}

	/**
	 * Return performance metrics for the current request.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_performance_metrics() {
		return array(
			'execution_time' => microtime( true ) - $this->start_time,
			'memory_usage'   => memory_get_usage() - $this->start_memory,
			'peak_memory'    => memory_get_peak_usage(),
			'queries'        => count( $this->queries ),
			'http_requests'  => count( $this->http_requests ),
			'errors'         => count( $this->errors ),
		);
	}

	/**
	 * Save captured logs to the database on shutdown.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function save_logs() {
		global $wpdb;

		$table     = $wpdb->prefix . 'wpseed_debug_logs';
		$cache_key = 'wpseed_logger_table_exists';
		$exists    = wp_cache_get( $cache_key, 'wpseed_logger' );

		if ( false === $exists ) {
			$exists = ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table );
			wp_cache_set( $cache_key, $exists, 'wpseed_logger', 3600 );
		}

		if ( ! $exists ) {
			self::create_table();
		}

		$wpdb->insert( $table, array(
			'request_uri'    => isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '',
			'query_count'    => count( $this->queries ),
			'query_time'     => $this->get_query_stats()['total_time'],
			'hook_count'     => $this->get_hook_stats()['total_calls'],
			'http_count'     => count( $this->http_requests ),
			'error_count'    => count( $this->errors ),
			'execution_time' => microtime( true ) - $this->start_time,
			'memory_usage'   => memory_get_usage() - $this->start_memory,
			'created_at'     => current_time( 'mysql' ),
			'data'           => wp_json_encode( array(
				'queries' => $this->queries,
				'hooks'   => $this->hooks,
				'http'    => $this->http_requests,
				'errors'  => $this->errors,
			) ),
		) );
	}

	/**
	 * Create the debug_logs database table.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function create_table() {
		global $wpdb;

		$table   = $wpdb->prefix . 'wpseed_debug_logs';
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			request_uri varchar(255) NOT NULL,
			query_count int(11) DEFAULT 0,
			query_time float DEFAULT 0,
			hook_count int(11) DEFAULT 0,
			http_count int(11) DEFAULT 0,
			error_count int(11) DEFAULT 0,
			execution_time float DEFAULT 0,
			memory_usage bigint(20) DEFAULT 0,
			created_at datetime NOT NULL,
			data longtext,
			PRIMARY KEY (id),
			KEY created_at (created_at),
			KEY request_uri (request_uri)
		) $charset;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Get recent log entries.
	 *
	 * @since  1.0.0
	 * @param  int $limit Number of entries to return.
	 * @return array|object|null
	 */
	public static function get_recent_logs( $limit = 50 ) {
		global $wpdb;

		$table     = $wpdb->prefix . 'wpseed_debug_logs';
		$safe      = esc_sql( $table );
		$cache_key = 'wpseed_recent_logs_' . absint( $limit );
		$cached    = wp_cache_get( $cache_key, 'wpseed_logger' );

		if ( false !== $cached ) {
			return $cached;
		}

		$results = $wpdb->get_results( $wpdb->prepare(
			'SELECT * FROM `' . $safe . '` ORDER BY created_at DESC LIMIT %d',
			absint( $limit )
		) );

		wp_cache_set( $cache_key, $results, 'wpseed_logger', 60 );
		return $results;
	}

	/**
	 * Clear log entries older than the specified number of days.
	 *
	 * @since  1.0.0
	 * @param  int $days Number of days to retain.
	 * @return int|false Number of rows deleted or false on error.
	 */
	public static function clear_old_logs( $days = 7 ) {
		global $wpdb;

		$table  = $wpdb->prefix . 'wpseed_debug_logs';
		$safe   = esc_sql( $table );
		$result = $wpdb->query( $wpdb->prepare(
			'DELETE FROM `' . $safe . '` WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)',
			absint( $days )
		) );

		wp_cache_delete( 'wpseed_recent_logs_50', 'wpseed_logger' );
		wp_cache_delete( 'wpseed_logger_table_exists', 'wpseed_logger' );

		return $result;
	}
}

// Initialize only in dev environments.
if ( \WPSeed_Developer_Mode::is_dev_environment() ) {
	Enhanced_Logger::instance();
}

// Create table on activation.
register_activation_hook( WPSEED_PLUGIN_FILE, array( __NAMESPACE__ . '\\Enhanced_Logger', 'create_table' ) );
