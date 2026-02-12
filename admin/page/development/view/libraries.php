<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$monitor = WPSeed_Library_Update_Monitor::instance();
$libraries = $monitor->get_libraries();
$updates = array();

// Check for updates if requested
if ( isset( $_GET['check_updates'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'wpseed_check_library_updates' ) ) {
    $monitor->clear_cache();
    $updates = $monitor->check_all_updates();
    echo '<div class="notice notice-success is-dismissible"><p>Library updates checked successfully.</p></div>';
}

// Get cached updates
foreach ( $libraries as $slug => $library ) {
    $updates[ $slug ] = $monitor->check_updates( $slug );
}
?>

<div class="wrap wpseed-libraries-tab">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Bundled Libraries</h2>
        <a href="<?php echo esc_url( add_query_arg( array( 'check_updates' => '1', '_wpnonce' => wp_create_nonce( 'wpseed_check_library_updates' ) ) ) ); ?>" class="button button-primary">
            <span class="dashicons dashicons-update" style="margin-top: 3px;"></span> Check for Updates
        </a>
    </div>
    
    <p>WPSeed bundles the following third-party libraries. Updates are checked automatically every 6 hours.</p>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 25%;">Library</th>
                <th style="width: 15%;">Current Version</th>
                <th style="width: 15%;">Latest Version</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 15%;">Bundled Date</th>
                <th style="width: 15%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $libraries as $slug => $library ) : 
                $update = $updates[ $slug ];
                $is_outdated = $monitor->is_outdated( $slug );
                $needs_update = ! is_wp_error( $update ) && ! empty( $update['needs_update'] );
            ?>
            <tr>
                <td>
                    <strong><?php echo esc_html( $library['name'] ); ?></strong>
                    <br>
                    <small style="color: #666;"><?php echo esc_html( $library['description'] ); ?></small>
                    <br>
                    <small>
                        License: <code><?php echo esc_html( $library['license'] ); ?></code> | 
                        <a href="https://github.com/<?php echo esc_attr( $library['github_repo'] ); ?>" target="_blank">GitHub</a>
                    </small>
                </td>
                <td>
                    <code><?php echo esc_html( $library['version'] ); ?></code>
                </td>
                <td>
                    <?php if ( is_wp_error( $update ) ) : ?>
                        <span style="color: #999;">—</span>
                        <br><small style="color: #d63638;"><?php echo esc_html( $update->get_error_message() ); ?></small>
                    <?php else : ?>
                        <code><?php echo esc_html( $update['latest_version'] ); ?></code>
                        <?php if ( ! empty( $update['release_date'] ) ) : ?>
                            <br><small style="color: #666;"><?php echo esc_html( human_time_diff( strtotime( $update['release_date'] ) ) ); ?> ago</small>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ( $needs_update ) : ?>
                        <span class="dashicons dashicons-warning" style="color: #d63638;"></span>
                        <strong style="color: #d63638;">Update Available</strong>
                    <?php elseif ( $is_outdated ) : ?>
                        <span class="dashicons dashicons-clock" style="color: #dba617;"></span>
                        <strong style="color: #dba617;">Outdated (6+ months)</strong>
                    <?php elseif ( ! is_wp_error( $update ) ) : ?>
                        <span class="dashicons dashicons-yes-alt" style="color: #00a32a;"></span>
                        <strong style="color: #00a32a;">Up to Date</strong>
                    <?php else : ?>
                        <span class="dashicons dashicons-info" style="color: #999;"></span>
                        <span style="color: #999;">Unknown</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo esc_html( date( 'M j, Y', strtotime( $library['bundled_date'] ) ) ); ?>
                    <br>
                    <small style="color: #666;"><?php echo esc_html( human_time_diff( strtotime( $library['bundled_date'] ) ) ); ?> ago</small>
                </td>
                <td>
                    <?php if ( ! is_wp_error( $update ) && ! empty( $update['release_url'] ) ) : ?>
                        <a href="<?php echo esc_url( $update['release_url'] ); ?>" target="_blank" class="button button-small">View Release</a>
                    <?php endif; ?>
                    <?php if ( $needs_update ) : ?>
                        <br><br>
                        <button type="button" class="button button-small" disabled title="Manual update coming soon">Update</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if ( ! is_wp_error( $update ) && ! empty( $update['release_notes'] ) && $needs_update ) : ?>
            <tr class="wpseed-release-notes" style="display: none;" data-library="<?php echo esc_attr( $slug ); ?>">
                <td colspan="6" style="background: #f9f9f9; padding: 15px;">
                    <strong>Release Notes:</strong>
                    <div style="margin-top: 10px; max-height: 300px; overflow-y: auto; white-space: pre-wrap; font-family: monospace; font-size: 12px;">
                        <?php echo esc_html( wp_trim_words( $update['release_notes'], 100 ) ); ?>
                    </div>
                    <a href="<?php echo esc_url( $update['release_url'] ); ?>" target="_blank">Read full release notes →</a>
                </td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 30px; padding: 20px; background: #f0f6fc; border-left: 4px solid #0073aa;">
        <h3 style="margin-top: 0;">About Library Updates</h3>
        <ul style="margin-bottom: 0;">
            <li><strong>Automatic Checking:</strong> WPSeed checks for updates every 6 hours via GitHub API</li>
            <li><strong>Outdated Warning:</strong> Libraries bundled 6+ months ago show a warning</li>
            <li><strong>Manual Updates:</strong> Currently requires manual download and replacement (automated updates coming soon)</li>
            <li><strong>Backup First:</strong> Always backup before updating libraries</li>
            <li><strong>Testing:</strong> Test updates in development environment before production</li>
        </ul>
    </div>
    
    <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #dba617;">
        <h4 style="margin-top: 0;">⚠️ Important: Do Not Edit Libraries Directly</h4>
        <p style="margin-bottom: 0;">
            Never modify bundled library files directly. Instead, create wrapper classes or use WordPress hooks to extend functionality. 
            Report bugs to the library's GitHub repository. Direct modifications will be lost on updates.
        </p>
    </div>
</div>

<style>
.wpseed-libraries-tab .wp-list-table td {
    vertical-align: top;
    padding: 12px 10px;
}
.wpseed-libraries-tab .button-small {
    height: auto;
    padding: 4px 8px;
    font-size: 12px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Dismiss library notice
    $(document).on('click', '.wpseed-dismiss-library-notice', function(e) {
        e.preventDefault();
        $.post(ajaxurl, {
            action: 'wpseed_dismiss_library_notice'
        });
        $(this).closest('.notice').fadeOut();
    });
});
</script>
