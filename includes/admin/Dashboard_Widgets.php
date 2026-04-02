<?php
/**
 * WordPress dashboard widgets for WPSeed.
 *
 * ROLE: admin-ui
 *
 * Single responsibility: Register and render WPSeed dashboard widgets on the
 * WordPress admin dashboard. Does NOT handle settings or plugin-specific pages.
 *
 * DEPENDS ON:
 *   - WordPress functions: wp_add_dashboard_widget, wp_cache_get
 *
 * CONSUMED BY:
 *   - Hook: wp_dashboard_setup (registered in constructor)
 *
 * DATA FLOW:
 *   Input  → wp_cache_get('wpseed_api_calls_today')
 *   Output → HTML rendered in WordPress dashboard widgets
 *
 * @package  WPSeed\Admin
 * @since    1.0.0
 */

namespace WPSeed\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and renders WPSeed dashboard widgets.
 *
 * Single responsibility: Dashboard widget UI only. Does NOT handle
 * data collection or settings.
 *
 * @since 1.0.0
 */
class Dashboard_Widgets {

	/**
	 * Constructor — hooks into wp_dashboard_setup.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'add_widgets' ) );
	}

	/**
	 * Register dashboard widgets.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function add_widgets() {
		wp_add_dashboard_widget(
			'wpseed_stats_widget',
			__( 'WPSeed Stats', 'wpseed' ),
			array( $this, 'render_stats_widget' )
		);

		wp_add_dashboard_widget(
			'wpseed_quick_links_widget',
			__( 'WPSeed Quick Links', 'wpseed' ),
			array( $this, 'render_quick_links_widget' )
		);
	}

	/**
	 * Render the stats widget.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function render_stats_widget() {
		$stats = $this->get_plugin_stats();
		?>
		<div class="wpseed-dashboard-widget">
			<ul>
				<li><strong><?php esc_html_e( 'Active Features:', 'wpseed' ); ?></strong> <?php echo (int) $stats['features']; ?></li>
				<li><strong><?php esc_html_e( 'API Calls Today:', 'wpseed' ); ?></strong> <?php echo (int) $stats['api_calls']; ?></li>
				<li><strong><?php esc_html_e( 'Cache Hit Rate:', 'wpseed' ); ?></strong> <?php echo (int) $stats['cache_rate']; ?>%</li>
			</ul>
			<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=wpseed-development' ) ); ?>" class="button button-primary"><?php esc_html_e( 'View Details', 'wpseed' ); ?></a></p>
		</div>
		<?php
	}

	/**
	 * Render the quick links widget.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function render_quick_links_widget() {
		?>
		<div class="wpseed-dashboard-widget">
			<ul>
				<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=wpseed-development' ) ); ?>"><?php esc_html_e( 'Development Dashboard', 'wpseed' ); ?></a></li>
				<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=wpseed-settings' ) ); ?>"><?php esc_html_e( 'Settings', 'wpseed' ); ?></a></li>
				<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=wpseed-learning' ) ); ?>"><?php esc_html_e( 'Learning Centre', 'wpseed' ); ?></a></li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Get basic plugin statistics for the dashboard widget.
	 *
	 * @since  1.0.0
	 * @return array{features: int, api_calls: int, cache_rate: int}
	 */
	private function get_plugin_stats() {
		return array(
			'features'   => 5,
			'api_calls'  => wp_cache_get( 'wpseed_api_calls_today' ) ?: 0,
			'cache_rate' => 85,
		);
	}
}
