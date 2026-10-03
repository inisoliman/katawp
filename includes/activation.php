<?php
if (!defined('ABSPATH')) exit;

/**
 * KataWP Activation Handler
 * Automatically creates pages and sets up initial configuration
 */

class KataWP_Activation {
    
    public static function activate() {
        // Run activation only once
        if (get_option('katawp_activated')) {
            return;
        }
        
        // Create pages
        self::create_pages();
        
        
        // Set activation flag
        update_option('katawp_activated', true);
        update_option('katawp_activation_date', current_time('mysql'));
    }
    
    /**
     * Create necessary pages with shortcodes
     */
    public static function create_pages() {
        $pages = array(
            array(
                'post_title'    => __('Daily Readings', 'katawp'),
                'post_content'  => '[katawp_readings]',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'meta_key'      => 'katawp_page_type',
                'meta_value'    => 'readings'
            ),
            array(
                'post_title'    => __('Search', 'katawp'),
                'post_content'  => '[katawp_search]',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'meta_key'      => 'katawp_page_type',
                'meta_value'    => 'search'
            ),
            array(
                'post_title'    => __('Synaxarium', 'katawp'),
                'post_content'  => '[katawp_synaxarium]',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'meta_key'      => 'katawp_page_type',
                'meta_value'    => 'synaxarium'
            ),
            array(
                'post_title'    => __('Saints', 'katawp'),
                'post_content'  => '[katawp_saints]',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'meta_key'      => 'katawp_page_type',
                'meta_value'    => 'saints'
            )
        );
        
        foreach ($pages as $page) {
            // Check if page already exists
$query = new WP_Query(array(
                'title' => $page['post_title'],
                'post_type' => 'page',
                'posts_per_page' => 1
            ));
            $existing = !empty($query->posts) ? $query->posts[0] : null;
            wp_reset_postdata();

            if (!$existing) {
                $post_id = wp_insert_post($page);
                $meta_key = $page['meta_key'];
                $meta_value = $page['meta_value'];
                add_post_meta($post_id, $meta_key, $meta_value, true);
            }
        }
    }
}
    
