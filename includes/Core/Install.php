<?php
/**
 * Plugin installation, activation, DB tables, roles, and version management.
 *
 * ROLE: data-model
 *
 * Single responsibility: Handle everything that happens on plugin activation,
 * deactivation, and version updates — DB table creation, role creation, option
 * defaults, version tracking, and transient cleanup. Does NOT handle admin UI
 * or settings rendering.
 *
 * DEPENDS ON:
 *   - WordPress functions: add_option, get_option, dbDelta, add_role, flush_rewrite_rules
 *   - WPSeed_Admin_Notices (not yet migrated — referenced via global class name)
 *   - WPSeed_Admin_Settings (not yet migrated — referenced via global class name)
 *   - global WPSeed() function in functions.php
 *
 * CONSUMED BY:
 *   - loader.php: register_activation_hook, register_deactivation_hook
 *   - Hook: init (priority 5) for version checking
 *   - Hook: admin_init for update actions
 *
 * DATA FLOW:
 *   Input  → wpseed_version option, wpseed_db_version option
 *   Output → DB tables created, options set, roles created, transients cleaned
 *
 * @package  WPSeed\Core
 * @since    1.0.0
 */

namespace WPSeed\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin installation, activation, deactivation, and version updates.
 *
 * Single responsibility: Lifecycle management. Does NOT render admin pages
 * or handle settings UI — those are in the Admin namespace.
 *
 * @since 1.0.0
 */
class Install {

	/** @var array DB updates and callbacks that need to be run per version. */
	private static $db_updates = array(
		'0.0.0' => array(
			'wpseed_update_000_file_paths',
			'wpseed_update_000_db_version',
		),
	);

	/**
	 * Register hooks for version checking and update actions.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
		add_action( 'in_plugin_update_message-wpseed/wpseed.php', array( __CLASS__, 'in_plugin_update_message' ) );
		add_filter( 'plugin_action_links_' . WPSEED_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Update the stored plugin version to the current package version.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function update_package_version() {
		delete_option( 'wpseed_version' );
		add_option( 'wpseed_version', \WPSeed()->version );
	}

	/**
	 * Check package version against installed version on every request.
	 * Runs install() if they differ.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && get_option( 'wpseed_version' ) !== \WPSeed()->version ) {
			self::install();
		}
	}

	/**
	 * Run update-related actions on admin_init.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function install_actions() {
		self::install_action_do_update();
		self::install_action_updater_cron();
	}

	/**
	 * Manual plugin update action via GET parameter.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function install_action_do_update() {
		if ( ! empty( $_GET['do_update_wpseed'] ) && current_user_can( 'manage_options' ) ) {
			if ( isset( $_GET['_wpseed_update_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpseed_update_nonce'] ) ), 'wpseed_do_update' ) ) {
				self::install();
				\WPSeed_Admin_Notices::add_notice( 'update' );
			}
		}
	}

	/**
	 * Forced update action via GET parameter with nonce verification.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function install_action_updater_cron() {
		if ( empty( $_GET['force_update_wpseed'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$wpseed_nonce = isset( $_GET['_wpseed_force_nonce'] )
			? sanitize_text_field( wp_unslash( $_GET['_wpseed_force_nonce'] ) )
			: '';

		if ( ! wp_verify_nonce( $wpseed_nonce, 'wpseed_force_update' ) ) {
			wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'wpseed' ) );
		}

		do_action( 'wpseed_updater_cron' );
		wp_safe_redirect( admin_url( 'options-general.php?page=wpseed-settings' ) );
		exit;
	}

	/**
	 * Run the full installation routine.
	 *
	 * Called on activation and when the stored version differs from the
	 * package version. Creates DB tables, roles, options, and directories.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function install() {
		global $wpdb;

		if ( ! defined( 'WPSEED_INSTALLING' ) ) {
			define( 'WPSEED_INSTALLING', true );
		}

		// WPSeed_Admin_Notices is not yet namespaced — load via legacy path.
		if ( ! class_exists( 'WPSeed_Admin_Notices' ) ) {
			include_once WPSEED_PLUGIN_DIR_PATH . 'includes/admin/admin-notices.php';
		}

		\WPSeed_Admin_Notices::remove_all_notices();

		self::create_options();
		self::create_roles();
		self::create_files();
		self::create_tables();

		$current_installed_version = get_option( 'wpseed_version', null );
		$current_db_version        = get_option( 'wpseed_db_version', null );

		if ( is_null( $current_installed_version ) && is_null( $current_db_version ) && apply_filters( 'wpseed_enable_setup_wizard', true ) ) {
			\WPSeed_Admin_Notices::add_notice( 'install' );
			set_transient( '_wpseed_activation_redirect', 1, 30 );
		}

		if ( ! is_null( $current_db_version ) && version_compare( $current_db_version, max( array_keys( self::$db_updates ) ), '<' ) ) {
			\WPSeed_Admin_Notices::add_notice( 'update' );
		} else {
			self::update_db_version();
		}

		self::update_package_version();

		flush_rewrite_rules();

		$wpdb->query( $wpdb->prepare(
			"DELETE a, b FROM $wpdb->options a, $wpdb->options b
			WHERE a.option_name LIKE %s
			AND a.option_name NOT LIKE %s
			AND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) )
			AND b.option_value < %d",
			$wpdb->esc_like( '_transient_' ) . '%',
			$wpdb->esc_like( '_transient_timeout_' ) . '%',
			time()
		) );

		do_action( 'wpseed_installed' );
	}

	/**
	 * Show plugin changes from the readme.txt stored at WordPress.org.
	 *
	 * @since 1.0.0
	 * @param array $args Plugin update message arguments.
	 * @return void
	 */
	public static function in_plugin_update_message( $args ) {
		$transient_name = 'wpseed_upgrade_notice_' . $args['Version'];

		if ( false === ( $upgrade_notice = get_transient( $transient_name ) ) ) {
			$response = '';
			if ( defined( 'WPSEED_WORDPRESSORG_SLUG' ) && WPSEED_WORDPRESSORG_SLUG !== false && is_string( WPSEED_WORDPRESSORG_SLUG ) ) {
				$response = wp_safe_remote_get( 'https://plugins.svn.wordpress.org/' . WPSEED_WORDPRESSORG_SLUG . '/trunk/readme.txt' );

				if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
					$upgrade_notice = self::parse_update_notice( $response['body'], $args['new_version'] );
					set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
				}
			}
		}

		echo wp_kses_post( $upgrade_notice );
	}

	/**
	 * Add plugin action links on the Plugins screen.
	 *
	 * @since 1.0.0
	 * @param array $links Existing action links.
	 * @return array Modified action links.
	 */
	public static function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=wpseed-settings' ) . '" title="' . esc_attr( __( 'View WPSeed Settings', 'wpseed' ) ) . '">' . __( 'Settings', 'wpseed' ) . '</a>',
		);
		return array_merge( $action_links, $links );
	}

	/**
	 * Add plugin row meta on the Plugins screen.
	 *
	 * @since 1.0.0
	 * @param array  $links Existing row meta.
	 * @param string $file  Plugin base file.
	 * @return array Modified row meta.
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( $file === WPSEED_PLUGIN_BASENAME ) {
			$row_meta = array(
				'docs'    => '<a href="' . esc_url( apply_filters( 'wpseed_docs_url', WPSEED_DOCS ) ) . '">' . __( 'Docs', 'wpseed' ) . '</a>',
				'support' => '<a href="' . esc_url( apply_filters( 'wpseed_support_url', WPSEED_GITHUB . '/issues' ) ) . '">' . __( 'Support', 'wpseed' ) . '</a>',
				'donate'  => '<a href="' . esc_url( apply_filters( 'wpseed_donate_url', WPSEED_DONATE ) ) . '">' . __( 'Donate', 'wpseed' ) . '</a>',
			);
			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}

	/**
	 * Create roles and capabilities.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new \WP_Roles();
		}

		add_role( 'seniordeveloper', __( 'Senior Developer', 'wpseed' ), array(
			'level_9'                => true, 'level_8'                => true,
			'level_7'                => true, 'level_6'                => true,
			'level_5'                => true, 'level_4'                => true,
			'level_3'                => true, 'level_2'                => true,
			'level_1'                => true, 'level_0'                => true,
			'read'                   => true, 'read_private_pages'     => true,
			'read_private_posts'     => true, 'edit_users'             => true,
			'edit_posts'             => true, 'edit_pages'             => true,
			'edit_published_posts'   => true, 'edit_published_pages'   => true,
			'edit_private_pages'     => true, 'edit_private_posts'     => true,
			'edit_others_posts'      => true, 'edit_others_pages'      => true,
			'publish_posts'          => true, 'publish_pages'          => true,
			'delete_posts'           => true, 'delete_pages'           => true,
			'delete_private_pages'   => true, 'delete_private_posts'   => true,
			'delete_published_pages' => true, 'delete_published_posts' => true,
			'delete_others_posts'    => true, 'delete_others_pages'    => true,
			'manage_categories'      => true, 'manage_links'           => true,
			'moderate_comments'      => true, 'unfiltered_html'        => true,
			'upload_files'           => true, 'export'                 => true,
			'import'                 => true, 'list_users'             => true,
		) );

		$capabilities = self::get_core_capabilities();
		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'administrator', $cap );
				$wp_roles->add_cap( 'seniordeveloper', $cap );
			}
		}
	}

	/**
	 * Remove all custom roles and capabilities.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function remove_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new \WP_Roles();
		}

		$capabilities = self::get_core_capabilities();
		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'seniordeveloper', $cap );
				$wp_roles->remove_cap( 'administrator', $cap );
			}
		}
		remove_role( 'seniordeveloper' );
	}

	/**
	 * Return the custom capabilities for this plugin.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	private static function get_core_capabilities() {
		return array(
			'core' => array(
				'manage_wpseed',
				'code_wpseed',
			),
		);
	}

	/**
	 * Create files and directories with .htaccess and index guards.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function create_files() {
		$upload_dir      = wp_upload_dir();
		$download_method = get_option( 'wpseed_file_download_method', 'force' );

		$files = array(
			array( 'base' => $upload_dir['basedir'] . '/wpseed_uploads', 'file' => 'index.html', 'content' => '' ),
			array( 'base' => WPSEED_LOG_DIR, 'file' => '.htaccess', 'content' => 'deny from all' ),
			array( 'base' => WPSEED_LOG_DIR, 'file' => 'index.html', 'content' => '' ),
		);

		if ( 'redirect' !== $download_method ) {
			$files[] = array( 'base' => $upload_dir['basedir'] . '/wpseed_uploads', 'file' => '.htaccess', 'content' => 'deny from all' );
		}

		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
				$file_path = trailingslashit( $file['base'] ) . $file['file'];
				if ( ! function_exists( 'WP_Filesystem' ) ) {
					require_once ABSPATH . 'wp-admin/includes/file.php';
				}
				\WP_Filesystem();
				global $wp_filesystem;
				$wp_filesystem->put_contents( $file_path, $file['content'], FS_CHMOD_FILE );
			}
		}
	}

	/**
	 * Set default options from the settings pages.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function create_options() {
		if ( ! class_exists( 'WPSeed_Admin_Settings' ) ) {
			include_once WPSEED_PLUGIN_DIR_PATH . 'includes/admin/admin-settings.php';
		}

		$settings = \WPSeed_Admin_Settings::get_settings_pages();

		foreach ( $settings as $section ) {
			if ( ! method_exists( $section, 'get_settings' ) ) {
				continue;
			}
			$subsections = array_unique( array_merge( array( '' ), array_keys( $section->get_sections() ) ) );

			foreach ( $subsections as $subsection ) {
				foreach ( $section->get_settings( $subsection ) as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
		}
	}

	/**
	 * Update the stored DB version.
	 *
	 * @since 1.0.0
	 * @param string|null $version Version to store. Null = current plugin version.
	 * @return void
	 */
	public static function update_db_version( $version = null ) {
		delete_option( 'wpseed_db_version' );
		add_option( 'wpseed_db_version', is_null( $version ) ? \WPSeed()->version : $version );
	}

	/**
	 * Create all database tables.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql = "CREATE TABLE {$wpdb->prefix}wpseed_api_calls (
			entryid bigint(20) NOT NULL AUTO_INCREMENT,
			service varchar(100) NOT NULL,
			`type` varchar(20) NOT NULL,
			`status` varchar(20) NOT NULL,
			`file` varchar(255) DEFAULT NULL,
			`function` varchar(100) DEFAULT NULL,
			`line` int(11) DEFAULT NULL,
			wpuserid bigint(20) DEFAULT NULL,
			`timestamp` datetime NOT NULL,
			description text,
			outcome text,
			PRIMARY KEY (entryid),
			KEY service (service),
			KEY `status` (`status`),
			KEY `timestamp` (`timestamp`)
		) $charset_collate;";
		dbDelta( $sql );

		$sql = "CREATE TABLE {$wpdb->prefix}wpseed_api_endpoints (
			endpointid bigint(20) NOT NULL AUTO_INCREMENT,
			entryid bigint(20) NOT NULL,
			service varchar(100) NOT NULL,
			endpoint varchar(255) NOT NULL,
			parameters text,
			firstuse datetime NOT NULL,
			lastuse datetime NOT NULL,
			counter int(11) DEFAULT 1,
			PRIMARY KEY (endpointid),
			KEY service (service),
			KEY endpoint (endpoint)
		) $charset_collate;";
		dbDelta( $sql );

		$sql = "CREATE TABLE {$wpdb->prefix}wpseed_api_errors (
			errorid bigint(20) NOT NULL AUTO_INCREMENT,
			entryid bigint(20) NOT NULL,
			code varchar(50) NOT NULL,
			error text NOT NULL,
			`line` int(11) DEFAULT NULL,
			`function` varchar(100) DEFAULT NULL,
			`file` varchar(255) DEFAULT NULL,
			`timestamp` datetime NOT NULL,
			PRIMARY KEY (errorid),
			KEY entryid (entryid),
			KEY code (code)
		) $charset_collate;";
		dbDelta( $sql );

		$sql = "CREATE TABLE {$wpdb->prefix}wpseed_ai_usage (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			provider varchar(50) NOT NULL,
			task_type varchar(50) NOT NULL,
			tokens int(11) DEFAULT 0,
			timestamp datetime NOT NULL,
			PRIMARY KEY (id),
			KEY provider (provider),
			KEY task_type (task_type),
			KEY timestamp (timestamp)
		) $charset_collate;";
		dbDelta( $sql );

		$sql = "CREATE TABLE {$wpdb->prefix}wpseed_debug_logs (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			request_uri varchar(255) NOT NULL,
			query_count int(11) DEFAULT 0,
			query_time float DEFAULT 0,
			hook_count int(11) DEFAULT 0,
			http_count int(11) DEFAULT 0,
			error_count int(11) DEFAULT 0,
			execution_time float DEFAULT 0,
			memory_usage bigint(20) DEFAULT 0,
			created_at datetime NOT NULL,
			data longtext,
			PRIMARY KEY (id),
			KEY created_at (created_at),
			KEY request_uri (request_uri)
		) $charset_collate;";
		dbDelta( $sql );

		$sql = "CREATE TABLE {$wpdb->prefix}wpseed_notifications (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			type varchar(50) NOT NULL,
			message text NOT NULL,
			user_id bigint(20) NOT NULL DEFAULT 0,
			priority varchar(20) NOT NULL DEFAULT 'normal',
			is_read tinyint(1) NOT NULL DEFAULT 0,
			is_snoozed tinyint(1) NOT NULL DEFAULT 0,
			snooze_until datetime DEFAULT NULL,
			category varchar(50) DEFAULT NULL,
			action_url varchar(255) DEFAULT NULL,
			action_label varchar(100) DEFAULT NULL,
			created_at datetime NOT NULL,
			expires_at datetime DEFAULT NULL,
			data longtext DEFAULT NULL,
			PRIMARY KEY  (id),
			KEY user_read (user_id, is_read),
			KEY type (type),
			KEY category (category)
		) $charset_collate;";
		dbDelta( $sql );
	}

	/**
	 * Called on plugin deactivation. Cleans up scheduled tasks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'action_scheduler_run_queue' );
	}
}
