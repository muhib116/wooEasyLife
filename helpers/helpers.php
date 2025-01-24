<?php
function api_permission_check () {
    // if (!is_user_logged_in() || !current_user_can('manage_options')) {
    //     wp_die('Access denied. Only admin can access.');
    //     return;
    // }
    return '__return_true';
}

function decode_json_if_string($data) {
    // Check if the data is a string and in JSON format
    if (is_string($data)) {
        $decoded = json_decode($data, true);
        // Verify if decoding was successful
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
    }

    // Return the original data if not a valid JSON string
    return $data;
}

function safe_json_encode($data) {
    // Check if the input is already a JSON string and valid
    if (is_string($data)) {
        $decoded = json_decode($data, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode($decoded);
        }
    }

    // Check if the input is an array or object for encoding
    if (is_array($data) || is_object($data)) {
        $encoded = json_encode($data);

        // Ensure the encoding was successful
        if (json_last_error() === JSON_ERROR_NONE) {
            return $encoded;
        }
    }

    // If the data cannot be JSON encoded, return null
    return $data;
}

function HPOSp() {
    if (class_exists('Automattic\WooCommerce\Utilities\OrderUtil')) {
        return Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
    }
    return false;
}

function get_contrast_color($hexColor) {
    // Remove the hash symbol if present
    $hexColor = str_replace('#', '', $hexColor);

    // If shorthand hex, convert to full form
    if (strlen($hexColor) === 3) {
        $hexColor = $hexColor[0] . $hexColor[0] . $hexColor[1] . $hexColor[1] . $hexColor[2] . $hexColor[2];
    }

    // Parse RGB values
    $r = hexdec(substr($hexColor, 0, 2));
    $g = hexdec(substr($hexColor, 2, 2));
    $b = hexdec(substr($hexColor, 4, 2));

    // Calculate relative luminance
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

    // Return white (#ffffff) for dark backgrounds, black (#000000) for light backgrounds
    return $luminance > 0.5 ? '#000000' : '#ffffff';
}


function send_sms($phone_number, $message)
{
    global $license_key;

    $url = "https://api.wpsalehub.com/api/sms/send";

    $data = [
        "phone" => $phone_number,
        "content" => $message,
    ];


    $headers = [
        'Authorization' => 'Bearer '.$license_key,
        'Content-Type'  => 'application/x-www-form-urlencoded', // Adjust this if the API requires a different content type
    ];

    // Use wp_remote_post for HTTP requests
    $response = wp_remote_post($url, [
        'method'      => 'POST',
        'body'        => $data,
        'headers'     => $headers,
        'timeout'     => 45,
        'sslverify'   => false,
    ]);

    // Check for errors in the response
    if (is_wp_error($response)) {
        return [
            'status'  => 'error',
            'message' => $response->get_error_message(),
        ];
    }

    // Decode and return the response
    $response_body = wp_remote_retrieve_body($response);
    return [
        'status'  => 'success',
        'message' => $response_body,
    ];
}


/**
 * payload formate
    [
        'phone' => ['phone_1', 'phone_2', ...],
    ]
    */
function getCustomerFraudData($payload) 
{
    global $license_key;
    if (empty($payload) || !is_array($payload)) {
        return new WP_Error('missing_data', 'Phone data is required in the correct format.', ['status' => 400]);
    }
    // External API URL and headers
    $api_url = 'https://api.wpsalehub.com/api/fraud-check';

    $args = [
        'body'    => json_encode($payload),
        'headers' => [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $license_key,
        ],
        'timeout' => 45,
    ];

    // Make the API request
    $response = wp_remote_post($api_url, $args);

    if (is_wp_error($response)) {
        return [
            'status'  => 500,
            'message' => 'Failed to connect to the external API.',
        ];
    }

    $response_body = wp_remote_retrieve_body($response);
    $response_code = wp_remote_retrieve_response_code($response);

    if ($response_code !== 200) {
        return [
            'status'  => $response_code,
            'message' => 'The API returned an error.',
            'details' => json_decode($response_body, true),
        ];
    }

    $fraud_data = json_decode($response_body, true);
    try {
        foreach ($fraud_data['data'] as $data) {
            _storeFraudData([
                "customer_id" => $data['phone'],
                "report" => $data
            ]);
        }
    } catch (\Exception $e) {
        error_log('Error in FraudCustomerTable::create: ' . $e->getMessage());
        return; // Bail if fraud data storage fails
    }

    return $fraud_data['data'];
}

function getCustomerSuccessRate($billing_phone) {
    global $wpdb;

    // Fetch fraud data from the custom table
    $table_name = $wpdb->prefix . __PREFIX.'fraud_customers';
    $fraud_data = $wpdb->get_row(
        $wpdb->prepare("SELECT report FROM $table_name WHERE customer_id = %d", $billing_phone),
        ARRAY_A
    );
    if ($fraud_data && isset($fraud_data['report'])) {
        // Decode the JSON report
        $report = json_decode($fraud_data['report'], true);
        $success_rate = $report[0]['report']['success_rate'];

        return $success_rate;
    }

    return 'No data found.';
}


function get_block_data_by_type($value, $type = 'phone_number') {
    global $wpdb;
    $table_name = $wpdb->prefix . __PREFIX . 'block_list'; // Adjusted to match standard table prefix

    // Validate the type to prevent SQL injection
    $allowed_types = ['phone_number', 'ip', 'email'];
    if (!in_array($type, $allowed_types, true)) {
        return false; // Invalid type
    }

    // Normalize the phone number if the type is 'phone_number'
    $value = $type === 'phone_number' ? normalize_phone_number(trim($value)) : trim($value);

    // Query to check if the value exists in the block list
    $query = $wpdb->prepare(
        "SELECT 1 FROM {$table_name} WHERE type = %s AND REPLACE(REPLACE(REPLACE(ip_or_phone, '+880', '0'), '-', ''), ' ', '') = %s",
        $type,
        $value
    );

    return (bool) $wpdb->get_var($query); // Return true if a match is found
}


function get_total_orders_by_billing_phone_and_status($order) {
        
    // Get billing phone and status from the current $order
    $billing_phone = $order->get_billing_phone();
    $order_status = $order->get_status();

    if (empty($billing_phone) || empty($order_status)) {
        return []; // Return empty array if inputs are invalid
    }

    $args = [
        'status'    => $order_status, // Specific order status
        'return'    => 'objects', // Fetch full order objects
        'type'     => 'shop_order',
        [
            'key'     => 'billing_phone',
            'value'   => $billing_phone, // Match any phone number containing the input
            'compare' => 'LIKE',
        ],
    ];

    $orders = wc_get_orders($args);

    return count($orders); // Total orders matching criteria
}

function storeFraudDataWhenPlaceOrder($order_id)
{
    $order = wc_get_order($order_id);
    if (!$order) {
        error_log("Order not found for ID: $order_id");
        return; // Bail if the order doesn't exist
    }

    // Extract order details
    $billing_phone = $order->get_billing_phone();

    $fraud_payload = [
        "data" => [
            [
                'id' => 1, // this id using for showing report data in order list, in this case the id is fake
                'phone' => $billing_phone,
            ]
        ]
    ];

    // Handle fraud data
    getCustomerFraudData($fraud_payload);
}

function _storeFraudData($fraud_data)
{
    $instance = new \WooEasyLife\CRUD\FraudCustomerTable();
    $instance->create_or_update($fraud_data);
}

function normalize_phone_number($phone) {
    // Remove all non-digit characters
    $normalized = preg_replace('/\D/', '', $phone);

    // Check if the number starts with the country code +880 and replace it with 0
    if (strpos($normalized, '880') === 0) {
        $normalized = '0' . substr($normalized, 3); // Remove '880' and prepend '0'
    }

    return $normalized;
}

/**
 * Get courier data for an order.
 *
 * @param WC_Order|int $order WooCommerce order object or order ID.
 * @return array|WP_Error Array of courier data or WP_Error if invalid order or no data found.
 */
function get_courier_data_from_order($order) {
    global $wpdb;

    // Validate the order object or retrieve order by ID
    if (!$order instanceof WC_Order) {
        $order = wc_get_order($order);
    }

    if (!$order) {
        return new WP_Error('invalid_order', 'Invalid order object or ID.');
    }

    $order_id = $order->get_id();

    // Retrieve courier data from wp_wc_orders_meta table
    $table_name = $wpdb->prefix . 'wc_orders_meta';
    $result = $wpdb->get_var($wpdb->prepare(
        "SELECT meta_value FROM {$table_name} WHERE order_id = %d AND meta_key = '_courier_data'",
        $order_id
    ));

    if (empty($result)) {
        return new stdClass();
    }

    // Unserialize the data to return it in array format
    $courier_data = maybe_unserialize($result);

    return $courier_data;
}

function update_courier_data_for_order($order_id, $courier_data) {
    global $wpdb;

    // Serialize the courier data if it is an array
    if (is_array($courier_data)) {
        $courier_data = maybe_serialize($courier_data);
    }
    
    // Update courier data in wp_wc_orders_meta table
    $table_name = $wpdb->prefix . 'wc_orders_meta';
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$table_name} WHERE order_id = %d AND meta_key = '_courier_data'",
        $order_id
    ));

    if ($exists) {
        $updated = $wpdb->update(
            $table_name,
            ['meta_value' => $courier_data],
            ['order_id' => $order_id, 'meta_key' => '_courier_data'],
            ['%s'],
            ['%d', '%s']
        );
    } else {
        $updated = $wpdb->insert(
            $table_name,
            ['order_id' => $order_id, 'meta_key' => '_courier_data', 'meta_value' => $courier_data],
            ['%d', '%s', '%s']
        );
    }

    if ($updated === false) {
        return new WP_Error('db_error', 'Failed to update courier data.');
    }

    return $courier_data;
}


function delete_wc_orders_meta_by_key($meta_key) {
    global $wpdb;

    // Table name
    $table_name = $wpdb->prefix . 'wc_orders_meta';

    // Execute the delete query
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$table_name} WHERE meta_key = %s",
            $meta_key
        )
    );
}