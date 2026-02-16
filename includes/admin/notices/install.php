<?php
/**
 * Admin View: Notice - Install with wizard start button.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div id="message" class="updated wpseed-message wpseed-connect">
    <p><strong><?php esc_html_e( 'Welcome to WordPress Seed', 'wpseed' ); ?></strong> &#8211; <?php esc_html_e( 'You&lsquo;re almost ready to begin using the plugin.', 'wpseed' ); ?></p>
    <p class="submit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=wpseed-setup' ) ); ?>" class="button-primary"><?php esc_html_e( 'Run the Setup Wizard', 'wpseed' ); ?></a> <a class="button-secondary skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpseed-hide-notice', 'install' ), 'wpseed_hide_notices_nonce', '_wpseed_notice_nonce' ) ); ?>"><?php esc_html_e( 'Skip Setup', 'wpseed' ); ?></a></p>
</div>
