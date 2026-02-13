<?php
/**
 * WPSeed UI Library
 *
 * @package WPSeed/Admin/Views
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Admin_Development_UI_Library {
    
    public static function output() {
        require_once WPSEED_PLUGIN_DIR_PATH . 'admin/page/development/partials/ui-library/main-container.php';
    }
}

WPSeed_Admin_Development_UI_Library::output();
