<?php
/**
 * WPSeed Style Assets Registry
 *
 * @package WPSeed/Assets
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

return array(
    'components' => array(
        'buttons' => array(
            'path' => 'css/components/buttons.css',
            'purpose' => 'Button styles',
            'pages' => array('all'),
            'dependencies' => array()
        ),
        'forms' => array(
            'path' => 'css/components/forms.css',
            'purpose' => 'Form elements and inputs',
            'pages' => array('all'),
            'dependencies' => array()
        ),
        'tables' => array(
            'path' => 'css/components/tables.css',
            'purpose' => 'Data tables',
            'pages' => array('all'),
            'dependencies' => array()
        ),
        'notices' => array(
            'path' => 'css/components/notices.css',
            'purpose' => 'Admin notices',
            'pages' => array('all'),
            'dependencies' => array()
        ),
    ),
    
    'layouts' => array(
        'admin' => array(
            'path' => 'css/layouts/admin.css',
            'purpose' => 'Admin layout adaptations',
            'pages' => array('all'),
            'dependencies' => array()
        ),
        'tabs' => array(
            'path' => 'css/layouts/tabs.css',
            'purpose' => 'Tab navigation',
            'pages' => array('all'),
            'dependencies' => array()
        ),
    ),
    
    'base' => array(
        'variables' => array(
            'path' => 'css/base/variables.css',
            'purpose' => 'CSS custom properties',
            'pages' => array('all'),
            'dependencies' => array()
        ),
        'main' => array(
            'path' => 'css/admin.css',
            'purpose' => 'Main admin stylesheet',
            'pages' => array('all'),
            'dependencies' => array('variables')
        ),
    ),
);
