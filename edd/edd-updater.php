<?php
if (!class_exists('EDD_SL_Plugin_Updater')) {
    include(dirname(__FILE__) . '/EDD_SL_Plugin_Updater.php');
}

$license_key = trim(get_option('affiliate_link_monitor_license_key'));

$edd_updater = new EDD_SL_Plugin_Updater('https://your-site.com', __FILE__, array(
    'version'   => AFFILIATE_LINK_MONITOR_VERSION,
    'license'   => $license_key,
    'item_name' => 'Affiliate Link Monitor',
    'author'    => 'Your Name',
    'url'       => home_url()
));
