<?php
/**
 * WPSeed Development Assets Tab (Asset Status Management)
 *
 * @package WPSeed\Admin\Development
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Admin_Development_Assets {

    public static function output() {
        $asset_manager = self::get_asset_manager();
        if (!$asset_manager) {
            echo '<div class="notice notice-error"><p>' . esc_html__('Asset manager not available.', 'wpseed') . '</p></div>';
            return;
        }

        $all_assets = $asset_manager->get_all_assets();
        $css_assets = $all_assets['css'] ?? array();
        $js_assets = $all_assets['js'] ?? array();
        $overall_status = self::check_overall_status($css_assets, $js_assets);
        ?>
        <div class="wpseed-assets-container">
            <div class="notice notice-<?php echo $overall_status['class'] === 'status-success' ? 'success' : ($overall_status['class'] === 'status-warning' ? 'warning' : 'error'); ?>">
                <p>
                    <span class="dashicons <?php echo esc_attr($overall_status['icon']); ?>"></span>
                    <strong><?php echo esc_html($overall_status['message']); ?></strong>
                    <?php if (!empty($overall_status['details'])): ?>
                        - <?php echo esc_html($overall_status['details']); ?>
                    <?php endif; ?>
                </p>
            </div>
            
            <h3><?php esc_html_e('CSS Assets', 'wpseed'); ?></h3>
            <?php $css_stats = self::get_asset_stats($css_assets); ?>
            <p><strong><?php echo esc_html($css_stats['total']); ?></strong> total | 
               <span style="color:green;"><?php echo esc_html($css_stats['found']); ?> available</span> | 
               <span style="color:red;"><?php echo esc_html($css_stats['missing']); ?> missing</span></p>
            <?php self::render_asset_table($css_assets, 'css'); ?>
            
            <h3><?php esc_html_e('JavaScript Assets', 'wpseed'); ?></h3>
            <?php $js_stats = self::get_asset_stats($js_assets); ?>
            <p><strong><?php echo esc_html($js_stats['total']); ?></strong> total | 
               <span style="color:green;"><?php echo esc_html($js_stats['found']); ?> available</span> | 
               <span style="color:red;"><?php echo esc_html($js_stats['missing']); ?> missing</span></p>
            <?php self::render_asset_table($js_assets, 'js'); ?>
        </div>
        <?php
    }

    private static function get_asset_manager() {
        global $wpseed_assets;
        return $wpseed_assets ?? false;
    }

    private static function render_asset_table($assets, $type) {
        if (empty($assets) || !is_array($assets)) {
            echo '<p>' . esc_html__('No assets found.', 'wpseed') . '</p>';
            return;
        }
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Name', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Category', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Purpose', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Status', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Path', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Pages', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Dependencies', 'wpseed'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($assets as $category => $category_assets):
                    if (empty($category_assets) || !is_array($category_assets)) continue;
                    
                    foreach ($category_assets as $name => $asset):
                        if (!is_array($asset)) continue;
                        $status = self::check_asset_status($asset, $type);
                        $asset_path = $asset['path'] ?? 'external';
                ?>
                        <tr>
                            <td><code><?php echo esc_html($name); ?></code></td>
                            <td><?php echo esc_html(ucwords(str_replace('_', ' ', $category))); ?></td>
                            <td><?php echo esc_html($asset['purpose'] ?? ''); ?></td>
                            <td>
                                <span class="dashicons <?php echo esc_attr($status['icon']); ?>" style="color:<?php echo $status['type'] === 'success' ? 'green' : 'red'; ?>;"></span>
                                <?php echo esc_html($status['message']); ?>
                            </td>
                            <td><small><?php echo esc_html($asset_path); ?></small></td>
                            <td>
                                <?php
                                $pages = $asset['pages'] ?? array();
                                if (is_array($pages)) {
                                    echo esc_html(implode(', ', array_slice($pages, 0, 2)));
                                    echo count($pages) > 2 ? '...' : '';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $deps = $asset['dependencies'] ?? array();
                                if (is_array($deps) && !empty($deps)) {
                                    echo esc_html(implode(', ', $deps));
                                }
                                ?>
                            </td>
                        </tr>
                <?php 
                    endforeach;
                endforeach; 
                ?>
            </tbody>
        </table>
        <?php
    }

    private static function check_asset_status($asset, $type) {
        if (!is_array($asset) || empty($asset['path'])) {
            return array('type' => 'error', 'icon' => 'dashicons-no', 'message' => __('Invalid', 'wpseed'));
        }
        
        if ($asset['path'] === 'external') {
            return array('type' => 'success', 'icon' => 'dashicons-yes', 'message' => __('External', 'wpseed'));
        }
        
        $asset_path = WPSEED_PLUGIN_DIR . 'assets/' . ltrim($asset['path'], '/');
        
        if (file_exists($asset_path)) {
            return array('type' => 'success', 'icon' => 'dashicons-yes', 'message' => __('Available', 'wpseed'));
        } else {
            return array('type' => 'error', 'icon' => 'dashicons-no', 'message' => __('Missing', 'wpseed'));
        }
    }

    private static function get_asset_stats($assets) {
        $total = 0;
        $found = 0;
        $missing = 0;
        
        if (!is_array($assets)) {
            return array('total' => 0, 'found' => 0, 'missing' => 0);
        }
        
        foreach ($assets as $category_assets) {
            if (!is_array($category_assets)) continue;
            
            foreach ($category_assets as $asset) {
                if (!is_array($asset)) continue;
                
                $total++;
                
                if (empty($asset['path']) || $asset['path'] === 'external') {
                    $found++;
                    continue;
                }
                
                $asset_path = WPSEED_PLUGIN_DIR . 'assets/' . ltrim($asset['path'], '/');
                
                if (file_exists($asset_path)) {
                    $found++;
                } else {
                    $missing++;
                }
            }
        }
        
        return array('total' => $total, 'found' => $found, 'missing' => $missing);
    }

    private static function check_overall_status($css_assets, $js_assets) {
        $css_stats = self::get_asset_stats($css_assets);
        $js_stats = self::get_asset_stats($js_assets);
        
        $total_missing = $css_stats['missing'] + $js_stats['missing'];
        $total_assets = $css_stats['total'] + $js_stats['total'];
        
        if ($total_missing === 0) {
            return array(
                'class' => 'status-success',
                'icon' => 'dashicons-yes',
                'message' => __('All Assets Available', 'wpseed'),
                /* translators: %d: Number of assets managed */
                'details' => sprintf(__('%1$d assets managed', 'wpseed'), $total_assets)
            );
        } elseif ($total_missing <= 3) {
            return array(
                'class' => 'status-warning',
                'icon' => 'dashicons-warning',
                'message' => __('Some Assets Missing', 'wpseed'),
                /* translators: %1$d: Number of missing assets, %2$d: Total number of assets */
                'details' => sprintf(__('%1$d of %2$d missing', 'wpseed'), $total_missing, $total_assets)
            );
        } else {
            return array(
                'class' => 'status-error',
                'icon' => 'dashicons-no',
                'message' => __('Many Assets Missing', 'wpseed'),
                /* translators: %1$d: Number of missing assets, %2$d: Total number of assets */
                'details' => sprintf(__('%1$d of %2$d missing', 'wpseed'), $total_missing, $total_assets)
            );
        }
    }
}

WPSeed_Admin_Development_Assets::output();
