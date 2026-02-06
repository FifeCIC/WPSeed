<?php
/**
 * WPSeed Asset Manager
 *
 * @package WPSeed/Assets
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Asset_Manager {
    
    private $assets_dir;
    private $assets_url;
    private $assets = array();
    private $current_page;
    
    public function __construct() {
        $this->assets_dir = WPSEED_PLUGIN_DIR . 'assets/';
        $this->assets_url = WPSEED_PLUGIN_URL . 'assets/';
        $this->init_assets();
        $this->detect_current_page();
        
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'), 5);
    }
    
    private function init_assets() {
        $this->assets = array(
            'css' => require $this->assets_dir . 'css-registry.php',
            'js' => require $this->assets_dir . 'js-registry.php'
        );
    }
    
    private function detect_current_page() {
        $this->current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
        
        // Detect plugins.php page
        global $pagenow;
        if ($pagenow === 'plugins.php') {
            $this->current_page = 'plugins';
        }
    }
    
    public function enqueue_assets() {
        $this->enqueue_css();
        $this->enqueue_js();
    }
    
    private function enqueue_css() {
        foreach ($this->assets['css'] as $handle => $asset) {
            if ($this->should_load_asset($asset)) {
                $file_path = $this->assets_dir . $asset['path'];
                if (file_exists($file_path)) {
                    wp_enqueue_style(
                        'wpseed-' . $handle,
                        $this->assets_url . $asset['path'],
                        $asset['dependencies'],
                        WPSEED_VERSION
                    );
                }
            }
        }
    }
    
    private function enqueue_js() {
        foreach ($this->assets['js'] as $handle => $asset) {
            if ($this->should_load_asset($asset)) {
                $file_path = $this->assets_dir . $asset['path'];
                if (file_exists($file_path)) {
                    wp_enqueue_script(
                        'wpseed-' . $handle,
                        $this->assets_url . $asset['path'],
                        $asset['dependencies'],
                        WPSEED_VERSION,
                        true
                    );
                    
                    // Localize if needed
                    if ($handle === 'ecosystem-installer') {
                        wp_localize_script('wpseed-ecosystem-installer', 'wpseedEcosystem', array(
                            'nonce' => wp_create_nonce('wpseed_install_plugin')
                        ));
                    }
                }
            }
        }
    }
    
    private function should_load_asset($asset) {
        if (in_array('all', $asset['pages'])) {
            return true;
        }
        return in_array($this->current_page, $asset['pages']);
    }
    
    public function get_all_assets($type = null) {
        if ($type && isset($this->assets[$type])) {
            return $this->assets[$type];
        }
        return $this->assets;
    }
    
    public function get_asset_url($type, $name) {
        if (isset($this->assets[$type][$name])) {
            return $this->assets_url . $this->assets[$type][$name]['path'];
        }
        return false;
    }
    
    public function asset_exists($type, $name) {
        if (isset($this->assets[$type][$name])) {
            $file_path = $this->assets_dir . $this->assets[$type][$name]['path'];
            return file_exists($file_path);
        }
        return false;
    }
    
    public function get_missing_assets() {
        $missing = array();
        foreach ($this->assets as $type => $assets) {
            foreach ($assets as $name => $asset) {
                $file_path = $this->assets_dir . $asset['path'];
                if (!file_exists($file_path)) {
                    $missing[$type][$name] = $asset;
                }
            }
        }
        return $missing;
    }
}

// Initialize
global $wpseed_assets;
$wpseed_assets = new WPSeed_Asset_Manager();

function wpseed_get_asset_url($type, $name) {
    global $wpseed_assets;
    return $wpseed_assets->get_asset_url($type, $name);
}

function wpseed_asset_exists($type, $name) {
    global $wpseed_assets;
    return $wpseed_assets->asset_exists($type, $name);
}
