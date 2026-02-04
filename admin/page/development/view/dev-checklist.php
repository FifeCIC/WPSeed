<?php
/**
 * Developer Checklist View
 *
 * @package WPSeed/Admin/Views
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Admin_Development_Checklist {
    
    public static function output() {
        $checklist_file = WPSEED_PLUGIN_DIR_PATH . 'docs/DEVELOPER-CHECKLIST.md';
        $checklist = file_exists($checklist_file) ? file_get_contents($checklist_file) : '';
        
        ?>
        <div class="wpseed-dev-checklist-tab">
            <?php if ($checklist): ?>
                <div class="markdown-content">
                    <?php echo wp_kses_post(wpautop($checklist)); ?>
                </div>
            <?php else: ?>
                <p><?php _e('Checklist file not found.', 'wpseed'); ?></p>
            <?php endif; ?>
        </div>
        
        <style>
        .wpseed-dev-checklist-tab {
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
        <?php
    }
}
