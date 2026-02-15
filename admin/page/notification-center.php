<?php
/**
 * WPSeed Notification Center
 *
 * @package WPSeed/Admin
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

// Enqueue assets
wp_enqueue_style('wpseed-notification-center', WPSeed()->plugin_url() . '/assets/css/notification-center.css', array(), WPSEED_VERSION);
wp_enqueue_script('wpseed-notification-center', WPSeed()->plugin_url() . '/assets/js/notification-center.js', array('jquery'), WPSEED_VERSION);

// Handle actions
if (isset($_POST['notification_action'])) {
    check_admin_referer('wpseed_notification_action');
    
    $action = sanitize_text_field($_POST['notification_action']);
    $notification_id = intval($_POST['notification_id']);
    
    switch ($action) {
        case 'mark_read':
            WPSeed_Notifications::mark_as_read($notification_id);
            break;
        case 'snooze':
            $duration = isset($_POST['snooze_duration']) ? intval($_POST['snooze_duration']) : 3600;
            WPSeed_Notifications::snooze_notification($notification_id, $duration);
            break;
        case 'delete':
            WPSeed_Notifications::delete_notification($notification_id);
            break;
        case 'mark_all_read':
            WPSeed_Notifications::mark_all_read(get_current_user_id());
            break;
    }
    
    wp_redirect(admin_url('admin.php?page=wpseed-notifications'));
    exit;
}

$user_id = get_current_user_id();
$filter = isset($_GET['filter']) ? sanitize_text_field($_GET['filter']) : 'all';

$args = array('limit' => 50);
if ($filter === 'unread') {
    $args['is_read'] = 0;
}

$notifications = WPSeed_Notifications::get_notifications($user_id, $args);
$unread_count = WPSeed_Notifications::get_unread_count($user_id);
?>

<div class="wrap wpseed-notification-center">
    <h1><?php _e('Notification Center', 'wpseed'); ?></h1>
    
    <div class="notification-header" style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
        <div class="notification-filters">
            <a href="<?php echo admin_url('admin.php?page=wpseed-notifications'); ?>" 
               class="button <?php echo $filter === 'all' ? 'button-primary' : ''; ?>">
                <?php _e('All', 'wpseed'); ?> (<?php echo count($notifications); ?>)
            </a>
            <a href="<?php echo admin_url('admin.php?page=wpseed-notifications&filter=unread'); ?>" 
               class="button <?php echo $filter === 'unread' ? 'button-primary' : ''; ?>">
                <?php _e('Unread', 'wpseed'); ?> (<?php echo $unread_count; ?>)
            </a>
        </div>
        
        <?php if ($unread_count > 0): ?>
        <form method="post" style="display: inline;">
            <?php wp_nonce_field('wpseed_notification_action'); ?>
            <input type="hidden" name="notification_action" value="mark_all_read">
            <button type="submit" class="button">
                <?php _e('Mark All Read', 'wpseed'); ?>
            </button>
        </form>
        <?php endif; ?>
    </div>
    
    <?php if (empty($notifications)): ?>
        <div class="notice notice-info">
            <p><?php _e('No notifications found.', 'wpseed'); ?></p>
        </div>
    <?php else: ?>
        <div class="notifications-list">
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-item <?php echo $notification->is_read ? 'read' : 'unread'; ?> <?php echo $notification->priority === 'high' ? 'priority-high' : ''; ?>">
                    
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <div class="notification-meta">
                                <?php if (!$notification->is_read): ?>
                                    <span class="unread-dot"></span>
                                <?php endif; ?>
                                
                                <span class="notification-type">
                                    <?php echo esc_html(strtoupper($notification->type)); ?>
                                </span>
                                
                                <?php if ($notification->priority === 'high'): ?>
                                    <span class="priority-high-label">⚠ <?php _e('High Priority', 'wpseed'); ?></span>
                                <?php endif; ?>
                                
                                <span class="notification-time">
                                    <?php echo human_time_diff(strtotime($notification->created_at), current_time('timestamp')); ?> <?php _e('ago', 'wpseed'); ?>
                                </span>
                            </div>
                            
                            <div class="notification-message">
                                <?php echo esc_html($notification->message); ?>
                            </div>
                            
                            <div class="notification-actions">
                                <?php if (!$notification->is_read): ?>
                                    <form method="post" style="display: inline;">
                                        <?php wp_nonce_field('wpseed_notification_action'); ?>
                                        <input type="hidden" name="notification_action" value="mark_read">
                                        <input type="hidden" name="notification_id" value="<?php echo $notification->id; ?>">
                                        <button type="submit" class="button button-small">
                                            <?php _e('Mark Read', 'wpseed'); ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($notification->action_url): ?>
                                    <a href="<?php echo esc_url($notification->action_url); ?>" class="button button-small button-primary">
                                        <?php echo esc_html($notification->action_label ?: __('View', 'wpseed')); ?>
                                    </a>
                                <?php endif; ?>
                                
                                <button type="button" class="button button-small snooze-btn" data-id="<?php echo $notification->id; ?>">
                                    <?php _e('Snooze', 'wpseed'); ?>
                                </button>
                                
                                <form method="post" style="display: inline;">
                                    <?php wp_nonce_field('wpseed_notification_action'); ?>
                                    <input type="hidden" name="notification_action" value="delete">
                                    <input type="hidden" name="notification_id" value="<?php echo $notification->id; ?>">
                                    <button type="submit" class="button button-small button-link-delete">
                                        <?php _e('Delete', 'wpseed'); ?>
                                    </button>
                                </form>
                            </div>
                            
                            <!-- Snooze options (hidden by default) -->
                            <div class="snooze-options" id="snooze-<?php echo $notification->id; ?>">
                                <form method="post">
                                    <?php wp_nonce_field('wpseed_notification_action'); ?>
                                    <input type="hidden" name="notification_action" value="snooze">
                                    <input type="hidden" name="notification_id" value="<?php echo $notification->id; ?>">
                                    <label><?php _e('Snooze for:', 'wpseed'); ?></label>
                                    <select name="snooze_duration">
                                        <option value="3600"><?php _e('1 hour', 'wpseed'); ?></option>
                                        <option value="21600"><?php _e('6 hours', 'wpseed'); ?></option>
                                        <option value="86400"><?php _e('1 day', 'wpseed'); ?></option>
                                        <option value="604800"><?php _e('1 week', 'wpseed'); ?></option>
                                    </select>
                                    <button type="submit" class="button button-small"><?php _e('Apply', 'wpseed'); ?></button>
                                    <button type="button" class="button button-small cancel-snooze"><?php _e('Cancel', 'wpseed'); ?></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
