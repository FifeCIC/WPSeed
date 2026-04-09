<?php
/**
 * Abstract base class for external API connectors.
 *
 * ROLE: api-endpoint
 *
 * Single responsibility: Provide HTTP request infrastructure, authentication,
 * logging, and the Connector_Interface contract for all external API clients.
 * Concrete connectors extend this class and implement the three interface
 * methods: test_connection(), get_capabilities(), and execute().
 *
 * DEPENDS ON:
 *   - WPSeed\API\Connector_Interface (implements)
 *   - WordPress functions: wp_remote_request, wp_remote_retrieve_body,
 *     wp_remote_retrieve_response_code, add_query_arg, is_wp_error, wp_json_encode
 *   - WPSeed_API_Logging (global class, not yet namespaced)
 *
 * CONSUMED BY:
 *   - Concrete connector classes (GitHub, Discord, payment gateways, etc.)
 *   - api/api-factory.php (creates instances via factory pattern)
 *   - api/api-directory.php (queries capabilities for feature discovery)
 *
 * DATA FLOW:
 *   Input  → API credentials, endpoint URLs, action name + parameters
 *   Output → Parsed JSON response data, logged API calls via WPSeed_API_Logging
 *
 * @package  WPSeed\API
 * @category API
 * @since    1.0.0
 */

namespace WPSeed\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base class for external API connectors.
 *
 * Provides HTTP request handling, Bearer token authentication, and API call
 * logging. Implements Connector_Interface so every subclass automatically
 * fulfils the contract used by the API Directory, Factory, and REST Bridge.
 *
 * Subclasses MUST override:
 *   - test_connection()  — verify credentials against the live API
 *   - get_capabilities() — declare supported actions
 *   - execute()          — route action names to concrete API calls
 *
 * Subclasses MAY override:
 *   - get_headers()      — customise authentication headers
 *   - get_timeout()      — change the default request timeout
 *
 * @since 1.0.0
 * @since 3.1.0 Implements Connector_Interface. Added execute(), get_capabilities(),
 *              get_timeout(), get_base_url(), get_api_key(), make_request() PUT/PATCH/DELETE support.
 */
abstract class Base_API implements Connector_Interface {

	/**
	 * Unique identifier for this connector provider.
	 *
	 * Used as the key in the API Directory and in option names for credential
	 * storage. Example: 'github', 'discord', 'stripe'.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	protected $provider_id;

	/**
	 * API key or access token for authentication.
	 *
	 * Sent in the Authorization header by default. Override get_headers()
	 * if the API uses a different authentication scheme.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	protected $api_key;

	/**
	 * API secret for authentication schemes that require a key pair.
	 *
	 * Not used by the default Bearer token auth. Available for subclasses
	 * that need HMAC signing or OAuth client credentials.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	protected $api_secret;

	/**
	 * Base URL for all API requests.
	 *
	 * Endpoint paths passed to make_request() are appended to this URL.
	 * Must include the protocol and trailing path segment but no trailing slash.
	 * Example: 'https://api.github.com'
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	protected $base_url;

	/**
	 * Optional account identifier for multi-account support.
	 *
	 * When a connector supports multiple accounts (e.g. two GitHub tokens
	 * for different organisations), this distinguishes between them.
	 *
	 * @since 3.1.0
	 * @var   string
	 */
	protected $account_id = '';

	/**
	 * Construct a connector instance.
	 *
	 * @since 1.0.0
	 * @since 3.1.0 Added $account_id support in $args.
	 *
	 * @param string $provider_id Unique provider identifier.
	 * @param array  $args {
	 *     Optional. Connection arguments.
	 *
	 *     @type string $api_key    API key or access token.
	 *     @type string $api_secret API secret (for key-pair auth schemes).
	 *     @type string $base_url   Base URL for API requests.
	 *     @type string $account_id Account identifier for multi-account setups.
	 * }
	 */
	public function __construct( $provider_id, $args = array() ) {
		$this->provider_id = $provider_id;
		$this->api_key     = $args['api_key'] ?? '';
		$this->api_secret  = $args['api_secret'] ?? '';
		$this->base_url    = $args['base_url'] ?? '';
		$this->account_id  = $args['account_id'] ?? '';
	}

	/**
	 * Test whether the connector can reach its external service.
	 *
	 * Subclasses MUST override this with a real API call that verifies
	 * the credentials work. Do not just check that credentials are non-empty.
	 *
	 * @since  1.0.0
	 * @since  3.1.0 Return type changed from bool|array to array per Connector_Interface.
	 *
	 * @return array {
	 *     @type bool   $success Whether the connection succeeded.
	 *     @type string $message Human-readable status message.
	 *     @type array  $data    Optional additional data.
	 * }
	 */
	abstract public function test_connection();

	/**
	 * Return the actions this connector supports.
	 *
	 * Subclasses MUST override this to declare their supported actions.
	 * The default implementation returns an empty array (no capabilities).
	 *
	 * @since  3.1.0
	 *
	 * @return array<string, array{
	 *     label: string,
	 *     description: string,
	 *     method?: string,
	 *     params?: string[],
	 * }> Keyed by action name.
	 */
	public function get_capabilities() {
		return array();
	}

	/**
	 * Execute a named action on the external service.
	 *
	 * Subclasses MUST override this to route action names to concrete API
	 * calls. The default implementation returns a WP_Error for any action.
	 *
	 * Typical implementation pattern:
	 *
	 *     public function execute( $action, $params = array() ) {
	 *         switch ( $action ) {
	 *             case 'list_repos':
	 *                 return $this->make_request( '/user/repos' );
	 *             case 'create_issue':
	 *                 return $this->make_request(
	 *                     '/repos/' . $params['repo'] . '/issues',
	 *                     array( 'title' => $params['title'] ),
	 *                     'POST'
	 *                 );
	 *             default:
	 *                 return new \WP_Error(
	 *                     'unsupported_action',
	 *                     'Action not supported: ' . $action
	 *                 );
	 *         }
	 *     }
	 *
	 * @since  3.1.0
	 *
	 * @param  string $action Action name (must exist in get_capabilities()).
	 * @param  array  $params Action parameters. Keys depend on the action.
	 * @return array|\WP_Error Result data on success, WP_Error on failure.
	 */
	public function execute( $action, $params = array() ) {
		$capabilities = $this->get_capabilities();

		if ( ! isset( $capabilities[ $action ] ) ) {
			return new \WP_Error(
				'unsupported_action',
				sprintf(
					/* translators: 1: action name, 2: provider ID */
					__( 'Action "%1$s" is not supported by the %2$s connector.', 'wpseed' ),
					$action,
					$this->provider_id
				),
				array( 'status' => 400 )
			);
		}

		return new \WP_Error(
			'not_implemented',
			sprintf(
				/* translators: 1: action name, 2: class name */
				__( 'Action "%1$s" is declared but not implemented. Override execute() in %2$s.', 'wpseed' ),
				$action,
				static::class
			),
			array( 'status' => 501 )
		);
	}

	/**
	 * Make an HTTP request to the external API.
	 *
	 * Handles URL construction, header injection, body encoding, logging,
	 * and error handling. Supports GET, POST, PUT, PATCH, and DELETE methods.
	 *
	 * @since  1.0.0
	 * @since  3.1.0 Added PUT, PATCH, DELETE support. Logs HTTP status code.
	 *
	 * @param  string $endpoint API endpoint path (appended to base_url).
	 * @param  array  $params   Request parameters. Sent as query string for GET,
	 *                          JSON body for POST/PUT/PATCH.
	 * @param  string $method   HTTP method: GET, POST, PUT, PATCH, DELETE.
	 * @return array|\WP_Error  Parsed JSON response on success, WP_Error on failure.
	 */
	protected function make_request( $endpoint, $params = array(), $method = 'GET' ) {
		$call_id = 0;

		if ( class_exists( '\\WPSeed_API_Logging', false ) ) {
			$call_id = \WPSeed_API_Logging::log_call(
				$this->provider_id,
				$endpoint,
				$method,
				'pending'
			);
		}

		$url  = $this->base_url . $endpoint;
		$args = array(
			'method'  => $method,
			'timeout' => $this->get_timeout(),
			'headers' => $this->get_headers(),
		);

		if ( 'GET' === $method || 'DELETE' === $method ) {
			if ( ! empty( $params ) ) {
				$url = add_query_arg( $params, $url );
			}
		} else {
			$args['body'] = wp_json_encode( $params );
		}

		/**
		 * Filter the request arguments before sending.
		 *
		 * @since 3.1.0
		 *
		 * @param array  $args        wp_remote_request() arguments.
		 * @param string $url         Full request URL.
		 * @param string $provider_id Connector provider ID.
		 * @param string $endpoint    API endpoint path.
		 */
		$args = apply_filters( 'wpseed_connector_request_args', $args, $url, $this->provider_id, $endpoint );

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			if ( $call_id && class_exists( '\\WPSeed_API_Logging', false ) ) {
				\WPSeed_API_Logging::log_error( $call_id, 'request_failed', $response->get_error_message() );
				\WPSeed_API_Logging::update_call_outcome( $call_id, 'Error: ' . $response->get_error_message(), 'error' );
			}
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = wp_remote_retrieve_body( $response );
		$data        = json_decode( $body, true );

		if ( $status_code >= 400 ) {
			$error_message = $data['message'] ?? $body;

			if ( $call_id && class_exists( '\\WPSeed_API_Logging', false ) ) {
				\WPSeed_API_Logging::log_error( $call_id, 'http_' . $status_code, $error_message );
				\WPSeed_API_Logging::update_call_outcome( $call_id, 'HTTP ' . $status_code . ': ' . $error_message, 'error' );
			}

			return new \WP_Error(
				'http_error',
				$error_message,
				array(
					'status'      => $status_code,
					'provider_id' => $this->provider_id,
				)
			);
		}

		if ( $call_id && class_exists( '\\WPSeed_API_Logging', false ) ) {
			\WPSeed_API_Logging::update_call_outcome( $call_id, 'HTTP ' . $status_code, 'success' );
		}

		return $data ?? array();
	}

	/**
	 * Return default request headers.
	 *
	 * Sends JSON content type and Bearer token authentication. Override in
	 * subclasses for APIs that use different auth schemes (Basic, API key
	 * header, OAuth signature, etc.).
	 *
	 * @since  1.0.0
	 *
	 * @return array<string, string> Header name => value pairs.
	 */
	protected function get_headers() {
		$headers = array(
			'Content-Type' => 'application/json',
			'Accept'       => 'application/json',
		);

		if ( ! empty( $this->api_key ) ) {
			$headers['Authorization'] = 'Bearer ' . $this->api_key;
		}

		return $headers;
	}

	/**
	 * Return the request timeout in seconds.
	 *
	 * Override in subclasses for APIs that need longer timeouts (e.g. file
	 * uploads, report generation).
	 *
	 * @since  3.1.0
	 *
	 * @return int Timeout in seconds.
	 */
	protected function get_timeout() {
		return 30;
	}

	/**
	 * Return the provider identifier.
	 *
	 * @since  1.0.0
	 *
	 * @return string Provider ID (e.g. 'github', 'discord').
	 */
	public function get_provider_id() {
		return $this->provider_id;
	}

	/**
	 * Return the account identifier.
	 *
	 * Empty string when multi-account is not in use.
	 *
	 * @since  3.1.0
	 *
	 * @return string Account ID.
	 */
	public function get_account_id() {
		return $this->account_id;
	}

	/**
	 * Return the base URL for API requests.
	 *
	 * @since  3.1.0
	 *
	 * @return string Base URL.
	 */
	public function get_base_url() {
		return $this->base_url;
	}

	/**
	 * Check whether credentials have been provided.
	 *
	 * Does NOT verify the credentials work — use test_connection() for that.
	 * This is a quick check for the admin UI to show "not configured" state.
	 *
	 * @since  3.1.0
	 *
	 * @return bool True if at least an API key is set.
	 */
	public function has_credentials() {
		return ! empty( $this->api_key );
	}

	/**
	 * Build a standardised success response for test_connection().
	 *
	 * Helper for subclasses to return consistent test results.
	 *
	 * @since  3.1.0
	 *
	 * @param  string $message Human-readable success message.
	 * @param  array  $data    Optional additional data.
	 * @return array  Standardised success response.
	 */
	protected function connection_success( $message = '', $data = array() ) {
		if ( empty( $message ) ) {
			$message = sprintf(
				/* translators: %s: provider ID */
				__( 'Successfully connected to %s.', 'wpseed' ),
				$this->provider_id
			);
		}

		return array(
			'success' => true,
			'message' => $message,
			'data'    => $data,
		);
	}

	/**
	 * Build a standardised failure response for test_connection().
	 *
	 * Helper for subclasses to return consistent test results.
	 *
	 * @since  3.1.0
	 *
	 * @param  string $message Human-readable error message.
	 * @param  array  $data    Optional additional data (error codes, etc.).
	 * @return array  Standardised failure response.
	 */
	protected function connection_failure( $message = '', $data = array() ) {
		if ( empty( $message ) ) {
			$message = sprintf(
				/* translators: %s: provider ID */
				__( 'Failed to connect to %s.', 'wpseed' ),
				$this->provider_id
			);
		}

		return array(
			'success' => false,
			'message' => $message,
			'data'    => $data,
		);
	}
}
