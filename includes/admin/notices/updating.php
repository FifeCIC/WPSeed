<?php
/**
 * Admin View: Notice - Updating
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div id="message" class="updated wpseed-message wpseed-connect">
    <p><strong><?php esc_html_e( 'WPSeed Data Update', 'wpseed' ); ?></strong> &#8211; <?php esc_html_e( 'Your database is being updated in the background.', 'wpseed' ); ?> <a href="<?php echo esc_url( add_query_arg( 'force_update_wpseed', 'true', admin_url( 'admin.php?page=wpseed-settings' ) ) ); ?>"><?php esc_html_e( 'Taking a while? Click here to run it now.', 'wpseed' ); ?></a></p>
</div>
