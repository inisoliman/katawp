<?php

if (!defined('ABSPATH')) exit;

class KataWP_Shortcodes {
    public function __construct() {
        add_shortcode('katawp_readings', [$this, 'readings_shortcode']);
        add_shortcode('katawp_search', [$this, 'search_shortcode']);
        add_shortcode('katawp_saint', [$this, 'saint_shortcode']);
    }
    
    public function readings_shortcode($atts) {
        $atts = shortcode_atts(['date' => date('Y-m-d')], $atts);
        
        $db = new KataWP_Database();
        $reading = $db->get_today_reading($atts['date']);
        
        if (!$reading) {
            return '<p>لا توجد قراءات لهذا اليوم</p>';
        }
        
        ob_start();
        include KATAWP_PLUGIN_DIR . 'templates/daily-readings.php';
        return ob_get_clean();
    }
    
    public function search_shortcode($atts) {
        ob_start();
        include KATAWP_PLUGIN_DIR . 'templates/search.php';
        return ob_get_clean();
    }
    
    public function saint_shortcode($atts) {
        $atts = shortcode_atts(['name' => ''], $atts);
        
        if (empty($atts['name'])) {
            return '';
        }
        
        global $wpdb;
        $saint = $wpdb->get_row(
            "SELECT * FROM " . KATAWP_DB_PREFIX . "synaxarium 
            WHERE saint_name LIKE '%" . $wpdb->esc_like($atts['name']) . "%'"
        );
        
        if (!$saint) {
            return '<p>لم يتم العثور على القديس</p>';
        }
        
        return '<div class="katawp-saint"><h3>' . $saint->saint_name . '</h3><p>' . $saint->biography . '</p></div>';
    }
}

new KataWP_Shortcodes();
