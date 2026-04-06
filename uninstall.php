<?php
/**
 * Uninstall WPSeed.
 *
 * Runs once when the plugin is deleted via the WordPress Plugins screen.
 * Direct $wpdb queries are used throughout because bulk DELETE by LIKE
 * pattern on wp_options and wp_usermeta has no WordPress API equivalent
 * (delete_option() and delete_user_meta() operate on exact keys only).
 * All queries use $wpdb->prepare() for dynamic values. Cache invalidation
 * is performed after each write group. No caching layer is added because
 * this script runs exactly once and exits immediately — caching a one-time
 * destructive operation would serve no purpose.
 *
 * @author      Ryan Bayne
 * @category    Core
 * @package     WPSeed/Uninstaller
 * @version     2.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

/**
 * Remove all plugin options from wp_options.
 *
 * delete_option() operates on exact keys only — it cannot remove all options
 * matching a prefix in a single call. A direct DELETE LIKE query is the only
 * available mechanism for bulk prefix-based removal.
 */
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
        $wpdb->esc_like( 'wpseed_' ) . '%'
    )
);
// Invalidate the alloptions cache so subsequent get_option() calls do not
// return stale data from the now-deleted rows.
wp_cache_delete( 'alloptions', 'options' );
wp_cache_delete( 'notoptions', 'options' );

/**
 * Remove all plugin transients from wp_options.
 *
 * delete_transient() operates on a single exact key. Removing all transients
 * sharing a prefix requires two direct DELETE LIKE queries — one for the
 * transient value rows and one for the corresponding timeout rows.
 */
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
        $wpdb->esc_like( '_transient_wpseed_' ) . '%'
    )
);
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
        $wpdb->esc_like( '_transient_timeout_wpseed_' ) . '%'
    )
);
// Flush the entire object cache after transient removal to ensure no
// in-memory copies of deleted transients survive the uninstall.
wp_cache_flush();

/**
 * Remove all plugin user meta from wp_usermeta.
 *
 * delete_user_meta() operates on a single user and exact key. Removing all
 * user meta sharing a prefix across all users requires a direct DELETE LIKE
 * query — there is no WordPress API equivalent for this operation.
 */
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
        $wpdb->esc_like( 'wpseed_' ) . '%'
    )
);
// Flush the object cache again after user meta removal so no cached user
// objects retain references to the deleted meta keys.
wp_cache_flush();

// Remove any scheduled cron events registered by the plugin.
wp_clear_scheduled_hook( 'wpseed_daily_cleanup' );

// Flush rewrite rules to remove any plugin-registered rewrite entries.
flush_rewrite_rules();
