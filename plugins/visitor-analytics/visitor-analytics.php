<?php
/*
Plugin Name: WP Visitor Analytics
Description: A plugin to track visitor and visualize the data in a dashboard
Version: 1.0
Author: Stanford Tse
*/

## Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

## Include required files
require_once plugin_dir_path(__FILE__) . 'includes/class-analytics-core.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-database-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-display-figure.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-visitor-tracker.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-register-script-style.php';

## Initialize plugin
function wp_visitor_analytics_init() {
    $analytics_core = new Analytics_Core();
    $register_script_style = new Register_Script_Style();
    $visitor_tracker = new Visitor_Tracker();
    $database_manager = new Database_Manager();
    $display_figure = new Display_Figure();
}

add_action('plugins_loaded', 'wp_visitor_analytics_init');