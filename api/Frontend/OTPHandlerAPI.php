<?php

namespace WooEasyLife\API\Frontend;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class OTPHandlerAPI extends WP_REST_Controller
{
    private $otp_length = 4;
    private $otp_expiry = 10 * MINUTE_IN_SECONDS; // 10 minutes expiry
    private $resend_cooldown = 2 * MINUTE_IN_SECONDS; // 2 minutes cooldown

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register REST API routes for OTP handling.
     */
    public function register_routes()
    {
        register_rest_route('wooeasylife/v1', '/otp/send', [
            'methods'             => 'POST',
            'callback'            => [$this, 'send_otp'],
            'permission_callback' => [$this, 'permissions_check'],
        ]);

        register_rest_route('wooeasylife/v1', '/otp/resend', [
            'methods'             => 'POST',
            'callback'            => [$this, 'resend_otp'],
            'permission_callback' => [$this, 'permissions_check'],
        ]);

        register_rest_route('wooeasylife/v1', '/otp/validate', [
            'methods'             => 'POST',
            'callback'            => [$this, 'validate_otp'],
            'permission_callback' => [$this, 'permissions_check'],
        ]);
    }

    /**
     * Permissions callback for API routes.
     */
    public function permissions_check()
    {
        return true; // Allow public access for now
    }

    /**
     * Send OTP to a phone number.
     */
    public function send_otp(WP_REST_Request $request)
    {
        $phone_number = sanitize_text_field($request->get_param('phone_number'));

        if (empty($phone_number)) {
            return new WP_Error('missing_phone', 'Phone number is required.', ['status' => 400]);
        }

        // Generate OTP
        $otp = $this->generate_otp($phone_number);

        // TODO: Integrate with your SMS API
        $this->send_otp_sms($phone_number, $otp);

        return new WP_REST_Response([
            'status'  => 'success',
            'message' => 'OTP sent successfully.',
            'expiry'  => $this->otp_expiry / 60 . ' minutes',
        ], 201);
    }

    /**
     * Resend OTP to a phone number.
     */
    public function resend_otp(WP_REST_Request $request)
    {
        $phone_number = sanitize_text_field($request->get_param('phone_number'));

        if (empty($phone_number)) {
            return new WP_Error('missing_phone', 'Phone number is required.', ['status' => 400]);
        }

        // Check resend cooldown
        $last_resend_time = get_transient('otp_resend_' . $phone_number);
        if ($last_resend_time && (time() - $last_resend_time < $this->resend_cooldown)) {
            $remaining_time = $this->resend_cooldown - (time() - $last_resend_time);
            return new WP_Error('cooldown_active', "Please wait $remaining_time seconds before requesting a new OTP.", ['status' => 429]);
        }

        // Generate OTP
        $otp = $this->generate_otp($phone_number);

        // Store resend cooldown
        set_transient('otp_resend_' . $phone_number, time(), $this->resend_cooldown);

        // TODO: Integrate with your SMS API
        $this->send_otp_sms($phone_number, $otp);

        return new WP_REST_Response([
            'status'  => 'success',
            'message' => 'OTP resent successfully.',
            'expiry'  => $this->otp_expiry / 60 . ' minutes',
            'cooldown' => $this->resend_cooldown / 60 . ' minutes',
        ], 200);
    }

    /**
     * Validate OTP for a phone number.
     */
    public function validate_otp(WP_REST_Request $request)
    {
        $phone_number = sanitize_text_field($request->get_param('phone_number'));
        $otp = sanitize_text_field($request->get_param('otp'));

        if (empty($phone_number) || empty($otp)) {
            return new WP_Error('missing_parameters', 'Both phone number and OTP are required.', ['status' => 400]);
        }

        // Retrieve the stored OTP
        $stored_otp = get_transient('otp_' . $phone_number);

        if (!$stored_otp) {
            return new WP_Error('otp_expired', 'OTP has expired or does not exist.', ['status' => 400]);
        }

        if ($stored_otp != $otp) {
            return new WP_Error('invalid_otp', 'Invalid OTP. Please try again.', ['status' => 400]);
        }

        // OTP is valid, clear transient
        delete_transient('otp_' . $phone_number);

        return new WP_REST_Response([
            'status'  => 'success',
            'message' => 'OTP validated successfully.',
        ], 200);
    }

    /**
     * Generate and store OTP for a phone number.
     */
    private function generate_otp($phone_number)
    {
        $otp = rand(pow(10, $this->otp_length - 1), pow(10, $this->otp_length) - 1);
        set_transient('otp_' . $phone_number, $otp, $this->otp_expiry);
        return $otp;
    }

    /**
     * Send OTP via Bulk SMS API using wp_remote_post
     *
     * @param string $phone_number The recipient's phone number.
     * @param string $message The message to send.
     * @return array The API response or error.
     */
    private function send_otp_sms($phone_number, $message)
    {
        $api_url = 'http://bulksmsbd.net/api/smsapi';
        $api_key = 'GuN1Tp8ueoRJACAl072B';
        $sender_id = '8809617619992';

        // API request parameters
        $params = [
            'api_key'   => $api_key,
            'type'      => 'text',
            'number'    => $phone_number,
            'senderid'  => $sender_id,
            'message'   => $message,
        ];

        // HTTP request headers
        $headers = [
            'Authorization' => 'Bearer Kod30eDnI1EFG9vaf9gBPsSwaD3IkklCIATZoSYz9cf733bd',
        ];

        // Make the API request
        $response = wp_remote_post($api_url, [
            'headers' => $headers,
            'body'    => $params,
            'timeout' => 30,
        ]);

        // Handle errors in the response
        if (is_wp_error($response)) {
            return [
                'status'  => 'error',
                'message' => $response->get_error_message(),
            ];
        }

        // Decode and return the response body
        $response_body = wp_remote_retrieve_body($response);
        $response_data = json_decode($response_body, true);

        if (isset($response_data['error_message'])) {
            return [
                'status'  => 'error',
                'message' => $response_data['error_message'],
            ];
        }

        return [
            'status'  => 'success',
            'data'    => $response_data,
        ];
    }


    // private function send_otp_sms($phone_number, $otp)
    // {
    //     $sms_api_url = 'https://api.wpsalehub.com/api/sms/send';
    //     $response = wp_remote_post($sms_api_url, [
    //         'body' => [
    //             'phone'   => $phone_number,
    //             'content' => "Your OTP is: $otp",
    //         ],
    //     ]);

    //     if (is_wp_error($response)) {
    //         error_log('Failed to send OTP SMS: ' . $response->get_error_message());
    //         return false;
    //     }

    //     return true;
    // }
}