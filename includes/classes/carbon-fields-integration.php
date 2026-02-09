<?php
/**
 * WPSeed Carbon Fields Integration
 *
 * @package WPSeed
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class WPSeed_Carbon_Fields {

    private static $instance = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('after_setup_theme', array($this, 'load'));
        add_action('carbon_fields_register_fields', array($this, 'register_fields'));
    }

    public function load() {
        \Carbon_Fields\Carbon_Fields::boot();
    }

    public function register_fields() {
        // Hook for custom field registration
        do_action('wpseed_carbon_fields_register');
    }

    /**
     * Create theme options page
     */
    public static function create_options_page($title, $slug, $parent = null) {
        $container = Container::make('theme_options', $slug, $title);
        
        if ($parent) {
            $container->set_page_parent($parent);
        }
        
        return $container;
    }

    /**
     * Create post meta box
     */
    public static function create_post_meta($title, $post_types = array('post')) {
        return Container::make('post_meta', $title)
            ->where('post_type', 'IN', $post_types);
    }

    /**
     * Create term meta
     */
    public static function create_term_meta($title, $taxonomies = array('category')) {
        return Container::make('term_meta', $title)
            ->where('term_taxonomy', 'IN', $taxonomies);
    }

    /**
     * Create user meta
     */
    public static function create_user_meta($title) {
        return Container::make('user_meta', $title);
    }
}

// Initialize
WPSeed_Carbon_Fields::instance();
