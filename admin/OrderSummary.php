<?php
namespace WooEasyLife\Admin;

use WP_REST_Controller;

class OrderSummary extends WP_REST_Controller {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('rest_api_init', [ $this, 'register_routes' ]);
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route(
            'wooeasylife/v1', // Namespace/Version
            '/order-summary', // Endpoint
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_order_summary' ],
                'permission_callback' => '__return_true', // Adjust permissions as needed
                'args'                => $this->get_endpoint_args(),
            ]
        );
    }

    /**
     * Define query parameters
     *
     * @return array
     */
    public function get_endpoint_args() {
        return [
            'start_date' => [
                'required' => false,
                'type'     => 'string',
                'description' => 'Filter orders from this start date (YYYY-MM-DD)',
            ],
            'end_date' => [
                'required' => false,
                'type'     => 'string',
                'description' => 'Filter orders up to this end date (YYYY-MM-DD)',
            ],
        ];
    }

    /**
     * Callback for the `/order-summary` endpoint
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_order_summary( $request ) {
        $start_date = $request->get_param('start_date');
        $end_date   = $request->get_param('end_date');

        $args = [
            'post_type'      => 'shop_order', // WooCommerce orders post type.
            'post_status'    => array_keys(wc_get_order_statuses()), // Fetch orders with all available statuses.
            // 'posts_per_page' => -1, // Retrieve all orders (no pagination).
            'orderby'        => 'date', // Sort orders by date (optional).
            'order'          => 'DESC', // Descending order (optional).
        ];

        // Add date filtering if provided
        // if ( $start_date ) {
        //     $args['date_query'][] = [
        //         'after' => $start_date,
        //     ];
        // }

        // if ( $end_date ) {
        //     $args['date_query'][] = [
        //         'before' => $end_date,
        //     ];
        // }

        $orders_query = new \WP_Query($args);
        $orders = $orders_query->posts;

        $summary = [
            'total_orders'     => 0,
            'pending_orders'   => 0,
            'processing_orders'=> 0,
            'completed_orders' => 0,
            'cancelled_orders' => 0,
            'refunded_orders'  => 0,
            'failed_orders'    => 0,
        ];

        // foreach ( $orders as $order_post ) {
        //     $order = wc_get_order($order_post->ID);
        //     $status = $order->get_status();

        //     $summary['total_orders']++;
        //     if ( isset($summary["{$status}_orders"]) ) {
        //         $summary["{$status}_orders"]++;
        //     }
        // }

        return rest_ensure_response($orders);
    }
}