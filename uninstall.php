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
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wpseed_%'");

// Delete transients
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_wpseed_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_wpseed_%'");

// Delete user meta
$wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'wpseed_%'");

// Clear scheduled hooks
wp_clear_scheduled_hook('wpseed_daily_cleanup');

// Flush rewrite rules
flush_rewrite_rules();