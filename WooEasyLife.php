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

    /**
     * API Path
     * /wp-json/wooeasylife/v1/orders
     * method: get
     */
    new WooEasyLife\Admin\OrderList();

    /**
     * API Path
     * /wp-json/wooeasylife/v1/payment-methods
     * method: get
     */
    new WooEasyLife\Admin\PaymentMethods();

    /**
     * API Path
     * /wp-json/wooeasylife/v1/update-address/{order_id}
     * method: post
     * payload:
     * {
     *       "billing": {
     *           "address_1": "123 Main St",
     *           "city": "New York",
     *           "state": "NY",
     *           "postcode": "10001",
     *           "country": "US",
     *           "email": "customer@example.com",
     *           "phone": "555-1234"
     *       },
     *       "shipping": {
     *           "address_1": "123 Main St",
     *           "city": "New York",
     *           "state": "NY",
     *           "postcode": "10001",
     *           "country": "US"
     *       }
     *   }
     */
    new WooEasyLife\Admin\UpdateAddress();

    /**
     * customer ip, phone block, list, and edit
     */
    // new WooEasyLife\Admin\BlockFakeCustomer();


    new WooEasyLife\Admin\OrderStatisticsAPI();
});
