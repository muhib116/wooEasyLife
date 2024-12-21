<?php

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
    $url = "http://bulksmsbd.net/api/smsapi";
    $api_key = "GuN1Tp8ueoRJACAl072B";
    $senderid = "8809617619992";
    $number = $phone_number;
    $message = $message;

    $data = [
        "api_key" => $api_key,
        "senderid" => $senderid,
        "number" => $number,
        "message" => $message
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function getCustomerFraudData($phone_number) {
    if (empty($phone_number) || !is_string($phone_number)) {
        return new WP_Error('missing_data', 'Phone data is required in the correct format.', ['status' => 400]);
    }

    // External API URL and headers
    $api_url = 'https://api.wpsalehub.com/api/fraud-check';
    $api_key = 'your-api-key'; // Replace with your actual API key

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