<?php
/**
 * WPSeed Development - Theme Info Tab
 *
 * @package WPSeed/Admin/Views
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Admin_Development_Theme_Info {
    public static function output() {
        $theme = wp_get_theme();
        ?>
        <div class="wpseed-dev-section">
            <h2><?php _e('Active Theme Information', 'wpseed'); ?></h2>
            <table class="widefat">
                <tbody>
                    <tr>
                        <th><?php _e('Theme Name', 'wpseed'); ?></th>
                        <td><?php echo esc_html($theme->get('Name')); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Theme Version', 'wpseed'); ?></th>
                        <td><?php echo esc_html($theme->get('Version')); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Author', 'wpseed'); ?></th>
                        <td><?php echo esc_html($theme->get('Author')); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Theme Directory', 'wpseed'); ?></th>
                        <td><code><?php echo esc_html($theme->get_stylesheet_directory()); ?></code></td>
                    </tr>
                    <tr>
                        <th><?php _e('Template Directory', 'wpseed'); ?></th>
                        <td><code><?php echo esc_html($theme->get_template_directory()); ?></code></td>
                    </tr>
                </tbody>
            </table>

            <h3><?php _e('WordPress Environment', 'wpseed'); ?></h3>
            <table class="widefat">
                <tbody>
                    <tr>
                        <th><?php _e('WordPress Version', 'wpseed'); ?></th>
                        <td><?php echo esc_html(get_bloginfo('version')); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Site URL', 'wpseed'); ?></th>
                        <td><?php echo esc_html(get_site_url()); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Home URL', 'wpseed'); ?></th>
                        <td><?php echo esc_html(get_home_url()); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Multisite', 'wpseed'); ?></th>
                        <td><?php echo is_multisite() ? __('Yes', 'wpseed') : __('No', 'wpseed'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
}
