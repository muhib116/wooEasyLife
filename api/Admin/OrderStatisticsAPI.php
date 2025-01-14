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
                'permission_callback' => api_permission_check(),
            ]
        );
        register_rest_route(__API_NAMESPACE, '/sales-summary', [
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_sales_summary'],
                'permission_callback' => api_permission_check(),
            ],
        ]);
        register_rest_route(__API_NAMESPACE, '/top-selling-products', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_top_selling_products'],
            'permission_callback' => api_permission_check(), // Adjust permissions as needed
        ]);
        register_rest_route(__API_NAMESPACE, '/sales-progress', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_sales_progress'],
            'permission_callback' => api_permission_check(), // Adjust permissions as needed
        ]);
        register_rest_route(__API_NAMESPACE, '/order-progress', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_order_progress'],
            'permission_callback' => api_permission_check(), // Adjust permissions as needed
        ]);
        register_rest_route(__API_NAMESPACE, '/orders-grouped-by-created-via', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_orders_grouped_by_created_via'],
            'permission_callback' => api_permission_check(), // Adjust permissions as needed
        ]);
        register_rest_route(__API_NAMESPACE, '/order-cycle-time', [
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_order_cycle_time'],
                'permission_callback' => api_permission_check(),
            ],
        ]);
    }

    /**
     * Callback to get WooCommerce order statistics.
     */
    public function get_order_statistics(WP_REST_Request $request)
    {
        // Get start and end date from request
        $start_date = $request->get_param('start_date');
        $end_date = $request->get_param('end_date');

        // Generate date query
        $args = [
            'type'   => 'shop_order',
            'status' => array_keys(wc_get_order_statuses()), // Include all statuses
            'limit'  => -1, // No limit, fetch all orders
            'orderby'      => 'date',
            'order'        => 'DESC',
        ];

        if ($start_date && $end_date) {
            $args['date_query'] = $this->get_date_query($start_date, $end_date);
        }

        $orders = wc_get_orders($args);

        // Initialize summary variables
        $summary = [
            'total_orders'    => 0,
            'status_wise'     => []
        ];

        // Loop through orders
        foreach ($orders as $order) {
            $summary['total_orders']++;

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
     * Get comprehensive sales data within a specified date range.
     *
     * @param WP_REST_Request $request The REST API request.
     * @return \WP_REST_Response The sales data.
     */
    public function get_sales_summary(WP_REST_Request $request) {
        // Get the start and end dates from the request
        $start_date = $request->get_param('start_date') ?: date('Y-m-d', strtotime('-7 days')); // Default to last 7 days
        $end_date = $request->get_param('end_date') ?: date('Y-m-d'); // Default to today
        $status = $request->get_param('status') ?: 'completed'; // Default to today

        // Validate dates
        if (strtotime($start_date) > strtotime($end_date)) {
            return new \WP_REST_Response([
                'status'  => 'error',
                'message' => 'Invalid date range. Start date cannot be later than the end date.',
            ], 400);
        }

        // Fetch orders within the date range
        $args = [
            'type'         => 'shop_order',
            'status'       => ['wc-'.$status], // Relevant statuses
            'limit'        => -1, // Retrieve all matching orders
            'date_created' => $start_date . '...' . $end_date, // Date range
            'orderby'      => 'date',
            'order'        => 'DESC',
            'return'       => 'objects', // Return full order objects
        ];

        $orders = wc_get_orders($args);

        // If no orders are found
        if (empty($orders)) {
            return new \WP_REST_Response([
                'status'  => 'success',
                'message' => 'No orders found in the specified date range.',
                'data'    => [
                    'total_sale_amount'      => 0,
                    'total_discount_amount'  => 0,
                    'total_orders'           => 0,
                    'average_order_value'    => 0,
                    'total_shipping_cost'    => 0,
                    'start_date'             => $start_date,
                    'end_date'               => $end_date,
                ],
            ], 200);
        }

        // Initialize variables for calculations
        $total_sale_amount = 0;
        $total_discount_amount = 0;
        $total_shipping_cost = 0;
        $total_orders = count($orders);

        foreach ($orders as $order) {
            $total_sale_amount += $order->get_total(); // Total amount including shipping and discounts
            $total_discount_amount += $order->get_discount_total(); // Total discount amount
            $total_shipping_cost += $order->get_shipping_total(); // Total shipping cost
        }

        // Calculate average order value
        $average_order_value = $total_sale_amount / $total_orders;

        return new \WP_REST_Response([
            'status'  => 'success',
            'message' => 'Sales summary retrieved successfully.',
            'data'    => [
                'total_sale_amount'      => wc_price($total_sale_amount), // Total sale amount
                'total_discount_amount'  => wc_price($total_discount_amount), // Total discount amount
                'total_orders'           => $total_orders, // Total orders
                'average_order_value'    => wc_price($average_order_value), // Average order value
                'total_shipping_cost'    => wc_price($total_shipping_cost), // Total shipping cost
                'start_date'             => $start_date, // Start date
                'end_date'               => $end_date, // End date
            ],
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

    
    public function get_sales_progress(WP_REST_Request $request)
    {
        global $wpdb;
    
        // Retrieve the start_date and end_date from the request
        $start_date = $request->get_param('start_date') ?? date('Y-m-d', strtotime('-6 days'));
        $end_date = $request->get_param('end_date') ?? date('Y-m-d');
    
        // Validate the date format
        if (!strtotime($start_date) || !strtotime($end_date)) {
            return new \WP_REST_Response([
                'status'  => 'error',
                'message' => 'Invalid date format. Use YYYY-MM-DD.',
            ], 400);
        }
    
        $args = [
            'status'       => ['wc-completed'], // Only completed orders
            'limit'        => -1, // Retrieve all orders
            'orderby'      => 'date',
            'order'        => 'DESC', // Descending order
            'return'       => 'objects', // Return full order objects
            'type'         => 'shop_order',
            'date_created' => $start_date . '...' . $end_date, // Date range
        ];
    
        // Fetch orders using wc_get_orders
        $orders = wc_get_orders($args);
    
        // Check if any orders are found
        if (empty($orders)) {
            return new \WP_REST_Response([
                'status'    => 'success',
                'data'      => [
                    'series'     => [['name' => 'Total sale', 'data' => []]],
                    'categories' => [],
                ],
            ], 200);
        }
    
        // Initialize an array to store sales count by date
        $sales_count = [];
        foreach ($orders as $order) {
            $date = $order->get_date_created() ? $order->get_date_created()->date('Y-m-d') : null;
    
            if (!isset($sales_count[$date])) {
                $sales_count[$date] = 0;
            }
    
            $sales_count[$date] += 1; // Increment the count for the date
        }
    
        // Format the response
        $series = [];
        $categories = [];
        $current_date = strtotime($start_date);
        $end_date_timestamp = strtotime($end_date);
    
        while ($current_date <= $end_date_timestamp) {
            $date = date('Y-m-d', $current_date);
            $categories[] = date('y-M-d', $current_date);
            $series[] = isset($sales_count[$date]) ? $sales_count[$date] : 0;
            $current_date = strtotime('+1 day', $current_date);
        }
    
        return new \WP_REST_Response([
            'status'    => 'success',
            'data'      => [
                'series'     => [['name' => 'Total sale', 'data' => $series]],
                'categories' => $categories,
            ],
        ], 200);
    }
    
    public function get_order_progress(WP_REST_Request $request)
    {
        global $wpdb;
    
        // Retrieve the start_date and end_date from the request
        $start_date = $request->get_param('start_date') ?? date('Y-m-d', strtotime('-6 days'));
        $end_date = $request->get_param('end_date') ?? date('Y-m-d');
    
        // Validate the date format
        if (!strtotime($start_date) || !strtotime($end_date)) {
            return new \WP_REST_Response([
                'status'  => 'error',
                'message' => 'Invalid date format. Use YYYY-MM-DD.',
            ], 400);
        }
    
        $args = [
            'limit'        => -1, // Retrieve all orders
            'orderby'      => 'date',
            'order'        => 'DESC', // Descending order
            'return'       => 'objects', // Return full order objects
            'type'         => 'shop_order',
            'date_created' => $start_date . '...' . $end_date, // Date range
        ];
    
        // Fetch orders using wc_get_orders
        $orders = wc_get_orders($args);
    
        // Check if any orders are found
        if (empty($orders)) {
            return new \WP_REST_Response([
                'status'    => 'success',
                'data'      => [
                    'series'     => [['name' => 'Total order', 'data' => []]],
                    'categories' => [],
                ],
            ], 200);
        }
    
        // Initialize an array to store sales count by date
        $sales_count = [];
        foreach ($orders as $order) {
            $date = $order->get_date_created() ? $order->get_date_created()->date('Y-m-d') : null;
    
            if (!isset($sales_count[$date])) {
                $sales_count[$date] = 0;
            }
    
            $sales_count[$date] += 1; // Increment the count for the date
        }
    
        // Format the response
        $series = [];
        $categories = [];
        $current_date = strtotime($start_date);
        $end_date_timestamp = strtotime($end_date);
    
        while ($current_date <= $end_date_timestamp) {
            $date = date('Y-m-d', $current_date);
            $categories[] = date('y-M-d', $current_date);
            $series[] = isset($sales_count[$date]) ? $sales_count[$date] : 0;
            $current_date = strtotime('+1 day', $current_date);
        }
    
        return new \WP_REST_Response([
            'status'    => 'success',
            'data'      => [
                'series'     => [['name' => 'Total order', 'data' => $series]],
                'categories' => $categories,
            ],
        ], 200);
    }
    
    public function get_orders_grouped_by_created_via(WP_REST_Request $request) {
        // Retrieve the start_date and end_date from the request
        $start_date = $request->get_param('start_date') ?? date('Y-m-d', strtotime('-6 days'));
        $end_date = $request->get_param('end_date') ?? date('Y-m-d');
    
    
        // Define query arguments
        $args = [
            'limit'       => -1, // Fetch all orders
            'orderby'     => 'date',
            'order'       => 'DESC',
            'return'      => 'objects',
            'type'         => 'shop_order',
            'date_created' => $start_date . '...' . $end_date, // Date range
        ];
    
        // Fetch orders using WooCommerce's wc_get_orders function
        $orders = wc_get_orders($args);
    
        // Initialize an array to store grouped data
        $grouped_data = [];
    
        // Group orders by 'created_via'
        foreach ($orders as $order) {
            $created_via = $order->get_meta('_created_via', true);
    
            if (!isset($grouped_data[$created_via])) {
                $grouped_data[$created_via] = [
                    'created_via' => $created_via ?: 'unknown',
                    'total_orders' => 0,
                    'total_amount' => 0,
                ];
            }
    
            $grouped_data[$created_via]['total_orders']++;
            $grouped_data[$created_via]['total_amount'] += $order->get_total();
        }

        // Prepare data for ApexCharts
        $categories = [];
        $total_orders_data = [];
        $total_amount_data = [];

        foreach ($grouped_data as $group) {
            $categories[] = ucfirst($group['created_via']); // Capitalize the source for categories
            $total_orders_data[] = $group['total_orders'];
            $total_amount_data[] = $group['total_amount'];
        }

        // Format response for ApexCharts
        $response = [
            'categories' => $categories,
            'series' => [
                [
                    'name' => 'Total Order',
                    'data' => $total_orders_data,
                ],
                [
                    'name' => 'Total Amount',
                    'data' => $total_amount_data,
                ],
            ],
        ];
    
        return new \WP_REST_Response([
            'status' => 'success',
            'data'   => $response,
        ], 200);
    }
    
    public function get_order_cycle_time(WP_REST_Request $request)
    {

        // Retrieve the start_date and end_date from the request
        $start_date = $request->get_param('start_date') ?? date('Y-m-d', strtotime('-6 days'));
        $end_date = $request->get_param('end_date') ?? date('Y-m-d');
    
        // Fetch all orders
        $args = [
            'limit'    => -1, // Get all orders
            'type'     => 'shop_order',
            'return'   => 'objects', // Return full order objects
            'date_created' => $start_date . '...' . $end_date, // Date range
        ];

        $orders = wc_get_orders($args);

        if (empty($orders)) {
            return new \WP_REST_Response([
                'status'  => 'error',
                'message' => 'No orders found.',
                'data'    => [],
            ], 404);
        }

        // Prepare data for chart
        $status_durations = []; // To store cumulative durations for each status pair
        $status_counts = []; // To count occurrences of each status pair

        foreach ($orders as $order) {
            $status_changes = $order->get_meta('_status_history', true);

            if (empty($status_changes)) {
                continue; // Skip orders with no status history
            }

            // Decode JSON if stored in that format
            if (is_string($status_changes)) {
                $status_changes = json_decode($status_changes, true);
            }

            // Sort status changes by date
            usort($status_changes, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            $previous_status = null;
            $previous_date = null;

            foreach ($status_changes as $change) {
                $current_status = $change['status'];
                $current_date = $change['date'];

                // Skip if no previous status to compare
                if ($previous_status !== null && $previous_date !== null) {
                    $duration = strtotime($current_date) - strtotime($previous_date);
                    $status_key = "{$previous_status} -> {$current_status}";

                    // Aggregate durations and counts
                    if (!isset($status_durations[$status_key])) {
                        $status_durations[$status_key] = 0;
                        $status_counts[$status_key] = 0;
                    }

                    $status_durations[$status_key] += $duration;
                    $status_counts[$status_key]++;
                }

                // Update previous status and date
                $previous_status = $current_status;
                $previous_date = $current_date;
            }
        }

        // Prepare data for ApexChart
        $categories = array_keys($status_durations);
        $series = [
            [
                'name' => 'Average Duration (in minutes)',
                'data' => array_map(function ($key) use ($status_durations, $status_counts) {
                    $average_duration = $status_durations[$key] / $status_counts[$key];
                    return round($average_duration / 60, 2); // Convert to minutes
                }, $categories),
            ],
            // [
            //     'name' => 'Transition Count',
            //     'data' => array_values($status_counts),
            // ],
        ];

        return new \WP_REST_Response([
            'status' => 'success',
            'data'   => [
                'categories' => $categories,
                'series'     => $series,
            ],
        ], 200);
    }
}