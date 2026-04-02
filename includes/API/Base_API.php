<?php
/**
 * Abstract base class for external API integrations.
 *
 * ROLE: api-endpoint
 *
 * Single responsibility: Provide a base class for external API clients with
 * standard request handling, authentication headers, and API call logging.
 * Concrete API classes extend this and implement test_connection().
 *
 * DEPENDS ON:
 *   - WordPress functions: wp_remote_request, add_query_arg, is_wp_error
 *   - WPSeed_API_Logging (not yet migrated — referenced via global class name)
 *
 * CONSUMED BY:
 *   - Concrete API classes (e.g. GitHub API, payment gateway clients)
 *   - api/api-factory.php (creates instances via factory pattern)
 *
 * DATA FLOW:
 *   Input  → API credentials, endpoint URLs, request parameters
 *   Output → Parsed JSON response data, logged API calls
 *
 * @package  WPSeed\API
 * @since    1.0.0
 */

namespace WPSeed\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base class for external API integrations with logging and authentication.
 *
 * Single responsibility: HTTP request infrastructure for external APIs.
 * Does NOT implement any specific API — those are concrete subclasses.
 *
 * @since 1.0.0
 */
abstract class Base_API {

	/** @var string API provider identifier. */
	protected $provider_id;

	/** @var string API key. */
	protected $api_key;

	/** @var string API secret. */
	protected $api_secret;

	/** @var string Base URL for API requests. */
	protected $base_url;

	/**
	 * @since 1.0.0
	 * @param string $provider_id Provider identifier.
	 * @param array  $args        { api_key, api_secret, base_url }
	 */
	public function __construct( $provider_id, $args = array() ) {
		$this->provider_id = $provider_id;
		$this->api_key     = $args['api_key'] ?? '';
		$this->api_secret  = $args['api_secret'] ?? '';
		$this->base_url    = $args['base_url'] ?? '';
	}

	/**
	 * Test the API connection. Must be implemented by concrete classes.
	 *
	 * @since  1.0.0
	 * @return bool|array True on success, error data on failure.
	 */
	abstract public function test_connection();

	/**
	 * Make an HTTP request to the API.
	 *
	 * @since  1.0.0
	 * @param  string $endpoint API endpoint path (appended to base_url).
	 * @param  array  $params   Request parameters.
	 * @param  string $method   HTTP method (GET, POST, etc.).
	 * @return array|\WP_Error Parsed JSON response or WP_Error.
	 */
	protected function make_request( $endpoint, $params = array(), $method = 'GET' ) {
		$call_id = \WPSeed_API_Logging::log_call(
			$this->provider_id,
			$endpoint,
			$method,
			'pending'
		);

		$url  = $this->base_url . $endpoint;
		$args = array(
			'method'  => $method,
			'timeout' => 30,
			'headers' => $this->get_headers(),
		);

		if ( 'GET' === $method && ! empty( $params ) ) {
			$url = add_query_arg( $params, $url );
		} elseif ( 'POST' === $method ) {
			$args['body'] = wp_json_encode( $params );
		}

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			\WPSeed_API_Logging::log_error( $call_id, 'request_failed', $response->get_error_message() );
			\WPSeed_API_Logging::update_call_outcome( $call_id, 'Error: ' . $response->get_error_message(), 'error' );
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		\WPSeed_API_Logging::update_call_outcome( $call_id, 'Success', 'success' );

		return $data;
	}

	/**
	 * Return default request headers including authorization.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	protected function get_headers() {
		return array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $this->api_key,
		);
	}

	/**
	 * Return the provider identifier.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function get_provider_id() {
		return $this->provider_id;
	}
}
