<?php
/**
 * WPSeed Carbon Fields Premium Templates
 * 
 * Ready-to-use field templates for common use cases
 * 
 * @package WPSeed/Examples
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * E-Commerce Product Fields Template
 * 
 * Usage: wpseed_carbon_ecommerce_product_fields()
 */
function wpseed_carbon_ecommerce_product_fields() {
    Container::make('post_meta', __('Product Details', 'wpseed'))
        ->where('post_type', '=', 'product')
        ->add_tab(__('Pricing', 'wpseed'), array(
            Field::make('text', 'product_price', __('Price', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'product_sale_price', __('Sale Price', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('checkbox', 'product_on_sale', __('On Sale', 'wpseed')),
            Field::make('text', 'product_sku', __('SKU', 'wpseed')),
        ))
        ->add_tab(__('Inventory', 'wpseed'), array(
            Field::make('text', 'product_stock', __('Stock Quantity', 'wpseed'))
                ->set_attribute('type', 'number'),
            Field::make('checkbox', 'product_manage_stock', __('Manage Stock', 'wpseed')),
            Field::make('select', 'product_stock_status', __('Stock Status', 'wpseed'))
                ->add_options(array(
                    'instock' => 'In Stock',
                    'outofstock' => 'Out of Stock',
                    'onbackorder' => 'On Backorder',
                )),
        ))
        ->add_tab(__('Shipping', 'wpseed'), array(
            Field::make('text', 'product_weight', __('Weight (kg)', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'product_length', __('Length (cm)', 'wpseed'))
                ->set_attribute('type', 'number'),
            Field::make('text', 'product_width', __('Width (cm)', 'wpseed'))
                ->set_attribute('type', 'number'),
            Field::make('text', 'product_height', __('Height (cm)', 'wpseed'))
                ->set_attribute('type', 'number'),
        ))
        ->add_tab(__('Variations', 'wpseed'), array(
            Field::make('complex', 'product_variations', __('Product Variations', 'wpseed'))
                ->add_fields(array(
                    Field::make('text', 'variation_name', __('Variation Name', 'wpseed')),
                    Field::make('text', 'variation_price', __('Price', 'wpseed'))
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    Field::make('text', 'variation_sku', __('SKU', 'wpseed')),
                    Field::make('text', 'variation_stock', __('Stock', 'wpseed'))
                        ->set_attribute('type', 'number'),
                    Field::make('image', 'variation_image', __('Image', 'wpseed')),
                )),
        ));
}

/**
 * Membership/Subscription Fields Template
 * 
 * Usage: wpseed_carbon_membership_fields()
 */
function wpseed_carbon_membership_fields() {
    Container::make('post_meta', __('Membership Plan', 'wpseed'))
        ->where('post_type', '=', 'membership')
        ->add_fields(array(
            Field::make('text', 'membership_price', __('Price', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01')
                ->set_help_text('Monthly price'),
            Field::make('select', 'membership_billing_period', __('Billing Period', 'wpseed'))
                ->add_options(array(
                    'monthly' => 'Monthly',
                    'quarterly' => 'Quarterly',
                    'yearly' => 'Yearly',
                    'lifetime' => 'Lifetime',
                )),
            Field::make('text', 'membership_trial_days', __('Trial Period (Days)', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_default_value(0),
            Field::make('checkbox', 'membership_featured', __('Featured Plan', 'wpseed')),
            Field::make('complex', 'membership_features', __('Plan Features', 'wpseed'))
                ->add_fields(array(
                    Field::make('text', 'feature_name', __('Feature', 'wpseed')),
                    Field::make('checkbox', 'feature_included', __('Included', 'wpseed'))
                        ->set_default_value(true),
                    Field::make('text', 'feature_limit', __('Limit', 'wpseed'))
                        ->set_help_text('Leave empty for unlimited'),
                )),
            Field::make('complex', 'membership_restrictions', __('Access Restrictions', 'wpseed'))
                ->add_fields(array(
                    Field::make('select', 'restriction_type', __('Type', 'wpseed'))
                        ->add_options(array(
                            'post' => 'Posts',
                            'page' => 'Pages',
                            'category' => 'Categories',
                            'custom' => 'Custom Post Type',
                        )),
                    Field::make('text', 'restriction_ids', __('IDs (comma-separated)', 'wpseed')),
                )),
        ));
}

/**
 * Booking/Appointment Fields Template
 * 
 * Usage: wpseed_carbon_booking_fields()
 */
function wpseed_carbon_booking_fields() {
    Container::make('post_meta', __('Booking Settings', 'wpseed'))
        ->where('post_type', '=', 'booking')
        ->add_tab(__('Availability', 'wpseed'), array(
            Field::make('complex', 'booking_hours', __('Available Hours', 'wpseed'))
                ->add_fields(array(
                    Field::make('select', 'day_of_week', __('Day', 'wpseed'))
                        ->add_options(array(
                            'monday' => 'Monday',
                            'tuesday' => 'Tuesday',
                            'wednesday' => 'Wednesday',
                            'thursday' => 'Thursday',
                            'friday' => 'Friday',
                            'saturday' => 'Saturday',
                            'sunday' => 'Sunday',
                        )),
                    Field::make('time', 'start_time', __('Start Time', 'wpseed')),
                    Field::make('time', 'end_time', __('End Time', 'wpseed')),
                    Field::make('checkbox', 'is_available', __('Available', 'wpseed'))
                        ->set_default_value(true),
                )),
            Field::make('text', 'booking_duration', __('Duration (minutes)', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_default_value(60),
            Field::make('text', 'booking_buffer', __('Buffer Time (minutes)', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_default_value(0)
                ->set_help_text('Time between bookings'),
        ))
        ->add_tab(__('Pricing', 'wpseed'), array(
            Field::make('text', 'booking_price', __('Base Price', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('complex', 'booking_price_rules', __('Dynamic Pricing', 'wpseed'))
                ->add_fields(array(
                    Field::make('select', 'rule_type', __('Rule Type', 'wpseed'))
                        ->add_options(array(
                            'day_of_week' => 'Day of Week',
                            'time_of_day' => 'Time of Day',
                            'date_range' => 'Date Range',
                        )),
                    Field::make('text', 'rule_value', __('Value', 'wpseed'))
                        ->set_help_text('e.g., "monday" or "09:00-12:00"'),
                    Field::make('text', 'price_modifier', __('Price Modifier', 'wpseed'))
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    Field::make('select', 'modifier_type', __('Modifier Type', 'wpseed'))
                        ->add_options(array(
                            'fixed' => 'Fixed Amount',
                            'percentage' => 'Percentage',
                        )),
                )),
        ))
        ->add_tab(__('Capacity', 'wpseed'), array(
            Field::make('text', 'booking_max_capacity', __('Max Capacity', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_default_value(1),
            Field::make('text', 'booking_min_advance', __('Min Advance Booking (hours)', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_default_value(24),
            Field::make('text', 'booking_max_advance', __('Max Advance Booking (days)', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_default_value(90),
        ));
}

/**
 * Event Management Fields Template
 * 
 * Usage: wpseed_carbon_event_fields()
 */
function wpseed_carbon_event_fields() {
    Container::make('post_meta', __('Event Details', 'wpseed'))
        ->where('post_type', '=', 'event')
        ->add_fields(array(
            Field::make('date_time', 'event_start', __('Start Date & Time', 'wpseed')),
            Field::make('date_time', 'event_end', __('End Date & Time', 'wpseed')),
            Field::make('text', 'event_venue', __('Venue Name', 'wpseed')),
            Field::make('text', 'event_address', __('Address', 'wpseed')),
            Field::make('map', 'event_location', __('Location', 'wpseed'))
                ->set_help_text('Drag the marker to set location'),
            Field::make('text', 'event_capacity', __('Capacity', 'wpseed'))
                ->set_attribute('type', 'number'),
            Field::make('text', 'event_price', __('Ticket Price', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('checkbox', 'event_featured', __('Featured Event', 'wpseed')),
            Field::make('complex', 'event_speakers', __('Speakers', 'wpseed'))
                ->add_fields(array(
                    Field::make('text', 'speaker_name', __('Name', 'wpseed')),
                    Field::make('text', 'speaker_title', __('Title', 'wpseed')),
                    Field::make('image', 'speaker_photo', __('Photo', 'wpseed')),
                    Field::make('textarea', 'speaker_bio', __('Bio', 'wpseed')),
                )),
        ));
}

/**
 * Real Estate Property Fields Template
 * 
 * Usage: wpseed_carbon_property_fields()
 */
function wpseed_carbon_property_fields() {
    Container::make('post_meta', __('Property Details', 'wpseed'))
        ->where('post_type', '=', 'property')
        ->add_tab(__('Basic Info', 'wpseed'), array(
            Field::make('text', 'property_price', __('Price', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('select', 'property_type', __('Property Type', 'wpseed'))
                ->add_options(array(
                    'house' => 'House',
                    'apartment' => 'Apartment',
                    'condo' => 'Condo',
                    'townhouse' => 'Townhouse',
                    'land' => 'Land',
                )),
            Field::make('select', 'property_status', __('Status', 'wpseed'))
                ->add_options(array(
                    'for_sale' => 'For Sale',
                    'for_rent' => 'For Rent',
                    'sold' => 'Sold',
                    'rented' => 'Rented',
                )),
            Field::make('text', 'property_bedrooms', __('Bedrooms', 'wpseed'))
                ->set_attribute('type', 'number'),
            Field::make('text', 'property_bathrooms', __('Bathrooms', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.5'),
            Field::make('text', 'property_area', __('Area (sq ft)', 'wpseed'))
                ->set_attribute('type', 'number'),
        ))
        ->add_tab(__('Location', 'wpseed'), array(
            Field::make('text', 'property_address', __('Address', 'wpseed')),
            Field::make('text', 'property_city', __('City', 'wpseed')),
            Field::make('text', 'property_state', __('State/Province', 'wpseed')),
            Field::make('text', 'property_zip', __('ZIP/Postal Code', 'wpseed')),
            Field::make('map', 'property_map', __('Map Location', 'wpseed')),
        ))
        ->add_tab(__('Features', 'wpseed'), array(
            Field::make('set', 'property_features', __('Features', 'wpseed'))
                ->add_options(array(
                    'pool' => 'Swimming Pool',
                    'garage' => 'Garage',
                    'garden' => 'Garden',
                    'balcony' => 'Balcony',
                    'fireplace' => 'Fireplace',
                    'ac' => 'Air Conditioning',
                    'heating' => 'Central Heating',
                    'security' => 'Security System',
                )),
            Field::make('complex', 'property_amenities', __('Additional Amenities', 'wpseed'))
                ->add_fields(array(
                    Field::make('text', 'amenity_name', __('Amenity', 'wpseed')),
                    Field::make('textarea', 'amenity_description', __('Description', 'wpseed')),
                )),
        ))
        ->add_tab(__('Media', 'wpseed'), array(
            Field::make('media_gallery', 'property_gallery', __('Photo Gallery', 'wpseed')),
            Field::make('oembed', 'property_video', __('Video Tour', 'wpseed')),
        ));
}

/**
 * Course/Learning Management Fields Template
 * 
 * Usage: wpseed_carbon_course_fields()
 */
function wpseed_carbon_course_fields() {
    Container::make('post_meta', __('Course Details', 'wpseed'))
        ->where('post_type', '=', 'course')
        ->add_fields(array(
            Field::make('text', 'course_price', __('Price', 'wpseed'))
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('select', 'course_level', __('Difficulty Level', 'wpseed'))
                ->add_options(array(
                    'beginner' => 'Beginner',
                    'intermediate' => 'Intermediate',
                    'advanced' => 'Advanced',
                )),
            Field::make('text', 'course_duration', __('Duration (hours)', 'wpseed'))
                ->set_attribute('type', 'number'),
            Field::make('text', 'course_students', __('Max Students', 'wpseed'))
                ->set_attribute('type', 'number'),
            Field::make('complex', 'course_curriculum', __('Curriculum', 'wpseed'))
                ->add_fields(array(
                    Field::make('text', 'lesson_title', __('Lesson Title', 'wpseed')),
                    Field::make('textarea', 'lesson_description', __('Description', 'wpseed')),
                    Field::make('text', 'lesson_duration', __('Duration (minutes)', 'wpseed'))
                        ->set_attribute('type', 'number'),
                    Field::make('file', 'lesson_materials', __('Materials', 'wpseed')),
                    Field::make('checkbox', 'lesson_preview', __('Free Preview', 'wpseed')),
                )),
            Field::make('complex', 'course_instructors', __('Instructors', 'wpseed'))
                ->add_fields(array(
                    Field::make('text', 'instructor_name', __('Name', 'wpseed')),
                    Field::make('text', 'instructor_title', __('Title', 'wpseed')),
                    Field::make('image', 'instructor_photo', __('Photo', 'wpseed')),
                    Field::make('textarea', 'instructor_bio', __('Bio', 'wpseed')),
                )),
        ));
}

/**
 * Register all premium templates
 * Uncomment the ones you want to use
 */
add_action('wpseed_carbon_fields_register', 'wpseed_register_premium_templates');
function wpseed_register_premium_templates() {
    // Uncomment to enable:
    // wpseed_carbon_ecommerce_product_fields();
    // wpseed_carbon_membership_fields();
    // wpseed_carbon_booking_fields();
    // wpseed_carbon_event_fields();
    // wpseed_carbon_property_fields();
    // wpseed_carbon_course_fields();
}
