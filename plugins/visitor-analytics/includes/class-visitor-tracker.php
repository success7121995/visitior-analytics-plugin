<?php 
/**
 * Visitor Tracker Class
 * 
 * @package WP Visitor Analytics
 * 
 * This class handles the tracking of visitor data.
 */

class Visitor_Tracker {
    private $database_manager;

    public function __construct() {
        $this -> database_manager = new Database_Manager();
        
        // 只用 AJAX tracking
        add_action('wp_ajax_get_geo_location_data', [$this, 'get_geo_location_data']);
        add_action('wp_ajax_nopriv_get_geo_location_data', [$this, 'get_geo_location_data']);
    }

    /**
     * Insert visitor data from client (AJAX)
     */
    public function get_geo_location_data() {
        $data = isset($_POST['data']) ? json_decode(stripslashes($_POST['data']), true) : null;
        if (!$data || empty($data['ip'])) {
            wp_send_json_error('No valid data');
            return;
        }
        $result = $this->database_manager->insert_visitor_data($data);
        if (!$result) {
            wp_send_json_error('Error inserting visitor data');
            return;
        }
        wp_send_json_success('Visitor data inserted');
    }

    /**
     * Check if the request is from a bot
     */
    private function is_bot() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $bot_patterns = [
            'bot', 'crawler', 'spider', 'slurp', 'search', 'mediapartners',
            'nagios', 'curl', 'wget', 'monitoring', 'validator'
        ];
        foreach ($bot_patterns as $pattern) {
            if (stripos($user_agent, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }
}
