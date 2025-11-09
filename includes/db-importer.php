<?php
// يتولى هذا الملف إنشاء الجداول واستيراد محتوى قاعدة البيانات SQL أوتوماتيك
function katawp_db_importer() {
    global $wpdb;
    $sql_file = plugin_dir_path(__FILE__).'../u626751827_katamars.sql';
    if (!file_exists($sql_file)) {
        error_log('ملف قاعدة البيانات غير موجود: '.$sql_file);
        return;
    }
    $sql_content = file_get_contents($sql_file);
    // تقسيم الملف إلى استعلامات منفصلة مع التعديل حسب جداول ووردبريس
    $queries = array_filter(array_map('trim', explode(';', $sql_content)));
    foreach ($queries as $query) {
        // تعديل لاحقًا لاستبدال أسماء الجداول القديمة بالجديدة إن لزم
        if ($query) $wpdb->query($query);
    }
    // إضافة رسالة نجاح
    update_option('katawp_db_imported', true);
}
