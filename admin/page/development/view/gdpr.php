<?php
/**
 * GDPR Compliance Tools
 *
 * @package WPSeed/Admin/Development
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Admin_Development_GDPR {
    
    public static function output() {
        // Handle actions
        self::handle_actions();
        
        ?>
        <div class="wpseed-gdpr-tools">
            
            <div class="wpseed-intro-box">
                <h2><?php esc_html_e('GDPR Compliance Tools', 'wpseed'); ?></h2>
                <p><?php esc_html_e('Tools to help manage user data, consent, and privacy compliance. These are working examples for your plugin development.', 'wpseed'); ?></p>
            </div>

            <div class="wpseed-gdpr-grid">
                
                <!-- Data Export -->
                <div class="wpseed-gdpr-card">
                    <h3><span class="dashicons dashicons-download"></span> <?php esc_html_e('Data Export', 'wpseed'); ?></h3>
                    <p><?php esc_html_e('Export all user data in JSON format for GDPR compliance.', 'wpseed'); ?></p>
                    
                    <form method="post" class="wpseed-gdpr-form">
                        <?php wp_nonce_field('wpseed_gdpr_export', 'wpseed_gdpr_nonce'); ?>
                        <input type="hidden" name="wpseed_action" value="export_user_data">
                        
                        <label for="export_user_id"><?php esc_html_e('User ID or Email:', 'wpseed'); ?></label>
                        <input type="text" id="export_user_id" name="user_identifier" placeholder="user@example.com" required>
                        
                        <button type="submit" class="button button-primary">
                            <span class="dashicons dashicons-download"></span> <?php esc_html_e('Export Data', 'wpseed'); ?>
                        </button>
                    </form>
                    
                    <div class="wpseed-code-example">
                        <strong><?php esc_html_e('Example Implementation:', 'wpseed'); ?></strong>
                        <pre>// Export user data
$data = array(
    'user_info' => get_userdata($user_id),
    'user_meta' => get_user_meta($user_id),
    'plugin_data' => get_option('wpseed_user_' . $user_id)
);
wp_send_json_success($data);</pre>
                    </div>
                </div>

                <!-- Data Deletion -->
                <div class="wpseed-gdpr-card">
                    <h3><span class="dashicons dashicons-trash"></span> <?php esc_html_e('Data Deletion', 'wpseed'); ?></h3>
                    <p><?php esc_html_e('Permanently delete all user data from the plugin.', 'wpseed'); ?></p>
                    
                    <form method="post" class="wpseed-gdpr-form" onsubmit="return confirm('<?php esc_attr_e('Are you sure? This cannot be undone!', 'wpseed'); ?>');">
                        <?php wp_nonce_field('wpseed_gdpr_delete', 'wpseed_gdpr_nonce'); ?>
                        <input type="hidden" name="wpseed_action" value="delete_user_data">
                        
                        <label for="delete_user_id"><?php esc_html_e('User ID or Email:', 'wpseed'); ?></label>
                        <input type="text" id="delete_user_id" name="user_identifier" placeholder="user@example.com" required>
                        
                        <button type="submit" class="button button-secondary">
                            <span class="dashicons dashicons-trash"></span> <?php esc_html_e('Delete Data', 'wpseed'); ?>
                        </button>
                    </form>
                    
                    <div class="wpseed-code-example">
                        <strong><?php esc_html_e('Example Implementation:', 'wpseed'); ?></strong>
                        <pre>// Delete user data
delete_user_meta($user_id, 'wpseed_data');
delete_option('wpseed_user_' . $user_id);
// Log deletion for compliance
do_action('wpseed_user_data_deleted', $user_id);</pre>
                    </div>
                </div>

                <!-- Consent Management -->
                <div class="wpseed-gdpr-card">
                    <h3><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Consent Management', 'wpseed'); ?></h3>
                    <p><?php esc_html_e('Track and manage user consent for data processing.', 'wpseed'); ?></p>
                    
                    <?php
                    global $wpdb;
                    $consent_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = 'wpseed_gdpr_consent'");
                    ?>
                    
                    <div class="wpseed-stats">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo esc_html($consent_count); ?></span>
                            <span class="stat-label"><?php esc_html_e('Users with Consent', 'wpseed'); ?></span>
                        </div>
                    </div>
                    
                    <div class="wpseed-code-example">
                        <strong><?php esc_html_e('Frontend Consent Form:', 'wpseed'); ?></strong>
                        <pre>&lt;form method="post"&gt;
    &lt;label&gt;
        &lt;input type="checkbox" name="gdpr_consent" required&gt;
        I consent to data processing
    &lt;/label&gt;
    &lt;button type="submit"&gt;Submit&lt;/button&gt;
&lt;/form&gt;

// Save consent
update_user_meta($user_id, 'wpseed_gdpr_consent', array(
    'granted' => true,
    'date' => current_time('mysql'),
    'ip' => $_SERVER['REMOTE_ADDR']
));</pre>
                    </div>
                </div>

                <!-- Privacy Policy Integration -->
                <div class="wpseed-gdpr-card">
                    <h3><span class="dashicons dashicons-privacy"></span> <?php esc_html_e('Privacy Policy', 'wpseed'); ?></h3>
                    <p><?php esc_html_e('Add privacy policy content for your plugin.', 'wpseed'); ?></p>
                    
                    <form method="post" class="wpseed-gdpr-form">
                        <?php wp_nonce_field('wpseed_gdpr_privacy', 'wpseed_gdpr_nonce'); ?>
                        <input type="hidden" name="wpseed_action" value="update_privacy_policy">
                        
                        <label for="privacy_content"><?php esc_html_e('Privacy Policy Content:', 'wpseed'); ?></label>
                        <textarea id="privacy_content" name="privacy_content" rows="6" class="large-text"><?php 
                            echo esc_textarea(get_option('wpseed_privacy_policy_content', '')); 
                        ?></textarea>
                        
                        <button type="submit" class="button button-primary">
                            <span class="dashicons dashicons-saved"></span> <?php esc_html_e('Save Policy', 'wpseed'); ?>
                        </button>
                    </form>
                    
                    <div class="wpseed-code-example">
                        <strong><?php esc_html_e('Register with WordPress:', 'wpseed'); ?></strong>
                        <pre>// Add to privacy policy guide
add_action('admin_init', function() {
    if (function_exists('wp_add_privacy_policy_content')) {
        wp_add_privacy_policy_content(
            'WPSeed',
            get_option('wpseed_privacy_policy_content')
        );
    }
});</pre>
                    </div>
                </div>

                <!-- Data Retention -->
                <div class="wpseed-gdpr-card">
                    <h3><span class="dashicons dashicons-clock"></span> <?php esc_html_e('Data Retention', 'wpseed'); ?></h3>
                    <p><?php esc_html_e('Configure automatic data deletion after specified period.', 'wpseed'); ?></p>
                    
                    <form method="post" class="wpseed-gdpr-form">
                        <?php wp_nonce_field('wpseed_gdpr_retention', 'wpseed_gdpr_nonce'); ?>
                        <input type="hidden" name="wpseed_action" value="update_retention">
                        
                        <label for="retention_days"><?php esc_html_e('Delete data after (days):', 'wpseed'); ?></label>
                        <input type="number" id="retention_days" name="retention_days" 
                               value="<?php echo esc_attr(get_option('wpseed_data_retention_days', 365)); ?>" 
                               min="30" max="3650">
                        
                        <button type="submit" class="button button-primary">
                            <span class="dashicons dashicons-saved"></span> <?php esc_html_e('Update Retention', 'wpseed'); ?>
                        </button>
                    </form>
                    
                    <div class="wpseed-code-example">
                        <strong><?php esc_html_e('Scheduled Cleanup:', 'wpseed'); ?></strong>
                        <pre>// Schedule cleanup
add_action('wpseed_daily_cleanup', function() {
    $days = get_option('wpseed_data_retention_days', 365);
    $date = date('Y-m-d', strtotime("-{$days} days"));
    
    // Delete old data
    global $wpdb;
    $wpdb->query($wpdb->prepare(
        "DELETE FROM {$wpdb->usermeta} 
         WHERE meta_key LIKE 'wpseed_%' 
         AND DATE(meta_value) < %s",
        $date
    ));
});</pre>
                    </div>
                </div>

                <!-- Data Portability -->
                <div class="wpseed-gdpr-card">
                    <h3><span class="dashicons dashicons-migrate"></span> <?php esc_html_e('Data Portability', 'wpseed'); ?></h3>
                    <p><?php esc_html_e('Generate machine-readable data export for users.', 'wpseed'); ?></p>
                    
                    <div class="wpseed-info-box">
                        <p><strong><?php esc_html_e('Supported Formats:', 'wpseed'); ?></strong></p>
                        <ul>
                            <li>JSON - Machine readable</li>
                            <li>CSV - Spreadsheet compatible</li>
                            <li>XML - Universal format</li>
                        </ul>
                    </div>
                    
                    <div class="wpseed-code-example">
                        <strong><?php esc_html_e('Export Formats:', 'wpseed'); ?></strong>
                        <pre>// JSON Export
header('Content-Type: application/json');
echo json_encode($user_data);

// CSV Export
header('Content-Type: text/csv');
$output = fopen('php://output', 'w');
fputcsv($output, array_keys($user_data));
fputcsv($output, $user_data);</pre>
                    </div>
                </div>

            </div>

            <style>
                .wpseed-gdpr-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
                    gap: 20px;
                    margin-top: 20px;
                }
                .wpseed-gdpr-card {
                    background: #fff;
                    border: 1px solid #ccd0d4;
                    padding: 20px;
                    border-radius: 4px;
                }
                .wpseed-gdpr-card h3 {
                    margin-top: 0;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                .wpseed-gdpr-card h3 .dashicons {
                    color: #2271b1;
                }
                .wpseed-gdpr-form {
                    margin: 15px 0;
                }
                .wpseed-gdpr-form label {
                    display: block;
                    margin-bottom: 5px;
                    font-weight: 600;
                }
                .wpseed-gdpr-form input[type="text"],
                .wpseed-gdpr-form input[type="number"],
                .wpseed-gdpr-form textarea {
                    width: 100%;
                    margin-bottom: 10px;
                }
                .wpseed-gdpr-form button {
                    margin-top: 5px;
                }
                .wpseed-code-example {
                    background: #f6f7f7;
                    border-left: 3px solid #2271b1;
                    padding: 10px;
                    margin-top: 15px;
                }
                .wpseed-code-example pre {
                    margin: 5px 0 0 0;
                    font-size: 12px;
                    overflow-x: auto;
                }
                .wpseed-stats {
                    display: flex;
                    gap: 20px;
                    margin: 15px 0;
                }
                .stat-item {
                    text-align: center;
                }
                .stat-number {
                    display: block;
                    font-size: 32px;
                    font-weight: bold;
                    color: #2271b1;
                }
                .stat-label {
                    display: block;
                    font-size: 12px;
                    color: #646970;
                }
                .wpseed-info-box {
                    background: #f0f6fc;
                    border-left: 3px solid #2271b1;
                    padding: 10px 15px;
                    margin: 15px 0;
                }
                .wpseed-info-box ul {
                    margin: 5px 0;
                }
            </style>
        </div>
        <?php
    }
    
    private static function handle_actions() {
        if (!isset($_POST['wpseed_action']) || !isset($_POST['wpseed_gdpr_nonce'])) {
            return;
        }
        
        $action = sanitize_text_field(wp_unslash($_POST['wpseed_action']));
        
        if (!wp_verify_nonce(wp_unslash($_POST['wpseed_gdpr_nonce']), 'wpseed_gdpr_' . str_replace('_user_data', '', str_replace('update_', '', $action)))) {
            wp_die(esc_html__('Security check failed', 'wpseed'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Unauthorized', 'wpseed'));
        }
        
        switch ($action) {
            case 'export_user_data':
                self::export_user_data();
                break;
            case 'delete_user_data':
                self::delete_user_data();
                break;
            case 'update_privacy_policy':
                self::update_privacy_policy();
                break;
            case 'update_retention':
                self::update_retention();
                break;
        }
    }
    
    private static function export_user_data() {
        $identifier = isset($_POST['user_identifier']) ? sanitize_text_field(wp_unslash($_POST['user_identifier'])) : '';
        $user = is_numeric($identifier) ? get_user_by('id', $identifier) : get_user_by('email', $identifier);
        
        if (!$user) {
            add_settings_error('wpseed_gdpr', 'user_not_found', __('User not found', 'wpseed'), 'error');
            return;
        }
        
        $data = array(
            'user_id' => $user->ID,
            'user_email' => $user->user_email,
            'user_login' => $user->user_login,
            'display_name' => $user->display_name,
            'registered' => $user->user_registered,
            'meta' => get_user_meta($user->ID),
            'plugin_data' => get_option('wpseed_user_' . $user->ID, array()),
        );
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="user-' . $user->ID . '-data.json"');
        echo wp_json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
    
    private static function delete_user_data() {
        $identifier = isset($_POST['user_identifier']) ? sanitize_text_field(wp_unslash($_POST['user_identifier'])) : '';
        $user = is_numeric($identifier) ? get_user_by('id', $identifier) : get_user_by('email', $identifier);
        
        if (!$user) {
            add_settings_error('wpseed_gdpr', 'user_not_found', __('User not found', 'wpseed'), 'error');
            return;
        }
        
        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key LIKE 'wpseed_%'", $user->ID));
        delete_option('wpseed_user_' . $user->ID);
        
        add_settings_error('wpseed_gdpr', 'data_deleted', __('User data deleted successfully', 'wpseed'), 'success');
    }
    
    private static function update_privacy_policy() {
        $content = isset($_POST['privacy_content']) ? wp_kses_post(wp_unslash($_POST['privacy_content'])) : '';
        update_option('wpseed_privacy_policy_content', $content);
        add_settings_error('wpseed_gdpr', 'policy_updated', __('Privacy policy updated', 'wpseed'), 'success');
    }
    
    private static function update_retention() {
        $days = isset($_POST['retention_days']) ? absint($_POST['retention_days']) : 365;
        update_option('wpseed_data_retention_days', $days);
        add_settings_error('wpseed_gdpr', 'retention_updated', __('Data retention period updated', 'wpseed'), 'success');
    }
}
