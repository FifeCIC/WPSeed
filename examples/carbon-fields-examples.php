<?php
/**
 * WPSeed Carbon Fields Examples
 * 
 * @package WPSeed/Examples
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Example 1: Theme Options Page
 */
add_action('wpseed_carbon_fields_register', 'wpseed_example_theme_options');
function wpseed_example_theme_options() {
    Container::make('theme_options', __('WPSeed Settings', 'wpseed'))
        ->set_page_parent('wpseed-settings')
        ->add_fields(array(
            Field::make('text', 'wpseed_site_title', __('Site Title', 'wpseed')),
            Field::make('textarea', 'wpseed_description', __('Description', 'wpseed')),
            Field::make('checkbox', 'wpseed_enable_feature', __('Enable Feature', 'wpseed')),
        ));
}

/**
 * Example 2: Post Meta Box
 */
add_action('wpseed_carbon_fields_register', 'wpseed_example_post_meta');
function wpseed_example_post_meta() {
    Container::make('post_meta', __('Additional Info', 'wpseed'))
        ->where('post_type', '=', 'post')
        ->add_fields(array(
            Field::make('text', 'wpseed_subtitle', __('Subtitle', 'wpseed')),
            Field::make('select', 'wpseed_priority', __('Priority', 'wpseed'))
                ->add_options(array(
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                )),
            Field::make('date', 'wpseed_deadline', __('Deadline', 'wpseed')),
        ));
}

/**
 * Example 3: Complex Fields
 */
add_action('wpseed_carbon_fields_register', 'wpseed_example_complex_fields');
function wpseed_example_complex_fields() {
    Container::make('theme_options', __('Advanced Settings', 'wpseed'))
        ->set_page_parent('wpseed-settings')
        ->add_tab(__('General', 'wpseed'), array(
            Field::make('text', 'wpseed_api_key', __('API Key', 'wpseed')),
            Field::make('select', 'wpseed_mode', __('Mode', 'wpseed'))
                ->add_options(array(
                    'development' => 'Development',
                    'production' => 'Production',
                )),
        ))
        ->add_tab(__('Social', 'wpseed'), array(
            Field::make('text', 'wpseed_facebook', __('Facebook URL', 'wpseed')),
            Field::make('text', 'wpseed_twitter', __('Twitter URL', 'wpseed')),
        ));
}

/**
 * Example 4: Repeater Fields
 */
add_action('wpseed_carbon_fields_register', 'wpseed_example_repeater');
function wpseed_example_repeater() {
    Container::make('theme_options', __('Team Members', 'wpseed'))
        ->set_page_parent('wpseed-settings')
        ->add_fields(array(
            Field::make('complex', 'wpseed_team_members', __('Team Members', 'wpseed'))
                ->add_fields(array(
                    Field::make('text', 'name', __('Name', 'wpseed')),
                    Field::make('text', 'position', __('Position', 'wpseed')),
                    Field::make('image', 'photo', __('Photo', 'wpseed')),
                    Field::make('textarea', 'bio', __('Bio', 'wpseed')),
                )),
        ));
}

/**
 * Example 5: Conditional Fields
 */
add_action('wpseed_carbon_fields_register', 'wpseed_example_conditional');
function wpseed_example_conditional() {
    Container::make('theme_options', __('Conditional Settings', 'wpseed'))
        ->set_page_parent('wpseed-settings')
        ->add_fields(array(
            Field::make('checkbox', 'wpseed_enable_api', __('Enable API', 'wpseed')),
            Field::make('text', 'wpseed_api_endpoint', __('API Endpoint', 'wpseed'))
                ->set_conditional_logic(array(
                    array(
                        'field' => 'wpseed_enable_api',
                        'value' => true,
                    )
                )),
        ));
}

/**
 * Example 6: Get Field Values
 */
function wpseed_get_carbon_value_example() {
    // Get theme option
    $site_title = carbon_get_theme_option('wpseed_site_title');
    
    // Get post meta
    $subtitle = carbon_get_post_meta(get_the_ID(), 'wpseed_subtitle');
    
    // Get term meta
    $term_color = carbon_get_term_meta(get_queried_object_id(), 'wpseed_color');
    
    // Get user meta
    $user_role = carbon_get_user_meta(get_current_user_id(), 'wpseed_role');
    
    return array(
        'site_title' => $site_title,
        'subtitle' => $subtitle,
        'term_color' => $term_color,
        'user_role' => $user_role,
    );
}

/**
 * Example 7: All Field Types
 */
add_action('wpseed_carbon_fields_register', 'wpseed_example_all_fields');
function wpseed_example_all_fields() {
    Container::make('theme_options', __('All Field Types', 'wpseed'))
        ->set_page_parent('wpseed-settings')
        ->add_fields(array(
            Field::make('text', 'cf_text', 'Text'),
            Field::make('textarea', 'cf_textarea', 'Textarea'),
            Field::make('rich_text', 'cf_rich_text', 'Rich Text'),
            Field::make('checkbox', 'cf_checkbox', 'Checkbox'),
            Field::make('radio', 'cf_radio', 'Radio')
                ->add_options(array('option1' => 'Option 1', 'option2' => 'Option 2')),
            Field::make('select', 'cf_select', 'Select')
                ->add_options(array('value1' => 'Label 1', 'value2' => 'Label 2')),
            Field::make('multiselect', 'cf_multiselect', 'Multi Select')
                ->add_options(array('val1' => 'Label 1', 'val2' => 'Label 2')),
            Field::make('image', 'cf_image', 'Image'),
            Field::make('file', 'cf_file', 'File'),
            Field::make('date', 'cf_date', 'Date'),
            Field::make('time', 'cf_time', 'Time'),
            Field::make('date_time', 'cf_datetime', 'Date Time'),
            Field::make('color', 'cf_color', 'Color'),
            Field::make('map', 'cf_map', 'Map'),
        ));
}
