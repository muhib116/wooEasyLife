<?php
namespace WooEasyLife\Frontend;

class CheckoutFormValidation {
    public function __construct()
    {
        add_action( 'woocommerce_after_checkout_validation', [$this, 'form_validation'] );
        add_action('woocommerce_checkout_create_order', [$this, 'modify_order_phone'], 10, 2);
    }

    public function form_validation() {
        // Retrieve data sent via AJAX
        $billing_phone = isset( $_POST['billing_phone'] ) ? normalize_phone_number(sanitize_text_field( $_POST['billing_phone'] )) : '';
        
        if(!validate_BD_phoneNumber($billing_phone)){
            throw new \Exception(
                sprintf(
                    __('Your phone number <strong style=\"font-weight:bold;color: #508ef5;\">%s</strong> is not valid. Please enter a valid BD phone number.', 'your-text-domain'),
                    esc_html($billing_phone)
                )
            );
            return;
        }
    }

    public function modify_order_phone($order, $data) {
        // Normalize the phone number again to ensure consistency
        if (isset($data['billing_phone'])) {
            $normalized_phone = normalize_phone_number($data['billing_phone']);
            $order->set_billing_phone($normalized_phone);
        }
    }
}