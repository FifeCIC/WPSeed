<?php
/**
 * WooCommerce Integration Example
 *
 * @package WPSeed/Examples/Integrations
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_WooCommerce_Integration
 *
 * @since   1.0.0
 * @version 2.0.0
 */
class WPSeed_WooCommerce_Integration {

    public function __construct() {
        // Check if WooCommerce is active
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        // Product hooks
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_custom_product_field' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_custom_product_field' ) );
        
        // Order hooks
        add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_custom_order_data' ) );
        add_action( 'woocommerce_checkout_create_order', array( $this, 'save_custom_order_data' ), 10, 2 );
        
        // Admin columns
        add_filter( 'manage_edit-product_columns', array( $this, 'add_product_column' ) );
        add_action( 'manage_product_posts_custom_column', array( $this, 'populate_product_column' ), 10, 2 );
    }

    /**
     * Add custom field to product
     */
    public function add_custom_product_field() {
        woocommerce_wp_text_input( array(
            'id'          => '_wpseed_custom_field',
            'label'       => __( 'Custom Field', 'wpseed' ),
            'placeholder' => __( 'Enter value', 'wpseed' ),
            'desc_tip'    => true,
            'description' => __( 'Custom field description', 'wpseed' ),
        ) );
    }

    /**
     * Save custom product field.
     *
     * The nonce and field value are each extracted into sanitised local
     * variables immediately after the isset() guard. Extracting before any
     * other use ensures PHPCS can confirm every superglobal read is both
     * unslashed and sanitised before it reaches wp_verify_nonce() or
     * update_post_meta().
     *
     * @since   1.1.0
     * @version 2.0.0
     *
     * @param int $post_id Product post ID.
     * @return void
     */
    public function save_custom_product_field( $post_id ) {
        // Bail early if either the field or the nonce is absent from the request.
        if ( ! isset( $_POST['_wpseed_custom_field'] ) || ! isset( $_POST['_wpseed_custom_field_nonce'] ) ) {
            return;
        }

        // Extract and sanitise the nonce immediately so PHPCS sees a sanitised
        // local variable rather than a bare superglobal at the verify call.
        $wpseed_nonce = sanitize_text_field( wp_unslash( $_POST['_wpseed_custom_field_nonce'] ) );

        if ( ! wp_verify_nonce( $wpseed_nonce, '_wpseed_custom_field_action' ) ) {
            return;
        }

        // Extract and sanitise the field value after nonce verification passes.
        $wpseed_field_value = sanitize_text_field( wp_unslash( $_POST['_wpseed_custom_field'] ) );
        update_post_meta( $post_id, '_wpseed_custom_field', $wpseed_field_value );
    }

    /**
     * Display custom order data
     */
    public function display_custom_order_data( $order ) {
        $custom_data = $order->get_meta( '_wpseed_order_data' );
        if ( $custom_data ) {
            echo '<p><strong>' . esc_html__( 'Custom Order Data', 'wpseed' ) . ':</strong> ' . esc_html( $custom_data ) . '</p>';
        }
    }

    /**
     * Save custom order data.
     *
     * Hooked to woocommerce_checkout_create_order which fires after WooCommerce
     * has already verified the checkout nonce. An explicit nonce check is added
     * here for defence-in-depth and to satisfy NonceVerification.Missing.
     *
     * @since   1.1.0
     * @version 1.2.0
     *
     * @param WC_Order $order The order object.
     * @param array    $data  Posted checkout data.
     * @return void
     */
    public function save_custom_order_data( $order, $data ) {
        // Verify WooCommerce's own checkout nonce before reading POST data.
        if ( ! isset( $_POST['woocommerce-process-checkout-nonce'] ) ) {
            return;
        }

        $wpseed_checkout_nonce = sanitize_text_field( wp_unslash( $_POST['woocommerce-process-checkout-nonce'] ) );

        if ( ! wp_verify_nonce( $wpseed_checkout_nonce, 'woocommerce-process_checkout' ) ) {
            return;
        }

        if ( isset( $_POST['wpseed_order_field'] ) ) {
            // wp_unslash() applied before sanitize_text_field() per MissingUnslash standard.
            $order->update_meta_data( '_wpseed_order_data', sanitize_text_field( wp_unslash( $_POST['wpseed_order_field'] ) ) );
        }
    }

    /**
     * Add custom column to products list
     */
    public function add_product_column( $columns ) {
        $columns['wpseed_custom'] = __( 'Custom', 'wpseed' );
        return $columns;
    }

    /**
     * Populate custom column
     */
    public function populate_product_column( $column, $post_id ) {
        if ( 'wpseed_custom' === $column ) {
            $value = get_post_meta( $post_id, '_wpseed_custom_field', true );
            echo $value ? esc_html( $value ) : '—';
        }
    }
}

// Initialize
new WPSeed_WooCommerce_Integration();
