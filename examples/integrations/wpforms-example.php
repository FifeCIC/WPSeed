<?php
/**
 * WPForms Integration Example
 *
 * @package WPSeed/Examples/Integrations
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_WPForms_Integration
 */
class WPSeed_WPForms_Integration {

    public function __construct() {
        // Check if WPForms is active
        if ( ! function_exists( 'wpforms' ) ) {
            return;
        }

        add_action( 'wpforms_process_complete', array( $this, 'after_form_submission' ), 10, 4 );
        add_filter( 'wpforms_process_before_form_data', array( $this, 'custom_validation' ), 10, 2 );
        add_filter( 'wpforms_frontend_confirmation_message', array( $this, 'custom_confirmation' ), 10, 3 );
    }

    /**
     * Process after form submission
     */
    public function after_form_submission( $fields, $entry, $form_data, $entry_id ) {
        // Log submission
        $this->log_submission( $entry_id, $form_data['id'], $fields );
        
        // Custom processing based on form ID
        if ( $form_data['id'] == 123 ) {
            $this->process_contact_form( $fields );
        }
        
        // Custom processing
        do_action( 'wpseed_wpforms_submission', $fields, $entry, $form_data );
    }

    /**
     * Custom form validation
     */
    public function custom_validation( $form_data, $entry ) {
        // Example: validate email domain
        if ( isset( $form_data['fields'][1]['value'] ) ) {
            $email = $form_data['fields'][1]['value'];
            
            if ( strpos( $email, 'blocked-domain.com' ) !== false ) {
                wpforms()->process->errors[ $form_data['id'] ][1] = __( 'This email domain is not allowed.', 'wpseed' );
            }
        }
        
        return $form_data;
    }

    /**
     * Custom confirmation message
     */
    public function custom_confirmation( $message, $form_data, $fields ) {
        $custom_message = '<div class="wpseed-confirmation">';
        $custom_message .= '<h3>' . __( 'Thank You!', 'wpseed' ) . '</h3>';
        $custom_message .= '<p>' . __( 'We have received your submission.', 'wpseed' ) . '</p>';
        $custom_message .= '</div>';
        
        return $custom_message;
    }

    /**
     * Process contact form
     */
    private function process_contact_form( $fields ) {
        // Extract field values
        $name = isset( $fields[0]['value'] ) ? $fields[0]['value'] : '';
        $email = isset( $fields[1]['value'] ) ? $fields[1]['value'] : '';
        
        // Custom processing
        update_option( 'wpseed_last_contact', array(
            'name'  => $name,
            'email' => $email,
            'date'  => current_time( 'mysql' ),
        ) );
    }

    /**
     * Log a WPForms submission to the custom submissions table.
     *
     * Writes entry data to wpseed_wpforms_submissions and invalidates the
     * cached submissions list so subsequent reads reflect the new row. A
     * direct database call is used because there is no WordPress API
     * equivalent for inserting into a custom table.
     *
     * @param int   $entry_id The WPForms entry ID.
     * @param int   $form_id  The WPForms form ID.
     * @param array $fields   The submitted field data.
     * @return void
     */
    private function log_submission( $entry_id, $form_id, $fields ) {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'wpseed_wpforms_submissions',
            array(
                'entry_id'    => $entry_id,
                'form_id'     => $form_id,
                'field_data'  => maybe_serialize( $fields ),
                'submit_time' => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%s', '%s' )
        );

        // Invalidate cached reads so the new row is visible immediately.
        wp_cache_delete( 'wpseed_wpforms_submissions', 'wpseed' );
    }
}

// Initialize
new WPSeed_WPForms_Integration();
