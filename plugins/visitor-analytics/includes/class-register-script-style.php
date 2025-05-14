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


        // Register and enqueue export-data.js
        wp_register_script('visitor_analytics_export-data', plugin_dir_url(__FILE__) . '../assets/js/export-data.js', [], '1.0', true);
        wp_enqueue_script('visitor_analytics_export-data');

        // Register and enqueue Chart.js and its plugins in correct order
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js', [], '4.4.1', true);
        wp_enqueue_script('chartjs-datalabels', 'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0', ['chart-js'], '2.2.0', true);

        // Register and enqueue chart.js
        wp_register_script('visitor_analytics_chart', plugin_dir_url(__FILE__) . '../assets/js/chart.js', ['chart-js', 'chartjs-datalabels'], '1.0', true);
        wp_enqueue_script('visitor_analytics_chart');

        // Register and enqueue admin scripts
        wp_register_script('visitor_analytics_admin', plugin_dir_url(__FILE__) . '../assets/js/admin.js', ['jquery'], '1.0', true);
        wp_enqueue_script('visitor_analytics_admin');

        // Localize script for chart
        wp_localize_script('visitor_analytics_chart', 'visitor_analytics_chart', [
            'ajaxurl' => admin_url('admin-ajax.php')
        ]);
    }
}