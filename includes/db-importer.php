<?php
if (!defined('ABSPATH')) exit;

class KataWP_DB_Importer {
    private $wpdb;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    public function import_data() {
        // استيراد البيانات من ملف SQL
        $sql_file = KATAWP_PLUGIN_DIR . 'data/katamars.sql';
        
        if (!file_exists($sql_file)) {
            return false;
        }
        
        $content = file_get_contents($sql_file);
        $content = $this->prepare_sql($content);
        
        return $this->execute_sql($content);
    }
    
    private function prepare_sql($content) {
        // استبدال أسماء الجداول القديمة بالجديدة
        $table_mappings = [
            'bible_ar' => KATAWP_DB_PREFIX . 'daily_readings',
            'bible_en' => KATAWP_DB_PREFIX . 'daily_readings',
            'gr_days' => KATAWP_DB_PREFIX . 'synaxarium',
            'gr_lent' => KATAWP_DB_PREFIX . 'synaxarium',
            'gr_nineveh' => KATAWP_DB_PREFIX . 'synaxarium',
            'gr_pentecost' => KATAWP_DB_PREFIX . 'synaxarium',
            'gr_sundays' => KATAWP_DB_PREFIX . 'synaxarium',
            'wp_katawp_synaxarium' => KATAWP_DB_PREFIX . 'synaxarium',
            'wp_katawp_epistle' => KATAWP_DB_PREFIX . 'epistle',
            'wp_katawp_gospel' => KATAWP_DB_PREFIX . 'gospel',
            'wp_katawp_apostles' => KATAWP_DB_PREFIX . 'apostles',
            'wp_katawp_liturgy' => KATAWP_DB_PREFIX . 'liturgy',
            'wp_katawp_saints' => KATAWP_DB_PREFIX . 'saints'
        ];
        
        foreach ($table_mappings as $old_table => $new_table) {
            $content = str_replace("`" . $old_table . "`", "`" . $new_table . "`", $content);
            $content = str_replace($old_table, $new_table, $content);
        }
        
        return $content;
    }
    
    private function execute_sql($content) {
        $queries = array_filter(array_map('trim', explode(';', $content)));
        
        foreach ($queries as $query) {
            if (!empty($query)) {
                $this->wpdb->query($query);
            }
        }
        
        return true;
    }
}
