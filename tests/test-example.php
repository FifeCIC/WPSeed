<?php
/**
 * Example Test Case
 *
 * @package WPSeed/Tests
 */

class WPSeed_Test_Example extends WP_UnitTestCase {
    
    public function test_plugin_activated() {
        $this->assertTrue(function_exists('WPSeed'));
    }
    
    public function test_version_constant() {
        $this->assertTrue(defined('WPSEED_VERSION'));
    }
}
