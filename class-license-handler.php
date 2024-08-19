<?php
class Affiliate_Link_Monitor_License_Handler {

    public function init() {
        // EDD Licensing
        if (class_exists('EDD_SL_Plugin_Updater')) {
            add_action('admin_init', array($this, 'edd_register_license_option'));
            add_action('admin_init', array($this, 'edd_activate_license'));
            add_action('admin_init', array($this, 'edd_deactivate_license'));
            add_action('admin_init', array($this, 'edd_check_license'));
        }
        
        // Freemius Licensing is handled automatically within the Freemius SDK
    }

    // Register EDD license key field in the options
    public function edd_register_license_option() {
        register_setting('affiliate_link_monitor_license', 'affiliate_link_monitor_license_key', array($this, 'sanitize_license'));
    }

    // Activate the license key
    public function edd_activate_license() {
        if (isset($_POST['edd_license_activate'])) {
            $license_key = trim(get_option('affiliate_link_monitor_license_key'));
            $api_params = array(
                'edd_action' => 'activate_license',
                'license'    => $license_key,
                'item_name'  => urlencode('Affiliate Link Monitor'),
                'url'        => home_url()
            );
            $response = wp_remote_post('https://your-site.com', array('body' => $api_params));
            $license_data = json_decode(wp_remote_retrieve_body($response));
            if ($license_data->license == 'valid') {
                update_option('affiliate_link_monitor_license_status', $license_data->license);
            }
        }
    }

    // Deactivate the license key
    public function edd_deactivate_license() {
        if (isset($_POST['edd_license_deactivate'])) {
            $license_key = trim(get_option('affiliate_link_monitor_license_key'));
            $api_params = array(
                'edd_action' => 'deactivate_license',
                'license'    => $license_key,
                'item_name'  => urlencode('Affiliate Link Monitor'),
                'url'        => home_url()
            );
            $response = wp_remote_post('https://your-site.com', array('body' => $api_params));
            if (wp_remote_retrieve_response_code($response) == 200) {
                delete_option('affiliate_link_monitor_license_status');
            }
        }
    }

    // Check the license status
    public function edd_check_license() {
        $license_key = trim(get_option('affiliate_link_monitor_license_key'));
        $api_params = array(
            'edd_action' => 'check_license',
            'license'    => $license_key,
            'item_name'  => urlencode('Affiliate Link Monitor'),
            'url'        => home_url()
        );
        $response = wp_remote_post('https://your-site.com', array('body' => $api_params));
        $license_data = json_decode(wp_remote_retrieve_body($response));
        if ($license_data->license != 'valid') {
            delete_option('affiliate_link_monitor_license_status');
        }
    }

    // Sanitize the license key before saving
    public function sanitize_license($new) {
        $old = get_option('affiliate_link_monitor_license_key');
        if ($old && $old != $new) {
            delete_option('affiliate_link_monitor_license_status');
        }
        return $new;
    }
}
