<?php
/**
 * GitHub Sync AJAX Handlers
 *
 * @package WPSeed/AJAX
 */

if (!defined('ABSPATH')) exit;

add_action('wp_ajax_wpseed_sync_github_file', 'wpseed_ajax_sync_github_file');
add_action('wp_ajax_wpseed_sync_github_all', 'wpseed_ajax_sync_github_all');

function wpseed_ajax_sync_github_file() {
    check_ajax_referer('wpseed_github_sync', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $file = sanitize_text_field($_POST['file']);
    $path = sanitize_text_field($_POST['path']);
    
    $sync = new WPSeed_GitHub_Sync();
    $result = $sync->sync_to_github($path, $file);
    
    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    }
    
    wp_send_json_success();
}

function wpseed_ajax_sync_github_all() {
    check_ajax_referer('wpseed_github_sync', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $sync = new WPSeed_GitHub_Sync();
    $results = $sync->sync_all();
    
    wp_send_json_success($results);
}
