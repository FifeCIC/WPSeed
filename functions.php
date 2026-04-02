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
