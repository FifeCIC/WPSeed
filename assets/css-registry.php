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
    ),
    'settings-import-export' => array(
        'path' => 'css/settings-import-export.css',
        'purpose' => 'Settings import/export interface',
        'pages' => array('wpseed-settings'),
        'dependencies' => array()
    ),
    'settings-repeater' => array(
        'path' => 'css/settings-repeater.css',
        'purpose' => 'Repeater field styles',
        'pages' => array('wpseed-settings'),
        'dependencies' => array()
    ),
    'license-manager' => array(
        'path' => 'css/license-manager.css',
        'purpose' => 'License management interface',
        'pages' => array('wpseed-settings'),
        'dependencies' => array()
    ),
    'wpseed-accordion-table' => array(
        'path' => 'css/accordion-table.css',
        'purpose' => 'Accordion table with sidebar layout',
        'pages' => array('wpseed-examples'),
        'dependencies' => array()
    ),
    'wpseed-credits' => array(
        'path' => 'css/credits.css',
        'purpose' => 'Credits and contributors page',
        'pages' => array('wpseed-development'),
        'dependencies' => array('wpseed-accordion-table')
    ),
    'wpseed-notification-center' => array(
        'path' => 'css/notification-center.css',
        'purpose' => 'Notification center page',
        'pages' => array('wpseed-notifications'),
        'dependencies' => array()
    )
);
