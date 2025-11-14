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
    return katawp_get_reading_by_date(date('Y-m-d'));
}

function katawp_get_reading_by_date($date) {
    $cache_key = 'reading_' . $date;
    $cached = KataWP_Cache::get_reading($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    $reading = katawp_get_db_instance()->get_reading_by_date($date);
    KataWP_Cache::set_reading($cache_key, $reading);

    return $reading;
}

function katawp_search_readings($query) {
    // سيتم تنفيذ هذا لاحقًا
    return [];
}
