<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package WP_Visitor_Analytics
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('wp_visitor_analytics_version');
delete_option('wp_visitor_analytics_delete_data_on_uninstall');

// Check if we should delete all plugin data
if (get_option('wp_visitor_analytics_delete_data_on_uninstall')) {
    global $wpdb;

    // Drop custom tables
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}visitor_analytics");
    
    // Delete any transients
    delete_transient('wp_visitor_analytics_cache');
    
    // Delete any scheduled events
    wp_clear_scheduled_hook('wp_visitor_analytics_cleanup');
} 