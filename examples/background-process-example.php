<?php
/**
 * Example Background Process (LEGACY)
 * 
 * DEPRECATED: This example uses the old WPSeed_Background_Process class.
 * For new projects, use Action Scheduler instead (see action-scheduler-examples.php)
 * 
 * If you still need this approach, manually include:
 * require_once WPSEED_PLUGIN_DIR_PATH . 'includes/libraries/library.async-request.php';
 * require_once WPSEED_PLUGIN_DIR_PATH . 'includes/libraries/library.background-process.php';
 * 
 * @package WPSeed
 * @deprecated Use Action Scheduler instead
 */

defined( 'ABSPATH' ) || die;

// Load legacy library if needed
if ( ! class_exists( 'WPSeed_Background_Process' ) ) {
    require_once WPSEED_PLUGIN_DIR_PATH . 'includes/libraries/library.async-request.php';
    require_once WPSEED_PLUGIN_DIR_PATH . 'includes/libraries/library.background-process.php';
}

class WPSeed_Example_Background_Process extends WPSeed_Background_Process {

    protected $action = 'example_process';

    protected function task( $item ) {
        // Process the item
        // Return false to remove from queue
        // Return modified item to keep in queue for next pass
        
        // Process your item here
        // Example: update_post_meta( $item['post_id'], 'processed', true );
        
        // Example: Process and remove
        return false;
    }

    protected function complete() {
        parent::complete();
        // Actions after queue is complete
        // Example: do_action( 'wpseed_background_process_complete' );
    }
}

/**
 * Usage:
 * $process = new WPSeed_Example_Background_Process();
 * $process->push_to_queue( array( 'task' => 'data1' ) );
 * $process->push_to_queue( array( 'task' => 'data2' ) );
 * $process->save()->dispatch();
 */
