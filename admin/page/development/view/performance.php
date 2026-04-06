<?php
/**
 * WPSeed Development - Performance Monitor
 *
 * @package WPSeed/Admin/Development
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Admin_Development_Performance {
    
    public static function output() {
        // Handle clear logs
        if (isset($_POST['clear_logs']) && isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'wpseed_clear_logs')) {
            WPSeed_Enhanced_Logger::clear_old_logs(0);
            echo '<div class="notice notice-success"><p>' . esc_html__('Logs cleared successfully.', 'wpseed') . '</p></div>';
        }
        
        $logs = WPSeed_Enhanced_Logger::get_recent_logs(50);
        $current_metrics = WPSeed_Enhanced_Logger::instance()->get_performance_metrics();
        
        ?>
        <div class="wpseed-performance-monitor">
            
            <!-- Current Request Metrics -->
            <div class="performance-cards" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
                
                <div class="metric-card" style="background: #fff; padding: 20px; border-left: 4px solid #2271b1; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="font-size: 32px; font-weight: bold; color: #2271b1;">
                        <?php echo number_format($current_metrics['execution_time'], 3); ?>s
                    </div>
                    <div style="color: #646970; margin-top: 5px;"><?php esc_html_e('Execution Time', 'wpseed'); ?></div>
                </div>
                
                <div class="metric-card" style="background: #fff; padding: 20px; border-left: 4px solid #00a32a; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="font-size: 32px; font-weight: bold; color: #00a32a;">
                        <?php echo number_format($current_metrics['memory_usage'] / 1024 / 1024, 2); ?>MB
                    </div>
                    <div style="color: #646970; margin-top: 5px;"><?php esc_html_e('Memory Used', 'wpseed'); ?></div>
                </div>
                
                <div class="metric-card" style="background: #fff; padding: 20px; border-left: 4px solid #f0b849; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="font-size: 32px; font-weight: bold; color: #f0b849;">
                        <?php echo number_format($current_metrics['queries']); ?>
                    </div>
                    <div style="color: #646970; margin-top: 5px;"><?php esc_html_e('DB Queries', 'wpseed'); ?></div>
                </div>
                
                <div class="metric-card" style="background: #fff; padding: 20px; border-left: 4px solid #d63638; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="font-size: 32px; font-weight: bold; color: #d63638;">
                        <?php echo number_format($current_metrics['errors']); ?>
                    </div>
                    <div style="color: #646970; margin-top: 5px;"><?php esc_html_e('Errors', 'wpseed'); ?></div>
                </div>
                
            </div>
            
            <!-- Query Statistics -->
            <?php 
            $query_stats = WPSeed_Enhanced_Logger::instance()->get_query_stats();
            if (!empty($query_stats['slow_queries'])):
            ?>
            <div class="slow-queries-section" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; margin-bottom: 20px;">
                <h3><?php esc_html_e('Slow Queries (>50ms)', 'wpseed'); ?></h3>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Query', 'wpseed'); ?></th>
                            <th style="width: 100px;"><?php esc_html_e('Time', 'wpseed'); ?></th>
                            <th style="width: 200px;"><?php esc_html_e('Called From', 'wpseed'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($query_stats['slow_queries'], 0, 10) as $query): ?>
                            <tr>
                                <td><code style="font-size: 11px;"><?php echo esc_html(substr($query['query'], 0, 100)); ?>...</code></td>
                                <td><strong style="color: #d63638;"><?php echo number_format($query['time'], 4); ?>s</strong></td>
                                <td><small><?php echo esc_html($query['backtrace']); ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <!-- Hook Statistics -->
            <?php $hook_stats = WPSeed_Enhanced_Logger::instance()->get_hook_stats(); ?>
            <div class="hook-stats-section" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; margin-bottom: 20px;">
                <h3><?php esc_html_e('Most Called Hooks', 'wpseed'); ?></h3>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Hook Name', 'wpseed'); ?></th>
                            <th style="width: 100px;"><?php esc_html_e('Calls', 'wpseed'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hook_stats['most_called'] as $hook => $count): ?>
                            <tr>
                                <td><code><?php echo esc_html($hook); ?></code></td>
                                <td><strong><?php echo number_format($count); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Recent Requests Log -->
            <div class="recent-logs-section" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0;"><?php esc_html_e('Recent Requests', 'wpseed'); ?></h3>
                    <form method="post">
                        <?php wp_nonce_field('wpseed_clear_logs'); ?>
                        <button type="submit" name="clear_logs" class="button">
                            <?php esc_html_e('Clear Logs', 'wpseed'); ?>
                        </button>
                    </form>
                </div>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Time', 'wpseed'); ?></th>
                            <th><?php esc_html_e('Request URI', 'wpseed'); ?></th>
                            <th style="width: 80px;"><?php esc_html_e('Queries', 'wpseed'); ?></th>
                            <th style="width: 100px;"><?php esc_html_e('Query Time', 'wpseed'); ?></th>
                            <th style="width: 100px;"><?php esc_html_e('Exec Time', 'wpseed'); ?></th>
                            <th style="width: 80px;"><?php esc_html_e('Memory', 'wpseed'); ?></th>
                            <th style="width: 60px;"><?php esc_html_e('Errors', 'wpseed'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #666;">
                                    <?php esc_html_e('No logs available. Logs are only collected in developer mode.', 'wpseed'); ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?php echo esc_html(human_time_diff(strtotime($log->created_at), current_time('timestamp')) . ' ago'); ?></td>
                                    <td><code style="font-size: 11px;"><?php echo esc_html($log->request_uri); ?></code></td>
                                    <td><?php echo number_format($log->query_count); ?></td>
                                    <td><?php echo number_format($log->query_time, 4); ?>s</td>
                                    <td><?php echo number_format($log->execution_time, 4); ?>s</td>
                                    <td><?php echo number_format($log->memory_usage / 1024 / 1024, 2); ?>MB</td>
                                    <td>
                                        <?php if ($log->error_count > 0): ?>
                                            <strong style="color: #d63638;"><?php echo number_format($log->error_count); ?></strong>
                                        <?php else: ?>
                                            <span style="color: #666;">0</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="performance-info" style="margin-top: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                <h4 style="margin-top: 0;"><?php esc_html_e('About Performance Monitoring', 'wpseed'); ?></h4>
                <ul style="margin: 0;">
                    <li><?php esc_html_e('Performance monitoring only runs in developer mode', 'wpseed'); ?></li>
                    <li><?php esc_html_e('Logs are automatically cleared after 7 days', 'wpseed'); ?></li>
                    <li><?php esc_html_e('Slow queries are those taking more than 50ms', 'wpseed'); ?></li>
                    <li><?php esc_html_e('High query counts may indicate N+1 query problems', 'wpseed'); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }
}
