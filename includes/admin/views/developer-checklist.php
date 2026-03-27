<?php
/**
 * Developer Checklist View
 *
 * @package WPSeed/Admin/Views
 */

if (!defined('ABSPATH')) exit;

// Only show in dev environment
if (!WPSeed_Developer_Mode::is_dev_environment()) {
    wp_die('Access denied');
}

$wpseed_checklist_file = plugin_dir_path(WPSEED_PLUGIN_FILE) . 'docs/DEVELOPER-CHECKLIST.md';
$checklist = file_exists($wpseed_checklist_file) ? file_get_contents($wpseed_checklist_file) : '';
?>

<div class="wrap wpseed-developer-checklist">
    <h1><?php esc_html_e('Developer Checklist', 'wpseed'); ?></h1>
    
    <div class="wpseed-checklist-content">
        <?php if ($checklist): ?>
            <div class="markdown-content">
                <?php echo wp_kses_post(wpautop($checklist)); ?>
            </div>
        <?php else: ?>
            <p><?php esc_html_e('Checklist file not found.', 'wpseed'); ?></p>
        <?php endif; ?>
    </div>
</div>


