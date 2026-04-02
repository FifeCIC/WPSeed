<?php
/**
 * Abstract base class for REST API controllers.
 *
 * ROLE: api-endpoint
 *
 * Single responsibility: Provide a base class with default permission checks
 * and namespace configuration for all WPSeed REST API endpoints. Concrete
 * controllers extend this and implement register_routes().
 *
 * DEPENDS ON:
 *   - WordPress WP_REST_Controller class
 *
 * CONSUMED BY:
 *   - Any REST controller class in the plugin
 *   - Hook: rest_api_init (controllers register routes there)
 *
 * DATA FLOW:
 *   Input  → WP_REST_Request objects
 *   Output → WP_REST_Response objects (JSON)
 *
 * @package  WPSeed\API
 * @since    1.0.0
 */

namespace WPSeed\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base REST API controller with secure-by-default permission checks.
 *
 * Single responsibility: Shared REST infrastructure. Does NOT implement
 * any specific endpoints — those are in concrete controller classes.
 *
 * @since 1.0.0
 */
abstract class REST_Controller extends \WP_REST_Controller {

	/** @var string REST API namespace. */
	protected $namespace = 'wpseed/v1';

	/**
	 * Register routes. Override in child classes.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function register_routes() {
		// Override in child classes.
	}

	/**
	 * Default permission check for GET requests — requires manage_options.
	 *
	 * @since  1.0.0
	 * @param  \WP_REST_Request $request Request object.
	 * @return bool
	 */
	public function get_items_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Default permission check for POST requests — requires manage_options.
	 *
	 * @since  1.0.0
	 * @param  \WP_REST_Request $request Request object.
	 * @return bool
	 */
	public function create_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}
}
