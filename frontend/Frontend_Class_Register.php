<?php
namespace WooEasyLife\Frontend;

class Frontend_Class_Register{
    public function __construct()
    {
        new \WooEasyLife\Frontend\OTPValidatorForOrderPlace();
        new \WooEasyLife\Frontend\IP_block();
        new \WooEasyLife\Frontend\OrderBlockForBlockedUser();
        new \WooEasyLife\Frontend\Order_limit();
        new \WooEasyLife\Frontend\TrackAbandonCart();
    }
}