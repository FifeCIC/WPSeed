<?php
/**
 * WPSeed Documentation Viewer
 *
 * @package WPSeed/Admin/Views
 * @version 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WPSeed_Admin_Development_Docs Class
 */
class WPSeed_Admin_Development_Docs {
    
    /**
     * Output the documentation viewer
     */
    public static function output() {
        $docs_dir = WPSEED_PLUGIN_DIR_PATH . 'docs/';
        $current_doc = isset($_GET['doc']) ? sanitize_file_name(wp_unslash($_GET['doc'])) : 'GETTING-STARTED';
        
        ?>
        <div class="wpseed-docs-viewer">
            <div class="wpseed-docs-sidebar">
                <h3><?php esc_html_e('Documentation', 'wpseed'); ?></h3>
                <?php self::render_docs_menu($docs_dir, $current_doc); ?>
            </div>
            
            <div class="wpseed-docs-content">
                <?php self::render_doc_content($docs_dir, $current_doc); ?>
            </div>
        </div>
        
        <style>
            .wpseed-docs-viewer {
                display: flex;
                gap: 20px;
                margin-top: 20px;
            }
            .wpseed-docs-sidebar {
                width: 250px;
                background: #fff;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 3px;
            }
            .wpseed-docs-sidebar h3 {
                margin-top: 0;
                padding-bottom: 10px;
                border-bottom: 2px solid #0073aa;
            }
            .wpseed-docs-sidebar ul {
                list-style: none;
                margin: 0;
                padding: 0;
            }
            .wpseed-docs-sidebar li {
                margin: 0;
                padding: 0;
            }
            .wpseed-docs-sidebar a {
                display: block;
                padding: 8px 12px;
                text-decoration: none;
                color: #333;
                border-radius: 3px;
                transition: background 0.2s;
            }
            .wpseed-docs-sidebar a:hover {
                background: #f0f0f0;
            }
            .wpseed-docs-sidebar a.active {
                background: #0073aa;
                color: #fff;
                font-weight: 600;
            }
            .wpseed-docs-content {
                flex: 1;
                background: #fff;
                padding: 30px;
                border: 1px solid #ddd;
                border-radius: 3px;
                max-width: 900px;
            }
            .wpseed-docs-content h1 {
                margin-top: 0;
                padding-bottom: 15px;
                border-bottom: 2px solid #0073aa;
            }
            .wpseed-docs-content h2 {
                margin-top: 30px;
                color: #0073aa;
            }
            .wpseed-docs-content pre {
                background: #f5f5f5;
                padding: 15px;
                border-left: 3px solid #0073aa;
                overflow-x: auto;
            }
            .wpseed-docs-content code {
                background: #f5f5f5;
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 0.9em;
            }
            .wpseed-docs-content pre code {
                background: none;
                padding: 0;
            }
            .wpseed-docs-content table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            .wpseed-docs-content table th,
            .wpseed-docs-content table td {
                padding: 10px;
                border: 1px solid #ddd;
                text-align: left;
            }
            .wpseed-docs-content table th {
                background: #f5f5f5;
                font-weight: 600;
            }
            .wpseed-docs-content blockquote {
                border-left: 4px solid #0073aa;
                padding-left: 20px;
                margin-left: 0;
                color: #666;
                font-style: italic;
            }
        </style>
        <?php
    }
    
    /**
     * Render documentation menu
     */
    private static function render_docs_menu($docs_dir, $current_doc) {
        $docs = array(
            'GETTING-STARTED' => __('Getting Started', 'wpseed'),
            'REPEATER-FIELDS' => __('Repeater Fields', 'wpseed'),
            'REPEATER-QUICK-REFERENCE' => __('Repeater Quick Ref', 'wpseed'),
            'ADVANCED-FEATURES' => __('Advanced Features', 'wpseed'),
            'INTEGRATIONS' => __('Integrations', 'wpseed'),
            'DEVELOPER-CHECKLIST' => __('Developer Checklist', 'wpseed'),
            'ECOSYSTEM' => __('Ecosystem', 'wpseed'),
            'UNIFIED-FEATURE' => __('Unified Feature', 'wpseed'),
            'DOCUMENTATION-STANDARD' => __('Doc Standards', 'wpseed'),
        );
        
        echo '<ul>';
        foreach ($docs as $doc_file => $doc_title) {
            $file_path = $docs_dir . $doc_file . '.md';
            if (file_exists($file_path)) {
                $active_class = ($current_doc === $doc_file) ? 'active' : '';
                $url = add_query_arg(array('tab' => 'docs', 'doc' => $doc_file));
                printf(
                    '<li><a href="%s" class="%s">%s</a></li>',
                    esc_url($url),
                    esc_attr($active_class),
                    esc_html($doc_title)
                );
            }
        }
        echo '</ul>';
    }
    
    /**
     * Render documentation content
     */
    private static function render_doc_content($docs_dir, $current_doc) {
        $file_path = $docs_dir . $current_doc . '.md';
        
        if (!file_exists($file_path)) {
            echo '<p>' . esc_html__('Documentation file not found.', 'wpseed') . '</p>';
            return;
        }
        
        $content = file_get_contents($file_path);
        
        // Simple markdown to HTML conversion
        $html = self::markdown_to_html($content);
        
        echo wp_kses_post($html);
    }
    
    /**
     * Convert markdown to HTML (basic implementation)
     */
    private static function markdown_to_html($markdown) {
        // Headers
        $markdown = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $markdown);
        $markdown = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $markdown);
        $markdown = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $markdown);
        
        // Code blocks
        $markdown = preg_replace('/```(\w+)?\n(.*?)\n```/s', '<pre><code>$2</code></pre>', $markdown);
        
        // Inline code
        $markdown = preg_replace('/`([^`]+)`/', '<code>$1</code>', $markdown);
        
        // Bold
        $markdown = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $markdown);
        
        // Italic
        $markdown = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $markdown);
        
        // Links
        $markdown = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2">$1</a>', $markdown);
        
        // Lists
        $markdown = preg_replace('/^\- (.+)$/m', '<li>$1</li>', $markdown);
        $markdown = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $markdown);
        
        // Paragraphs
        $markdown = preg_replace('/\n\n/', '</p><p>', $markdown);
        $markdown = '<p>' . $markdown . '</p>';
        
        // Clean up empty paragraphs
        $markdown = preg_replace('/<p>\s*<\/p>/', '', $markdown);
        $markdown = preg_replace('/<p>(<h[1-6]>)/', '$1', $markdown);
        $markdown = preg_replace('/(<\/h[1-6]>)<\/p>/', '$1', $markdown);
        $markdown = preg_replace('/<p>(<ul>)/', '$1', $markdown);
        $markdown = preg_replace('/(<\/ul>)<\/p>/', '$1', $markdown);
        $markdown = preg_replace('/<p>(<pre>)/', '$1', $markdown);
        $markdown = preg_replace('/(<\/pre>)<\/p>/', '$1', $markdown);
        
        return $markdown;
    }
}
