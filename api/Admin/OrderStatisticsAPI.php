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

    public function get_top_selling_products(WP_REST_Request $request)
    {
        $limit = intval($request->get_param('limit') ?? 10); // Default to 10 products if no limit provided
    
        // Use a direct query to ensure accurate sales data
        global $wpdb;
    
        $query = $wpdb->prepare(
            "SELECT p.ID, p.post_title, pm.meta_value as total_sales
            FROM {$wpdb->prefix}posts AS p
            LEFT JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND pm.meta_key = 'total_sales'
            ORDER BY CAST(pm.meta_value AS UNSIGNED) DESC
            LIMIT %d",
            $limit
        );
    
        $results = $wpdb->get_results($query);
    
        if (empty($results)) {
            return new WP_REST_Response([
                'status'  => 'error',
                'message' => 'No top-selling products found.',
            ], 404);
        }
    
        $data = array_map(function ($result) {
            $product = wc_get_product($result->ID);
    
            return [
                'product_id'   => $result->ID,
                'product_name' => $product->get_name(),
                'total_sold'   => intval($result->total_sales),
                'price'        => $product->get_price(),
                'image'        => wp_get_attachment_url($product->get_image_id()),
                'stock_status' => $product->get_stock_status(), // 'instock', 'outofstock', or 'onbackorder'
                'stock_quantity' => $product->get_stock_quantity() ?: 'Not managing stock', // Null for products without stock management
                'manage_stock' => $product->managing_stock() ? 'Managing' : 'Not managing',
                'low_stock_threshold' => $product->get_low_stock_amount() ?: false, // Get low stock threshold
            ];
        }, $results);
    
        return new WP_REST_Response([
            'status' => 'success',
            'data'   => $data,
        ], 200);
    }
    
}