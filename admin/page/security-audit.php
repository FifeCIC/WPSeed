<?php
/**
 * WPSeed Security Audit Page
 *
 * @package WPSeed/Admin
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

// Get audit data
$wpseed_audit_data = get_option('wpseed_security_audit', array());

// Handle form submission
if (isset($_POST['wpseed_update_audit']) && check_admin_referer('wpseed_security_audit')) {
    $check_id = isset($_POST['check_id']) ? sanitize_text_field($_POST['check_id']) : '';
    $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
    $tool = isset($_POST['tool']) ? sanitize_text_field($_POST['tool']) : '';
    $notes = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';
    
    $wpseed_audit_data[$check_id] = array(
        'status' => $status,
        'tool' => $tool,
        'notes' => $notes,
        'last_checked' => current_time('mysql'),
        'checked_by' => get_current_user_id()
    );
    
    update_option('wpseed_security_audit', $wpseed_audit_data);
    echo '<div class="notice notice-success"><p>' . esc_html__('Security check updated successfully.', 'wpseed') . '</p></div>';
}

// Security checklist
$security_checks = array(
    'input_sanitization' => array(
        'title' => 'Input Validation & Sanitization',
        'items' => array(
            'All user input sanitized',
            'Data types validated',
            'HTML content filtered with wp_kses()',
            'File uploads sanitized'
        )
    ),
    'output_escaping' => array(
        'title' => 'Output Escaping',
        'items' => array(
            'All output escaped (esc_html, esc_attr, esc_url)',
            'JSON output uses wp_json_encode()',
            'No raw user data in output'
        )
    ),
    'nonce_verification' => array(
        'title' => 'Nonce Verification',
        'items' => array(
            'Nonces on all forms',
            'AJAX requests verified',
            'check_admin_referer() used'
        )
    ),
    'capability_checks' => array(
        'title' => 'Capability Checks',
        'items' => array(
            'current_user_can() before actions',
            'Appropriate capabilities used',
            'Admin functions protected'
        )
    ),
    'sql_injection' => array(
        'title' => 'SQL Injection Prevention',
        'items' => array(
            '$wpdb->prepare() for all queries',
            'No SQL concatenation',
            'WordPress query functions used'
        )
    ),
    'csrf_protection' => array(
        'title' => 'CSRF Protection',
        'items' => array(
            'Nonces on state changes',
            'POST used for mutations',
            'Referrer verification'
        )
    ),
    'file_security' => array(
        'title' => 'File Security',
        'items' => array(
            'ABSPATH check in all files',
            'File uploads validated',
            'wp_handle_upload() used'
        )
    ),
    'api_security' => array(
        'title' => 'API Security',
        'items' => array(
            'REST endpoints authenticated',
            'permission_callback set',
            'API input validated'
        )
    )
);

?>

<div class="wrap">
    <h1><?php esc_html_e('Security Audit', 'wpseed'); ?></h1>
    <p><?php esc_html_e('Track security compliance and testing for your WordPress plugin.', 'wpseed'); ?></p>
    
    <div class="security-audit-container">
        <?php foreach ($security_checks as $check_id => $check): ?>
            <?php 
            $check_data = isset($wpseed_audit_data[$check_id]) ? $wpseed_audit_data[$check_id] : null;
            $status = $check_data ? $check_data['status'] : 'pending';
            $status_class = $status === 'pass' ? 'status-pass' : ($status === 'fail' ? 'status-fail' : 'status-pending');
            ?>
            
            <div class="security-check-card <?php echo esc_attr($status_class); ?>">
                <div class="check-header">
                    <h2><?php echo esc_html($check['title']); ?></h2>
                    <span class="status-badge status-<?php echo esc_attr($status); ?>">
                        <?php echo esc_html(ucfirst($status)); ?>
                    </span>
                </div>
                
                <div class="check-items">
                    <ul>
                        <?php foreach ($check['items'] as $item): ?>
                            <li><?php echo esc_html($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <?php if ($check_data): ?>
                    <div class="check-info">
                        <p><strong><?php esc_html_e('Last Checked:', 'wpseed'); ?></strong> 
                            <?php echo esc_html( gmdate( 'F j, Y g:i a', strtotime( $check_data['last_checked'] ) ) ); ?>
                        </p>
                        <p><strong><?php esc_html_e('Tool Used:', 'wpseed'); ?></strong> 
                            <?php echo esc_html($check_data['tool']); ?>
                        </p>
                        <?php if (!empty($check_data['notes'])): ?>
                            <p><strong><?php esc_html_e('Notes:', 'wpseed'); ?></strong> 
                                <?php echo esc_html($check_data['notes']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <button type="button" class="button update-check-btn" data-check-id="<?php echo esc_attr($check_id); ?>">
                    <?php esc_html_e('Update Check', 'wpseed'); ?>
                </button>
                
                <!-- Update Form (hidden by default) -->
                <div class="update-form" id="form-<?php echo esc_attr($check_id); ?>" style="display:none;">
                    <form method="post">
                        <?php wp_nonce_field('wpseed_security_audit'); ?>
                        <input type="hidden" name="wpseed_update_audit" value="1">
                        <input type="hidden" name="check_id" value="<?php echo esc_attr($check_id); ?>">
                        
                        <table class="form-table">
                            <tr>
                                <th><?php esc_html_e('Status', 'wpseed'); ?></th>
                                <td>
                                    <select name="status" required>
                                        <option value="pending" <?php selected($status, 'pending'); ?>><?php esc_html_e('Pending', 'wpseed'); ?></option>
                                        <option value="pass" <?php selected($status, 'pass'); ?>><?php esc_html_e('Pass', 'wpseed'); ?></option>
                                        <option value="fail" <?php selected($status, 'fail'); ?>><?php esc_html_e('Fail', 'wpseed'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Tool Used', 'wpseed'); ?></th>
                                <td>
                                    <input type="text" name="tool" class="regular-text" 
                                           value="<?php echo $check_data ? esc_attr($check_data['tool']) : ''; ?>" 
                                           placeholder="e.g., Plugin Check, Manual Review, Amazon Q">
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Notes', 'wpseed'); ?></th>
                                <td>
                                    <textarea name="notes" rows="3" class="large-text"><?php echo $check_data ? esc_textarea($check_data['notes']) : ''; ?></textarea>
                                </td>
                            </tr>
                        </table>
                        
                        <p>
                            <button type="submit" class="button button-primary"><?php esc_html_e('Save', 'wpseed'); ?></button>
                            <button type="button" class="button cancel-update-btn"><?php esc_html_e('Cancel', 'wpseed'); ?></button>
                        </p>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.security-audit-container {
    margin-top: 20px;
}
.security-check-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-left: 4px solid #ddd;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
.security-check-card.status-pass {
    border-left-color: #46b450;
}
.security-check-card.status-fail {
    border-left-color: #dc3232;
}
.security-check-card.status-pending {
    border-left-color: #ffb900;
}
.check-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}
.check-header h2 {
    margin: 0;
    font-size: 18px;
}
.status-badge {
    padding: 4px 12px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}
.status-badge.status-pass {
    background: #46b450;
    color: #fff;
}
.status-badge.status-fail {
    background: #dc3232;
    color: #fff;
}
.status-badge.status-pending {
    background: #ffb900;
    color: #000;
}
.check-items ul {
    margin: 0 0 15px 20px;
}
.check-items li {
    margin-bottom: 5px;
}
.check-info {
    background: #f6f7f7;
    padding: 10px;
    margin: 15px 0;
    border-radius: 3px;
}
.check-info p {
    margin: 5px 0;
}
.update-form {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #ddd;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.update-check-btn').on('click', function() {
        var checkId = $(this).data('check-id');
        $('#form-' + checkId).slideToggle();
    });
    
    $('.cancel-update-btn').on('click', function() {
        $(this).closest('.update-form').slideUp();
    });
});
</script>
