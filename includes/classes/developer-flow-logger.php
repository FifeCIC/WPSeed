<?php
/**
 * Developer Flow Logger
 * 
 * Detailed decision tracking for debugging
 * 
 * @package WPSeed
 */

defined( 'ABSPATH' ) || die;

class WPSeed_Developer_Flow_Logger {
    
    private static $flow_steps = array();
    private static $current_context = '';
    
    public static function start_flow( $context, $description = '' ) {
        if ( ! wpseed_is_developer_mode() ) {
            return;
        }
        
        self::$current_context = $context;
        self::$flow_steps = array();
        self::log_step( 'START', $description ?: "Starting {$context}", array( 'context' => $context ) );
    }
    
    public static function log_decision( $decision, $result, $reason = '', $data = array() ) {
        if ( ! wpseed_is_developer_mode() ) {
            return;
        }
        
        self::log_step( 'DECISION', "{$decision} → {$result}", array_merge( $data, array(
            'decision' => $decision,
            'result' => $result,
            'reason' => $reason
        ) ) );
    }
    
    public static function log_action( $action, $details = '', $data = array() ) {
        if ( ! wpseed_is_developer_mode() ) {
            return;
        }
        
        self::log_step( 'ACTION', "{$action}: {$details}", array_merge( $data, array( 'action' => $action ) ) );
    }
    
    public static function log_cache( $operation, $key, $result, $data = array() ) {
        if ( ! wpseed_is_developer_mode() ) {
            return;
        }
        
        self::log_step( 'CACHE', "{$operation} {$key} → {$result}", array_merge( $data, array(
            'operation' => $operation,
            'cache_key' => $key,
            'result' => $result
        ) ) );
    }
    
    public static function end_flow( $final_result = '', $error = null ) {
        if ( ! wpseed_is_developer_mode() ) {
            return;
        }
        
        self::log_step( 'END', $final_result ?: 'Flow completed', array(
            'final_result' => $final_result,
            'error' => $error,
            'total_steps' => count( self::$flow_steps )
        ) );
        
        self::display_flow();
    }
    
    private static function log_step( $type, $message, $data = array() ) {
        self::$flow_steps[] = array(
            'type' => $type,
            'message' => $message,
            'data' => $data,
            'timestamp' => microtime( true ),
            'memory' => memory_get_usage()
        );
    }
    
    private static function display_flow() {
        if ( empty( self::$flow_steps ) ) {
            return;
        }
        
        $start_time = self::$flow_steps[0]['timestamp'];
        $duration = ( end( self::$flow_steps )['timestamp'] - $start_time ) * 1000;
        
        echo '<div class="wpseed-flow-log">';
        echo '<h3>🔍 Flow: ' . esc_html( self::$current_context ) . '</h3>';
        echo '<p>Steps: ' . count( self::$flow_steps ) . ' | Duration: ' . number_format( $duration, 2 ) . 'ms</p>';
        
        foreach ( self::$flow_steps as $index => $step ) {
            $elapsed = number_format( ( $step['timestamp'] - $start_time ) * 1000, 2 );
            echo '<div class="flow-step">';
            echo '<strong>' . esc_html( $index + 1 ) . '. ' . esc_html( $step['type'] ) . '</strong> (+' . esc_html( $elapsed ) . 'ms): ';
            echo esc_html( $step['message'] );
            if ( ! empty( $step['data'] ) ) {
                echo '<pre>' . esc_html( wp_json_encode( $step['data'] ) ) . '</pre>';
            }
            echo '</div>';
        }
        
        echo '</div>';
        echo '<style>
        .wpseed-flow-log { margin: 20px 0; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; font-family: monospace; font-size: 12px; }
        .flow-step { margin: 10px 0; padding: 8px; background: white; border-left: 4px solid #007bff; }
        .flow-step pre { margin: 5px 0; padding: 5px; background: #f8f9fa; }
        </style>';
        
        self::$flow_steps = array();
        self::$current_context = '';
    }
}
