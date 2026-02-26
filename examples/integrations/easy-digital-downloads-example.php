<?php
/**
 * Easy Digital Downloads Integration Example
 *
 * @package WPSeed/Examples/Integrations
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_EDD_Integration
 */
class WPSeed_EDD_Integration {

    public function __construct() {
        // Check if EDD is active
        if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
            return;
        }

        add_action( 'edd_meta_box_fields', array( $this, 'add_download_meta_field' ), 10, 2 );
        add_filter( 'edd_metabox_save_download', array( $this, 'save_download_meta_field' ) );
        add_action( 'edd_complete_purchase', array( $this, 'after_purchase' ) );
        add_filter( 'edd_purchase_receipt', array( $this, 'custom_receipt' ), 10, 3 );
        add_filter( 'manage_edit-download_columns', array( $this, 'add_download_column' ) );
        add_action( 'manage_download_posts_custom_column', array( $this, 'populate_download_column' ), 10, 2 );
    }

    /**
     * Add custom meta field to download
     */
    public function add_download_meta_field( $post_id, $post ) {
        $value = get_post_meta( $post_id, '_wpseed_custom_field', true );
        ?>
        <p>
            <label for="wpseed_custom_field"><?php esc_html_e( 'Custom Field', 'wpseed' ); ?></label>
            <input type="text" name="wpseed_custom_field" id="wpseed_custom_field" value="<?php echo esc_attr( $value ); ?>" class="large-text" />
        </p>
        <?php
    }

    /**
     * Save download meta field
     */
    public function save_download_meta_field( $fields ) {
        $fields[] = 'wpseed_custom_field';
        return $fields;
    }

    /**
     * Process after purchase completion
     */
    public function after_purchase( $payment_id ) {
        $payment = edd_get_payment( $payment_id );
        
        // Log purchase
        $this->log_purchase( $payment );
        
        // Custom processing
        do_action( 'wpseed_edd_purchase', $payment );
    }

    /**
     * Customize purchase receipt
     */
    public function custom_receipt( $receipt, $edd_receipt_args, $payment ) {
        $custom_message = '<div class="wpseed-receipt-message">';
        $custom_message .= '<p>' . __( 'Thank you for your purchase!', 'wpseed' ) . '</p>';
        $custom_message .= '</div>';
        
        return $custom_message . $receipt;
    }

    /**
     * Add custom column to downloads list
     */
    public function add_download_column( $columns ) {
        $columns['wpseed_custom'] = __( 'Custom', 'wpseed' );
        return $columns;
    }

    /**
     * Populate custom column
     */
    public function populate_download_column( $column, $post_id ) {
        if ( 'wpseed_custom' === $column ) {
            $value = get_post_meta( $post_id, '_wpseed_custom_field', true );
            echo $value ? esc_html( $value ) : '—';
        }
    }

    /**
     * Log purchase
     */
    private function log_purchase( $payment ) {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'wpseed_edd_purchases',
            array(
                'payment_id'   => $payment->ID,
                'customer_id'  => $payment->customer_id,
                'total'        => $payment->total,
                'purchase_date' => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%f', '%s' )
        );
    }
}

// Initialize
new WPSeed_EDD_Integration();
