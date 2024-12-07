<?php

namespace WooEasyLife\Frontend;

use WooEasyLife\API\Admin\WPOptionAPI;

class OTPValidatorForOrderPlace
{
    public $option_data;

    function __construct()
    {
        $this->option_data = get_option(__PREFIX.'config');
        add_filter('woocommerce_order_button_html', [$this, 'customize_place_order_button'], 30);
        add_filter('woocommerce_checkout_order_button_text', [$this, 'change_order_button_text'], 15);
        add_action('woocommerce_after_checkout_form', [$this, 'pushPopupTemplateToAfterCheckoutForm'], 15);
    }

    public function customize_place_order_button($btn)
    {
        // echo '---'.$btn.'----';
        global $config_data;
        // Separate the 'place_order_otp_verification' data
        $place_order_otp_verification = $config_data['place_order_otp_verification'] ?? null;
        
        
        
        if($place_order_otp_verification){
            // Customize the button text or add extra attributes
            $custom_button = '<button type="button" id="wooEasyLifeOtpModalOpener">';
            $custom_button .= 'Confirm & Pay'; // Custom text for the button
            $custom_button .= '</button>';
            return $custom_button;
        }

        return $btn;
    }

    public function pushPopupTemplateToAfterCheckoutForm () {
        include_once plugin_dir_path(__DIR__) . 'includes/checkoutPage/CheckOutOtpPopup.php';
    }

    public function enqueue_checkout_footer_script() {
        if (is_checkout()) {
            wp_enqueue_script('woo-easy-life-custom-js-script', plugin_dir_url(__DIR__) . 'includes/checkoutPage/popup.js', [], null, true);
        }
    }

    private function getStatusForOTPVerification() {

    }
}
