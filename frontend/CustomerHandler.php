<?php
namespace WooEasyLife\Frontend;

class CustomerHandler {
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'woo_easy_life_customer_data';

        // Hook into WooCommerce order creation
        add_action('woocommerce_checkout_create_order', [$this, 'handle_customer_data'], 10, 2);
    }

    /**
     * Handle customer insertion or update when a new order is placed
     */
    public function handle_customer_data($order, $data) {
        global $wpdb;

        // Normalize phone number
        $phone = isset($data['billing_phone']) ? normalize_phone_number($data['billing_phone']) : '';
        $email = isset($data['billing_email']) ? sanitize_email($data['billing_email']) : '';

        // If no phone and email exist, stop execution
        if (empty($phone) && empty($email)) {
            return;
        }

        // Define the primary identifier (Phone > Email)
        $primary_identifier = !empty($phone) ? $phone : $email;

        // Search for an existing customer by phone or email
        $existing_customer = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE phone = %s OR email = %s LIMIT 1",
                $phone,
                $email
            ),
            ARRAY_A
        );

        // Gather data for insertion or update
        $customer_data = [
            'customer_id'    => $order->get_customer_id(),
            'order_id'       => $order->get_id(),
            'phone'          => $phone,
            'email'          => $email,
            'first_name'     => sanitize_text_field($data['billing_first_name']),
            'last_name'      => sanitize_text_field($data['billing_last_name']),
            'address'        => sanitize_text_field($data['billing_address_1'] . ' ' . $data['billing_address_2']),
            'city'           => sanitize_text_field($data['billing_city']),
            'state'          => sanitize_text_field($data['billing_state']),
            'postcode'       => sanitize_text_field($data['billing_postcode']),
            'country'        => sanitize_text_field($data['billing_country']),
            'last_order_date'=> current_time('mysql'),
            'updated_at'     => current_time('mysql'),
        ];

        if ($existing_customer) {
            // Update existing customer record
            $wpdb->update(
                $this->table_name,
                $customer_data,
                ['id' => $existing_customer['id']]
            );
        } else {
            // Insert new customer record
            $customer_data['first_order_date'] = current_time('mysql');
            $customer_data['created_at'] = current_time('mysql');

            $wpdb->insert($this->table_name, $customer_data);
        }
    }
}