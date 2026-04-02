<?php
/**
 * Ecosystem plugin installer — one-click installation of related plugins.
 *
 * ROLE: admin-ui
 *
 * Single responsibility: Provide a UI for browsing and installing EvolveWP
 * ecosystem plugins. Handles the installer admin page and the AJAX install
 * endpoint. Does NOT manage the registry (Registry) or menu placement
 * (Menu_Manager).
 *
 * DEPENDS ON:
 *   - WordPress Plugin_Upgrader, WP_Ajax_Upgrader_Skin (wp-admin includes)
 *   - Filter: wpseed_ecosystem_available_plugins
 *
 * CONSUMED BY:
 *   - Hook: admin_menu (registers the installer submenu page)
 *   - Hook: wp_ajax_wpseed_install_plugin
 *
 * DATA FLOW:
 *   Input  → $_POST['slug'] via AJAX, available_plugins filter
 *   Output → Plugin installed to wp-content/plugins/ via Plugin_Upgrader
 *
 * @package  WPSeed\Ecosystem
 * @since    1.0.0
 */

namespace WPSeed\Ecosystem;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * One-click installer for EvolveWP ecosystem plugins.
 *
 * Single responsibility: Installer page rendering and AJAX install handler.
 * Does NOT manage the registry or menu placement.
 *
 * @since 1.0.0
 */
class Installer {

	/** @var array Available ecosystem plugins. */
	private $available_plugins = array();

	/**
	 * Constructor — defines available plugins and registers hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->define_available_plugins();
		add_action( 'admin_menu', array( $this, 'add_installer_page' ) );
		add_action( 'wp_ajax_wpseed_install_plugin', array( $this, 'ajax_install_plugin' ) );
	}

	/**
	 * Populate the available plugins list via a filterable array.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function define_available_plugins() {
		$this->available_plugins = apply_filters( 'wpseed_ecosystem_available_plugins', array(
			'wpseed' => array(
				'name'            => 'WPSeed',
				'description'     => 'WordPress plugin boilerplate for the EvolveWP ecosystem.',
				'download_url'    => 'https://github.com/FifeCIC/wpseed/archive/main.zip',
				'required_by'     => array(),
				'integrates_with' => array(),
			),
		) );
	}

	/**
	 * Register the installer submenu page under WPSeed settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_installer_page() {
		add_submenu_page(
			'wpseed-settings',
			__( 'Install Ecosystem Plugins', 'wpseed' ),
			__( 'Install Plugins', 'wpseed' ),
			'install_plugins',
			'wpseed-ecosystem-installer',
			array( $this, 'render_installer_page' )
		);
	}

	/**
	 * Render the installer page with plugin cards.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_installer_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Ecosystem Plugin Installer', 'wpseed' ); ?></h1>
			<p><?php esc_html_e( 'Install related EvolveWP plugins with one click.', 'wpseed' ); ?></p>

			<div class="ecosystem-plugins-grid">
				<?php foreach ( $this->available_plugins as $slug => $plugin ) : ?>
					<?php
					$is_installed = $this->is_plugin_installed( $slug );
					$is_active    = $this->is_plugin_active( $slug );
					?>
					<div class="plugin-card">
						<div class="plugin-card-top">
							<h3><?php echo esc_html( $plugin['name'] ); ?></h3>
							<p><?php echo esc_html( $plugin['description'] ); ?></p>

							<?php if ( ! empty( $plugin['integrates_with'] ) ) : ?>
								<p class="plugin-integrations">
									<strong><?php esc_html_e( 'Integrates with:', 'wpseed' ); ?></strong>
									<?php echo esc_html( implode( ', ', $plugin['integrates_with'] ) ); ?>
								</p>
							<?php endif; ?>
						</div>

						<div class="plugin-card-bottom">
							<?php if ( $is_active ) : ?>
								<span class="button button-disabled">
									<span class="dashicons dashicons-yes-alt"></span>
									<?php esc_html_e( 'Active', 'wpseed' ); ?>
								</span>
							<?php elseif ( $is_installed ) : ?>
								<a href="<?php echo esc_url( wp_nonce_url( 'plugins.php?action=activate&plugin=' . $slug, 'activate-plugin_' . $slug ) ); ?>" class="button button-primary">
									<?php esc_html_e( 'Activate', 'wpseed' ); ?>
								</a>
							<?php else : ?>
								<button class="button button-primary install-plugin"
										data-slug="<?php echo esc_attr( $slug ); ?>"
										data-name="<?php echo esc_attr( $plugin['name'] ); ?>">
									<?php esc_html_e( 'Install Now', 'wpseed' ); ?>
								</button>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX handler for plugin installation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_install_plugin() {
		check_ajax_referer( 'wpseed_install_plugin', 'nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( __( 'Insufficient permissions.', 'wpseed' ) );
		}

		if ( empty( $_POST['slug'] ) ) {
			wp_send_json_error( __( 'Plugin slug is required.', 'wpseed' ) );
		}

		$slug = sanitize_text_field( wp_unslash( $_POST['slug'] ) );

		if ( ! isset( $this->available_plugins[ $slug ] ) ) {
			wp_send_json_error( __( 'Invalid plugin.', 'wpseed' ) );
		}

		$plugin = $this->available_plugins[ $slug ];

		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';

		$upgrader = new \Plugin_Upgrader( new \WP_Ajax_Upgrader_Skin() );
		$result   = $upgrader->install( $plugin['download_url'] );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		wp_send_json_success();
	}

	/**
	 * Check if a plugin is installed (by slug prefix in plugin file path).
	 *
	 * @since 1.0.0
	 * @param string $slug Plugin slug.
	 * @return bool
	 */
	private function is_plugin_installed( $slug ) {
		foreach ( get_plugins() as $plugin_file => $plugin_data ) {
			if ( strpos( $plugin_file, $slug . '/' ) === 0 ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Check if a plugin is active (by slug prefix in plugin file path).
	 *
	 * @since 1.0.0
	 * @param string $slug Plugin slug.
	 * @return bool
	 */
	private function is_plugin_active( $slug ) {
		foreach ( get_plugins() as $plugin_file => $plugin_data ) {
			if ( strpos( $plugin_file, $slug . '/' ) === 0 ) {
				return is_plugin_active( $plugin_file );
			}
		}
		return false;
	}
}
