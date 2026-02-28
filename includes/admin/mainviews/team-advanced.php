<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSeed_ListTable_Stuff' ) ) {
    require_once( 'class.wpseed-listtable-demo-advanced.php' );
}

/**
 * WPSeed_MainView_Team_Items.
 * 
 * This is one of multiple classes that extends a parent class which builds
 * the table. This approach essentially splits a table into common views just as if
 * a search criteria was entered.  
 *
 * @author      Ryan Bayne
 * @category    Admin
 * @package     WPSeed/Admin/Reports
 * @version     1.0.0
 */
class WPSeed_MainView_Team_Advanced extends WPSeed_ListTable_Demo {

    /**
     * No items found text.
     */
    public function no_items() {
        esc_html_e( 'No applicable items found.', 'wpseed' );
    }

    /**
     * Filter the main data result and only return the items that apply
     * to this report.
     *
     * @param int $current_page
     * @param int $per_page
     */
    public function get_items( $current_page, $per_page ) {
        global $wpdb;
        
        // Filter $this->items to create a dataset suitable for this view.
        unset($this->items[1],$this->items[2],$this->items[3]);          
    }
    
    function column_headerone( $item ) {   
        // Establish an item ID for request processing.
        $id = $item['headerone'];
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page parameter used only for building display links, not data processing
        $page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '';
        $actions = array(
                'edit'      => sprintf('<a href="?page=%s&action=%s&examplevalue=%s">Edit</a>', esc_attr( $page ), 'edit', esc_attr( $id ) ),
                'delete'    => sprintf('<a href="?page=%s&action=%s&examplevalue=%s">Delete</a>', esc_attr( $page ), 'delete', esc_attr( $id ) ),
            );

        return sprintf('%1$s %2$s', $item['headerone'], $this->row_actions($actions) );
    }    
}