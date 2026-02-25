<?php
/**
 * WooCommerce Integration Example
 *
 * @package WPSeed/Examples/Integrations
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_WooCommerce_Integration
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
     * Save custom product field
     */
    public function save_custom_product_field( $post_id ) {
        $value = isset( $_POST['_wpseed_custom_field'] ) ? sanitize_text_field( $_POST['_wpseed_custom_field'] ) : '';
        update_post_meta( $post_id, '_wpseed_custom_field', $value );
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
     * Save custom order data
     */
    public function save_custom_order_data( $order, $data ) {
        if ( isset( $_POST['wpseed_order_field'] ) ) {
            $order->update_meta_data( '_wpseed_order_data', sanitize_text_field( $_POST['wpseed_order_field'] ) );
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
