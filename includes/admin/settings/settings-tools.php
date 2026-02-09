<?php
/**
 * WPSeed Tools Settings Page
 *
 * @package WPSeed/Admin/Settings
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPSeed_Settings_Tools' ) ) :

/**
 * WPSeed_Settings_Tools
 */
class WPSeed_Settings_Tools extends WPSeed_Settings_Page {

    /**
     * Constructor
     */
    public function __construct() {
        $this->id    = 'tools';
        $this->label = __( 'Tools', 'wpseed' );

        parent::__construct();
    }

    /**
     * Get settings array
     */
    public function get_settings() {
        $settings = array(

            array(
                'title' => __( 'Plugin Tools', 'wpseed' ),
                'type'  => 'title',
                'desc'  => __( 'Utilities for managing your plugin settings and data.', 'wpseed' ),
                'id'    => 'tools_section'
            ),

            array(
                'type' => 'sectionend',
                'id'   => 'tools_section'
            ),

        );

        return apply_filters( 'wpseed_tools_settings', $settings );
    }

    /**
     * Output the settings
     */
    public function output() {
        $settings = $this->get_settings();
        WPSeed_Admin_Settings::output_fields( $settings );
        
        // Output import/export UI
        do_action( 'wpseed_settings_export_import' );
    }

    /**
     * Save settings
     */
    public function save() {
        // Import/export handles its own saving
    }
}

endif;

return new WPSeed_Settings_Tools();
