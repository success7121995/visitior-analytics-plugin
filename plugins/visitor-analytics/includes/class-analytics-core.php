<?php
/**
 * Analytics Core Class
 * 
 * @package WP Visitor Analytics
 * 
 * This class handles the initialization and management of the visitor analytics system.
 * It includes methods for initializing the database table, adding an admin menu, and rendering the admin page.
 */

class Analytics_Core {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        try {
            ## Add top-level menu page
            add_menu_page(
                'Visitor Analytics',
                'Visitor Analytics',
                'manage_options',
                'visitor-analytics',
                [$this, 'render_admin_page'],
                'dashicons-chart-area',
                30
            );
        } catch (Exception $e) {
            error_log('Error adding admin menu: ' . $e -> getMessage());
        }
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        require_once plugin_dir_path(__FILE__) . '../templates/admin-page.php';
    }
}