<?php
/**
 * Settings Backup/Restore
 * 
 * @package WPSeed
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Settings_Backup {
    
    public function __construct() {
        add_action('admin_post_wpseed_export_settings', array($this, 'export_settings'));
        add_action('admin_post_wpseed_import_settings', array($this, 'import_settings'));
        add_action('admin_post_wpseed_reset_settings', array($this, 'reset_settings'));
    }
    
    public function export_settings() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'wpseed'));
        }
        
        check_admin_referer('wpseed_export_settings');
        
        global $wpdb;
        $settings = $wpdb->get_results(
            $wpdb->prepare("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE %s", 'wpseed_%'),
            ARRAY_A
        );
        
        $export = array(
            'version' => WPSEED_VERSION,
            'date' => current_time('mysql'),
            'site_url' => get_site_url(),
            'settings' => $settings
        );
        
        $filename = 'wpseed-settings-' . date('Y-m-d-His') . '.json';
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        
        echo wp_json_encode($export, JSON_PRETTY_PRINT);
        exit;
    }
    
    public function import_settings() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'wpseed'));
        }
        
        check_admin_referer('wpseed_import_settings');
        
        if (empty($_FILES['import_file'])) {
            wp_die(__('No file uploaded', 'wpseed'));
        }
        
        $file = $_FILES['import_file'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            wp_die(__('Upload error', 'wpseed'));
        }
        
        $content = file_get_contents($file['tmp_name']);
        $data = json_decode($content, true);
        
        if (!$data || !isset($data['settings'])) {
            wp_die(__('Invalid file format', 'wpseed'));
        }
        
        $imported = 0;
        foreach ($data['settings'] as $setting) {
            update_option($setting['option_name'], maybe_unserialize($setting['option_value']));
            $imported++;
        }
        
        set_transient('wpseed_admin_notice', array(
            'type' => 'success',
            'message' => sprintf(__('%d settings imported', 'wpseed'), $imported)
        ), 30);
        
        wp_redirect(wp_get_referer());
        exit;
    }
    
    public function reset_settings() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'wpseed'));
        }
        
        check_admin_referer('wpseed_reset_settings');
        
        global $wpdb;
        $deleted = $wpdb->query(
            $wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", 'wpseed_%')
        );
        
        set_transient('wpseed_admin_notice', array(
            'type' => 'success',
            'message' => sprintf(__('%d settings reset to defaults', 'wpseed'), $deleted)
        ), 30);
        
        wp_redirect(wp_get_referer());
        exit;
    }
    
    public static function render_backup_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Settings Backup & Restore', 'wpseed'); ?></h1>
            
            <div class="card" style="max-width:800px;">
                <h2><?php _e('Export Settings', 'wpseed'); ?></h2>
                <p><?php _e('Download all WPSeed settings as JSON file', 'wpseed'); ?></p>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <?php wp_nonce_field('wpseed_export_settings'); ?>
                    <input type="hidden" name="action" value="wpseed_export_settings">
                    <button type="submit" class="button button-primary">
                        <span class="dashicons dashicons-download" style="margin-top:3px;"></span>
                        <?php _e('Export Settings', 'wpseed'); ?>
                    </button>
                </form>
            </div>
            
            <div class="card" style="max-width:800px;margin-top:20px;">
                <h2><?php _e('Import Settings', 'wpseed'); ?></h2>
                <p><?php _e('Upload a previously exported JSON file', 'wpseed'); ?></p>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
                    <?php wp_nonce_field('wpseed_import_settings'); ?>
                    <input type="hidden" name="action" value="wpseed_import_settings">
                    <input type="file" name="import_file" accept=".json" required>
                    <button type="submit" class="button button-secondary">
                        <span class="dashicons dashicons-upload" style="margin-top:3px;"></span>
                        <?php _e('Import Settings', 'wpseed'); ?>
                    </button>
                </form>
            </div>
            
            <div class="card" style="max-width:800px;margin-top:20px;border-left:4px solid #d63638;">
                <h2><?php _e('Reset to Defaults', 'wpseed'); ?></h2>
                <p><?php _e('Delete all WPSeed settings and restore defaults', 'wpseed'); ?></p>
                <p><strong><?php _e('Warning: This action cannot be undone!', 'wpseed'); ?></strong></p>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" onsubmit="return confirm('<?php esc_attr_e('Are you sure? All settings will be deleted.', 'wpseed'); ?>')">
                    <?php wp_nonce_field('wpseed_reset_settings'); ?>
                    <input type="hidden" name="action" value="wpseed_reset_settings">
                    <button type="submit" class="button button-secondary">
                        <span class="dashicons dashicons-trash" style="margin-top:3px;"></span>
                        <?php _e('Reset All Settings', 'wpseed'); ?>
                    </button>
                </form>
            </div>
        </div>
        <?php
    }
}

new WPSeed_Settings_Backup();
