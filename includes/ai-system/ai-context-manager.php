<?php
/**
 * WPSeed AI Context Manager
 *
 * @package WPSeed/AI
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_AI_Context_Manager {
    
    private $context = array();
    
    public function __construct() {
        $this->load_context();
    }
    
    private function load_context() {
        $user_id = get_current_user_id();
        $saved = get_user_meta($user_id, 'wpseed_ai_context', true);
        
        $this->context = !empty($saved) ? $saved : array(
            'current_file' => '',
            'recent_activities' => array(),
            'last_updated' => current_time('mysql')
        );
    }
    
    private function save_context() {
        update_user_meta(get_current_user_id(), 'wpseed_ai_context', $this->context);
    }
    
    public function update_current_file($file_path) {
        $this->context['current_file'] = $file_path;
        $this->context['last_updated'] = current_time('mysql');
        $this->save_context();
    }
    
    public function add_activity($type, $description, $metadata = array()) {
        array_unshift($this->context['recent_activities'], array(
            'type' => $type,
            'description' => $description,
            'timestamp' => current_time('mysql'),
            'metadata' => $metadata
        ));
        
        $this->context['recent_activities'] = array_slice($this->context['recent_activities'], 0, 20);
        $this->save_context();
    }
    
    public function get_context() {
        return $this->context;
    }
}
