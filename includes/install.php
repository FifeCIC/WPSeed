<?php
/**
 * WPSeed Installation Class
 *
 * @package WPSeed/Classes
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Install {

    public function __construct() {
        register_activation_hook(WPSEED_PLUGIN_FILE, array($this, 'install'));
        add_action('admin_init', array($this, 'check_version'), 5);
    }

    public function check_version() {
        if (get_option('wpseed_version') !== WPSEED_VERSION) {
            $this->install();
            do_action('wpseed_updated');
        }
    }

    public function install() {
        if ('yes' === get_transient('wpseed_installing')) {
            return;
        }

        set_transient('wpseed_installing', 'yes', MINUTE_IN_SECONDS * 10);
        
        $this->create_options();
        $this->create_roles();
        $this->setup_environment();
        $this->create_cron_jobs();
        
        delete_transient('wpseed_installing');
        
        delete_option('wpseed_version');
        add_option('wpseed_version', WPSEED_VERSION);
        
        flush_rewrite_rules();
        
        do_action('wpseed_installed');
    }

    private function create_options() {
        add_option('wpseed_installed', 'yes');
        add_option('wpseed_demo_mode', 'yes');
    }
    
    private function create_roles() {
        add_role(
            'wpseed_user',
            __('WPSeed User', 'wpseed'),
            array(
                'read' => true,
                'manage_wpseed' => true
            )
        );
        
        $admin = get_role('administrator');
        if ($admin) {
            $admin->add_cap('manage_wpseed');
        }
    }
    
    private function setup_environment() {
        $this->register_post_types();
        $this->register_taxonomies();
    }
    
    private function register_post_types() {
        if (!is_blog_installed() || post_type_exists('wpseed_item')) {
            return;
        }
        
        register_post_type('wpseed_item', array(
            'labels' => array(
                'name' => __('Items', 'wpseed'),
                'singular_name' => __('Item', 'wpseed'),
                'add_new' => __('Add Item', 'wpseed'),
                'edit_item' => __('Edit Item', 'wpseed'),
                'view_item' => __('View Item', 'wpseed')
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => 'wpseed',
            'supports' => array('title', 'editor', 'thumbnail'),
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'wpseed-item')
        ));
    }
    
    private function register_taxonomies() {
        if (!is_blog_installed() || taxonomy_exists('wpseed_category')) {
            return;
        }
        
        register_taxonomy('wpseed_category', array('wpseed_item'), array(
            'hierarchical' => true,
            'labels' => array(
                'name' => __('Categories', 'wpseed'),
                'singular_name' => __('Category', 'wpseed')
            ),
            'show_ui' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'wpseed-category')
        ));
    }
    
    private function create_cron_jobs() {
        // Example: Daily cleanup job (commented out by default)
        // if (!wp_next_scheduled('wpseed_daily_cleanup')) {
        //     wp_schedule_event(time(), 'daily', 'wpseed_daily_cleanup');
        // }
    }
}

new WPSeed_Install();
