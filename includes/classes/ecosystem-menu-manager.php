<?php
/**
 * Ecosystem Menu Manager
 * Dynamically places menus based on ecosystem mode
 *
 * @package WPSeed/Ecosystem
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Ecosystem_Menu_Manager {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'register_menus'), 999);
    }
    
    /**
     * Register menus based on ecosystem mode
     */
    public function register_menus() {
        $ecosystem = wpseed_ecosystem();
        
        if ($ecosystem->use_shared_menu()) {
            $this->register_shared_menus();
        } else {
            $this->register_plugin_menus();
        }
    }
    
    /**
     * Register shared menus (Tools & Settings)
     */
    private function register_shared_menus() {
        // Shared Logging in Tools
        add_management_page(
            __('Ecosystem Logging', 'wpseed'),
            __('Ecosystem Logs', 'wpseed'),
            'manage_options',
            'wpseed-ecosystem-logs',
            array($this, 'render_shared_logging')
        );
        
        // Shared Cron/Background Tasks in Tools
        add_management_page(
            __('Background Tasks', 'wpseed'),
            __('Background Tasks', 'wpseed'),
            'manage_options',
            'wpseed-ecosystem-tasks',
            array($this, 'render_shared_tasks')
        );
        
        // Shared Settings
        add_options_page(
            __('Ecosystem Settings', 'wpseed'),
            __('Ecosystem', 'wpseed'),
            'manage_options',
            'wpseed-ecosystem-settings',
            array($this, 'render_shared_settings')
        );
    }
    
    /**
     * Register plugin-specific menus
     */
    private function register_plugin_menus() {
        // Keep in plugin's own menu
        // (existing menu structure remains)
    }
    
    /**
     * Render shared logging view
     */
    public function render_shared_logging() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Ecosystem Logging', 'wpseed'); ?></h1>
            <p><?php esc_html_e('Unified logging across all Ryan Bayne plugins', 'wpseed'); ?></p>
            
            <?php
            $ecosystem = wpseed_ecosystem();
            $plugins = $ecosystem->get_plugins();
            ?>
            
            <div class="ecosystem-tabs">
                <?php foreach ($plugins as $slug => $plugin): ?>
                    <?php if ($plugin['has_logging']): ?>
                        <a href="#<?php echo esc_attr($slug); ?>-logs" class="nav-tab">
                            <?php echo esc_html($plugin['name']); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
            <?php foreach ($plugins as $slug => $plugin): ?>
                <?php if ($plugin['has_logging']): ?>
                    <div id="<?php echo esc_attr($slug); ?>-logs" class="tab-content">
                        <?php
                        // Call plugin's logging view
                        $resources = $ecosystem->get_shared_resources('logging');
                        foreach ($resources as $resource) {
                            if (is_callable($resource['callback'])) {
                                call_user_func($resource['callback'], $slug);
                            }
                        }
                        ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Render shared background tasks view
     */
    public function render_shared_tasks() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Background Tasks Monitor', 'wpseed'); ?></h1>
            <p><?php esc_html_e('View CRON jobs, async processes, and background tasks across all plugins', 'wpseed'); ?></p>
            
            <?php
            $ecosystem = wpseed_ecosystem();
            $plugins = $ecosystem->get_plugins();
            ?>
            
            <h2><?php esc_html_e('WordPress CRON Jobs', 'wpseed'); ?></h2>
            <?php $this->render_cron_jobs($plugins); ?>
            
            <h2><?php esc_html_e('Background Processes', 'wpseed'); ?></h2>
            <?php $this->render_background_processes($plugins); ?>
            
            <h2><?php esc_html_e('Async Tasks', 'wpseed'); ?></h2>
            <?php $this->render_async_tasks($plugins); ?>
        </div>
        <?php
    }
    
    /**
     * Render CRON jobs
     */
    private function render_cron_jobs($plugins) {
        $crons = _get_cron_array();
        
        if (empty($crons)) {
            echo '<p>' . esc_html__('No scheduled CRON jobs found.', 'wpseed') . '</p>';
            return;
        }
        
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Hook', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Plugin', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Next Run', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Recurrence', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Actions', 'wpseed'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($crons as $timestamp => $cron): ?>
                    <?php foreach ($cron as $hook => $events): ?>
                        <?php
                        // Detect which plugin owns this hook
                        $owner = 'Unknown';
                        foreach ($plugins as $slug => $plugin) {
                            if (strpos($hook, $slug) !== false) {
                                $owner = $plugin['name'];
                                break;
                            }
                        }
                        ?>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><code><?php echo esc_html($hook); ?></code></td>
                                <td><?php echo esc_html($owner); ?></td>
                                <td><?php echo esc_html(human_time_diff($timestamp, current_time('timestamp')) . ' from now'); ?></td>
                                <td><?php echo esc_html($event['schedule'] ?? 'One-time'); ?></td>
                                <td>
                                    <button class="button button-small run-now" data-hook="<?php echo esc_attr($hook); ?>">
                                        <?php esc_html_e('Run Now', 'wpseed'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Render background processes
     */
    private function render_background_processes($plugins) {
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Process', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Plugin', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Status', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Progress', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Started', 'wpseed'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Get background processes from each plugin
                $ecosystem = wpseed_ecosystem();
                $resources = $ecosystem->get_shared_resources('background_tasks');
                
                if (empty($resources)) {
                    echo '<tr><td colspan="5">' . esc_html__('No background processes running.', 'wpseed') . '</td></tr>';
                } else {
                    foreach ($resources as $resource) {
                        if (is_callable($resource['callback'])) {
                            call_user_func($resource['callback']);
                        }
                    }
                }
                ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Render async tasks
     */
    private function render_async_tasks($plugins) {
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Task', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Plugin', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Status', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Queued', 'wpseed'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Get async tasks from each plugin
                $ecosystem = wpseed_ecosystem();
                $resources = $ecosystem->get_shared_resources('async_tasks');
                
                if (empty($resources)) {
                    echo '<tr><td colspan="4">' . esc_html__('No async tasks queued.', 'wpseed') . '</td></tr>';
                } else {
                    foreach ($resources as $resource) {
                        if (is_callable($resource['callback'])) {
                            call_user_func($resource['callback']);
                        }
                    }
                }
                ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Render shared settings
     */
    public function render_shared_settings() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Ecosystem Settings', 'wpseed'); ?></h1>
            
            <form method="post" action="options.php">
                <?php settings_fields('wpseed_ecosystem'); ?>
                
                <h2><?php esc_html_e('Menu Location', 'wpseed'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e('Shared Views Location', 'wpseed'); ?></th>
                        <td>
                            <label>
                                <input type="radio" name="wpseed_ecosystem_menu_location" value="shared" <?php checked(get_option('wpseed_ecosystem_menu_location', 'shared'), 'shared'); ?>>
                                <?php esc_html_e('Tools & Settings (Recommended for 2+ plugins)', 'wpseed'); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="wpseed_ecosystem_menu_location" value="plugin" <?php checked(get_option('wpseed_ecosystem_menu_location', 'shared'), 'plugin'); ?>>
                                <?php esc_html_e('Each Plugin Menu (Single plugin mode)', 'wpseed'); ?>
                            </label>
                            <p class="description">
                                <?php esc_html_e('When multiple Ryan Bayne plugins are installed, shared views (logging, CRON, background tasks) can be moved to WordPress Tools and Settings menus.', 'wpseed'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <h2><?php esc_html_e('Installed Plugins', 'wpseed'); ?></h2>
                <?php
                $ecosystem = wpseed_ecosystem();
                $plugins = $ecosystem->get_plugins();
                ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Plugin', 'wpseed'); ?></th>
                            <th><?php esc_html_e('Version', 'wpseed'); ?></th>
                            <th><?php esc_html_e('Features', 'wpseed'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($plugins as $slug => $plugin): ?>
                            <tr>
                                <td><strong><?php echo esc_html($plugin['name']); ?></strong></td>
                                <td><?php echo esc_html($plugin['version']); ?></td>
                                <td>
                                    <?php if ($plugin['has_logging']): ?>
                                        <span class="dashicons dashicons-list-view" title="<?php esc_attr_e('Logging', 'wpseed'); ?>"></span>
                                    <?php endif; ?>
                                    <?php if ($plugin['has_cron']): ?>
                                        <span class="dashicons dashicons-clock" title="<?php esc_attr_e('CRON Jobs', 'wpseed'); ?>"></span>
                                    <?php endif; ?>
                                    <?php if ($plugin['has_background_tasks']): ?>
                                        <span class="dashicons dashicons-update" title="<?php esc_attr_e('Background Tasks', 'wpseed'); ?>"></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

return new WPSeed_Ecosystem_Menu_Manager();
