<?php
/**
 * PHPUnit Bootstrap
 *
 * This file is a PHPUnit entry point and runs entirely outside of WordPress.
 * ABSPATH is never defined in this context, so the standard direct-access
 * guard is intentionally omitted — adding it would prevent the test suite
 * from loading.
 *
 * @package WPSeed/Tests
 * @version 1.2.0
 */

// Prefixed with wpseed_ to satisfy WordPress global variable naming standards.
$wpseed_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $wpseed_tests_dir ) {
    $wpseed_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $wpseed_tests_dir . '/includes/functions.php' ) ) {
    // Write to STDERR so PHPUnit surfaces the message — error_log() is not
    // appropriate here as this file runs outside WordPress in a CLI context.
    fwrite( STDERR, "Could not find {$wpseed_tests_dir}/includes/functions.php" . PHP_EOL );
    exit( 1 );
}

require_once $wpseed_tests_dir . '/includes/functions.php';

function wpseed_manually_load_plugin() {
    require dirname(dirname(__FILE__)) . '/wpseed.php';
}

tests_add_filter('muplugins_loaded', 'wpseed_manually_load_plugin');

require $wpseed_tests_dir . '/includes/bootstrap.php';
