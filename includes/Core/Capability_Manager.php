<?php
/**
 * Capability Manager — registration and checking of custom capabilities.
 *
 * ROLE: utility
 *
 * Single responsibility: Maintain a registry of custom capabilities with
 * metadata, handle their installation into WordPress roles on activation,
 * removal on uninstall, and provide a single user_can() check point that
 * all plugin code uses instead of calling current_user_can() directly.
 *
 * DEPENDS ON:
 *   - WordPress functions: current_user_can, get_role, add_role, wp_roles,
 *     get_userdata, do_action, apply_filters
 *
 * CONSUMED BY:
 *   - functions.php: wpseed_user_can() global accessor
 *   - WPSeed\Core\Install: delegates capability installation here
 *   - REST controllers, admin pages, AJAX handlers — anywhere a permission
 *     check is needed
 *   - EvolveWP Core (elevates this into cross-plugin permission management)
 *
 * DATA FLOW:
 *   Input  → Capability definitions registered via register()
 *   Output → WordPress role capabilities added/removed
 *
 * @package  WPSeed\Core
 * @category Core
 * @since    3.1.0
 */

namespace WPSeed\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages custom capability registration, installation, and checking.
 *
 * Capabilities are registered with metadata (label, description, default
 * roles) so the admin UI can display them in a human-readable permissions
 * panel. The class handles adding caps to roles on activation and removing
 * them on uninstall.
 *
 * Does NOT handle role creation — that stays in Install::create_roles().
 * Does NOT render admin UI — that belongs in the Admin namespace.
 *
 * Usage:
 *
 *     // Register capabilities (during plugin init).
 *     Capability_Manager::register( 'wpseed_manage_settings', array(
 *         'label'       => 'Manage Settings',
 *         'description' => 'Access the plugin settings page.',
 *         'grant_to'    => array( 'administrator' ),
 *     ) );
 *
 *     // Check a capability.
 *     if ( Capability_Manager::user_can( 'wpseed_manage_settings' ) ) {
 *         // show settings page
 *     }
 *
 *     // Install all registered caps into roles (called on activation).
 *     Capability_Manager::install();
 *
 * @since 3.1.0
 */
class Capability_Manager {

	/**
	 * Registered capabilities with metadata.
	 *
	 * @since 3.1.0
	 * @var   array<string, array{
	 *     label: string,
	 *     description: string,
	 *     grant_to: string[],
	 *     group: string,
	 * }>
	 */
	private static $capabilities = array();

	/**
	 * Whether default capabilities have been registered.
	 *
	 * @since 3.1.0
	 * @var   bool
	 */
	private static $defaults_registered = false;

	/**
	 * Register a custom capability.
	 *
	 * Call this during plugin init (or earlier) to declare a capability.
	 * The capability is not added to WordPress roles until install() is
	 * called (typically on plugin activation).
	 *
	 * @since  3.1.0
	 *
	 * @param  string $capability Capability name. Must be prefixed with the
	 *                            plugin prefix (e.g. 'wpseed_manage_settings').
	 * @param  array  $args {
	 *     Capability configuration.
	 *
	 *     @type string   $label       Human-readable label for admin UI.
	 *     @type string   $description Longer description of what this cap allows.
	 *     @type string[] $grant_to    Role slugs that should receive this cap by
	 *                                 default on installation. Example: array( 'administrator' ).
	 *     @type string   $group       Grouping key for organising caps in the admin UI.
	 *                                 Example: 'core', 'api', 'development'.
	 * }
	 * @return void
	 */
	public static function register( $capability, $args = array() ) {
		$defaults = array(
			'label'       => $capability,
			'description' => '',
			'grant_to'    => array( 'administrator' ),
			'group'       => 'core',
		);

		self::$capabilities[ $capability ] = wp_parse_args( $args, $defaults );
	}

	/**
	 * Register multiple capabilities at once.
	 *
	 * Convenience method for registering a batch of capabilities.
	 *
	 * @since  3.1.0
	 *
	 * @param  array<string, array> $capabilities Keyed by capability name,
	 *                                            values are args arrays per register().
	 * @return void
	 */
	public static function register_many( $capabilities ) {
		foreach ( $capabilities as $capability => $args ) {
			self::register( $capability, $args );
		}
	}

	/**
	 * Register the default WPSeed capabilities.
	 *
	 * Called automatically on first use. These are the baseline caps that
	 * every WPSeed-based plugin starts with. Plugins add their own via
	 * register() during init.
	 *
	 * @since  3.1.0
	 *
	 * @return void
	 */
	public static function register_defaults() {
		if ( self::$defaults_registered ) {
			return;
		}

		self::register_many( array(
			'manage_wpseed' => array(
				'label'       => __( 'Manage WPSeed', 'wpseed' ),
				'description' => __( 'Full administrative access to the WPSeed plugin.', 'wpseed' ),
				'grant_to'    => array( 'administrator' ),
				'group'       => 'core',
			),
			'code_wpseed' => array(
				'label'       => __( 'Code WPSeed', 'wpseed' ),
				'description' => __( 'Access developer-level features and code tools.', 'wpseed' ),
				'grant_to'    => array( 'administrator' ),
				'group'       => 'development',
			),
			'wpseed_manage_settings' => array(
				'label'       => __( 'Manage Settings', 'wpseed' ),
				'description' => __( 'Access and modify the plugin settings page.', 'wpseed' ),
				'grant_to'    => array( 'administrator' ),
				'group'       => 'core',
			),
			'wpseed_view_development' => array(
				'label'       => __( 'View Development Tools', 'wpseed' ),
				'description' => __( 'Access the Development page with architecture, roadmap, and debug tabs.', 'wpseed' ),
				'grant_to'    => array( 'administrator' ),
				'group'       => 'development',
			),
			'wpseed_manage_connectors' => array(
				'label'       => __( 'Manage API Connectors', 'wpseed' ),
				'description' => __( 'Configure API credentials and test connector connections.', 'wpseed' ),
				'grant_to'    => array( 'administrator' ),
				'group'       => 'api',
			),
			'wpseed_execute_connectors' => array(
				'label'       => __( 'Execute Connector Actions', 'wpseed' ),
				'description' => __( 'Run actions on external APIs via the connector system.', 'wpseed' ),
				'grant_to'    => array( 'administrator' ),
				'group'       => 'api',
			),
			'wpseed_view_logs' => array(
				'label'       => __( 'View Logs', 'wpseed' ),
				'description' => __( 'Access API call logs, debug logs, and trace output.', 'wpseed' ),
				'grant_to'    => array( 'administrator' ),
				'group'       => 'development',
			),
		) );

		self::$defaults_registered = true;
	}

	/**
	 * Check whether a user has a specific capability.
	 *
	 * This is the single check point for all permission checks in the plugin.
	 * It ensures defaults are registered, then delegates to WordPress
	 * current_user_can(). Using this instead of current_user_can() directly
	 * allows EvolveWP Core to intercept and extend permission logic later.
	 *
	 * @since  3.1.0
	 *
	 * @param  string   $capability Capability name to check.
	 * @param  int|null $user_id    User ID to check. Null = current user.
	 * @return bool     True if the user has the capability.
	 */
	public static function user_can( $capability, $user_id = null ) {
		self::register_defaults();

		/**
		 * Filter the capability check result.
		 *
		 * Allows EvolveWP Core or other plugins to override permission
		 * decisions. Return a non-null value to short-circuit the check.
		 *
		 * @since 3.1.0
		 *
		 * @param bool|null $result     Null to use default check, bool to override.
		 * @param string    $capability Capability being checked.
		 * @param int|null  $user_id    User ID being checked (null = current user).
		 */
		$override = apply_filters( 'wpseed_user_can', null, $capability, $user_id );

		if ( null !== $override ) {
			return (bool) $override;
		}

		if ( null === $user_id ) {
			return current_user_can( $capability );
		}

		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return false;
		}

		return $user->has_cap( $capability );
	}

	/**
	 * Install all registered capabilities into their default roles.
	 *
	 * Called during plugin activation. Adds each registered capability to
	 * the WordPress roles specified in its 'grant_to' array.
	 *
	 * Safe to call multiple times — WordPress ignores duplicate cap additions.
	 *
	 * @since  3.1.0
	 *
	 * @return void
	 */
	public static function install() {
		self::register_defaults();

		foreach ( self::$capabilities as $capability => $args ) {
			foreach ( $args['grant_to'] as $role_slug ) {
				$role = get_role( $role_slug );
				if ( $role ) {
					$role->add_cap( $capability );
				}
			}
		}

		/**
		 * Fires after all capabilities have been installed into roles.
		 *
		 * @since 3.1.0
		 *
		 * @param array<string, array> $capabilities All registered capabilities.
		 */
		do_action( 'wpseed_capabilities_installed', self::$capabilities );
	}

	/**
	 * Remove all registered capabilities from all roles.
	 *
	 * Called during plugin uninstall. Removes every registered capability
	 * from every role that has it.
	 *
	 * @since  3.1.0
	 *
	 * @return void
	 */
	public static function uninstall() {
		self::register_defaults();

		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new \WP_Roles();
		}

		foreach ( self::$capabilities as $capability => $args ) {
			foreach ( $wp_roles->role_objects as $role ) {
				$role->remove_cap( $capability );
			}
		}

		/**
		 * Fires after all capabilities have been removed from roles.
		 *
		 * @since 3.1.0
		 *
		 * @param array<string, array> $capabilities All registered capabilities.
		 */
		do_action( 'wpseed_capabilities_uninstalled', self::$capabilities );
	}

	/**
	 * Return all registered capabilities.
	 *
	 * @since  3.1.0
	 *
	 * @return array<string, array{
	 *     label: string,
	 *     description: string,
	 *     grant_to: string[],
	 *     group: string,
	 * }>
	 */
	public static function get_all() {
		self::register_defaults();
		return self::$capabilities;
	}

	/**
	 * Return capabilities filtered by group.
	 *
	 * @since  3.1.0
	 *
	 * @param  string $group Group key (e.g. 'core', 'api', 'development').
	 * @return array<string, array> Capabilities in the specified group.
	 */
	public static function get_by_group( $group ) {
		self::register_defaults();

		return array_filter(
			self::$capabilities,
			static function ( $args ) use ( $group ) {
				return $args['group'] === $group;
			}
		);
	}

	/**
	 * Return the metadata for a single capability.
	 *
	 * @since  3.1.0
	 *
	 * @param  string $capability Capability name.
	 * @return array|null Capability metadata, or null if not registered.
	 */
	public static function get( $capability ) {
		self::register_defaults();
		return self::$capabilities[ $capability ] ?? null;
	}

	/**
	 * Check whether a capability is registered.
	 *
	 * @since  3.1.0
	 *
	 * @param  string $capability Capability name.
	 * @return bool
	 */
	public static function is_registered( $capability ) {
		self::register_defaults();
		return isset( self::$capabilities[ $capability ] );
	}

	/**
	 * Return all registered group keys.
	 *
	 * @since  3.1.0
	 *
	 * @return string[] Unique group keys.
	 */
	public static function get_groups() {
		self::register_defaults();

		return array_unique( array_column( self::$capabilities, 'group' ) );
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
		self::$capabilities       = array();
		self::$defaults_registered = false;
	}
}
