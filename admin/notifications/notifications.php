<?php
/**
 * WPSeed Notifications
 *
 * @package WPSeed
 * @version 1.0.0
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
                'expires_at' => $args['expiration'] ? date('Y-m-d H:i:s', $args['expiration']) : null,
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
        
        $subject = sprintf(__('[%s] %s Notification', 'wpseed'), 
            get_bloginfo('name'), 
            self::$notification_types[$type]['label']
        );
        
        $email_body = sprintf(
            __("Hello %s,\n\nYou have received a new notification:\n\n%s\n\nRegards,\n%s Team", 'wpseed'),
            $user_name,
            $message,
            get_bloginfo('name')
        );
        
        return wp_mail($user_email, $subject, $email_body);
    }

    public static function process_pending_notifications() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wpseed_notifications';
        
        $wpdb->query("UPDATE $table_name SET is_read = 1 WHERE expires_at IS NOT NULL AND expires_at < NOW()");
        
        do_action('wpseed_process_scheduled_notifications');
    }

    public static function get_notifications($user_id = 0, $args = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wpseed_notifications';
        
        $defaults = array(
            'limit'   => 20,
            'offset'  => 0,
            'type'    => '',
            'is_read' => null,
            'orderby' => 'created_at',
            'order'   => 'DESC',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array();
        $where[] = $wpdb->prepare("user_id = %d OR user_id = 0", $user_id);
        
        if ($args['type']) {
            $where[] = $wpdb->prepare("type = %s", $args['type']);
        }
        
        if ($args['is_read'] !== null) {
            $where[] = $wpdb->prepare("is_read = %d", $args['is_read']);
        }
        
        $where[] = "(expires_at IS NULL OR expires_at > NOW())";
        
        $where_clause = implode(' AND ', $where);
        
        $orderby = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);
        
        $limit_clause = $wpdb->prepare("LIMIT %d OFFSET %d", $args['limit'], $args['offset']);
        
        $notifications = $wpdb->get_results(
            "SELECT * FROM $table_name WHERE $where_clause ORDER BY $orderby $limit_clause"
        );
        
        foreach ($notifications as &$notification) {
            $notification->data = maybe_unserialize($notification->data);
        }
        
        return $notifications;
    }

    public static function mark_as_read($notification_id, $user_id = 0) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wpseed_notifications';
        
        $where = array('id' => $notification_id);
        
        if ($user_id > 0) {
            $where['user_id'] = $user_id;
        }
        
        $updated = $wpdb->update(
            $table_name,
            array('is_read' => 1),
            $where
        );
        
        return $updated !== false;
    }

    public static function maybe_create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wpseed_notifications';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
            
            $sql = "CREATE TABLE $table_name (
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
        $snooze_until = date('Y-m-d H:i:s', time() + $duration);
        
        return $wpdb->update(
            $table,
            array(
                'is_snoozed' => 1,
                'snooze_until' => $snooze_until,
            ),
            array('id' => $notification_id)
        );
    }
    
    /**
     * Mark all as read
     */
    public static function mark_all_read($user_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'wpseed_notifications';
        
        return $wpdb->update(
            $table,
            array('is_read' => 1),
            array('user_id' => $user_id, 'is_read' => 0)
        );
    }
    
    /**
     * Delete notification
     */
    public static function delete_notification($notification_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'wpseed_notifications';
        
        return $wpdb->delete($table, array('id' => $notification_id));
    }
    
    /**
     * Get unread count
     */
    public static function get_unread_count($user_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'wpseed_notifications';
        
        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE (user_id = %d OR user_id = 0) AND is_read = 0 AND (expires_at IS NULL OR expires_at > NOW())",
            $user_id
        ));
    }
}

add_action('init', array('WPSeed_Notifications', 'init'));
