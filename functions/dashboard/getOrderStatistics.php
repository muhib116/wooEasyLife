<?php
function get_woocommerce_order_statistics() {
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return new WP_Error(
            'woocommerce_not_active',
            __('WooCommerce is not active', 'text-domain'),
            ['status' => 404]
        );
    }

    // Retrieve order counts by status
    $total_orders = wc_orders_count(); // Total orders
    $pending_orders = wc_orders_count('pending'); // Pending orders
    $canceled_orders = wc_orders_count('cancelled'); // Canceled orders
    $completed_orders = wc_orders_count('completed'); // Completed orders
    $refunded_orders = wc_orders_count('refunded'); // Refunded orders

    // Return aggregated data
    return rest_ensure_response([
        'status' => 'success',
        'data' => [
            'total_orders' => $total_orders,
            'pending_orders' => $pending_orders,
            'canceled_orders' => $canceled_orders,
            'completed_orders' => $completed_orders,
            'refunded_orders' => $refunded_orders,
        ],
    ]);
}

// Register the REST API route
add_action('rest_api_init', function () {
    register_rest_route('wc/v1', '/order-stats', [
        'methods' => 'GET',
        'callback' => 'get_woocommerce_order_statistics',
        'permission_callback' => function () {
            return current_user_can('manage_woocommerce'); // Restrict to WooCommerce admins
        },
    ]);
});
