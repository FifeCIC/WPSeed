<?php
/**
 * WPSeed API Directory
 *
 * @package WPSeed/API
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_API_Directory {
    
    public static function get_all_providers() {
        return array(
            'custom_api' => array(
                'name' => 'Custom API',
                'description' => 'Generic REST API integration',
                'url' => '',
                'api_doc_url' => '',
                'class_path' => 'custom/custom-api.php',
                'class_name' => 'WPSeed_Custom_API',
                'auth_type' => 'api_key',
                'features' => array('data_retrieval' => true)
            ),
            'discord' => array(
                'name' => 'Discord Webhook',
                'description' => 'Send notifications via Discord',
                'url' => 'https://discord.com/',
                'api_doc_url' => 'https://discord.com/developers/docs/resources/webhook',
                'class_path' => 'discord/discord-api.php',
                'class_name' => 'WPSeed_Discord_API',
                'auth_type' => 'webhook_url',
                'features' => array('notifications' => true)
            )
        );
    }
    
    public static function get_provider($provider_id) {
        $providers = self::get_all_providers();
        return $providers[$provider_id] ?? false;
    }
}
