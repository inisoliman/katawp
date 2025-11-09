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
        $old_prefix = 'wp_';
        $new_prefix = KATAWP_DB_PREFIX;
        
        $tables = ['daily_readings', 'synaxarium', 'epistle', 'gospel', 'apostles', 'liturgy', 'saints'];
        
        foreach ($tables as $table) {
            $content = str_replace($old_prefix . $table, $new_prefix . $table, $content);
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
