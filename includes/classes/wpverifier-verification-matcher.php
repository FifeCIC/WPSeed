<?php
/**
 * Verification Matcher for WP Verifier
 * Checks verification status during scans
 * 
 * @package WPSeed/WPVerifier
 * @see /docs/WP-VERIFIER-ARCHITECTURE.md
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPVerifier_Verification_Matcher {
    
    private $json_storage;
    private $hash_generator;
    
    public function __construct($plugin_path) {
        $this->json_storage = new WPVerifier_JSON_Storage($plugin_path);
        $this->hash_generator = new WPVerifier_Hash_Generator();
    }
    
    /**
     * Process scan results and update verification data
     */
    public function process_scan_results($scan_results, $plugin_name) {
        // Initialize verification file if needed
        $this->json_storage->initialize_verification_file($plugin_name);
        
        // Extract file paths from scan results
        $scanned_files = array_keys($scan_results['results'] ?? []);
        
        // Generate hashes for scanned files
        $file_hashes = [];
        foreach ($scanned_files as $file_path) {
            if (file_exists($file_path)) {
                $hash = $this->hash_generator->generate_file_hash($file_path);
                $file_hashes[$file_path] = $hash;
                
                // Log hash generation
                error_log("WPVerifier Hash: File {$file_path} = {$hash}");
            }
        }
        
        // Update verification data with new hashes
        $this->json_storage->update_file_hashes($file_hashes);
        
        return $file_hashes;
    }
    
    /**
     * Get verification status for scan results
     */
    public function get_verification_status_for_results($scan_results) {
        $verification_status = [];
        
        foreach ($scan_results['results'] as $file_path => $issues) {
            $status = $this->json_storage->get_file_verification_status($file_path);
            
            $verification_status[$file_path] = [
                'is_verified' => isset($status['last_verified']),
                'verified_by' => $status['verified_by'] ?? null,
                'verified_at' => $status['last_verified'] ?? null,
                'has_changed' => $this->has_file_changed_since_verification($file_path),
                'verification_notes' => $status['verification_notes'] ?? ''
            ];
        }
        
        return $verification_status;
    }
    
    /**
     * Check if file has changed since last verification
     */
    private function has_file_changed_since_verification($file_path) {
        if (!file_exists($file_path)) {
            return true;
        }
        
        $current_hash = $this->hash_generator->generate_file_hash($file_path);
        return $this->json_storage->has_file_changed_since_verification($file_path, $current_hash);
    }
    
    /**
     * Mark file as verified
     */
    public function mark_file_verified($file_path, $notes = '') {
        $current_user_id = get_current_user_id();
        return $this->json_storage->mark_file_verified($file_path, $current_user_id, $notes);
    }
    
    /**
     * Get summary of verification status
     */
    public function get_verification_summary($scan_results) {
        $total_files = count($scan_results['results'] ?? []);
        $verified_files = 0;
        $changed_files = 0;
        
        foreach ($scan_results['results'] as $file_path => $issues) {
            $status = $this->json_storage->get_file_verification_status($file_path);
            
            if (isset($status['last_verified'])) {
                $verified_files++;
                
                if ($this->has_file_changed_since_verification($file_path)) {
                    $changed_files++;
                }
            }
        }
        
        return [
            'total_files' => $total_files,
            'verified_files' => $verified_files,
            'unverified_files' => $total_files - $verified_files,
            'changed_since_verification' => $changed_files,
            'verification_percentage' => $total_files > 0 ? round(($verified_files / $total_files) * 100, 1) : 0
        ];
    }
}