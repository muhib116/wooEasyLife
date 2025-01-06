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
        $data = $this->prepare_order_data_from_request($request);
        $address = $data['address'];
        $payment_method_id = $data['payment_method_id'];
        $shipping_method_id = $data['shipping_method_id'];
        $order_note = $data['order_note'];
        $order_status = 'pending';
        $order_source = $data['order_source'];
        $coupon_codes  = $data['coupon_codes'];
    
        // Step 1: Initialize the Custom Order
        $order = wc_create_order();
        if (is_wp_error($order)) {
            return new WP_Error('order_creation_failed', 'Failed to create order.', ['status' => 500]);
        }
    
        $order_id = $order->get_id(); // Retrieve the new order ID.
    
        // Step 2: Add Products to the Order
        foreach ($data['products'] as $item) {
            $this->add_product_to_order($order, $item['id'], $item['quantity']);
        }
    
        // Step 3: Add Billing and Shipping Details
        $this->add_billing_and_shipping_details_to_order($order, $address);
    
        // Step 4: Set Payment Method
        $order->set_payment_method($payment_method_id);
    
        // Step 5: Add Shipping Method
        $this->add_shipping_method_to_order($order, $shipping_method_id);
    
        // Step 6: Add Order Notes
        if (!empty($order_note)) {
            $order->add_order_note($order_note, false);
        }
    
        // Step 7: Set the Order Status
        $order->update_status($order_status);
        $order->set_created_via($order_source);
    
        // Step 8: Apply Coupon Codes
        if (!empty($coupon_codes)) {
            foreach ($coupon_codes as $coupon) {
                $order->apply_coupon($coupon);
            }
        }
    
        // Step 9: Calculate Totals
        $order->calculate_totals();
    
        // Step 10: Save the Order
        $order->save();
    
        return new WP_REST_Response([
            'status' => 'success',
            'order_id' => $order->get_id(),
        ], 200);
    }
    

    private function prepare_order_data_from_request($request) {
        // Get the JSON payload from the request
        $payload = $request->get_json_params();
    
        // Prepare products data
        $products = [];
        if (!empty($payload['products'])) {
            foreach ($payload['products'] as $product) {
                $products[] = [
                    'id'       => isset($product['id']) ? intval($product['id']) : null,
                    'quantity' => isset($product['quantity']) ? intval($product['quantity']) : 1,
                ];
            }
        }
    
        // Prepare address data
        $address = [];
        if (!empty($payload['address'])) {
            foreach ($payload['address'] as $field) {
                $address = array_merge($address, $field);
            }
        }
    
        // Payment method
        $payment_method_id = isset($payload['payment_method_id']) ? sanitize_text_field($payload['payment_method_id']) : '';
    
        // Shipping method
        $shipping_method_id = isset($payload['shipping_method_id']) ? sanitize_text_field($payload['shipping_method_id']) : '';
    
        // Order note
        $order_note = isset($payload['order_note']) ? sanitize_textarea_field($payload['order_note']) : '';
    
        // Order source
        $order_source = isset($payload['order_source']) ? sanitize_text_field($payload['order_source']) : 'website';
    
        // Order status
        $order_status = isset($payload['order_status']) ? sanitize_text_field($payload['order_status']) : 'wc-pending';
    
        // Coupon codes
        $coupon_codes = !empty($payload['coupon_codes']) ? array_map('sanitize_text_field', $payload['coupon_codes']) : [];
    
        return [
            'products'          => $products,
            'address'           => $address,
            'payment_method_id' => $payment_method_id,
            'shipping_method_id' => $shipping_method_id,
            'order_note'        => $order_note,
            'order_source'      => $order_source,
            'order_status'      => $order_status,
            'coupon_codes'      => $coupon_codes,
        ];
    }
    
    private function add_product_to_order($order, $product_id, $quantity) {
        $product = wc_get_product($product_id);
        if ($product) {
            $order->add_product($product, $quantity); // Add product and quantity to the order
        } else {
            die('Product not found.');
        }
    }

    private function add_billing_and_shipping_details_to_order($order, $address) {
        $order->set_address($address, 'billing');
        $order->set_address($address, 'shipping');
    }

    private function add_shipping_method_to_order($order, $shipping_method_id)
    {
        $shipping_methods = WC()->shipping->get_shipping_methods();
        $method = $shipping_methods[$shipping_method_id] ?? null;
    
        if (!$method) {
            throw new \Exception('Invalid shipping method.');
        }
    
        // Create a shipping item
        $item = new \WC_Order_Item_Shipping();
        $item->set_method_id($shipping_method_id);
        $item->set_method_title($method->get_title());
        $item->set_total($method->get_instance_option('cost', '0')); // Get the cost from the shipping method settings
    
        // Add the shipping item to the order
        $order->add_item($item);
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