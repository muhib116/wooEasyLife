<?php

namespace WooEasyLife\Frontend;


$order_button_text;
class OTPValidatorForOrderPlace
{
    public $option_data;

    function __construct()
    {
        $this->option_data = get_option(__PREFIX . 'config');
        add_filter('woocommerce_order_button_html', [$this, 'customize_place_order_button'], 30);
        add_filter('woocommerce_checkout_order_button_text', [$this, 'change_order_button_text'], 15);
        add_filter('woocommerce_order_button_text', [$this, 'change_order_button_text'], 15);
        add_action('woocommerce_after_checkout_form', [$this, 'pushPopupTemplateToAfterCheckoutForm'], 15);
        add_action('woocommerce_checkout_order_processed', [$this, 'storeFraudDataWhenPlaceOrder']);
    }

    public function change_order_button_text($text)
    {
        global $order_button_text;
        $order_button_text = $text;
        return $text;
    }

    public function customize_place_order_button($btn)
    {
        global $order_button_text;
        global $config_data;
        // Separate the 'place_order_otp_verification' data
        $place_order_otp_verification = $config_data['place_order_otp_verification'] ?? null;

        if ($place_order_otp_verification) {
            // Customize the button text or add extra attributes
            $custom_button = '<button 
                                type="button" 
                                class="button alt fc-place-order-button" 
                                name="woocommerce_checkout_place_order" 
                                id="wooEasyLifeOtpModalOpener"
                                value="Place order" 
                                data-value="Place order"
                            >';
            $custom_button .= $order_button_text; // Custom text for the button
            $custom_button .= '</button>';

            $custom_button .= "<div style='opacity: 0.4; pointer-events: auto;  '>$btn</div>";
            return $custom_button;
        }

        return $btn;
    }

    public function pushPopupTemplateToAfterCheckoutForm()
    {
        global $config_data;
        // Separate the 'place_order_otp_verification' data
        $place_order_otp_verification = $config_data['place_order_otp_verification'] ?? null;

        if ($place_order_otp_verification) {
            include_once plugin_dir_path(__DIR__) . 'includes/checkoutPage/CheckOutOtpPopup.php';
        }
    }

    public function storeFraudDataWhenPlaceOrder($order_id)
    {
        $order = wc_get_order($order_id);
        if (!$order) {
            error_log("Order not found for ID: $order_id");
            return; // Bail if the order doesn't exist
        }

        // Extract order details
        $billing_phone = $order->get_billing_phone();

        // Handle fraud data
        $fraud_data = $this->getFraudData($billing_phone);
        if (is_wp_error($fraud_data)) {
            wc_add_notice(__('We encountered an issue while processing your order. Please try again.', 'your-textdomain'), 'error');
            return;
        }

        try {
            $this->storeFraudData([
                "customer_id" => $billing_phone,
                "report" => $fraud_data
            ]);
            // $customer_success_rate = $fraud_data[0]['report']['success_rate'] ?? 'n/a';
        } catch (\Exception $e) {
            error_log('Error in FraudCustomerTable::create: ' . $e->getMessage());
            return; // Bail if fraud data storage fails
        }
    }

    private function getFraudData($billing_phone)
    {
        // Simulate fetching fraud data
        return getCustomerFraudData($billing_phone);
    }

    private function storeFraudData($fraud_data)
    {
        $instance = new \WooEasyLife\CRUD\FraudCustomerTable();
        $instance->create($fraud_data);
    }
}
