<?php
/**
 * WPSeed Development - Database Diagnosis Tab
 *
 * @package WPSeed/Admin/Views
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Admin_Development_Database {
    public static function output() {
        global $wpdb;
        ?>
        <div class="wpseed-dev-section">
            <h2><?php esc_html_e('Database Information', 'wpseed'); ?></h2>
            
            <table class="widefat">
                <tbody>
                    <tr>
                        <th><?php esc_html_e('Database Name', 'wpseed'); ?></th>
                        <td><code><?php echo esc_html(DB_NAME); ?></code></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Database Host', 'wpseed'); ?></th>
                        <td><code><?php echo esc_html(DB_HOST); ?></code></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Table Prefix', 'wpseed'); ?></th>
                        <td><code><?php echo esc_html($wpdb->prefix); ?></code></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Database Charset', 'wpseed'); ?></th>
                        <td><code><?php echo esc_html(DB_CHARSET); ?></code></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Database Collate', 'wpseed'); ?></th>
                        <td><code><?php echo esc_html(DB_COLLATE ? DB_COLLATE : 'default'); ?></code></td>
                    </tr>
                </tbody>
            </table>

            <h3><?php esc_html_e('WordPress Tables', 'wpseed'); ?></h3>
            <?php
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching -- Development diagnostic page requires real-time data
            $tables = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}%'", ARRAY_N);
            ?>
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Table Name', 'wpseed'); ?></th>
                        <th><?php esc_html_e('Rows', 'wpseed'); ?></th>
                        <th><?php esc_html_e('Size', 'wpseed'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tables as $table): 
                        $table_name = $table[0];
                        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name from SHOW TABLES is safe
                        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching -- Development diagnostic page requires real-time data
                        $row_count = $wpdb->get_var("SELECT COUNT(*) FROM `{$table_name}`");
                        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching -- Development diagnostic page requires real-time data
                        $table_status = $wpdb->get_row($wpdb->prepare("SHOW TABLE STATUS LIKE %s", $table_name));
                        $size = $table_status ? size_format($table_status->Data_length + $table_status->Index_length) : 'N/A';
                    ?>
                        <tr>
                            <td><code><?php echo esc_html($table_name); ?></code></td>
                            <td><?php echo number_format($row_count); ?></td>
                            <td><?php echo esc_html($size); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
