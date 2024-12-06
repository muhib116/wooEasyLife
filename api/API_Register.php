<?php
namespace WooEasyLife\API;

class API_Register {
    public function __construct() {
        /**
         * API Path
         * /wp-json/wooeasylife/v1/orders
         * method: get
         */
        new \WooEasyLife\API\Admin\OrderListAPI();

        /**
         * API Path
         * /wp-json/wooeasylife/v1/payment-methods
         * method: get
         */
        new \WooEasyLife\API\Admin\PaymentMethodsAPI();

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
        new \WooEasyLife\API\Admin\UpdateAddressAPI();

        /**
         * customer ip, phone block, list, and edit
         */
        // new WooEasyLife\Admin\BlockFakeCustomer();


        new \WooEasyLife\API\Admin\OrderStatisticsAPI();
        new \WooEasyLife\API\Admin\CustomOrderStatusAPI();
        new \WooEasyLife\API\Admin\ConfigAPI();
    }
}