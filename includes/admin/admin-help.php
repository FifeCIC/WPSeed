<?php
/**
 * Admin help tabs — contextual help for WPSeed admin screens.
 *
 * ROLE: admin-ui
 *
 * Adds help tabs to the WordPress Help panel (top-right "Help" button)
 * on all WPSeed admin screens. Includes FAQ with accordion, instructions,
 * support links, and about information.
 *
 * @package  WPSeed
 * @category Admin
 * @since    1.0.0
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPSeed_Admin_Help', false ) ) :

/**
 * Registers help tabs on WPSeed admin screens.
 *
 * @since   1.0.0
 * @version 3.1.0
 */
class WPSeed_Admin_Help {

	/**
	 * Hook in tabs.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'current_screen', array( $this, 'add_tabs' ), 50 );
	}

	/**
	 * Add contextual help tabs to WPSeed admin screens.
	 *
	 * @since   1.0.0
	 * @version 3.1.0
	 *
	 * @return void
	 */
	public function add_tabs() {
		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->id, wpseed_get_screen_ids(), true ) ) {
			return;
		}

		$screen->add_help_tab( array(
			'id'       => 'wpseed_instructions_tab',
			'title'    => __( 'Getting Started', 'wpseed' ),
			'content'  => '',
			'callback' => array( $this, 'instructions' ),
		) );

		$screen->add_help_tab( array(
			'id'       => 'wpseed_faq_tab',
			'title'    => __( 'FAQ', 'wpseed' ),
			'content'  => '',
			'callback' => array( $this, 'faq' ),
		) );

		$screen->add_help_tab( array(
			'id'      => 'wpseed_support_tab',
			'title'   => __( 'Help & Support', 'wpseed' ),
			'content' =>
				'<h2>' . esc_html__( 'Help & Support', 'wpseed' ) . '</h2>' .
				'<p>' . esc_html__( 'WPSeed is a boilerplate for building WordPress plugins. For guidance on extending it, refer to the docs/ directory in the plugin folder.', 'wpseed' ) . '</p>' .
				'<p><strong>' . esc_html__( 'Key documentation files:', 'wpseed' ) . '</strong></p>' .
				'<ul>' .
				'<li><code>docs/CLONING-GUIDE.md</code> — ' . esc_html__( 'How to create a new plugin from WPSeed', 'wpseed' ) . '</li>' .
				'<li><code>docs/CONNECTORS.md</code> — ' . esc_html__( 'Building API connectors', 'wpseed' ) . '</li>' .
				'<li><code>docs/CAPABILITIES.md</code> — ' . esc_html__( 'Custom capability management', 'wpseed' ) . '</li>' .
				'<li><code>docs/REST-BRIDGE.md</code> — ' . esc_html__( 'REST API endpoint registration', 'wpseed' ) . '</li>' .
				'<li><code>docs/ASSET-GUIDE.md</code> — ' . esc_html__( 'CSS/JS system and design tokens', 'wpseed' ) . '</li>' .
				'<li><code>docs/NAMING-CONVENTIONS.md</code> — ' . esc_html__( 'Prefix and naming patterns', 'wpseed' ) . '</li>' .
				'</ul>' .
				( defined( 'WPSEED_GITHUB' ) && WPSEED_GITHUB
					? '<p><a href="' . esc_url( WPSEED_GITHUB . '/issues' ) . '" class="button" target="_blank">' . esc_html__( 'Report an Issue', 'wpseed' ) . '</a></p>'
					: ''
				),
		) );

		$screen->add_help_tab( array(
			'id'      => 'wpseed_about_tab',
			'title'   => __( 'About', 'wpseed' ),
			'content' =>
				'<h2>' . esc_html__( 'About WPSeed', 'wpseed' ) . '</h2>' .
				'<p>' . esc_html__( 'WPSeed is a WordPress plugin boilerplate maintained by FifeCIC. It provides the foundation for the EvolveWP ecosystem — a suite of plugins for project management, client onboarding, and business operations.', 'wpseed' ) . '</p>' .
				'<p>' . sprintf(
					/* translators: %s: version number */
					esc_html__( 'Version: %s', 'wpseed' ),
					esc_html( WPSEED_VERSION )
				) . '</p>' .
				'<p><a href="https://evolvewp.dev" class="button" target="_blank">' . esc_html__( 'EvolveWP Website', 'wpseed' ) . '</a> ' .
				( defined( 'WPSEED_GITHUB' ) && WPSEED_GITHUB
					? '<a href="' . esc_url( WPSEED_GITHUB ) . '" class="button" target="_blank">' . esc_html__( 'GitHub', 'wpseed' ) . '</a>'
					: ''
				) . '</p>',
		) );

		$screen->set_help_sidebar(
			'<p><strong>' . esc_html__( 'Quick Links:', 'wpseed' ) . '</strong></p>' .
			'<p><a href="https://evolvewp.dev" target="_blank">' . esc_html__( 'EvolveWP', 'wpseed' ) . '</a></p>' .
			( defined( 'WPSEED_GITHUB' ) && WPSEED_GITHUB
				? '<p><a href="' . esc_url( WPSEED_GITHUB ) . '" target="_blank">' . esc_html__( 'GitHub', 'wpseed' ) . '</a></p>'
				: ''
			) .
			'<p><a href="https://fifecic.scot" target="_blank">' . esc_html__( 'FifeCIC', 'wpseed' ) . '</a></p>'
		);
	}

	/**
	 * Getting Started tab content.
	 *
	 * @since  3.1.0
	 *
	 * @return void
	 */
	public function instructions() {
		?>
		<h2><?php esc_html_e( 'Getting Started with WPSeed', 'wpseed' ); ?></h2>
		<p><?php esc_html_e( 'WPSeed is a boilerplate — a starting point for building WordPress plugins. It is not a finished product. Here is how to use it:', 'wpseed' ); ?></p>

		<div style="margin: 15px 0;">
			<div style="background: #f8f9fa; border-left: 4px solid #2271b1; padding: 12px 15px; margin: 8px 0; border-radius: 0 4px 4px 0;">
				<strong style="color: #2271b1;">1. <?php esc_html_e( 'Explore the Development Page', 'wpseed' ); ?></strong>
				<p style="margin: 5px 0 0;"><?php esc_html_e( 'Navigate to the Development menu item. The tabs show the plugin architecture, roadmap, available connectors, capabilities, and UI components. This is your reference while building.', 'wpseed' ); ?></p>
			</div>

			<div style="background: #f8f9fa; border-left: 4px solid #2271b1; padding: 12px 15px; margin: 8px 0; border-radius: 0 4px 4px 0;">
				<strong style="color: #2271b1;">2. <?php esc_html_e( 'Clone to Create Your Plugin', 'wpseed' ); ?></strong>
				<p style="margin: 5px 0 0;"><?php esc_html_e( 'Follow docs/CLONING-GUIDE.md to copy WPSeed into a new plugin directory and replace all prefixes with your own. This gives you a fully structured plugin in minutes.', 'wpseed' ); ?></p>
			</div>

			<div style="background: #f8f9fa; border-left: 4px solid #2271b1; padding: 12px 15px; margin: 8px 0; border-radius: 0 4px 4px 0;">
				<strong style="color: #2271b1;">3. <?php esc_html_e( 'Build Your Features', 'wpseed' ); ?></strong>
				<p style="margin: 5px 0 0;"><?php esc_html_e( 'Your cloned plugin inherits the connector system, capability manager, REST bridge, ecosystem registry, admin tabs, and the full CSS component library. Build on top of these foundations.', 'wpseed' ); ?></p>
			</div>

			<div style="background: #f8f9fa; border-left: 4px solid #2271b1; padding: 12px 15px; margin: 8px 0; border-radius: 0 4px 4px 0;">
				<strong style="color: #2271b1;">4. <?php esc_html_e( 'Read the Documentation', 'wpseed' ); ?></strong>
				<p style="margin: 5px 0 0;"><?php esc_html_e( 'The docs/ directory contains guides for every major system. Each PHP file has a ROLE header explaining what it does, what it depends on, and what consumes it.', 'wpseed' ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * FAQ tab content with accordion.
	 *
	 * @since  1.0.0
	 * @version 3.1.0
	 *
	 * @return void
	 */
	public function faq() {
		$faqs = array(
			array(
				'q' => __( 'What is WPSeed?', 'wpseed' ),
				'a' => __( 'WPSeed is a WordPress plugin boilerplate — a fully structured starting point for building new plugins. It provides PSR-4 autoloading, an ecosystem registry for multi-plugin communication, an API connector system, capability management, REST endpoint registration, admin development tabs, and a complete CSS component library. You clone it, rename the prefixes, and start building your features on top.', 'wpseed' ),
			),
			array(
				'q' => __( 'Is WPSeed a finished plugin I can use on a live site?', 'wpseed' ),
				'a' => __( 'No. WPSeed is a development tool, not a production plugin. It is designed to be cloned and transformed into your own plugin. Running WPSeed itself on a live site is harmless but pointless — it does not provide end-user features. The Development page and admin tabs are there to help developers understand the architecture while building.', 'wpseed' ),
			),
			array(
				'q' => __( 'How do I create a new plugin from WPSeed?', 'wpseed' ),
				'a' => __( 'Follow the step-by-step process in docs/CLONING-GUIDE.md. In short: copy the WPSeed directory, rename the main plugin file, run 7 find-and-replace passes to change all prefixes (WPSeed_ to YourPlugin_, wpseed_ to yourplugin_, etc.), update the plugin header and composer.json, delete the example files, and activate. The whole process takes about 10 minutes.', 'wpseed' ),
			),
			array(
				'q' => __( 'What is the Ecosystem Registry?', 'wpseed' ),
				'a' => __( 'The Ecosystem Registry allows multiple plugins built from WPSeed to detect each other and share resources. When two or more ecosystem plugins are active, shared menus appear under Tools and Settings, logging is unified, and background tasks are coordinated. Each plugin registers itself on the wpseed_ecosystem_register action.', 'wpseed' ),
			),
			array(
				'q' => __( 'What is the Connector System?', 'wpseed' ),
				'a' => __( 'The Connector System provides a standard way to integrate with external APIs. Every connector implements three methods: test_connection() to verify credentials, get_capabilities() to declare supported actions, and execute() to run those actions. The REST Bridge can auto-generate REST endpoints for any connector, so external tools (like AI assistants) can interact with APIs through your plugin.', 'wpseed' ),
			),
			array(
				'q' => __( 'What are the Development tabs for?', 'wpseed' ),
				'a' => __( 'The Development page (accessible from the admin menu) provides tabs for inspecting the plugin internals: Architecture shows the namespace map and boot sequence, Roadmap tracks development progress, Connectors shows registered API integrations, Capabilities shows permission management, and the Theme tab showcases all available UI components. These tabs are inherited by every plugin cloned from WPSeed.', 'wpseed' ),
			),
			array(
				'q' => __( 'How does the CSS system work?', 'wpseed' ),
				'a' => __( 'WPSeed uses CSS custom properties (design tokens) defined in assets/css/base/variables.css. All components use --wpseed-* variables for colours, spacing, typography, and shadows. Component CSS files are in assets/css/components/ and are loaded per-page via the style-assets.php registry — they only load where needed, not globally. See docs/ASSET-GUIDE.md for the full reference.', 'wpseed' ),
			),
			array(
				'q' => __( 'How do I add a custom capability to my plugin?', 'wpseed' ),
				'a' => __( 'Use the Capability Manager: call Capability_Manager::register() with a capability name, label, description, and default roles. The capability is installed into WordPress roles on plugin activation and removed on uninstall. Check permissions with wpseed_user_can(). See docs/CAPABILITIES.md for examples.', 'wpseed' ),
			),
			array(
				'q' => __( 'How do I register a REST API endpoint?', 'wpseed' ),
				'a' => __( 'Use the REST Bridge. In a controller that extends REST_Controller, call $this->register_endpoint() with the route, method, callback, capability, and a label/description for documentation. The endpoint is automatically registered with WordPress on rest_api_init and appears in the endpoint catalogue. See docs/REST-BRIDGE.md.', 'wpseed' ),
			),
			array(
				'q' => __( 'Do I need to give credit to WPSeed?', 'wpseed' ),
				'a' => __( 'WPSeed is licensed under GPL-3.0. You are free to use it for any purpose, including commercial plugins. Credit is appreciated but not required. If you find it useful, consider contributing back — report bugs, suggest improvements, or share it with other developers.', 'wpseed' ),
			),
			array(
				'q' => __( 'What is EvolveWP?', 'wpseed' ),
				'a' => __( 'EvolveWP is an ecosystem of WordPress plugins built on WPSeed. It includes EvolveWP Core (shared infrastructure), EvolveWP.Verifier (code quality scanning), EvolveWP.OpsStudio (project management), EvolveWP.ClientJourney (client onboarding), EvolveWP.PredictiveERP (business intelligence), and EvolveWP.Outreach (email campaigns). All are built from WPSeed and communicate via the Ecosystem Registry.', 'wpseed' ),
			),
		);
		?>
		<h2><?php esc_html_e( 'Frequently Asked Questions', 'wpseed' ); ?></h2>

		<div class="wpseed-faq-list" style="max-width: 800px;">
			<?php foreach ( $faqs as $index => $faq ) : ?>
				<div class="wpseed-faq-item" style="border: 1px solid #dcdcde; border-radius: 4px; margin-bottom: 8px; background: #fff;">
					<button type="button"
						class="wpseed-faq-toggle"
						aria-expanded="false"
						aria-controls="wpseed-faq-answer-<?php echo esc_attr( $index ); ?>"
						style="display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 12px 15px; background: none; border: none; cursor: pointer; font-size: 13px; font-weight: 600; color: #1d2327; text-align: left;">
						<span><?php echo esc_html( $faq['q'] ); ?></span>
						<span class="dashicons dashicons-arrow-down-alt2" style="flex-shrink: 0; transition: transform 0.2s;"></span>
					</button>
					<div id="wpseed-faq-answer-<?php echo esc_attr( $index ); ?>"
						class="wpseed-faq-answer"
						style="display: none; padding: 0 15px 15px; color: #50575e; line-height: 1.6;">
						<?php echo esc_html( $faq['a'] ); ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<script>
		jQuery( function( $ ) {
			$( '.wpseed-faq-toggle' ).on( 'click', function() {
				var $button = $( this );
				var $answer = $button.next( '.wpseed-faq-answer' );
				var $icon   = $button.find( '.dashicons' );
				var isOpen  = $button.attr( 'aria-expanded' ) === 'true';

				$button.attr( 'aria-expanded', ! isOpen );
				$answer.slideToggle( 200 );
				$icon.css( 'transform', isOpen ? 'rotate(0deg)' : 'rotate(180deg)' );
			} );
		} );
		</script>
		<?php
	}
}

endif;

return new WPSeed_Admin_Help();
