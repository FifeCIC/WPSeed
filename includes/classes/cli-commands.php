<?php
/**
 * WP-CLI Commands
 *
 * @package WPSeed/CLI
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_CLI')) {
    return;
}

class WPSeed_CLI_Commands {
    
    /**
     * Get plugin info
     *
     * ## EXAMPLES
     *
     *     wp wpseed info
     *
     * @when after_wp_load
     */
    public function info($args, $assoc_args) {
        WP_CLI::line('WPSeed Plugin Information:');
        WP_CLI::line('Version: ' . WPSEED_VERSION);
        WP_CLI::line('Status: Active');
    }
    
    /**
     * Clear plugin cache
     *
     * ## EXAMPLES
     *
     *     wp wpseed cache clear
     *
     * @when after_wp_load
     */
    public function cache($args, $assoc_args) {
        if (isset($args[0]) && $args[0] === 'clear') {
            wp_cache_flush();
            WP_CLI::success('Cache cleared!');
        }
    }
}

WP_CLI::add_command('wpseed', 'WPSeed_CLI_Commands');
