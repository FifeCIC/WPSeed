<?php
/**
 * ACF Integration Example
 * 
 * @package WPSeed/Examples/Integrations
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_ACF_Integration {
    
    public function __construct() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        
        add_action('acf/init', array($this, 'register_field_groups'));
    }
    
    public function register_field_groups() {
        acf_add_local_field_group(array(
            'key' => 'group_wpseed_post_meta',
            'title' => 'WPSeed Custom Fields',
            'fields' => array(
                array(
                    'key' => 'field_wpseed_text',
                    'label' => 'Custom Text',
                    'name' => 'wpseed_custom_text',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_wpseed_select',
                    'label' => 'Custom Select',
                    'name' => 'wpseed_custom_select',
                    'type' => 'select',
                    'choices' => array(
                        'option1' => 'Option 1',
                        'option2' => 'Option 2',
                    ),
                ),
                array(
                    'key' => 'field_wpseed_image',
                    'label' => 'Custom Image',
                    'name' => 'wpseed_custom_image',
                    'type' => 'image',
                    'return_format' => 'array',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post',
                    ),
                ),
            ),
        ));
    }
}

new WPSeed_ACF_Integration();

function wpseed_get_custom_text($post_id = null) {
    return get_field('wpseed_custom_text', $post_id);
}
