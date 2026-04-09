<?php
/**
 * WPSeed global helper functions.
 *
 * ROLE: utility
 *
 * Global accessor functions that provide convenient shorthand for namespaced
 * classes. These functions are intentionally global (no namespace) so any
 * code — including third-party plugins — can call them without a use statement.
 *
 * CONSUMED BY:
 *   - Any plugin or template that needs the ecosystem registry or main instance.
 *
 * @package  WPSeed
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the main WPSeed plugin instance.
 *
 * Shorthand for WordPressPluginSeed::instance(). Prevents the need to use
 * globals anywhere in the codebase.
 *
 * @since  1.0.0
 * @return WordPressPluginSeed
 */
function WPSeed() {
	return WordPressPluginSeed::instance();
}

/**
 * Return the ecosystem Registry singleton.
 *
 * Global accessor for \WPSeed\Ecosystem\Registry. The class is namespaced and
 * autoloaded via Composer; this function keeps call sites clean.
 *
 * @since  3.0.0
 * @return \WPSeed\Ecosystem\Registry
 */
function wpseed_ecosystem() {
	return \WPSeed\Ecosystem\Registry::instance();
}

/**
 * Return the structured Logger singleton.
 *
 * Global accessor for \WPSeed\Core\Logger.
 *
 * @since  3.0.0
 * @return \WPSeed\Core\Logger
 */
function wpseed_log() {
	return \WPSeed\Core\Logger::instance();
}

/**
 * Record a trace entry via the structured Logger.
 *
 * Convenience shorthand for wpseed_log()->trace().
 *
 * @since  3.0.0
 * @param  string $type    Trace type.
 * @param  string $message Description.
 * @param  array  $data    Optional structured data.
 * @return void
 */
function wpseed_trace( $type, $message, $data = array() ) {
	\WPSeed\Core\Logger::instance()->trace( $type, $message, $data );
}

/**
 * Create or retrieve an API connector instance.
 *
 * Global accessor for WPSeed_API_Factory::create_from_settings(). Returns
 * a connector loaded with credentials from wp_options.
 *
 * Usage:
 *
 *     $github = wpseed_connector( 'github' );
 *     if ( ! is_wp_error( $github ) ) {
 *         $result = $github->execute( 'list_repos' );
 *     }
 *
 * Multi-account:
 *
 *     $github_org = wpseed_connector( 'github', 'my-org' );
 *
 * @since  3.1.0
 *
 * @param  string $provider_id Provider identifier (e.g. 'github', 'discord').
 * @param  string $account_id  Optional. Account identifier for multi-account setups.
 * @return \WPSeed\API\Connector_Interface|\WP_Error Connector instance or error.
 */
function wpseed_connector( $provider_id, $account_id = '' ) {
	return WPSeed_API_Factory::create_from_settings( $provider_id, $account_id );
}

/**
 * Check whether a user has a specific capability.
 *
 * Global accessor for \WPSeed\Core\Capability_Manager::user_can(). This is
 * the standard way to check permissions throughout the plugin. Use this
 * instead of current_user_can() for any plugin-specific capability.
 *
 * Usage:
 *
 *     if ( wpseed_user_can( 'wpseed_manage_settings' ) ) {
 *         // show settings page
 *     }
 *
 *     // Check for a specific user.
 *     if ( wpseed_user_can( 'wpseed_view_logs', $user_id ) ) {
 *         // show logs
 *     }
 *
 * @since  3.1.0
 *
 * @param  string   $capability Capability name to check.
 * @param  int|null $user_id    User ID to check. Null = current user.
 * @return bool     True if the user has the capability.
 */
function wpseed_user_can( $capability, $user_id = null ) {
	return \WPSeed\Core\Capability_Manager::user_can( $capability, $user_id );
}
