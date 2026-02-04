<?php
/**
 * Assets Tracker View
 *
 * @package WPSeed/Admin/Views
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Admin_Development_Assets {
    
    public static function output() {
        global $wpseed_assets;
        
        if (!$wpseed_assets) {
            echo '<p>' . __('Asset manager not initialized.', 'wpseed') . '</p>';
            return;
        }
        
        $all_assets = $wpseed_assets->get_all_assets();
        $css_assets = $all_assets['css'] ?? array();
        $js_assets = $all_assets['js'] ?? array();
        
        ?>
        <div class="wpseed-assets-tracker">
            <h3><?php _e('Asset Overview', 'wpseed'); ?></h3>
            
            <div class="assets-summary">
                <?php self::render_summary($css_assets, $js_assets); ?>
            </div>
            
            <h4><?php _e('CSS Assets', 'wpseed'); ?></h4>
            <?php self::render_asset_table($css_assets, 'css'); ?>
            
            <h4><?php _e('JavaScript Assets', 'wpseed'); ?></h4>
            <?php self::render_asset_table($js_assets, 'js'); ?>
        </div>
        
        <style>
        .wpseed-assets-tracker {
            background: #fff;
            padding: 20px;
            margin-top: 20px;
        }
        
        .assets-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: #f0f0f1;
            padding: 15px;
            border-radius: 4px;
        }
        
        .summary-card h4 {
            margin-top: 0;
        }
        
        .status-success { color: #46b450; }
        .status-error { color: #dc3232; }
        </style>
        <?php
    }
    
    private static function render_summary($css_assets, $js_assets) {
        $css_stats = self::get_asset_stats($css_assets);
        $js_stats = self::get_asset_stats($js_assets);
        $total = $css_stats['total'] + $js_stats['total'];
        $found = $css_stats['found'] + $js_stats['found'];
        $missing = $css_stats['missing'] + $js_stats['missing'];
        
        ?>
        <div class="summary-card">
            <h4><?php _e('CSS Assets', 'wpseed'); ?></h4>
            <p><strong><?php echo esc_html($css_stats['total']); ?></strong> total</p>
            <p class="status-success"><?php echo esc_html($css_stats['found']); ?> found</p>
            <p class="status-error"><?php echo esc_html($css_stats['missing']); ?> missing</p>
        </div>
        
        <div class="summary-card">
            <h4><?php _e('JS Assets', 'wpseed'); ?></h4>
            <p><strong><?php echo esc_html($js_stats['total']); ?></strong> total</p>
            <p class="status-success"><?php echo esc_html($js_stats['found']); ?> found</p>
            <p class="status-error"><?php echo esc_html($js_stats['missing']); ?> missing</p>
        </div>
        
        <div class="summary-card">
            <h4><?php _e('Overall', 'wpseed'); ?></h4>
            <p><strong><?php echo esc_html($total); ?></strong> total assets</p>
            <p class="status-success"><?php echo esc_html($found); ?> available</p>
            <?php if ($missing > 0): ?>
                <p class="status-error"><?php echo esc_html($missing); ?> need attention</p>
            <?php endif; ?>
        </div>
        <?php
    }
    
    private static function render_asset_table($assets, $type) {
        if (empty($assets)) {
            echo '<p>' . __('No assets found.', 'wpseed') . '</p>';
            return;
        }
        
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Name', 'wpseed'); ?></th>
                    <th><?php _e('Category', 'wpseed'); ?></th>
                    <th><?php _e('Path', 'wpseed'); ?></th>
                    <th><?php _e('Status', 'wpseed'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assets as $category => $category_assets): ?>
                    <?php foreach ($category_assets as $name => $asset): ?>
                        <?php
                        $path = WPSEED_PLUGIN_DIR_PATH . 'assets/' . ltrim($asset['path'], '/');
                        $exists = file_exists($path);
                        ?>
                        <tr>
                            <td><code><?php echo esc_html($name); ?></code></td>
                            <td><?php echo esc_html(ucwords(str_replace('_', ' ', $category))); ?></td>
                            <td><?php echo esc_html($asset['path']); ?></td>
                            <td>
                                <?php if ($exists): ?>
                                    <span class="status-success dashicons dashicons-yes"></span> Available
                                <?php else: ?>
                                    <span class="status-error dashicons dashicons-no"></span> Missing
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    private static function get_asset_stats($assets) {
        $total = 0;
        $found = 0;
        $missing = 0;
        
        foreach ($assets as $category_assets) {
            foreach ($category_assets as $asset) {
                $total++;
                $path = WPSEED_PLUGIN_DIR_PATH . 'assets/' . ltrim($asset['path'], '/');
                if (file_exists($path)) {
                    $found++;
                } else {
                    $missing++;
                }
            }
        }
        
        return array('total' => $total, 'found' => $found, 'missing' => $missing);
    }
}
