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

    /**
     * Output the database diagnosis tab.
     *
     * Direct queries are used throughout because SHOW TABLES, SHOW TABLE STATUS,
     * and COUNT(*) on arbitrary tables have no WordPress API equivalents.
     * All results are cached per request to avoid repeated database hits.
     *
     * @since   1.0.0
     * @version 1.2.0
     * @return void
     */
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
            // SHOW TABLES has no WP API equivalent; result cached for this request.
            $tables_cache_key = 'wpseed_dev_tables_' . md5( $wpdb->prefix );
            $tables = wp_cache_get( $tables_cache_key, 'wpseed_dev' );
            if ( false === $tables ) {
                $tables = $wpdb->get_results( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->prefix . '%' ), ARRAY_N );
                wp_cache_set( $tables_cache_key, $tables, 'wpseed_dev' );
            }
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
                    <?php foreach ( $tables as $table ) :
                        // esc_sql() used for table identifier — %i requires WP 6.2+
                        // and this plugin targets WP 4.4+.
                        $table_name     = $table[0];
                        $safe_table     = esc_sql( $table_name );

                        // COUNT(*) cached per table name for this request.
                        $count_cache_key = 'wpseed_dev_count_' . md5( $table_name );
                        $row_count = wp_cache_get( $count_cache_key, 'wpseed_dev' );
                        if ( false === $row_count ) {
                            $row_count = $wpdb->get_var( 'SELECT COUNT(*) FROM `' . $safe_table . '`' );
                            wp_cache_set( $count_cache_key, $row_count, 'wpseed_dev' );
                        }

                        // SHOW TABLE STATUS cached per table name for this request.
                        $status_cache_key = 'wpseed_dev_status_' . md5( $table_name );
                        $table_status = wp_cache_get( $status_cache_key, 'wpseed_dev' );
                        if ( false === $table_status ) {
                            $table_status = $wpdb->get_row( $wpdb->prepare( 'SHOW TABLE STATUS LIKE %s', $table_name ) );
                            wp_cache_set( $status_cache_key, $table_status, 'wpseed_dev' );
                        }

                        $size = $table_status ? size_format( $table_status->Data_length + $table_status->Index_length ) : 'N/A';
                    ?>
                        <tr>
                            <td><code><?php echo esc_html( $table_name ); ?></code></td>
                            <td><?php echo number_format( intval( $row_count ) ); ?></td>
                            <td><?php echo esc_html( $size ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
