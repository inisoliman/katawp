<?php
if (!defined('ABSPATH')) exit;

class KataWP_DB_Importer {
    private $wpdb;
    private $import_log = [];
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    /**
     * استيراد بيانات قاعدة البيانات
     */
    public function import_data() {
        // البحث عن ملف SQL من مواقع مختلفة
        $sql_file = $this->find_sql_file();
        
        if (!$sql_file) {
            error_log('KataWP: SQL ملف لم يتم العثور عليه');
            return false;
        }
        
        // قراءة محتويات الملف
        $content = @file_get_contents($sql_file);
        if (!$content) {
            error_log('KataWP: لم قراءة محتويات SQL: ' . $sql_file);
            return false;
        }
        
        // تحضير SQL
        $content = $this->prepare_sql($content);
        
        // تنفيذ SQL
        return $this->execute_sql($content);
    }
    
    /**
     * البحث عن ملف SQL بطرق مختلفة
     */
    private function find_sql_file() {
        $possible_locations = [
            KATAWP_PLUGIN_DIR . 'data/katamars.sql',
            KATAWP_PLUGIN_DIR . 'data/katamars.sql0',
            dirname(__FILE__) . '/../data/katamars.sql',
            dirname(__FILE__) . '/../data/katamars.sql0',
            wp_upload_dir()['basedir'] . '/katawp/katamars.sql',
        ];
        
        foreach ($possible_locations as $location) {
            if (file_exists($location)) {
                error_log('KataWP: SQL ملف وجد: ' . $location);
                return $location;
            }
        }
        
        return false;
    }
    
    /**
     * تحضير SQL - استبدال أسماء الجداول
     */
    private function prepare_sql($content) {
        // استبدال أسماء الجداول القديمة بالجديدة
        $table_mappings = [
            '`bible_ar`' => '`' . KATAWP_DB_PREFIX . 'daily_readings`',
            '`bible_en`' => '`' . KATAWP_DB_PREFIX . 'daily_readings`',
            '`gr_days`' => '`' . KATAWP_DB_PREFIX . 'synaxarium`',
            '`gr_lent`' => '`' . KATAWP_DB_PREFIX . 'synaxarium`',
            '`gr_nineveh`' => '`' . KATAWP_DB_PREFIX . 'synaxarium`',
            '`gr_pentecost`' => '`' . KATAWP_DB_PREFIX . 'synaxarium`',
            '`gr_sundays`' => '`' . KATAWP_DB_PREFIX . 'synaxarium`',
            'bible_ar' => KATAWP_DB_PREFIX . 'daily_readings',
            'bible_en' => KATAWP_DB_PREFIX . 'daily_readings',
            'gr_days' => KATAWP_DB_PREFIX . 'synaxarium',
            'gr_lent' => KATAWP_DB_PREFIX . 'synaxarium',
            'gr_nineveh' => KATAWP_DB_PREFIX . 'synaxarium',
            'gr_pentecost' => KATAWP_DB_PREFIX . 'synaxarium',
            'gr_sundays' => KATAWP_DB_PREFIX . 'synaxarium',
        ];
        
        foreach ($table_mappings as $old_table => $new_table) {
            $content = str_replace($old_table, $new_table, $content);
        }
        
        return $content;
    }
    
    /**
     * تنفيذ استعلامات SQL - مع التعامل مع الأخطاء
     */
    private function execute_sql($content) {
        // تقسيم SQL إلى استعلامات منفردة
        $queries = array_filter(array_map('trim', explode(";", $content)));
        
        $executed = 0;
        $failed = 0;
        
        foreach ($queries as $query) {
            if (empty($query)) {
                continue;
            }
            
            // تنفيذ استعلامة SQL
            $result = $this->wpdb->query($query);
            
            if ($result === false) {
                error_log('KataWP SQL خطأ: ' . $this->wpdb->last_error);
                error_log('استعلامة: ' . substr($query, 0, 100));
                $failed++;
            } else {
                $executed++;
            }
        }
        
        error_log('KataWP: تم تنفيذ ' . $executed . ' استعلامة');
        if ($failed > 0) {
            error_log('KataWP: فشل ' . $failed . ' استعلامة');
        }
        
        return true;
    }
    
    /**
     * الحصول على سجل الاستيراد
     */
    public function get_import_log() {
        return $this->import_log;
    }
}
