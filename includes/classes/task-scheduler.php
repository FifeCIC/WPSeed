<?php
/**
 * WPSeed Task Scheduler
 * Wrapper for Action Scheduler library
 *
 * @package WPSeed
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Task_Scheduler {

    private static $instance = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', array($this, 'init'));
    }

    public function init() {
        // Action Scheduler is loaded in loader.php
        do_action('wpseed_task_scheduler_init');
    }

    /**
     * Schedule a single action
     */
    public function schedule_single($hook, $args = array(), $timestamp = null, $group = 'wpseed') {
        if (!function_exists('as_schedule_single_action')) {
            return false;
        }
        
        $timestamp = $timestamp ?: time();
        return as_schedule_single_action($timestamp, $hook, $args, $group);
    }

    /**
     * Schedule a recurring action
     */
    public function schedule_recurring($hook, $interval, $args = array(), $timestamp = null, $group = 'wpseed') {
        if (!function_exists('as_schedule_recurring_action')) {
            return false;
        }
        
        $timestamp = $timestamp ?: time();
        return as_schedule_recurring_action($timestamp, $interval, $hook, $args, $group);
    }

    /**
     * Schedule a cron action
     */
    public function schedule_cron($hook, $cron_expression, $args = array(), $timestamp = null, $group = 'wpseed') {
        if (!function_exists('as_schedule_cron_action')) {
            return false;
        }
        
        $timestamp = $timestamp ?: time();
        return as_schedule_cron_action($timestamp, $cron_expression, $hook, $args, $group);
    }

    /**
     * Unschedule an action
     */
    public function unschedule($hook, $args = array(), $group = 'wpseed') {
        if (!function_exists('as_unschedule_action')) {
            return false;
        }
        
        return as_unschedule_action($hook, $args, $group);
    }

    /**
     * Unschedule all actions for a hook
     */
    public function unschedule_all($hook, $args = array(), $group = 'wpseed') {
        if (!function_exists('as_unschedule_all_actions')) {
            return false;
        }
        
        return as_unschedule_all_actions($hook, $args, $group);
    }

    /**
     * Get next scheduled time for an action
     */
    public function next_scheduled($hook, $args = array(), $group = 'wpseed') {
        if (!function_exists('as_next_scheduled_action')) {
            return false;
        }
        
        return as_next_scheduled_action($hook, $args, $group);
    }

    /**
     * Check if action is scheduled
     */
    public function is_scheduled($hook, $args = array(), $group = 'wpseed') {
        return $this->next_scheduled($hook, $args, $group) !== false;
    }

    /**
     * Get scheduled actions
     */
    public function get_scheduled($args = array()) {
        if (!function_exists('as_get_scheduled_actions')) {
            return array();
        }
        
        $defaults = array(
            'group' => 'wpseed',
            'status' => ActionScheduler_Store::STATUS_PENDING,
            'per_page' => 25,
        );
        
        $args = wp_parse_args($args, $defaults);
        return as_get_scheduled_actions($args);
    }
}

// Initialize
WPSeed_Task_Scheduler::instance();
