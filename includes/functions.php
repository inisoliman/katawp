<?php

if (!defined('ABSPATH')) exit;

function katawp_get_db_instance() {
    static $db = null;
    if ($db === null) {
        $db = new KataWP_Database();
    }
    return $db;
}

function katawp_get_today_reading() {
    return katawp_get_db_instance()->get_today_reading();
}

function katawp_get_reading_by_date($date) {
    $cached = KataWP_Cache::get_reading($date);
    if ($cached !== false) {
        return $cached;
    }

    $reading = katawp_get_db_instance()->get_today_reading($date);
    KataWP_Cache::set_reading($date, $reading);

    return $reading;
}

function katawp_search_readings($query) {
    $db = katawp_get_db_instance();
    return $db->search_readings($query);
}

function katawp_format_date($gregorian_date) {
    $date = new DateTime($gregorian_date);
    return $date->format('d/m/Y');
}

function katawp_get_coptic_date($date) {
    // تحويل التاريخ الميلادي إلى القبطي
    $db = katawp_get_db_instance();
    $result = $db->wpdb->get_row(
        $db->wpdb->prepare(
            "SELECT coptic_day, coptic_month FROM " . KATAWP_DB_PREFIX . "daily_readings
            WHERE gregorian_date = %s",
            $date
        )
    );
    
    return $result ? $result->coptic_day . ' ' . $result->coptic_month : '';
}
