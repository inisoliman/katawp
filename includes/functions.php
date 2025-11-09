<?php
// دوال مساعدة للتعامل مع جداول البيانات وعرض القراءات
function katawp_get_reading($date) {
    global $wpdb;
    // مثال: جلب قراءة معينة بناء على التاريخ
    $table = 'katamars_readings';
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE read_date=%s", $date));
    return $row;
}

function katawp_get_synax($date) {
    global $wpdb;
    $table = 'katamars_synax';
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE synax_date=%s", $date));
}

// إضافة دوال لمزيد من العمليات حسب قاعدة البيانات الأصلية
