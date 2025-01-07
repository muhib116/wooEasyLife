<?php

namespace WooEasyLife\API\Admin;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

class OrderStatisticsAPI extends WP_REST_Controller
{

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register REST API routes.
     */
    public function register_routes()
    {
        register_rest_route(
            __API_NAMESPACE,
            '/order-stats',
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_order_statistics'],
                'permission_callback' => '__return_true',
            ]
        );
        register_rest_route(__API_NAMESPACE, '/top-selling-products', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_top_selling_products'],
            'permission_callback' => '__return_true', // Adjust permissions as needed
        ]);
    }

    /**
     * Callback to get WooCommerce order statistics.
     */
    public function get_order_statistics(WP_REST_Request $request)
    {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            return new WP_Error(
                'woocommerce_not_active',
                __('WooCommerce is not active', 'text-domain'),
                ['status' => 404]
            );
        }

        // Get start and end date from request
        $start_date = $request->get_param('start_date');
        $end_date = $request->get_param('end_date');

        // Generate date query
        $args = [
            'status'     => array_keys(wc_get_order_statuses()), // Include all statuses
            'limit'      => -1, // No limit, fetch all orders
        ];

        if ($start_date && $end_date) {
            $args['date_query'] = $this->get_date_query($start_date, $end_date);
        }

        $orders = wc_get_orders($args);

        // Initialize summary variables
        $summary = [
            'total_orders'    => 0,
            'status_wise'     => [],
            'total_revenue'   => 0,
        ];

        // Loop through orders
        foreach ($orders as $order) {
            $summary['total_orders']++;

            // Add revenue
            $summary['total_revenue'] += $order->get_total();

            // Count statuses
            $status = $order->get_status();
            if (!isset($summary['status_wise'][$status])) {
                $summary['status_wise'][$status] = 0;
            }
            $summary['status_wise'][$status]++;
        }

        // Return aggregated data
        return new WP_REST_Response([
            'status' => 'success',
            'data'   => $summary,
        ], 200);
    }

    /**
     * Generate a WooCommerce-compatible date query for a range.
     */
    private function get_date_query($start_date, $end_date)
    {
        return [
            'after'     => date('Y-m-d 00:00:00', strtotime($start_date)),
            'before'    => date('Y-m-d 23:59:59', strtotime($end_date)),
            'inclusive' => true,
        ];
    }


    /**
     * Get Top-Selling Products
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_top_selling_products(WP_REST_Request $request)
    {
        $limit = intval($request->get_param('limit') ?? 10); // Default to 10 products if no limit provided

        // Use WC_Product_Query to get top-selling products
        $args = [
            'limit'    => $limit,
            'orderby'  => 'total_sales',
            'order'    => 'DESC',
            'status'   => 'publish',
            'return'   => 'ids',
        ];


        $query = new \WC_Product_Query($args);
        $products = $query->get_products();

        if (empty($products)) {
            return new WP_REST_Response([
                'status'  => 'error',
                'message' => 'No top-selling products found.',
            ], 404);
        }

        $data = array_map(function ($product_id) {
            $product = wc_get_product($product_id);

            return [
                'product_id'   => $product->get_id(),
                'product_name' => $product->get_name(),
                'total_sold'   => $product->get_total_sales(),
                'price'        => $product->get_price(),
                'image'        => wp_get_attachment_url($product->get_image_id()),
                'stock_status' => $product->get_stock_status(), // 'instock', 'outofstock', or 'onbackorder'
                'stock_quantity' => $product->get_stock_quantity(), // Null for products without stock management
                'manage_stock' => $product->managing_stock(), // Boolean: Whether stock is managed    
            ];
        }, $products);

        return new WP_REST_Response([
            'status' => 'success',
            'data'   => $data,
        ], 200);
    }
}