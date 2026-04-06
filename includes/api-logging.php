<?php
/**
 * WPSeed API Logging
 *
 * Database-driven logging system for API activity, errors, and usage tracking.
 *
 * @package WPSeed
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_API_Logging {
    
    /**
     * Check whether the API logging table exists and logging is enabled.
     *
     * The SHOW TABLES query is cached for one hour so it does not run on every
     * page load. Direct query is necessary — no WordPress API equivalent exists
     * for checking whether a custom table is present. $wpdb->prepare() is used
     * for the dynamic table name value.
     *
     * @since  2.0.0
     * @return bool True when logging is enabled and the table exists.
     */
    public static function ready() {
        $logging_enabled = get_option( 'wpseed_api_logging_enabled', 'yes' );

        if ( $logging_enabled !== 'yes' ) {
            return false;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'wpseed_api_calls';

        // Cache the table-existence result to avoid a direct query on every request.
        $cache_key    = 'wpseed_api_table_exists_' . $table;
        $table_exists = wp_cache_get( $cache_key, 'wpseed_api' );

        if ( false === $table_exists ) {
            // SHOW TABLES on a custom table — no WP API equivalent.
            $table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table;
            // Cache for one hour; invalidated on plugin activation when the table is created.
            wp_cache_set( $cache_key, $table_exists, 'wpseed_api', HOUR_IN_SECONDS );
        }

        return (bool) $table_exists;
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
        
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wpseed_api_endpoints WHERE service = %s AND endpoint = %s",
                $service,
                $endpoint
            )
        );
        
        $params_json = json_encode($parameters);
        
        if ($existing) {
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
            $wpdb->update(
                $wpdb->prefix . 'wpseed_api_calls',
                array('status' => 'error'),
                array('entryid' => $entry_id),
                array('%s'),
                array('%d')
            );
        }
        
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
     *
     * @since   1.0.0
     * @version 1.2.0
     *
     * @param array $args Query arguments.
     * @return array
     */
    public static function get_api_calls( $args = array() ) {
        if ( ! self::ready() ) {
            return array();
        }

        global $wpdb;

        $defaults = array(
            'service' => '',
            'status'  => '',
            'type'    => '',
            'limit'   => 50,
            'offset'  => 0,
            'orderby' => 'timestamp',
            'order'   => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );

        $cache_key = 'wpseed_api_calls_' . md5( serialize( $args ) );
        $cached    = wp_cache_get( $cache_key, 'wpseed_api' );

        if ( false !== $cached ) {
            return $cached;
        }

        // esc_sql() used for table/column identifiers — %i requires WP 6.2+
        // and this plugin targets WP 4.4+.
        $safe_table = esc_sql( $wpdb->prefix . 'wpseed_api_calls' );

        $allowed_columns = array( 'entryid', 'service', 'type', 'status', 'function', 'timestamp' );
        $orderby         = in_array( $args['orderby'], $allowed_columns, true ) ? $args['orderby'] : 'timestamp';
        $order           = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';
        $safe_orderby    = esc_sql( $orderby );

        $where  = 'WHERE 1=1';
        $params = array();

        if ( ! empty( $args['service'] ) ) {
            $where   .= ' AND service = %s';
            $params[] = $args['service'];
        }

        if ( ! empty( $args['status'] ) ) {
            $where   .= ' AND `status` = %s';
            $params[] = $args['status'];
        }

        if ( ! empty( $args['type'] ) ) {
            $where   .= ' AND `type` = %s';
            $params[] = $args['type'];
        }

        $params[] = absint( $args['limit'] );
        $params[] = absint( $args['offset'] );

        $sql = $wpdb->prepare(
            'SELECT * FROM `' . $safe_table . '` ' . $where . ' ORDER BY `' . $safe_orderby . '` ' . $order . ' LIMIT %d OFFSET %d',
            $params
        );

        $results = $wpdb->get_results( $sql, ARRAY_A );

        wp_cache_set( $cache_key, $results, 'wpseed_api', 60 );

        return $results;
    }

    /**
     * Get API call count
     *
     * @since   1.0.0
     * @version 1.2.0
     *
     * @param array $args Query arguments.
     * @return int
     */
    public static function get_api_call_count( $args = array() ) {
        if ( ! self::ready() ) {
            return 0;
        }

        global $wpdb;

        $defaults = array(
            'service' => '',
            'status'  => '',
            'type'    => '',
        );

        $args = wp_parse_args( $args, $defaults );

        $cache_key = 'wpseed_api_count_' . md5( serialize( $args ) );
        $cached    = wp_cache_get( $cache_key, 'wpseed_api' );

        if ( false !== $cached ) {
            return (int) $cached;
        }

        // esc_sql() used for table identifier — %i requires WP 6.2+.
        $safe_table = esc_sql( $wpdb->prefix . 'wpseed_api_calls' );

        $where  = 'WHERE 1=1';
        $params = array();

        if ( ! empty( $args['service'] ) ) {
            $where   .= ' AND service = %s';
            $params[] = $args['service'];
        }

        if ( ! empty( $args['status'] ) ) {
            $where   .= ' AND `status` = %s';
            $params[] = $args['status'];
        }

        if ( ! empty( $args['type'] ) ) {
            $where   .= ' AND `type` = %s';
            $params[] = $args['type'];
        }

        if ( ! empty( $params ) ) {
            $count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM `' . $safe_table . '` ' . $where, $params ) );
        } else {
            $count = $wpdb->get_var( 'SELECT COUNT(*) FROM `' . $safe_table . '`' );
        }

        wp_cache_set( $cache_key, $count, 'wpseed_api', 60 );

        return (int) $count;
    }
}
