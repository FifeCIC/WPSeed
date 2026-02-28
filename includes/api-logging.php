<?php
/**
 * WPSeed API Logging
 *
 * Database-driven logging system for API activity, errors, and usage tracking
 * 
 * @package WPSeed
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_API_Logging {
    
    /**
     * Check if logging is ready
     */
    public static function ready() {
        $logging_enabled = get_option('wpseed_api_logging_enabled', 'yes');
        
        if ($logging_enabled !== 'yes') {
            return false;
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'wpseed_api_calls';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching -- Table existence check, caching not applicable
        return $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table;
    }
    
    /**
     * Log an API call
     */
    public static function log_call($service, $function, $type = 'GET', $status = 'pending', $file = '', $line = '', $description = '', $outcome = '') {
        if (!self::ready()) {
            return 0;
        }
        
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        if (empty($file) || empty($line)) {
            // Only use backtrace in debug mode to avoid production warnings
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace -- Debug mode only, not used in production
                $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
                $file = $backtrace[0]['file'] ?? '';
                $line = $backtrace[0]['line'] ?? '';
            } else {
                // Fallback values for production
                $file = empty($file) ? 'unknown' : $file;
                $line = empty($line) ? 0 : $line;
            }
        }
        
        $data = array(
            'service'     => sanitize_text_field($service),
            'type'        => sanitize_text_field($type),
            'status'      => sanitize_text_field($status),
            'file'        => sanitize_text_field($file),
            'function'    => sanitize_text_field($function),
            'line'        => intval($line),
            'wpuserid'    => $user_id,
            'timestamp'   => current_time('mysql'),
            'description' => $description,
            'outcome'     => $outcome,
        );
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table for API logging, no WP equivalent
        $result = $wpdb->insert(
            $wpdb->prefix . 'wpseed_api_calls',
            $data,
            array('%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s')
        );
        
        return $result ? $wpdb->insert_id : 0;
    }
    
    /**
     * Track endpoint usage
     */
    public static function track_endpoint($entry_id, $service, $endpoint, $parameters = array()) {
        if (!self::ready()) {
            return false;
        }
        
        global $wpdb;
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table query, no WP equivalent
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wpseed_api_endpoints WHERE service = %s AND endpoint = %s",
                $service,
                $endpoint
            )
        );
        
        $params_json = json_encode($parameters);
        
        if ($existing) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table update, no WP equivalent
            $result = $wpdb->update(
                $wpdb->prefix . 'wpseed_api_endpoints',
                array(
                    'entryid'    => $entry_id,
                    'lastuse'    => current_time('mysql'),
                    'counter'    => $existing->counter + 1,
                    'parameters' => $params_json,
                ),
                array('endpointid' => $existing->endpointid),
                array('%d', '%s', '%d', '%s'),
                array('%d')
            );
            
            return $result ? $existing->endpointid : false;
        } else {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table insert, no WP equivalent
            $result = $wpdb->insert(
                $wpdb->prefix . 'wpseed_api_endpoints',
                array(
                    'entryid'    => $entry_id,
                    'service'    => $service,
                    'endpoint'   => $endpoint,
                    'parameters' => $params_json,
                    'firstuse'   => current_time('mysql'),
                    'lastuse'    => current_time('mysql'),
                    'counter'    => 1,
                ),
                array('%d', '%s', '%s', '%s', '%s', '%s', '%d')
            );
            
            return $result ? $wpdb->insert_id : false;
        }
    }
    
    /**
     * Log an error
     */
    public static function log_error($entry_id, $code, $error, $function = '', $file = '', $line = 0) {
        if (!self::ready()) {
            return false;
        }
        
        global $wpdb;
        
        if (empty($file) || empty($function) || empty($line)) {
            // Only use backtrace in debug mode to avoid production warnings
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace -- Debug mode only, not used in production
                $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
                $file = empty($file) ? ($backtrace[0]['file'] ?? '') : $file;
                $function = empty($function) ? ($backtrace[0]['function'] ?? '') : $function;
                $line = empty($line) ? ($backtrace[0]['line'] ?? 0) : $line;
            } else {
                // Fallback values for production
                $file = empty($file) ? 'unknown' : $file;
                $function = empty($function) ? 'unknown' : $function;
                $line = empty($line) ? 0 : $line;
            }
        }
        
        if ($entry_id) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table update, no WP equivalent
            $wpdb->update(
                $wpdb->prefix . 'wpseed_api_calls',
                array('status' => 'error'),
                array('entryid' => $entry_id),
                array('%s'),
                array('%d')
            );
        }
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table insert, no WP equivalent, caching not applicable for insert operations
        $result = $wpdb->insert(
            $wpdb->prefix . 'wpseed_api_errors',
            array(
                'entryid'   => $entry_id,
                'code'      => sanitize_text_field($code),
                'error'     => sanitize_text_field($error),
                'line'      => intval($line),
                'function'  => sanitize_text_field($function),
                'file'      => sanitize_text_field($file),
                'timestamp' => current_time('mysql'),
            ),
            array('%d', '%s', '%s', '%d', '%s', '%s', '%s')
        );
        
        return $result ? $wpdb->insert_id : false;
    }
    
    /**
     * Update call outcome
     */
    public static function update_call_outcome($entry_id, $outcome, $status = 'complete') {
        if (!self::ready()) {
            return false;
        }
        
        global $wpdb;
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table update, no WP equivalent
        $result = $wpdb->update(
            $wpdb->prefix . 'wpseed_api_calls',
            array(
                'outcome' => sanitize_text_field($outcome),
                'status'  => sanitize_text_field($status),
            ),
            array('entryid' => $entry_id),
            array('%s', '%s'),
            array('%d')
        );
        
        return $result !== false;
    }

    /**
     * Get API calls
     */
    public static function get_api_calls($args = array()) {
        if (!self::ready()) {
            return array();
        }
        
        global $wpdb;
        
        $defaults = array(
            'service'    => '',
            'status'     => '',
            'type'       => '',
            'limit'      => 50,
            'offset'     => 0,
            'orderby'    => 'timestamp',
            'order'      => 'DESC',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $query = "SELECT * FROM {$wpdb->prefix}wpseed_api_calls WHERE 1=1";
        $params = array();
        
        if (!empty($args['service'])) {
            $query .= " AND service = %s";
            $params[] = $args['service'];
        }
        
        if (!empty($args['status'])) {
            $query .= " AND status = %s";
            $params[] = $args['status'];
        }
        
        if (!empty($args['type'])) {
            $query .= " AND type = %s";
            $params[] = $args['type'];
        }
        
        $allowed_columns = array('entryid', 'service', 'type', 'status', 'function', 'timestamp');
        $orderby = in_array($args['orderby'], $allowed_columns) ? $args['orderby'] : 'timestamp';
        $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';
        
        $query .= $wpdb->prepare(" ORDER BY `%1s` %2s", $orderby, $order);
        $query .= " LIMIT %d OFFSET %d";
        $params[] = absint($args['limit']);
        $params[] = absint($args['offset']);
        
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Query is prepared with $wpdb->prepare() below, custom table query with no WP equivalent
        $results = $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A);
        
        return $results;
    }

    /**
     * Get API call count
     */
    public static function get_api_call_count($args = array()) {
        if (!self::ready()) {
            return 0;
        }
        
        global $wpdb;
        
        $defaults = array(
            'service' => '',
            'status'  => '',
            'type'    => '',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $query = "SELECT COUNT(*) FROM {$wpdb->prefix}wpseed_api_calls WHERE 1=1";
        $params = array();
        
        if (!empty($args['service'])) {
            $query .= " AND service = %s";
            $params[] = $args['service'];
        }
        
        if (!empty($args['status'])) {
            $query .= " AND status = %s";
            $params[] = $args['status'];
        }
        
        if (!empty($args['type'])) {
            $query .= " AND type = %s";
            $params[] = $args['type'];
        }
        
        // Prepare query only if we have parameters to bind
        if (!empty($params)) {
            $prepared_query = $wpdb->prepare($query, $params);
        } else {
            $prepared_query = $query;
        }
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table query, no WP equivalent
        $count = $wpdb->get_var($prepared_query);
        
        return (int) $count;
    }
}
