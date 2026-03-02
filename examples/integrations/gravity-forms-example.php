<?php
/**
 * Gravity Forms Integration Example
 *
 * @package WPSeed/Examples/Integrations
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_GravityForms_Integration
 */
class WPSeed_GravityForms_Integration {

    public function __construct() {
        // Check if Gravity Forms is active
        if ( ! class_exists( 'GFForms' ) ) {
            return;
        }

        add_action( 'gform_after_submission', array( $this, 'after_submission' ), 10, 2 );
        add_filter( 'gform_validation', array( $this, 'custom_validation' ) );
        add_filter( 'gform_pre_render', array( $this, 'populate_dropdown' ) );
        add_filter( 'gform_confirmation', array( $this, 'custom_confirmation' ), 10, 4 );
    }

    /**
     * Process form after submission
     */
    public function after_submission( $entry, $form ) {
        // Log entry
        $this->log_entry( $entry, $form );
        
        // Custom processing
        do_action( 'wpseed_gf_submission', $entry, $form );
    }

    /**
     * Custom form validation
     */
    public function custom_validation( $validation_result ) {
        $form = $validation_result['form'];
        
        foreach ( $form['fields'] as &$field ) {
            if ( $field->label === 'Custom Field' ) {
                $value = rgpost( "input_{$field->id}" );
                
                if ( strlen( $value ) < 5 ) {
                    $validation_result['is_valid'] = false;
                    $field->failed_validation = true;
                    $field->validation_message = __( 'Must be at least 5 characters.', 'wpseed' );
                }
            }
        }
        
        $validation_result['form'] = $form;
        return $validation_result;
    }

    /**
     * Populate dropdown dynamically
     */
    public function populate_dropdown( $form ) {
        foreach ( $form['fields'] as &$field ) {
            if ( $field->type !== 'select' || strpos( $field->cssClass, 'populate-posts' ) === false ) {
                continue;
            }
            
            $posts = get_posts( array( 'numberposts' => -1 ) );
            $choices = array();
            
            foreach ( $posts as $post ) {
                $choices[] = array( 'text' => $post->post_title, 'value' => $post->ID );
            }
            
            $field->choices = $choices;
        }
        
        return $form;
    }

    /**
     * Custom confirmation message
     */
    public function custom_confirmation( $confirmation, $form, $entry, $ajax ) {
        $custom_message = '<div class="wpseed-confirmation">';
        $custom_message .= '<h3>' . __( 'Thank you!', 'wpseed' ) . '</h3>';
        $custom_message .= '<p>' . __( 'Your submission has been received.', 'wpseed' ) . '</p>';
        $custom_message .= '</div>';
        
        return $custom_message;
    }

    /**
     * Log form entry
     */
    private function log_entry( $entry, $form ) {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'wpseed_gf_entries',
            array(
                'form_id'     => $form['id'],
                'entry_data'  => maybe_serialize( $entry ),
                'submit_time' => current_time( 'mysql' ),
            ),
            array( '%d', '%s', '%s' )
        );
    }
}

// Initialize
new WPSeed_GravityForms_Integration();
