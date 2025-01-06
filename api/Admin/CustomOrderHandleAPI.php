<?php

namespace WooEasyLife\API\Admin;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use WC_Shipping_Zones;
use WC_Payment_Gateways;

class CustomOrderHandleAPI extends WP_REST_Controller
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register REST API routes
     */
    public function register_routes()
    {
        register_rest_route(__API_NAMESPACE, '/create-custom-order', [
            [
                'methods'             => 'post',
                'callback'            => [$this, 'create_custom_order'],
                'permission_callback' => [$this, 'permissions_check'],
            ],
        ]);

        register_rest_route(__API_NAMESPACE, '/custom-orders/get-products', [
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_products'],
                'permission_callback' => [$this, 'permissions_check'],
            ],
        ]);
    }

    /**
     * Permissions callback for the endpoints
     */
    public function permissions_check()
    {
        return true; // Restrict access to WooCommerce admins
    }


    /**
     * Get custom orders
     */
    public function create_custom_order(WP_REST_Request $request)
    {
        $product_id = 123; // Replace with your product ID
        $quantity = 2; // Quantity of the product
        $billing_address = [];
        $payment_method_id = 'cod';
        $shipping_method_id = 'flat_rate';
        $order_note = '';
        $order_status = 'pending';
        $order_source = 'whats app';
        $coupon_code  = 'Free';

        //Step 1: Initialize the Custom Order
        $order = wc_create_order();
        if (is_wp_error($order)) {
            // Handle error
            die('Failed to create order.');
        }
        $order_id = $order->get_id(); // Retrieve the new order ID.

        // Step 2: Add Products to the Order
        $this->add_product_to_order($order, $product_id, $quantity);

        // Step 3: Add Billing and Shipping Details
        $this->add_billing_and_shipping_details_to_order($order, $billing_address, $billing_address);

        // Step 4: Set Payment Method
        $order->set_payment_method($payment_method_id); // 'cod' is for Cash on Delivery

        // Step 5: Set Shipping Method
        $this->add_shipping_method_to_order($order, $shipping_method_id);

        // Step 6: Add Order Notes
        $order->add_order_note($order_note, false); // 'false' means the note is private

        // Step 7: Set the Order Status
        $order->update_status($order_status);
        $order->set_created_via($order_source); // you can also use custom values here
        
        if(!empty($coupon_code)){
            $order->apply_coupon($coupon_code);
        }

        /**
         * Step 8: Calculate Totals
         * Recalculate the order totals after all items, fees, and discounts are added.
         */
        $order->calculate_totals();

        // Step 9: Save the Order
        $order->save();

        return $order->get_id();
    }

    private function add_product_to_order($order, $product_id, $quantity) {
        $product = wc_get_product($product_id);
        if ($product) {
            $order->add_product($product, $quantity); // Add product and quantity to the order
        } else {
            die('Product not found.');
        }
    }

    private function add_billing_and_shipping_details_to_order($order, $billing_address, $shipping_address) {
        $order->set_address($billing_address, 'billing');
        $order->set_address($shipping_address, 'shipping');
    }

    private function add_shipping_method_to_order($order, $shipping_method_id){
        // Fetch available shipping methods
        $shipping_methods = WC()->shipping->get_shipping_methods();

        // Check if the provided shipping method ID exists
        if (!isset($shipping_methods[$shipping_method_id])) {
            return 'Invalid shipping method ID.';
        }

        $shipping_method = $shipping_methods[$shipping_method_id];

        // Ensure the shipping method is enabled
        if (!$shipping_method->enabled) {
            return 'The shipping method is not enabled.';
        }

        // Calculate shipping cost dynamically (example logic; replace with your logic)
        $shipping_cost = isset($shipping_method->settings['cost']) ? $shipping_method->settings['cost'] : 0;

        // Add shipping to the order
        $order->add_shipping([
            'method_id'    => $shipping_method->id,
            'method_title' => $shipping_method->get_title(),
            'total'        => $shipping_cost,
        ]);
    }

    public function get_products(WP_REST_Request $request)
    {
        $search = sanitize_text_field($request->get_param('search'));
    
        // Base arguments for getting products
        $args = [
            'limit' => -1, // Retrieve all products
            'status' => 'publish', // Only published products
        ];
    
        // Handle search by name or ID
        if (!empty($search)) {
            $args['search'] = $search; // Search term for product name or ID
        }
    
        // Fetch products using WooCommerce functions
        $products = wc_get_products($args);
        $response = [];
    
        // Format product data
        foreach ($products as $product) {
            $image_id = $product->get_image_id(); // Get the main image ID
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : wc_placeholder_img_src(); // Get the URL or a placeholder
            $product_currency_symbol = get_woocommerce_currency_symbol();

            $response[] = [
                'id'          => $product->get_id(),
                'currency_symbol' => $product_currency_symbol,
                'name'        => $product->get_name(),
                'price'       => $product->get_price(),
                'sku'         => $product->get_sku(),
                'stock_status'=> $product->get_stock_status(),
                'type'        => $product->get_type(),
                'permalink'   => get_permalink($product->get_id()),
                'image'       => $image_url, // Add image URL
            ];
        }
    
        // Check if no products were found
        if (empty($response)) {
            return new \WP_REST_Response([
                'status'  => 'success',
                'message' => 'No products found.',
                'data'    => [],
            ], 200);
        }
    
        // Return the response
        return new \WP_REST_Response([
            'status'  => 'success',
            'message' => 'Products retrieved successfully.',
            'data'    => $response,
        ], 200);
    }    
    
}