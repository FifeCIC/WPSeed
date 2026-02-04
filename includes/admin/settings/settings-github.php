<?php
/**
 * GitHub Settings Tab
 *
 * @package WPSeed/Admin/Settings
 */

if (!defined('ABSPATH')) exit;

if (!class_exists('WPSeed_Settings_GitHub')) :

class WPSeed_Settings_GitHub extends WPSeed_Settings_Page {
    
    public function __construct() {
        $this->id    = 'github';
        $this->label = __('GitHub', 'wpseed');
        
        // Only show tab in dev environment
        if (WPSeed_Developer_Mode::is_dev_environment()) {
            parent::__construct();
        }
    }
    
    public function get_settings($current_section = '') {
        $settings = array(
            array(
                'title' => __('GitHub Integration', 'wpseed'),
                'type'  => 'title',
                'desc'  => __('Configure GitHub API access for documentation sync.', 'wpseed'),
                'id'    => 'github_options'
            ),
            
            array(
                'title'    => __('GitHub Token', 'wpseed'),
                'desc'     => __('Personal access token from GitHub Settings → Developer → Personal access tokens', 'wpseed'),
                'id'       => 'wpseed_github_token',
                'type'     => 'password',
                'default'  => '',
                'autoload' => false
            ),
            
            array(
                'title'    => __('Repository', 'wpseed'),
                'desc'     => __('Format: username/repository-name (e.g., yourusername/wpseed)', 'wpseed'),
                'id'       => 'wpseed_github_repo',
                'type'     => 'text',
                'default'  => '',
                'autoload' => false
            ),
            
            array(
                'title'    => __('Branch', 'wpseed'),
                'desc'     => __('Branch to sync documentation to', 'wpseed'),
                'id'       => 'wpseed_github_branch',
                'type'     => 'text',
                'default'  => 'main',
                'autoload' => false
            ),
            
            array(
                'title'    => __('Auto Sync', 'wpseed'),
                'desc'     => __('Automatically sync docs when files change', 'wpseed'),
                'id'       => 'wpseed_github_auto_sync',
                'type'     => 'checkbox',
                'default'  => 'no',
                'autoload' => false
            ),
            
            array(
                'title'    => __('Developer Domains', 'wpseed'),
                'desc'     => __('Comma-separated list of domains where developer features are enabled (e.g., mysite.local, dev.mysite.com)', 'wpseed'),
                'id'       => 'wpseed_dev_domains',
                'type'     => 'text',
                'default'  => '',
                'autoload' => false
            ),
            
            array(
                'type' => 'sectionend',
                'id'   => 'github_options'
            ),
            
            array(
                'title' => __('Connection Test', 'wpseed'),
                'type'  => 'title',
                'desc'  => __('Test your GitHub connection.', 'wpseed'),
                'id'    => 'github_test'
            ),
            
            array(
                'type' => 'github_test_button',
                'id'   => 'github_test_connection'
            ),
            
            array(
                'type' => 'sectionend',
                'id'   => 'github_test'
            )
        );
        
        return apply_filters('wpseed_get_settings_' . $this->id, $settings, $current_section);
    }
    
    public function output() {
        $settings = $this->get_settings();
        WPSeed_Admin_Settings::output_fields($settings);
    }
    
    public function save() {
        $settings = $this->get_settings();
        WPSeed_Admin_Settings::save_fields($settings);
    }
}

endif;

return new WPSeed_Settings_GitHub();
