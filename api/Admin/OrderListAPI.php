<?php

namespace WooEasyLife\API\Admin;

class OrderListAPI
{

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Registers the routes for the custom endpoint.
     */
    public function register_routes()
    {
        register_rest_route(
            __API_NAMESPACE, // Namespace and version.
            '/orders',         // Endpoint: /orders
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_orders'],
                'permission_callback' => '__return_true', // Allow public access (modify as needed).
                'args'                => [
                    'status' => [
                        'required' => false,
                        'type'     => 'string',
                        'default'  => 'any',
                        'description' => 'Filter orders by status (e.g., processing, completed).',
                    ],
                    'per_page' => [
                        'required' => false,
                        'type'     => 'integer',
                        'default'  => 10,
                        'description' => 'Number of orders per page.',
                    ],
                    'page' => [
                        'required' => false,
                        'type'     => 'integer',
                        'default'  => 1,
                        'description' => 'Page number for pagination.',
                    ],
                ],
            ]
        );
        register_rest_route(
            __API_NAMESPACE, // Namespace and version.
            '/status-with-counts',         // Endpoint: /status-with-counts
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_order_status_with_counts'],
                'permission_callback' => '__return_true', // Allow public access (modify as needed).
            ]
        );
    }

    /**
     * Retrieves the list of WooCommerce orders.
     *
     * @param WP_REST_Request $request The API request.
     * @return WP_REST_Response The response with order data.
     */
    public function get_orders($request)
    {
        $status   = $request->get_param('status');
        $per_page = $request->get_param('per_page');
        $page     = $request->get_param('page');
        $billing_phone = $request->get_param('billing_phone');

        // Use WooCommerce Order Query to fetch orders.
        $args = [
            'status'        => $status,
            'limit'         => $per_page,
            'page'          => $page,
            'billing_phone' => $billing_phone,
            'type'          => 'shop_order',
            'return'        => 'objects',
        ];

        $orders = wc_get_orders($args);

        if (empty($orders)) {
            return rest_ensure_response([
                'message' => 'No orders found.',
                'data'    => [],
            ]);
        }

        // Prepare the order data.
        $data = [];
        global $wpdb;
        foreach ($orders as $order) {
            $product_info = getProductInfo($order);
            $customer_ip = $order->get_meta('_customer_ip_address', true);
            
            // Fetch fraud data from the custom table
            $table_name = $wpdb->prefix . __PREFIX.'fraud_customers';
            $_billing_phone = $order->get_billing_phone();
            $fraud_data = $wpdb->get_row(
                $wpdb->prepare("SELECT report FROM $table_name WHERE customer_id = %d", $_billing_phone),
                ARRAY_A
            );

            $ip_block_listed = $this->get_block_data_by_type($customer_ip, 'ip');
            $phone_block_listed = $this->get_block_data_by_type($_billing_phone, 'phone_number');

            $data[] = [
                'id'            => $order->get_id(),
                'status'        => $order->get_status(),
                'total'         => $order->get_total(),
                'date_created'  => $order->get_date_created() ? $order->get_date_created()->date('M j, Y \a\t g:i A') : null,
                'customer_id'   => $order->get_customer_id(),
                'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                'customer_ip'   => $customer_ip,
                'phone_block_listed' => $phone_block_listed,
                'ip_block_listed' => $ip_block_listed,
                'payment_method' => $order->get_payment_method(), // e.g., 'paypal'
                'payment_method_title' => $order->get_payment_method_title(), // e.g., 'PayPal'
                'transaction_id' => $order->get_transaction_id() ?: '',
                'product_price' => wc_price($product_info['total_price']), // Get the product price
                'product_info' => $product_info,
                'billing_address' => [
                    'type' => 'billing',
                    'order_id'   => $order->get_id(),
                    'first_name' => $order->get_billing_first_name(),
                    'last_name'  => $order->get_billing_last_name(),
                    'company'    => $order->get_billing_company(),
                    'address_1'  => $order->get_billing_address_1(),
                    'address_2'  => $order->get_billing_address_2(),
                    'city'       => $order->get_billing_city(),
                    'state'      => $order->get_billing_state(),
                    'postcode'   => $order->get_billing_postcode(),
                    'country'    => $order->get_billing_country(),
                    'email'      => $order->get_billing_email(),
                    'phone'      => $_billing_phone,
                    'transaction_id' => $order->get_transaction_id() ?: '',
                ],
                'shipping_address' => [
                    'type' => 'shipping',
                    'order_id'   => $order->get_id(),
                    'first_name' => $order->get_shipping_first_name(),
                    'last_name'  => $order->get_shipping_last_name(),
                    'company'    => $order->get_shipping_company(),
                    'address_1'  => $order->get_shipping_address_1(),
                    'address_2'  => $order->get_shipping_address_2(),
                    'city'       => $order->get_shipping_city(),
                    'state'      => $order->get_shipping_state(),
                    'postcode'   => $order->get_shipping_postcode(),
                    'country'    => $order->get_shipping_country(),
                    'customer_note' => $order->get_customer_note()
                ],
                'customer_report' => json_decode($fraud_data['report'], true)[0]['report']
            ];
        }

        return new \WP_REST_Response([
            'status' => 'success',
            'data'   => $data
        ], 200);
    }

    public function get_order_status_with_counts()
    {
        $statuses = wc_get_order_statuses(); // Retrieve all order statuses
        foreach ($statuses as $status_key => $status_label) {
            // Query orders by status
            $args = [
                'status' => str_replace('wc-', '', $status_key), // Remove 'wc-' prefix for the query
                'limit'  => -1,
                'type'     => 'shop_order',
                'return' => 'ids',
            ];
            $orders = wc_get_orders($args);
            $order_count = count($orders);

            if ($order_count > 0) {
                $order_counts[] = [
                    "title" => $status_label,
                    "slug" => str_replace('wc-', '', $status_key),
                    "count" => $order_count
                ]; // Count orders per status
            }
        }

        return new \WP_REST_Response([
            'status' => 'success',
            'data'   => $order_counts
        ], 200);
    }

    private function get_block_data_by_type($value, $type = 'phone_number') {
        global $wpdb;
        $table_name = $wpdb->prefix . __PREFIX . 'block_list';

        // Validate the type to prevent SQL injection
        $allowed_types = ['phone_number', 'ip'];
        if (!in_array($type, $allowed_types, true)) {
            return false; // Invalid type
        }
    
        $query = $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE type = %s AND ip_or_phone = %s",
            $type,
            $value
        );
    
        $result = $wpdb->get_row($query, ARRAY_A);
    
        return $result ? true : false;
    }
    
}


function getProductInfo($order)
{
    $productInfo = [
        'total_price' => 0,
        'product_info' => []
    ];
    if ($order) {
        foreach ($order->get_items() as $item_id => $item) {
            // Get product details
            $product = $item->get_product(); // Get the product object
            $product_image_url = wp_get_attachment_url($product->get_image_id()); // Get the featured image URL

            if ($product) {
                $product_total = $item->get_total(); // Total for the line item (quantity * price)
                $productInfo["total_price"] = (int)$productInfo["total_price"] += (int)$product_total;
                $productInfo["product_info"][] = [
                    'product_name' => $product->get_name(),
                    'product_price' => $product->get_price(),
                    'product_total' => $product_total,
                    'product_quantity' => $item->get_quantity(),
                    'product_image' => $product_image_url
                ];
            }
        }
    }
    return $productInfo;
}
