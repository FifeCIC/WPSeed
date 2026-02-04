<?php
/**
 * WPSeed Asset Queue System
 * 
 * Centralized asset enqueueing based on page detection
 * 
 * @package WPSeed/Assets
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Asset_Queue {
    
    private $asset_manager;
    private $current_page;
    private $current_tab;
    
    public function __construct() {
        if (!class_exists('WPSeed_Asset_Manager')) {
            require_once WPSEED_PLUGIN_DIR_PATH . 'assets/manage-assets.php';
        }
        
        global $wpseed_assets;
        $this->asset_manager = $wpseed_assets;
        
        $this->detect_current_context();
        
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'), 5);
        add_action('admin_notices', array($this, 'missing_assets_notice'));
    }
    
    private function detect_current_context() {
        $this->current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
        $this->current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : '';
    }
    
    public function enqueue_assets() {
        if (!is_admin()) {
            return;
        }
        
        $page_id = $this->current_page;
        if (empty($page_id)) {
            return;
        }
        
        $page_assets = $this->asset_manager->get_assets_by_page($page_id);
        
        if (isset($page_assets['css'])) {
            foreach ($page_assets['css'] as $name => $asset) {
                $this->enqueue_style($name, $asset);
            }
        }
        
        if (isset($page_assets['js'])) {
            foreach ($page_assets['js'] as $name => $asset) {
                $this->enqueue_script($name, $asset);
            }
        }
    }
    
    private function enqueue_style($name, $asset) {
        $url = $this->asset_manager->get_asset_url('css', $name);
        
        if ($url === false) {
            return;
        }
        
        wp_enqueue_style(
            'wpseed-' . $name,
            $url,
            $asset['dependencies'],
            WPSEED_VERSION,
            'all'
        );
    }
    
    private function enqueue_script($name, $asset) {
        $url = $this->asset_manager->get_asset_url('js', $name);
        
        if ($url === false) {
            return;
        }
        
        wp_enqueue_script(
            'wpseed-' . $name,
            $url,
            $asset['dependencies'],
            WPSEED_VERSION,
            true
        );
    }
    
    public function missing_assets_notice() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (!$this->asset_manager->has_missing_assets()) {
            return;
        }
        
        $missing = $this->asset_manager->get_missing_assets();
        $count = count($missing);
        
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong>WPSeed:</strong> 
                <?php echo sprintf(_n('%d asset file is missing', '%d asset files are missing', $count, 'wpseed'), $count); ?>
            </p>
            <ul style="list-style: disc; margin-left: 20px;">
                <?php foreach ($missing as $asset): ?>
                    <li>
                        <code><?php echo esc_html($asset['name']); ?></code> 
                        (<?php echo esc_html($asset['type']); ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
            <p>
                <em>Check the error log for full paths.</em>
            </p>
        </div>
        <?php
    }
}

if (!class_exists('WPSeed_Asset_Queue_Instance')) {
    class WPSeed_Asset_Queue_Instance {
        private static $instance = null;
        
        public static function get_instance() {
            if (self::$instance === null) {
                self::$instance = new WPSeed_Asset_Queue();
            }
            return self::$instance;
        }
    }
    
    WPSeed_Asset_Queue_Instance::get_instance();
}
