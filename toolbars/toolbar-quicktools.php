<?php
/**
 * Admin Toolbar - Quick Tools
 * 
 * @package WPSeed
 */

defined( 'ABSPATH' ) || die;

global $wp_admin_bar;

$wp_admin_bar->add_menu( array(
    'id'    => 'wpseed_toolbar',
    'title' => '⚡ WPSeed',
    'href'  => admin_url( 'admin.php?page=wpseed-development' ),
) );

$wp_admin_bar->add_menu( array(
    'parent' => 'wpseed_toolbar',
    'id'     => 'wpseed_development',
    'title'  => 'Development',
    'href'   => admin_url( 'admin.php?page=wpseed-development' ),
) );

$wp_admin_bar->add_menu( array(
    'parent' => 'wpseed_toolbar',
    'id'     => 'wpseed_settings',
    'title'  => 'Settings',
    'href'   => admin_url( 'admin.php?page=wpseed-settings' ),
) );

if ( function_exists( 'wpseed_is_developer_mode' ) && wpseed_is_developer_mode() ) {
    $wp_admin_bar->add_menu( array(
        'parent' => 'wpseed_toolbar',
        'id'     => 'wpseed_clear_cache',
        'title'  => 'Clear Cache',
        'href'   => wp_nonce_url( admin_url( 'admin-post.php?action=wpseed_clear_cache' ), 'wpseed_clear_cache' ),
    ) );
}
