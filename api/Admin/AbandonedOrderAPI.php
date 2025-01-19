<?php

namespace WooEasyLife\API\Admin;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

class AbandonedOrderAPI extends WP_REST_Controller {
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . __PREFIX . 'abandon_cart';

        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route(__API_NAMESPACE, '/abandoned-orders', [
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_all_abandoned_orders'],
                'permission_callback' => api_permission_check(),
            ],
            [
                'methods'             => 'POST',
                'callback'            => [$this, 'create_abandoned_order'],
                'permission_callback' => api_permission_check(),
                'args'                => $this->get_abandoned_order_schema(false),
            ],
        ]);

        register_rest_route(__API_NAMESPACE, '/abandoned-orders/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_abandoned_order_by_id'],
                'permission_callback' => api_permission_check(),
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [$this, 'update_abandoned_order'],
                'permission_callback' => api_permission_check(),
                'args'                => $this->get_abandoned_order_schema(true),
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [$this, 'delete_abandoned_order'],
                'permission_callback' => api_permission_check(),
            ],
        ]);
    }

    /**
     * Get all abandoned orders
     */
    public function get_all_abandoned_orders() {
        global $wpdb;

        $results = $wpdb->get_results("SELECT * FROM {$this->table_name}", ARRAY_A);

        if (empty($results)) {
            return new WP_REST_Response([
                'status'  => 'success',
                'message' => 'No abandoned orders found.',
                'data'    => [],
            ], 200);
        }

        // Deserialize cart_contents for each result
        foreach ($results as &$result) {
            if (isset($result['cart_contents'])) {
                $result['cart_contents'] = maybe_unserialize($result['cart_contents']);
            }
        }

        return new WP_REST_Response([
            'status'  => 'success',
            'message' => 'Abandoned orders retrieved successfully.',
            'data'    => $results,
        ], 200);
    }

    /**
     * Create a new abandoned order
     */
    public function create_abandoned_order(WP_REST_Request $request) {
        global $wpdb;

        $customer_email = sanitize_email($request->get_param('customer_email'));
        $cart_contents = maybe_serialize($request->get_param('cart_contents'));
        $total_value   = floatval($request->get_param('total_value'));
        $abandoned_at  = current_time('mysql');
        $updated_at    = current_time('mysql');

        // Insert the new abandoned order
        $inserted = $wpdb->insert(
            $this->table_name,
            [
                'customer_email' => $customer_email,
                'cart_contents'  => $cart_contents,
                'total_value'    => $total_value,
                'abandoned_at'   => $abandoned_at,
                'updated_at'     => $updated_at,
            ],
            [
                '%s',
                '%s',
                '%f',
                '%s',
                '%s',
            ]
        );

        if ($inserted === false) {
            return new WP_REST_Response([
                'status'  => 'error',
                'message' => 'Failed to create abandoned order.',
            ], 500);
        }

        return new WP_REST_Response([
            'status'  => 'success',
            'message' => 'Abandoned order created successfully.',
            'data'    => [
                'id'             => $wpdb->insert_id,
                'customer_email' => $customer_email,
                'cart_contents'  => maybe_unserialize($cart_contents),
                'total_value'    => $total_value,
                'abandoned_at'   => $abandoned_at,
                'updated_at'     => $updated_at,
            ],
        ], 201);
    }

    /**
     * Get an abandoned order by ID
     */
    public function get_abandoned_order_by_id(WP_REST_Request $request) {
        global $wpdb;

        $id = $request->get_param('id');
        $result = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id),
            ARRAY_A
        );

        if (empty($result)) {
            return new WP_REST_Response([
                'status'  => 'error',
                'message' => 'Abandoned order not found.',
            ], 404);
        }

        // Deserialize cart_contents
        if (isset($result['cart_contents'])) {
            $result['cart_contents'] = maybe_unserialize($result['cart_contents']);
        }

        return new WP_REST_Response([
            'status'  => 'success',
            'message' => 'Abandoned order retrieved successfully.',
            'data'    => $result,
        ], 200);
    }

    /**
     * Update an abandoned order by ID
     */
    public function update_abandoned_order(WP_REST_Request $request) {
        global $wpdb;

        $id             = $request->get_param('id');
        $customer_email = sanitize_email($request->get_param('customer_email'));
        $cart_contents  = maybe_serialize($request->get_param('cart_contents'));
        $total_value    = floatval($request->get_param('total_value'));
        $updated_at     = current_time('mysql');

        $updated = $wpdb->update(
            $this->table_name,
            [
                'customer_email' => $customer_email,
                'cart_contents'  => $cart_contents,
                'total_value'    => $total_value,
                'updated_at'     => $updated_at,
            ],
            ['id' => $id],
            [
                '%s',
                '%s',
                '%f',
                '%s',
            ],
            ['%d']
        );

        if ($updated === false) {
            return new WP_REST_Response([
                'status'  => 'error',
                'message' => 'Failed to update abandoned order.',
            ], 500);
        }

        return new WP_REST_Response([
            'status'  => 'success',
            'message' => 'Abandoned order updated successfully.',
            'data'    => [
                'id'             => $id,
                'customer_email' => $customer_email,
                'cart_contents'  => maybe_unserialize($cart_contents),
                'total_value'    => $total_value,
                'updated_at'     => $updated_at,
            ],
        ], 200);
    }

    /**
     * Delete an abandoned order by ID
     */
    public function delete_abandoned_order(WP_REST_Request $request) {
        global $wpdb;

        $id = $request->get_param('id');
        $deleted = $wpdb->delete(
            $this->table_name,
            ['id' => $id],
            ['%d']
        );

        if ($deleted === false) {
            return new WP_REST_Response([
                'status'  => 'error',
                'message' => 'Failed to delete abandoned order.',
            ], 500);
        }

        return new WP_REST_Response([
            'status'  => 'success',
            'message' => 'Abandoned order deleted successfully.',
        ], 200);
    }

    /**
     * Schema for abandoned order input validation
     */
    private function get_abandoned_order_schema($require_id = false) {
        $schema = [
            'customer_email' => [
                'required'    => true,
                'type'        => 'string',
                'format'      => 'email',
                'description' => 'Customer email address.',
            ],
            'cart_contents' => [
                'required'    => true,
                'type'        => 'array',
                'description' => 'Contents of the abandoned cart.',
            ],
            'total_value' => [
                'required'    => true,
                'type'        => 'number',
                'description' => 'Total value of the abandoned cart.',
            ],
        ];

        if ($require_id) {
            $schema['id'] = [
                'required'    => true,
                'type'        => 'integer',
                'description' => 'Unique identifier for the abandoned order.',
            ];
        }

        return $schema;
    }
}
