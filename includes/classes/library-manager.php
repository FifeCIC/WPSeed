<?php
/**
 * WPSeed Library Manager
 * Tracks bundled libraries and checks for updates
 *
 * @package WPSeed
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Library_Manager {

    private static $instance = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_init', array($this, 'maybe_check_updates'));
    }

    /**
     * Get all bundled libraries
     */
    public static function get_libraries() {
        return array(
            'action-scheduler' => array(
                'name' => 'Action Scheduler',
                'version' => '3.8.1',
                'license' => 'GPL v3',
                'github_repo' => 'woocommerce/action-scheduler',
                'path' => 'includes/libraries/action-scheduler',
                'file' => 'action-scheduler.php',
                'bundled_date' => '2024-01-15',
            ),
            'carbon-fields' => array(
                'name' => 'Carbon Fields',
                'version' => '3.6.3',
                'license' => 'GPL v2',
                'github_repo' => 'htmlburger/carbon-fields',
                'path' => 'includes/libraries/carbon-fields',
                'file' => 'core/Carbon_Fields.php',
                'bundled_date' => '2024-01-15',
            ),
        );
    }

    /**
     * Check for library updates (once per day)
     */
    public function maybe_check_updates() {
        $last_check = get_transient('wpseed_library_check');
        
        if (false === $last_check) {
            $this->check_all_updates();
            set_transient('wpseed_library_check', time(), DAY_IN_SECONDS);
        }
    }

    /**
     * Check all libraries for updates
     */
    public function check_all_updates() {
        $libraries = self::get_libraries();
        
        foreach ($libraries as $id => $library) {
            $this->check_library_update($id, $library);
        }
    }

    /**
     * Check single library for updates
     */
    private function check_library_update($id, $library) {
        $latest = $this->get_latest_version($library['github_repo']);
        
        if ($latest && version_compare($latest, $library['version'], '>')) {
            update_option('wpseed_library_update_' . $id, array(
                'current' => $library['version'],
                'latest' => $latest,
                'checked' => current_time('mysql'),
            ));
        } else {
            delete_option('wpseed_library_update_' . $id);
        }
    }

    /**
     * Get latest version from GitHub
     */
    private function get_latest_version($repo) {
        $url = "https://api.github.com/repos/{$repo}/releases/latest";
        
        $response = wp_remote_get($url, array(
            'timeout' => 10,
            'headers' => array('User-Agent' => 'WPSeed-Library-Manager'),
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['tag_name'])) {
            return ltrim($body['tag_name'], 'v');
        }
        
        return false;
    }

    /**
     * Get library status
     */
    public static function get_library_status($id) {
        $libraries = self::get_libraries();
        
        if (!isset($libraries[$id])) {
            return array('status' => 'unknown');
        }
        
        $library = $libraries[$id];
        $update = get_option('wpseed_library_update_' . $id);
        
        // Check if library exists
        $path = WPSEED_PLUGIN_DIR_PATH . $library['path'] . '/' . $library['file'];
        $exists = file_exists($path);
        
        if (!$exists) {
            return array(
                'status' => 'missing',
                'message' => 'Library files not found',
            );
        }
        
        if ($update) {
            $days_old = floor((time() - strtotime($update['checked'])) / DAY_IN_SECONDS);
            
            if ($days_old > 180) {
                return array(
                    'status' => 'outdated',
                    'current' => $update['current'],
                    'latest' => $update['latest'],
                    'message' => 'Update available (6+ months old)',
                );
            } else {
                return array(
                    'status' => 'update_available',
                    'current' => $update['current'],
                    'latest' => $update['latest'],
                    'message' => 'Update available',
                );
            }
        }
        
        return array(
            'status' => 'up_to_date',
            'version' => $library['version'],
            'message' => 'Up to date',
        );
    }

    /**
     * Get library changelog
     */
    public static function get_changelog($repo, $from_version, $to_version) {
        $url = "https://api.github.com/repos/{$repo}/releases";
        
        $response = wp_remote_get($url, array(
            'timeout' => 10,
            'headers' => array('User-Agent' => 'WPSeed-Library-Manager'),
        ));
        
        if (is_wp_error($response)) {
            return array();
        }
        
        $releases = json_decode(wp_remote_retrieve_body($response), true);
        $changelog = array();
        
        foreach ($releases as $release) {
            $version = ltrim($release['tag_name'], 'v');
            
            if (version_compare($version, $from_version, '>') && 
                version_compare($version, $to_version, '<=')) {
                $changelog[] = array(
                    'version' => $version,
                    'date' => $release['published_at'],
                    'notes' => $release['body'],
                );
            }
        }
        
        return $changelog;
    }
}

// Initialize
WPSeed_Library_Manager::instance();
