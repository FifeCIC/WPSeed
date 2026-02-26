<?php
/**
 * WPSeed Task Scheduler Examples
 * 
 * This file demonstrates how to use the WPSeed_Task_Scheduler wrapper
 * for Action Scheduler library.
 *
 * @package WPSeed/Examples
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Example 1: Schedule a single task
 */
function wpseed_example_schedule_single_task() {
    $scheduler = WPSeed_Task_Scheduler::instance();
    
    // Schedule task to run in 1 hour
    $scheduler->schedule_single(
        'wpseed_process_data',           // Hook name
        array('user_id' => 123),         // Arguments
        time() + HOUR_IN_SECONDS,        // When to run
        'wpseed'                         // Group name
    );
}

/**
 * Example 2: Schedule a recurring task
 */
function wpseed_example_schedule_recurring_task() {
    $scheduler = WPSeed_Task_Scheduler::instance();
    
    // Run every hour
    $scheduler->schedule_recurring(
        'wpseed_hourly_cleanup',
        HOUR_IN_SECONDS,                 // Interval
        array(),                         // Arguments
        time(),                          // Start time
        'wpseed'
    );
}

/**
 * Example 3: Schedule a cron-style task
 */
function wpseed_example_schedule_cron_task() {
    $scheduler = WPSeed_Task_Scheduler::instance();
    
    // Run every day at 3am
    $scheduler->schedule_cron(
        'wpseed_daily_report',
        '0 3 * * *',                     // Cron expression
        array(),
        time(),
        'wpseed'
    );
}

/**
 * Example 4: Check if task is scheduled
 */
function wpseed_example_check_scheduled() {
    $scheduler = WPSeed_Task_Scheduler::instance();
    
    if ($scheduler->is_scheduled('wpseed_process_data', array('user_id' => 123))) {
        // Task is already scheduled
        return;
    }
    
    // Schedule it
    $scheduler->schedule_single('wpseed_process_data', array('user_id' => 123));
}

/**
 * Example 5: Unschedule a task
 */
function wpseed_example_unschedule_task() {
    $scheduler = WPSeed_Task_Scheduler::instance();
    
    // Unschedule specific task
    $scheduler->unschedule('wpseed_process_data', array('user_id' => 123));
    
    // Or unschedule all tasks for a hook
    $scheduler->unschedule_all('wpseed_hourly_cleanup');
}

/**
 * Example 6: Process the scheduled task
 */
add_action('wpseed_process_data', 'wpseed_handle_process_data', 10, 1);
function wpseed_handle_process_data($user_id) {
    // Your task logic here
    $user = get_userdata($user_id);
    
    if ($user) {
        // Process user data
        update_user_meta($user_id, 'last_processed', time());
    }
}

/**
 * Example 7: Batch processing with Action Scheduler
 */
function wpseed_example_batch_process() {
    $scheduler = WPSeed_Task_Scheduler::instance();
    
    // Get all users
    $users = get_users(array('number' => 100));
    
    // Schedule a task for each user (staggered by 1 minute)
    $delay = 0;
    foreach ($users as $user) {
        $scheduler->schedule_single(
            'wpseed_process_user',
            array('user_id' => $user->ID),
            time() + $delay,
            'wpseed_batch'
        );
        $delay += 60; // 1 minute delay between each
    }
}

/**
 * Example 8: Get scheduled actions
 */
function wpseed_example_get_scheduled() {
    $scheduler = WPSeed_Task_Scheduler::instance();
    
    $actions = $scheduler->get_scheduled(array(
        'group' => 'wpseed',
        'per_page' => 50,
    ));
    
    foreach ($actions as $action) {
        echo 'Hook: ' . esc_html($action->get_hook()) . '<br>';
        echo 'Scheduled: ' . esc_html($action->get_schedule()->get_date()->format('Y-m-d H:i:s')) . '<br>';
    }
}

/**
 * Example 9: Migration from WPSeed_Background_Process
 * 
 * OLD WAY (WPSeed_Background_Process):
 * 
 * class My_Process extends WPSeed_Background_Process {
 *     protected $action = 'my_process';
 *     protected function task($item) {
 *         // Process item
 *         return false;
 *     }
 * }
 * 
 * $process = new My_Process();
 * $process->push_to_queue(array('id' => 1));
 * $process->save()->dispatch();
 * 
 * 
 * NEW WAY (Action Scheduler):
 */
function wpseed_example_migration() {
    $scheduler = WPSeed_Task_Scheduler::instance();
    
    // Schedule the task
    $scheduler->schedule_single(
        'my_process_task',
        array('id' => 1),
        time(),
        'my_process'
    );
}

// Handle the task
add_action('my_process_task', 'wpseed_handle_my_process', 10, 1);
function wpseed_handle_my_process($id) {
    // Your processing logic here
    // This replaces the task() method from WPSeed_Background_Process
}
