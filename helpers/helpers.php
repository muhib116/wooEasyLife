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
        'Authorization' => $license_key,
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

function getCustomerFraudData($phone_number) {
    if (empty($phone_number) || !is_string($phone_number)) {
        return new WP_Error('missing_data', 'Phone data is required in the correct format.', ['status' => 400]);
    }
    // External API URL and headers
    $api_url = 'https://api.wpsalehub.com/api/fraud-check';
    $api_key = __wpsalehub_api_key__; // Replace with your actual API key

    $payload = [
        'phone' => [
            [
                'id'    => 1,
                'phone' => $phone_number,
            ],
        ],
    ];

    $args = [
        'body'    => json_encode($payload),
        'headers' => [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
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

    return json_decode($response_body, true);
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
    $table_name = $wpdb->prefix . __PREFIX . 'block_list';

    // Validate the type to prevent SQL injection
    $allowed_types = ['phone_number', 'ip'];
    if (!in_array($type, $allowed_types, true)) {
        return false; // Invalid type
    }

    $query = $wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE type = %s AND REPLACE(REPLACE(REPLACE(ip_or_phone, '+880', '0'), '-', ''), ' ', '') = %s",
        $type,
        $value
    );

    $result = $wpdb->get_row($query, ARRAY_A);

    return $result ? true : false;
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
        'billing_phone' => $billing_phone
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

    // Handle fraud data
    $fraud_data = getCustomerFraudData($billing_phone);

    try {
        _storeFraudData([
            "customer_id" => $billing_phone,
            "report" => $fraud_data
        ]);
        // $customer_success_rate = $fraud_data[0]['report']['success_rate'] ?? 'n/a';
    } catch (\Exception $e) {
        error_log('Error in FraudCustomerTable::create: ' . $e->getMessage());
        return; // Bail if fraud data storage fails
    }
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