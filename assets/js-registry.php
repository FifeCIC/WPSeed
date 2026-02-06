<?php
/**
 * WPSeed JS Asset Registry
 *
 * @package WPSeed/Assets
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

return array(
    'admin' => array(
        'path' => 'js/admin.js',
        'purpose' => 'Main admin JavaScript',
        'pages' => array('all'),
        'dependencies' => array('jquery')
    ),
    'settings' => array(
        'path' => 'js/settings.js',
        'purpose' => 'Settings page functionality',
        'pages' => array('wpseed-settings'),
        'dependencies' => array('jquery')
    ),
    'select2' => array(
        'path' => 'js/select2/select2.min.js',
        'purpose' => 'Select2 dropdown enhancement library',
        'pages' => array('wpseed-settings'),
        'dependencies' => array('jquery')
    ),
    'development-tabs' => array(
        'path' => 'js/development-tabs.js',
        'purpose' => 'Development page tab navigation',
        'pages' => array('wpseed-development'),
        'dependencies' => array('jquery')
    ),
    'tooltips' => array(
        'path' => 'js/tooltips.js',
        'purpose' => 'Tooltip system functionality',
        'pages' => array('all'),
        'dependencies' => array('jquery')
    ),
    'notifications' => array(
        'path' => 'js/notifications.js',
        'purpose' => 'Notification system functionality',
        'pages' => array('all'),
        'dependencies' => array('jquery')
    ),
    'uninstall-feedback' => array(
        'path' => 'js/uninstall-feedback.js',
        'purpose' => 'Uninstall feedback modal functionality',
        'pages' => array('plugins'),
        'dependencies' => array('jquery')
    ),
    'ecosystem-installer' => array(
        'path' => 'js/ecosystem-installer.js',
        'purpose' => 'Ecosystem plugin installer AJAX',
        'pages' => array('wpseed-ecosystem-installer'),
        'dependencies' => array('jquery')
    ),
    'mermaid' => array(
        'path' => 'js/mermaid.min.js',
        'purpose' => 'Mermaid diagram rendering library',
        'pages' => array('wpseed-development'),
        'dependencies' => array()
    )
);
