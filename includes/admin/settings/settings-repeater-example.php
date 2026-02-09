<?php
/**
 * WPSeed Repeater Settings Example
 *
 * @author   Ryan Bayne
 * @category Admin
 * @package  WPSeed/Admin
 * @version  1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPSeed_Settings_Repeater_Example' ) ) :

/**
 * WPSeed_Settings_Repeater_Example.
 */
class WPSeed_Settings_Repeater_Example extends WPSeed_Settings_Page {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->id    = 'repeater';
        $this->label = __( 'Repeater Example', 'wpseed' );

        parent::__construct();
    }

    /**
     * Get settings array.
     *
     * @return array
     */
    public function get_settings() {
        $settings = array(

            array(
                'title' => __( 'Repeater Field Examples', 'wpseed' ),
                'type'  => 'title',
                'desc'  => __( 'Examples of repeater fields for dynamic configuration.', 'wpseed' ),
                'id'    => 'repeater_examples'
            ),

            array(
                'title'           => __( 'API Credentials', 'wpseed' ),
                'desc'            => __( 'Add multiple API credentials for different services.', 'wpseed' ),
                'id'              => 'wpseed_api_credentials',
                'type'            => 'repeater',
                'item_title'      => __( 'API Credential', 'wpseed' ),
                'add_button_text' => __( 'Add API Credential', 'wpseed' ),
                'fields'          => array(
                    array(
                        'id'          => 'service_name',
                        'label'       => __( 'Service Name', 'wpseed' ),
                        'type'        => 'text',
                        'placeholder' => __( 'e.g., Stripe, PayPal', 'wpseed' ),
                    ),
                    array(
                        'id'          => 'api_key',
                        'label'       => __( 'API Key', 'wpseed' ),
                        'type'        => 'text',
                        'placeholder' => __( 'Enter API key', 'wpseed' ),
                    ),
                    array(
                        'id'          => 'api_secret',
                        'label'       => __( 'API Secret', 'wpseed' ),
                        'type'        => 'text',
                        'placeholder' => __( 'Enter API secret', 'wpseed' ),
                    ),
                    array(
                        'id'      => 'environment',
                        'label'   => __( 'Environment', 'wpseed' ),
                        'type'    => 'select',
                        'options' => array(
                            'sandbox'    => __( 'Sandbox', 'wpseed' ),
                            'production' => __( 'Production', 'wpseed' ),
                        ),
                        'default' => 'sandbox',
                    ),
                    array(
                        'id'          => 'enabled',
                        'label'       => __( 'Enabled', 'wpseed' ),
                        'type'        => 'checkbox',
                        'description' => __( 'Enable this API credential', 'wpseed' ),
                        'default'     => 1,
                    ),
                ),
            ),

            array(
                'title'           => __( 'Custom Rules', 'wpseed' ),
                'desc'            => __( 'Define custom rules for your plugin logic.', 'wpseed' ),
                'id'              => 'wpseed_custom_rules',
                'type'            => 'repeater',
                'item_title'      => __( 'Rule', 'wpseed' ),
                'add_button_text' => __( 'Add Rule', 'wpseed' ),
                'fields'          => array(
                    array(
                        'id'          => 'rule_name',
                        'label'       => __( 'Rule Name', 'wpseed' ),
                        'type'        => 'text',
                        'placeholder' => __( 'Enter rule name', 'wpseed' ),
                    ),
                    array(
                        'id'          => 'condition',
                        'label'       => __( 'Condition', 'wpseed' ),
                        'type'        => 'textarea',
                        'placeholder' => __( 'Describe the condition', 'wpseed' ),
                    ),
                    array(
                        'id'      => 'action',
                        'label'   => __( 'Action', 'wpseed' ),
                        'type'    => 'select',
                        'options' => array(
                            'allow' => __( 'Allow', 'wpseed' ),
                            'deny'  => __( 'Deny', 'wpseed' ),
                            'log'   => __( 'Log Only', 'wpseed' ),
                        ),
                    ),
                ),
            ),

            array(
                'title'           => __( 'Social Media Links', 'wpseed' ),
                'desc'            => __( 'Add your social media profiles.', 'wpseed' ),
                'id'              => 'wpseed_social_links',
                'type'            => 'repeater',
                'item_title'      => __( 'Social Link', 'wpseed' ),
                'add_button_text' => __( 'Add Social Link', 'wpseed' ),
                'fields'          => array(
                    array(
                        'id'      => 'platform',
                        'label'   => __( 'Platform', 'wpseed' ),
                        'type'    => 'select',
                        'options' => array(
                            'facebook'  => __( 'Facebook', 'wpseed' ),
                            'twitter'   => __( 'Twitter', 'wpseed' ),
                            'linkedin'  => __( 'LinkedIn', 'wpseed' ),
                            'instagram' => __( 'Instagram', 'wpseed' ),
                            'youtube'   => __( 'YouTube', 'wpseed' ),
                        ),
                    ),
                    array(
                        'id'          => 'url',
                        'label'       => __( 'Profile URL', 'wpseed' ),
                        'type'        => 'url',
                        'placeholder' => __( 'https://...', 'wpseed' ),
                    ),
                ),
            ),

            array(
                'type' => 'sectionend',
                'id'   => 'repeater_examples'
            ),

        );

        return apply_filters( 'wpseed_repeater_settings', $settings );
    }
}

endif;

return new WPSeed_Settings_Repeater_Example();
