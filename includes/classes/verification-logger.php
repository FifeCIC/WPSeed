<?php
/**
 * WPSeed Verification Logger
 * Specialised logging for verification process debugging.
 *
 * @package WPSeed
 * @version 2.0.0
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Verification_Logger {

    private static $instance = null;
    private $verification_steps = array();
    private $data_counts        = array();

    /**
     * Write a message to the WordPress debug log.
     *
     * Only writes when WP_DEBUG and WP_DEBUG_LOG are both enabled, keeping
     * output out of production environments. Uses file_put_contents() rather
     * than error_log() to satisfy WordPress.PHP.DevelopmentFunctions.error_log_error_log.
     *
     * @since   1.0.0
     * @version 2.0.0
     *
     * @param  string $message Message to write.
     * @return void
     */
    private function write_log( $message ) {
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }
        if ( ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) {
            return;
        }
        // Resolve the log path: WP_DEBUG_LOG may be a custom file path string
        // (WP 5.1+) or simply true, in which case the default location is used.
        $log_path = is_string( WP_DEBUG_LOG ) ? WP_DEBUG_LOG : WP_CONTENT_DIR . '/debug.log';
        // Append directly to the debug log — avoids error_log() entirely.
        file_put_contents( $log_path, gmdate( '[d-M-Y H:i:s e]' ) . ' ' . $message . PHP_EOL, FILE_APPEND | LOCK_EX );
    }

    /**
     * Return the singleton instance.
     *
     * @since  1.0.0
     * @return WPSeed_Verification_Logger
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Log a verification step with input/output counts and data-loss detection.
     *
     * No-ops when developer mode is inactive so there is zero overhead in
     * production. Data-loss entries are written to the debug log immediately
     * via write_log() rather than waiting for output_summary().
     *
     * @since  1.0.0
     * @version 2.0.0
     *
     * @param  string $step_name    Human-readable step identifier.
     * @param  int    $input_count  Number of items entering the step.
     * @param  int    $output_count Number of items leaving the step.
     * @param  array  $details      Optional structured context.
     * @return void
     */
    public function log_step( $step_name, $input_count, $output_count, $details = array() ) {
        if ( ! wpseed_is_developer_mode() ) {
            return;
        }

        $step_data = array(
            'step'         => $step_name,
            'input_count'  => $input_count,
            'output_count' => $output_count,
            'data_loss'    => $input_count > $output_count,
            'loss_amount'  => $input_count - $output_count,
            'timestamp'    => microtime( true ),
            'details'      => $details,
        );

        $this->verification_steps[]       = $step_data;
        $this->data_counts[ $step_name ]  = $output_count;

        // Delegate to the unified logger for cross-system trace correlation.
        if ( class_exists( 'WPSeed_Unified_Logger' ) ) {
            WPSeed_Unified_Logger::instance()->php_trace( $step_name, $input_count, $output_count, $details );
        }

        // Write data-loss entries immediately so they appear in the log even
        // if output_summary() is never called (e.g. on a fatal error).
        if ( $step_data['data_loss'] ) {
            $this->write_log( "WPSeed_Verification: DATA LOSS in {$step_name}: {$input_count} → {$output_count} (lost {$step_data['loss_amount']})" );
            if ( ! empty( $details ) ) {
                // wp_json_encode() used — handles encoding edge cases and avoids
                // the raw json_encode() call that can silently return false.
                $this->write_log( 'WPSeed_Verification: Details: ' . wp_json_encode( $details ) );
            }
        }

        $this->write_log( "WPSeed_Verification: {$step_name}: {$input_count} → {$output_count}" );
    }
    
    /**
     * Log a JavaScript data-transmission operation.
     *
     * @since  1.0.0
     * @version 2.0.0
     *
     * @param  string   $operation    Label describing the operation.
     * @param  int      $data_count   Number of items transmitted.
     * @param  int|null $payload_size Optional byte size of the payload.
     * @return void
     */
    public function log_js_transmission( $operation, $data_count, $payload_size = null ) {
        if ( ! wpseed_is_developer_mode() ) {
            return;
        }

        $details = array( 'operation' => $operation, 'count' => $data_count );
        if ( $payload_size !== null ) {
            $details['payload_size'] = $payload_size;
        }

        $this->write_log(
            "WPSeed_Verification: JS_TRACE - {$operation}: {$data_count} items" .
            ( $payload_size ? " ({$payload_size} bytes)" : '' )
        );

        if ( class_exists( 'WPSeed_Unified_Logger' ) ) {
            WPSeed_Unified_Logger::instance()->js_trace( $operation, $data_count, $details );
        }
    }
    
    /**
     * Log a single iteration of a file-processing loop.
     *
     * Delegates to WPSeed_Unified_Logger::loop_trace() which throttles output
     * to every 10th iteration to avoid flooding the debug log.
     *
     * @since  1.0.0
     * @version 2.0.0
     *
     * @param  string $loop_id      Unique identifier for the loop.
     * @param  string $file_path    Path of the file being processed.
     * @param  int    $result_count Number of results produced for this file.
     * @param  array  $details      Optional extra context.
     * @return void
     */
    public function log_file_loop( $loop_id, $file_path, $result_count, $details = array() ) {
        if ( ! wpseed_is_developer_mode() ) {
            return;
        }

        $loop_details = array_merge( $details, array(
            'file'         => basename( $file_path ),
            'result_count' => $result_count,
        ) );

        if ( class_exists( 'WPSeed_Unified_Logger' ) ) {
            WPSeed_Unified_Logger::instance()->loop_trace( $loop_id, $loop_details );
        }
    }
    
    /**
     * Log a hash comparison result for a single file.
     *
     * @since  1.0.0
     * @version 2.0.0
     *
     * @param  string $file_path Path of the file being compared.
     * @param  string $old_hash  Previously stored hash.
     * @param  string $new_hash  Freshly computed hash.
     * @param  bool   $changed   Whether the hashes differ.
     * @return void
     */
    public function log_hash_comparison( $file_path, $old_hash, $new_hash, $changed ) {
        if ( ! wpseed_is_developer_mode() ) {
            return;
        }

        $details = array(
            'file'     => basename( $file_path ),
            'old_hash' => $old_hash,
            'new_hash' => $new_hash,
            'changed'  => $changed,
        );

        if ( class_exists( 'WPSeed_Unified_Logger' ) ) {
            WPSeed_Unified_Logger::instance()->trace(
                'HASH_COMPARE',
                $changed ? 'File changed' : 'File unchanged',
                $details
            );
        }
    }
    
    /**
     * Log a filter or exclusion operation as a verification step.
     *
     * Delegates to log_step() so the filter appears in the verification summary
     * alongside other data-flow steps.
     *
     * @since  1.0.0
     * @version 2.0.0
     *
     * @param  string $filter_name  Label for the filter being applied.
     * @param  int    $input_count  Number of items before filtering.
     * @param  int    $output_count Number of items after filtering.
     * @param  string $criteria     Optional description of the filter criteria.
     * @return void
     */
    public function log_filter( $filter_name, $input_count, $output_count, $criteria = '' ) {
        if ( ! wpseed_is_developer_mode() ) {
            return;
        }

        $this->log_step( "FILTER_{$filter_name}", $input_count, $output_count, array(
            'filter'   => $filter_name,
            'criteria' => $criteria,
        ) );
    }
    
    /**
     * Log an AJAX payload extraction with expected vs actual count comparison.
     *
     * @since  1.0.0
     * @version 2.0.0
     *
     * @param  string     $payload_key    Key used to extract the payload.
     * @param  int        $expected_count Expected number of items.
     * @param  int        $actual_count   Actual number of items extracted.
     * @param  mixed|null $raw_data       Optional raw data for type/size inspection.
     * @return void
     */
    public function log_payload_extraction( $payload_key, $expected_count, $actual_count, $raw_data = null ) {
        if ( ! wpseed_is_developer_mode() ) {
            return;
        }

        $details = array(
            'payload_key' => $payload_key,
            'expected'    => $expected_count,
            'actual'      => $actual_count,
        );

        if ( $raw_data !== null ) {
            $details['raw_data_type'] = gettype( $raw_data );
            $details['raw_data_size'] = is_array( $raw_data ) ? count( $raw_data ) : strlen( $raw_data );
        }

        $this->write_log( "WPSeed_Verification: EXTRACT_PAYLOAD - {$payload_key}: expected {$expected_count}, got {$actual_count}" );

        if ( class_exists( 'WPSeed_Unified_Logger' ) ) {
            WPSeed_Unified_Logger::instance()->trace(
                'EXTRACT_PAYLOAD',
                "Payload {$payload_key}: {$expected_count} → {$actual_count}",
                $details
            );
        }
    }
    
    /**
     * Return a structured summary of all recorded verification steps.
     *
     * @since  1.0.0
     * @version 2.0.0
     *
     * @return array Summary array with keys: total_steps, data_loss_steps,
     *               total_data_loss, final_count, steps.
     */
    public function get_verification_summary() {
        $summary = array(
            'total_steps'      => count( $this->verification_steps ),
            'data_loss_steps'  => 0,
            'total_data_loss'  => 0,
            'final_count'      => 0,
            'steps'            => array(),
        );

        foreach ( $this->verification_steps as $step ) {
            if ( $step['data_loss'] ) {
                $summary['data_loss_steps']++;
                $summary['total_data_loss'] += $step['loss_amount'];
            }

            $summary['steps'][] = array(
                'step'   => $step['step'],
                'input'  => $step['input_count'],
                'output' => $step['output_count'],
                'loss'   => $step['loss_amount'],
            );
        }

        if ( ! empty( $this->verification_steps ) ) {
            $summary['final_count'] = end( $this->verification_steps )['output_count'];
        }

        return $summary;
    }
    
    /**
     * Write the verification summary to the debug log.
     *
     * No-ops when developer mode is inactive. All output routed through
     * write_log() — no error_log() used anywhere in this class.
     *
     * @since  1.0.0
     * @version 2.0.0
     * @return void
     */
    public function output_summary() {
        if ( ! wpseed_is_developer_mode() ) {
            return;
        }

        $summary = $this->get_verification_summary();

        $this->write_log( "WPSeed_Verification: SUMMARY - {$summary['total_steps']} steps, {$summary['data_loss_steps']} with data loss" );
        $this->write_log( "WPSeed_Verification: SUMMARY - Total data loss: {$summary['total_data_loss']}, Final count: {$summary['final_count']}" );

        foreach ( $summary['steps'] as $step ) {
            $loss_indicator = $step['loss'] > 0 ? " ❌ LOST {$step['loss']}" : ' ✅';
            $this->write_log( "WPSeed_Verification: STEP - {$step['step']}: {$step['input']} → {$step['output']}{$loss_indicator}" );
        }
    }
    
    /**
     * Clear all recorded verification steps and data counts.
     *
     * @since  1.0.0
     * @version 2.0.0
     * @return void
     */
    public function clear_logs() {
        $this->verification_steps = array();
        $this->data_counts        = array();
    }
}

// Global helper functions
function wpseed_verification_log() {
    return WPSeed_Verification_Logger::instance();
}

function wpseed_log_verification_step($step_name, $input_count, $output_count, $details = array()) {
    WPSeed_Verification_Logger::instance()->log_step($step_name, $input_count, $output_count, $details);
}

function wpseed_log_js_data($operation, $count, $size = null) {
    WPSeed_Verification_Logger::instance()->log_js_transmission($operation, $count, $size);
}