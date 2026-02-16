<?php
/**
 * GitHub Documentation Sync
 * Syncs plugin docs to GitHub Pages and README
 *
 * @package WPSeed/GitHub
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

class WPSeed_GitHub_Sync {
    
    private $docs_path;
    private $github_token;
    private $github_repo;
    
    public function __construct() {
        $this->docs_path = plugin_dir_path(WPSEED_PLUGIN_FILE) . 'docs/';
        $this->github_token = get_option('wpseed_github_token', '');
        $this->github_repo = get_option('wpseed_github_repo', ''); // format: username/repo
        
        // Only add menu in dev environment
        if (WPSeed_Developer_Mode::is_dev_environment()) {
            add_action('admin_menu', array($this, 'add_menu'));
        }
    }
    
    public function add_menu() {
        add_submenu_page(
            'wpseed-settings',
            'GitHub Sync',
            'GitHub Sync',
            'manage_options',
            'wpseed-github-sync',
            array($this, 'render_page')
        );
    }
    
    public function render_page() {
        $docs = $this->scan_local_docs();
        $status = $this->check_sync_status($docs);
        include dirname(__FILE__) . '/../admin/views/github-sync.php';
    }
    
    public function scan_local_docs() {
        $docs = array();
        $files = glob($this->docs_path . '*.md');
        
        foreach ($files as $file) {
            $docs[] = array(
                'name' => basename($file),
                'path' => $file,
                'content' => file_get_contents($file),
                'modified' => filemtime($file),
                'hash' => md5_file($file)
            );
        }
        
        return $docs;
    }
    
    public function check_sync_status($docs) {
        $status = array();
        
        foreach ($docs as $doc) {
            $github_hash = get_transient('wpseed_github_hash_' . $doc['name']);
            $status[$doc['name']] = array(
                'synced' => ($github_hash === $doc['hash']),
                'local_modified' => gmdate('Y-m-d H:i:s', $doc['modified']),
                'needs_sync' => ($github_hash !== $doc['hash'])
            );
        }
        
        return $status;
    }
    
    public function sync_to_github($file_path, $file_name) {
        if (empty($this->github_token) || empty($this->github_repo)) {
            return new WP_Error('missing_config', 'GitHub token or repo not configured');
        }
        
        $content = file_get_contents($file_path);
        $encoded_content = base64_encode($content);
        
        // Get current file SHA (if exists)
        $sha = $this->get_github_file_sha($file_name);
        
        $api_url = "https://api.github.com/repos/{$this->github_repo}/contents/docs/{$file_name}";
        
        $body = array(
            'message' => "Update {$file_name} via WPSeed",
            'content' => $encoded_content,
            'branch' => 'main'
        );
        
        if ($sha) {
            $body['sha'] = $sha;
        }
        
        $response = wp_remote_request($api_url, array(
            'method' => 'PUT',
            'headers' => array(
                'Authorization' => 'token ' . $this->github_token,
                'Content-Type' => 'application/json',
                'User-Agent' => 'WPSeed-Sync'
            ),
            'body' => json_encode($body)
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $code = wp_remote_retrieve_response_code($response);
        
        if ($code === 200 || $code === 201) {
            set_transient('wpseed_github_hash_' . $file_name, md5_file($file_path), WEEK_IN_SECONDS);
            return true;
        }
        
        return new WP_Error('sync_failed', wp_remote_retrieve_body($response));
    }
    
    private function get_github_file_sha($file_name) {
        $api_url = "https://api.github.com/repos/{$this->github_repo}/contents/docs/{$file_name}";
        
        $response = wp_remote_get($api_url, array(
            'headers' => array(
                'Authorization' => 'token ' . $this->github_token,
                'User-Agent' => 'WPSeed-Sync'
            )
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        return isset($body['sha']) ? $body['sha'] : false;
    }
    
    public function sync_all() {
        $docs = $this->scan_local_docs();
        $results = array();
        
        foreach ($docs as $doc) {
            $result = $this->sync_to_github($doc['path'], $doc['name']);
            $results[$doc['name']] = !is_wp_error($result);
        }
        
        return $results;
    }
}

return new WPSeed_GitHub_Sync();
