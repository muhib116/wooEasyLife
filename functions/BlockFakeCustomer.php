<?php

namespace WooEasyLife\Admin;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class BlockFakeCustomer extends WP_REST_Controller {

    public function __construct() {
        add_action('rest_api_init', [ $this, 'register_routes' ]);
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route('wooeasylife/v1', '/block-customer', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'block_customer' ],
            'permission_callback' => [ $this, 'permissions_check' ],
            'args'                => $this->get_endpoint_args(),
        ]);

        register_rest_route('wooeasylife/v1', '/blocked-customers', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_blocked_customers' ],
            'permission_callback' => [ $this, 'permissions_check' ],
        ]);

        register_rest_route('wooeasylife/v1', '/update-customer/(?P<id>\d+)', [
            'methods'             => 'PUT',
            'callback'            => [ $this, 'update_blocked_customer' ],
            'permission_callback' => [ $this, 'permissions_check' ],
            'args'                => $this->get_endpoint_args(),
        ]);
    }

    /**
     * Permissions callback
     */
    public function permissions_check(WP_REST_Request $request) {
        return current_user_can('manage_woocommerce'); // Only admins can use this API
    }

    /**
     * Arguments for the endpoint
     */
    public function get_endpoint_args() {
        return [
            'ip_address' => [
                'required'          => false,
                'type'              => 'string',
                'description'       => 'The IP address to block or update',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'phone_number' => [
                'required'          => false,
                'type'              => 'string',
                'description'       => 'The phone number to block or update',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'reason' => [
                'required'          => true,
                'type'              => 'string',
                'description'       => 'Reason for blocking or updating',
                'sanitize_callback' => 'sanitize_textarea_field',
            ],
        ];
    }

    /**
     * Block customer IP or phone number
     */
    public function block_customer(WP_REST_Request $request) {
        global $wpdb;
    
        $ip_address = $request->get_param('ip_address');
        $phone_number = $request->get_param('phone_number');
        $reason = $request->get_param('reason');
    
        if (empty($ip_address) && empty($phone_number)) {
            return new WP_Error('missing_data', 'Either IP address or phone number must be provided.', [ 'status' => 400 ]);
        }
    
        $table_name = $wpdb->prefix . 'blocked_customers';
    
        // Create table if it doesn't exist
        $this->create_blocked_customers_table();
    
        // Check for existing record
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE ip_address = %s OR phone_number = %s",
                $ip_address,
                $phone_number
            )
        );
    
        if ($existing) {
            return new WP_REST_Response([
                'message' => 'Customer is already blocked.',
                'data'    => $existing,
            ], 200);
        }
    
        // Prepare data for insertion
        $data = [
            'ip_address'   => $ip_address,
            'phone_number' => $phone_number,
            'reason'       => $reason,
            'blocked_at'   => current_time('mysql'),
        ];
    
        // Insert the data into the database
        $wpdb->insert($table_name, $data);
    
        return new WP_REST_Response([
            'message' => 'Customer blocked successfully',
            'data'    => $data,
        ], 200);
    }

    /**
     * Update blocked customer
     */
    public function update_blocked_customer(WP_REST_Request $request) {
        global $wpdb;

        $id = $request->get_param('id');
        $ip_address = $request->get_param('ip_address');
        $phone_number = $request->get_param('phone_number');
        $reason = $request->get_param('reason');

        if (!$id) {
            return new WP_Error('missing_id', 'Record ID is required.', [ 'status' => 400 ]);
        }

        $table_name = $wpdb->prefix . 'blocked_customers';

        // Ensure the table exists
        $this->create_blocked_customers_table();

        // Check if the record exists
        $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
        if (!$existing) {
            return new WP_Error('not_found', 'Blocked customer record not found.', [ 'status' => 404 ]);
        }

        $data = [];
        if (!empty($ip_address)) {
            $data['ip_address'] = $ip_address;
        }
        if (!empty($phone_number)) {
            $data['phone_number'] = $phone_number;
        }
        $data['reason'] = $reason;

        $updated = $wpdb->update($table_name, $data, [ 'id' => $id ]);

        if ($updated === false) {
            return new WP_Error('db_error', 'Failed to update the record.', [ 'status' => 500 ]);
        }

        return new WP_REST_Response([
            'message' => 'Blocked customer updated successfully',
            'data'    => $data,
        ], 200);
    }

    /**
     * Get blocked customers
     */
    public function get_blocked_customers(WP_REST_Request $request) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'blocked_customers';
        $this->create_blocked_customers_table(); // Ensure table exists

        $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

        return new WP_REST_Response([
            'blocked_customers' => $results,
        ], 200);
    }

    /**
     * Create the blocked customers table
     */
    private function create_blocked_customers_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'blocked_customers';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ip_address VARCHAR(45) DEFAULT NULL,
            phone_number VARCHAR(20) DEFAULT NULL,
            reason TEXT NOT NULL,
            blocked_at DATETIME NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}