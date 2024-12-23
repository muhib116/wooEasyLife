<?php
namespace WooEasyLife\Frontend;

class Phone_number_block {
    public function __construct()
    {
        add_action('woocommerce_checkout_order_processed', [$this, 'phone_number_block']);
    }

    public function phone_number_block() {
        global $config_data;
        if($config_data["phone_number_block"]){
            $billing_phone = isset($_POST['billing_phone']) ? sanitize_text_field($_POST['billing_phone']) : '';
    
            $phone_block_listed = get_block_data_by_type($billing_phone, 'phone_number');
    
            if($phone_block_listed){
                // Add an error notice for WooCommerce
                // wc_add_notice(__('This phone number is restricted and cannot be used to place an order. Please contact our support team for assistance.', 'your-text-domain'), 'error');
                
                // Stop the order processing
                throw new \Exception(__('This phone number is restricted and cannot be used to place an order. Please contact our support team for assistance.', 'your-text-domain'));
            }
        }
    }
}