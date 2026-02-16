<?php
/**
 * WPSeed License Client
 * 
 * Handles license validation, updates, and premium feature activation
 * 
 * @package WPSeed
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPSeed_License_Client {
    
    private $license_server_url;
    private $plugin_slug;
    private $plugin_version;
    private $option_name = 'wpseed_license_data';
    
    public function __construct() {
        $this->license_server_url = defined('WPSEED_LICENSE_SERVER') ? WPSEED_LICENSE_SERVER : '';
        $this->plugin_slug = 'wpseed';
        $this->plugin_version = WPSEED_VERSION;
        
        // License system disabled - pending full development
        // add_action('admin_init', array($this, 'check_license_status'));
        // add_action('admin_notices', array($this, 'license_notices'));
        // add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_updates'));
        // add_filter('plugins_api', array($this, 'plugin_info'), 10, 3);
    }
    
    /**
     * Activate license
     */
    public function activate_license($license_key) {
        $response = $this->api_request('activate', array(
            'license_key' => $license_key,
            'domain' => $this->get_domain(),
            'plugin_slug' => $this->plugin_slug,
            'plugin_version' => $this->plugin_version,
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        if ($response['success']) {
            $this->save_license_data(array(
                'license_key' => $license_key,
                'status' => 'active',
                'expires' => $response['expires'],
                'license_type' => $response['license_type'],
                'activated_at' => time(),
            ));
            
            return true;
        }
        
        return new WP_Error('activation_failed', $response['message']);
    }
    
    /**
     * Deactivate license
     */
    public function deactivate_license() {
        $license_data = $this->get_license_data();
        
        if (!$license_data || !isset($license_data['license_key'])) {
            return new WP_Error('no_license', 'No license key found');
        }
        
        $response = $this->api_request('deactivate', array(
            'license_key' => $license_data['license_key'],
            'domain' => $this->get_domain(),
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        delete_option($this->option_name);
        
        return true;
    }
    
    /**
     * Check license status
     */
    public function check_license_status() {
        $license_data = $this->get_license_data();
        
        if (!$license_data || !isset($license_data['license_key'])) {
            return;
        }
        
        // Check once per day
        $last_check = get_transient('wpseed_license_check');
        if ($last_check) {
            return;
        }
        
        $response = $this->api_request('check', array(
            'license_key' => $license_data['license_key'],
            'domain' => $this->get_domain(),
        ));
        
        if (!is_wp_error($response) && isset($response['status'])) {
            $license_data['status'] = $response['status'];
            $license_data['expires'] = $response['expires'];
            $this->save_license_data($license_data);
        }
        
        set_transient('wpseed_license_check', time(), DAY_IN_SECONDS);
    }
    
    /**
     * Check for plugin updates
     */
    public function check_for_updates($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        $license_data = $this->get_license_data();
        
        if (!$this->is_license_valid()) {
            return $transient;
        }
        
        $response = $this->api_request('update_check', array(
            'license_key' => $license_data['license_key'],
            'plugin_slug' => $this->plugin_slug,
            'current_version' => $this->plugin_version,
        ));
        
        if (is_wp_error($response)) {
            return $transient;
        }
        
        if (isset($response['new_version']) && version_compare($this->plugin_version, $response['new_version'], '<')) {
            $plugin_file = plugin_basename(WPSEED_PLUGIN_DIR_PATH . 'wpseed.php');
            
            $transient->response[$plugin_file] = (object) array(
                'slug' => $this->plugin_slug,
                'new_version' => $response['new_version'],
                'package' => $response['download_url'],
                'url' => $response['info_url'],
            );
        }
        
        return $transient;
    }
    
    /**
     * Plugin information for update screen
     */
    public function plugin_info($false, $action, $args) {
        if ($action !== 'plugin_information' || $args->slug !== $this->plugin_slug) {
            return $false;
        }
        
        $license_data = $this->get_license_data();
        
        if (!$this->is_license_valid()) {
            return $false;
        }
        
        $response = $this->api_request('plugin_info', array(
            'license_key' => $license_data['license_key'],
            'plugin_slug' => $this->plugin_slug,
        ));
        
        if (is_wp_error($response)) {
            return $false;
        }
        
        return (object) $response;
    }
    
    /**
     * Check if premium feature is available
     */
    public function is_premium_feature_available($feature = null) {
        if (!$this->is_license_valid()) {
            return false;
        }
        
        $license_data = $this->get_license_data();
        
        // Check license type supports feature
        if ($feature && isset($license_data['features'])) {
            return in_array($feature, $license_data['features']);
        }
        
        return true;
    }
    
    /**
     * Check if license is valid
     */
    public function is_license_valid() {
        $license_data = $this->get_license_data();
        
        if (!$license_data || !isset($license_data['status'])) {
            return false;
        }
        
        if ($license_data['status'] !== 'active') {
            return false;
        }
        
        // Check expiration
        if (isset($license_data['expires']) && $license_data['expires'] !== 'lifetime') {
            if (strtotime($license_data['expires']) < time()) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get license data
     */
    public function get_license_data() {
        return get_option($this->option_name, array());
    }
    
    /**
     * Save license data
     */
    private function save_license_data($data) {
        update_option($this->option_name, $data);
    }
    
    /**
     * Make API request to license server
     */
    private function api_request($endpoint, $data = array()) {
        if (empty($this->license_server_url)) {
            return new WP_Error('no_server', 'License server URL not configured');
        }
        
        $url = trailingslashit($this->license_server_url) . 'wp-json/wpseed-license/v1/' . $endpoint;
        
        $response = wp_remote_post($url, array(
            'timeout' => 15,
            'body' => $data,
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_response', 'Invalid JSON response from server');
        }
        
        return $data;
    }
    
    /**
     * Get current domain
     */
    private function get_domain() {
        return parse_url(home_url(), PHP_URL_HOST);
    }
    
    /**
     * Show license notices
     */
    public function license_notices() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $license_data = $this->get_license_data();
        
        // No license
        if (empty($license_data)) {
            echo '<div class="notice notice-warning"><p>';
            echo '<strong>WPSeed:</strong> ';
            echo 'No license key activated. <a href="' . esc_url( admin_url('admin.php?page=wpseed-license') ) . '">Activate your license</a> to receive updates and premium features.';
            echo '</p></div>';
            return;
        }
        
        // Expired license
        if (isset($license_data['expires']) && $license_data['expires'] !== 'lifetime') {
            $expires = strtotime($license_data['expires']);
            $days_left = floor(($expires - time()) / DAY_IN_SECONDS);
            
            if ($days_left < 0) {
                echo '<div class="notice notice-error"><p>';
                echo '<strong>WPSeed:</strong> ';
                echo 'Your license has expired. <a href="' . esc_url( admin_url('admin.php?page=wpseed-license') ) . '">Renew your license</a> to continue receiving updates.';
                echo '</p></div>';
            } elseif ($days_left < 30) {
                echo '<div class="notice notice-warning"><p>';
                echo '<strong>WPSeed:</strong> ';
                echo 'Your license expires in ' . esc_html( $days_left ) . ' days. <a href="' . esc_url( admin_url('admin.php?page=wpseed-license') ) . '">Renew now</a> to avoid interruption.';
                echo '</p></div>';
            }
        }
    }
}

// Initialize - disabled pending full development
// new WPSeed_License_Client();
