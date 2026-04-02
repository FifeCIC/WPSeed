<?php
/**
 * Action Scheduler wrapper — simplified API for background job scheduling.
 *
 * ROLE: utility
 *
 * Single responsibility: Provide a clean API over the Action Scheduler library
 * for scheduling, cancelling, and querying background jobs. All methods are
 * no-ops if Action Scheduler is not loaded.
 *
 * DEPENDS ON:
 *   - Action Scheduler library (includes/libraries/action-scheduler/)
 *   - Action Scheduler global functions: as_schedule_single_action, etc.
 *
 * CONSUMED BY:
 *   - Any class that needs to schedule background work
 *   - Hook: init (fires wpseed_task_scheduler_init action)
 *
 * DATA FLOW:
 *   Input  → schedule/cancel calls from plugin code
 *   Output → Action Scheduler queue entries in the wp_actionscheduler_* tables
 *
 * @package  WPSeed\Core
 * @since    1.0.0
 */

namespace WPSeed\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wrapper around Action Scheduler for background job management.
 *
 * Single responsibility: Job scheduling API. Does NOT implement the jobs
 * themselves — those are registered by domain-specific classes.
 *
 * @since 1.0.0
 */
class Task_Scheduler {

	/** @var Task_Scheduler|null */
	private static $instance = null;

	/**
	 * @since  1.0.0
	 * @return Task_Scheduler
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Fire the task scheduler init action.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function init() {
		do_action( 'wpseed_task_scheduler_init' );
	}

	/**
	 * Schedule a single (one-time) action.
	 *
	 * @since  1.0.0
	 * @param  string   $hook      Action hook name.
	 * @param  array    $args      Arguments to pass to the hook.
	 * @param  int|null $timestamp Unix timestamp. Null = now.
	 * @param  string   $group     Action group name.
	 * @return int|false Action ID or false if Action Scheduler unavailable.
	 */
	public function schedule_single( $hook, $args = array(), $timestamp = null, $group = 'wpseed' ) {
		if ( ! function_exists( 'as_schedule_single_action' ) ) {
			return false;
		}
		return as_schedule_single_action( $timestamp ?? time(), $hook, $args, $group );
	}

	/**
	 * Schedule a recurring action.
	 *
	 * @since  1.0.0
	 * @param  string   $hook      Action hook name.
	 * @param  int      $interval  Seconds between recurrences.
	 * @param  array    $args      Arguments to pass to the hook.
	 * @param  int|null $timestamp First run timestamp. Null = now.
	 * @param  string   $group     Action group name.
	 * @return int|false Action ID or false if Action Scheduler unavailable.
	 */
	public function schedule_recurring( $hook, $interval, $args = array(), $timestamp = null, $group = 'wpseed' ) {
		if ( ! function_exists( 'as_schedule_recurring_action' ) ) {
			return false;
		}
		return as_schedule_recurring_action( $timestamp ?? time(), $interval, $hook, $args, $group );
	}

	/**
	 * Schedule a cron action.
	 *
	 * @since  1.0.0
	 * @param  string   $hook            Action hook name.
	 * @param  string   $cron_expression Cron schedule expression.
	 * @param  array    $args            Arguments to pass to the hook.
	 * @param  int|null $timestamp       First run timestamp. Null = now.
	 * @param  string   $group           Action group name.
	 * @return int|false Action ID or false if Action Scheduler unavailable.
	 */
	public function schedule_cron( $hook, $cron_expression, $args = array(), $timestamp = null, $group = 'wpseed' ) {
		if ( ! function_exists( 'as_schedule_cron_action' ) ) {
			return false;
		}
		return as_schedule_cron_action( $timestamp ?? time(), $cron_expression, $hook, $args, $group );
	}

	/**
	 * Unschedule a specific action.
	 *
	 * @since  1.0.0
	 * @param  string $hook  Action hook name.
	 * @param  array  $args  Arguments that were passed when scheduling.
	 * @param  string $group Action group name.
	 * @return bool|null
	 */
	public function unschedule( $hook, $args = array(), $group = 'wpseed' ) {
		if ( ! function_exists( 'as_unschedule_action' ) ) {
			return false;
		}
		return as_unschedule_action( $hook, $args, $group );
	}

	/**
	 * Unschedule all actions for a hook.
	 *
	 * @since  1.0.0
	 * @param  string $hook  Action hook name.
	 * @param  array  $args  Arguments that were passed when scheduling.
	 * @param  string $group Action group name.
	 * @return void
	 */
	public function unschedule_all( $hook, $args = array(), $group = 'wpseed' ) {
		if ( ! function_exists( 'as_unschedule_all_actions' ) ) {
			return;
		}
		as_unschedule_all_actions( $hook, $args, $group );
	}

	/**
	 * Get the next scheduled timestamp for an action.
	 *
	 * @since  1.0.0
	 * @param  string $hook  Action hook name.
	 * @param  array  $args  Arguments that were passed when scheduling.
	 * @param  string $group Action group name.
	 * @return int|false Timestamp or false.
	 */
	public function next_scheduled( $hook, $args = array(), $group = 'wpseed' ) {
		if ( ! function_exists( 'as_next_scheduled_action' ) ) {
			return false;
		}
		return as_next_scheduled_action( $hook, $args, $group );
	}

	/**
	 * Check if an action is currently scheduled.
	 *
	 * @since  1.0.0
	 * @param  string $hook  Action hook name.
	 * @param  array  $args  Arguments that were passed when scheduling.
	 * @param  string $group Action group name.
	 * @return bool
	 */
	public function is_scheduled( $hook, $args = array(), $group = 'wpseed' ) {
		return $this->next_scheduled( $hook, $args, $group ) !== false;
	}

	/**
	 * Get scheduled actions matching the given criteria.
	 *
	 * @since  1.0.0
	 * @param  array $args Query arguments for as_get_scheduled_actions().
	 * @return array
	 */
	public function get_scheduled( $args = array() ) {
		if ( ! function_exists( 'as_get_scheduled_actions' ) ) {
			return array();
		}

		$defaults = array(
			'group'    => 'wpseed',
			'status'   => \ActionScheduler_Store::STATUS_PENDING,
			'per_page' => 25,
		);

		return as_get_scheduled_actions( wp_parse_args( $args, $defaults ) );
	}
}
