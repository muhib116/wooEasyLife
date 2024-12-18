<?php
namespace WooEasyLife\Admin;

class SMSForStatusChange {
    function __construct()
    {
        add_action('woocommerce_order_status_changed', [$this, 'handle_order_status_change'], 10, 3);
    }

    function handle_order_status_change($order_id, $old_status, $new_status) {
        // Get the order object
        $order = wc_get_order($order_id);

        if (!$order) {
            return; // Exit if the order is invalid
        }

        switch($new_status) {
            case 'confirmed ':
                function (){};
            break;
            case 'call-not-received':
                function (){};
            break;
            case 'courier-entry':
                function (){};
            break;
            case 'courier-hand-over':
                function (){};
            break;
            case 'out-for-delivery':
                function (){};
            break;
            case 'cancelled':
                function (){};
            break;
            case 'refunded':
                function (){};
            break;
        }
    }
}