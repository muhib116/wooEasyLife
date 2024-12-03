<?php

namespace WooEasyLife\Admin;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class CustomOrderStatusAPI extends WP_REST_Controller {

    public function __construct() {
        add_action('rest_api_init', [ $this, 'register_routes' ]);
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route('wooeasylife/v1', '/statuses', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_statuses' ],
                'permission_callback' => [ $this, 'permissions_check' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'create_status' ],
                'permission_callback' => [ $this, 'permissions_check' ],
                'args'                => $this->get_status_schema(false), // No 'id' required for creation
            ]
        ]);

        register_rest_route('wooeasylife/v1', '/statuses/(?P<id>[a-zA-Z0-9\-_]+)', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_status' ],
                'permission_callback' => [ $this, 'permissions_check' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'update_status' ],
                'permission_callback' => [ $this, 'permissions_check' ],
                'args'                => $this->get_status_schema(true), // 'id' required for update
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'delete_status' ],
                'permission_callback' => [ $this, 'permissions_check' ],
            ],
        ]);
    }

    /**
     * Permissions callback for the endpoints.
     */
    public function permissions_check() {
        return '__return_true'; // Restrict to WooCommerce admins
    }

    /**
     * Get all custom statuses
     */
    public function get_statuses() {
        $statuses = get_option('custom_order_statuses', []);

        return new WP_REST_Response([
            'status' => 'success',
            'data'   => $statuses,
        ], 200);
    }

    /**
     * Get a single custom status
     */
    public function get_status(WP_REST_Request $request) {
        $statuses = get_option('custom_order_statuses', []);
        $status_id = $request->get_param('id');

        if (!isset($statuses[$status_id])) {
            return new WP_Error('not_found', 'Status not found', [ 'status' => 404 ]);
        }

        return new WP_REST_Response([
            'status' => 'success',
            'data'   => $statuses[$status_id],
        ], 200);
    }

    /**
     * Create a new custom status
     */
    public function create_status(WP_REST_Request $request) {
        $statuses = get_option('custom_order_statuses', []);

        // Generate a unique ID if not provided
        $id = sanitize_title($request->get_param('id') ?? uniqid('status_'));

        if (isset($statuses[$id])) {
            return new WP_Error('status_exists', 'A status with this ID already exists', [ 'status' => 400 ]);
        }

        $data = [
            'label'       => sanitize_text_field($request->get_param('label')),
            'color'       => sanitize_hex_color($request->get_param('color')),
            'description' => sanitize_textarea_field($request->get_param('description')),
        ];

        $statuses[$id] = $data;
        update_option('custom_order_statuses', $statuses);

        return new WP_REST_Response([
            'status'  => 'success',
            'message' => 'Status created successfully',
            'data'    => $data,
        ], 201);
    }

    /**
     * Update an existing custom status
     */
    public function update_status(WP_REST_Request $request) {
        $statuses = get_option('custom_order_statuses', []);
        $id = sanitize_title($request->get_param('id'));

        if (!isset($statuses[$id])) {
            return new WP_Error('not_found', 'Status not found', [ 'status' => 404 ]);
        }

        $statuses[$id] = [
            'label'       => sanitize_text_field($request->get_param('label')),
            'color'       => sanitize_hex_color($request->get_param('color')),
            'description' => sanitize_textarea_field($request->get_param('description')),
        ];

        update_option('custom_order_statuses', $statuses);

        return new WP_REST_Response([
            'status'  => 'success',
            'message' => 'Status updated successfully',
            'data'    => $statuses[$id],
        ], 200);
    }

    /**
     * Delete a custom status
     */
    public function delete_status(WP_REST_Request $request) {
        $statuses = get_option('custom_order_statuses', []);
        $id = sanitize_title($request->get_param('id'));

        if (!isset($statuses[$id])) {
            return new WP_Error('not_found', 'Status not found', [ 'status' => 404 ]);
        }

        unset($statuses[$id]);
        update_option('custom_order_statuses', $statuses);

        return new WP_REST_Response([
            'status'  => 'success',
            'message' => 'Status deleted successfully',
        ], 200);
    }

    /**
     * Schema for status input validation
     */
    public function get_status_schema($require_id = true) {
        $schema = [
            'label' => [
                'required' => true,
                'type'     => 'string',
                'description' => 'Label for the status.',
            ],
            'color' => [
                'required' => true,
                'type'     => 'string',
                'description' => 'Hex color for the status.',
            ],
            'description' => [
                'required' => false,
                'type'     => 'string',
                'description' => 'Description for the status.',
            ],
        ];

        if ($require_id) {
            $schema['id'] = [
                'required' => true,
                'type'     => 'string',
                'description' => 'Unique identifier for the status.',
            ];
        }

        return $schema;
    }
}