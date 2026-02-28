<?php
/**
 * WPSeed - Toolbars Class
 *
 * Add menus to the admin toolbar
 *
 * @package WPSeed/Toolbars
 * @since 1.0.0
 */
 
if (!defined('ABSPATH')) {
    exit;
}  

if (!class_exists('WPSeed_Toolbars')) :

class WPSeed_Toolbars {
    
    public function __construct() {     
        add_action('wp_before_admin_bar_render', array($this, 'admin_only_toolbars'));
        
        // Register admin post handlers
        add_action('admin_post_wpseed_demo_mode_switch', array($this, 'handle_demo_mode_switch'));
        add_action('admin_post_wpseed_reset_pointers', array($this, 'handle_reset_pointers'));
        add_action('admin_post_wpseed_clear_cache', array($this, 'handle_clear_cache'));
    }   
    
    public function admin_only_toolbars() {       
        if (!current_user_can('activate_plugins')) return;  
        
        // Include QuickTools toolbar
        include_once('toolbar-quicktools.php');
        
        // Include developer toolbar (developer mode only)
        if (wpseed_is_developer_mode() && current_user_can('manage_options')) {
            include_once('toolbar-developers.php');
        }
    }
    
    /**
     * Handle demo mode toggle
     */
    public function handle_demo_mode_switch() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Insufficient permissions', 'wpseed'));
        }

        $current_status = get_option('wpseed_demo_mode', false);
        $new_status = !$current_status;
        
        update_option('wpseed_demo_mode', $new_status);
        
        $status_text = $new_status ? __('enabled', 'wpseed') : __('disabled', 'wpseed');
        /* translators: %s: Status text (enabled or disabled) */
        $message = sprintf(__('Demo mode has been %s.', 'wpseed'), $status_text);
        
        set_transient('wpseed_admin_notice', array(
            'type' => 'success',
            'message' => $message
        ), 30);
        
        wp_safe_redirect(wp_get_referer() ?: admin_url());
        exit;
    }
    
    /**
     * Handle reset all pointers
     */
    public function handle_reset_pointers() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Insufficient permissions', 'wpseed'));
        }

        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wpseed_reset_pointers')) {
            wp_die(esc_html__('Security check failed', 'wpseed'));
        }
        
        delete_user_meta(get_current_user_id(), 'dismissed_wp_pointers');
        
        set_transient('wpseed_admin_notice', array(
            'type' => 'success',
            'message' => __('All pointers have been reset.', 'wpseed')
        ), 30);
        
        wp_safe_redirect(wp_get_referer() ?: admin_url());
        exit;
    }
} 

endif;

return new WPSeed_Toolbars();
