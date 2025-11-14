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
        
        // فتح الملف للقراءة
        $handle = fopen($sql_file, 'r');
        if (!$handle) {
            error_log('KataWP: لم قراءة محتويات SQL: ' . $sql_file);
            return false;
        }
        
        // تنفيذ SQL
        $result = $this->execute_sql($handle);

        fclose($handle);

        return $result;
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
        
        return str_replace(array_keys($table_mappings), array_values($table_mappings), $content);
    }
    
    /**
     * تنفيذ استعلامات SQL - مع التعامل مع الأخطاء
     */
    private function execute_sql($handle) {
        $query = '';
        $executed = 0;
        $failed = 0;

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);

            if (empty($line) || substr($line, 0, 2) == '--' || substr($line, 0, 1) == '#') {
                continue;
            }

            $query .= $line;

            if (substr($query, -1) == ';') {
                $prepared_query = $this->prepare_sql(substr($query, 0, -1));

                if (!empty($prepared_query) && strpos(strtoupper($prepared_query), 'INSERT INTO') === 0) {
                    $result = $this->wpdb->query($prepared_query);

                    if ($result === false) {
                        error_log('KataWP SQL خطأ: ' . $this->wpdb->last_error);
                        error_log('استعلامة: ' . substr($prepared_query, 0, 100));
                        $failed++;
                    } else {
                        $executed++;
                    }
                }
                $query = '';
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
