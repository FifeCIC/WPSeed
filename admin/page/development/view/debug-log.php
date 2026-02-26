<?php
/**
 * WPSeed Development - Debug Log Tab
 *
 * @package WPSeed/Admin/Views
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Admin_Development_Debug_Log {
    public static function output() {
        $debug_file = WP_CONTENT_DIR . '/debug.log';
        $log_exists = file_exists($debug_file);
        ?>
        <div class="wpseed-dev-section">
            <h2><?php esc_html_e('WordPress Debug Log', 'wpseed'); ?></h2>
            
            <?php if (!$log_exists): ?>
                <div class="notice notice-info">
                    <p><?php esc_html_e('Debug log file does not exist. Enable WP_DEBUG_LOG in wp-config.php to create it.', 'wpseed'); ?></p>
                </div>
            <?php else: ?>
                <p>
                    <strong><?php esc_html_e('Log File:', 'wpseed'); ?></strong> 
                    <code><?php echo esc_html($debug_file); ?></code>
                </p>
                <p>
                    <strong><?php esc_html_e('File Size:', 'wpseed'); ?></strong> 
                        <?php echo esc_html(size_format(filesize($debug_file))); ?>
                </p>
                
                <div style="margin: 20px 0;">
                    <a href="<?php echo esc_url(wp_nonce_url(add_query_arg('wpseed_clear_log', '1'), 'wpseed_clear_log_action')); ?>" 
                       class="button button-secondary"
                       onclick="return confirm('<?php esc_attr_e('Are you sure you want to clear the debug log?', 'wpseed'); ?>');">
                        <?php esc_html_e('Clear Log', 'wpseed'); ?>
                    </a>
                </div>

                <?php
                if (isset($_GET['wpseed_clear_log']) && check_admin_referer('wpseed_clear_log_action')) {
                    file_put_contents($debug_file, '');
                    echo '<div class="notice notice-success"><p>' . esc_html__('Debug log cleared.', 'wpseed') . '</p></div>';
                }
                
                $log_content = file_get_contents($debug_file);
                $lines = explode("\n", $log_content);
                $last_lines = array_slice($lines, -100);
                ?>
                
                <h3><?php esc_html_e('Last 100 Lines', 'wpseed'); ?></h3>
                <textarea readonly style="width: 100%; height: 400px; font-family: monospace; font-size: 12px;"><?php 
                    echo esc_textarea(implode("\n", $last_lines)); 
                ?></textarea>
            <?php endif; ?>
        </div>
        <?php
    }
}
