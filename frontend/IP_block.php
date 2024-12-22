<?php
namespace WooEasyLife\Frontend;

class IP_block {
    public function __construct()
    {
        add_action('init', [$this, 'block_non_bangladeshi_users']);
        add_action('woocommerce_checkout_order_processed', [$this, 'phone_number_block']);
    }

    public function block_non_bangladeshi_users() {
        global $config_data;

        if (
            isset($config_data['only_bangladeshi_ip']) && 
            $config_data['only_bangladeshi_ip'] && 
            !current_user_can('manage_options')
        ) {
            // Get the user's IP address
            $user_ip = $_SERVER['REMOTE_ADDR'];
            $user_ip  = $user_ip == '::1' ? '23.106.249.37' : $user_ip; 
            //bd_ip:103.204.210.233, sg ip:23.106.249.37
        
            // Use an IP geolocation API (e.g., ip-api.com)
            $api_url = "http://ip-api.com/json/{$user_ip}";
            $response = wp_remote_get($api_url);
        
            // Ensure the API response is valid
            if (!is_wp_error($response)) {     
                $data = json_decode(wp_remote_retrieve_body($response), true);
                
                // Check if the user's country is Bangladesh
                if (isset($data['countryCode']) && $data['countryCode'] !== 'BD') {
                    // Block access or redirect
                    wp_die(
                        'Access restricted. This site is only accessible from Bangladesh.',
                        'Access Denied', 'your-text-domain',
                        array('response' => 403)
                    );
                }
            }
        }
    
    }

    public function phone_number_block() {
        $billing_phone = isset($_POST['billing_phone']) ? sanitize_text_field($_POST['billing_phone']) : '';

        wp_die($billing_phone);
    }
}