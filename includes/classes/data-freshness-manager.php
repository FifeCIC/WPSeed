<?php
/**
 * Data Freshness Manager
 * 
 * Validates cache freshness and triggers updates when needed
 * 
 * @package WPSeed
 */

defined( 'ABSPATH' ) || die;

class WPSeed_Data_Freshness_Manager {

    private static $freshness_requirements = array(
        'default' => 3600,      // 1 hour
        'realtime' => 60,       // 1 minute
        'hourly' => 3600,       // 1 hour
        'daily' => 86400,       // 24 hours
    );

    public static function validate_freshness( $cache_key, $use_case = 'default' ) {
        $max_age = self::get_max_age( $use_case );
        $cached_data = get_transient( $cache_key );
        
        if ( false === $cached_data ) {
            return array(
                'status' => 'missing',
                'age' => null,
                'max_age' => $max_age,
                'needs_update' => true
            );
        }

        $age = isset( $cached_data['timestamp'] ) ? time() - $cached_data['timestamp'] : 0;
        $is_fresh = $age <= $max_age;

        return array(
            'status' => $is_fresh ? 'fresh' : 'stale',
            'age' => $age,
            'max_age' => $max_age,
            'needs_update' => ! $is_fresh
        );
    }

    public static function get_max_age( $use_case ) {
        return isset( self::$freshness_requirements[ $use_case ] ) 
            ? self::$freshness_requirements[ $use_case ] 
            : self::$freshness_requirements['default'];
    }

    public static function set_fresh_data( $cache_key, $data, $expiration = 3600 ) {
        $cached_data = array(
            'data' => $data,
            'timestamp' => time()
        );
        set_transient( $cache_key, $cached_data, $expiration );
    }

    public static function get_fresh_data( $cache_key ) {
        $cached_data = get_transient( $cache_key );
        return $cached_data ? $cached_data['data'] : null;
    }

    public static function ensure_freshness( $cache_key, $use_case, $callback ) {
        $validation = self::validate_freshness( $cache_key, $use_case );
        
        if ( $validation['needs_update'] ) {
            $data = call_user_func( $callback );
            self::set_fresh_data( $cache_key, $data, $validation['max_age'] );
            return $data;
        }

        return self::get_fresh_data( $cache_key );
    }
}
