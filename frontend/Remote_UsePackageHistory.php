<?php
namespace WooEasyLife\Frontend;

class Remote_UsePackageHistory {
    public function __construct()
    {
        add_action('woocommerce_thankyou', [$this, 'confirmOrderPlaced'], 11, 1);
        // add_action('woocommerce_checkout_order_processed', [$this, 'confirmOrderPlaced'], 11, 1);
    }
    public function confirmOrderPlaced($order_id) {
        global $license_key;
    
        $url = get_api_end_point("package-order-use");
    
        // Get the WooCommerce order object
        $order = wc_get_order($order_id);
    
        if (!$order) {
            return [
                'status'  => 'error',
                'message' => 'Invalid order ID.',
            ];
        }
    
        $cart_contents = [];
        $total_value = 0;
    
        foreach ($order->get_items() as $item_id => $item) {
            // Ensure item is a product
            if (!$item instanceof \WC_Order_Item_Product) {
                continue;
            }
    
            $product = $item->get_product(); // WC_Product object
    
            if (!$product) {
                continue; // Skip if product data is not found
            }
    
            $cart_contents[] = [
                'name'        => $product->get_name(),
                'product_url' => get_permalink($product->get_id()),
                'quantity'    => $item->get_quantity(),
                'price'       => $product->get_price(), // Unit price
                'total_price' => $item->get_total(), // Total for this item
            ];
    
            $total_value += $item->get_total();
        }
    
        // Encode data properly for API request
        $data = json_encode([
            'order_count' => 1,
            'use_details' => $cart_contents,
        ]);
    
        $headers = [
            'Authorization' => 'Bearer ' . $license_key,
            'Content-Type'  => 'application/json', // JSON format
            'origin' => site_url()
        ];
    
        // Use wp_remote_post for HTTP requests
        $response = wp_remote_post($url, [
            'method'      => 'POST',
            'body'        => $data,
            'headers'     => $headers,
            'timeout'     => 45,
            'sslverify'   => false,
        ]);

        print_r($response);
        wp_die();
    
        // Check for errors in the response
        if (is_wp_error($response)) {
            return [
                'status'  => 'error',
                'message' => $response->get_error_message(),
            ];
        }
    
        // Decode and return the response
        $response_body = wp_remote_retrieve_body($response);

        return [
            'status'  => 'success',
            'message' => json_decode($response_body, true) ?: $response_body,
        ];
    }    
}