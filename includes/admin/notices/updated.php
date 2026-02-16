<?php
/**
 * Admin View: Notice - Updated
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div id="message" class="updated wpseed-message wpseed-connect">
    <a class="wpseed-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpseed-hide-notice', 'update', remove_query_arg( 'do_update_wpseed' ) ), 'wpseed_hide_notices_nonce', '_wpseed_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'wpseed' ); ?></a>

    <p><?php esc_html_e( 'WPSeed data update complete. Thank you for updating to the latest version!', 'wpseed' ); ?></p>
</div>
