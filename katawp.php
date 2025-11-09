<?php
/*
Plugin Name: Katamars Daily Readings
Description: استيراد وعرض القراءات اليومية مع السنكسار من قاعدة بيانات خارجية وتحويلها لجداول موسعة في ووردبريس تلقائيًا.
Version: 1.0
Author: inisoliman & Comet Assistant
Text Domain: katawp
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/db-importer.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';

// تفعيل الإضافة: إنشاء الجداول واستيراد البيانات أوتوماتيك
register_activation_hook(__FILE__, 'katawp_activate_plugin');
function katawp_activate_plugin() {
    require_once plugin_dir_path(__FILE__) . 'includes/db-importer.php';
    katawp_db_importer();
}

// لوحة تحكم
add_action('admin_menu', function() {
    add_menu_page('إعدادات القراءات اليومية', 'القراءات اليومية', 'manage_options', 'katawp-settings', 'katawp_settings_page', 'dashicons-book', 40);
});

function katawp_settings_page() {
    echo '<h2>إعدادات إضافة القراءات اليومية</h2>';
    echo '<p>تم استيراد قاعدة البيانات تلقائيًا ويمكنك إدارة خيارات العرض والبنامج</p>';
}
