<?php
/**
 * Unified Example Feature
 * 
 * Same content/feature available as:
 * - Dashboard Widget
 * - Block (Gutenberg)
 * - Shortcode
 * - Sidebar Widget
 * 
 * @package WPSeed
 * @version 1.2.0
 */

defined( 'ABSPATH' ) || die;

class WPSeed_Unified_Feature {
    
    public function __construct() {
        // Dashboard Widget
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
        
        // Shortcode
        add_shortcode( 'wpseed_feature', array( $this, 'render_shortcode' ) );
        
        // Sidebar Widget
        add_action( 'widgets_init', array( $this, 'register_sidebar_widget' ) );
        
        // Block (Gutenberg)
        add_action( 'init', array( $this, 'register_block' ) );
    }
    
    /**
     * Core rendering function - used by all implementations
     */
    public static function render_content( $args = array() ) {
        $defaults = array(
            'title' => __( 'WPSeed Feature', 'wpseed' ),
            'show_icon' => true,
            'show_stats' => true
        );
        
        $args = wp_parse_args( $args, $defaults );
        
        ob_start();
        ?>
        <div class="wpseed-unified-feature">
            <?php if ( $args['show_icon'] ) : ?>
                <div class="feature-icon">🌱</div>
            <?php endif; ?>
            
            <h3><?php echo esc_html( $args['title'] ); ?></h3>
            
            <?php if ( $args['show_stats'] ) : ?>
                <div class="feature-stats">
                    <p><strong><?php esc_html_e( 'Active Users:', 'wpseed' ); ?></strong> <?php echo esc_html( self::get_active_users() ); ?></p>
                    <p><strong><?php esc_html_e( 'Total Posts:', 'wpseed' ); ?></strong> <?php echo esc_html( self::get_total_posts() ); ?></p>
                </div>
            <?php endif; ?>
            
            <p><?php esc_html_e( 'This feature demonstrates how the same content can be displayed across multiple WordPress contexts.', 'wpseed' ); ?></p>
        </div>
        
        <style>
        .wpseed-unified-feature { padding: 15px; background: #f9f9f9; border-radius: 4px; }
        .wpseed-unified-feature .feature-icon { font-size: 48px; text-align: center; margin-bottom: 10px; }
        .wpseed-unified-feature h3 { margin-top: 0; }
        .wpseed-unified-feature .feature-stats { margin: 15px 0; }
        </style>
        <?php
        return ob_get_clean();
    }
    
    // Dashboard Widget
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'wpseed_unified_feature',
            __( 'WPSeed Unified Feature', 'wpseed' ),
            array( $this, 'render_dashboard_widget' )
        );
    }
    
    public function render_dashboard_widget() {
        echo wp_kses_post( self::render_content() );
    }
    
    // Shortcode
    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'title' => __( 'WPSeed Feature', 'wpseed' ),
            'show_icon' => 'yes',
            'show_stats' => 'yes'
        ), $atts );
        
        return self::render_content( array(
            'title' => $atts['title'],
            'show_icon' => $atts['show_icon'] === 'yes',
            'show_stats' => $atts['show_stats'] === 'yes'
        ) );
    }
    
    // Sidebar Widget
    public function register_sidebar_widget() {
        register_widget( 'WPSeed_Unified_Feature_Widget' );
    }
    
    // Block
    public function register_block() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }
        
        register_block_type( 'wpseed/unified-feature', array(
            'render_callback' => array( $this, 'render_block' ),
            'attributes' => array(
                'title' => array( 'type' => 'string', 'default' => __( 'WPSeed Feature', 'wpseed' ) ),
                'showIcon' => array( 'type' => 'boolean', 'default' => true ),
                'showStats' => array( 'type' => 'boolean', 'default' => true )
            )
        ) );
    }
    
    public function render_block( $attributes ) {
        return self::render_content( array(
            'title' => $attributes['title'],
            'show_icon' => $attributes['showIcon'],
            'show_stats' => $attributes['showStats']
        ) );
    }
    
    // Helper functions
    private static function get_active_users() {
        return count( get_users( array( 'fields' => 'ID' ) ) );
    }
    
    private static function get_total_posts() {
        return wp_count_posts()->publish;
    }
}

// Sidebar Widget Class
class WPSeed_Unified_Feature_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'wpseed_unified_feature',
            __( 'WPSeed Unified Feature', 'wpseed' ),
            array( 'description' => __( 'Example unified feature widget', 'wpseed' ) )
        );
    }
    
    /**
     * Output the widget on the front end.
     *
     * $args['before_widget'] and $args['after_widget'] are HTML strings
     * generated by WordPress core via register_sidebar() — wp_kses_post()
     * is used to allow the expected markup whilst stripping anything
     * unexpected, satisfying the escaping requirement.
     *
     * @since   1.0.0
     * @version 1.2.0
     *
     * @param array $args     Display arguments including before/after widget markup.
     * @param array $instance The widget settings.
     * @return void
     */
    public function widget( $args, $instance ) {
        // before_widget is core-generated HTML; wp_kses_post allows expected markup.
        echo wp_kses_post( $args['before_widget'] );
        echo wp_kses_post( WPSeed_Unified_Feature::render_content( array(
            'title'      => ! empty( $instance['title'] ) ? $instance['title'] : __( 'WPSeed Feature', 'wpseed' ),
            'show_icon'  => ! empty( $instance['show_icon'] ),
            'show_stats' => ! empty( $instance['show_stats'] )
        ) ) );
        // after_widget is core-generated HTML; wp_kses_post allows expected markup.
        echo wp_kses_post( $args['after_widget'] );
    }
    
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'WPSeed Feature', 'wpseed' );
        $show_icon = ! empty( $instance['show_icon'] );
        $show_stats = ! empty( $instance['show_stats'] );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wpseed' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $show_icon ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_icon' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_icon' ) ); ?>">
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_icon' ) ); ?>"><?php esc_html_e( 'Show Icon', 'wpseed' ); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $show_stats ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_stats' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_stats' ) ); ?>">
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_stats' ) ); ?>"><?php esc_html_e( 'Show Stats', 'wpseed' ); ?></label>
        </p>
        <?php
    }
    
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['show_icon'] = ! empty( $new_instance['show_icon'] );
        $instance['show_stats'] = ! empty( $new_instance['show_stats'] );
        return $instance;
    }
}

return new WPSeed_Unified_Feature();
