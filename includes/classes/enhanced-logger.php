<?php
/**
 * WPSeed Enhanced Logger
 * Query Monitor-style logging and debugging.
 *
 * @package WPSeed
 * @version 2.0.0
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Enhanced_Logger {

    private static $instance = null;
    private $queries = array();
    private $hooks = array();
    private $http_requests = array();
    private $errors = array();
    private $start_time;
    private $start_memory;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        if ( ! WPSeed_Developer_Mode::is_dev_environment() ) {
            return;
        }

        $this->start_time   = microtime( true );
        $this->start_memory = memory_get_usage();

        add_filter( 'query',            array( $this, 'log_query' ) );
        add_action( 'all',              array( $this, 'log_hook' ) );
        add_filter( 'pre_http_request', array( $this, 'log_http_request' ), 10, 3 );

        // Register the error handler after WordPress is bootstrapped so it does
        // not interfere with core startup. Gated behind WP_DEBUG so it never
        // runs in production. restore_error_handler() is called on shutdown
        // (before save_logs) to limit the scope to a single request and avoid
        // affecting other plugins or WordPress error handling.
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            add_action( 'init',     array( $this, 'register_error_handler' ), 1 );
            add_action( 'shutdown', array( $this, 'restore_error_handler' ), 9 );
        }

        add_action( 'shutdown', array( $this, 'save_logs' ) );
    }

    /**
     * Register the custom PHP error handler.
     *
     * Called on the init hook (priority 1) so WordPress is fully bootstrapped
     * before the handler is installed. Only registered when WP_DEBUG is true.
     * The previous handler is stored so restore_error_handler() can reinstate
     * it cleanly on shutdown.
     *
     * @since  2.0.0
     * @return void
     */
    public function register_error_handler() {
        // set_error_handler() is intentionally used here: this is a dedicated
        // debug logger, gated behind WP_DEBUG, with a matching restore call on
        // shutdown (priority 9) to limit its scope to the current request only.
        set_error_handler( array( $this, 'log_error' ) );
    }

    /**
     * Restore the previous PHP error handler.
     *
     * Called on shutdown at priority 9, before save_logs() at default priority
     * 10, so the custom handler is active for the full request but removed
     * before WordPress and other plugins run their own shutdown routines.
     *
     * @since  2.0.0
     * @return void
     */
    public function restore_error_handler() {
        // Reinstate the handler that was active before register_error_handler()
        // ran, preventing the custom handler from leaking into shutdown hooks
        // registered by WordPress core or other plugins.
        restore_error_handler();
    }

    /**
     * Log database query with loop counting
     */
    public function log_query($query) {
        global $wpdb;
        
        $query_hash = md5($query);
        $query_type = strtoupper(strtok(trim($query), ' '));
        
        $this->queries[] = array(
            'query' => $query,
            'query_hash' => $query_hash,
            'query_type' => $query_type,
            'time' => $wpdb->timer_stop(),
            'backtrace' => wp_debug_backtrace_summary(null, 3),
            'timestamp' => microtime(true),
        );
        
        // Use unified logger for query tracking
        if (class_exists('WPSeed_Unified_Logger')) {
            WPSeed_Unified_Logger::instance()->trace('DB_QUERY', "{$query_type} query", array(
                'query_hash' => $query_hash,
                'execution_time' => $wpdb->timer_stop()
            ));
        }
        
        return $query;
    }

    /**
     * Log hook execution with smart counting
     */
    public function log_hook($hook) {
        if (!isset($this->hooks[$hook])) {
            $this->hooks[$hook] = array(
                'count' => 0,
                'callbacks' => array(),
            );
        }
        
        $this->hooks[$hook]['count']++;
        
        // Use unified logger for hook tracking (only log frequently called hooks)
        if (class_exists('WPSeed_Unified_Logger') && $this->hooks[$hook]['count'] % 50 === 0) {
            WPSeed_Unified_Logger::instance()->trace('HOOK_FREQUENT', "Hook {$hook} called {$this->hooks[$hook]['count']} times");
        }
        
        return $hook;
    }

    /**
     * Log HTTP request
     */
    public function log_http_request($response, $args, $url) {
        $start = microtime(true);
        
        $this->http_requests[] = array(
            'url' => $url,
            'method' => isset($args['method']) ? $args['method'] : 'GET',
            'start' => $start,
            'args' => $args,
        );
        
        return $response;
    }

    /**
     * Log PHP error
     */
    public function log_error($errno, $errstr, $errfile, $errline) {
        $this->errors[] = array(
            'type' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'timestamp' => microtime(true),
        );
        
        return false;
    }

    /**
     * Get enhanced query statistics with loop detection
     */
    public function get_query_stats() {
        $total_time = 0;
        $slow_queries = array();
        $query_patterns = array();
        
        foreach ($this->queries as $query) {
            $total_time += $query['time'];
            
            // Track query patterns for loop detection
            $hash = $query['query_hash'];
            if (!isset($query_patterns[$hash])) {
                $query_patterns[$hash] = array(
                    'count' => 0,
                    'type' => $query['query_type'],
                    'total_time' => 0,
                    'sample_query' => substr($query['query'], 0, 100)
                );
            }
            $query_patterns[$hash]['count']++;
            $query_patterns[$hash]['total_time'] += $query['time'];
            
            if ($query['time'] > 0.05) { // 50ms threshold
                $slow_queries[] = $query;
            }
        }
        
        // Find repeated queries (potential loops)
        $repeated_queries = array_filter($query_patterns, function($pattern) {
            return $pattern['count'] > 5;
        });
        
        return array(
            'total' => count($this->queries),
            'total_time' => $total_time,
            'slow_queries' => $slow_queries,
            'repeated_queries' => $repeated_queries,
            'unique_patterns' => count($query_patterns),
            'avg_time' => count($this->queries) > 0 ? $total_time / count($this->queries) : 0,
        );
    }

    /**
     * Get hook statistics
     */
    public function get_hook_stats() {
        $total = 0;
        $most_called = array();
        
        foreach ($this->hooks as $hook => $data) {
            $total += $data['count'];
            $most_called[$hook] = $data['count'];
        }
        
        arsort($most_called);
        
        return array(
            'total_hooks' => count($this->hooks),
            'total_calls' => $total,
            'most_called' => array_slice($most_called, 0, 10, true),
        );
    }

    /**
     * Get performance metrics
     */
    public function get_performance_metrics() {
        return array(
            'execution_time' => microtime(true) - $this->start_time,
            'memory_usage' => memory_get_usage() - $this->start_memory,
            'peak_memory' => memory_get_peak_usage(),
            'queries' => count($this->queries),
            'http_requests' => count($this->http_requests),
            'errors' => count($this->errors),
        );
    }

    /**
     * Save logs to database
     */
    public function save_logs() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'wpseed_debug_logs';
        
        // SHOW TABLES has no WP API equivalent; use $wpdb->prepare() for the value
        // and cache the result to avoid repeated database hits.
        $safe_table   = esc_sql( $table );
        $table_exists_cache_key = 'wpseed_logger_table_exists';
        $table_exists = wp_cache_get( $table_exists_cache_key, 'wpseed_logger' );
        if ( false === $table_exists ) {
            $table_exists = ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table );
            wp_cache_set( $table_exists_cache_key, $table_exists, 'wpseed_logger', 3600 );
        }
        if ( ! $table_exists ) {
            self::create_table();
        }

        $wpdb->insert( $table, array(
            // wp_unslash() applied to server variable per MissingUnslash standard.
            'request_uri'    => isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '',
            'query_count' => count($this->queries),
            'query_time' => $this->get_query_stats()['total_time'],
            'hook_count' => $this->get_hook_stats()['total_calls'],
            'http_count' => count($this->http_requests),
            'error_count' => count($this->errors),
            'execution_time' => microtime(true) - $this->start_time,
            'memory_usage' => memory_get_usage() - $this->start_memory,
            'created_at' => current_time('mysql'),
            'data' => json_encode(array(
                'queries' => $this->queries,
                'hooks' => $this->hooks,
                'http' => $this->http_requests,
                'errors' => $this->errors,
            )),
        ));
    }

    /**
     * Create database table
     */
    public static function create_table() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'wpseed_debug_logs';
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
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Get recent logs
     */
    public static function get_recent_logs( $limit = 50 ) {
        global $wpdb;

        $table     = $wpdb->prefix . 'wpseed_debug_logs';
        $safe_table = esc_sql( $table );
        $cache_key = 'wpseed_recent_logs_' . absint( $limit );
        $cached    = wp_cache_get( $cache_key, 'wpseed_logger' );

        if ( false !== $cached ) {
            return $cached;
        }

        // esc_sql() used for table identifier — %i requires WP 6.2+, plugin targets WP 4.4+.
        $results = $wpdb->get_results( $wpdb->prepare(
            'SELECT * FROM `' . $safe_table . '` ORDER BY created_at DESC LIMIT %d',
            absint( $limit )
        ) );

        wp_cache_set( $cache_key, $results, 'wpseed_logger', 60 );

        return $results;
    }

    /**
     * Clear old logs
     */
    public static function clear_old_logs( $days = 7 ) {
        global $wpdb;

        $table      = $wpdb->prefix . 'wpseed_debug_logs';
        $safe_table = esc_sql( $table );

        // esc_sql() used for table identifier — %i requires WP 6.2+, plugin targets WP 4.4+.
        $result = $wpdb->query( $wpdb->prepare(
            'DELETE FROM `' . $safe_table . '` WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)',
            absint( $days )
        ) );

        // Invalidate cached logs after deletion.
        wp_cache_delete( 'wpseed_recent_logs_50', 'wpseed_logger' );
        wp_cache_delete( 'wpseed_logger_table_exists', 'wpseed_logger' );

        return $result;
    }
}

// Initialize
if (WPSeed_Developer_Mode::is_dev_environment()) {
    WPSeed_Enhanced_Logger::instance();
}

// Create table on activation
register_activation_hook(WPSEED_PLUGIN_FILE, array('WPSeed_Enhanced_Logger', 'create_table'));
