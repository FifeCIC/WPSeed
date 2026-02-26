<?php
/**
 * WPSeed Database Helper Functions
 * 
 * These are low-level database utility functions that perform direct queries.
 * Caching is intentionally NOT implemented here - it should be handled by the
 * calling code based on specific use cases and requirements.
 * 
 * When adding new functions to this file, use the phpcs:ignore comment for
 * NoCaching warnings as caching decisions belong in the business logic layer.
 * 
 * @package WPSeed
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

function wpseed_db_selectrow($tablename, $condition, $select = '*') {
    global $wpdb;
    if (empty($condition)) {
        return null;
    }
    $tablename = esc_sql($tablename);
    $select = esc_sql($select);
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Utility function, caching handled by callers, identifiers escaped
    return $wpdb->get_row("SELECT $select FROM $tablename WHERE $condition", OBJECT);
}

function wpseed_db_selectwhere($tablename, $condition = null, $orderby = null, $select = '*', $object = 'ARRAY_A') {
    global $wpdb;
    $tablename = esc_sql($tablename);
    $select = esc_sql($select);
    $orderby = esc_sql($orderby);
    $condition = empty($condition) ? '' : ' WHERE ' . $condition;
    $condition .= empty($orderby) ? '' : ' ORDER BY ' . $orderby;
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Utility function, caching handled by callers, identifiers escaped
    return $wpdb->get_results("SELECT $select FROM $tablename $condition", $object);
}

function wpseed_db_count_rows($tablename, $where = '') {
    global $wpdb;
    $tablename = esc_sql($tablename);
    $sql = "SELECT COUNT(*) FROM $tablename";
    if (!empty($where)) {
        $sql .= " WHERE " . $where;
    }
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Utility function, caching handled by callers, $where expected to be pre-sanitized by caller
    return $wpdb->get_var($sql);
}

function wpseed_db_get_value($column, $tablename, $conditions) {
    global $wpdb;
    $column = esc_sql($column);
    $tablename = esc_sql($tablename);
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Utility function, caching handled by callers, identifiers escaped
    return $wpdb->get_var("SELECT $column FROM $tablename WHERE $conditions");
}

function wpseed_db_table_exists($table_name) {
    global $wpdb;
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching -- Utility function, caching handled by callers
    return $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;
}

function wpseed_db_get_table_columns($table_name, $columns_only = true) {
    global $wpdb;
    $table_name = esc_sql($table_name);
    
    if ($columns_only) {
        $columns_array = array();
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Utility function, caching handled by callers, table name escaped
        foreach ($wpdb->get_col("DESC " . $table_name, 0) as $column_name) {
            $columns_array[] = $column_name;
        }
        return $columns_array;
    }
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Utility function, caching handled by callers, table name escaped
    return $wpdb->get_results("DESC " . $table_name);
}

function wpseed_db_drop_table($table_name) {
    global $wpdb;
    $table_name = esc_sql($table_name);
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name escaped
    return $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

function wpseed_get_table_row_count($table_name) {
    global $wpdb;
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching -- Utility function, caching handled by callers
    $count = @$wpdb->get_var("SELECT COUNT(*) FROM `{$table_name}`");
    return ($count !== null) ? (int) $count : 0;
}

function wpseed_get_table_size($table_name) {
    global $wpdb;
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching -- Utility function, caching handled by callers
    $table_status = $wpdb->get_row($wpdb->prepare("
        SELECT data_length, index_length 
        FROM information_schema.TABLES 
        WHERE table_schema = %s AND table_name = %s
    ", DB_NAME, $table_name));
    
    if ($table_status && isset($table_status->data_length) && isset($table_status->index_length)) {
        $size_in_bytes = $table_status->data_length + $table_status->index_length;
        return wpseed_format_bytes($size_in_bytes);
    }
    
    return 'Unknown';
}

function wpseed_format_bytes($bytes) {
    if ($bytes < 1024) {
        return $bytes . ' B';
    } elseif ($bytes < 1048576) {
        return round($bytes / 1024, 2) . ' KB';
    } elseif ($bytes < 1073741824) {
        return round($bytes / 1048576, 2) . ' MB';
    } else {
        return round($bytes / 1073741824, 2) . ' GB';
    }
}

function wpseed_get_table_status($table_name) {
    global $wpdb;
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching -- Utility function, caching handled by callers
    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) != $table_name) {
        return 'Missing';
    }
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching -- Utility function, caching handled by callers
    $count = $wpdb->get_var("SELECT COUNT(*) FROM `{$table_name}`");
    if ($count == 0) {
        return 'Empty';
    }
    
    return 'Good';
}

function wpseed_db_max_value($column, $tablename) {
    global $wpdb;
    $column = esc_sql($column);
    $tablename = esc_sql($tablename);
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Utility function, caching handled by callers, identifiers escaped
    return $wpdb->get_var("SELECT $column FROM $tablename ORDER BY $column DESC LIMIT 1");
}
