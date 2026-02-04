<?php
/**
 * WPSeed AI Assistant
 *
 * @package WPSeed/AI
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_AI_Assistant {
    
    private $context_manager;
    
    public function __construct() {
        $this->context_manager = new WPSeed_AI_Context_Manager();
        add_action('admin_init', array($this, 'init'));
    }
    
    public function init() {
        add_action('wp_ajax_wpseed_ai_request', array($this, 'process_ai_request'));
        add_action('wp_ajax_wpseed_ai_suggestions', array($this, 'get_suggestions_ajax'));
    }
    
    public function get_suggestions_ajax() {
        check_ajax_referer('wpseed_ai_nonce', 'nonce');
        
        $suggestions = $this->get_proactive_suggestions();
        
        wp_send_json(array(
            'success' => true,
            'suggestions' => $suggestions
        ));
    }
    
    public function process_ai_request() {
        check_ajax_referer('wpseed_ai_nonce', 'nonce');
        
        $prompt = sanitize_textarea_field($_POST['prompt'] ?? '');
        $task_type = sanitize_text_field($_POST['task_type'] ?? 'general');
        $context = $this->context_manager->get_context();
        
        // Get AI provider based on task type
        $provider = WPSeed_AI_Provider_Factory::get_provider(null, $task_type);
        
        if (!$provider) {
            wp_send_json(array(
                'success' => false,
                'message' => 'AI provider not available or rate limit exceeded'
            ));
        }
        
        // Generate response
        $result = $provider->generate_response($prompt, $context);
        
        // Track usage
        if ($result['success']) {
            WPSeed_AI_Usage_Tracker::track_call(
                $result['provider'] ?? 'unknown',
                $task_type,
                true,
                0
            );
        } else {
            WPSeed_AI_Usage_Tracker::track_call(
                $result['provider'] ?? 'unknown',
                $task_type,
                false,
                0
            );
        }
        
        // Log activity
        $this->context_manager->add_activity('ai_request', 'AI request made', array(
            'prompt' => substr($prompt, 0, 100),
            'task_type' => $task_type
        ));
        
        wp_send_json($result);
    }
    
    public function get_proactive_suggestions() {
        $context = $this->context_manager->get_context();
        
        $suggestions = array();
        
        if (!empty($context['current_file'])) {
            $suggestions[] = array(
                'id' => 'file_' . uniqid(),
                'type' => 'info',
                'title' => 'File Context',
                'description' => 'Working on: ' . basename($context['current_file'])
            );
        }
        
        return $suggestions;
    }
}
