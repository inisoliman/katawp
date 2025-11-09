<?php
if (!defined('ABSPATH')) exit;

/**
 * KataWP REST API Handlers
 * Handles AJAX and REST API requests
 */

class KataWP_API_Handlers {
    
    public static function register_hooks() {
        // AJAX Search
        add_action('wp_ajax_katawp_search', array(__CLASS__, 'handle_search'));
        add_action('wp_ajax_nopriv_katawp_search', array(__CLASS__, 'handle_search'));
        
        // AJAX Get Reading
        add_action('wp_ajax_katawp_get_reading', array(__CLASS__, 'handle_get_reading'));
        add_action('wp_ajax_nopriv_katawp_get_reading', array(__CLASS__, 'handle_get_reading'));
        
        // AJAX Get Saints
        add_action('wp_ajax_katawp_get_saints', array(__CLASS__, 'handle_get_saints'));
        add_action('wp_ajax_nopriv_katawp_get_saints', array(__CLASS__, 'handle_get_saints'));
    }
    
    /**
     * Handle search requests
     */
    public static function handle_search() {
        check_ajax_referer('katawp_nonce');
        
        global $wpdb;
        $query = sanitize_text_field($_POST['query'] ?? '');
        
        if (empty($query)) {
            wp_send_json_error('Empty query');
        }
        
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}daily_readings WHERE title LIKE %s OR content LIKE %s LIMIT 20",
                '%' . $query . '%',
                '%' . $query . '%'
            )
        );
        
        wp_send_json_success($results);
    }
    
    /**
     * Handle get reading requests
     */
    public static function handle_get_reading() {
        check_ajax_referer('katawp_nonce');
        
        global $wpdb;
        $date = sanitize_text_field($_POST['date'] ?? current_time('Y-m-d'));
        
        $reading = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}daily_readings WHERE reading_date = %s",
                $date
            )
        );
        
        if ($reading) {
            wp_send_json_success($reading);
        } else {
            wp_send_json_error('Reading not found');
        }
    }
    
    /**
     * Handle get saints requests
     */
    public static function handle_get_saints() {
        check_ajax_referer('katawp_nonce');
        
        global $wpdb;
        
        $saints = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}saints ORDER BY saint_name ASC"
        );
        
        wp_send_json_success($saints);
    }
}

// Initialize
KataWP_API_Handlers::register_hooks();
