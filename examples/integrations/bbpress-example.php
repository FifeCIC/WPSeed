<?php
/**
 * bbPress Integration Example
 *
 * @package WPSeed/Examples/Integrations
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_bbPress_Integration
 */
class WPSeed_bbPress_Integration {

    public function __construct() {
        // Check if bbPress is active
        if ( ! class_exists( 'bbPress' ) ) {
            return;
        }

        add_action( 'bbp_new_topic', array( $this, 'after_new_topic' ), 10, 4 );
        add_action( 'bbp_new_reply', array( $this, 'after_new_reply' ), 10, 5 );
        add_filter( 'bbp_get_topic_content', array( $this, 'modify_topic_content' ), 10, 2 );
        add_action( 'bbp_theme_before_topic_form_submit_wrapper', array( $this, 'add_custom_field' ) );
        add_action( 'bbp_new_topic_post_extras', array( $this, 'save_custom_field' ) );
    }

    /**
     * Process after new topic
     */
    public function after_new_topic( $topic_id, $forum_id, $anonymous_data, $topic_author ) {
        // Log new topic
        $this->log_topic( $topic_id, $forum_id );
        
        // Custom processing
        do_action( 'wpseed_bbpress_new_topic', $topic_id, $forum_id );
    }

    /**
     * Process after new reply
     */
    public function after_new_reply( $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author ) {
        // Log new reply
        $this->log_reply( $reply_id, $topic_id );
        
        // Custom processing
        do_action( 'wpseed_bbpress_new_reply', $reply_id, $topic_id );
    }

    /**
     * Modify topic content
     */
    public function modify_topic_content( $content, $topic_id ) {
        $custom_data = get_post_meta( $topic_id, '_wpseed_custom_data', true );
        
        if ( $custom_data ) {
            $content .= '<div class="wpseed-custom-data">' . esc_html( $custom_data ) . '</div>';
        }
        
        return $content;
    }

    /**
     * Add custom field to topic form
     */
    public function add_custom_field() {
        wp_nonce_field( 'wpseed_custom_field_action', 'wpseed_custom_field_nonce' );
        ?>
        <p>
            <label for="wpseed_custom_field"><?php esc_html_e( 'Custom Field', 'wpseed' ); ?></label>
            <input type="text" name="wpseed_custom_field" id="wpseed_custom_field" class="regular-text" />
        </p>
        <?php
    }

    /**
     * Save custom field
     */
    public function save_custom_field( $topic_id ) {
        if ( isset( $_POST['wpseed_custom_field'] ) && isset( $_POST['wpseed_custom_field_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['wpseed_custom_field_nonce'] ), 'wpseed_custom_field_action' ) ) {
            update_post_meta( $topic_id, '_wpseed_custom_data', sanitize_text_field( $_POST['wpseed_custom_field'] ) );
        }
    }

    /**
     * Log new topic
     */
    private function log_topic( $topic_id, $forum_id ) {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'wpseed_bbpress_topics',
            array(
                'topic_id'    => $topic_id,
                'forum_id'    => $forum_id,
                'created_at'  => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%s' )
        );
    }

    /**
     * Log new reply
     */
    private function log_reply( $reply_id, $topic_id ) {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'wpseed_bbpress_replies',
            array(
                'reply_id'   => $reply_id,
                'topic_id'   => $topic_id,
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%s' )
        );
    }
}

// Initialize
new WPSeed_bbPress_Integration();
