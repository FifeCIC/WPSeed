<?php
/**
 * MemberPress Integration Example
 *
 * @package WPSeed/Examples/Integrations
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_MemberPress_Integration
 *
 * @since   1.1.0
 * @version 1.2.0
 */
class WPSeed_MemberPress_Integration {

    public function __construct() {
        // Check if MemberPress is active
        if ( ! defined( 'MEPR_VERSION' ) ) {
            return;
        }

        add_action( 'mepr-event-transaction-completed', array( $this, 'after_transaction_completed' ) );
        add_action( 'mepr-event-subscription-created', array( $this, 'after_subscription_created' ) );
        add_action( 'mepr-event-subscription-stopped', array( $this, 'after_subscription_stopped' ) );
        add_filter( 'mepr-validate-account', array( $this, 'custom_account_validation' ) );
    }

    /**
     * Process after transaction completed
     */
    public function after_transaction_completed( $event ) {
        $txn = $event->get_data();
        
        // Log transaction
        $this->log_transaction( $txn );
        
        // Custom processing
        do_action( 'wpseed_mepr_transaction_completed', $txn );
    }

    /**
     * Process after subscription created
     */
    public function after_subscription_created( $event ) {
        $sub = $event->get_data();
        
        // Log subscription
        $this->log_subscription( $sub );
        
        // Send custom welcome email
        $this->send_welcome_email( $sub->user_id );
        
        // Custom processing
        do_action( 'wpseed_mepr_subscription_created', $sub );
    }

    /**
     * Process after subscription stopped
     */
    public function after_subscription_stopped( $event ) {
        $sub = $event->get_data();
        
        // Log cancellation
        $this->log_cancellation( $sub );
        
        // Custom processing
        do_action( 'wpseed_mepr_subscription_stopped', $sub );
    }

    /**
     * Custom account validation.
     *
     * The MemberPress signup nonce is extracted into a sanitised local variable
     * before wp_verify_nonce() so PHPCS can confirm the superglobal is both
     * unslashed and sanitised — the same pattern used throughout this plugin.
     *
     * @since   1.1.0
     * @version 1.2.0
     *
     * @param array $errors Existing validation errors.
     * @return array Validation errors, possibly with a new entry appended.
     */
    public function custom_account_validation( $errors ) {
        if ( isset( $_POST['user_email'], $_POST['mepr_process_signup_form'] ) ) {
            // Unslash and sanitise the nonce into a local variable so PHPCS
            // recognises it as sanitised before it is passed to wp_verify_nonce().
            $wpseed_mepr_nonce = sanitize_text_field( wp_unslash( $_POST['mepr_process_signup_form'] ) );

            if ( wp_verify_nonce( $wpseed_mepr_nonce, 'mepr_process_signup_form' ) ) {
                $email = sanitize_email( wp_unslash( $_POST['user_email'] ) );

                // Custom validation logic
                if ( strpos( $email, 'example.com' ) !== false ) {
                    $errors[] = __( 'Email from this domain is not allowed.', 'wpseed' );
                }
            }
        }

        return $errors;
    }

    /**
     * Log transaction to the custom transactions table.
     *
     * $wpdb->insert() is used because this writes to a custom plugin table
     * for which no WordPress API equivalent exists. The format array ensures
     * all values are parameterised. wp_cache_delete() invalidates any cached
     * reads for this transaction so subsequent queries reflect the new row.
     *
     * @since   1.1.0
     * @version 1.2.0
     *
     * @param object $txn MemberPress transaction object.
     * @return void
     */
    private function log_transaction( $txn ) {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'wpseed_mepr_transactions',
            array(
                'transaction_id' => $txn->id,
                'user_id'        => $txn->user_id,
                'amount'         => $txn->amount,
                'created_at'     => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%f', '%s' )
        );

        // Invalidate cached transaction data for this user after the insert.
        wp_cache_delete( 'wpseed_mepr_transactions_' . $txn->user_id, 'wpseed_mepr' );
    }

    /**
     * Log subscription
     */
    private function log_subscription( $sub ) {
        update_user_meta( $sub->user_id, '_wpseed_subscription_start', current_time( 'mysql' ) );
    }

    /**
     * Log cancellation
     */
    private function log_cancellation( $sub ) {
        update_user_meta( $sub->user_id, '_wpseed_subscription_end', current_time( 'mysql' ) );
    }

    /**
     * Send welcome email
     */
    private function send_welcome_email( $user_id ) {
        $user = get_userdata( $user_id );
        
        $subject = __( 'Welcome to Our Membership!', 'wpseed' );
        /* translators: %s: User display name */
        $message = sprintf( __( 'Hi %s, welcome to our membership site!', 'wpseed' ), $user->display_name );
        
        wp_mail( $user->user_email, $subject, $message );
    }
}

// Initialize
new WPSeed_MemberPress_Integration();
