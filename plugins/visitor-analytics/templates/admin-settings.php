<?php
/**
 * Template for the admin settings page
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

// Add admin page content
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="visitor-analytics-admin-content">

        <div class="visitor-analytics-admin-header">
            <h2>Visitor Analytics Setting</h2>
        </div>
        
        <div class="visitor-analytics-admin-body">
            
            <div class="visitor-analytics-admin-section">

            </div>

        </div>
    </div>


</div>

    
