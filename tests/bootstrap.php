<?php
/**
 * PHPUnit Bootstrap
 *
 * @package WPSeed/Tests
 */

// Define test environment constant before loading anything
define( 'WP_TESTS_DIR', getenv('WP_TESTS_DIR') ?: rtrim(sys_get_temp_dir(), '/\\') . '/wordpress-tests-lib' );

$wpseed_tests_dir = WP_TESTS_DIR;

if (!file_exists($wpseed_tests_dir . '/includes/functions.php')) {
    echo "Could not find {$wpseed_tests_dir}/includes/functions.php\n";
    exit(1);
}

require_once $wpseed_tests_dir . '/includes/functions.php';

function wpseed_manually_load_plugin() {
    require dirname(dirname(__FILE__)) . '/wpseed.php';
}

tests_add_filter('muplugins_loaded', 'wpseed_manually_load_plugin');

require $wpseed_tests_dir . '/includes/bootstrap.php';
