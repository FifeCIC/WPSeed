<?php
/**
 * REST Bridge — standardised endpoint registration and connector routing.
 *
 * ROLE: api-endpoint
 *
 * Single responsibility: Provide a central registry for REST endpoints with
 * metadata, capability-based permission checks, and automatic route generation
 * for API connectors. Every REST endpoint in the plugin should be registered
 * through this class so the ecosystem can discover what's available.
 *
 * DEPENDS ON:
 *   - WordPress functions: register_rest_route, current_user_can, rest_ensure_response,
 *     is_wp_error, add_action, apply_filters, do_action
 *   - WPSeed\Core\Capability_Manager (for wpseed_user_can checks)
 *   - WPSeed_API_Factory (for connector route generation)
 *   - WPSeed_API_Directory (for connector capability discovery)
 *
 * CONSUMED BY:
 *   - WPSeed\API\REST_Controller::register_endpoint() (convenience wrapper)
 *   - Concrete controller classes (register their endpoints here)
 *   - EvolveWP Core Feature Gate (queries registered endpoints)
 *   - Amazon Q (discovers available actions via get_registered_endpoints)
 *   - Admin UI (shows endpoint catalogue on development tabs)
 *
 * DATA FLOW:
 *   Input  → Endpoint definitions via register(), connector IDs via register_connector_routes()
 *   Output → WordPress REST routes registered, endpoint metadata in static registry
 *
 * @package  WPSeed\API
 * @category API
 * @since    3.1.0
 */

namespace WPSeed\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Central registry and factory for REST API endpoints.
 *
 * Wraps register_rest_route() with consistent capability checks, metadata
 * tracking, and automatic connector-to-REST route generation. Does NOT
 * wrap or intercept response data — callbacks return whatever they want.
 * Use the static helper methods for consistent response formatting.
 *
 * @since 3.1.0
 */
class REST_Bridge {

	/**
	 * Registered endpoint metadata.
	 *
	 * @since 3.1.0
	 * @var   array<string, array{
	 *     namespace: string,
	 *     route: string,
	 *     method: string,
	 *     callback: callable,
	 *     capability: string,
	 *     label: string,
	 *     description: string,
	 *     args: array,
	 *     source: string,
	 * }>
	 */
	private static $endpoints = array();

	/**
	 * Whether the rest_api_init hook has been attached.
	 *
	 * @since 3.1.0
	 * @var   bool
	 */
	private static $hooked = false;

	/**
	 * Register a REST endpoint with metadata.
	 *
	 * Call this during plugin init. The actual WordPress route registration
	 * is deferred to the rest_api_init hook automatically.
	 *
	 * @since  3.1.0
	 *
	 * @param  string $namespace REST namespace (e.g. 'wpseed/v1').
	 * @param  string $route     Route path (e.g. '/status').
	 * @param  array  $args {
	 *     Endpoint configuration.
	 *
	 *     @type string   $method      HTTP method: GET, POST, PUT, PATCH, DELETE.
	 *                                 Supports pipe-separated: 'GET|POST'. Default 'GET'.
	 *     @type callable $callback    Handler function. Receives WP_REST_Request.
	 *     @type string   $capability  Capability required. Checked via wpseed_user_can().
	 *                                 Use 'public' for unauthenticated access.
	 *     @type string   $label       Human-readable endpoint name for documentation.
	 *     @type string   $description What this endpoint does.
	 *     @type array    $args        WordPress REST argument definitions for validation.
	 * }
	 * @return void
	 */
	public static function register( $namespace, $route, $args = array() ) {
		$defaults = array(
			'method'      => 'GET',
			'callback'    => null,
			'capability'  => 'manage_options',
			'label'       => '',
			'description' => '',
			'args'        => array(),
		);

		$args = wp_parse_args( $args, $defaults );
		$key  = $namespace . ':' . $route . ':' . $args['method'];

		self::$endpoints[ $key ] = array(
			'namespace'   => $namespace,
			'route'       => $route,
			'method'      => $args['method'],
			'callback'    => $args['callback'],
			'capability'  => $args['capability'],
			'label'       => $args['label'],
			'description' => $args['description'],
			'args'        => $args['args'],
			'source'      => 'manual',
		);

		self::ensure_hooked();
	}

	/**
	 * Register REST routes for all actions of a connector.
	 *
	 * Auto-generates endpoints from the connector's get_capabilities():
	 *   POST /wp-json/{namespace}/connector/{provider_id}/{action}
	 *   GET  /wp-json/{namespace}/connector/{provider_id}/test
	 *   GET  /wp-json/{namespace}/connector/{provider_id}/capabilities
	 *
	 * @since  3.1.0
	 *
	 * @param  string $namespace   REST namespace (e.g. 'wpseed/v1').
	 * @param  string $provider_id Connector provider ID (e.g. 'github').
	 * @param  array  $args {
	 *     Optional. Override defaults.
	 *
	 *     @type string $capability_execute Capability for execute actions.
	 *                                      Default 'wpseed_execute_connectors'.
	 *     @type string $capability_manage  Capability for test/capabilities.
	 *                                      Default 'wpseed_manage_connectors'.
	 * }
	 * @return void
	 */
	public static function register_connector_routes( $namespace, $provider_id, $args = array() ) {
		$defaults = array(
			'capability_execute' => 'wpseed_execute_connectors',
			'capability_manage'  => 'wpseed_manage_connectors',
		);

		$args = wp_parse_args( $args, $defaults );

		// Test connection endpoint.
		self::register( $namespace, '/connector/' . $provider_id . '/test', array(
			'method'      => 'GET',
			'callback'    => self::make_connector_callback( $provider_id, '__test_connection' ),
			'capability'  => $args['capability_manage'],
			'label'       => ucfirst( $provider_id ) . ': Test Connection',
			'description' => 'Test the ' . $provider_id . ' connector credentials.',
		) );
		self::$endpoints[ array_key_last( self::$endpoints ) ]['source'] = 'connector';

		// Capabilities endpoint.
		self::register( $namespace, '/connector/' . $provider_id . '/capabilities', array(
			'method'      => 'GET',
			'callback'    => self::make_connector_callback( $provider_id, '__get_capabilities' ),
			'capability'  => $args['capability_manage'],
			'label'       => ucfirst( $provider_id ) . ': List Capabilities',
			'description' => 'List available actions for the ' . $provider_id . ' connector.',
		) );
		self::$endpoints[ array_key_last( self::$endpoints ) ]['source'] = 'connector';

		// Execute action endpoint (single route, action in URL).
		self::register( $namespace, '/connector/' . $provider_id . '/(?P<action>[a-z_]+)', array(
			'method'      => 'POST',
			'callback'    => self::make_connector_callback( $provider_id, '__execute' ),
			'capability'  => $args['capability_execute'],
			'label'       => ucfirst( $provider_id ) . ': Execute Action',
			'description' => 'Execute a named action on the ' . $provider_id . ' connector.',
			'args'        => array(
				'action' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_key',
					'description'       => 'Action name from get_capabilities().',
				),
			),
		) );
		self::$endpoints[ array_key_last( self::$endpoints ) ]['source'] = 'connector';
	}

	/**
	 * Return all registered endpoint metadata.
	 *
	 * Used by the admin UI, Feature Gate, and AI tools to discover
	 * what REST endpoints are available.
	 *
	 * @since  3.1.0
	 *
	 * @param  string $source Optional. Filter by source: 'manual', 'connector', or empty for all.
	 * @return array<string, array> Endpoints keyed by namespace:route:method.
	 */
	public static function get_registered_endpoints( $source = '' ) {
		if ( empty( $source ) ) {
			return self::$endpoints;
		}

		return array_filter(
			self::$endpoints,
			static function ( $endpoint ) use ( $source ) {
				return $endpoint['source'] === $source;
			}
		);
	}

	/**
	 * Build a standardised success response.
	 *
	 * Opt-in helper for callbacks that want consistent formatting.
	 * Callbacks are NOT required to use this — they can return any
	 * WP_REST_Response or data array they want.
	 *
	 * @since  3.1.0
	 *
	 * @param  mixed $data    Response data.
	 * @param  int   $status  HTTP status code. Default 200.
	 * @return \WP_REST_Response
	 */
	public static function success( $data = null, $status = 200 ) {
		return new \WP_REST_Response(
			array(
				'success' => true,
				'data'    => $data,
			),
			$status
		);
	}

	/**
	 * Build a standardised error response.
	 *
	 * Opt-in helper for callbacks. Accepts a WP_Error or raw parameters.
	 *
	 * @since  3.1.0
	 *
	 * @param  string|\WP_Error $code    Error code string or WP_Error object.
	 * @param  string           $message Error message (ignored if $code is WP_Error).
	 * @param  int              $status  HTTP status code. Default 400.
	 * @return \WP_REST_Response
	 */
	public static function error( $code, $message = '', $status = 400 ) {
		if ( is_wp_error( $code ) ) {
			$error_data = $code->get_error_data();
			$status     = $error_data['status'] ?? $status;
			$message    = $code->get_error_message();
			$code       = $code->get_error_code();
		}

		return new \WP_REST_Response(
			array(
				'success' => false,
				'error'   => array(
					'code'    => $code,
					'message' => $message,
				),
			),
			$status
		);
	}

	/**
	 * Hook into rest_api_init to register all queued endpoints.
	 *
	 * @since  3.1.0
	 * @return void
	 */
	private static function ensure_hooked() {
		if ( self::$hooked ) {
			return;
		}

		add_action( 'rest_api_init', array( static::class, 'register_all_routes' ) );
		self::$hooked = true;
	}

	/**
	 * Register all queued endpoints with WordPress.
	 *
	 * Called on rest_api_init. Iterates the registry and calls
	 * register_rest_route() for each endpoint.
	 *
	 * @since  3.1.0
	 * @return void
	 */
	public static function register_all_routes() {
		/**
		 * Fires before REST Bridge registers its routes.
		 *
		 * Last chance for plugins to call REST_Bridge::register() or
		 * REST_Bridge::register_connector_routes().
		 *
		 * @since 3.1.0
		 */
		do_action( 'wpseed_rest_bridge_before_register' );

		foreach ( self::$endpoints as $key => $endpoint ) {
			if ( null === $endpoint['callback'] ) {
				continue;
			}

			$capability = $endpoint['capability'];

			register_rest_route( $endpoint['namespace'], $endpoint['route'], array(
				'methods'             => $endpoint['method'],
				'callback'            => $endpoint['callback'],
				'permission_callback' => self::make_permission_callback( $capability ),
				'args'                => $endpoint['args'],
			) );
		}

		/**
		 * Fires after REST Bridge has registered all routes.
		 *
		 * @since 3.1.0
		 *
		 * @param array $endpoints All registered endpoint metadata.
		 */
		do_action( 'wpseed_rest_bridge_registered', self::$endpoints );
	}

	/**
	 * Build a permission callback for a capability string.
	 *
	 * @since  3.1.0
	 *
	 * @param  string $capability Capability name, or 'public' for open access.
	 * @return callable
	 */
	private static function make_permission_callback( $capability ) {
		if ( 'public' === $capability ) {
			return '__return_true';
		}

		return static function () use ( $capability ) {
			return \wpseed_user_can( $capability );
		};
	}

	/**
	 * Build a callback closure for connector routes.
	 *
	 * The $internal_action parameter determines which connector method to call:
	 * - '__test_connection' → connector->test_connection()
	 * - '__get_capabilities' → connector->get_capabilities()
	 * - '__execute' → connector->execute( $action, $params )
	 *
	 * @since  3.1.0
	 *
	 * @param  string $provider_id     Connector provider ID.
	 * @param  string $internal_action Internal routing key.
	 * @return callable
	 */
	private static function make_connector_callback( $provider_id, $internal_action ) {
		return static function ( \WP_REST_Request $request ) use ( $provider_id, $internal_action ) {
			$connector = \WPSeed_API_Factory::create_from_settings( $provider_id );

			if ( is_wp_error( $connector ) ) {
				return REST_Bridge::error( $connector, '', 503 );
			}

			switch ( $internal_action ) {
				case '__test_connection':
					$result = $connector->test_connection();
					return REST_Bridge::success( $result );

				case '__get_capabilities':
					$result = $connector->get_capabilities();
					return REST_Bridge::success( $result );

				case '__execute':
					$action = $request->get_param( 'action' );
					$params = $request->get_json_params();
					unset( $params['action'] );

					$result = $connector->execute( $action, $params );

					if ( is_wp_error( $result ) ) {
						return REST_Bridge::error( $result );
					}

					return REST_Bridge::success( $result );

				default:
					return REST_Bridge::error( 'invalid_action', 'Unknown internal action.', 500 );
			}
		};
	}

	/**
	 * Reset the internal registry.
	 *
	 * Used during testing.
	 *
	 * @since  3.1.0
	 *
	 * @return void
	 */
	public static function reset() {
		self::$endpoints = array();
		self::$hooked    = false;
	}
}
