<?php
/**
 * Admin View: Notice - Update
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div id="message" class="updated wpseed-message wpseed-connect">
    <p><strong><?php esc_html_e( 'WPSeed Data Update', 'wpseed' ); ?></strong> &#8211; <?php esc_html_e( 'We need to update your store\'s database to the latest version.', 'wpseed' ); ?></p>
    <p class="submit"><a href="<?php echo esc_url( add_query_arg( array( 'do_update_wpseed' => 'true', '_wpseed_update_nonce' => wp_create_nonce( 'wpseed_do_update' ) ), admin_url( 'admin.php?page=wpseed-settings' ) ) ); ?>" class="wpseed-update-now button-primary"><?php esc_html_e( 'Run the updater', 'wpseed' ); ?></a></p>
</div>
<script type="text/javascript">
    jQuery( '.wpseed-update-now' ).click( 'click', function() {
        return window.confirm( '<?php echo esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'wpseed' ) ); ?>' ); // jshint ignore:line
    });
</script>
