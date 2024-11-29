<?php

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

add_action('woocommerce_admin_order_data_after_billing_address', 'add_custom_heading_after_order_details');
function add_custom_heading_after_order_details($order) {
    $order_id = $order->get_id();
    $billing_phone = $order->get_billing_phone();

    // Output the custom heading
    $fraud_data = getCustomerFraudData($billing_phone);

    if (is_wp_error($fraud_data)) {
        echo '<p>Error: ' . esc_html($fraud_data->get_error_message()) . '</p>';
    } else {

    ?>
        <style>
            .fraud-history-container {
                width: 490px;
                margin: 20px auto;
                text-align: center;
                background-color: #ffffff;
                border-radius: 4px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                padding: 6px;
                overflow: hidden;
                font-family: Arial, sans-serif;
                border: 1px solid #4442;
                font-size: 16px;
            }
            .fraud-history-container .fraud-history-table .text-center{
                text-align: center;
            }
            .fraud-history-container .fraud-history-title {
                font-size: 16px;
                color: #333333;
                margin-bottom: 15px;
            }

            .fraud-history-container .fraud-history-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
            }

            .fraud-history-container .fraud-history-table thead th {
                background-color: #e5e7eb;
                color: #333333;
                font-weight: 400;
                text-align: left;
                padding: 12px;
                border-bottom: 2px solid #ddd;
            }

            .fraud-history-container .fraud-history-table tbody td {
                text-align: left;
                padding: 10px 12px;
                border-bottom: 1px solid #eee;
            }

            .fraud-history-container .fraud-history-table tbody tr:last-child td {
                border-bottom: none;
            }

            .fraud-history-container .fraud-history-table tbody .confirm {
                background-color: #d4edda;
                color: #155724;
                font-weight: bold;
                text-align: center;
            }

            .fraud-history-container .fraud-history-table tbody .cancel {
                background-color: #f8d7da;
                color: #721c24;
                font-weight: bold;
                text-align: center;
            }

            .fraud-history-container .fraud-history-table tbody .success-rate {
                background-color: #cce5ff;
                color: #004085;
                font-weight: bold;
                text-align: center;
            }

            /* Updated Footer Row (Total) */
            .fraud-history-container .fraud-history-table .total-row td {
                font-weight: bold;
                background-color: #e5e7eb; /* Match footer with a neutral gray */
                color: #333333;
                text-align: center;
                border-top: 1px solid #ddd;
            }
            .fraud-history-container .fraud-history-table .header-row .confirm {
                background-color: #dcfce7;
            }
            .fraud-history-container .fraud-history-table .header-row .cancel {
                background-color: #fee2e1;
            }
            .fraud-history-container .fraud-history-table .header-row .success-rate {
                background-color: #e0f2fe;
            }
            .fraud-history-container .fraud-history-table .total-row .confirm {
                background-color: #17a34a;
                color: white;
            }
            .fraud-history-container .fraud-history-table .total-row .cancel {
                background-color: #dc2625;
                color: white;
            }
            .fraud-history-container .fraud-history-table .total-row .success-rate {
                background-color: #0084c7;
                color: white;
            }
        </style>

        <div class="fraud-history-container">
            <h2 class="fraud-history-title">
                <?php
                    if($fraud_data[0]['report']['success_rate'] == '100%'){
                        echo '🎉 The number has no fraud history! ✅';
                    }
                ?>
            </h2>

            <?php if($fraud_data && $fraud_data[0]['report']['total_order'] > 0) { ?>
                <table class="fraud-history-table">
                    <thead>
                        <tr class="header-row">
                            <th>Courier Name</th>
                            <th class="confirm">Confirm</th>
                            <th class="cancel">Cancel</th>
                            <th class="success-rate">Success Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fraud_data[0]['report']['courier'] as $item) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-center bg-green-500 text-white"><?php echo htmlspecialchars($item['report']['confirmed'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-center bg-red-500 text-white"><?php echo htmlspecialchars($item['report']['cancel'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-center bg-sky-500 text-white"><?php echo htmlspecialchars($item['report']['success_rate'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php } ?>


                        <tr class="total-row table_footer">
                            <td>Total</td>
                            <td class="confirm">
                                <?php echo isset($fraud_data[0]['report']['confirmed']) ? htmlspecialchars($fraud_data[0]['report']['confirmed'], ENT_QUOTES, 'UTF-8') : 0; ?>
                            </td>
                            <td class="cancel">
                                <?php echo $fraud_data[0]['report']['cancel']; ?>
                            </td>
                            <td class="success-rate">
                                <?php echo isset($fraud_data[0]['report']['success_rate']) ? htmlspecialchars($fraud_data[0]['report']['success_rate'], ENT_QUOTES, 'UTF-8') : '0%'; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php } ?>

            <?php if($fraud_data && $fraud_data[0]['report']['total_order'] == 0) { ?>
                <div>
                    <h3 style="font-weight:bold; font-size: 20px; margin-bottom: 16px; text-align: center;">
                        🎉 The number has no data! ✅
                    </h3>
                </div>
            <?php } ?>
        </div>

    <?php
    }
}
