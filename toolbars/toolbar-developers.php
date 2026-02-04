<?php
/**
 * WPSeed - Developer Toolbar
 *
 * @package WPSeed/Toolbars
 * @since 1.0.0
 */
 
if (!defined('ABSPATH')) {
    exit;
}  

if (!class_exists('WPSeed_Admin_Toolbar_Developers')) :

class WPSeed_Admin_Toolbar_Developers {
    public function __construct() {
        if (!current_user_can('manage_options')) {
            return false;
        }
        
        $this->init(); 
    }    
    
    private function init() {
        global $wp_admin_bar;  

        self::parent_level();
        self::second_level_tools();
    }

    private static function parent_level() {
        global $wp_admin_bar;   
        
        $args = array(
            'id'     => 'wpseed-toolbarmenu-developers',
            'title'  => __('WPSeed Dev', 'wpseed'),          
        );
        $wp_admin_bar->add_menu($args);        
    }
    
    private static function second_level_tools() {
        global $wp_admin_bar;
        
        // Group - Developer Tools
        $args = array(
            'id'     => 'wpseed-toolbarmenu-devtools',
            'parent' => 'wpseed-toolbarmenu-developers',
            'title'  => __('Developer Tools', 'wpseed'), 
            'meta'   => array('class' => 'second-toolbar-group')         
        );        
        $wp_admin_bar->add_menu($args);        
            
        // Demo Mode Switch
        $thisaction = 'wpseed_demo_mode_switch';
        $href = admin_url('admin-post.php?action=' . $thisaction);
        
        $is_demo = get_option('wpseed_demo_mode', false);
        
        if ($is_demo) {
            $title = __('✅ Demo Mode: ON', 'wpseed');        
        } else {
            $title = __('❌ Demo Mode: OFF', 'wpseed');    
        }
           
        $args = array(
            'id'     => 'wpseed-toolbarmenu-toggledemomode',
            'parent' => 'wpseed-toolbarmenu-devtools',
            'title'  => $title,
            'href'   => esc_url($href),            
        );
        
        $wp_admin_bar->add_menu($args);
        
        // Reset Pointers
        $thisaction = 'wpseed_reset_pointers';
        $href = admin_url('admin-post.php?action=' . $thisaction);
        
        $args = array(
            'id'     => 'wpseed-toolbarmenu-resetpointers',
            'parent' => 'wpseed-toolbarmenu-devtools',
            'title'  => __('Reset Pointers', 'wpseed'),
            'href'   => esc_url(wp_nonce_url($href, 'wpseed_reset_pointers')),
        );
        
        $wp_admin_bar->add_menu($args);
        
        // Link to Development Page
        $args = array(
            'id'     => 'wpseed-toolbarmenu-devpage',
            'parent' => 'wpseed-toolbarmenu-devtools',
            'title'  => __('Development Page', 'wpseed'),
            'href'   => admin_url('admin.php?page=wpseed_development'),
        );
        
        $wp_admin_bar->add_menu($args);
    }
}   

endif;

if (current_user_can('manage_options')) {
    return new WPSeed_Admin_Toolbar_Developers();
}
