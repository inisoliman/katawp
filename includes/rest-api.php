<?php

if (!defined('ABSPATH')) exit;

class KataWP_REST_API {
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    
    public function register_routes() {
        register_rest_route('katawp/v1', '/readings', [
            'methods' => 'GET',
            'callback' => [$this, 'get_readings'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route('katawp/v1', '/readings/(?P<date>[0-9-]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_reading_by_date'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route('katawp/v1', '/search', [
            'methods' => 'GET',
            'callback' => [$this, 'search_readings'],
            'permission_callback' => '__return_true',
        ]);
    }
    
    public function get_readings($request) {
        $db = new KataWP_Database();
        $reading = $db->get_today_reading();
        
        if (!$reading) {
            return new WP_Error('no_data', 'No readings found', ['status' => 404]);
        }
        
        return new WP_REST_Response($reading, 200);
    }
    
    public function get_reading_by_date($request) {
        $date = $request->get_param('date');
        $db = new KataWP_Database();
        $reading = $db->get_today_reading($date);
        
        if (!$reading) {
            return new WP_Error('not_found', 'Reading not found', ['status' => 404]);
        }
        
        return new WP_REST_Response($reading, 200);
    }
    
    public function search_readings($request) {
        $query = $request->get_param('q');
        
        if (empty($query)) {
            return new WP_Error('empty_query', 'Search query is required', ['status' => 400]);
        }
        
        $db = new KataWP_Database();
        $results = $db->search_readings($query);
        
        return new WP_REST_Response($results, 200);
    }
}
