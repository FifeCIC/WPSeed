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
    'layouts' => array(
        'tabs' => array(
            'path' => 'css/layouts/tabs.css',
            'purpose' => 'Tab navigation',
            'pages' => array(),
            'dependencies' => array()
        ),
        'grids' => array(
            'path' => 'css/layouts/grids.css',
            'purpose' => 'CSS Grid layouts',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
    ),
    
    'base' => array(
        'variables' => array(
            'path' => 'css/base/variables.css',
            'purpose' => 'CSS custom properties',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'main' => array(
            'path' => 'css/admin.css',
            'purpose' => 'Main admin stylesheet',
            'pages' => array('all'),
            'dependencies' => array()
        ),
        'jquery-ui' => array(
            'path' => 'external',
            'purpose' => 'jQuery UI styles',
            'pages' => array('wpseed-jquery-ui'),
            'dependencies' => array(),
            'external' => array('wp-jquery-ui-dialog')
        ),
    ),
    
    'components' => array(
        'buttons' => array(
            'path' => 'css/components/buttons.css',
            'purpose' => 'Button styles',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'forms' => array(
            'path' => 'css/components/forms.css',
            'purpose' => 'Form elements',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'cards' => array(
            'path' => 'css/components/cards.css',
            'purpose' => 'Card UI elements',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'tables' => array(
            'path' => 'css/components/tables.css',
            'purpose' => 'Data tables',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'notices' => array(
            'path' => 'css/components/notices.css',
            'purpose' => 'Admin notices',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'badges' => array(
            'path' => 'css/components/badges.css',
            'purpose' => 'Status badges',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
    ),
    
    'ui-library' => array(
        'accordion' => array(
            'path' => 'css/components/accordion.css',
            'purpose' => 'Accordion components',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'animations' => array(
            'path' => 'css/components/animations.css',
            'purpose' => 'CSS animations',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'charts' => array(
            'path' => 'css/components/charts.css',
            'purpose' => 'Chart visualization',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'controls' => array(
            'path' => 'css/components/controls.css',
            'purpose' => 'Control components',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'data-analysis' => array(
            'path' => 'css/components/data-analysis.css',
            'purpose' => 'Data analysis components',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'filters' => array(
            'path' => 'css/components/filters.css',
            'purpose' => 'Filter components',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'modals' => array(
            'path' => 'css/components/modals.css',
            'purpose' => 'Modal dialogs',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'pagination' => array(
            'path' => 'css/components/pagination.css',
            'purpose' => 'Pagination controls',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'pointers' => array(
            'path' => 'css/components/pointers.css',
            'purpose' => 'WordPress pointers',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'progress' => array(
            'path' => 'css/components/progress.css',
            'purpose' => 'Progress indicators',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'status-indicators' => array(
            'path' => 'css/components/status-indicators.css',
            'purpose' => 'Status indicators',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'tooltips' => array(
            'path' => 'css/components/tooltips.css',
            'purpose' => 'Tooltips',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
        'ui-library' => array(
            'path' => 'css/pages/ui-library.css',
            'purpose' => 'UI library main styles',
            'pages' => array('wpseed_development'),
            'dependencies' => array()
        ),
    ),
);
