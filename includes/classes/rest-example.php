<?php
/**
 * Example REST API Endpoint
 *
 * @package WPSeed/API
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_REST_Example_Controller extends WPSeed_REST_Controller {
    
    protected $rest_base = 'example';
    
    public function register_routes() {
        register_rest_route($this->namespace, '/' . $this->rest_base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_items'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
            ),
        ));
    }
    
    public function get_items($request) {
        return rest_ensure_response(array(
            'status' => 'success',
            'data'   => array('message' => 'WPSeed REST API is working!')
        ));
    }
}
