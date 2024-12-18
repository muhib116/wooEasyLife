<?php

/**
 * Plugin Name: WooEasyLife
 * Plugin URI: https://example.com/wooeasylife
 * Description: WooEasyLife is a custom plugin for enhancing WooCommerce functionality.
 * Version: 1.0.0
 * Author: Muhibbullah Ansary
 * Author URI: https://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wooeasylife
 * Domain Path: /languages
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    die('Invalid request.');
}

define('__PREFIX', 'woo_easy_life_');
define('__API_NAMESPACE', 'wooeasylife/v1');
$config_data;



if (!class_exists('WooEasyLife')) :
    require_once 'vendor/autoload.php';

    class WooEasyLife
    {
        public $handleDBTable;
        public function __construct()
        {
            register_activation_hook(__FILE__, [$this, 'woo_easy_life_activation_hook']);
            register_deactivation_hook(__FILE__, [$this, 'woo_easy_life_deactivation_hook']);

            new WooEasyLife\Init\InitClass();
            new WooEasyLife\API\API_Register();
            new WooEasyLife\Admin\Admin_Class_Register();
            new WooEasyLife\Frontend\Frontend_Class_Register();
            $this->handleDBTable = new WooEasyLife\Admin\DBTable\HandleDBTable();
            $this->get_and_set_config_data();
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
            if (empty(get_option('woo_easy_life_license'))) update_option('woo_easy_life_license', ['key'=> ""]);
            if (empty(get_option('woo_easy_life_balance'))) update_option('woo_easy_life_balance', '200');

            // Save a flag to indicate the table was created
            if (empty(get_option('woo_easy_life_plugin_installed'))) update_option('woo_easy_life_plugin_installed', true);
            $this->handleDBTable->create();
            $this->create_static_statuses();
            $this->save_default_config();
        }

        public function woo_easy_life_deactivation_hook()
        {
            if (get_option('woo_easy_life_license') !== false) delete_option('woo_easy_life_license');
            if (get_option('woo_easy_life_balance') !== false) delete_option('woo_easy_life_balance');
            if (get_option('woo_easy_life_config') !== false) delete_option('woo_easy_life_config');

            // Remove plugin-specific options
            if (get_option('woo_easy_life_plugin_installed') !== false) delete_option('woo_easy_life_plugin_installed');
            if (get_option('woo_easy_life_custom_order_statuses') !== false) delete_option('woo_easy_life_custom_order_statuses');
            $this->handleDBTable->delete();
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
            if (empty(get_option('woo_easy_life_custom_order_statuses'))) {
                update_option('woo_easy_life_custom_order_statuses', $static_statuses);
            }
        }

        public function save_default_config()
        {
            $config = [
                "ip_block" => true,
                "phone_number_block" => true,
                "place_order_sms_for_customer" => false,
                "place_order_sms_for_admin" => false,
                "confirmed_order_sms" => false,
                "sms_for_customer_call_not_received" => false,
                "parcel_courier_entry" => false,
                "parcel_courier_hand_over" => false,
                "parcel_out_for_delivery_sms" => false,
                "cancelled_order_sms" => false,
                "refunded_order_sms" => false,
                "place_order_otp_verification" => false,
                "place_order_otp_intelligence" => false,
                "daily_order_place_limit_per_customer" => 3,
                "only_bangladeshi_ip" => false,
                "courier_automation" => false,
                "fraud_customer_checker" => false,
            ];

            // Save the updated config
            if (empty(get_option('woo_easy_life_config'))) {
                update_option('woo_easy_life_config', $config);
            }
        }
    }

    // add_action('init', function () {
        new WooEasyLife();
    // });
endif;
