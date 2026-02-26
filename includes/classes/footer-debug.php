<?php
/**
 * Footer Debug Area
 * Displays debug information in admin footer when enabled
 *
 * @package WPSeed/Developer
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Footer_Debug {
    
    public function __construct() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        add_action('admin_footer', array($this, 'render_debug_area'));
        add_action('admin_post_wpseed_footer_debug_toggle', array($this, 'handle_toggle'));
    }
    
    public static function is_enabled() {
        return get_option('wpseed_footer_debug', false);
    }
    
    public function handle_toggle() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'wpseed'));
        }
        
        $current = self::is_enabled();
        update_option('wpseed_footer_debug', !$current);
        
        wp_redirect(wp_get_referer());
        exit;
    }
    
    public function render_debug_area() {
        if (!self::is_enabled()) {
            return;
        }
        
        $recent_requests = WPSeed_Listener::get_recent_requests(10);
        ?>
        <div id="wpseed-footer-debug" style="background:#f0f0f1;border-top:3px solid #2271b1;padding:20px;margin-top:20px;">
            <h3 style="margin-top:0;">WPSeed Debug Area</h3>
            
            <?php if (!empty($_POST)): ?>
            <div style="margin-bottom:15px;">
                <strong>$_POST:</strong>
                <pre style="background:#fff;padding:10px;overflow:auto;max-height:200px;"><?php print_r($_POST); ?></pre>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($_GET)): ?>
            <div style="margin-bottom:15px;">
                <strong>$_GET:</strong>
                <pre style="background:#fff;padding:10px;overflow:auto;max-height:200px;"><?php print_r($_GET); ?></pre>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($recent_requests)): ?>
            <div style="margin-bottom:15px;">
                <strong>Recent Requests (Last 10):</strong>
                <table style="width:100%;background:#fff;border-collapse:collapse;margin-top:10px;">
                    <thead>
                        <tr style="background:#2271b1;color:#fff;">
                            <th style="padding:8px;text-align:left;">Time</th>
                            <th style="padding:8px;text-align:left;">Type</th>
                            <th style="padding:8px;text-align:left;">URL</th>
                            <th style="padding:8px;text-align:left;">Status</th>
                            <th style="padding:8px;text-align:left;">Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_requests as $req): ?>
                        <tr style="border-bottom:1px solid #ddd;">
                            <td style="padding:8px;"><?php echo esc_html(date('H:i:s', strtotime($req->created_at))); ?></td>
                            <td style="padding:8px;"><?php echo esc_html($req->request_type); ?></td>
                            <td style="padding:8px;font-size:11px;"><?php echo esc_html(substr($req->url, 0, 50)); ?></td>
                            <td style="padding:8px;"><?php echo esc_html($req->status ?: 'N/A'); ?></td>
                            <td style="padding:8px;font-size:11px;"><?php echo esc_html($req->decision_reason ?: '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
}

add_action('init', function() {
    new WPSeed_Footer_Debug();
});
