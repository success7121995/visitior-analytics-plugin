<?php
/**
 * Database Manager
 * 
 * This class is responsible for managing the database
 * 
 * @package WP Visitor Analytics
 */

class Database_Manager {
    public function __construct() {
        add_action('init', [$this, 'init_table']);

        ## Hook to get total visitors by month from AJAX
        add_action('wp_ajax_get_total_visitors_by_month', [$this, 'get_total_visitors_by_month']);
        add_action('wp_ajax_nopriv_get_total_visitors_by_month', [$this, 'get_total_visitors_by_month']);

        ## Hook to get total visitors by day from AJAX
        add_action('wp_ajax_get_total_visitors_by_day', [$this, 'get_total_visitors_by_day']);
        add_action('wp_ajax_nopriv_get_total_visitors_by_day', [$this, 'get_total_visitors_by_day']);
    }

    /**
     * Initialize visitor analytics table
     */
    public static function init_table() {
        global $wpdb;
        
        $table_name = $wpdb -> prefix . 'visitor_analytics';

        ## Check if the table already exists
        if ($wpdb -> get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
            return;
        }

        ## Try to create the table and log any errors
        try {
            ## SQL to create the table
            $sql = "
                CREATE TABLE $table_name (
                    id VARCHAR(255) NOT NULL,
                    visit_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                    user_agent TEXT NOT NULL,
                    landing_page TEXT NOT NULL,
                    device TEXT NOT NULL,
                    browser TEXT NOT NULL,
                    referrer TEXT,
                    ip TEXT,
                    network TEXT,
                    version TEXT,
                    city TEXT,
                    region TEXT,
                    region_code TEXT,
                    country_name TEXT,
                    country_code TEXT,
                    postal TEXT,
                    latitude TEXT,
                    longitude TEXT,
                    languages TEXT,
                    timezone TEXT,
                    utc_offset TEXT,
                    country_calling_code TEXT,
                    country_area TEXT,
                    asn TEXT,
                    org TEXT,
                    PRIMARY KEY (id)
                )
            ";
    
            ## Include upgrade.php file to use dbDelta function
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            if ($wpdb -> last_error) {
                throw new Exception($wpdb -> last_error);
            }
        } catch (Exception $e) {
            error_log('Error creating table: ' . $e -> getMessage());
        }
    }

    /**
     * Insert necessary visitor data
     * @param array $data The data to insert
     * @return array | bool Array with success status and id if successful, false otherwise
     */
    public function insert_visitor_data($data) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'visitor_analytics';
        
        ## Generate unique ID
        $data['id'] = uniqid();

        $wpdb->insert($table_name, $data);

        if ($wpdb->last_error) {
            error_log('Database error: ' . $wpdb->last_error);
            return false;
        }

        return ['id' => $data['id']];
    }

    /**
     * Get visitor current single record
     * @param string $id The id of the visitor record
     * @return bool True if visitor data exists, false otherwise
     */
    public function get_visitor_current_record($id) {
        global $wpdb;

        $table_name = $wpdb -> prefix . 'visitor_analytics';
        $visitor_data = $wpdb -> get_row($wpdb -> prepare("
            SELECT * FROM $table_name 
            WHERE id = %s
            ORDER BY visit_time DESC
            LIMIT 1
        ", $id), ARRAY_A);
        
        if (empty($visitor_data)) {
            return false;
        }

        return true;
    }

    /**
     * Update optional visitor data
     * @param string $id The id of the visitor record
     * @param array $data The data to update
     * @return array Array with success status if successful, false otherwise
     */
    public function update_visitor_data($id, $data) {
        global $wpdb;

        $table_name = $wpdb -> prefix . 'visitor_analytics';
        $wpdb -> update($table_name, $data, ['id' => $id]);

        if ($wpdb -> last_error) {
            error_log('Database error: ' . $wpdb -> last_error);
            return ['success' => false];
        }

        return ['success' => true];
    }

    /********* Get Figures *********/

    /**
     * Get total visitors
     * @return int The total number of visitors
     */
    public function get_total_visitors() {
        global $wpdb;

        $table_name = $wpdb -> prefix . 'visitor_analytics';
        $total_visitors = $wpdb -> get_var("SELECT COUNT(*) FROM $table_name");
        return $total_visitors;
    }

    /**
     * Get total visitors grouped by a specific column
     * 
     * @param string $column The column name to group by (e.g., 'ip', 'country_name', 'browser', 'device')
     * @return array The total number of visitors grouped by the given column
     */
    public function get_total_visitors_by($column) {
        global $wpdb;

        $allowed_columns = ['ip', 'network', 'landing_page', 'version', 'country_name', 'browser', 'device', 'city', 'region', 'region_code', 'postal', 'latitude', 'longitude', 'languages', 'timezone', 'utc_offset', 'country_calling_code', 'country_area', 'asn', 'org'];

        if (!in_array($column, $allowed_columns)) {
            throw new InvalidArgumentException("Invalid column name: " . htmlspecialchars($column));
        }

        $table_name = $wpdb -> prefix . 'visitor_analytics';

        $query = $wpdb->prepare("
            SELECT `$column` AS group_value, COUNT(*) AS total_visitors 
            FROM $table_name 
            GROUP BY `$column` 
            ORDER BY total_visitors DESC
        ");

        return $wpdb -> get_results($query, ARRAY_A);
    }
    
    /**
     * Get total visitors by month for a specific year
     * @return array The total number of visitors by month for the specified year
     */
    public function get_total_visitors_by_month() {
        global $wpdb;

        ## Get year from AJAX
        $year = isset($_POST['year']) ? (int) $_POST['year'] : null;

        if (!$year) {
            return ['error' => 'Year is required'];
        }

        $table_name = $wpdb -> prefix . 'visitor_analytics';

        $query_results = $wpdb->get_results($wpdb -> prepare("
            SELECT MONTH(visit_time) AS month, COUNT(*) AS total_visitors 
            FROM $table_name 
            WHERE YEAR(visit_time) = %d
            GROUP BY MONTH(visit_time) 
            ORDER BY MONTH(visit_time) ASC
        ", $year), ARRAY_A);

        $months = [
            'Jan', 'Feb', 'Mar', 'Apr',
            'May', 'Jun', 'Jul', 'Aug',
            'Sep', 'Oct', 'Nov', 'Dec'
        ];
        
        ## Initialize array with 0 values for each month
        $total_visitors_by_month = [];

        foreach ($months as $month) {
            $total_visitors_by_month[] = [
                'year' => $year,
                'month' => $month,
                'total_visitors' => 0
            ];
        }

        ## Populate results
        foreach ($query_results as $row) {
            $month_index = $row['month'] - 1; // 0-based index
            $total_visitors_by_month[$month_index]['total_visitors'] = (int) $row['total_visitors'];
        }

        // Send JSON response with the total visitors by month
        wp_send_json_success($total_visitors_by_month);
    }

    /**
     * Get total visitors by day for a specific month and year
     * @return array The total number of visitors by day for the specified month and year
     */
    public function get_total_visitors_by_day() {
        global $wpdb;

        ## Get year and month from AJAX
        $year = isset($_POST['year']) ? (int) $_POST['year'] : null;
        $month = isset($_POST['month']) ? (int) $_POST['month'] : null;

        if (!$year || !$month) {
            return ['error' => 'Year and month are required'];
        }

        $table_name = $wpdb -> prefix . 'visitor_analytics';

        $query_results = $wpdb->get_results($wpdb->prepare("
            SELECT DAY(visit_time) AS day, COUNT(*) AS total_visitors 
            FROM $table_name 
            WHERE YEAR(visit_time) = %d AND MONTH(visit_time) = %d
            GROUP BY DAY(visit_time) 
            ORDER BY DAY(visit_time) ASC
        ", $year, $month), ARRAY_A);

        ## Get the number of days in the month
        $days_in_month = $this -> get_days_in_month($month, $year);

        ## Initialize array with 0 values for each day of the month
        $total_visitors_by_day = [];

        ## Populate array with 0 values for each day of the month
        for ($day = 1; $day <= $days_in_month; $day++) {
            $total_visitors_by_day[] = [
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'total_visitors' => 0
            ];
        }

        ## Populate results
        foreach ($query_results as $row) {
            $day_index = $row['day'] - 1; // 0-based index
            $total_visitors_by_day[$day_index]['total_visitors'] = (int) $row['total_visitors'];
        }

        // Send JSON response with the total visitors by day
        wp_send_json_success($total_visitors_by_day);
    }

    /**
     * Get the number of days in a given month and year
     * @param int $month The month (1-12)
     * @param int $year The year
     * @return int The number of days in the month
     */
    private function get_days_in_month($month, $year) {
        // Handle February
        if ($month == 2) {
            return ($year % 4 == 0 && ($year % 100 != 0 || $year % 400 == 0)) ? 29 : 28;
        }

        // Handle months with 30 days
        if (in_array($month, [4, 6, 9, 11])) {
            return 30;
        }

        // Handle months with 31 days
        return 31;
    }

}