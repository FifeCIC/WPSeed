<?php
/**
 * WPSeed - Admin Only Functions
 *
 * @author   Ryan Bayne
 * @category Admin
 * @package  WPSeed/Admin
 * @since    1.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get all WordPress WPSeed screen ids.
 *
 * @return array
 */
function wpseed_get_screen_ids() {
    $screen_ids = array(
        'toplevel_page_wpseed',
        'wpseed_page_wpseed-settings',
    );

    return apply_filters( 'wpseed_screen_ids', $screen_ids );
}
