<?php
/**
 * Request Listener
 * 
 * Centralized form processing with security
 * 
 * @package WPSeed
 */

defined( 'ABSPATH' ) || die;

class WPSeed_Listener {
    
    public function __construct() {
        add_action( 'wp_loaded', array( $this, 'process_requests' ) );
        add_action( 'admin_notices', array( $this, 'display_notices' ) );
    }
    
    public function process_requests() {
        if ( defined( 'DOING_AUTOSAVE' ) || defined( 'DOING_CRON' ) || defined( 'DOING_AJAX' ) ) {
            return;
        }
        
        if ( empty( $_SERVER['REQUEST_METHOD'] ) || 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
            return;
        }

        $this->process_post_requests();
    }
    
    private function process_post_requests() {
        if ( ! isset( $_POST['wpseed_form_action'] ) ) {
            return;
        }
        
        if ( ! function_exists( 'is_user_logged_in' ) || ! is_user_logged_in() ) {
            return;
        }
        
        $action = sanitize_key( $_POST['wpseed_form_action'] );
        
        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), $action ) ) {
            wp_die( esc_html__( 'Security check failed', 'wpseed' ) );
        }
        
        do_action( 'wpseed_process_form_' . $action );
    }
    
    public function display_notices() {
        $notice = get_transient( 'wpseed_admin_notice' );
        
        if ( $notice ) {
            echo '<div class="notice notice-' . esc_attr( $notice['type'] ) . ' is-dismissible"><p>' . 
                 wp_kses_post( $notice['message'] ) . '</p></div>';
            delete_transient( 'wpseed_admin_notice' );
        }
    }
}

// Initialize only after WordPress is loaded
add_action('init', function() {
    new WPSeed_Listener();
});
