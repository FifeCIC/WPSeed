<?php
/**
 * WPSeed Enhanced Logger
 * Query Monitor-style logging and debugging
 *
 * @package WPSeed
 * @version 1.2.0
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
        if (!WPSeed_Developer_Mode::is_dev_environment()) {
            return;
        }

        $this->start_time = microtime(true);
        $this->start_memory = memory_get_usage();

        add_filter('query', array($this, 'log_query'));
        add_action('all', array($this, 'log_hook'));
        add_filter('pre_http_request', array($this, 'log_http_request'), 10, 3);
        add_action('shutdown', array($this, 'save_logs'));
        
        set_error_handler(array($this, 'log_error'));
    }

    /**
     * Log database query
     */
    public function log_query($query) {
        global $wpdb;
        
        $this->queries[] = array(
            'query' => $query,
            'time' => $wpdb->timer_stop(),
            'backtrace' => wp_debug_backtrace_summary(null, 3),
            'timestamp' => microtime(true),
        );
        
        return $query;
    }

    /**
     * Log hook execution
     */
    public function log_hook($hook) {
        if (!isset($this->hooks[$hook])) {
            $this->hooks[$hook] = array(
                'count' => 0,
                'callbacks' => array(),
            );
        }
        
        $this->hooks[$hook]['count']++;
        
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
     * Get query statistics
     */
    public function get_query_stats() {
        $total_time = 0;
        $slow_queries = array();
        
        foreach ($this->queries as $query) {
            $total_time += $query['time'];
            
            if ($query['time'] > 0.05) { // 50ms threshold
                $slow_queries[] = $query;
            }
        }
        
        return array(
            'total' => count($this->queries),
            'total_time' => $total_time,
            'slow_queries' => $slow_queries,
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
        
        // Create table if it doesn't exist
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
            self::create_table();
        }
        
        $wpdb->insert($table, array(
            'request_uri' => $_SERVER['REQUEST_URI'],
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
    public static function get_recent_logs($limit = 50) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'wpseed_debug_logs';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table ORDER BY created_at DESC LIMIT %d",
            $limit
        ));
    }

    /**
     * Clear old logs
     */
    public static function clear_old_logs($days = 7) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'wpseed_debug_logs';
        
        return $wpdb->query($wpdb->prepare(
            "DELETE FROM $table WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ));
    }
}

// Initialize
if (WPSeed_Developer_Mode::is_dev_environment()) {
    WPSeed_Enhanced_Logger::instance();
}

// Create table on activation
register_activation_hook(WPSEED_PLUGIN_FILE, array('WPSeed_Enhanced_Logger', 'create_table'));
