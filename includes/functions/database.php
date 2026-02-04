<?php
/**
 * WPSeed Database Helper Functions
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
    return $wpdb->get_row($wpdb->prepare("SELECT $select FROM $tablename WHERE $condition"), OBJECT);
}

function wpseed_db_selectwhere($tablename, $condition = null, $orderby = null, $select = '*', $object = 'ARRAY_A') {
    global $wpdb;
    $condition = empty($condition) ? '' : ' WHERE ' . $condition;
    $condition .= empty($orderby) ? '' : ' ORDER BY ' . $orderby;
    return $wpdb->get_results("SELECT $select FROM $tablename $condition", $object);
}

function wpseed_db_count_rows($tablename, $where = '') {
    global $wpdb;
    return $wpdb->get_var("SELECT COUNT(*) FROM $tablename" . $where);
}

function wpseed_db_get_value($column, $tablename, $conditions) {
    global $wpdb;
    return $wpdb->get_var($wpdb->prepare("SELECT $column FROM $tablename WHERE $conditions"));
}

function wpseed_db_table_exists($table_name) {
    global $wpdb;
    return $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;
}

function wpseed_db_get_table_columns($table_name, $columns_only = true) {
    global $wpdb;
    
    if ($columns_only) {
        $columns_array = array();
        foreach ($wpdb->get_col("DESC " . $table_name, 0) as $column_name) {
            $columns_array[] = $column_name;
        }
        return $columns_array;
    }
    
    return $wpdb->get_results("DESC " . $table_name);
}

function wpseed_db_drop_table($table_name) {
    global $wpdb;
    return $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

function wpseed_get_table_row_count($table_name) {
    global $wpdb;
    $count = @$wpdb->get_var("SELECT COUNT(*) FROM `{$table_name}`");
    return ($count !== null) ? (int) $count : 0;
}

function wpseed_get_table_size($table_name) {
    global $wpdb;
    
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
    
    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) != $table_name) {
        return 'Missing';
    }
    
    $count = $wpdb->get_var("SELECT COUNT(*) FROM `{$table_name}`");
    if ($count == 0) {
        return 'Empty';
    }
    
    return 'Good';
}

function wpseed_db_max_value($column, $tablename) {
    global $wpdb;
    return $wpdb->get_var("SELECT $column FROM $tablename ORDER BY $column DESC LIMIT 1");
}
