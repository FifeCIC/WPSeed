<?php
/**
 * WPSeed Admin - Debugging Class
 *
 * Including this class starts debugging. The level
 * and depth of debugging depends on configuration.
 *
 * @class    WPSeed_Debug
 * @author   Ryan Bayne
 * @category Admin
 * @package  WPSeed/Admin
 * @version  2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if( !class_exists( 'WPSeed_Debug' ) ) :

class WPSeed_Debug {

    /**
     * Activate database error display for the current request.
     *
     * ini_set() and error_reporting() have been removed: WordPress calls
     * wp_debug_mode() during bootstrap which already sets display_errors and
     * error_reporting correctly based on WP_DEBUG and WP_DEBUG_DISPLAY. Calling
     * them again here is redundant and triggers Squiz.PHP.DiscouragedFunctions.
     * The $wpdb error-display calls are the only behaviour this method needs to
     * add on top of what WordPress has already configured.
     *
     * @since   1.0.0
     * @version 2.0.0
     * @return  void
     */
    public function debugmode() {
        if ( wpseed_is_background_process() ) {
            return;
        }

        // Only expose database errors in debug mode — show_errors() and
        // print_error() can leak table names and query structure in production.
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }

        global $wpdb;

        // WordPress bootstrap (wp_debug_mode()) has already set display_errors
        // and error_reporting from WP_DEBUG / WP_DEBUG_DISPLAY, so no ini_set()
        // or error_reporting() call is needed here.
        $wpdb->show_errors();
        $wpdb->print_error();
    }
    
    /**
    * Dump $_POST
    */
    private function dump_post() {
        if( !current_user_can( 'activate_plugins') ) return;
        // var_dump() gated behind WP_DEBUG — must never run in production.
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) return;

        echo '<h1>$_POST</h1>';
        echo '<pre>';
        var_dump( $_POST );
        echo '</pre>';
    }   
      
    /**
    * Dump $_GET
    */
    private function dump_get() {
        if( !current_user_can( 'activate_plugins') ) return;
        // var_dump() gated behind WP_DEBUG — must never run in production.
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) return;

        echo '<h1>$_GET</h1>';
        echo '<pre>';
        var_dump( $_GET );
        echo '</pre>';
    }        
}

endif; 