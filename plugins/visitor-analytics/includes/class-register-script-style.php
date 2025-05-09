<?php
/**
 * Register script and style
 * 
 * @package WP Visitor Analytics
 * 
 * This class handles the registration of scripts and styles for the visitor analytics plugin.
 * It includes methods for enqueuing scripts and styles, and for registering them with WordPress.
 */

class Register_Script_Style {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts() {
        ## Enqueue manage preference script
        wp_enqueue_script('visitor_analytics_manage-preference', plugin_dir_url(__FILE__) . '../assets/js/manage-preference.js', [], '1.0', true);

        ## Enqueue cookie script
        wp_enqueue_script('visitor_analytics_cookie', plugin_dir_url(__FILE__) . '../assets/js/cookie.js', [], '1.0', true);

        ## Enqueue ajax script
        wp_enqueue_script('visitor_analytics_get_geolocation', plugin_dir_url(__FILE__) . '../assets/js/get-geolocation.js', [], '1.0', true);
        
        ## Localize script for get geolocation
        wp_localize_script('visitor_analytics_get_geolocation', 'visitor_analytics_get_geolocation', [
            'ajaxurl' => admin_url('admin-ajax.php')
        ]);
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our plugin page
        if ($hook !== 'toplevel_page_visitor-analytics') {
            return;
        }

        // Register and enqueue admin styles
        wp_register_style('visitor_analytics_admin', plugin_dir_url(__FILE__) . '../assets/css/admin.css', [], '1.0');
        wp_enqueue_style('visitor_analytics_admin');

        // Register and enqueue admin scripts
        wp_register_script('visitor_analytics_admin', plugin_dir_url(__FILE__) . '../assets/js/admin.js', ['jquery'], '1.0', true);
        wp_enqueue_script('visitor_analytics_admin');
    }
}