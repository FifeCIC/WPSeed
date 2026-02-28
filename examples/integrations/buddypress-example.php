<?php
/**
 * BuddyPress Integration Example
 *
 * @package WPSeed/Examples/Integrations
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPSeed_BuddyPress_Integration
 */
class WPSeed_BuddyPress_Integration {

    public function __construct() {
        // Check if BuddyPress is active
        if ( ! function_exists( 'buddypress' ) ) {
            return;
        }

        add_action( 'bp_setup_nav', array( $this, 'add_profile_tab' ) );
        add_action( 'bp_after_profile_field_content', array( $this, 'add_custom_profile_field' ) );
        add_action( 'xprofile_updated_profile', array( $this, 'save_custom_profile_field' ), 10, 5 );
        add_filter( 'bp_get_activity_action', array( $this, 'custom_activity_action' ), 10, 2 );
    }

    /**
     * Add custom profile tab
     */
    public function add_profile_tab() {
        bp_core_new_nav_item( array(
            'name'                    => __( 'Custom Tab', 'wpseed' ),
            'slug'                    => 'custom-tab',
            'screen_function'         => array( $this, 'custom_tab_screen' ),
            'position'                => 50,
            'default_subnav_slug'     => 'custom-tab',
        ) );
    }

    /**
     * Custom tab screen
     */
    public function custom_tab_screen() {
        add_action( 'bp_template_content', array( $this, 'custom_tab_content' ) );
        bp_core_load_template( 'members/single/plugins' );
    }

    /**
     * Custom tab content
     */
    public function custom_tab_content() {
        echo '<div class="wpseed-custom-tab">';
        echo '<h3>' . esc_html__( 'Custom Content', 'wpseed' ) . '</h3>';
        echo '<p>' . esc_html__( 'Your custom content here.', 'wpseed' ) . '</p>';
        echo '</div>';
    }

    /**
     * Add custom profile field
     */
    public function add_custom_profile_field() {
        $user_id = bp_displayed_user_id();
        $value = get_user_meta( $user_id, 'wpseed_custom_field', true );
        ?>
        <div class="editfield">
            <label for="wpseed_custom_field"><?php esc_html_e( 'Custom Field', 'wpseed' ); ?></label>
            <input type="text" name="wpseed_custom_field" id="wpseed_custom_field" value="<?php echo esc_attr( $value ); ?>" />
        </div>
        <?php
    }

    /**
     * Save custom profile field
     */
    public function save_custom_profile_field( $user_id, $posted_field_ids, $errors, $old_values, $new_values ) {
        if ( isset( $_POST['wpseed_custom_field'], $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'bp_xprofile_edit' ) ) {
            update_user_meta( $user_id, 'wpseed_custom_field', sanitize_text_field( wp_unslash( $_POST['wpseed_custom_field'] ) ) );
        }
    }

    /**
     * Customize activity action
     */
    public function custom_activity_action( $action, $activity ) {
        if ( $activity->type === 'custom_type' ) {
            /* translators: %s: User display name */
            $action = sprintf( __( '%s did something custom', 'wpseed' ), bp_core_get_userlink( $activity->user_id ) );
        }
        return $action;
    }
}

// Initialize
new WPSeed_BuddyPress_Integration();
