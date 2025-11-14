<?php
/**
 * فئة إدارة قاعدة البيانات
 */
if (!defined('ABSPATH')) {
    exit;
}

class KataWP_Database {
    
    public $wpdb;
    public $readings_table;
    public $synaxarium_table;
    public $epistle_table;
    public $gospel_table;
    public $apostles_table;
    public $liturgy_table;
    public $saints_table;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        
        // تعريف أسماء الجداول
        $this->readings_table = KATAWP_DB_PREFIX . 'daily_readings';
        $this->synaxarium_table = KATAWP_DB_PREFIX . 'synaxarium';
        $this->epistle_table = KATAWP_DB_PREFIX . 'epistle';
        $this->gospel_table = KATAWP_DB_PREFIX . 'gospel';
        $this->apostles_table = KATAWP_DB_PREFIX . 'apostles';
        $this->liturgy_table = KATAWP_DB_PREFIX . 'liturgy';
        $this->saints_table = KATAWP_DB_PREFIX . 'saints';
    }
    
    /**
     * إنشاء جداول قاعدة البيانات
     */
    public function create_tables() {
        $charset_collate = $this->wpdb->get_charset_collate();
        $sql = "";

        // جدول القراءات اليومية (متوافق مع bible_ar)
        $sql .= "CREATE TABLE IF NOT EXISTS {$this->readings_table} (
            ID INT(11) NOT NULL,
            Book_Name VARCHAR(50) NOT NULL,
            book INT(4) NOT NULL,
            Chapter INT(4) NOT NULL,
            Verse INT(4) NOT NULL,
            Text TEXT NOT NULL,
            PRIMARY KEY (ID)
        ) $charset_collate;";

        // جدول السنكسار (هيكل موحد لجميع جداول gr_*)
        // هذا هيكل عام، قد يحتاج إلى تعديل بناءً على تحليل أعمق لجميع جداول gr_*
        $sql .= "CREATE TABLE IF NOT EXISTS {$this->synaxarium_table} (
            ID INT(11) NOT NULL,
            Week DOUBLE DEFAULT NULL,
            Day VARCHAR(300) DEFAULT NULL,
            DayName VARCHAR(300) DEFAULT NULL,
            Seasonal_Tune VARCHAR(300) DEFAULT NULL,
            Weather_Prayers VARCHAR(300) DEFAULT NULL,
            V_Psalm_Ref VARCHAR(300) DEFAULT NULL,
            V_Gospel_Ref VARCHAR(300) DEFAULT NULL,
            M_Psalm_Ref VARCHAR(300) DEFAULT NULL,
            M_Gospel_Ref VARCHAR(300) DEFAULT NULL,
            P_Gospel_Ref VARCHAR(300) DEFAULT NULL,
            C_Gospel_Ref VARCHAR(300) DEFAULT NULL,
            X_Gospel_Ref VARCHAR(300) DEFAULT NULL,
            L_Psalm_Ref VARCHAR(300) DEFAULT NULL,
            L_Gospel_Ref VARCHAR(300) DEFAULT NULL,
            Prophecy VARCHAR(500) DEFAULT NULL,
            Month_Number SMALLINT(6) DEFAULT NULL,
            Month_Name VARCHAR(255) DEFAULT NULL,
            Other VARCHAR(255) DEFAULT NULL,
            Day_Tune VARCHAR(255) DEFAULT NULL,
            Season VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY (ID)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * الحصول على قراءة اليوم
     */
    public function get_reading_by_date($date) {
        $day_of_year = date('z', strtotime($date)) + 1;

        $synaxarium_entry = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->synaxarium_table} WHERE ID = %d",
                $day_of_year
            )
        );

        if (!$synaxarium_entry) {
            return null;
        }

        $reading = new stdClass();
        $reading->synaxarium = $synaxarium_entry;
        
        // استرجاع قراءة الإنجيل
        $reading->gospel_reading = $this->get_reading_by_ref($synaxarium_entry->V_Gospel_Ref);

        // استرجاع قراءة البولس
        $reading->pauline_reading = $this->get_reading_by_ref($synaxarium_entry->P_Gospel_Ref);

        return $reading;
    }

    private function get_reading_by_ref($ref) {
        if (empty($ref)) {
            return null;
        }

        // مثال على مرجع: "Matthew 12:24-34"
        preg_match('/(\d?\s?[a-zA-Z]+)\s(\d+):(\d+)-?(\d+)?/', $ref, $matches);

        if (count($matches) < 4) {
            return null;
        }

        $book_name = trim($matches[1]);
        $chapter = $matches[2];
        $verse_start = $matches[3];
        $verse_end = isset($matches[4]) ? $matches[4] : $verse_start;

        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->readings_table} WHERE Book_Name = %s AND Chapter = %d AND Verse >= %d AND Verse <= %d",
                $book_name, $chapter, $verse_start, $verse_end
            )
        );
    }
}
