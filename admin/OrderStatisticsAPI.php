<?php

namespace WooEasyLife\Admin;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

class OrderStatisticsAPI extends WP_REST_Controller {

    public function __construct() {
        add_action('rest_api_init', [ $this, 'register_routes' ]);
    }

    /**
     * Register REST API routes.
     */
    public function register_routes() {
        register_rest_route(
            'wooeasylife/v1',
            '/order-stats',
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_order_statistics' ],
                'permission_callback' => '__return_true',
            ]
        );
    }

    /**
     * Callback to get WooCommerce order statistics.
     */
    public function get_order_statistics(WP_REST_Request $request) {
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
    private function get_date_query($start_date, $end_date) {
        return [
            'after'     => date('Y-m-d 00:00:00', strtotime($start_date)),
            'before'    => date('Y-m-d 23:59:59', strtotime($end_date)),
            'inclusive' => true,
        ];
    }
}
