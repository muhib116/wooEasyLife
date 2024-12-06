<?php

namespace WooEasyLife\API\Admin;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class ConfigAPI extends WP_REST_Controller {
    private $option_name = 'woo_easy_life_config';

    public function __construct() {
        add_action('rest_api_init', [ $this, 'register_routes' ]);
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route('wooeasylife/v1', '/config-integration', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_integration_data' ],
                'permission_callback' => [ $this, 'permissions_check' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'create_or_update_integration_data' ],
                'permission_callback' => [ $this, 'permissions_check' ],
                'args'                => $this->get_schema(),
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'delete_integration_data' ],
                'permission_callback' => [ $this, 'permissions_check' ],
            ],
        ]);
    }

    /**
     * Permissions callback for the endpoints
     */
    public function permissions_check() {
        return '__return_true'; // Allow all for now (you can restrict later)
        // current_user_can('manage_options');
    }

    /**
     * GET: Retrieve the JSON data
     */
    public function get_integration_data() {
        $data = get_option($this->option_name, []);
        $decoded_data = is_string($data) ? json_decode($data, true) : $data;

        return new WP_REST_Response([
            'status' => 'success',
            'data'   => $decoded_data ?: [],
        ], 200);
    }

    /**
     * POST: Create or Update JSON data
     */
    public function create_or_update_integration_data(WP_REST_Request $request) {
        $data = $request->get_json_params();

        if (!is_array($data)) {
            return new WP_Error('invalid_data', 'Data must be a valid JSON object.', [ 'status' => 400 ]);
        }

        update_option($this->option_name, json_encode($data));

        return new  ([
            'status'  => 'success',
            'message' => 'Data saved successfully.',
            'data'    => $data,
        ], 200);
    }

    /**
     * DELETE: Remove the JSON data
     */
    public function delete_integration_data() {
        delete_option($this->option_name);

        return new WP_REST_Response([
            'status'  => 'success',
            'message' => 'Data deleted successfully.',
        ], 200);
    }

    /**
     * Schema for input validation
     */
    public function get_schema() {
        return [
            'data' => [
                'required'    => true,
                'type'        => 'object',
                'description' => 'The integration data as a JSON object.',
            ],
        ];
    }
}