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
        // add_action('woocommerce_checkout_process', [$this, 'smsHandleForCustomerAndAdminWhenPlaceOrder']);
        add_action('woocommerce_checkout_order_processed', [$this, 'smsHandleForCustomerAndAdminWhenPlaceOrder']);
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

    public function smsHandleForCustomerAndAdminWhenPlaceOrder($order_id)
    {
        $order = wc_get_order($order_id);
        if (!$order) {
            error_log("Order not found for ID: $order_id");
            return; // Bail if the order doesn't exist
        }

        // Extract order details
        $billing_phone = $order->get_billing_phone();
        $customer_name = $order->get_billing_first_name();
        $customer_id = $order->get_customer_id();
        $order_total = $order->get_total();
        $product_names = [];

        foreach ($order->get_items() as $item) {
            $product_names[] = $item->get_name();
        }

        $product_list = implode(', ', $product_names); // Convert product names to a readable string
        $site_title = get_bloginfo('name');           // Site title
        $admin_phone = '01770989591';                 // Admin phone number
        $customer_success_rate = 'n/a';               // Default success rate

        // Handle fraud data
        $fraud_data = $this->getFraudData($billing_phone);
        if (is_wp_error($fraud_data)) {
            wc_add_notice(__('We encountered an issue while processing your order. Please try again.', 'your-textdomain'), 'error');
            return;
        }

        try {
            $this->storeFraudData([
                "customer_id" => $customer_id,
                "report" => $fraud_data
            ]);
            $customer_success_rate = $fraud_data[0]['report']['success_rate'] ?? 'n/a';
        } catch (\Exception $e) {
            error_log('Error in FraudCustomerTable::create: ' . $e->getMessage());
            return; // Bail if fraud data storage fails
        }

        // Send SMS to customer and admin
        global $config_data;
        // Separate the 'place_order_otp_verification' data
        $place_order_sms_for_customer = $config_data['place_order_sms_for_customer'] ?? null;
        $place_order_sms_for_admin = $config_data['place_order_sms_for_admin'] ?? null;

        if($place_order_sms_for_customer){
            $this->customer_sms($customer_name, $product_list, $site_title, $order_total, $billing_phone, $admin_phone);
        }
        if($place_order_sms_for_admin){
            $this->admin_sms($customer_name, $product_list, $site_title, $order_total, $billing_phone, $admin_phone, $customer_success_rate);
        }
    }

    private function admin_sms($customer_name, $product_list, $site_title, $order_total, $billing_phone, $admin_phone, $customer_success_rate)
    {
        // Prepare the SMS message with emojis
        $sms_message = sprintf(
            "New order by %s (%s), for \"%s\" at %s.\n\nSuccess rate: %s\nTotal bill: $%s.",
            $customer_name,
            $billing_phone,
            $product_list,
            $site_title,
            $customer_success_rate,
            $order_total
        );

        // Send the SMS
        $this->send_sms($admin_phone, $sms_message);
    }

    private function customer_sms($customer_name, $product_list, $site_title, $order_total, $billing_phone, $admin_phone)
    {
        // Prepare the SMS message with emojis
        $sms_message = sprintf(
            "Hi %s, your order for \"%s\" has been placed at %s.\n\nTotal bill: $%s.\n\nFor any assistance: %s.\nThank you!",
            $customer_name,
            $product_list,
            $site_title,
            $order_total,
            $admin_phone
        );

        // Send the SMS
        $this->send_sms($billing_phone, $sms_message);
    }

    private function send_sms($phone_number, $message)
    {
        $response = send_sms($phone_number, $message);

        // Log the response for debugging
        if (is_wp_error($response)) {
            error_log("SMS sending failed for $phone_number: " . $response->get_error_message());
        } else {
            error_log("SMS sent successfully to $phone_number: " . print_r($response, true));
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
