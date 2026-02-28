<?php
/**
 * Contact Form 7 Integration Example
 *
 * @package WPSeed/Examples/Integrations
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_CF7_Integration
 */
class WPSeed_CF7_Integration {

    public function __construct() {
        // Check if CF7 is active
        if ( ! function_exists( 'wpcf7' ) ) {
            return;
        }

        add_action( 'wpcf7_before_send_mail', array( $this, 'before_send_mail' ) );
        add_action( 'wpcf7_mail_sent', array( $this, 'after_mail_sent' ) );
        add_filter( 'wpcf7_validate_text*', array( $this, 'custom_validation' ), 10, 2 );
    }

    /**
     * Process form before sending email
     */
    public function before_send_mail( $contact_form ) {
        $submission = WPCF7_Submission::get_instance();
        
        if ( $submission ) {
            $posted_data = $submission->get_posted_data();
            
            // Log submission
            $this->log_submission( $posted_data );
            
            // Custom processing
            do_action( 'wpseed_cf7_submission', $posted_data, $contact_form );
        }
    }

    /**
     * After mail is sent
     */
    public function after_mail_sent( $contact_form ) {
        // Custom action after successful submission
        do_action( 'wpseed_cf7_mail_sent', $contact_form );
    }

    /**
     * Custom field validation
     */
    public function custom_validation( $result, $tag ) {
        $value = isset( $tag->values[0] ) ? trim( $tag->values[0] ) : '';
        
        // Example: validate minimum length
        if ( strlen( $value ) < 5 ) {
            $result->invalidate( $tag, __( 'This field must be at least 5 characters.', 'wpseed' ) );
        }
        
        return $result;
    }

    /**
     * Log form submission
     */
    private function log_submission( $data ) {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'wpseed_cf7_submissions',
            array(
                'form_data'   => maybe_serialize( $data ),
                'submit_time' => current_time( 'mysql' ),
                'ip_address'  => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
            ),
            array( '%s', '%s', '%s' )
        );
    }
}

// Initialize
new WPSeed_CF7_Integration();
