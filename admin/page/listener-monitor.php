<?php
/**
 * Listener Monitor Admin Page
 * 
 * @package WPSeed/Admin
 */

if (!defined('ABSPATH')) exit;

function wpseed_render_listener_monitor_page() {
    global $wpdb;
    
    if (!current_user_can('manage_options')) {
        wp_die(__('Unauthorized', 'wpseed'));
    }
    
    // Handle clear logs action
    if (isset($_POST['clear_logs']) && check_admin_referer('clear_listener_logs')) {
        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wpseed_request_log");
        echo '<div class="notice notice-success"><p>' . __('Logs cleared', 'wpseed') . '</p></div>';
    }
    
    // Get filter values
    $filter_type = isset($_GET['filter_type']) ? sanitize_text_field($_GET['filter_type']) : '';
    $filter_status = isset($_GET['filter_status']) ? sanitize_text_field($_GET['filter_status']) : '';
    
    // Build query
    $where = array('1=1');
    if ($filter_type) {
        $where[] = $wpdb->prepare("request_type = %s", $filter_type);
    }
    if ($filter_status) {
        $where[] = $wpdb->prepare("status = %s", $filter_status);
    }
    
    $where_clause = implode(' AND ', $where);
    $requests = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpseed_request_log WHERE $where_clause ORDER BY created_at DESC LIMIT 100");
    
    // Get stats
    $total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wpseed_request_log");
    $processed = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wpseed_request_log WHERE status = 'processed'");
    $rejected = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wpseed_request_log WHERE status = 'rejected'");
    $skipped = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wpseed_request_log WHERE status = 'skipped'");
    ?>
    
    <div class="wrap">
        <h1><?php _e('Listener Monitor', 'wpseed'); ?></h1>
        
        <div class="wpseed-stats" style="display:flex;gap:20px;margin:20px 0;">
            <div style="background:#fff;padding:20px;border-left:4px solid #2271b1;flex:1;">
                <h3 style="margin:0 0 10px 0;"><?php echo esc_html($total); ?></h3>
                <p style="margin:0;color:#666;"><?php _e('Total Requests', 'wpseed'); ?></p>
            </div>
            <div style="background:#fff;padding:20px;border-left:4px solid #00a32a;flex:1;">
                <h3 style="margin:0 0 10px 0;"><?php echo esc_html($processed); ?></h3>
                <p style="margin:0;color:#666;"><?php _e('Processed', 'wpseed'); ?></p>
            </div>
            <div style="background:#fff;padding:20px;border-left:4px solid #d63638;flex:1;">
                <h3 style="margin:0 0 10px 0;"><?php echo esc_html($rejected); ?></h3>
                <p style="margin:0;color:#666;"><?php _e('Rejected', 'wpseed'); ?></p>
            </div>
            <div style="background:#fff;padding:20px;border-left:4px solid #dba617;flex:1;">
                <h3 style="margin:0 0 10px 0;"><?php echo esc_html($skipped); ?></h3>
                <p style="margin:0;color:#666;"><?php _e('Skipped', 'wpseed'); ?></p>
            </div>
        </div>
        
        <div style="background:#fff;padding:20px;margin-bottom:20px;">
            <form method="get" style="display:flex;gap:10px;align-items:end;">
                <input type="hidden" name="page" value="wpseed-listener-monitor">
                
                <div>
                    <label><?php _e('Type', 'wpseed'); ?></label><br>
                    <select name="filter_type">
                        <option value=""><?php _e('All', 'wpseed'); ?></option>
                        <option value="POST" <?php selected($filter_type, 'POST'); ?>>POST</option>
                        <option value="GET" <?php selected($filter_type, 'GET'); ?>>GET</option>
                        <option value="AJAX" <?php selected($filter_type, 'AJAX'); ?>>AJAX</option>
                    </select>
                </div>
                
                <div>
                    <label><?php _e('Status', 'wpseed'); ?></label><br>
                    <select name="filter_status">
                        <option value=""><?php _e('All', 'wpseed'); ?></option>
                        <option value="processed" <?php selected($filter_status, 'processed'); ?>><?php _e('Processed', 'wpseed'); ?></option>
                        <option value="rejected" <?php selected($filter_status, 'rejected'); ?>><?php _e('Rejected', 'wpseed'); ?></option>
                        <option value="skipped" <?php selected($filter_status, 'skipped'); ?>><?php _e('Skipped', 'wpseed'); ?></option>
                    </select>
                </div>
                
                <button type="submit" class="button"><?php _e('Filter', 'wpseed'); ?></button>
                <a href="<?php echo admin_url('admin.php?page=wpseed-listener-monitor'); ?>" class="button"><?php _e('Reset', 'wpseed'); ?></a>
            </form>
        </div>
        
        <form method="post" style="margin-bottom:20px;">
            <?php wp_nonce_field('clear_listener_logs'); ?>
            <button type="submit" name="clear_logs" class="button button-secondary" onclick="return confirm('<?php esc_attr_e('Clear all logs?', 'wpseed'); ?>')">
                <?php _e('Clear All Logs', 'wpseed'); ?>
            </button>
        </form>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Time', 'wpseed'); ?></th>
                    <th><?php _e('Type', 'wpseed'); ?></th>
                    <th><?php _e('URL', 'wpseed'); ?></th>
                    <th><?php _e('User', 'wpseed'); ?></th>
                    <th><?php _e('IP', 'wpseed'); ?></th>
                    <th><?php _e('Status', 'wpseed'); ?></th>
                    <th><?php _e('Reason', 'wpseed'); ?></th>
                    <th><?php _e('Actions', 'wpseed'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;">
                        <?php _e('No requests logged yet', 'wpseed'); ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($requests as $req): ?>
                <tr>
                    <td><?php echo esc_html(date('Y-m-d H:i:s', strtotime($req->created_at))); ?></td>
                    <td><span class="dashicons dashicons-<?php echo $req->request_type === 'AJAX' ? 'update' : 'admin-post'; ?>"></span> <?php echo esc_html($req->request_type); ?></td>
                    <td style="font-size:11px;"><?php echo esc_html(substr($req->url, 0, 60)); ?></td>
                    <td><?php 
                        if ($req->user_id) {
                            $user = get_userdata($req->user_id);
                            echo $user ? esc_html($user->user_login) : 'N/A';
                        } else {
                            echo 'Guest';
                        }
                    ?></td>
                    <td><?php echo esc_html($req->ip_address); ?></td>
                    <td>
                        <?php 
                        $status_colors = array(
                            'processed' => '#00a32a',
                            'rejected' => '#d63638',
                            'skipped' => '#dba617'
                        );
                        $color = isset($status_colors[$req->status]) ? $status_colors[$req->status] : '#666';
                        ?>
                        <span style="color:<?php echo $color; ?>;font-weight:600;">
                            <?php echo esc_html($req->status ?: 'N/A'); ?>
                        </span>
                    </td>
                    <td style="font-size:11px;"><?php echo esc_html($req->decision_reason ?: '-'); ?></td>
                    <td>
                        <button type="button" class="button button-small" onclick="viewDetails(<?php echo esc_attr($req->id); ?>)">
                            <?php _e('View', 'wpseed'); ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <p style="margin-top:20px;color:#666;">
            <?php printf(__('Showing last 100 requests. Total: %d', 'wpseed'), $total); ?>
        </p>
    </div>
    
    <div id="request-details-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);z-index:100000;">
        <div style="background:#fff;max-width:800px;margin:50px auto;padding:20px;max-height:80vh;overflow:auto;">
            <h2><?php _e('Request Details', 'wpseed'); ?></h2>
            <div id="request-details-content"></div>
            <button type="button" class="button" onclick="closeDetails()"><?php _e('Close', 'wpseed'); ?></button>
        </div>
    </div>
    
    <script>
    function viewDetails(id) {
        var requests = <?php echo wp_json_encode($requests); ?>;
        var req = requests.find(r => r.id == id);
        
        if (!req) return;
        
        var html = '<table class="widefat">';
        html += '<tr><th>ID</th><td>' + req.id + '</td></tr>';
        html += '<tr><th>Time</th><td>' + req.created_at + '</td></tr>';
        html += '<tr><th>Type</th><td>' + req.request_type + '</td></tr>';
        html += '<tr><th>URL</th><td>' + req.url + '</td></tr>';
        html += '<tr><th>Status</th><td>' + (req.status || 'N/A') + '</td></tr>';
        html += '<tr><th>Reason</th><td>' + (req.decision_reason || '-') + '</td></tr>';
        
        if (req.post_data) {
            html += '<tr><th>POST Data</th><td><pre style="background:#f0f0f1;padding:10px;overflow:auto;">' + req.post_data + '</pre></td></tr>';
        }
        
        if (req.get_data) {
            html += '<tr><th>GET Data</th><td><pre style="background:#f0f0f1;padding:10px;overflow:auto;">' + req.get_data + '</pre></td></tr>';
        }
        
        html += '</table>';
        
        document.getElementById('request-details-content').innerHTML = html;
        document.getElementById('request-details-modal').style.display = 'block';
    }
    
    function closeDetails() {
        document.getElementById('request-details-modal').style.display = 'none';
    }
    </script>
    
    <?php
}
