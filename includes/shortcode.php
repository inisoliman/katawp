<?php
if (!defined('ABSPATH')) exit;

/**
 * KataWP Shortcodes
 * Handles all shortcodes for displaying readings, search, and saints
 * Integrated with date converter for Coptic calendar support
 */

class KataWP_Shortcodes {
    
    public function __construct() {
        add_shortcode('katawp_daily_readings', [$this, 'readings_shortcode']);
        add_shortcode('katawp_search', [$this, 'search_shortcode']);
        add_shortcode('katawp_saint', [$this, 'saint_shortcode']);
    }
    
    /**
     * Display daily readings shortcode
     * Supports both Gregorian and Coptic dates
     * Falls back to Gregorian if Coptic query returns no results
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
            $coptic_date = $this->gregorian_to_coptic_approximation($month, $day, $year);
        }
        
        // Try to fetch reading by Coptic date first
        $reading = $db->get_today_reading_by_coptic_date($coptic_date);
        
        // Fallback: Try Gregorian date if Coptic query returns nothing
        if (!$reading) {
            $reading = $db->get_today_reading($gregorian_date);
        }
        
        // Fallback 2: Log if both queries fail
        if (!$reading) {
            error_log('[KataWP] No reading found for Coptic: ' . $coptic_date . ' or Gregorian: ' . $gregorian_date);
            return '<div class="katawp-no-reading"><p>' . esc_html__('لا توجد قراءات لهذا اليوم', 'katawp') . '</p></div>';
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
     * Helper method to convert Gregorian to approximate Coptic date
     */
    private function gregorian_to_coptic_approximation($month, $day, $year) {
        $coptic_month = $month;
        $coptic_day = $day;
        $coptic_year = $year - 284; // Coptic calendar starts 284 AD
        
        return $coptic_month . '/' . $coptic_day . '/' . $coptic_year;
    }
    
    /**
     * Search shortcode for searching readings and saints
     */
    public function search_shortcode($atts) {
        $atts = shortcode_atts([
            'keyword' => '',
            'limit' => 20
        ], $atts);
        
        if (empty($atts['keyword'])) {
            return '';
        }
        
        $db = new KataWP_Database();
        $results = $db->search_synaxarium($atts['keyword']);
        
        if (empty($results)) {
            return '<div class="katawp-search-no-results"><p>' . esc_html__('لم يتم العثور على نتائج', 'katawp') . '</p></div>';
        }
        
        ob_start();
        ?>
        <div class="katawp-search-results">
            <?php foreach ($results as $result) : ?>
                <div class="katawp-search-result-item">
                    <h4><?php echo esc_html($result->saint_name); ?></h4>
                    <p><?php echo wp_trim_words(wp_kses_post($result->saint_biography), 50); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Saint/Synaxarium shortcode
     */
    public function saint_shortcode($atts) {
        $atts = shortcode_atts([
            'id' => 0
        ], $atts);
        
        if (empty($atts['id'])) {
            return '';
        }
        
        $db = new KataWP_Database();
        $saint = $db->get_synaxarium(intval($atts['id']));
        
        if (!$saint) {
            return '<div class="katawp-saint-not-found"><p>' . esc_html__('لم يتم العثور على القديس', 'katawp') . '</p></div>';
        }
        
        ob_start();
        ?>
        <div class="katawp-saint-info">
            <h3><?php echo esc_html($saint->saint_name); ?></h3>
            <?php if (!empty($saint->icon_url)) : ?>
                <img src="<?php echo esc_url($saint->icon_url); ?>" alt="<?php echo esc_attr($saint->saint_name); ?>" class="katawp-saint-icon">
            <?php endif; ?>
            <div class="katawp-saint-biography">
                <?php echo wp_kses_post($saint->saint_biography); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
