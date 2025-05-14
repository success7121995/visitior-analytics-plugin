<?php
/**
 * Plugin Name: Visitor Tracker: Analytics for WP
 * Plugin URI: https://wordpress.org/plugins/visitor-tracker-analytics-for-wp/
 * Description: Track and visualize your website visitors with detailed analytics and beautiful charts.
 * Version: 1.0.0
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Author: Stanford Tse
 * Author URI: https://profiles.wordpress.org/stanfordtse/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: visitor-tracker-analytics-for-wp
 * Domain Path: /languages
 *
 * @package WP_Visitor_Analytics
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('WP_VISITOR_ANALYTICS_VERSION', '1.0.0');
define('WP_VISITOR_ANALYTICS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_VISITOR_ANALYTICS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once WP_VISITOR_ANALYTICS_PLUGIN_DIR . 'includes/class-analytics-core.php';
require_once WP_VISITOR_ANALYTICS_PLUGIN_DIR . 'includes/class-database-manager.php';
require_once WP_VISITOR_ANALYTICS_PLUGIN_DIR . 'includes/class-display-figure.php';
require_once WP_VISITOR_ANALYTICS_PLUGIN_DIR . 'includes/class-visitor-tracker.php';
require_once WP_VISITOR_ANALYTICS_PLUGIN_DIR . 'includes/class-register-script-style.php';
require_once WP_VISITOR_ANALYTICS_PLUGIN_DIR . 'includes/class-api-endpoints.php';

/**
 * Initialize plugin
 */
function wp_visitor_analytics_init() {
    // Load plugin text domain
    load_plugin_textdomain('visitor-tracker-analytics-for-wp', false, dirname(plugin_basename(__FILE__)) . '/languages');

    // Initialize plugin components
    new Analytics_Core();
    new Register_Script_Style();
    new Visitor_Tracker();
    new Database_Manager();
    new Display_Figure();
    new API_Endpoints();
}
add_action('plugins_loaded', 'wp_visitor_analytics_init');

/**
 * Activation hook
 */
function wp_visitor_analytics_activate() {
    // Set default options
    add_option('wp_visitor_analytics_version', WP_VISITOR_ANALYTICS_VERSION);
}
register_activation_hook(__FILE__, 'wp_visitor_analytics_activate');

/**
 * Deactivation hook
 */
function wp_visitor_analytics_deactivate() {
    // Cleanup if necessary
}
register_deactivation_hook(__FILE__, 'wp_visitor_analytics_deactivate');

/**
 * Uninstall hook
 */
function wp_visitor_analytics_uninstall() {
    // Remove plugin data if uninstall is requested
    if (get_option('wp_visitor_analytics_delete_data_on_uninstall')) {
        delete_option('wp_visitor_analytics_version');
        delete_option('wp_visitor_analytics_delete_data_on_uninstall');
    }
}
register_uninstall_hook(__FILE__, 'wp_visitor_analytics_uninstall');