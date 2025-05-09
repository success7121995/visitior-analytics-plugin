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

        add_action('init', [$this, 'track_visitor']);

        ## Hook to get IP address from AJAX
        add_action('wp_ajax_get_geo_location_data', [$this, 'get_geo_location_data']);
        add_action('wp_ajax_nopriv_get_geo_location_data', [$this, 'get_geo_location_data']);
    }

    /**
     * Track visitor
     */
    public function track_visitor() {
        ## Skip tracking in admin are
        if (is_admin()) {
            return;
        }

        ## Get referrer
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

        ## Get site's hostname
        $site_hostname = wp_parse_url(home_url(), PHP_URL_HOST);

        ## Skip tracking if referrer is from the same site
        if (!empty($referrer)) {
            $referrer_host = wp_parse_url($referrer, PHP_URL_HOST);
            if ($referrer_host === $site_hostname) {
                return;
            }
        }

        ## Get visit time
        $visit_time = gmdate('Y-m-d H:i:s');

        ## Get user agent
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        ## Get device and browser information
        $device = $this -> get_device($user_agent) ?? null;
        $browser = $this -> get_browser($user_agent) ?? null;

        ## Get landing page
        $landing_page = home_url(add_query_arg([], $_SERVER['REQUEST_URI']));

        $visitor_data = [
            'landing_page' => $landing_page,
            'user_agent' => $user_agent,
            'device' => $device,
            'browser' => $browser,
            'visit_time' => $visit_time,
            'referrer' => $referrer  
        ];

        ## Insert visitor data into database
        $result = $this -> database_manager -> insert_visitor_data($visitor_data);

        if (!$result) {
            error_log('Error inserting visitor data');
            return;
        }

        ## Set record id in cookie temporarily
        setcookie('visitor_id', $result['id'], time() + 3600, '/');
    }

    /**
     * Get Geo Location Data
     */
    public function get_geo_location_data() {
        $id = $_POST['visitor_id'] ?? null;

        if ($id === null) {
            wp_send_json_error('No visitor\'s record id');
            return;
        }

        ## Get geo location data
        $data = isset($_POST['data']) ? json_decode(stripslashes($_POST['data']), true) : null;

        if ($data === null) {
            wp_send_json_error('No data');
            return;
        }

        ## Format data
        $additional_data = [
            'ip' => $data['ip'] ?? null,
            'network' => $data['network'] ?? null,
            'version' => $data['version'] ?? null,
            'city' => $data['city'] ?? null,
            'region' => $data['region'] ?? null,
            'region_code' => $data['region_code'] ?? null,
            'country_name' => $data['country_name'] ?? null,
            'country_code' => $data['country_code'] ?? null,
            'postal' => $data['postal'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'languages' => $data['languages'] ?? null,
            'timezone' => $data['timezone'] ?? null,
            'utc_offset' => $data['utc_offset'] ?? null,
            'country_calling_code' => $data['country_calling_code'] ?? null,
            'country_area' => $data['country_area'] ?? null,
            'asn' => $data['asn'] ?? null,
            'org' => $data['org'] ?? null
        ];

        ## Update visitor data
        $result = $this -> database_manager -> update_visitor_data($id, $additional_data);

        if (!$result) {
            wp_send_json_error('Error updating visitor data');
            return;
        }

        ## Remove visitor id from cookie
        setcookie('visitor_id', '', time() - 3600, '/');

        wp_send_json_success('Geo location data updated');
    }

    /**
     * Get device
     */
    private function get_device($user_agent) {
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $user_agent)) {
            return 'Mobile';
        } else if (preg_match('/ipad|tablet|playbook|silk/i', $user_agent)) {
            return 'Tablet';
        } else {
            return 'Desktop';
        } 
    }

    /**
     * Get browser
     */
    private function get_browser($user_agent) {
        if (preg_match('/MSIE/i', $user_agent)) {
            return 'Internet Explorer';
        } elseif (preg_match('/Firefox/i', $user_agent)) {
            return 'Firefox';
        } elseif (preg_match('/Chrome/i', $user_agent)) {
            return 'Chrome';
        } elseif (preg_match('/Safari/i', $user_agent)) {
            return 'Safari';
        } elseif (preg_match('/Opera/i', $user_agent)) {
            return 'Opera';
        } elseif (preg_match('/Edge/i', $user_agent)) {
            return 'Edge';
        }
    }
}