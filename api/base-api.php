<?php
/**
 * WPSeed Base API Class
 *
 * @package WPSeed/API
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

abstract class WPSeed_Base_API {
    
    protected $provider_id;
    protected $api_key;
    protected $api_secret;
    protected $base_url;
    
    public function __construct($provider_id, $args = array()) {
        $this->provider_id = $provider_id;
        $this->api_key = $args['api_key'] ?? '';
        $this->api_secret = $args['api_secret'] ?? '';
        $this->base_url = $args['base_url'] ?? '';
    }
    
    abstract public function test_connection();
    
    protected function make_request($endpoint, $params = array(), $method = 'GET') {
        $call_id = WPSeed_API_Logging::log_call(
            $this->provider_id,
            $endpoint,
            $method,
            'pending'
        );
        
        $url = $this->base_url . $endpoint;
        
        $args = array(
            'method' => $method,
            'timeout' => 30,
            'headers' => $this->get_headers()
        );
        
        if ($method === 'GET' && !empty($params)) {
            $url = add_query_arg($params, $url);
        } elseif ($method === 'POST') {
            $args['body'] = json_encode($params);
        }
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            WPSeed_API_Logging::log_error($call_id, 'request_failed', $response->get_error_message());
            WPSeed_API_Logging::update_call_outcome($call_id, 'Error: ' . $response->get_error_message(), 'error');
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        WPSeed_API_Logging::update_call_outcome($call_id, 'Success', 'success');
        
        return $data;
    }
    
    protected function get_headers() {
        return array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key
        );
    }
    
    public function get_provider_id() {
        return $this->provider_id;
    }
}
