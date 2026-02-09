<?php
/**
 * WPSeed Development - Libraries Monitor
 *
 * @package WPSeed/Admin/Development
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Admin_Development_Libraries {
    
    public static function output() {
        $libraries = WPSeed_Library_Manager::get_libraries();
        
        // Handle manual check
        if (isset($_POST['check_updates']) && wp_verify_nonce($_POST['_wpnonce'], 'wpseed_check_updates')) {
            WPSeed_Library_Manager::instance()->check_all_updates();
            delete_transient('wpseed_library_check');
            echo '<div class="notice notice-success"><p>' . __('Library updates checked successfully.', 'wpseed') . '</p></div>';
        }
        
        ?>
        <div class="wpseed-libraries-monitor">
            
            <div class="libraries-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h3 style="margin: 0;"><?php _e('Bundled Libraries', 'wpseed'); ?></h3>
                    <p style="margin: 5px 0 0 0; color: #666;">
                        <?php 
                        $last_check = get_transient('wpseed_library_check');
                        if ($last_check) {
                            printf(__('Last checked: %s ago', 'wpseed'), human_time_diff($last_check));
                        } else {
                            _e('Never checked', 'wpseed');
                        }
                        ?>
                    </p>
                </div>
                <form method="post">
                    <?php wp_nonce_field('wpseed_check_updates'); ?>
                    <button type="submit" name="check_updates" class="button button-primary">
                        <?php _e('Check for Updates', 'wpseed'); ?>
                    </button>
                </form>
            </div>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Library', 'wpseed'); ?></th>
                        <th><?php _e('Current Version', 'wpseed'); ?></th>
                        <th><?php _e('Latest Version', 'wpseed'); ?></th>
                        <th><?php _e('License', 'wpseed'); ?></th>
                        <th><?php _e('Status', 'wpseed'); ?></th>
                        <th><?php _e('Bundled', 'wpseed'); ?></th>
                        <th><?php _e('Actions', 'wpseed'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($libraries as $id => $library): 
                        $status = WPSeed_Library_Manager::get_library_status($id);
                    ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html($library['name']); ?></strong>
                            </td>
                            <td>
                                <code><?php echo esc_html($library['version']); ?></code>
                            </td>
                            <td>
                                <?php if (isset($status['latest'])): ?>
                                    <code><?php echo esc_html($status['latest']); ?></code>
                                <?php else: ?>
                                    <span style="color: #666;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="license-badge"><?php echo esc_html($library['license']); ?></span>
                            </td>
                            <td>
                                <?php echo self::get_status_badge($status['status']); ?>
                            </td>
                            <td>
                                <?php echo esc_html(human_time_diff(strtotime($library['bundled_date']), current_time('timestamp')) . ' ago'); ?>
                            </td>
                            <td>
                                <a href="https://github.com/<?php echo esc_attr($library['github_repo']); ?>/releases" 
                                   class="button button-small" target="_blank">
                                    <?php _e('View Releases', 'wpseed'); ?>
                                </a>
                                <?php if ($status['status'] === 'update_available' || $status['status'] === 'outdated'): ?>
                                    <a href="https://github.com/<?php echo esc_attr($library['github_repo']); ?>/releases/latest" 
                                       class="button button-small button-primary" target="_blank">
                                        <?php _e('Download Update', 'wpseed'); ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <?php if ($status['status'] === 'update_available' || $status['status'] === 'outdated'): ?>
                            <tr class="update-notice-row">
                                <td colspan="7" style="background: #f0f8ff; border-left: 4px solid #0073aa; padding: 12px;">
                                    <strong><?php _e('Update Available:', 'wpseed'); ?></strong>
                                    <?php printf(__('Version %s → %s', 'wpseed'), $status['current'], $status['latest']); ?>
                                    <br>
                                    <small style="color: #666;">
                                        <?php _e('Download the latest version, extract to the library folder, and replace existing files.', 'wpseed'); ?>
                                    </small>
                                </td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if ($status['status'] === 'missing'): ?>
                            <tr class="error-notice-row">
                                <td colspan="7" style="background: #ffeaea; border-left: 4px solid #dc3232; padding: 12px;">
                                    <strong><?php _e('Library Missing:', 'wpseed'); ?></strong>
                                    <?php echo esc_html($status['message']); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="libraries-info" style="margin-top: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                <h4 style="margin-top: 0;"><?php _e('About Library Updates', 'wpseed'); ?></h4>
                <ul style="margin: 0;">
                    <li><?php _e('WPSeed checks for library updates once per day automatically', 'wpseed'); ?></li>
                    <li><?php _e('Updates are not installed automatically - you must download and replace files manually', 'wpseed'); ?></li>
                    <li><?php _e('Always backup your site before updating libraries', 'wpseed'); ?></li>
                    <li><?php _e('Test updates in a development environment first', 'wpseed'); ?></li>
                </ul>
            </div>
            
            <div class="libraries-cli" style="margin-top: 20px; padding: 15px; background: #f0f0f1; border-radius: 4px;">
                <h4 style="margin-top: 0;"><?php _e('WP-CLI Commands', 'wpseed'); ?></h4>
                <code style="display: block; padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 3px;">
                    wp wpseed libraries check
                </code>
                <p style="margin: 10px 0 0 0; color: #666; font-size: 13px;">
                    <?php _e('Check for library updates via command line', 'wpseed'); ?>
                </p>
            </div>
        </div>
        <?php
    }
    
    private static function get_status_badge($status) {
        $badges = array(
            'up_to_date' => '<span style="display: inline-block; padding: 3px 8px; background: #00a32a; color: #fff; border-radius: 3px; font-size: 11px; font-weight: 600;">✓ Up to Date</span>',
            'update_available' => '<span style="display: inline-block; padding: 3px 8px; background: #f0b849; color: #fff; border-radius: 3px; font-size: 11px; font-weight: 600;">⚠ Update Available</span>',
            'outdated' => '<span style="display: inline-block; padding: 3px 8px; background: #d63638; color: #fff; border-radius: 3px; font-size: 11px; font-weight: 600;">⚠ Outdated</span>',
            'missing' => '<span style="display: inline-block; padding: 3px 8px; background: #dc3232; color: #fff; border-radius: 3px; font-size: 11px; font-weight: 600;">✗ Missing</span>',
            'unknown' => '<span style="display: inline-block; padding: 3px 8px; background: #ddd; color: #666; border-radius: 3px; font-size: 11px; font-weight: 600;">? Unknown</span>',
        );
        
        return isset($badges[$status]) ? $badges[$status] : $badges['unknown'];
    }
}
