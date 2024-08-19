<?php
/*
Plugin Name: Affiliate Link Monitor
Description: A lightweight plugin to monitor changes to affiliate links on pages.
Version: 1.0
Author: Your Name
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin version
define('AFFILIATE_LINK_MONITOR_VERSION', '1.0.0');

// Include the licensing handlers
require_once plugin_dir_path(__FILE__) . 'includes/class-license-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-alert-handler.php';

// Initialize licensing
$license_handler = new Affiliate_Link_Monitor_License_Handler();
$license_handler->init();

// Initialize alert handler
$alert_handler = new Affiliate_Link_Monitor_Alert_Handler();
$alert_handler->init();

// Freemius integration
require_once plugin_dir_path(__FILE__) . 'freemius/freemius.php';
