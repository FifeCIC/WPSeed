<?php
/**
 * WPSeed Example Widget
 *
 * Demonstrates the WordPress widget API. Rename WPSeed_Foo_Widget and update
 * the register_widget() call below when building a real widget.
 *
 * @package WPSeed/Widgets
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_Foo_Widget Class.
 *
 * Example widget — prefixed with WPSeed_ to satisfy WordPress global naming
 * standards. The class was previously named Foo_Widget (unprefixed).
 *
 * @since   1.0.0
 * @version 1.2.0
 */
class WPSeed_Foo_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'wpseed_foo_widget',
            __( 'Widget Title', 'wpseed' ),
            array( 'description' => __( 'A WPSeed example widget.', 'wpseed' ) )
        );
    }

    /**
     * Front-end display of widget.
     *
     * $args values (before_widget, after_widget, before_title, after_title) are
     * theme-generated HTML passed through WordPress core. wp_kses_post() is used
     * rather than esc_html() so that the structural HTML tags are preserved.
     *
     * @see WP_Widget::widget()
     *
     * @since   1.0.0
     * @version 1.2.0
     *
     * @param array $args     Widget arguments supplied by the theme.
     * @param array $instance Saved widget values from the database.
     * @return void
     */
    public function widget( $args, $instance ) {
        // Theme-supplied wrapper HTML — wp_kses_post() preserves structural tags.
        echo wp_kses_post( $args['before_widget'] );

        if ( ! empty( $instance['title'] ) ) {
            echo wp_kses_post( $args['before_title'] )
                . wp_kses_post( apply_filters( 'widget_title', $instance['title'] ) )
                . wp_kses_post( $args['after_title'] );
        }

        echo esc_html__( 'Hello, World!', 'wpseed' );

        echo wp_kses_post( $args['after_widget'] );
    }
}

add_action( 'widgets_init', function() {
    register_widget( 'WPSeed_Foo_Widget' );
} );
