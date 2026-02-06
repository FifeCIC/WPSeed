<?php
/**
 * Object Registry
 * 
 * Provides object access throughout WordPress without using globals
 * 
 * @package WPSeed
 */

defined( 'ABSPATH' ) || die;

class WPSeed_Object_Registry {

    private static $storage = array();

    public static function add( $id, $object ) {
        self::$storage[ $id ] = $object;
    }

    public static function get( $id ) {
        return isset( self::$storage[ $id ] ) ? self::$storage[ $id ] : null;
    }

    public static function update_var( $id, $var, $new ) {
        if ( isset( self::$storage[ $id ] ) ) {
            self::$storage[ $id ]->$var = $new;
        }
    }

    public static function remove( $id ) {
        unset( self::$storage[ $id ] );
    }

    public static function exists( $id ) {
        return isset( self::$storage[ $id ] );
    }
}
