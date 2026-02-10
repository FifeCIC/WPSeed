<?php
/**
 * WPSeed Development Tasks
 *
 * @package WPSeed/Admin/Views
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WPSeed_Admin_Development_Tasks Class
 */
class WPSeed_Admin_Development_Tasks {
    
    /**
     * Output the tasks view
     */
    public static function output() {
        // Dashicons is a WordPress core style, keep as-is
        wp_enqueue_style('dashicons');
        
        $tasks = self::get_github_tasks();
        ?>
        
        <div class="tab-content" id="tasks">
            <div class="wpseed-tasks-container">
                <div class="wpseed-tasks-header">
                    <p><?php esc_html_e('GitHub issues provide a feedback and reporting system. Users can report bugs, request features, and participate in development.', 'wpseed'); ?></p>
                    
                    <div class="wpseed-tasks-filters">
                        <div class="filter-group">
                            <label for="task-status"><?php esc_html_e('Status:', 'wpseed'); ?></label>
                            <select id="task-status" class="task-filter">
                                <option value="all"><?php esc_html_e('All', 'wpseed'); ?></option>
                                <option value="open"><?php esc_html_e('Open', 'wpseed'); ?></option>
                                <option value="closed"><?php esc_html_e('Closed', 'wpseed'); ?></option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="task-search"><?php esc_html_e('Search:', 'wpseed'); ?></label>
                            <input type="text" id="task-search" class="task-search" placeholder="<?php esc_attr_e('Search tasks...', 'wpseed'); ?>">
                        </div>
                    </div>
                </div>
                
                <table class="wpseed-tasks-table widefat">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Status', 'wpseed'); ?></th>
                            <th><?php esc_html_e('Issue', 'wpseed'); ?></th>
                            <th><?php esc_html_e('Labels', 'wpseed'); ?></th>
                            <th><?php esc_html_e('Updated', 'wpseed'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tasks)) : ?>
                            <tr>
                                <td colspan="4"><?php esc_html_e('No GitHub issues found. Configure GitHub settings to sync issues.', 'wpseed'); ?></td>
                            </tr>
                        <?php else : 
                            foreach ($tasks as $task) : 
                        ?>
                            <tr data-status="<?php echo esc_attr($task['status']); ?>">
                                <td>
                                    <span class="task-status-badge <?php echo esc_attr($task['status']); ?>">
                                        <?php echo esc_html(ucfirst($task['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url($task['link']); ?>" target="_blank">
                                        <?php echo esc_html($task['title']); ?>
                                    </a>
                                    <span class="task-number">#<?php echo esc_html($task['number']); ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($task['labels'])) : ?>
                                        <?php foreach ($task['labels'] as $label) : ?>
                                            <span class="task-label" style="background-color: #<?php echo esc_attr($label->color); ?>">
                                                <?php echo esc_html($label->name); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html(human_time_diff(strtotime($task['updated_at']), current_time('timestamp')) . ' ago'); ?></td>
                            </tr>
                        <?php 
                            endforeach; 
                        endif; 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <style>
        .wpseed-tasks-container { margin: 20px 0; }
        .wpseed-tasks-header { margin-bottom: 20px; }
        .wpseed-tasks-filters { display: flex; gap: 15px; margin-top: 15px; }
        .filter-group { display: flex; align-items: center; gap: 8px; }
        .task-filter, .task-search { padding: 5px 10px; }
        .wpseed-tasks-table { margin-top: 20px; }
        .task-status-badge { 
            display: inline-block; 
            padding: 4px 10px; 
            border-radius: 3px; 
            font-size: 12px; 
            font-weight: 600; 
        }
        .task-status-badge.open { background: #28a745; color: white; }
        .task-status-badge.closed { background: #6c757d; color: white; }
        .task-number { color: #666; margin-left: 5px; }
        .task-label { 
            display: inline-block; 
            padding: 2px 8px; 
            margin: 2px; 
            border-radius: 3px; 
            font-size: 11px; 
            color: white; 
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            $('#task-status').on('change', function() {
                var status = $(this).val();
                $('.wpseed-tasks-table tbody tr').each(function() {
                    if (status === 'all' || $(this).data('status') === status) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
            
            $('#task-search').on('keyup', function() {
                var search = $(this).val().toLowerCase();
                $('.wpseed-tasks-table tbody tr').each(function() {
                    var text = $(this).text().toLowerCase();
                    $(this).toggle(text.indexOf(search) > -1);
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * Get GitHub issues as tasks
     */
    private static function get_github_tasks() {
        $repo_owner = get_option('wpseed_github_repo_owner', '');
        $repo_name = get_option('wpseed_github_repo_name', '');
        
        if (empty($repo_owner) || empty($repo_name)) {
            return array();
        }
        
        $token = get_option('wpseed_github_token', '');
        if (empty($token)) {
            return array();
        }
        
        $cache_key = 'wpseed_github_issues_' . md5($repo_owner . $repo_name);
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        $url = "https://api.github.com/repos/{$repo_owner}/{$repo_name}/issues";
        
        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'token ' . $token,
                'Accept' => 'application/vnd.github.v3+json',
            ),
            'timeout' => 15,
        ));
        
        if (is_wp_error($response)) {
            return array();
        }
        
        $issues = json_decode(wp_remote_retrieve_body($response));
        
        if (!is_array($issues)) {
            return array();
        }
        
        $tasks = array();
        foreach ($issues as $issue) {
            $tasks[] = array(
                'number' => $issue->number,
                'title' => $issue->title,
                'status' => $issue->state,
                'link' => $issue->html_url,
                'labels' => isset($issue->labels) ? $issue->labels : array(),
                'updated_at' => $issue->updated_at,
            );
        }
        
        set_transient($cache_key, $tasks, HOUR_IN_SECONDS);
        
        return $tasks;
    }
}
