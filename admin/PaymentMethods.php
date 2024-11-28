<?php

namespace WooEasyLife\Admin;

class PaymentMethods {

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route('wooeasylife/v1', '/payment-methods', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_payment_methods'],
            'permission_callback' => '__return_true', // Publicly accessible
        ]);
    }

    /**
     * Get available payment methods
     */
    public function get_payment_methods() {
        $payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
        $methods = [];

        foreach ($payment_gateways as $gateway) {
            $methods[] = [
                'id'          => $gateway->id,
                'title'       => $gateway->get_title(),
                'description' => $gateway->get_description(),
            ];
        }

        return rest_ensure_response($methods);
    }
}