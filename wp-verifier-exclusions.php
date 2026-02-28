<?php
/**
 * WP Verifier Exclusions
 * 
 * Excludes WordPress.org specific checks that don't apply to open source projects
 * 
 * @package WPSeed
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Exclude WordPress.org specific checks for open source projects
 */
add_filter( 'wp_plugin_check_checks', 'wpseed_exclude_wporg_checks', 10, 1 );

function wpseed_exclude_wporg_checks( $checks ) {
    // Remove the trademarks check as it's WordPress.org specific
    if ( isset( $checks['trademarks'] ) ) {
        unset( $checks['trademarks'] );
    }
    
    return $checks;
}
