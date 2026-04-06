<?php
/**
 * Example Background Process using Action Scheduler
 * 
 * Action Scheduler is the recommended way to handle background tasks in WPSeed.
 * It's battle-tested (used by WooCommerce) and provides better reliability.
 * 
 * @package WPSeed
 */

defined( 'ABSPATH' ) || die;

/**
 * Example 1: Simple One-Time Background Task
 */
function wpseed_example_schedule_single_task() {
    // Schedule a single action to run in 1 hour
    as_schedule_single_action( time() + HOUR_IN_SECONDS, 'wpseed_process_single_item', array( 'item_id' => 123 ) );
}

// Hook the callback
add_action( 'wpseed_process_single_item', 'wpseed_handle_single_item' );
function wpseed_handle_single_item( $item_id ) {
    // Your processing logic here
}

/**
 * Example 2: Process Multiple Items in Background
 */
function wpseed_example_process_batch() {
    $items = array( 1, 2, 3, 4, 5 );
    
    foreach ( $items as $item_id ) {
        // Schedule each item as async action (runs immediately in background)
        as_enqueue_async_action( 'wpseed_process_batch_item', array( 'item_id' => $item_id ) );
    }
}

add_action( 'wpseed_process_batch_item', 'wpseed_handle_batch_item' );
function wpseed_handle_batch_item( $item_id ) {
    // Your processing logic here
}

/**
 * Example 3: Recurring Background Task
 */
function wpseed_example_schedule_recurring() {
    // Schedule recurring action every hour
    if ( ! as_next_scheduled_action( 'wpseed_hourly_sync' ) ) {
        as_schedule_recurring_action( time(), HOUR_IN_SECONDS, 'wpseed_hourly_sync' );
    }
}

add_action( 'wpseed_hourly_sync', 'wpseed_handle_hourly_sync' );
function wpseed_handle_hourly_sync() {
    // Your sync logic here
}

/**
 * Example 4: Cancel Scheduled Actions
 */
function wpseed_example_cancel_actions() {
    // Cancel specific action
    as_unschedule_action( 'wpseed_process_single_item', array( 'item_id' => 123 ) );
    
    // Cancel all actions with a hook
    as_unschedule_all_actions( 'wpseed_hourly_sync' );
}

/**
 * Example 5: Check if Action is Scheduled
 */
function wpseed_example_check_scheduled() {
    if ( as_next_scheduled_action( 'wpseed_hourly_sync' ) ) {
        // Hourly sync is scheduled
    }
}

/**
 * LEGACY: Old WPSeed_Background_Process approach (DEPRECATED)
 * 
 * If you need the old approach, use the library versions:
 * - includes/libraries/library.async-request.php
 * - includes/libraries/library.background-process.php
 * 
 * However, Action Scheduler is strongly recommended.
 */
