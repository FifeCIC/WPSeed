<?php
/**
 * LearnDash Integration Example
 *
 * @package WPSeed/Examples/Integrations
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_LearnDash_Integration
 *
 * @since   1.1.0
 * @version 1.2.0
 */
class WPSeed_LearnDash_Integration {

    public function __construct() {
        // Check if LearnDash is active
        if ( ! defined( 'LEARNDASH_VERSION' ) ) {
            return;
        }

        add_action( 'learndash_course_completed', array( $this, 'after_course_completed' ) );
        add_action( 'learndash_lesson_completed', array( $this, 'after_lesson_completed' ) );
        add_action( 'learndash_quiz_completed', array( $this, 'after_quiz_completed' ), 10, 2 );
        add_filter( 'learndash_course_grid_custom_field', array( $this, 'add_custom_course_field' ), 10, 2 );
    }

    /**
     * Process after course completion
     */
    public function after_course_completed( $data ) {
        $user_id = $data['user']->ID;
        $course_id = $data['course']->ID;
        
        // Log completion
        $this->log_course_completion( $user_id, $course_id );
        
        // Award custom points
        $this->award_points( $user_id, 100 );
        
        // Custom processing
        do_action( 'wpseed_learndash_course_completed', $user_id, $course_id );
    }

    /**
     * Process after lesson completion
     */
    public function after_lesson_completed( $data ) {
        $user_id = $data['user']->ID;
        $lesson_id = $data['lesson']->ID;
        
        // Log completion
        $this->log_lesson_completion( $user_id, $lesson_id );
        
        // Custom processing
        do_action( 'wpseed_learndash_lesson_completed', $user_id, $lesson_id );
    }

    /**
     * Process after quiz completion
     */
    public function after_quiz_completed( $data, $user ) {
        $user_id = $user->ID;
        $quiz_id = $data['quiz'];
        $score = $data['percentage'];
        
        // Log quiz result
        $this->log_quiz_result( $user_id, $quiz_id, $score );
        
        // Award points based on score
        if ( $score >= 80 ) {
            $this->award_points( $user_id, 50 );
        }
        
        // Custom processing
        do_action( 'wpseed_learndash_quiz_completed', $user_id, $quiz_id, $score );
    }

    /**
     * Add custom field to course
     */
    public function add_custom_course_field( $output, $course_id ) {
        $custom_value = get_post_meta( $course_id, '_wpseed_custom_field', true );
        
        if ( $custom_value ) {
            $output .= '<div class="wpseed-course-custom">' . esc_html( $custom_value ) . '</div>';
        }
        
        return $output;
    }

    /**
     * Log course completion to the custom completions table.
     *
     * $wpdb->insert() is used because this writes to a custom plugin table
     * for which no WordPress API equivalent exists. The format array ensures
     * all values are parameterised. wp_cache_delete() invalidates any cached
     * reads for this user so subsequent queries reflect the new row.
     *
     * @since   1.1.0
     * @version 1.2.0
     *
     * @param int $user_id   ID of the user who completed the course.
     * @param int $course_id ID of the completed course.
     * @return void
     */
    private function log_course_completion( $user_id, $course_id ) {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'wpseed_ld_completions',
            array(
                'user_id'       => $user_id,
                'course_id'     => $course_id,
                'completed_at'  => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%s' )
        );

        // Invalidate cached completion data for this user after the insert.
        wp_cache_delete( 'wpseed_ld_completions_' . $user_id, 'wpseed_ld' );
    }

    /**
     * Log lesson completion
     */
    private function log_lesson_completion( $user_id, $lesson_id ) {
        update_user_meta( $user_id, '_wpseed_lesson_' . $lesson_id . '_completed', current_time( 'mysql' ) );
    }

    /**
     * Log quiz result to the custom quiz results table.
     *
     * $wpdb->insert() is used because this writes to a custom plugin table
     * for which no WordPress API equivalent exists. The format array ensures
     * all values are parameterised. wp_cache_delete() invalidates any cached
     * reads for this user so subsequent queries reflect the new row.
     *
     * @since   1.1.0
     * @version 1.2.0
     *
     * @param int   $user_id ID of the user who completed the quiz.
     * @param int   $quiz_id ID of the completed quiz.
     * @param float $score   Percentage score achieved.
     * @return void
     */
    private function log_quiz_result( $user_id, $quiz_id, $score ) {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'wpseed_ld_quiz_results',
            array(
                'user_id'      => $user_id,
                'quiz_id'      => $quiz_id,
                'score'        => $score,
                'completed_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%f', '%s' )
        );

        // Invalidate cached quiz result data for this user after the insert.
        wp_cache_delete( 'wpseed_ld_quiz_results_' . $user_id, 'wpseed_ld' );
    }

    /**
     * Award points to user
     */
    private function award_points( $user_id, $points ) {
        $current_points = get_user_meta( $user_id, '_wpseed_points', true );
        $new_points = intval( $current_points ) + $points;
        update_user_meta( $user_id, '_wpseed_points', $new_points );
    }
}

// Initialize
new WPSeed_LearnDash_Integration();
