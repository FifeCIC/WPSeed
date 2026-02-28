<?php
/**
 * Uninstall plugin.
 *
 * @author      Ryan Bayne
 * @category    Core
 * @package     WPSeed/Uninstaller
 * @version     1.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb, $wp_version;

// Delete options
$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $wpdb->esc_like( 'wpseed_' ) . '%' ) );
wp_cache_delete_multiple( array( 'alloptions', 'wpseed_settings' ) );

// Delete transients
$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $wpdb->esc_like( '_transient_wpseed_' ) . '%' ) );
$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $wpdb->esc_like( '_transient_timeout_wpseed_' ) . '%' ) );
wp_cache_flush();

// Delete user meta
$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s", $wpdb->esc_like( 'wpseed_' ) . '%' ) );
wp_cache_delete_multiple( array( 'user_meta' ) );

// Clear scheduled hooks
wp_clear_scheduled_hook('wpseed_daily_cleanup');

// Flush rewrite rules
flush_rewrite_rules();