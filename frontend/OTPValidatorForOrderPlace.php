<?php
namespace WooEasyLife\Frontend;

class OTPValidatorForOrderPlace {
    function __construct()
    {
        // add_action('wp_footer', [$this, 'wooeasylife_check_if_checkout_page']);
        add_filter('woocommerce_order_button_html', [$this, 'customize_place_order_button'], 30);
        // add_filter('proceedToCheckoutButtonLabel', [$this, 'customize_place_order_button']);
    }

    function customize_place_order_button($button_html) {
        // Customize the button text or add extra attributes
        $custom_button = '<button type="submit" class="button alt custom-class" name="woocommerce_checkout_place_order" id="place_order" value="Place Order Now">';
        $custom_button .= 'Confirm & Pay'; // Custom text for the button
        $custom_button .= '</button>';
    
        return $custom_button;
    }
    
}