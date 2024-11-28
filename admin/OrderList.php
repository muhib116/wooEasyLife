<?php
namespace WooEasyLife\Admin;

class OrderList {

    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    /**
     * Registers the routes for the custom endpoint.
     */
    public function register_routes() {
        register_rest_route(
            'wooeasylife/v1', // Namespace and version.
            '/orders',         // Endpoint: /orders
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_orders' ],
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
    }

    /**
     * Retrieves the list of WooCommerce orders.
     *
     * @param WP_REST_Request $request The API request.
     * @return WP_REST_Response The response with order data.
     */
    public function get_orders( $request ) {
        $status   = $request->get_param( 'status' );
        $per_page = $request->get_param( 'per_page' );
        $page     = $request->get_param( 'page' );

        // Use WooCommerce Order Query to fetch orders.
        $args = [
            'status'   => $status,
            'limit'    => $per_page,
            'page'     => $page,
            'return'   => 'objects',
        ];

        $orders = wc_get_orders( $args );

        if ( empty( $orders ) ) {
            return rest_ensure_response( [
                'message' => 'No orders found.',
                'data'    => [],
            ] );
        }

        // Prepare the order data.
        $data = [];
        foreach ( $orders as $order ) {
            $product_info = getProductInfo($order);
            $data[] = [
                'id'            => $order->get_id(),
                'status'        => $order->get_status(),
                'total'         => $order->get_total(),
                'date_created'  => $order->get_date_created() ? $order->get_date_created()->date( 'M j, Y \a\t g:i A' ) : null,
                'customer_id'   => $order->get_customer_id(),
                'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                'customer_ip'   => $order->get_meta( '_customer_ip_address', true ),
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
                    'phone'      => $order->get_billing_phone(),
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
                ]
            ];
        }

        return rest_ensure_response($data);
    }
}


function getProductInfo($order) {
    $productInfo = [
        'total_price' => 0,
        'product_info' => []
    ];
    if ($order) {
        foreach ($order->get_items() as $item_id => $item) {
            // Get product details
            $product = $item->get_product(); // Get the product object
    
            if ($product) {
                $product_total = $item->get_total(); // Total for the line item (quantity * price)
                $productInfo["total_price"] = (Int)$productInfo["total_price"] += (Int)$product_total;
                $productInfo["product_info"][] = [
                    'product_name' => $product->get_name(),
                    'product_price' => $product->get_price(),
                    'product_total' => $product_total,
                    'product_quantity' => $item->get_quantity()
                ];
            }
        }
    }
    return $productInfo;
}