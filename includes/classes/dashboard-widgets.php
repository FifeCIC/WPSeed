<?php
/**
 * Dashboard Widgets
 * 
 * @package WPSeed
 */

defined( 'ABSPATH' ) || die;

class WPSeed_Dashboard_Widgets {
    
    public function __construct() {
        add_action( 'wp_dashboard_setup', array( $this, 'add_widgets' ) );
    }
    
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
    
    public function render_stats_widget() {
        $stats = $this->get_plugin_stats();
        ?>
        <div class="wpseed-dashboard-widget">
            <ul>
                <li><strong><?php _e( 'Active Features:', 'wpseed' ); ?></strong> <?php echo (int) $stats['features']; ?></li>
                <li><strong><?php _e( 'API Calls Today:', 'wpseed' ); ?></strong> <?php echo (int) $stats['api_calls']; ?></li>
                <li><strong><?php _e( 'Cache Hit Rate:', 'wpseed' ); ?></strong> <?php echo (int) $stats['cache_rate']; ?>%</li>
            </ul>
            <p><a href="<?php echo admin_url( 'admin.php?page=wpseed-development' ); ?>" class="button button-primary"><?php _e( 'View Details', 'wpseed' ); ?></a></p>
        </div>
        <?php
    }
    
    public function render_quick_links_widget() {
        ?>
        <div class="wpseed-dashboard-widget">
            <ul>
                <li><a href="<?php echo admin_url( 'admin.php?page=wpseed-development' ); ?>"><?php _e( 'Development Dashboard', 'wpseed' ); ?></a></li>
                <li><a href="<?php echo admin_url( 'admin.php?page=wpseed-settings' ); ?>"><?php _e( 'Settings', 'wpseed' ); ?></a></li>
                <li><a href="<?php echo admin_url( 'admin.php?page=wpseed-learning' ); ?>"><?php _e( 'Learning Centre', 'wpseed' ); ?></a></li>
            </ul>
        </div>
        <?php
    }
    
    private function get_plugin_stats() {
        return array(
            'features' => 5,
            'api_calls' => wp_cache_get( 'wpseed_api_calls_today' ) ?: 0,
            'cache_rate' => 85
        );
    }
}

return new WPSeed_Dashboard_Widgets();
