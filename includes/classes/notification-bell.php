<?php
/**
 * WPSeed Notification Bell
 * Admin bar notification indicator
 *
 * @package WPSeed/Admin
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Notification_Bell {
    
    public static function init() {
        add_action('admin_bar_menu', array(__CLASS__, 'add_notification_bell'), 999);
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
    }
    
    public static function add_notification_bell($wp_admin_bar) {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $unread_count = WPSeed_Notifications::get_unread_count(get_current_user_id());
        
        $wp_admin_bar->add_node(array(
            'id' => 'wpseed-notifications',
            'title' => self::get_bell_html($unread_count),
            'href' => admin_url('admin.php?page=wpseed-notifications'),
            'meta' => array(
                'class' => 'wpseed-notification-bell',
            ),
        ));
    }
    
    private static function get_bell_html($count) {
        $html = '<span class="ab-icon dashicons dashicons-bell"></span>';
        
        if ($count > 0) {
            $html .= '<span class="wpseed-notification-count">' . $count . '</span>';
        }
        
        return $html;
    }
    
    public static function enqueue_assets() {
        wp_add_inline_style('admin-bar', '
            #wp-admin-bar-wpseed-notifications .ab-icon {
                font-size: 20px;
                margin-top: 2px;
            }
            .wpseed-notification-count {
                display: inline-block;
                background: #d63638;
                color: #fff;
                border-radius: 10px;
                padding: 2px 6px;
                font-size: 11px;
                font-weight: 600;
                margin-left: 5px;
                line-height: 1;
            }
            #wp-admin-bar-wpseed-notifications:hover .ab-icon {
                color: #00a0d2;
            }
        ');
    }
}

WPSeed_Notification_Bell::init();
