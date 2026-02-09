<?php
/**
 * Elementor Integration Example
 *
 * @package WPSeed/Examples/Integrations
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_Elementor_Integration
 */
class WPSeed_Elementor_Integration {

    public function __construct() {
        // Check if Elementor is active
        if ( ! did_action( 'elementor/loaded' ) ) {
            return;
        }

        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
        add_action( 'elementor/elements/categories_registered', array( $this, 'add_widget_category' ) );
    }

    /**
     * Register custom widgets
     */
    public function register_widgets( $widgets_manager ) {
        require_once __DIR__ . '/elementor-widgets/custom-widget.php';
        $widgets_manager->register( new \WPSeed_Elementor_Custom_Widget() );
    }

    /**
     * Add custom widget category
     */
    public function add_widget_category( $elements_manager ) {
        $elements_manager->add_category(
            'wpseed',
            array(
                'title' => __( 'WPSeed', 'wpseed' ),
                'icon'  => 'fa fa-plug',
            )
        );
    }
}

/**
 * Custom Elementor Widget
 */
class WPSeed_Elementor_Custom_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'wpseed_custom_widget';
    }

    public function get_title() {
        return __( 'Custom Widget', 'wpseed' );
    }

    public function get_icon() {
        return 'eicon-code';
    }

    public function get_categories() {
        return array( 'wpseed' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            array(
                'label' => __( 'Content', 'wpseed' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'title',
            array(
                'label'       => __( 'Title', 'wpseed' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => __( 'Default Title', 'wpseed' ),
                'placeholder' => __( 'Enter title', 'wpseed' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        echo '<div class="wpseed-widget">';
        echo '<h2>' . esc_html( $settings['title'] ) . '</h2>';
        echo '</div>';
    }
}

// Initialize
new WPSeed_Elementor_Integration();
