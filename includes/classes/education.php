<?php
/**
 * Education System
 * Built-in training and documentation for plugin users
 *
 * @package WPSeed/Education
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Education {
    
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'wpseed_lessons';
        
        add_action('admin_menu', array($this, 'add_menu'));
    }
    
    public function add_menu() {
        add_submenu_page(
            'wpseed-settings',
            'Learning Center',
            'Learning Center',
            'manage_options',
            'wpseed-learning',
            array($this, 'render_page')
        );
    }
    
    public function render_page() {
        $lessons = $this->get_lessons();
        include dirname(__FILE__) . '/../admin/views/education.php';
    }
    
    public function get_lessons() {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY lesson_order ASC");
    }
    
    public function get_lesson($id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id));
    }
    
    public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            content longtext NOT NULL,
            lesson_type varchar(50) DEFAULT 'tutorial',
            lesson_order int(11) DEFAULT 0,
            duration varchar(50) DEFAULT NULL,
            video_url varchar(500) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        $this->seed_default_lessons();
    }
    
    private function seed_default_lessons() {
        global $wpdb;
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
        if ($count > 0) return;
        
        $lessons = array(
            array(
                'title' => 'Getting Started with WPSeed',
                'slug' => 'getting-started',
                'content' => 'Welcome to WPSeed! This tutorial will guide you through the basics...',
                'lesson_type' => 'tutorial',
                'lesson_order' => 1,
                'duration' => '5 min'
            ),
            array(
                'title' => 'Creating Custom Post Types',
                'slug' => 'custom-post-types',
                'content' => 'Learn how to create and manage custom post types...',
                'lesson_type' => 'tutorial',
                'lesson_order' => 2,
                'duration' => '10 min'
            ),
            array(
                'title' => 'REST API Integration',
                'slug' => 'rest-api',
                'content' => 'Build custom REST API endpoints for your plugin...',
                'lesson_type' => 'tutorial',
                'lesson_order' => 3,
                'duration' => '15 min'
            )
        );
        
        foreach ($lessons as $lesson) {
            $wpdb->insert($this->table_name, $lesson);
        }
    }
    
    public function export_for_website() {
        $lessons = $this->get_lessons();
        return array(
            'version' => WPSEED_VERSION,
            'updated' => current_time('mysql'),
            'lessons' => $lessons
        );
    }
}

return new WPSeed_Education();
