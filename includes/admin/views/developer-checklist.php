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

$checklist_file = plugin_dir_path(WPSEED_PLUGIN_FILE) . 'docs/DEVELOPER-CHECKLIST.md';
$checklist = file_exists($checklist_file) ? file_get_contents($checklist_file) : '';
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

<style>
.wpseed-checklist-content {
    background: #fff;
    padding: 20px;
    margin-top: 20px;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.markdown-content {
    max-width: 900px;
}

.markdown-content h2 {
    border-bottom: 2px solid #0073aa;
    padding-bottom: 10px;
    margin-top: 30px;
}

.markdown-content h3 {
    color: #0073aa;
    margin-top: 20px;
}

.markdown-content code {
    background: #f0f0f1;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}

.markdown-content pre {
    background: #23282d;
    color: #f0f0f1;
    padding: 15px;
    border-radius: 4px;
    overflow-x: auto;
}
</style>
