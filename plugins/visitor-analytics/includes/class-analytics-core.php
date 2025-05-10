<?php
/**
 * Analytics Core Class
 * 
 * @package WP Visitor Analytics
 * 
 * This class handles the initialization and management of the visitor analytics system.
 * It includes methods for initializing the database table, adding an admin menu, and rendering the admin pages.
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
            // Add top-level menu page
            add_menu_page(
                'Visitor Analytics', // Page title
                'Visitor Analytics', // Menu title
                'manage_options', // Capability
                'visitor-analytics', // Menu slug
                [$this, 'render_dashboard_page'], // Callback function
                'dashicons-chart-area', // Icon
                30 // Order
            );

            // Override default submenu (so clicking main menu opens dashboard)
            add_submenu_page(
                'visitor-analytics', // Parent slug
                'Dashboard', // Page title
                'Dashboard', // Submenu title
                'manage_options', // Capability
                'visitor-analytics', // Menu slug (same as main menu slug → highlights main menu)
                [$this, 'render_dashboard_page'] // Callback function
            );

            // Add settings submenu
            add_submenu_page(
                'visitor-analytics', // Parent slug
                'Settings', // Page title
                'Settings', // Submenu title
                'manage_options', // Capability
                'visitor-analytics-settings', // Menu slug
                [$this, 'render_settings_page'] // Callback function
            );
        } catch (Exception $e) {
            error_log('Error adding admin menu: ' . $e -> getMessage());
        }
    }

    /**
     * Render dashboard admin page
     */
    public function render_dashboard_page() {
        require_once plugin_dir_path(__FILE__) . '../templates/admin-dashboard.php';
    }

    /**
     * Render settings admin page
     */
    public function render_settings_page() {
        require_once plugin_dir_path(__FILE__) . '../templates/admin-settings.php';
    }
}
