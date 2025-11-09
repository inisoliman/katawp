<?php
/**
 * Plugin Name: كاتامرس - القراءات اليومية المتقدمة | Katawp - Advanced Daily Readings
 * Description: إضافة شاملة وقوية لعرض القراءات اليومية، السنكسار، الإنجيل، والبولس مع دعم تعدد اللغات والبحث المتقدم
 * Version: 1.0.0
 * Author: inisoliman
 * Author URI: https://github.com/inisoliman
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /languages
 * Text Domain: katawp
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

// التحقق من أمان الملف
if (!defined('ABSPATH')) {
    exit;
}

// تعريف الثوابت الأساسية للإضافة
define('KATAWP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KATAWP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KATAWP_PLUGIN_VERSION', '1.0.0');
define('KATAWP_DB_PREFIX', $GLOBALS['wpdb']->prefix . 'katawp_');

// تحميل ملفات الإضافة الأساسية
require_once KATAWP_PLUGIN_DIR . 'includes/class-database.php';
require_once KATAWP_PLUGIN_DIR . 'includes/functions.php';
require_once KATAWP_PLUGIN_DIR . 'includes/shortcodes.php';
require_once KATAWP_PLUGIN_DIR . 'includes/widgets.php';
require_once KATAWP_PLUGIN_DIR . 'includes/rest-api.php';

// تحميل ملفات الإدارة (Admin)
if (is_admin()) {
    require_once KATAWP_PLUGIN_DIR . 'admin/admin-panel.php';
}

/**
 * فئة رئيسية للإضافة
 */
class KataWP {
    
    private static $instance = null;
    public $db;
    public $importer;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        // تحميل نطاقات الترجمة
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        
        // تفعيل الإضافة
        register_activation_hook(__FILE__, [$this, 'activate']);
        
        // إلغاء تفعيل الإضافة
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        
        // تحميل الأصول (CSS/JS)
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_assets']);
        
        // تهيئة قاعدة البيانات
        $this->db = new KataWP_Database();
        
        // تهيئة باقي المكونات
        $this->init_components();
    }
    
    /**
     * تحميل نطاقات الترجمة
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'katawp',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
    
    /**
     * تفعيل الإضافة
     */
    public function activate() {
        // إنشاء الجداول
        $this->db->create_tables();
        
        // استيراد البيانات
        require_once KATAWP_PLUGIN_DIR . 'includes/db-importer.php';
        $importer = new KataWP_DB_Importer();
        $importer->import_data();
        
        // إضافة خيارات افتراضية
        add_option('katawp_settings', $this->get_default_settings());
        
        flush_rewrite_rules();
    }
    
    /**
     * إلغاء تفعيل الإضافة
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * الإعدادات الافتراضية للإضافة
     */
    private function get_default_settings() {
        return [
            'language' => 'ar',
            'enable_search' => true,
            'enable_dark_mode' => true,
            'enable_social_sharing' => true,
            'enable_email_notifications' => true,
            'enable_pdf_export' => true,
            'enable_timeline' => true,
            'enable_calendar' => true,
            'items_per_page' => 10,
        ];
    }
    
    /**
     * تحميل الأصول (Frontend)
     */
    public function enqueue_assets() {
        // CSS
        wp_enqueue_style(
            'katawp-style',
            KATAWP_PLUGIN_URL . 'assets/css/katawp.css',
            [],
            KATAWP_PLUGIN_VERSION
        );
        
        // JavaScript
        wp_enqueue_script(
            'katawp-script',
            KATAWP_PLUGIN_URL . 'assets/js/katawp.js',
            ['jquery'],
            KATAWP_PLUGIN_VERSION,
            true
        );
        
        // تمرير البيانات للجافاسكريبت
        wp_localize_script('katawp-script', 'KataWP', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'plugin_url' => KATAWP_PLUGIN_URL,
            'nonce' => wp_create_nonce('katawp_nonce'),
            'language' => get_option('katawp_language', 'ar'),
        ]);
    }
    
    /**
     * تحميل الأصول (Admin)
     */
    public function admin_enqueue_assets() {
        // CSS
        wp_enqueue_style(
            'katawp-admin-style',
            KATAWP_PLUGIN_URL . 'assets/css/admin.css',
            [],
            KATAWP_PLUGIN_VERSION
        );
        
        // JavaScript
        wp_enqueue_script(
            'katawp-admin-script',
            KATAWP_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            KATAWP_PLUGIN_VERSION,
            true
        );
    }
    
    /**
     * تهيئة المكونات الرئيسية
     */
    private function init_components() {
        // تهيئة الشورت كود
        new KataWP_Shortcodes();
        
        // تهيئة الويدجت
        add_action('widgets_init', function() {
            register_widget('KataWP_Widget_Readings');
            register_widget('KataWP_Widget_Search');
        });
        
        // تهيئة REST API
        new KataWP_REST_API();
    }
}

// إنشاء نسخة من الفئة الرئيسية
KataWP::get_instance();
