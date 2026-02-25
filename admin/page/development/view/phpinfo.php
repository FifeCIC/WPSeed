<?php
/**
 * WPSeed Development - PHP Info Tab
 *
 * @package WPSeed/Admin/Views
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Admin_Development_PHPInfo {
    public static function output() {
        ?>
        <div class="wpseed-dev-section">
            <h2><?php esc_html_e('PHP Configuration', 'wpseed'); ?></h2>
            
            <table class="widefat">
                <tbody>
                    <tr>
                        <th><?php esc_html_e('PHP Version', 'wpseed'); ?></th>
                        <td><?php echo esc_html(PHP_VERSION); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Memory Limit', 'wpseed'); ?></th>
                        <td><?php echo esc_html(ini_get('memory_limit')); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Max Execution Time', 'wpseed'); ?></th>
                        <td><?php echo esc_html(ini_get('max_execution_time')); ?> seconds</td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Max Upload Size', 'wpseed'); ?></th>
                        <td><?php echo esc_html(ini_get('upload_max_filesize')); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Max Post Size', 'wpseed'); ?></th>
                        <td><?php echo esc_html(ini_get('post_max_size')); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Display Errors', 'wpseed'); ?></th>
                        <td><?php echo ini_get('display_errors') ? __('On', 'wpseed') : __('Off', 'wpseed'); ?></td>
                    </tr>
                </tbody>
            </table>

            <h3><?php esc_html_e('Loaded Extensions', 'wpseed'); ?></h3>
            <div style="columns: 3; column-gap: 20px;">
                <?php
                $extensions = get_loaded_extensions();
                sort($extensions);
                foreach ($extensions as $ext) {
                    echo '<div style="break-inside: avoid; padding: 2px 0;"><code>' . esc_html($ext) . '</code></div>';
                }
                ?>
            </div>
        </div>
        <?php
    }
}
