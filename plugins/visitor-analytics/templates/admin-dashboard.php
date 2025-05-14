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
        
        <!-- Total Visitors -->
        <div class="visitor-analytics-admin-body">
            <?php echo $display_figure -> display_total_visitors(); ?>
        </div>

        <!-- Charts -->
        <div class="visitor-analytics-admin-body">

            <div class="visitor-analytics-chart-section">

                <!-- Total Visitors by Month -->
                <?php echo $display_figure -> display_total_visitors_by_month(); ?>

                <!-- Total Visitors by Day -->
                <?php echo $display_figure -> display_total_visitors_by_day(); ?>

            </div>
        </div>

        <div class="visitor-analytics-admin-body visitor-analytics-total-visitors-by">
            <?php echo $display_figure -> display_total_visitors_by('ip', 'Total Visitors by IP', 'IP'); ?>
            <?php echo $display_figure -> display_total_visitors_by('country_name', 'Total Visitors by Country', 'Country'); ?>
            <?php echo $display_figure -> display_total_visitors_by('landing_page', 'Landing Page', 'Landing Page'); ?>
            <?php echo $display_figure -> display_total_visitors_by('browser', 'Browser', 'Browser'); ?>
            <?php echo $display_figure -> display_total_visitors_by('device', 'Device', 'Device'); ?>
            <?php echo $display_figure -> display_total_visitors_by('city', 'City', 'City'); ?>
        </div>
    </div>


</div>

    
