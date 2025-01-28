<?php
/**
 * Plugin Name: Woo Life Changer
 * Plugin URI: https://api.wpsalehub.com/api/get-metadata
 * Description: "Woo Life Changer" is a custom plugin designed to enhance WooCommerce functionality with features like bulk SMS, fraud detection, OTP validation, and much more.
 * Version: 1.1.4
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
        public function __construct()
        {
            add_action('woocommerce_init', function () {
                if (WC()->session) {
                    WC()->session->set_customer_session_cookie(true);
                }
            });

            $this->get_license_key();
            $this->get_and_set_config_data();
            new WooEasyLife\Init\BootClass();
            new WooEasyLife\PluginLifecycleHandle();
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
    }

    new WooEasyLife();
endif;