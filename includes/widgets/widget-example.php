<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Adds WPSeed_Widget widget.
 */
class WPSeed_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */                                
    function __construct() {
        parent::__construct(
            'wpseed_widget', // Base ID
            __( 'Widget Title', 'wpseed' ), // Name
            array( 'description' => __( 'A Foo Widget', 'wpseed' ), ) // Args
        );
    }
                                              
    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        echo esc_html__( 'Hello, World!', 'wpseed' );
        echo $args['after_widget'];
    }
}