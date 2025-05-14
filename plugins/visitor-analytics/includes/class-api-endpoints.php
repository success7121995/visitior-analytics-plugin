<?php
/**
 * API Endpoints class
 * 
 * @package WP Visitor Analytics
 */

class API_Endpoints {
    private $database_manager;

    public function __construct() {
        $this -> database_manager = new Database_Manager();
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route('visitor-analytics/v1', '/monthly-data', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_monthly_data'),
            'permission_callback' => array($this, 'check_permission'),
            'args' => array(
                'year' => array(
                    'required' => true,
                    'type' => 'integer',
                    'sanitize_callback' => 'absint'
                )
            )
        ));

        register_rest_route('visitor-analytics/v1', '/daily-data', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_daily_data'),
            'permission_callback' => array($this, 'check_permission'),
            'args' => array(
                'year' => array(
                    'required' => true,
                    'type' => 'integer',
                    'sanitize_callback' => 'absint'
                ),
                'month' => array(
                    'required' => true,
                    'type' => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => function($param) {
                        return $param >= 1 && $param <= 12;
                    }
                )
            )
        ));
    }

    /**
     * Check if user has permission to access the API
     * 
     * @return bool
     */
    public function check_permission() {
        return current_user_can('manage_options');
    }

    /**
     * Get monthly visitor data
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_monthly_data($request) {
        $year = $request->get_param('year');
        
        // Get data from database manager
        $data = $this->database_manager->get_total_visitors_by_month($year);
        
        if (is_wp_error($data)) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => $data->get_error_message()
            ), 400);
        }

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $data
        ));
    }

    /**
     * Get daily visitor data
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_daily_data($request) {
        $year = $request->get_param('year');
        $month = $request->get_param('month');
        
        // Get data from database manager
        $data = $this->database_manager->get_total_visitors_by_day($year, $month);
        
        if (is_wp_error($data)) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => $data->get_error_message()
            ), 400);
        }

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $data
        ));
    }
} 