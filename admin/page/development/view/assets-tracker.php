<?php
/**
 * Assets Tracker View
 * 
 * @package WPSeed
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpseed_assets;
$all_assets = $wpseed_assets->get_all_assets();
$missing_assets = $wpseed_assets->get_missing_assets();

$css_files = array();
foreach ($all_assets['css'] as $name => $asset) {
    $css_files[] = array(
        'name' => $name,
        'path' => $asset['path'],
        'purpose' => $asset['purpose'],
        'pages' => implode(', ', $asset['pages']),
        'exists' => $wpseed_assets->asset_exists('css', $name)
    );
}

$js_files = array();
foreach ($all_assets['js'] as $name => $asset) {
    $js_files[] = array(
        'name' => $name,
        'path' => $asset['path'],
        'purpose' => $asset['purpose'],
        'pages' => implode(', ', $asset['pages']),
        'exists' => $wpseed_assets->asset_exists('js', $name)
    );
}
?>

<div class="wpseed-assets-tracker">
    <?php if (!empty($missing_assets)): ?>
        <div class="notice notice-warning">
            <p><strong>Missing Assets:</strong> <?php echo count($missing_assets['css'] ?? array()) + count($missing_assets['js'] ?? array()); ?> files not found.</p>
        </div>
    <?php endif; ?>

    <h2>CSS Assets (<?php echo count($css_files); ?>)</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>File Path</th>
                <th>Purpose</th>
                <th>Pages</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($css_files as $file): ?>
                <tr>
                    <td><strong><?php echo esc_html($file['name']); ?></strong></td>
                    <td><code><?php echo esc_html($file['path']); ?></code></td>
                    <td><?php echo esc_html($file['purpose']); ?></td>
                    <td><small><?php echo esc_html($file['pages']); ?></small></td>
                    <td>
                        <?php if ($file['exists']): ?>
                            <span class="dashicons dashicons-yes-alt" style="color: green;"></span>
                        <?php else: ?>
                            <span class="dashicons dashicons-dismiss" style="color: red;"></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>JavaScript Assets (<?php echo count($js_files); ?>)</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>File Path</th>
                <th>Purpose</th>
                <th>Pages</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($js_files as $file): ?>
                <tr>
                    <td><strong><?php echo esc_html($file['name']); ?></strong></td>
                    <td><code><?php echo esc_html($file['path']); ?></code></td>
                    <td><?php echo esc_html($file['purpose']); ?></td>
                    <td><small><?php echo esc_html($file['pages']); ?></small></td>
                    <td>
                        <?php if ($file['exists']): ?>
                            <span class="dashicons dashicons-yes-alt" style="color: green;"></span>
                        <?php else: ?>
                            <span class="dashicons dashicons-dismiss" style="color: red;"></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
