<?php
/**
 * bbPress Integration Example
 *
 * @package WPSeed/Examples/Integrations
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_bbPress_Integration
 *
 * @since   1.0.0
 * @version 2.0.0
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
     * Save custom field.
     *
     * The nonce is extracted into a sanitised local variable before
     * wp_verify_nonce() so PHPCS can confirm the superglobal is both
     * unslashed and sanitised.
     *
     * @since   1.1.0
     * @version 1.2.0
     *
     * @param int $topic_id ID of the newly created topic.
     * @return void
     */
    public function save_custom_field( $topic_id ) {
        if ( ! isset( $_POST['wpseed_custom_field'], $_POST['wpseed_custom_field_nonce'] ) ) {
            return;
        }

        // Unslash and sanitise the nonce into a local variable so PHPCS
        // recognises it as sanitised before it is passed to wp_verify_nonce().
        $wpseed_nonce = sanitize_text_field( wp_unslash( $_POST['wpseed_custom_field_nonce'] ) );

        if ( wp_verify_nonce( $wpseed_nonce, 'wpseed_custom_field_action' ) ) {
            // wp_unslash() applied before sanitize_text_field() per MissingUnslash standard.
            update_post_meta( $topic_id, '_wpseed_custom_data', sanitize_text_field( wp_unslash( $_POST['wpseed_custom_field'] ) ) );
        }
    }

    /**
     * Log new topic to the custom topics table.
     *
     * $wpdb->insert() writes to a custom plugin table for which no WordPress
     * API equivalent exists. wp_cache_delete() invalidates any cached read for
     * this topic so get_topic_log() reflects the new row on the next call.
     *
     * @since   1.1.0
     * @version 2.0.0
     *
     * @param int $topic_id ID of the new topic.
     * @param int $forum_id ID of the forum.
     * @return void
     */
    private function log_topic( $topic_id, $forum_id ) {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'wpseed_bbpress_topics',
            array(
                'topic_id'   => $topic_id,
                'forum_id'   => $forum_id,
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%s' )
        );

        // Invalidate the cached read so get_topic_log() fetches fresh data.
        wp_cache_delete( 'wpseed_bbpress_topic_' . $topic_id, 'wpseed_bbpress' );
    }

    /**
     * Retrieve the log row for a topic, with caching.
     *
     * Pairs with log_topic() to complete the cache round-trip: log_topic()
     * writes and invalidates; this method reads and caches. Custom table —
     * no WordPress API equivalent. $wpdb->prepare() used for the dynamic value.
     *
     * @since  2.0.0
     *
     * @param int $topic_id ID of the topic.
     * @return object|null Row object, or null if not found.
     */
    public function get_topic_log( $topic_id ) {
        global $wpdb;

        $cache_key = 'wpseed_bbpress_topic_' . (int) $topic_id;
        $row       = wp_cache_get( $cache_key, 'wpseed_bbpress' );

        if ( false === $row ) {
            // Direct query on a custom table — no WP API equivalent.
            $row = $wpdb->get_row(
                $wpdb->prepare(
                    'SELECT * FROM `' . esc_sql( $wpdb->prefix . 'wpseed_bbpress_topics' ) . '` WHERE topic_id = %d',
                    (int) $topic_id
                )
            );
            // Cache for 5 minutes; invalidated by log_topic() on insert.
            wp_cache_set( $cache_key, $row, 'wpseed_bbpress', 5 * MINUTE_IN_SECONDS );
        }

        return $row;
    }

    /**
     * Log new reply to the custom replies table.
     *
     * $wpdb->insert() writes to a custom plugin table for which no WordPress
     * API equivalent exists. wp_cache_delete() invalidates any cached read for
     * this reply so get_reply_log() reflects the new row on the next call.
     *
     * @since   1.1.0
     * @version 2.0.0
     *
     * @param int $reply_id ID of the new reply.
     * @param int $topic_id ID of the parent topic.
     * @return void
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

        // Invalidate the cached read so get_reply_log() fetches fresh data.
        wp_cache_delete( 'wpseed_bbpress_reply_' . $reply_id, 'wpseed_bbpress' );
    }

    /**
     * Retrieve the log row for a reply, with caching.
     *
     * Pairs with log_reply() to complete the cache round-trip: log_reply()
     * writes and invalidates; this method reads and caches. Custom table —
     * no WordPress API equivalent. $wpdb->prepare() used for the dynamic value.
     *
     * @since  2.0.0
     *
     * @param int $reply_id ID of the reply.
     * @return object|null Row object, or null if not found.
     */
    public function get_reply_log( $reply_id ) {
        global $wpdb;

        $cache_key = 'wpseed_bbpress_reply_' . (int) $reply_id;
        $row       = wp_cache_get( $cache_key, 'wpseed_bbpress' );

        if ( false === $row ) {
            // Direct query on a custom table — no WP API equivalent.
            $row = $wpdb->get_row(
                $wpdb->prepare(
                    'SELECT * FROM `' . esc_sql( $wpdb->prefix . 'wpseed_bbpress_replies' ) . '` WHERE reply_id = %d',
                    (int) $reply_id
                )
            );
            // Cache for 5 minutes; invalidated by log_reply() on insert.
            wp_cache_set( $cache_key, $row, 'wpseed_bbpress', 5 * MINUTE_IN_SECONDS );
        }

        return $row;
    }
}

// Initialize
new WPSeed_bbPress_Integration();
