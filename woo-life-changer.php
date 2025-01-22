<?php
/**
 * Plugin Name: Woo Life Changer
 * Plugin URI: https://api.wpsalehub.com/api/get-metadata
 * Description: "Woo Life Changer" is a custom plugin designed to enhance WooCommerce functionality with features like bulk SMS, fraud detection, OTP validation, and much more.
 * Version: 1.1.3
 * Author: Muhibbullah Ansary
 * Author URI: https://wpsalehub.com
 * Text Domain: woo-life-changer
 * Domain Path: /languages
 */


// Exit if accessed directly.
if (! defined('ABSPATH')) {
    die('Invalid request.');
}

define('__PREFIX', 'woo_easy_life_');
define('__API_NAMESPACE', 'wooeasylife/v1');

$config_data;
$license_key;

if (!class_exists('WooEasyLife')) :
    require_once 'vendor/autoload.php';

    class WooEasyLife
    {
        public $handleDBTable;
        public function __construct()
        {
            add_action('woocommerce_init', function () {
                if (WC()->session) {
                    WC()->session->set_customer_session_cookie(true);
                }
            });

            register_activation_hook(__FILE__, [$this, 'woo_easy_life_activation_hook']);
            register_deactivation_hook(__FILE__, [$this, 'woo_easy_life_deactivation_hook']);

            $this->handleDBTable = new WooEasyLife\Admin\DBTable\HandleDBTable();
            $this->get_license_key();
            $this->get_and_set_config_data();
            new WooEasyLife\Init\InitClass();
            new WooEasyLife\API\API_Register();
            new WooEasyLife\Admin\Admin_Class_Register();
            new WooEasyLife\Frontend\Frontend_Class_Register();
        }

        private function get_license_key() {
            global $license_key;
            $license_key = get_option(__PREFIX . 'license');
            $license_key = is_string($license_key) ? json_decode($license_key, true) : $license_key;
            $license_key = $license_key['key'];
        }
        private function get_and_set_config_data()
        {
            global $config_data;
            $config_data = get_option(__PREFIX . 'config');
            // Decode the JSON data into an associative array
            $decoded_config_data = is_string($config_data) ? json_decode($config_data, true) : $config_data;
            
            // Ensure it's an array
            if (!is_array($decoded_config_data)) {
                $decoded_config_data = [];
            }
            
            $config_data = $decoded_config_data;
        }

        public function woo_easy_life_activation_hook()
        {
            ob_start(); // Start output buffering

            if (empty(get_option(__PREFIX.'license'))) update_option(__PREFIX.'license', ['key'=> ""]);
            if (empty(get_option(__PREFIX.'balance'))) update_option(__PREFIX.'balance', '200');

            // Save a flag to indicate the table was created
            if (empty(get_option(__PREFIX.'plugin_installed'))) update_option(__PREFIX.'plugin_installed', true);
            
            $this->handleDBTable->create();
            $this->create_static_statuses();
            $this->save_default_config();

            ob_end_clean(); // Clear any unexpected output
        }

        public function woo_easy_life_deactivation_hook()
        {
            global $config_data;

            if($config_data['clear_data_when_deactivate_plugin']){
                if (get_option(__PREFIX.'license') !== false) delete_option(__PREFIX.'license');
                if (get_option(__PREFIX.'balance') !== false) delete_option(__PREFIX.'balance');
                if (get_option(__PREFIX.'config') !== false) delete_option(__PREFIX.'config');
    
                // Remove plugin-specific options
                if (get_option(__PREFIX.'plugin_installed') !== false) delete_option(__PREFIX.'plugin_installed');
                if (get_option(__PREFIX.'custom_order_statuses') !== false) delete_option(__PREFIX.'custom_order_statuses');
                $this->handleDBTable->delete();
            }
        }

        public function create_static_statuses()
        {
            // Define static statuses
            $static_statuses = [
                'processing' => [
                    'title'       => 'Processing',
                    'slug'        => 'processing',
                    'is_default'  => true,
                    'not_using'   => false,
                    'color'       => '#FFA500',
                    'description' => 'Order is being prepared.',
                ],
                'follow-up' => [
                    'title'       => 'Follow Up',
                    'slug'        => 'follow-up',
                    'is_default'  => true,
                    'not_using'   => false,
                    'color'       => '#e61976',
                    'description' => 'Follow-up action is required.',
                ],
                'confirmed' => [
                    'title'       => 'Confirmed',
                    'slug'        => 'confirmed',
                    'is_default'  => true,
                    'not_using'   => false,
                    'color'       => '#28A745',
                    'description' => 'Order details have been confirmed.',
                ],
                'call-not-received' => [
                    'title'       => 'Call Not Received',
                    'slug'        => 'call-not-received',
                    'is_default'  => true,
                    'not_using'   => false,
                    'color'       => '#6C757D',
                    'description' => 'Customer call not received.',
                ],
                'canceled' => [
                    'title'       => 'Canceled',
                    'slug'        => 'canceled',
                    'is_default'  => true,
                    'not_using'   => false,
                    'color'       => '#DC3545',
                    'description' => 'Order has been canceled.',
                ],
                'fake' => [
                    'title'       => 'Fake Order',
                    'slug'        => 'fake',
                    'is_default'  => true,
                    'not_using'   => false,
                    'color'       => '#8B0000',
                    'description' => 'Order is marked as fraudulent.',
                ],
                'courier-entry' => [
                    'title'       => 'Courier Entry',
                    'slug'        => 'courier-entry',
                    'is_default'  => true,
                    'not_using'   => false,
                    'color'       => '#6F42C1',
                    'description' => 'Order entered into courier system.',
                ],
                'courier-hand-over' => [
                    'title'       => 'Courier Hand Over',
                    'slug'        => 'courier-hand-over',
                    'is_default'  => true,
                    'not_using'   => false,
                    'color'       => '#0056B3',
                    'description' => 'Order handed over to courier.',
                ],
                'out-for-delivery' => [
                    'title'       => 'Out for Delivery',
                    'slug'        => 'out-for-delivery',
                    'is_default'  => true,
                    'not_using'   => false,
                    'color'       => '#8bc005',
                    'description' => 'Courier is delivering the order.',
                ],
                'delivered' => [
                    'title'       => 'Delivered',
                    'slug'        => 'delivered',
                    'is_default'  => true,
                    'not_using'   => false,
                    'color'       => '#18ac2e',
                    'description' => 'Order delivered successfully.',
                ],
                'payment-received' => [
                    'title'       => 'Payment Received',
                    'slug'        => 'payment-received',
                    'is_default'  => true,
                    'not_using'   => false,
                    'color'       => '#FFD700',
                    'description' => 'Payment received for the order.',
                ],
                'pending-payment' => [
                    'title'       => 'Pending Payment',
                    'slug'        => 'pending-payment',
                    'is_default'  => true,
                    'not_using'   => false,
                    'color'       => '#FFC107',
                    'description' => 'Awaiting payment confirmation.',
                ],
                'returned' => [
                    'title'       => 'Returned',
                    'slug'        => 'returned',
                    'is_default'  => true,
                    'not_using'   => false,
                    'color'       => '#FF6961',
                    'description' => 'Order returned by the customer.',
                ],
                'refunded' => [
                    'title'       => 'Refunded',
                    'slug'        => 'refunded',
                    'is_default'  => true,
                    'not_using'   => false,
                    'color'       => '#0e1011',
                    'description' => 'Payment refunded to the customer.',
                ],
            ];

            // Save the updated statuses
            if (empty(get_option(__PREFIX.'custom_order_statuses'))) {
                update_option(__PREFIX.'custom_order_statuses', $static_statuses);
            }
        }

        public function save_default_config()
        {
            $site_title = get_bloginfo('name');
            $custom_logo_id = get_theme_mod('custom_logo');
            $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');

            $config = [
                "admin_phone" => '',
                "invoice_company_name" => $site_title,
                "invoice_logo" => $logo_url,
                "invoice_email" => '',
                "invoice_phone" => '',
                "invoice_print" => false,
                "clear_data_when_deactivate_plugin" => false,
                "ip_block" => true,
                "phone_number_block" => true,
                "place_order_otp_verification" => false,
                "daily_order_place_limit_per_customer" => 3,
                "only_bangladeshi_ip" => false,
                "courier_automation" => false,
                "fraud_customer_checker" => false,
            ];

            // Save the updated config
            if (empty(get_option(__PREFIX.'config'))) {
                update_option(__PREFIX.'config', $config);
            }
        }
    }

    // add_action('init', function () {
        new WooEasyLife();
        new  WooEasyLife\Init\UpdatePlugin(get_current_plugin_version(), $license_key);
        add_action('init', function () {
            delete_site_transient('update_plugins'); // Clear old cached data
        
            $response = wp_remote_get('https://yourwebsite.com/api/get-metadata');
            if (is_wp_error($response)) {
                error_log('Error fetching metadata: ' . $response->get_error_message());
            } else {
                error_log('Metadata response: ' . wp_remote_retrieve_body($response));
            }
        });
    // });
endif;



function get_current_plugin_version() {
    // Define the path to the plugin file
    $plugin_file = plugin_dir_path(__FILE__) . basename(__FILE__);

    // Check if the file exists
    if (file_exists($plugin_file)) {
        // Retrieve the plugin data
        $plugin_data = get_file_data($plugin_file, array('Version' => 'Version'));

        // Return the version if available
        return isset($plugin_data['Version']) ? $plugin_data['Version'] : null;
    }

    return null; // Return null if the file does not exist
}