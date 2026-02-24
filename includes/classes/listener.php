<?php
/**
 * Request Listener
 * 
 * Centralized form processing with security and tracking
 * 
 * @package WPSeed
 */

defined( 'ABSPATH' ) || die;

class WPSeed_Listener {
    
    private static $current_request_id = null;
    
    public function __construct() {
        add_action( 'wp_loaded', array( $this, 'process_requests' ) );
        add_action( 'admin_notices', array( $this, 'display_notices' ) );
    }
    
    public function process_requests() {
        if ( defined( 'DOING_AUTOSAVE' ) || defined( 'DOING_CRON' ) ) {
            return;
        }
        
        // Track all requests in dev mode
        if ( WPSeed_Developer_Mode::is_dev_environment() ) {
            $this->track_request();
        }
        
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $this->process_post_requests();
        }
    }
    
    private function track_request() {
        global $wpdb;
        
        $is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
        $has_post = !empty( $_POST );
        $has_get = !empty( $_GET );
        
        if ( !$has_post && !$has_get && !$is_ajax ) {
            return;
        }
        
        $table = $wpdb->prefix . 'wpseed_request_log';
        $this->maybe_create_log_table();
        
        $wpdb->insert(
            $table,
            array(
                'request_type' => $is_ajax ? 'AJAX' : $_SERVER['REQUEST_METHOD'],
                'url' => esc_url_raw( $_SERVER['REQUEST_URI'] ),
                'post_data' => $has_post ? wp_json_encode( $_POST ) : null,
                'get_data' => $has_get ? wp_json_encode( $_GET ) : null,
                'user_id' => get_current_user_id(),
                'ip_address' => $this->get_client_ip(),
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
        );
        
        self::$current_request_id = $wpdb->insert_id;
    }
    
    private function process_post_requests() {
        if ( ! isset( $_POST['wpseed_form_action'] ) ) {
            $this->log_decision( 'skipped', 'No wpseed_form_action field' );
            return;
        }
        
        if ( ! function_exists( 'is_user_logged_in' ) || ! is_user_logged_in() ) {
            $this->log_decision( 'rejected', 'User not logged in' );
            return;
        }
        
        $action = sanitize_key( $_POST['wpseed_form_action'] );
        
        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], $action ) ) {
            $this->log_decision( 'rejected', 'Nonce verification failed' );
            wp_die( esc_html__( 'Security check failed', 'wpseed' ) );
        }
        
        $this->log_decision( 'processed', 'Action: ' . $action );
        do_action( 'wpseed_process_form_' . $action );
    }
    
    private function log_decision( $status, $reason ) {
        if ( ! WPSeed_Developer_Mode::is_dev_environment() || ! self::$current_request_id ) {
            return;
        }
        
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . 'wpseed_request_log',
            array(
                'status' => $status,
                'decision_reason' => $reason,
            ),
            array( 'id' => self::$current_request_id ),
            array( '%s', '%s' ),
            array( '%d' )
        );
    }
    
    private function maybe_create_log_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'wpseed_request_log';
        
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) ) === $table ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            request_type varchar(10) NOT NULL,
            url text NOT NULL,
            post_data longtext,
            get_data longtext,
            user_id bigint(20) NOT NULL DEFAULT 0,
            ip_address varchar(45),
            status varchar(20),
            decision_reason text,
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    private function get_client_ip() {
        $ip = '';
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return sanitize_text_field( $ip );
    }
    
    public function display_notices() {
        $notice = get_transient( 'wpseed_admin_notice' );
        
        if ( $notice ) {
            echo '<div class="notice notice-' . esc_attr( $notice['type'] ) . ' is-dismissible"><p>' . 
                 wp_kses_post( $notice['message'] ) . '</p></div>';
            delete_transient( 'wpseed_admin_notice' );
        }
    }
    
    public static function get_recent_requests( $limit = 20 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'wpseed_request_log';
        
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table ORDER BY created_at DESC LIMIT %d",
            $limit
        ) );
    }
}

add_action('init', function() {
    new WPSeed_Listener();
});
