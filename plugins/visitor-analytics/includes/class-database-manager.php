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
        $visitor_data = $wpdb -> get_row($wpdb -> prepare("SELECT * FROM $table_name WHERE id = %s", $id), ARRAY_A);
        
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
}