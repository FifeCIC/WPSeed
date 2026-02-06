<?php
/**
 * Example Background Process
 * 
 * Demonstrates how to use the background processing system
 * 
 * Usage:
 * $process = new WPSeed_Example_Background_Process();
 * $process->push_to_queue( array( 'task' => 'data1' ) );
 * $process->push_to_queue( array( 'task' => 'data2' ) );
 * $process->save()->dispatch();
 * 
 * @package WPSeed
 */

defined( 'ABSPATH' ) || die;

class WPSeed_Example_Background_Process extends WPSeed_Background_Process {

    protected $action = 'example_process';

    protected function task( $item ) {
        // Process the item
        // Return false to remove from queue
        // Return modified item to keep in queue for next pass
        
        error_log( 'Processing: ' . print_r( $item, true ) );
        
        // Example: Process and remove
        return false;
    }

    protected function complete() {
        parent::complete();
        // Actions after queue is complete
        error_log( 'Background process complete' );
    }
}
