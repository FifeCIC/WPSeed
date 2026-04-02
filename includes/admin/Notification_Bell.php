<?php
/**
 * Admin bar notification bell indicator.
 *
 * ROLE: admin-ui
 *
 * Single responsibility: Add a notification bell icon to the WordPress admin
 * bar showing the unread notification count. Does NOT manage notifications
 * themselves — that is WPSeed_Notifications.
 *
 * DEPENDS ON:
 *   - WPSeed_Notifications::get_unread_count() (not yet migrated)
 *
 * CONSUMED BY:
 *   - Hook: admin_bar_menu (priority 999)
 *   - Hook: admin_enqueue_scripts
 *
 * DATA FLOW:
 *   Input  → WPSeed_Notifications unread count
 *   Output → Admin bar node HTML
 *
 * @package  WPSeed\Admin
 * @since    1.0.0
 */

namespace WPSeed\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds a notification bell to the WordPress admin bar.
 *
 * @since 1.0.0
 */
class Notification_Bell {

	/**
	 * Register hooks.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_bar_menu', array( __CLASS__, 'add_notification_bell' ), 999 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}

	/**
	 * Add the notification bell node to the admin bar.
	 *
	 * @since  1.0.0
	 * @param  \WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 * @return void
	 */
	public static function add_notification_bell( $wp_admin_bar ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$unread_count = \WPSeed_Notifications::get_unread_count( get_current_user_id() );

		$wp_admin_bar->add_node( array(
			'id'    => 'wpseed-notifications',
			'title' => self::get_bell_html( $unread_count ),
			'href'  => admin_url( 'admin.php?page=wpseed-notifications' ),
			'meta'  => array( 'class' => 'wpseed-notification-bell' ),
		) );
	}

	/**
	 * Build the bell icon HTML with optional count badge.
	 *
	 * @since  1.0.0
	 * @param  int $count Unread notification count.
	 * @return string
	 */
	private static function get_bell_html( $count ) {
		$html = '<span class="ab-icon dashicons dashicons-bell"></span>';
		if ( $count > 0 ) {
			$html .= '<span class="wpseed-notification-count">' . (int) $count . '</span>';
		}
		return $html;
	}

	/**
	 * Enqueue inline CSS for the notification bell.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function enqueue_assets() {
		wp_add_inline_style( 'admin-bar', '
			#wp-admin-bar-wpseed-notifications .ab-icon { font-size: 20px; margin-top: 2px; }
			.wpseed-notification-count { display: inline-block; background: #d63638; color: #fff; border-radius: 10px; padding: 2px 6px; font-size: 11px; font-weight: 600; margin-left: 5px; line-height: 1; }
			#wp-admin-bar-wpseed-notifications:hover .ab-icon { color: #00a0d2; }
		' );
	}
}
