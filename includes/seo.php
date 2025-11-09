<?php
if (!defined('ABSPATH')) exit;

/**
 * KataWP SEO Module
 * Handles all SEO optimization for the plugin
 */

class KataWP_SEO {
    
    public function __construct() {
        // Add head tags
        add_action('wp_head', array($this, 'add_meta_tags'));
        // Add structured data
        add_action('wp_head', array($this, 'add_structured_data'));
        // Register sitemap
        add_action('init', array($this, 'register_sitemap'));
    }
    
    /**
     * Add meta tags for each reading
     */
    public function add_meta_tags() {
        if (is_singular('page')) {
            global $post;
            $page_type = get_post_meta($post->ID, 'katawp_page_type', true);
            
            if (!empty($page_type)) {
                $title = get_the_title();
                $excerpt = wp_trim_words(get_the_excerpt(), 20);
                $url = get_permalink();
                
                echo '<meta name="description" content="' . esc_attr($excerpt) . '" />' . "\n";
                echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
                echo '<meta property="og:description" content="' . esc_attr($excerpt) . '" />' . "\n";
                echo '<meta property="og:url" content="' . esc_url($url) . '" />' . "\n";
                echo '<meta property="og:type" content="website" />' . "\n";
                echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
                echo '<meta name="twitter:card" content="summary" />' . "\n";
                echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
                echo '<meta name="twitter:description" content="' . esc_attr($excerpt) . '" />' . "\n";
            }
        }
    }
    
    /**
     * Add structured data (Schema.org)
     */
    public function add_structured_data() {
        if (is_singular('page')) {
            global $post, $wpdb;
            $page_type = get_post_meta($post->ID, 'katawp_page_type', true);
            
            if ($page_type === 'readings') {
                $structured_data = array(
                    '@context' => 'https://schema.org',
                    '@type' => 'Event',
                    'name' => get_the_title(),
                    'description' => wp_trim_words(get_the_excerpt(), 30),
                    'url' => get_permalink(),
                    'image' => get_the_post_thumbnail_url(),
                    'organizer' => array(
                        '@type' => 'Organization',
                        'name' => get_bloginfo('name'),
                        'url' => home_url()
                    )
                );
                
                echo '<script type="application/ld+json">' . json_encode($structured_data, JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
            }
        }
    }
    
    /**
     * Register XML sitemap
     */
    public function register_sitemap() {
        add_rewrite_rule(
            '^katawp-sitemap\.xml$',
            'index.php?katawp_sitemap=1',
            'top'
        );
        add_rewrite_tag('%katawp_sitemap%', '([0-9]+)');
        
        // Handle sitemap requests
        add_action('template_redirect', array($this, 'generate_sitemap'));
    }
    
    /**
     * Generate XML sitemap
     */
    public function generate_sitemap() {
        if (get_query_var('katawp_sitemap')) {
            global $wpdb;
            
            header('Content-Type: application/xml; charset=UTF-8');
            echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            
            // Add plugin pages
            $pages = get_posts(array(
                'post_type' => 'page',
                'numberposts' => -1,
                'meta_key' => 'katawp_page_type',
                'post_status' => 'publish'
            ));
            
            foreach ($pages as $page) {
                echo '  <url>' . "\n";
                echo '    <loc>' . esc_url(get_permalink($page->ID)) . '</loc>' . "\n";
                echo '    <lastmod>' . mysql2date('Y-m-d', $page->post_modified) . '</lastmod>' . "\n";
                echo '    <changefreq>daily</changefreq>' . "\n";
                echo '    <priority>0.8</priority>' . "\n";
                echo '  </url>' . "\n";
            }
            
            echo '</urlset>';
            die();
        }
    }
}

// Initialize SEO
if (is_admin() || !is_admin()) {
    new KataWP_SEO();
}
