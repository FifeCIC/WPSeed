<?php
/**
 * Abstract base class for REST API controllers.
 *
 * ROLE: api-endpoint
 *
 * Single responsibility: Provide a base class with default permission checks,
 * namespace configuration, and REST Bridge integration for all WPSeed REST
 * API endpoints. Concrete controllers extend this and implement register_routes().
 *
 * DEPENDS ON:
 *   - WordPress WP_REST_Controller class
 *   - WPSeed\API\REST_Bridge (for register_endpoint convenience method)
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
 * Provides register_endpoint() as a convenience wrapper around REST_Bridge
 * that auto-fills the namespace from the controller.
 *
 * @since 1.0.0
 * @since 3.1.0 Added register_endpoint() for REST Bridge integration.
 */
abstract class REST_Controller extends \WP_REST_Controller {

	/**
	 * REST API namespace.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	protected $namespace = 'wpseed/v1';

	/**
	 * Register routes. Override in child classes.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		// Override in child classes.
	}

	/**
	 * Register an endpoint via REST Bridge with automatic namespace.
	 *
	 * Convenience wrapper that pre-fills the namespace from the controller
	 * and delegates to REST_Bridge::register(). Use this instead of calling
	 * register_rest_route() directly.
	 *
	 * Usage in a concrete controller:
	 *
	 *     public function register_routes() {
	 *         $this->register_endpoint( '/projects', array(
	 *             'method'      => 'GET',
	 *             'callback'    => array( $this, 'get_projects' ),
	 *             'capability'  => 'wpseed_manage_settings',
	 *             'label'       => 'List Projects',
	 *             'description' => 'Returns all projects for the current user.',
	 *         ) );
	 *     }
	 *
	 * @since  3.1.0
	 *
	 * @param  string $route Route path (e.g. '/projects').
	 * @param  array  $args  Endpoint configuration. See REST_Bridge::register().
	 * @return void
	 */
	protected function register_endpoint( $route, $args = array() ) {
		REST_Bridge::register( $this->namespace, $route, $args );
	}

	/**
	 * Default permission check for GET requests — requires manage_options.
	 *
	 * @since  1.0.0
	 *
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
	 *
	 * @param  \WP_REST_Request $request Request object.
	 * @return bool
	 */
	public function create_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}
}
