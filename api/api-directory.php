<?php
/**
 * WPSeed API Directory — registry of all available connectors.
 *
 * ROLE: api-endpoint
 *
 * Single responsibility: Maintain a registry of connector providers and their
 * metadata. Supports both static (hardcoded) and runtime (plugin-registered)
 * providers. The Factory uses this to instantiate connectors; the admin UI
 * uses this to display the connector grid.
 *
 * DEPENDS ON:
 *   - WordPress functions: apply_filters, wp_parse_args
 *
 * CONSUMED BY:
 *   - WPSeed_API_Factory (looks up providers to instantiate)
 *   - Admin UI (displays connector grid with status)
 *   - REST Bridge (validates connector IDs)
 *   - wpseed_connector() global accessor in functions.php
 *
 * DATA FLOW:
 *   Input  → Static provider definitions + runtime registrations via filter
 *   Output → Provider metadata arrays
 *
 * @package  WPSeed
 * @category API
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registry of all available API connector providers.
 *
 * Connectors are registered in two ways:
 * 1. Static — hardcoded in get_default_providers() as boilerplate examples.
 * 2. Runtime — plugins call WPSeed_API_Directory::register() or use the
 *    'wpseed_api_providers' filter during init.
 *
 * @since 1.0.0
 * @since 3.1.0 Added runtime registration, get_provider_capabilities(),
 *              get_configured_providers(), is_registered().
 */
class WPSeed_API_Directory {

	/**
	 * Runtime-registered providers.
	 *
	 * @since 3.1.0
	 * @var   array<string, array>
	 */
	private static $registered = array();

	/**
	 * Cached merged provider list (static + runtime + filtered).
	 *
	 * @since 3.1.0
	 * @var   array<string, array>|null
	 */
	private static $cache = null;

	/**
	 * Register a connector provider at runtime.
	 *
	 * Call this during plugin init to make a connector available in the
	 * API Directory. The provider will appear in the admin UI and be
	 * instantiable via the Factory.
	 *
	 * @since  3.1.0
	 *
	 * @param  string $provider_id Unique provider identifier (e.g. 'github').
	 * @param  array  $args {
	 *     Provider configuration.
	 *
	 *     @type string $name        Human-readable name.
	 *     @type string $description Short description of what this connector does.
	 *     @type string $url         Provider's website URL.
	 *     @type string $api_doc_url Link to the provider's API documentation.
	 *     @type string $class_name  Fully-qualified class name (must implement Connector_Interface).
	 *     @type string $class_path  Path to the class file relative to plugin root.
	 *                               Empty if the class is autoloaded via Composer.
	 *     @type string $auth_type   Authentication type: 'bearer', 'api_key', 'basic',
	 *                               'oauth2', 'webhook_url', 'none'.
	 *     @type string $icon        Dashicon class or URL to icon image.
	 *     @type array  $features    Legacy feature flags (deprecated in favour of
	 *                               get_capabilities() on the connector instance).
	 * }
	 * @return void
	 */
	public static function register( $provider_id, $args = array() ) {
		$defaults = array(
			'name'        => '',
			'description' => '',
			'url'         => '',
			'api_doc_url' => '',
			'class_name'  => '',
			'class_path'  => '',
			'auth_type'   => 'bearer',
			'icon'        => 'dashicons-admin-generic',
			'features'    => array(),
		);

		self::$registered[ $provider_id ] = wp_parse_args( $args, $defaults );
		self::$cache                      = null; // Invalidate cache.
	}

	/**
	 * Return all registered providers (static + runtime + filtered).
	 *
	 * @since  1.0.0
	 * @since  3.1.0 Merged static, runtime, and filtered providers. Added caching.
	 *
	 * @return array<string, array> Providers keyed by provider ID.
	 */
	public static function get_all_providers() {
		if ( null !== self::$cache ) {
			return self::$cache;
		}

		$providers = array_merge(
			self::get_default_providers(),
			self::$registered
		);

		/**
		 * Filter the complete list of API connector providers.
		 *
		 * Plugins can add, remove, or modify providers via this filter.
		 * Runs after static and runtime providers are merged.
		 *
		 * @since 3.1.0
		 *
		 * @param array<string, array> $providers All registered providers.
		 */
		self::$cache = apply_filters( 'wpseed_api_providers', $providers );

		return self::$cache;
	}

	/**
	 * Return a single provider's configuration.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $provider_id Provider identifier.
	 * @return array|false Provider config array, or false if not found.
	 */
	public static function get_provider( $provider_id ) {
		$providers = self::get_all_providers();
		return $providers[ $provider_id ] ?? false;
	}

	/**
	 * Check whether a provider is registered.
	 *
	 * @since  3.1.0
	 *
	 * @param  string $provider_id Provider identifier.
	 * @return bool
	 */
	public static function is_registered( $provider_id ) {
		$providers = self::get_all_providers();
		return isset( $providers[ $provider_id ] );
	}

	/**
	 * Return only providers that have credentials configured.
	 *
	 * Checks wp_options for a non-empty API key for each provider.
	 * Used by the admin UI to distinguish "configured" from "available".
	 *
	 * @since  3.1.0
	 *
	 * @return array<string, array> Configured providers keyed by provider ID.
	 */
	public static function get_configured_providers() {
		$all        = self::get_all_providers();
		$configured = array();

		foreach ( $all as $provider_id => $provider ) {
			$key = get_option( "wpseed_api_{$provider_id}_key", '' );
			if ( ! empty( $key ) ) {
				$configured[ $provider_id ] = $provider;
			}
		}

		return $configured;
	}

	/**
	 * Return the capabilities of a specific provider.
	 *
	 * Instantiates the connector (without credentials) and calls
	 * get_capabilities(). Returns an empty array if the connector
	 * cannot be instantiated.
	 *
	 * @since  3.1.0
	 *
	 * @param  string $provider_id Provider identifier.
	 * @return array<string, array> Capabilities keyed by action name.
	 */
	public static function get_provider_capabilities( $provider_id ) {
		$instance = WPSeed_API_Factory::create( $provider_id );

		if ( is_wp_error( $instance ) ) {
			return array();
		}

		return $instance->get_capabilities();
	}

	/**
	 * Return the default (static) provider definitions.
	 *
	 * These are boilerplate examples shipped with WPSeed. When cloning to
	 * a new plugin, replace or remove these with the plugin's own connectors.
	 *
	 * @since  1.0.0
	 * @since  3.1.0 Added icon, auth_type defaults. Structured for Connector_Interface.
	 *
	 * @return array<string, array> Default providers keyed by provider ID.
	 */
	private static function get_default_providers() {
		return array(
			'custom_api' => array(
				'name'        => 'Custom API',
				'description' => 'Generic REST API integration.',
				'url'         => '',
				'api_doc_url' => '',
				'class_path'  => 'custom/custom-api.php',
				'class_name'  => 'WPSeed_Custom_API',
				'auth_type'   => 'api_key',
				'icon'        => 'dashicons-admin-generic',
				'features'    => array( 'data_retrieval' => true ),
			),
			'discord'    => array(
				'name'        => 'Discord Webhook',
				'description' => 'Send notifications via Discord webhooks.',
				'url'         => 'https://discord.com/',
				'api_doc_url' => 'https://discord.com/developers/docs/resources/webhook',
				'class_path'  => 'discord/discord-api.php',
				'class_name'  => 'WPSeed_Discord_API',
				'auth_type'   => 'webhook_url',
				'icon'        => 'dashicons-format-chat',
				'features'    => array( 'notifications' => true ),
			),
		);
	}

	/**
	 * Reset the internal cache.
	 *
	 * Used during testing or when providers are re-registered.
	 *
	 * @since  3.1.0
	 *
	 * @return void
	 */
	public static function reset() {
		self::$cache = null;
	}
}
