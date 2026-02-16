<?php
/**
 * GitHub Sync Admin View
 *
 * @package WPSeed/Admin/Views
 */

if (!defined('ABSPATH')) exit;
?>

<div class="wrap wpseed-github-sync">
    <h1><?php esc_html_e('GitHub Documentation Sync', 'wpseed'); ?></h1>
    
    <?php if (empty($this->github_token) || empty($this->github_repo)): ?>
        <div class="notice notice-warning">
            <p><strong><?php esc_html_e('Configuration Required', 'wpseed'); ?></strong></p>
            <p><?php esc_html_e('Set GitHub token and repo in Settings to enable sync.', 'wpseed'); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="wpseed-sync-status">
        <h2><?php esc_html_e('Documentation Files', 'wpseed'); ?></h2>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('File', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Status', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Last Modified', 'wpseed'); ?></th>
                    <th><?php esc_html_e('Actions', 'wpseed'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($docs as $doc): ?>
                    <?php $file_status = $status[$doc['name']]; ?>
                    <tr>
                        <td><strong><?php echo esc_html($doc['name']); ?></strong></td>
                        <td>
                            <?php if ($file_status['synced']): ?>
                                <span class="dashicons dashicons-yes-alt" style="color: green;"></span>
                                <?php esc_html_e('Synced', 'wpseed'); ?>
                            <?php else: ?>
                                <span class="dashicons dashicons-warning" style="color: orange;"></span>
                                <?php esc_html_e('Needs Sync', 'wpseed'); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($file_status['local_modified']); ?></td>
                        <td>
                            <button class="button button-primary sync-file" 
                                    data-file="<?php echo esc_attr($doc['name']); ?>"
                                    data-path="<?php echo esc_attr($doc['path']); ?>">
                                <?php esc_html_e('Sync Now', 'wpseed'); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p class="submit">
            <button class="button button-primary button-large" id="sync-all">
                <?php esc_html_e('Sync All Files', 'wpseed'); ?>
            </button>
        </p>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.sync-file').on('click', function() {
        var btn = $(this);
        var file = btn.data('file');
        var path = btn.data('path');
        
        btn.prop('disabled', true).text('Syncing...');
        
        $.post(ajaxurl, {
            action: 'wpseed_sync_github_file',
            file: file,
            path: path,
            nonce: '<?php echo wp_create_nonce('wpseed_github_sync'); ?>'
        }, function(response) {
            if (response.success) {
                btn.text('Synced!').css('background', 'green');
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                alert('Sync failed: ' + response.data);
                btn.prop('disabled', false).text('Sync Now');
            }
        });
    });
    
    $('#sync-all').on('click', function() {
        var btn = $(this);
        btn.prop('disabled', true).text('Syncing All...');
        
        $.post(ajaxurl, {
            action: 'wpseed_sync_github_all',
            nonce: '<?php echo wp_create_nonce('wpseed_github_sync'); ?>'
        }, function(response) {
            if (response.success) {
                alert('All files synced!');
                location.reload();
            } else {
                alert('Sync failed: ' + response.data);
                btn.prop('disabled', false).text('Sync All Files');
            }
        });
    });
});
</script>

<style>
.wpseed-sync-status {
    background: #fff;
    padding: 20px;
    margin-top: 20px;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.wpseed-sync-status h2 {
    margin-top: 0;
}
</style>
