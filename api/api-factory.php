<?php
/**
 * WPSeed API Factory — creates connector instances.
 *
 * ROLE: api-endpoint
 *
 * Single responsibility: Instantiate connector objects from the API Directory,
 * injecting credentials from wp_options. Validates that every instance
 * implements Connector_Interface.
 *
 * DEPENDS ON:
 *   - WPSeed_API_Directory (looks up provider metadata)
 *   - WPSeed\API\Connector_Interface (validates instances)
 *   - WordPress functions: get_option, is_wp_error
 *
 * CONSUMED BY:
 *   - wpseed_connector() global accessor in functions.php
 *   - Admin UI "Test Connection" handler
 *   - REST Bridge (creates connector to execute actions)
 *   - Any code that needs an API connector instance
 *
 * DATA FLOW:
 *   Input  → Provider ID + optional credentials array
 *   Output → Connector instance (implements Connector_Interface) or WP_Error
 *
 * @package  WPSeed
 * @category API
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Factory for creating API connector instances.
 *
 * Looks up the provider in the API Directory, loads the class file if needed,
 * validates the instance implements Connector_Interface, and returns it.
 *
 * @since 1.0.0
 * @since 3.1.0 Validates Connector_Interface. Added multi-account support,
 *              instance caching, create_all().
 */
class WPSeed_API_Factory {

	/**
	 * Cached connector instances keyed by "provider_id:account_id".
	 *
	 * @since 3.1.0
	 * @var   array<string, \WPSeed\API\Connector_Interface>
	 */
	private static $instances = array();

	/**
	 * Create a connector instance with explicit credentials.
	 *
	 * @since  1.0.0
	 * @since  3.1.0 Validates Connector_Interface. Supports instance caching.
	 *
	 * @param  string $provider_id Provider identifier from the API Directory.
	 * @param  array  $args {
	 *     Optional. Connection arguments. If omitted, use create_from_settings().
	 *
	 *     @type string $api_key    API key or access token.
	 *     @type string $api_secret API secret.
	 *     @type string $base_url   Base URL for API requests.
	 *     @type string $account_id Account identifier for multi-account setups.
	 * }
	 * @return \WPSeed\API\Connector_Interface|\WP_Error Connector instance or error.
	 */
	public static function create( $provider_id, $args = array() ) {
		$provider = WPSeed_API_Directory::get_provider( $provider_id );

		if ( ! $provider ) {
			return new \WP_Error(
				'unknown_provider',
				sprintf(
					/* translators: %s: provider ID */
					__( 'Unknown API provider: %s', 'wpseed' ),
					$provider_id
				)
			);
		}

		// Load the class file if a path is specified and class isn't autoloaded.
		if ( ! empty( $provider['class_path'] ) && ! class_exists( $provider['class_name'], false ) ) {
			$class_file = WPSEED_PLUGIN_DIR_PATH . 'api/' . $provider['class_path'];

			if ( ! file_exists( $class_file ) ) {
				return new \WP_Error(
					'missing_class_file',
					sprintf(
						/* translators: 1: file path, 2: provider ID */
						__( 'Connector class file not found: %1$s (provider: %2$s)', 'wpseed' ),
						$provider['class_path'],
						$provider_id
					)
				);
			}

			require_once $class_file;
		}

		// Check class exists (may be autoloaded via Composer).
		if ( ! class_exists( $provider['class_name'] ) ) {
			return new \WP_Error(
				'missing_class',
				sprintf(
					/* translators: 1: class name, 2: provider ID */
					__( 'Connector class not found: %1$s (provider: %2$s)', 'wpseed' ),
					$provider['class_name'],
					$provider_id
				)
			);
		}

		try {
			$instance = new $provider['class_name']( $provider_id, $args );
		} catch ( \Exception $e ) {
			return new \WP_Error(
				'instantiation_error',
				sprintf(
					/* translators: 1: provider ID, 2: error message */
					__( 'Failed to create %1$s connector: %2$s', 'wpseed' ),
					$provider_id,
					$e->getMessage()
				)
			);
		}

		if ( ! ( $instance instanceof \WPSeed\API\Connector_Interface ) ) {
			return new \WP_Error(
				'invalid_connector',
				sprintf(
					/* translators: 1: class name, 2: interface name */
					__( '%1$s must implement %2$s.', 'wpseed' ),
					$provider['class_name'],
					'WPSeed\\API\\Connector_Interface'
				)
			);
		}

		return $instance;
	}

	/**
	 * Create a connector instance using credentials stored in wp_options.
	 *
	 * Loads credentials from the standard option naming convention:
	 *   wpseed_api_{provider_id}_{field}           (default account)
	 *   wpseed_api_{provider_id}_{account_id}_{field}  (named account)
	 *
	 * @since  1.0.0
	 * @since  3.1.0 Added $account_id parameter for multi-account support.
	 *
	 * @param  string $provider_id Provider identifier.
	 * @param  string $account_id  Optional. Account identifier for multi-account
	 *                             setups. Empty string for the default account.
	 * @return \WPSeed\API\Connector_Interface|\WP_Error Connector instance or error.
	 */
	public static function create_from_settings( $provider_id, $account_id = '' ) {
		$cache_key = $provider_id . ':' . $account_id;

		if ( isset( self::$instances[ $cache_key ] ) ) {
			return self::$instances[ $cache_key ];
		}

		$prefix = empty( $account_id )
			? "wpseed_api_{$provider_id}"
			: "wpseed_api_{$provider_id}_{$account_id}";

		$args = array(
			'api_key'    => get_option( "{$prefix}_key", '' ),
			'api_secret' => get_option( "{$prefix}_secret", '' ),
			'base_url'   => get_option( "{$prefix}_url", '' ),
			'account_id' => $account_id,
		);

		/**
		 * Filter the credentials before creating a connector instance.
		 *
		 * Allows plugins to inject credentials from alternative sources
		 * (environment variables, secrets manager, etc.).
		 *
		 * @since 3.1.0
		 *
		 * @param array  $args        Credential arguments.
		 * @param string $provider_id Provider identifier.
		 * @param string $account_id  Account identifier (empty for default).
		 */
		$args = apply_filters( 'wpseed_connector_credentials', $args, $provider_id, $account_id );

		$instance = self::create( $provider_id, $args );

		if ( ! is_wp_error( $instance ) ) {
			self::$instances[ $cache_key ] = $instance;
		}

		return $instance;
	}

	/**
	 * Create instances for all configured providers.
	 *
	 * Returns only successfully created instances. Errors are silently skipped.
	 * Used by the admin dashboard to show connector status.
	 *
	 * @since  3.1.0
	 *
	 * @return array<string, \WPSeed\API\Connector_Interface> Keyed by provider ID.
	 */
	public static function create_all_configured() {
		$configured = WPSeed_API_Directory::get_configured_providers();
		$instances  = array();

		foreach ( $configured as $provider_id => $provider ) {
			$instance = self::create_from_settings( $provider_id );
			if ( ! is_wp_error( $instance ) ) {
				$instances[ $provider_id ] = $instance;
			}
		}

		return $instances;
	}

	/**
	 * Clear the instance cache.
	 *
	 * Used during testing or when credentials change.
	 *
	 * @since  3.1.0
	 *
	 * @return void
	 */
	public static function reset() {
		self::$instances = array();
	}
}
