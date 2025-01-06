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
        register_rest_route(__API_NAMESPACE, '/custom-orders', [
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_custom_orders'],
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
    public function get_custom_orders(WP_REST_Request $request)
    {
        $args = [
            'post_type' => 'shop_order',
            'post_status' => 'any',
            'posts_per_page' => $request->get_param('limit') ?? 10,
            'paged' => $request->get_param('page') ?? 1,
        ];

        $query = new \WP_Query($args);
        $orders = [];

        foreach ($query->posts as $post) {
            $order = wc_get_order($post->ID);

            $orders[] = [
                'id' => $order->get_id(),
                'status' => $order->get_status(),
                'total' => $order->get_total(),
                'currency' => $order->get_currency(),
                'date_created' => $order->get_date_created() ? $order->get_date_created()->date('Y-m-d H:i:s') : null,
                'customer_ip' => $order->get_customer_ip_address(),
                'payment_method' => $order->get_payment_method(),
                'shipping_total' => $order->get_shipping_total(),
            ];
        }

        return new WP_REST_Response([
            'status' => 'success',
            'data' => $orders,
            'pagination' => [
                'total' => $query->found_posts,
                'total_pages' => $query->max_num_pages,
            ],
        ], 200);
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
    
            $response[] = [
                'id'          => $product->get_id(),
                'name'        => $product->get_name(),
                'price'       => $product->get_price_html(),
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