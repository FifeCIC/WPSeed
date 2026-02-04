<?php
/**
 * WPSeed Script Assets Registry
 *
 * @package WPSeed/Assets
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

return array(
    'admin' => array(
        'admin-common' => array(
            'path' => 'js/admin-common.js',
            'purpose' => 'Common admin functionality',
            'pages' => array('all'),
            'dependencies' => array('jquery')
        ),
        'development-tabs' => array(
            'path' => 'js/development-tabs.js',
            'purpose' => 'Development page tab navigation',
            'pages' => array('wpseed_development'),
            'dependencies' => array('jquery')
        ),
    ),
    
    'libraries' => array(
        'select2' => array(
            'path' => 'js/select2/select2.js',
            'purpose' => 'Select2 dropdown enhancement',
            'pages' => array('all'),
            'dependencies' => array('jquery')
        ),
        'jquery-blockui' => array(
            'path' => 'js/jquery-blockui/jquery.blockUI.js',
            'purpose' => 'jQuery BlockUI for loading states',
            'pages' => array('all'),
            'dependencies' => array('jquery')
        ),
    ),
);
