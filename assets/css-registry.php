<?php
/**
 * WPSeed CSS Asset Registry
 *
 * @package WPSeed/Assets
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

return array(
    'admin' => array(
        'path' => 'css/admin.css',
        'purpose' => 'Main admin styles',
        'pages' => array('all'),
        'dependencies' => array()
    ),
    'settings' => array(
        'path' => 'css/settings.css',
        'purpose' => 'Settings page styles',
        'pages' => array('wpseed-settings'),
        'dependencies' => array()
    ),
    'development' => array(
        'path' => 'css/development.css',
        'purpose' => 'Development page styles',
        'pages' => array('wpseed-development'),
        'dependencies' => array()
    ),
    'tooltips' => array(
        'path' => 'css/tooltips.css',
        'purpose' => 'Tooltip system styles',
        'pages' => array('all'),
        'dependencies' => array()
    ),
    'notifications' => array(
        'path' => 'css/notifications.css',
        'purpose' => 'Notification system styles',
        'pages' => array('all'),
        'dependencies' => array()
    ),
    'uninstall-feedback' => array(
        'path' => 'css/uninstall-feedback.css',
        'purpose' => 'Uninstall feedback modal styles',
        'pages' => array('plugins'),
        'dependencies' => array()
    ),
    'ecosystem-installer' => array(
        'path' => 'css/ecosystem-installer.css',
        'purpose' => 'Ecosystem plugin installer grid',
        'pages' => array('wpseed-ecosystem-installer'),
        'dependencies' => array()
    )
);
