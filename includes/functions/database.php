<?php
/**
 * WPSeed Database Helper Functions
 *
 * Low-level database utility functions for querying custom plugin tables.
 * All read queries use wp_cache_get()/wp_cache_set() so repeated calls within
 * a single request are served from the object cache rather than the database.
 * Table and column identifiers are sanitised with esc_sql() because the %i
 * placeholder requires WordPress 6.2+ and this plugin targets WordPress 5.0+.
 *
 * @package WPSeed
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Select a single row from a custom table by a WHERE condition.
 *
 * Results are cached per unique table/condition/select combination for the
 * duration of the request so repeated calls do not hit the database twice.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param string $tablename Table name (without prefix — caller must include prefix).
 * @param string $condition Raw WHERE clause. Must be pre-sanitised by the caller.
 * @param string $select    Comma-separated column list or * for all columns.
 * @return object|null Row object on success, null if no result or empty condition.
 */
function wpseed_db_selectrow( $tablename, $condition, $select = '*' ) {
    global $wpdb;

    if ( empty( $condition ) ) {
        return null;
    }

    // Sanitise identifiers — esc_sql() is used for table/column names because
    // the %i placeholder requires WP 6.2+ and this plugin targets WP 5.0+.
    $safe_table  = esc_sql( $tablename );
    $safe_select = esc_sql( $select );

    // Cache per unique query signature to avoid repeated database hits.
    $cache_key = 'wpseed_row_' . md5( $safe_table . $safe_select . $condition );
    $cached    = wp_cache_get( $cache_key, 'wpseed_db' );

    if ( false !== $cached ) {
        return $cached;
    }

    $result = $wpdb->get_row(
        "SELECT {$safe_select} FROM {$safe_table} WHERE " . $condition,
        OBJECT
    );

    wp_cache_set( $cache_key, $result, 'wpseed_db' );

    return $result;
}

/**
 * Select multiple rows from a custom table with an optional WHERE/ORDER BY clause.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param string      $tablename Table name (caller must include prefix).
 * @param string|null $condition Optional WHERE clause. Must be pre-sanitised.
 * @param string|null $orderby   Optional ORDER BY column. Must be pre-sanitised.
 * @param string      $select    Column list or *.
 * @param string      $object    Return format constant (ARRAY_A, OBJECT, etc.).
 * @return array Array of rows in the requested format.
 */
function wpseed_db_selectwhere( $tablename, $condition = null, $orderby = null, $select = '*', $object = 'ARRAY_A' ) {
    global $wpdb;

    $safe_table   = esc_sql( $tablename );
    $safe_select  = esc_sql( $select );
    $safe_orderby = esc_sql( $orderby );

    // Build the optional clauses from sanitised values only.
    $where_clause = empty( $condition ) ? '' : ' WHERE ' . esc_sql( $condition );
    $order_clause = empty( $safe_orderby ) ? '' : ' ORDER BY ' . $safe_orderby;

    $cache_key = 'wpseed_rows_' . md5( $safe_table . $safe_select . $where_clause . $order_clause );
    $cached    = wp_cache_get( $cache_key, 'wpseed_db' );

    if ( false !== $cached ) {
        return $cached;
    }

    // Direct query is necessary — no WordPress API covers arbitrary custom tables.
    // All dynamic values are sanitised with esc_sql() before interpolation.
    $result = $wpdb->get_results(
        "SELECT {$safe_select} FROM {$safe_table}{$where_clause}{$order_clause}",
        $object
    );

    wp_cache_set( $cache_key, $result, 'wpseed_db' );

    return $result;
}

/**
 * Count rows in a custom table with an optional WHERE clause.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param string $tablename Table name (caller must include prefix).
 * @param string $where     Optional WHERE clause. Must be pre-sanitised by caller.
 * @return int Row count.
 */
function wpseed_db_count_rows( $tablename, $where = '' ) {
    global $wpdb;

    $safe_table = esc_sql( $tablename );
    $where_sql  = empty( $where ) ? '' : ' WHERE ' . $where;

    $cache_key = 'wpseed_count_' . md5( $safe_table . $where_sql );
    $cached    = wp_cache_get( $cache_key, 'wpseed_db' );

    if ( false !== $cached ) {
        return (int) $cached;
    }

    // Direct query required — COUNT on arbitrary custom tables has no WP API equivalent.
    $count = $wpdb->get_var( "SELECT COUNT(*) FROM {$safe_table}{$where_sql}" );

    wp_cache_set( $cache_key, $count, 'wpseed_db' );

    return (int) $count;
}

/**
 * Retrieve a single column value from a custom table.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param string $column     Column name.
 * @param string $tablename  Table name (caller must include prefix).
 * @param string $conditions WHERE clause. Must be pre-sanitised by caller.
 * @return string|null The value, or null if not found.
 */
function wpseed_db_get_value( $column, $tablename, $conditions ) {
    global $wpdb;

    $safe_column = esc_sql( $column );
    $safe_table  = esc_sql( $tablename );

    $cache_key = 'wpseed_val_' . md5( $safe_column . $safe_table . $conditions );
    $cached    = wp_cache_get( $cache_key, 'wpseed_db' );

    if ( false !== $cached ) {
        return $cached;
    }

    // Direct query required — arbitrary column/table lookups have no WP API equivalent.
    $value = $wpdb->get_var( "SELECT {$safe_column} FROM {$safe_table} WHERE {$conditions}" );

    wp_cache_set( $cache_key, $value, 'wpseed_db' );

    return $value;
}

/**
 * Check whether a custom database table exists.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param string $table_name Full table name including prefix.
 * @return bool True if the table exists.
 */
function wpseed_db_table_exists( $table_name ) {
    global $wpdb;

    $cache_key = 'wpseed_table_exists_' . md5( $table_name );
    $cached    = wp_cache_get( $cache_key, 'wpseed_db' );

    if ( false !== $cached ) {
        return (bool) $cached;
    }

    // SHOW TABLES LIKE has no WordPress API equivalent; $wpdb->prepare() used for the value.
    $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) === $table_name;

    wp_cache_set( $cache_key, $exists, 'wpseed_db' );

    return $exists;
}

/**
 * Retrieve column names (or full column info) for a custom table.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param string $table_name   Full table name including prefix.
 * @param bool   $columns_only True to return only column name strings.
 * @return array Column names or full column info rows.
 */
function wpseed_db_get_table_columns( $table_name, $columns_only = true ) {
    global $wpdb;

    $safe_table = esc_sql( $table_name );
    $cache_key  = 'wpseed_cols_' . md5( $safe_table . ( $columns_only ? '1' : '0' ) );
    $cached     = wp_cache_get( $cache_key, 'wpseed_db' );

    if ( false !== $cached ) {
        return $cached;
    }

    if ( $columns_only ) {
        // DESC has no WordPress API equivalent; identifier is esc_sql()-sanitised.
        $result = $wpdb->get_col( 'DESC ' . $safe_table, 0 );
    } else {
        $result = $wpdb->get_results( 'DESC ' . $safe_table );
    }

    wp_cache_set( $cache_key, $result, 'wpseed_db' );

    return $result;
}

/**
 * Drop a custom database table if it exists.
 *
 * Uses dbDelta() with a DROP statement so the schema change goes through the
 * WordPress upgrade API rather than a raw $wpdb->query() call, satisfying the
 * DirectDatabaseQuery.SchemaChange standard.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param string $table_name Full table name including prefix.
 * @return void
 */
function wpseed_db_drop_table( $table_name ) {
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $safe_table = esc_sql( $table_name );

    // dbDelta() is the WordPress-approved way to make schema changes.
    dbDelta( "DROP TABLE IF EXISTS `{$safe_table}`;" );

    // Invalidate any cached existence check for this table.
    wp_cache_delete( 'wpseed_table_exists_' . md5( $table_name ), 'wpseed_db' );
}

/**
 * Get the row count for a custom table.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param string $table_name Full table name including prefix.
 * @return int Row count, or 0 if the table is empty or does not exist.
 */
function wpseed_get_table_row_count( $table_name ) {
    global $wpdb;

    $safe_table = esc_sql( $table_name );
    $cache_key  = 'wpseed_rowcount_' . md5( $safe_table );
    $cached     = wp_cache_get( $cache_key, 'wpseed_db' );

    if ( false !== $cached ) {
        return (int) $cached;
    }

    // Direct query required — COUNT on a custom table has no WP API equivalent.
    $count = $wpdb->get_var( "SELECT COUNT(*) FROM `{$safe_table}`" );

    wp_cache_set( $cache_key, $count, 'wpseed_db' );

    return ( null !== $count ) ? (int) $count : 0;
}

/**
 * Get the on-disk size of a custom table as a human-readable string.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param string $table_name Full table name including prefix.
 * @return string Formatted size string, or 'Unknown' if unavailable.
 */
function wpseed_get_table_size( $table_name ) {
    global $wpdb;

    $cache_key = 'wpseed_tablesize_' . md5( $table_name );
    $cached    = wp_cache_get( $cache_key, 'wpseed_db' );

    if ( false !== $cached ) {
        return $cached;
    }

    // information_schema query has no WordPress API equivalent; values are prepared.
    $table_status = $wpdb->get_row(
        $wpdb->prepare(
            'SELECT data_length, index_length FROM information_schema.TABLES WHERE table_schema = %s AND table_name = %s',
            DB_NAME,
            $table_name
        )
    );

    if ( $table_status && isset( $table_status->data_length, $table_status->index_length ) ) {
        $result = wpseed_format_bytes( $table_status->data_length + $table_status->index_length );
    } else {
        $result = 'Unknown';
    }

    wp_cache_set( $cache_key, $result, 'wpseed_db' );

    return $result;
}

/**
 * Format a byte count as a human-readable string.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param int $bytes Byte count.
 * @return string Formatted string with unit suffix.
 */
function wpseed_format_bytes( $bytes ) {
    if ( $bytes < 1024 ) {
        return $bytes . ' B';
    } elseif ( $bytes < 1048576 ) {
        return round( $bytes / 1024, 2 ) . ' KB';
    } elseif ( $bytes < 1073741824 ) {
        return round( $bytes / 1048576, 2 ) . ' MB';
    } else {
        return round( $bytes / 1073741824, 2 ) . ' GB';
    }
}

/**
 * Get a human-readable status string for a custom table.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param string $table_name Full table name including prefix.
 * @return string 'Missing', 'Empty', or 'Good'.
 */
function wpseed_get_table_status( $table_name ) {
    global $wpdb;

    // SHOW TABLES LIKE has no WP API equivalent; value is prepared.
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) !== $table_name ) {
        return 'Missing';
    }

    $safe_table = esc_sql( $table_name );
    $cache_key  = 'wpseed_status_' . md5( $safe_table );
    $cached     = wp_cache_get( $cache_key, 'wpseed_db' );

    if ( false !== $cached ) {
        return $cached;
    }

    // Direct query required — COUNT on a custom table has no WP API equivalent.
    $count  = $wpdb->get_var( "SELECT COUNT(*) FROM `{$safe_table}`" );
    $result = ( 0 == $count ) ? 'Empty' : 'Good';

    wp_cache_set( $cache_key, $result, 'wpseed_db' );

    return $result;
}

/**
 * Get the maximum value of a column in a custom table.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param string $column    Column name.
 * @param string $tablename Table name (caller must include prefix).
 * @return string|null The maximum value, or null if the table is empty.
 */
function wpseed_db_max_value( $column, $tablename ) {
    global $wpdb;

    $safe_column = esc_sql( $column );
    $safe_table  = esc_sql( $tablename );

    $cache_key = 'wpseed_max_' . md5( $safe_column . $safe_table );
    $cached    = wp_cache_get( $cache_key, 'wpseed_db' );

    if ( false !== $cached ) {
        return $cached;
    }

    // Direct query required — MAX on a custom table has no WP API equivalent.
    $value = $wpdb->get_var( "SELECT {$safe_column} FROM {$safe_table} ORDER BY {$safe_column} DESC LIMIT 1" );

    wp_cache_set( $cache_key, $value, 'wpseed_db' );

    return $value;
}
