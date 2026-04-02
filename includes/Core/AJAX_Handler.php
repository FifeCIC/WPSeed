<?php
/**
 * AJAX event handler — custom endpoint detection, nonce verification, and dispatch.
 *
 * ROLE: ajax-handler
 *
 * Single responsibility: Detect WPSeed AJAX requests (both admin-ajax.php and
 * custom ?wpseed-ajax= endpoints), verify nonces, set appropriate headers, and
 * dispatch to registered action handlers. Does NOT implement any specific AJAX
 * actions — those are registered by other classes via the $ajax_events array.
 *
 * DEPENDS ON:
 *   - WordPress functions: add_action, sanitize_text_field, wp_verify_nonce
 *   - WordPress globals: $wp_query, $wpdb
 *
 * CONSUMED BY:
 *   - loader.php (calls AJAX_Handler::init() during bootstrap)
 *   - Any class that registers AJAX events via the $ajax_events array
 *
 * DATA FLOW:
 *   Input  → $_GET['wpseed-ajax'], $_GET['_wpnonce']
 *   Output → Fires wpseed_ajax_{action} action, sends headers, dies
 *
 * @package  WPSeed\Core
 * @since    1.0.0
 */

namespace WPSeed\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles WPSeed AJAX request detection, nonce verification, and dispatch.
 *
 * Single responsibility: AJAX infrastructure. Does NOT implement specific
 * AJAX actions — those are registered by domain-specific handler classes.
 *
 * @since 1.0.0
 */
class AJAX_Handler {

	/**
	 * Register AJAX detection and dispatch hooks.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
		add_action( 'template_redirect', array( __CLASS__, 'do_wpseed_ajax' ), 0 );
		self::add_ajax_events();
	}

	/**
	 * Get the WPSeed AJAX endpoint URL.
	 *
	 * @since  1.0.0
	 * @param  string $request Optional action name to append.
	 * @return string
	 */
	public static function get_endpoint( $request = '' ) {
		return esc_url_raw( apply_filters(
			'wpseed_ajax_get_endpoint',
			add_query_arg( 'wpseed-ajax', $request, remove_query_arg( array( 'remove_item', 'add-to-cart', 'added-to-cart' ) ) ),
			$request
		) );
	}

	/**
	 * Detect a WPSeed AJAX request and set constants/headers.
	 *
	 * Runs at init priority 0. Only detects whether a request is in progress —
	 * nonce verification happens in do_wpseed_ajax() after WordPress is fully
	 * bootstrapped.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function define_ajax() {
		$wpseed_ajax_action = isset( $_GET['wpseed-ajax'] )
			? sanitize_text_field( wp_unslash( $_GET['wpseed-ajax'] ) )
			: '';

		if ( ! empty( $wpseed_ajax_action ) ) {
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}
			if ( ! defined( 'WPSEED_DOING_AJAX' ) ) {
				define( 'WPSEED_DOING_AJAX', true );
			}
			if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
				@ini_set( 'display_errors', 0 ); // phpcs:ignore WordPress.PHP.IniSet.display_errors_Disallowed
			}
			$GLOBALS['wpdb']->hide_errors();
		}
	}

	/**
	 * Send appropriate headers for WPSeed AJAX responses.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	private static function wpseed_ajax_headers() {
		send_origin_headers();
		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		@header( 'X-Robots-Tag: noindex' );
		send_nosniff_header();
		nocache_headers();
		status_header( 200 );
	}

	/**
	 * Check for a WPSeed AJAX request, verify nonce, and fire the action.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function do_wpseed_ajax() {
		global $wp_query;

		if ( ! empty( $_GET['wpseed-ajax'] ) ) {
			if ( empty( $_GET['_wpnonce'] ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'wpseed' ) );
			}
			$wpseed_ajax_nonce = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );
			if ( ! wp_verify_nonce( $wpseed_ajax_nonce, 'wpseed-ajax-action' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'wpseed' ) );
			}
			$wp_query->set( 'wpseed-ajax', sanitize_text_field( wp_unslash( $_GET['wpseed-ajax'] ) ) );
		}

		if ( $action = $wp_query->get( 'wpseed-ajax' ) ) {
			self::wpseed_ajax_headers();
			do_action( 'wpseed_ajax_' . sanitize_text_field( $action ) );
			die();
		}
	}

	/**
	 * Register AJAX event handlers via admin-ajax.php.
	 *
	 * Add entries to the $ajax_events array as: 'event_name' => bool $nopriv.
	 * Each event maps to a static method on this class with the same name.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function add_ajax_events() {
		// Format: 'wpseed_EVENT' => nopriv (true = available to logged-out users)
		$ajax_events = array();

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_wpseed_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_wpseed_' . $ajax_event, array( __CLASS__, $ajax_event ) );
				add_action( 'wpseed_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}
}
