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
            <h2><?php _e('Database Information', 'wpseed'); ?></h2>
            
            <table class="widefat">
                <tbody>
                    <tr>
                        <th><?php _e('Database Name', 'wpseed'); ?></th>
                        <td><code><?php echo esc_html(DB_NAME); ?></code></td>
                    </tr>
                    <tr>
                        <th><?php _e('Database Host', 'wpseed'); ?></th>
                        <td><code><?php echo esc_html(DB_HOST); ?></code></td>
                    </tr>
                    <tr>
                        <th><?php _e('Table Prefix', 'wpseed'); ?></th>
                        <td><code><?php echo esc_html($wpdb->prefix); ?></code></td>
                    </tr>
                    <tr>
                        <th><?php _e('Database Charset', 'wpseed'); ?></th>
                        <td><code><?php echo esc_html(DB_CHARSET); ?></code></td>
                    </tr>
                    <tr>
                        <th><?php _e('Database Collate', 'wpseed'); ?></th>
                        <td><code><?php echo esc_html(DB_COLLATE ? DB_COLLATE : 'default'); ?></code></td>
                    </tr>
                </tbody>
            </table>

            <h3><?php _e('WordPress Tables', 'wpseed'); ?></h3>
            <?php
            $tables = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}%'", ARRAY_N);
            ?>
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e('Table Name', 'wpseed'); ?></th>
                        <th><?php _e('Rows', 'wpseed'); ?></th>
                        <th><?php _e('Size', 'wpseed'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tables as $table): 
                        $table_name = $table[0];
                        $row_count = $wpdb->get_var("SELECT COUNT(*) FROM `{$table_name}`");
                        $table_status = $wpdb->get_row("SHOW TABLE STATUS LIKE '{$table_name}'");
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
