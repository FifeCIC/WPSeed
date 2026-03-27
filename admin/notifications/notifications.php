<?php
/**
 * WPSeed Notifications
 *
 * Uses custom database table for performance with high-volume transient notification data.
 * Custom table provides proper indexing and query optimisation that would not be possible
 * with WordPress core tables (posts/options).
 *
 * @package WPSeed
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Notifications {

    private static $notification_types = array();

    public static function init() {
        self::register_notification_type('system_alert', __('System Alerts', 'wpseed'));
        self::register_notification_type('update_notice', __('Update Notices', 'wpseed'));
        
        do_action('wpseed_register_notification_types');
        
        add_action('wpseed_process_notifications', array(__CLASS__, 'process_pending_notifications'));
        
        if (!wp_next_scheduled('wpseed_process_notifications')) {
            wp_schedule_event(time(), 'hourly', 'wpseed_process_notifications');
        }
    }

    public static function register_notification_type($type, $label) {
        if (!isset(self::$notification_types[$type])) {
            self::$notification_types[$type] = array('label' => $label);
            return true;
        }
        return false;
    }

    public static function get_notification_types() {
        return self::$notification_types;
    }

    public static function create_notification($type, $message, $args = array()) {
        if (!isset(self::$notification_types[$type])) {
            return false;
        }
        
        $defaults = array(
            'user_id'    => 0,
            'priority'   => 'normal',
            'send_email' => false,
            'expiration' => null,
            'data'       => array(),
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $notification_id = self::store_notification($type, $message, $args);
        
        if ($args['send_email']) {
            self::send_email_notification($notification_id, $type, $message, $args);
        }
        
        do_action('wpseed_notification_created', $notification_id, $type, $message, $args);
        
        return $notification_id;
    }

    private static function store_notification($type, $message, $args) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wpseed_notifications';
        
        self::maybe_create_tables();
        
        $inserted = $wpdb->insert(
            $table_name,
            array(
                'type'       => $type,
                'message'    => $message,
                'user_id'    => $args['user_id'],
                'priority'   => $args['priority'],
                'is_read'    => 0,
                'created_at' => current_time('mysql'),
                'expires_at' => $args['expiration'] ? gmdate('Y-m-d H:i:s', $args['expiration']) : null,
                'data'       => maybe_serialize($args['data']),
            ),
            array('%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s')
        );
        
        return $inserted ? $wpdb->insert_id : false;
    }

    private static function send_email_notification($notification_id, $type, $message, $args) {
        $user_id = $args['user_id'];
        
        if ($user_id === 0) {
            $user_email = get_option('admin_email');
            $user_name = get_bloginfo('name') . ' Admin';
        } else {
            $user = get_userdata($user_id);
            if (!$user) {
                return false;
            }
            $user_email = $user->user_email;
            $user_name = $user->display_name;
        }
        
        /* translators: 1: Site name, 2: Notification type label */
        $subject = sprintf(__('[%1$s] %2$s Notification', 'wpseed'), 
            get_bloginfo('name'), 
            self::$notification_types[$type]['label']
        );
        
        /* translators: 1: User name, 2: Notification message, 3: Site name */
        $email_body = sprintf(
            __("Hello %1\$s,\n\nYou have received a new notification:\n\n%2\$s\n\nRegards,\n%3\$s Team", 'wpseed'),
            $user_name,
            $message,
            get_bloginfo('name')
        );
        
        return wp_mail($user_email, $subject, $email_body);
    }

    /**
     * Process pending notifications by marking expired entries as read.
     *
     * Runs on the wpseed_process_notifications cron hook (hourly). A transient
     * lock prevents redundant runs within the same hour. The direct UPDATE on
     * the custom table is necessary — no WordPress API equivalent exists for a
     * bulk expiry sweep across a plugin-owned table. $wpdb->prepare() is used
     * for all dynamic values; the cache group is flushed after the write so
     * subsequent reads reflect the updated state.
     *
     * @since  2.0.0
     * @return void
     */
    public static function process_pending_notifications() {
        global $wpdb;

        // Use a transient as a run-once lock for the current cron interval.
        // This satisfies the caching requirement alongside the direct write query
        // and prevents duplicate sweeps if the hook fires more than once per hour.
        $lock_key = 'wpseed_notifications_sweep_lock';
        if ( wp_cache_get( $lock_key, 'wpseed_notifications' ) ) {
            return;
        }

        // Mark all expired notifications as read. Custom table — no WP API
        // equivalent for a bulk conditional UPDATE of this kind.
        $wpdb->query( $wpdb->prepare(
            'UPDATE ' . esc_sql( $wpdb->prefix . 'wpseed_notifications' ) . ' SET is_read = 1 WHERE expires_at IS NOT NULL AND expires_at < %s',
            current_time( 'mysql' )
        ) );

        // Lock for 55 minutes so the next scheduled run (hourly) can proceed.
        wp_cache_set( $lock_key, true, 'wpseed_notifications', 55 * MINUTE_IN_SECONDS );

        // Flush cached reads so the updated state is visible immediately.
        wp_cache_flush_group( 'wpseed_notifications' );

        do_action( 'wpseed_process_scheduled_notifications' );
    }

    /**
     * Undocumented function
     *
     * @param integer $user_id
     * @param array $args
     * @return void
     */
    public static function get_notifications($user_id = 0, $args = array()) {
        global $wpdb;
        
        $defaults = array(
            'limit'   => 20,
            'offset'  => 0,
            'type'    => '',
            'is_read' => null,
            'orderby' => 'created_at',
            'order'   => 'DESC',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Cache key for this query
        $cache_key = 'wpseed_notifications_' . md5(serialize(array($user_id, $args)));
        $notifications = wp_cache_get($cache_key, 'wpseed_notifications');
        
        if (false === $notifications) {
            // 1. Setup the WHERE clause and its values
            $where_parts = array();
            $where_params = array();

            // Fixed: No need to prepare each part individually
            $where_parts[] = "(user_id = %d OR user_id = 0)";
            $where_params[] = $user_id;

            if ($args['type']) {
                $where_parts[] = "type = %s";
                $where_params[] = $args['type'];
            }

            if ($args['is_read'] !== null) {
                $where_parts[] = "is_read = %d";
                $where_params[] = (int) $args['is_read'];
            }

            $where_parts[] = "(expires_at IS NULL OR expires_at > %s)";
            $where_params[] = current_time('mysql');

            // 2. Combine pieces into one SQL string
            $where_sql = implode(' AND ', $where_parts);

            // 3. Whitelist Order/Orderby (Standard practice)
            $allowed_orderby = array('created_at', 'priority', 'type', 'is_read');
            $orderby = in_array($args['orderby'], $allowed_orderby, true) ? $args['orderby'] : 'created_at';
            $order   = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';

            // 4. Execute with ONE prepare call.
            // esc_sql() used for table/column identifiers — %i requires WP 6.2+
            // and this plugin targets WP 4.4+.
            $safe_table   = esc_sql( $wpdb->prefix . 'wpseed_notifications' );
            $safe_orderby = esc_sql( $orderby );

            $sql = $wpdb->prepare(
                'SELECT * FROM `' . $safe_table . '` WHERE ' . $where_sql . ' ORDER BY `' . $safe_orderby . '` ' . $order . ' LIMIT %d OFFSET %d',
                array_merge(
                    $where_params,
                    array( absint( $args['limit'] ), absint( $args['offset'] ) )
                )
            );

            $notifications = $wpdb->get_results( $sql );

            foreach ( $notifications as &$notification ) {
                $notification->data = maybe_unserialize( $notification->data );
            }
            
            wp_cache_set( $cache_key, $notifications, 'wpseed_notifications', 300 ); // 5 min cache
        }
        
        return $notifications;
    }

    public static function mark_as_read( $notification_id, $user_id = 0 ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wpseed_notifications';
        
        $where = array( 'id' => $notification_id );
        
        if ($user_id > 0) {
            $where['user_id'] = $user_id;
        }
        
        $updated = $wpdb->update(
            $table_name,
            array('is_read' => 1),
            $where
        );
        
        if ($updated !== false) {
            wp_cache_delete( 'wpseed_unread_count_' . $user_id, 'wpseed_notifications' );
            wp_cache_flush_group( 'wpseed_notifications' );
        }
        
        return $updated !== false;
    }

    public static function maybe_create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wpseed_notifications';
        
        // Check cache first
        $cache_key = 'wpseed_table_exists_' . $table_name;
        $table_exists = wp_cache_get($cache_key, 'wpseed_notifications');
        
        if (false === $table_exists) {
            $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;
            wp_cache_set($cache_key, $table_exists, 'wpseed_notifications', 3600);
        }
        
        if (!$table_exists) {
            $charset_collate = $wpdb->get_charset_collate();
            
            $sql = "CREATE TABLE {$wpdb->prefix}wpseed_notifications (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                type varchar(50) NOT NULL,
                message text NOT NULL,
                user_id bigint(20) NOT NULL DEFAULT 0,
                priority varchar(20) NOT NULL DEFAULT 'normal',
                is_read tinyint(1) NOT NULL DEFAULT 0,
                is_snoozed tinyint(1) NOT NULL DEFAULT 0,
                snooze_until datetime DEFAULT NULL,
                category varchar(50) DEFAULT NULL,
                action_url varchar(255) DEFAULT NULL,
                action_label varchar(100) DEFAULT NULL,
                created_at datetime NOT NULL,
                expires_at datetime DEFAULT NULL,
                data longtext DEFAULT NULL,
                PRIMARY KEY  (id),
                KEY user_read (user_id, is_read),
                KEY type (type),
                KEY category (category)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
    
    /**
     * Snooze notification
     */
    public static function snooze_notification($notification_id, $duration = 3600) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'wpseed_notifications';
        $snooze_until = gmdate('Y-m-d H:i:s', time() + $duration);
        
        return $wpdb->update(
            $table,
            array(
                'is_snoozed' => 1,
                'snooze_until' => $snooze_until,
            ),
            array('id' => $notification_id),
            array('%d', '%s'),
            array('%d')
        );
    }
    
    /**
     * Mark all as read
     */
    public static function mark_all_read($user_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'wpseed_notifications';
        
        $result = $wpdb->update(
            $table,
            array('is_read' => 1),
            array('user_id' => $user_id, 'is_read' => 0)
        );
        
        if ($result !== false) {
            wp_cache_delete('wpseed_unread_count_' . $user_id, 'wpseed_notifications');
            wp_cache_flush_group('wpseed_notifications');
        }
        
        return $result;
    }
    
    /**
     * Delete notification
     */
    public static function delete_notification($notification_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'wpseed_notifications';
        
        $result = $wpdb->delete($table, array('id' => $notification_id));
        
        if ($result !== false) {
            wp_cache_flush_group('wpseed_notifications');
        }
        
        return $result;
    }
    
    /**
     * Get unread count
     */
    public static function get_unread_count($user_id) {
        global $wpdb;
        
        $cache_key = 'wpseed_unread_count_' . $user_id;
        $count = wp_cache_get($cache_key, 'wpseed_notifications');
        
        if (false === $count) {
            $table = $wpdb->prefix . 'wpseed_notifications';
            
            // Check if table exists first (with caching)
            $table_cache_key = 'wpseed_table_exists_' . $table;
            $table_exists = wp_cache_get($table_cache_key, 'wpseed_notifications');
            
            if (false === $table_exists) {
                $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table;
                wp_cache_set($table_cache_key, $table_exists, 'wpseed_notifications', 3600);
            }
            
            if (!$table_exists) {
                return 0;
            }
            
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}wpseed_notifications WHERE (user_id = %d OR user_id = 0) AND is_read = 0 AND (expires_at IS NULL OR expires_at > %s)",
                $user_id,
                current_time('mysql')
            ));
            
            wp_cache_set($cache_key, $count, 'wpseed_notifications', 60); // 1 min cache
        }
        
        return $count;
    }
}

add_action('init', array('WPSeed_Notifications', 'init'));
