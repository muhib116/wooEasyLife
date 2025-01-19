<?php
namespace WooEasyLife\Frontend;

class TrackAbandonCart {
    public function __construct()
    {
        // work when change checkout page data
        add_action('woocommerce_cart_updated', [$this, 'store_abandoned_cart_data']);
        
        //fire when reload the checkout page
        add_action('woocommerce_checkout_update_order_review', [$this, 'store_abandoned_cart_data']);

        add_action('woocommerce_thankyou', [$this, 'mark_abandoned_cart_as_recovered'], 10, 1);
    }

    public function store_abandoned_cart_data() {
        global $wpdb;
    
        // Define the table name
        $table_name = $wpdb->prefix . __PREFIX .'abandon_cart';
    
        // Get WooCommerce session data
        $session = WC()->session;
        $cart = WC()->cart->get_cart();
    
        if (empty($cart)) {
            return; // Exit if the cart is empty
        }

    
        // Get customer details
        $customer_email = WC()->customer->get_email();
        $customer_name = WC()->customer->get_billing_first_name() . ' ' . WC()->customer->get_billing_last_name();
        $customer_phone = normalize_phone_number(WC()->customer->get_billing_phone());
        $billing_address = WC()->customer->get_billing_address_1() . ', ' . WC()->customer->get_billing_city() . ', ' . WC()->customer->get_billing_state() . ', ' . WC()->customer->get_billing_postcode();
        $shipping_address = WC()->customer->get_shipping_address_1() . ', ' . WC()->customer->get_shipping_city() . ', ' . WC()->customer->get_shipping_state() . ', ' . WC()->customer->get_shipping_postcode();
     
        // Determine if the customer is a repeat customer (check WooCommerce orders)
        $is_repeat_customer = $this->is_repeat_customer_by_billing_phone($customer_phone);
    
        // Serialize cart contents to store in the database
        $cart_contents = [];
        $total_value = 0;
    
        foreach ($cart as $cart_item) {
            $cart_contents[] = [
                'name'     => $cart_item['data']->get_name(),
                'quantity' => $cart_item['quantity'],
                'price'    => $cart_item['line_total'],
            ];
            $total_value += $cart_item['line_total'];
        }
    
        $serialized_cart_contents = maybe_serialize($cart_contents);
    
        // Check if the cart is already stored
        $session_id = $session->get_customer_id();
        $existing_cart = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM $table_name 
                WHERE session_id = %s 
                AND (status = 'active' 
                OR (recovered_at IS NULL OR recovered_at < DATE_SUB(NOW(), INTERVAL 2 MINUTE)))",
                $session_id
            )
        );
    

        if ($existing_cart) {
            // Update the existing abandoned cart record
            $wpdb->update(
                $table_name,
                [
                    'customer_email'         => $customer_email,
                    'customer_name'          => $customer_name,
                    'customer_phone'         => $customer_phone,
                    'cart_contents'          => $serialized_cart_contents,
                    'total_value'            => $total_value,
                    'billing_address'        => $billing_address,
                    'shipping_address'       => $shipping_address,
                    'is_repeat_customer'     => $is_repeat_customer,
                    'updated_at'             => current_time('mysql'),
                ],
                ['id' => $existing_cart],
                ['%s', '%s', '%s', '%s', '%f', '%s', '%s', '%d', '%d', '%s'],
                ['%d']
            );
        } else {
            // Insert a new abandoned cart record
            $wpdb->insert(
                $table_name,
                [
                    'session_id'             => $session_id,
                    'customer_email'         => $customer_email,
                    'customer_name'          => $customer_name,
                    'customer_phone'         => $customer_phone,
                    'cart_contents'          => $serialized_cart_contents,
                    'total_value'            => $total_value,
                    'billing_address'        => $billing_address,
                    'shipping_address'       => $shipping_address,
                    'is_repeat_customer'     => $is_repeat_customer,
                    'abandoned_at'           => current_time('mysql'),
                    'status'                 => 'active', // Mark cart as active initially
                    'created_at'             => current_time('mysql'),
                    'updated_at'             => current_time('mysql'),
                ],
                ['%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%d', '%d', '%s', '%s', '%s']
            );
        }
    }

    private function is_repeat_customer_by_billing_phone($billing_phone) {
        if (empty($billing_phone)) {
            return false; // No billing phone provided, cannot determine repeat status
        }

        // Query WooCommerce for all completed orders with the same billing phone
        $args = [
            'billing_phone' => $billing_phone,
            'status'        => 'wc-completed',
            'type'          => 'shop_order',
            'limit'         => -1,
            'return'        => 'ids', // Only retrieve order IDs
        ];

        $completed_orders = wc_get_orders($args);

        // If there are any remaining completed orders, the customer is a repeat customer
        return count($completed_orders) > 0;
    }


    public function mark_abandoned_cart_as_recovered($order_id) {
        global $wpdb;

        // Get the WooCommerce order object
        $order = wc_get_order($order_id);
        if (!$order) {
            return; // Exit if the order object is not found
        }

        // Define the abandoned cart table name
        $table_name = $wpdb->prefix . __PREFIX . 'abandon_cart';

        // Retrieve customer data from the order
        $customer_email = $order->get_billing_email();
        $customer_phone = $order->get_billing_phone();
        $session_id     = WC()->session->get_customer_id();

        // Check if an abandoned cart exists for this customer
        $abandoned_cart_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM $table_name WHERE (session_id = %s OR customer_email = %s OR customer_phone = %s) AND status = 'abandoned'",
                $session_id,
                $customer_email,
                $customer_phone
            )
        );

        if ($abandoned_cart_id) {
            // Update the abandoned cart record to mark it as recovered
            $wpdb->update(
                $table_name,
                [
                    'status'       => 'recovered',
                    'recovered_at' => current_time('mysql'),
                    'updated_at'   => current_time('mysql'),
                ],
                ['id' => $abandoned_cart_id],
                ['%s', '%s', '%s'],
                ['%d']
            );
        }
    }


    public function mark_abandoned_carts() {
        global $wpdb;
    
        $cutoff_time = strtotime('-15 minutes'); // Example cut-off time: 15 minutes ago
        $cutoff_date = date('Y-m-d H:i:s', $cutoff_time);
    
        $table_name = $wpdb->prefix . __PREFIX .'abandon_cart';
    
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE $table_name 
                SET status = 'abandoned' 
                WHERE status = 'active' AND updated_at < %s",
                $cutoff_date
            )
        );
    }
    
}
