<?php
if (!defined('ABSPATH')) exit;

/**
 * KataWP Activation Handler
 * Automatically creates tables, imports data, and sets up pages
 */
class KataWP_Activation {
    
    public static function activate() {
        // Run activation only once
        if (get_option('katawp_activated')) {
            return;
        }
        
        // Create tables and import data
        self::setup_database();
        
        // Create pages
        self::create_pages();
        
        // Set activation flag
        update_option('katawp_activated', true);
        update_option('katawp_activation_date', current_time('mysql'));
    }
    
    /**
     * Setup database - create tables and import data
     */
    private static function setup_database() {
        $db = new KataWP_Database();
        $db->create_tables();
        $db->populate_from_existing_tables();
    }
    
    /**
     * Create necessary pages with shortcodes
     */
    private static function create_pages() {
        $pages = array(
            array(
                'post_title' => __('Daily Readings', 'katawp'),
                'post_content' => '[katawp_daily_readings]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'meta_key' => 'katawp_page_type',
                'meta_value' => 'readings'
            ),
            array(
                'post_title' => __('Search', 'katawp'),
                'post_content' => '[katawp_search]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'meta_key' => 'katawp_page_type',
                'meta_value' => 'search'
            ),
            array(
                'post_title' => __('Synaxarium', 'katawp'),
                'post_content' => '[katawp_synaxarium]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'meta_key' => 'katawp_page_type',
                'meta_value' => 'synaxarium'
            )
        );
        
        foreach ($pages as $page) {
            // Check if page already exists
            $existing = get_page_by_title($page['post_title']);
            if (!$existing) {
                $post_id = wp_insert_post($page);
                $meta_key = $page['meta_key'];
                $meta_value = $page['meta_value'];
                add_post_meta($post_id, $meta_key, $meta_value, true);
            }
        }
    }
}

// Hook activation
register_activation_hook(KATAWP_PLUGIN_FILE, array('KataWP_Activation', 'activate'));
