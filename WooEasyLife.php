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
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once 'vendor/autoload.php';



require_once __DIR__ . '/functions/createAdminMenu.php';
require_once __DIR__ . '/functions/wooCommerceExist.php';
require_once __DIR__ . '/functions/loadAdminScripts.php';

add_action( 'plugins_loaded', function(){
    // Initialize the API class
    require_once __DIR__ . '/functions/showCustomerFraudDate.php';



    // add_action('woocommerce_checkout_order_review', 'storeapps_checkout_order_review');
    // function storeapps_checkout_order_review() {
    //     echo '<h2>woocommerce_checkout_order_review</h2>';
    // }

    // frontend start
    new WooEasyLife\Includes\Frontend_Class_Register();
    new WooEasyLife\Includes\Admin_API_Register();
});
