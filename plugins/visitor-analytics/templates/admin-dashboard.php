<?php
/**
 * Template for the admin dashboard page
 * 
 * @package WP Visitor Analytics
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Check user capabilities
if (!current_user_can('manage_options')) {
    return;
}

// Include display figure class
require_once plugin_dir_path(__FILE__) . '../includes/class-display-figure.php';

// Initialize display figure class
$display_figure = new Display_Figure();


// Add admin page content
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="visitor-analytics-admin-content">

        <div class="visitor-analytics-admin-header">
            <h2>Visitor Analytics Dashboard</h2>
        </div>
        
        <div class="visitor-analytics-admin-body">
            <div class="visitor-analytics-admin-section">
                <!-- Total Visitors -->
                <?php echo $display_figure -> display_total_visitors(); ?>
            </div>

            <div class="visitor-analytics-admin-section">
                <!-- Total Visitors by Month -->
                <?php echo $display_figure -> display_total_visitors_by_month(); ?>
            </div>

            <div class="visitor-analytics-admin-section">
                <!-- Total Visitors by Day -->
                <?php echo $display_figure -> display_total_visitors_by_day(); ?>
            </div>

            <div class="visitor-analytics-admin-section">
                <!-- Unique Visitors -->
                <?php echo $display_figure ->  display_total_visitors_by('ip', 'IP Address', 'IP Address') ?>
            </div>

            <div class="visitor-analytics-admin-section">
                <!-- Total Visitors by Country -->
                <?php echo $display_figure ->  display_total_visitors_by('country_name', 'Country', 'Country') ?>
            </div>

            <div class="visitor-analytics-admin-section">
                <!-- Total Visitors by Browser -->
                <?php echo $display_figure ->  display_total_visitors_by('device', 'Device', 'Device') ?>
            </div>

            <div class="visitor-analytics-admin-section">
                <!-- Total Visitors by Device -->
                <?php echo $display_figure ->  display_total_visitors_by('browser', 'Browser', 'Browser') ?>
            </div>
        </div>
    </div>


</div>

    
