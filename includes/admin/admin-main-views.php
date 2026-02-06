<?php
/**
 * WPSeed Admin Main Views
 *
 * @package WPSeed/Admin
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Admin_Main_Views {
    
    public static function output() {
        ?>
        <div class="wrap">
            <h1><?php _e('WPSeed Plugin', 'wpseed'); ?></h1>
            
            <div class="wpseed-main-dashboard">
                <p><?php _e('Welcome to WPSeed - The AI-Powered WordPress Plugin Boilerplate', 'wpseed'); ?></p>
                
                <div class="wpseed-quick-links" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 30px;">
                    <div class="wpseed-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
                        <h2><?php _e('Development Tools', 'wpseed'); ?></h2>
                        <p><?php _e('Access 10-tab developer dashboard with assets, debugging, and architecture tools.', 'wpseed'); ?></p>
                        <a href="<?php echo admin_url('admin.php?page=wpseed-development'); ?>" class="button button-primary"><?php _e('Open Development', 'wpseed'); ?></a>
                    </div>
                    
                    <div class="wpseed-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
                        <h2><?php _e('Settings', 'wpseed'); ?></h2>
                        <p><?php _e('Configure plugin settings, API keys, and preferences.', 'wpseed'); ?></p>
                        <a href="<?php echo admin_url('options-general.php?page=wpseed-settings'); ?>" class="button button-primary"><?php _e('Open Settings', 'wpseed'); ?></a>
                    </div>
                    
                    <div class="wpseed-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
                        <h2><?php _e('Documentation', 'wpseed'); ?></h2>
                        <p><?php _e('Read guides, API reference, and integration examples.', 'wpseed'); ?></p>
                        <a href="https://github.com/ryanbayne/wpseed" target="_blank" class="button"><?php _e('View Docs', 'wpseed'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
