<?php
/**
 * Connector Interface — contract for all external API connectors.
 *
 * ROLE: api-endpoint
 *
 * Single responsibility: Define the three methods every connector must
 * implement so the API Directory, Factory, and REST Bridge can interact
 * with any connector without knowing its internals.
 *
 * DEPENDS ON:
 *   - Nothing. This is a pure interface with no dependencies.
 *
 * CONSUMED BY:
 *   - WPSeed\API\Base_API (implements this interface)
 *   - api/api-factory.php (validates instances against this interface)
 *   - api/api-directory.php (calls get_capabilities() for feature discovery)
 *   - Any concrete connector class (GitHub, Discord, payment gateways, etc.)
 *
 * DATA FLOW:
 *   Input  → None (interface definition only)
 *   Output → None (interface definition only)
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
 * Contract for external API connectors.
 *
 * Every connector in the ecosystem — whether it talks to GitHub, a payment
 * gateway, a social platform, or a custom REST service — implements this
 * interface. This gives the API Directory, Factory, and REST Bridge a
 * universal way to interact with any connector.
 *
 * Concrete connectors extend Base_API (which implements this interface)
 * and override the three methods below plus get_headers() for auth.
 *
 * @since 3.1.0
 */
interface Connector_Interface {

	/**
	 * Test whether the connector can reach its external service.
	 *
	 * Called by the admin UI "Test Connection" button and by the ecosystem
	 * health check. Must make a real API call (not just check credentials
	 * exist) to confirm the service is reachable and the credentials work.
	 *
	 * @since  3.1.0
	 *
	 * @return array {
	 *     Connection test result.
	 *
	 *     @type bool   $success Whether the connection succeeded.
	 *     @type string $message Human-readable status message.
	 *     @type array  $data    Optional additional data (API version, rate limits, etc.).
	 * }
	 */
	public function test_connection();

	/**
	 * Return the actions this connector supports.
	 *
	 * Called by the API Directory to build the feature matrix and by the
	 * REST Bridge to validate incoming action requests. Each capability
	 * is a string action name that can be passed to execute().
	 *
	 * Example return value for a GitHub connector:
	 *
	 *     array(
	 *         'list_repos'   => array(
	 *             'label'       => 'List Repositories',
	 *             'description' => 'Retrieve all repositories for the authenticated user.',
	 *             'method'      => 'GET',
	 *         ),
	 *         'create_issue' => array(
	 *             'label'       => 'Create Issue',
	 *             'description' => 'Create a new issue in a repository.',
	 *             'method'      => 'POST',
	 *             'params'      => array( 'repo', 'title', 'body', 'labels' ),
	 *         ),
	 *     )
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
	public function get_capabilities();

	/**
	 * Execute a named action on the external service.
	 *
	 * This is the universal entry point for all connector operations. The
	 * REST Bridge routes incoming requests to this method. The action name
	 * must match one of the keys returned by get_capabilities().
	 *
	 * @since  3.1.0
	 *
	 * @param  string $action Action name (must exist in get_capabilities()).
	 * @param  array  $params Action parameters. Keys depend on the action.
	 * @return array|\WP_Error Result data on success, WP_Error on failure.
	 */
	public function execute( $action, $params = array() );
}
