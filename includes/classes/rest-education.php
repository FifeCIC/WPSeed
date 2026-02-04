<?php
/**
 * Education REST API Controller
 * Exports lessons for website documentation sync
 *
 * @package WPSeed/API
 */

if (!defined('ABSPATH')) exit;

class WPSeed_REST_Education_Controller extends WPSeed_REST_Controller {
    
    protected $rest_base = 'education';
    
    public function register_routes() {
        register_rest_route($this->namespace, '/' . $this->rest_base . '/export', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array($this, 'export_lessons'),
            'permission_callback' => array($this, 'get_items_permissions_check'),
        ));
    }
    
    public function export_lessons($request) {
        $education = new WPSeed_Education();
        $data = $education->export_for_website();
        
        return rest_ensure_response(array(
            'success' => true,
            'data'    => $data
        ));
    }
}
