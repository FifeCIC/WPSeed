<?php
/**
 * Plugin Name: Plugin Seed
 * Plugin URI: https://pluginseed.wordpress.com/
 * Github URI: https://github.com/RyanBayne/wpseed
 * Description: Grow a new plugin using a WordPress plugin Seed. 
 * Version: 1.0.0
 * Author: Ryan Bayne
 * Author URI: https://www.linkedin.com/in/ryanrbayne/
 * Requires at least: 4.4
 * Tested up to: 4.7
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /i18n/languages/
 * 
 * @package WPSeed
 * @category Core
 * @author Ryan Bayne
 * @license GNU General Public License, Version 3
 * @copyright 2016-2017 Ryan R. Bayne (SqueekyCoder@Gmail.com)
 */

 // Exit if accessed directly. 
if ( ! defined( 'ABSPATH' ) ) {exit;}
                 
if ( ! class_exists( 'WordPressPluginSeed' ) ) :

    if ( ! defined( 'WPSEED_VERSION' ) ) { define( 'WPSEED_VERSION', '0.0.1' ); }
    if ( ! defined( 'WPSEED_PLUGIN_FILE' ) ) { define( 'WPSEED_PLUGIN_FILE', __FILE__ ); }
    if ( ! defined( 'WPSEED_PLUGIN_BASENAME' ) ) { define( 'WPSEED_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); }
    if ( ! defined( 'WPSEED_PLUGIN_DIR_PATH' ) ) { define( 'WPSEED_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) ); }
    if ( ! defined( 'WPSEED_PLUGIN_DIR' ) ) { define( 'WPSEED_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ); }
    if ( ! defined( 'WPSEED_PLUGIN_URL' ) ) { define( 'WPSEED_PLUGIN_URL', plugin_dir_url( __FILE__ ) ); }
    
    // Load core functions with importance on making them available to third-party.                                            
    require_once( WPSEED_PLUGIN_DIR_PATH . 'install.php' );
    include_once( WPSEED_PLUGIN_DIR_PATH . 'functions.php' );
    include_once( WPSEED_PLUGIN_DIR_PATH . 'deprecated.php' );
    
    // Run the plugin
    include_once( WPSEED_PLUGIN_DIR_PATH . 'loader.php' );
                     
endif;