<?php
if (!function_exists('fs')) {
    function fs() {
        global $affiliate_link_monitor_fs;

        if (!isset($affiliate_link_monitor_fs)) {
            $affiliate_link_monitor_fs = fs_dynamic_init(array(
                'id'             => 'YOUR_PLUGIN_ID',
                'slug'           => 'affiliate-link-monitor',
                'type'           => 'plugin',
                'public_key'     => 'YOUR_PUBLIC_KEY',
                'is_premium'     => true,
                'has_paid_plans' => true,
                'trial'          => array(
                    'days'               => 14,
                    'is_require_payment' => true,
                ),
                'menu'           => array(
                    'slug'    => 'affiliate-link-monitor',
                ),
            ));
        }

        return $affiliate_link_monitor_fs;
    }

    // Initialize Freemius
    fs();
}
