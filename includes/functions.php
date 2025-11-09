<?php

if (!defined('ABSPATH')) exit;

function katawp_get_today_reading() {
    $db = new KataWP_Database();
    return $db->get_today_reading();
}

function katawp_get_reading_by_date($date) {
    $db = new KataWP_Database();
    return $db->get_today_reading($date);
}

function katawp_search_readings($query) {
    $db = new KataWP_Database();
    return $db->search_readings($query);
}

function katawp_format_date($gregorian_date) {
    $date = new DateTime($gregorian_date);
    return $date->format('d/m/Y');
}

function katawp_get_coptic_date($date) {
    // تحويل التاريخ الميلادي إلى القبطي
    $reading = new KataWP_Database();
    $result = $reading->wpdb->get_row(
        "SELECT coptic_day, coptic_month FROM " . KATAWP_DB_PREFIX . "daily_readings 
        WHERE gregorian_date = '" . $date . "'"
    );
    
    return $result ? $result->coptic_day . ' ' . $result->coptic_month : '';
}
