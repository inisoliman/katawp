<?php
if (!defined('ABSPATH')) exit;

/**
 * KataWP Cache Manager
 * Handles caching for readings and other data
 */

class KataWP_Cache {
    
    const CACHE_PREFIX = 'katawp_';
    const CACHE_DURATION = 24 * HOUR_IN_SECONDS; // 24 hours
    
    /**
     * Get cached reading
     */
    public static function get_reading($date) {
        $cache_key = self::CACHE_PREFIX . 'reading_' . $date;
        $cached = wp_cache_get($cache_key, 'katawp');
        
        if (false !== $cached) {
            return $cached;
        }
        return false;
    }
    
    /**
     * Set reading cache
     */
    public static function set_reading($date, $data) {
        $cache_key = self::CACHE_PREFIX . 'reading_' . $date;
        wp_cache_set($cache_key, $data, 'katawp', self::CACHE_DURATION);
    }
    
    /**
     * Get all saints cache
     */
    public static function get_saints() {
        $cache_key = self::CACHE_PREFIX . 'saints';
        $cached = wp_cache_get($cache_key, 'katawp');
        
        if (false !== $cached) {
            return $cached;
        }
        return false;
    }
    
    /**
     * Set saints cache
     */
    public static function set_saints($data) {
        $cache_key = self::CACHE_PREFIX . 'saints';
        wp_cache_set($cache_key, $data, 'katawp', self::CACHE_DURATION);
    }
    
    /**
     * Get synaxarium cache
     */
    public static function get_synaxarium($date) {
        $cache_key = self::CACHE_PREFIX . 'synaxarium_' . $date;
        $cached = wp_cache_get($cache_key, 'katawp');
        
        if (false !== $cached) {
            return $cached;
        }
        return false;
    }
    
    /**
     * Set synaxarium cache
     */
    public static function set_synaxarium($date, $data) {
        $cache_key = self::CACHE_PREFIX . 'synaxarium_' . $date;
        wp_cache_set($cache_key, $data, 'katawp', self::CACHE_DURATION);
    }
    
    /**
     * Invalidate reading cache
     */
    public static function invalidate_reading($date) {
        $cache_key = self::CACHE_PREFIX . 'reading_' . $date;
        wp_cache_delete($cache_key, 'katawp');
    }
    
    /**
     * Invalidate saints cache
     */
    public static function invalidate_saints() {
        $cache_key = self::CACHE_PREFIX . 'saints';
        wp_cache_delete($cache_key, 'katawp');
    }
    
    /**
     * Invalidate all cache
     */
    public static function invalidate_all() {
        wp_cache_flush();
    }
    
    /**
     * Get cache statistics
     */
    public static function get_stats() {
        global $wp_object_cache;
        
        return array(
            'hits' => $wp_object_cache->hits ?? 0,
            'misses' => $wp_object_cache->misses ?? 0,
            'cache_group' => 'katawp'
        );
    }
    
    /**
     * Setup cache groups
     */
    public static function setup_cache_groups() {
        wp_cache_add_global_groups('katawp');
    }
}

// Initialize cache
KataWP_Cache::setup_cache_groups();
