<?php
/**
 * Uninstall Feedback System
 * Shows modal on plugin deactivation to collect user feedback
 *
 * @package WPSeed/Admin
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Uninstall_Feedback {
    
    public function __construct() {
        add_action('admin_footer', array($this, 'render_modal'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_ajax_wpseed_uninstall_feedback', array($this, 'handle_feedback'));
    }
    
    public function enqueue_assets($hook) {
        if ($hook !== 'plugins.php') {
            return;
        }
        
        wp_enqueue_style('wpseed-uninstall-feedback', plugins_url('assets/css/uninstall-feedback.css', WPSEED_PLUGIN_FILE), array(), WPSEED_VERSION);
        wp_enqueue_script('wpseed-uninstall-feedback', plugins_url('assets/js/uninstall-feedback.js', WPSEED_PLUGIN_FILE), array('jquery'), WPSEED_VERSION, true);
        
        wp_localize_script('wpseed-uninstall-feedback', 'wpseedUninstall', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpseed_uninstall_feedback'),
            'plugin_slug' => WPSEED_PLUGIN_BASENAME,
        ));
    }
    
    public function render_modal() {
        $screen = get_current_screen();
        if ($screen->id !== 'plugins') {
            return;
        }
        ?>
        <div id="wpseed-uninstall-feedback-modal" style="display:none;">
            <div class="wpseed-modal-overlay"></div>
            <div class="wpseed-modal-content">
                <div class="wpseed-modal-header">
                    <h2><?php _e('Quick Feedback', 'wpseed'); ?></h2>
                    <button class="wpseed-modal-close">&times;</button>
                </div>
                
                <div class="wpseed-modal-body">
                    <p><?php _e('If you have a moment, please let us know why you\'re deactivating WPSeed:', 'wpseed'); ?></p>
                    
                    <form id="wpseed-feedback-form">
                        <label class="wpseed-reason">
                            <input type="radio" name="reason" value="temporary">
                            <span><?php _e('Temporary deactivation', 'wpseed'); ?></span>
                        </label>
                        
                        <label class="wpseed-reason">
                            <input type="radio" name="reason" value="missing_features">
                            <span><?php _e('Missing features I need', 'wpseed'); ?></span>
                        </label>
                        
                        <label class="wpseed-reason">
                            <input type="radio" name="reason" value="found_better">
                            <span><?php _e('Found a better plugin', 'wpseed'); ?></span>
                        </label>
                        
                        <label class="wpseed-reason">
                            <input type="radio" name="reason" value="not_working">
                            <span><?php _e('Plugin not working', 'wpseed'); ?></span>
                        </label>
                        
                        <label class="wpseed-reason">
                            <input type="radio" name="reason" value="too_complex">
                            <span><?php _e('Too complex to use', 'wpseed'); ?></span>
                        </label>
                        
                        <label class="wpseed-reason">
                            <input type="radio" name="reason" value="other">
                            <span><?php _e('Other', 'wpseed'); ?></span>
                        </label>
                        
                        <div class="wpseed-details" style="display:none;">
                            <textarea name="details" placeholder="<?php esc_attr_e('Please tell us more...', 'wpseed'); ?>" rows="4"></textarea>
                        </div>
                        
                        <div class="wpseed-email">
                            <input type="email" name="email" placeholder="<?php esc_attr_e('Your email (optional)', 'wpseed'); ?>">
                            <small><?php _e('We may follow up to help resolve issues', 'wpseed'); ?></small>
                        </div>
                    </form>
                </div>
                
                <div class="wpseed-modal-footer">
                    <button class="button button-secondary wpseed-skip"><?php _e('Skip & Deactivate', 'wpseed'); ?></button>
                    <button class="button button-primary wpseed-submit"><?php _e('Submit & Deactivate', 'wpseed'); ?></button>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function handle_feedback() {
        check_ajax_referer('wpseed_uninstall_feedback', 'nonce');
        
        $reason = sanitize_text_field($_POST['reason'] ?? '');
        $details = sanitize_textarea_field($_POST['details'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        
        // Log feedback
        $feedback = array(
            'reason' => $reason,
            'details' => $details,
            'email' => $email,
            'date' => current_time('mysql'),
            'site_url' => get_site_url(),
            'wp_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION,
        );
        
        // Save to options (last 50 feedbacks)
        $feedbacks = get_option('wpseed_uninstall_feedbacks', array());
        array_unshift($feedbacks, $feedback);
        $feedbacks = array_slice($feedbacks, 0, 50);
        update_option('wpseed_uninstall_feedbacks', $feedbacks);
        
        // Send email to admin
        $admin_email = get_option('admin_email');
        $subject = sprintf(__('[%s] Plugin Deactivation Feedback', 'wpseed'), get_bloginfo('name'));
        $message = sprintf(
            __("Reason: %s\n\nDetails: %s\n\nEmail: %s\n\nSite: %s\nWP Version: %s\nPHP Version: %s", 'wpseed'),
            $reason,
            $details,
            $email,
            get_site_url(),
            get_bloginfo('version'),
            PHP_VERSION
        );
        
        wp_mail($admin_email, $subject, $message);
        
        // Optional: Send to external API
        // wp_remote_post('https://your-api.com/feedback', array('body' => $feedback));
        
        wp_send_json_success();
    }
}

return new WPSeed_Uninstall_Feedback();
