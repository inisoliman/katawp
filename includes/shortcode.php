<?php
if (!defined('ABSPATH')) exit;

/**
 * KataWP Shortcodes
 * Handles all shortcodes for displaying readings, search, and saints
 * Integrated with date converter for Coptic calendar support
 */

class KataWP_Shortcodes {

    public function __construct() {
        add_shortcode('katawp_readings', [$this, 'readings_shortcode']);
        add_shortcode('katawp_search', [$this, 'search_shortcode']);
        add_shortcode('katawp_saint', [$this, 'saint_shortcode']);
    }
    
    /**
     * Display daily readings shortcode
     * Supports both Gregorian and Coptic dates
     */
    public function readings_shortcode($atts) {
        $atts = shortcode_atts([
            'date' => date('Y-m-d'),
            'coptic_date' => '',
            'format' => 'html'
        ], $atts);
        
        $db = new KataWP_Database();
        $gregorian_date = $atts['date'];
        $coptic_date = $atts['coptic_date'];
        
        // Get current dates in both calendars
        $dates_info = KataWP_Date_Converter::get_current_dates();
        
        // If coptic date not provided, convert from gregorian
        if (empty($coptic_date)) {
            $gregorian_parts = explode('-', $gregorian_date);
            $month = intval($gregorian_parts[1]);
            $day = intval($gregorian_parts[2]);
            $year = intval($gregorian_parts[0]);
            // Use a reference point to determine coptic date
            // For now, use current date conversion as example
            $coptic_date = $this->gregorian_to_coptic_approximation($month, $day, $year);
        }
        
        // Fetch reading from database using both date formats
        $reading = $db->get_today_reading_by_coptic_date($coptic_date);
        
        if (!$reading) {
            return '<div class="katawp-no-reading"><p>' . __('لا توجد قراءات لهذا اليوم', 'katawp') . '</p></div>';
        }
        
        // Prepare data for template
        $data = [
            'reading' => $reading,
            'gregorian_date' => $gregorian_date,
            'coptic_date' => $coptic_date,
            'dates_info' => $dates_info
        ];
        
        // Render template
        ob_start();
        extract($data);
        include KATAWP_PLUGIN_DIR . 'templates/daily-readings.php';
        return ob_get_clean();
    }
    
    /**
     * Search shortcode for readings
     */
    public function search_shortcode($atts) {
        ob_start();
        include KATAWP_PLUGIN_DIR . 'templates/search.php';
        return ob_get_clean();
    }
    
    /**
     * Display saint information shortcode
     */
    public function saint_shortcode($atts) {
        $atts = shortcode_atts(['name' => ''], $atts);
        
        if (empty($atts['name'])) {
            return '';
        }
        
        global $wpdb;
        $saint = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM " . KATAWP_DB_PREFIX . "synaxarium WHERE saint_name LIKE %s",
                '%' . $wpdb->esc_like($atts['name']) . '%'
            )
        );
        
        if (!$saint) {
            return '<div class="katawp-no-saint"><p>' . __('لم يتم العثور على القديس', 'katawp') . '</p></div>';
        }
        
        return '<div class="katawp-saint"><h3>' . esc_html($saint->saint_name) . '</h3><p>' . wp_kses_post($saint->biography) . '</p></div>';
    }
    
    /**
     * Helper function to approximate Coptic date from Gregorian
     * This uses a simple approximation - can be enhanced with full conversion
     */
    private function gregorian_to_coptic_approximation($month, $day, $year) {
        // Create a date string in the format expected by date converter
        // This is a simplified version - full conversion would use the converter class
        $gregorian_string = $month . '/' . $day . '/' . $year;
        
        // For accurate conversion, we would need to implement the reverse
        // of the coptic_to_gregorian function. For now, return in a format
        // that the database can query
        return $month . '/' . $day . '/' . ($year - 5492); // Approximate Coptic year
    }
}

// Initialize shortcodes
new KataWP_Shortcodes();
?>
