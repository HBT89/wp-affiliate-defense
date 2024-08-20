<?php
/*
Plugin Name: Affiliate Link Defender
Description: A lightweight plugin to monitor changes to affiliate links on pages.
Version: 1.102
Author: Joshua A. Selvidge
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Function to create the necessary database table on plugin activation
function affiliate_defense_create_table() {
    global $wpdb;

    // Use the WordPress table prefix
    $table_name = $wpdb->prefix . 'affiliate_link_changes';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        changed_links text NOT NULL,
        change_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Hook the function to the plugin activation
register_activation_hook(__FILE__, 'affiliate_defense_create_table');

// Include the Alert Handler class file
require_once plugin_dir_path(__FILE__) . 'includes/class-alert-handler.php';

// Check if the class exists before initializing it
if (class_exists('Affiliate_Link_Monitor_Alert_Handler')) {
    $alert_handler = new Affiliate_Link_Monitor_Alert_Handler();
    $alert_handler->init();
} else {
    error_log('Affiliate_Link_Monitor_Alert_Handler class not found.');
}

// Add a custom admin menu
function affiliate_defense_add_admin_menu() {
    add_menu_page(
        'Affiliate Defense',      // Page title
        'Affiliate Defense',      // Menu title
        'manage_options',         // Capability
        'affiliate-defense',      // Menu slug
        'affiliate_defense_admin_page', // Callback function
        'dashicons-shield',       // Icon (optional)
        65                        // Position (optional)
    );
}
add_action('admin_menu', 'affiliate_defense_add_admin_menu');

// Callback function for the admin page
function affiliate_defense_admin_page() {
    // Check if the user has the required capability
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    ?>
    <div class="wrap">
        <h1>Affiliate Defense</h1>
        <p>Welcome to the Affiliate Defense plugin. Here you can monitor affiliate link changes and access logs.</p>
        
        <!-- Example: Displaying some information -->
        <h2>Affiliate Link Changes Log</h2>
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_link_changes';
        $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY change_time DESC LIMIT 10");

        if ($results) {
            echo '<ul>';
            foreach ($results as $row) {
                echo '<li>' . esc_html($row->change_time) . ' - ' . esc_html($row->changed_links) . ' (Post ID: ' . esc_html($row->post_id) . ')</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No recent changes logged.</p>';
        }
        ?>
    </div>
    <?php
}
