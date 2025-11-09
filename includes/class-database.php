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
        
        // جدول القراءات اليومية
        $sql .= "CREATE TABLE IF NOT EXISTS {$this->readings_table} (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            gregorian_date DATE NOT NULL,
            coptic_month VARCHAR(50),
            coptic_day INT,
            coptic_year INT,
            holiday_name VARCHAR(255),
            holiday_description LONGTEXT,
            reading_type VARCHAR(100),
            synaxarium_id BIGINT(20),
            epistle_id BIGINT(20),
            gospel_id BIGINT(20),
            apostles_id BIGINT(20),
            liturgy_id BIGINT(20),
            saints_ids LONGTEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY unique_gregorian (gregorian_date),
            KEY idx_coptic_date (coptic_month, coptic_day, coptic_year),
            FULLTEXT KEY ft_holiday (holiday_name, holiday_description)
        ) $charset_collate;";
        
        // جدول السنكسار
        $sql .= "CREATE TABLE IF NOT EXISTS {$this->synaxarium_table} (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            saint_name VARCHAR(255) NOT NULL,
            saint_name_en VARCHAR(255),
            saint_biography LONGTEXT,
            celebration_date VARCHAR(100),
            icon_url VARCHAR(500),
            feast_type VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_name (saint_name),
            FULLTEXT KEY ft_search (saint_name, saint_biography)
        ) $charset_collate;";
        
        // جدول البولس (الرسالة)
        $sql .= "CREATE TABLE IF NOT EXISTS {$this->epistle_table} (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            book_name VARCHAR(255),
            chapter INT,
            verse_start INT,
            verse_end INT,
            text LONGTEXT,
            text_en LONGTEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_book (book_name, chapter),
            FULLTEXT KEY ft_search (text)
        ) $charset_collate;";
        
        // جدول الإنجيل
        $sql .= "CREATE TABLE IF NOT EXISTS {$this->gospel_table} (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            gospel_name VARCHAR(100),
            chapter INT,
            verse_start INT,
            verse_end INT,
            text LONGTEXT,
            text_en LONGTEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_gospel (gospel_name, chapter),
            FULLTEXT KEY ft_search (text)
        ) $charset_collate;";
        
        // جدول الرسل
        $sql .= "CREATE TABLE IF NOT EXISTS {$this->apostles_table} (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            reading_text LONGTEXT,
            reading_text_en LONGTEXT,
            source VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FULLTEXT KEY ft_search (reading_text)
        ) $charset_collate;";
        
        // جدول القداس
        $sql .= "CREATE TABLE IF NOT EXISTS {$this->liturgy_table} (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            liturgy_name VARCHAR(255),
            liturgy_type VARCHAR(100),
            content LONGTEXT,
            content_en LONGTEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_type (liturgy_type)
        ) $charset_collate;";
        
        // جدول القديسين
        $sql .= "CREATE TABLE IF NOT EXISTS {$this->saints_table} (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            name_en VARCHAR(255),
            biography LONGTEXT,
            feast_date VARCHAR(100),
            icon_url VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FULLTEXT KEY ft_search (name, biography)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * الحصول على قراءة اليوم
     */
    public function get_today_reading($date = null) {
        if (null === $date) {
            $date = date('Y-m-d');
        }
        
        $reading = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->readings_table} WHERE gregorian_date = %s",
                $date
            )
        );
        
        if ($reading) {
            $reading->synaxarium = $this->get_synaxarium($reading->synaxarium_id);
            $reading->epistle = $this->get_epistle($reading->epistle_id);
            $reading->gospel = $this->get_gospel($reading->gospel_id);
            $reading->apostles = $this->get_apostles($reading->apostles_id);
            $reading->liturgy = $this->get_liturgy($reading->liturgy_id);
        }
        
        return $reading;
    }
    
    /**
     * Get reading by Coptic date
     */
    public function get_today_reading_by_coptic_date($coptic_date) {
        if (empty($coptic_date)) {
            return null;
        }
        
        $coptic_parts = explode('/', $coptic_date);
        if (count($coptic_parts) !== 3) {
            return null;
        }
        
        $coptic_month = intval($coptic_parts[0]);
        $coptic_day = intval($coptic_parts[1]);
        $coptic_year = intval($coptic_parts[2]);
        
        $reading = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->readings_table} WHERE coptic_month = %d AND coptic_day = %d AND coptic_year = %d",
                $coptic_month, $coptic_day, $coptic_year
            )
        );
        
        if ($reading) {
            $reading->synaxarium = $this->get_synaxarium($reading->synaxarium_id);
            $reading->epistle = $this->get_epistle($reading->epistle_id);
            $reading->gospel = $this->get_gospel($reading->gospel_id);
            $reading->apostles = $this->get_apostles($reading->apostles_id);
            $reading->liturgy = $this->get_liturgy($reading->liturgy_id);
        }
        
        return $reading;
    }
    
    /**
     * Get synaxarium by ID
     */
    public function get_synaxarium($id) {
        if (empty($id)) {
            return null;
        }
        
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->synaxarium_table} WHERE id = %d",
                $id
            )
        );
    }
    
    /**
     * Get epistle by ID
     */
    public function get_epistle($id) {
        if (empty($id)) {
            return null;
        }
        
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->epistle_table} WHERE id = %d",
                $id
            )
        );
    }
    
    /**
     * Get gospel by ID
     */
    public function get_gospel($id) {
        if (empty($id)) {
            return null;
        }
        
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->gospel_table} WHERE id = %d",
                $id
            )
        );
    }
    
    /**
     * Get apostles by ID
     */
    public function get_apostles($id) {
        if (empty($id)) {
            return null;
        }
        
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->apostles_table} WHERE id = %d",
                $id
            )
        );
    }
    
    /**
     * Get liturgy by ID
     */
    public function get_liturgy($id) {
        if (empty($id)) {
            return null;
        }
        
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->liturgy_table} WHERE id = %d",
                $id
            )
        );
    }
    
    /**
     * Get all synaxarium entries
     */
    public function get_all_synaxarium($limit = 50, $offset = 0) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->synaxarium_table} ORDER BY celebration_date ASC LIMIT %d OFFSET %d",
                $limit, $offset
            )
        );
    }
    
    /**
     * Search synaxarium
     */
    public function search_synaxarium($keyword) {
        if (empty($keyword)) {
            return [];
        }
        
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->synaxarium_table} WHERE MATCH (saint_name, saint_biography) AGAINST (%s IN BOOLEAN MODE)",
                $keyword
            )
        );
    }

    	/**
	 * Populate tables from existing gr_* tables
	 * Called during plugin activation
	 */
	/**
     * استيراد البيانات من الجداول القديمة برموز آمنة وتوافق صحيح
     * تحويل من gr_days/bible_ar إلى wp_katawp_* جداول
     */
    public function populate_from_existing_tables() {
        global $wpdb;
        
        // تحقق من وجود جداول المصدر
        if (!$this->table_exists('gr_days')) {
            return false;
        }
        
        try {
            // 1. استيراد السريانيين (Synaxarium) من gr_days
            $gr_days = $wpdb->get_results("SELECT * FROM gr_days");
            
            foreach ($gr_days as $day) {
                // حساب التاريخ الغريغوري (يوليو 1 من سنة معينة)
                $month = intval($day->Month_Number);
                $day_num = intval($day->Day);
                
                $wpdb->insert(
                    $this->synaxarium_table,
                    array(
                        'saint_name' => $day->DayName ?? $day->Month_Name,
                        'saint_biography' => $day->Other,
                        'celebration_date' => sprintf("%02d/%02d", $month, $day_num),
                        'feast_type' => $day->Season,
                    ),
                    array('%s', '%s', '%s', '%s')
                );
            }
            
            // 2. استيراد القراءات اليومية من gr_days والمراجع
            foreach ($gr_days as $day) {
                $gregorian_date = $this->convert_coptic_to_gregorian(
                    intval($day->Month_Number),
                    intval($day->Day),
                    (int)date('Y')
                );
                
                // البحث عن السريانى الذي تم إدراجه
                $synaxarium_id = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM {$this->synaxarium_table} WHERE saint_name = %s LIMIT 1",
                    $day->DayName ?? $day->Month_Name
                ));
                
                $wpdb->insert(
                    $this->readings_table,
                    array(
                        'gregorian_date' => $gregorian_date,
                        'coptic_month' => $day->Month_Name,
                        'coptic_day' => intval($day->Day),
                        'coptic_year' => 0,
                        'holiday_name' => $day->DayName,
                        'holiday_description' => $day->Other,
                        'synaxarium_id' => $synaxarium_id,
                        'reading_type' => $day->Season,
                    ),
                    array('%s', '%s', '%d', '%d', '%s', '%s', '%d', '%s')
                );
            }
            
            return true;
        } catch (Exception $e) {
            error_log('KataWP populate error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * تحويل التاريخ القبطي إلى غريغوري (تحويل تقريبي)
     */
    private function convert_coptic_to_gregorian($coptic_month, $coptic_day, $coptic_year) {
        // التاريخ القبطي يبدأ في 29 أغسطس (في السنة الكبيسة) أو 30 أغسطس
        $gregorian_year = $coptic_year + 283; // تحويل تقريبي
        $coptic_epoch = new DateTime('1972-09-11'); // بداية التقويم القبطي في التقويم الغريغوري
        
        // حساب الأيام من بداية السنة القبطية
        $days_in_year = (($coptic_month - 1) * 30) + $coptic_day;
        
        $date = clone $coptic_epoch;
        $date->modify("+{$gregorian_year} years +{$days_in_year} days");
        
        return $date->format('Y-m-d');
    }
	
	/**
	 * Check if table exists
	 */
private function table_exists($table_name) {
			global $wpdb;
			$result = $wpdb->query("SHOW TABLES LIKE '" . $table_name . "'");
			return $result > 0;
		}

