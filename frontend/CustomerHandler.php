<?php
namespace WooEasyLife\Frontend;

class CustomerHandler {
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . __PREFIX . 'customer_data';

        // Hook into WooCommerce order creation
        add_action('woocommerce_checkout_order_created', [$this, 'handle_customer_data'], 10, 2);
    }

    /**
     * Handle customer insertion or update when a new order is placed
     */
    public function handle_customer_data($order) {
        global $wpdb;

    
        // Ensure $order is a valid WooCommerce order object
        if (!$order instanceof \WC_Order) {
            return;
        }
    
        // Extract billing details directly from $order
        $phone = normalize_phone_number($order->get_billing_phone());
        $email = sanitize_email($order->get_billing_email());
        $first_name = sanitize_text_field($order->get_billing_first_name());
        $last_name = sanitize_text_field($order->get_billing_last_name());
        $address = sanitize_text_field($order->get_billing_address_1() . ' ' . $order->get_billing_address_2());
        $city = sanitize_text_field($order->get_billing_city());
        $state = sanitize_text_field($order->get_billing_state());
        $postcode = sanitize_text_field($order->get_billing_postcode());
        $country = sanitize_text_field($order->get_billing_country());
    
        // If both phone and email are missing, stop processing
        if (empty($phone) && empty($email)) {
            return;
        }
    
        // Fetch order frequency & total orders for customer
        $order_frequency = $this->calculate_order_frequency($phone, $email);
        $total_orders = $this->get_total_orders($phone, $email);
        $referral_source = $this->get_referral_source($order);
        $fraud_score = $this->calculate_fraud_score($order);
        $total_spent = $this->get_total_spent($phone, $email);
    
        // Search for an existing customer using phone or email
        $existing_customer = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE phone = %s OR email = %s LIMIT 1",
                $phone, $email
            ),
            ARRAY_A
        );
    
        // Prepare customer data for insert/update
        $customer_data = [
            'customer_id'     => $order->get_customer_id(),
            'order_id'        => $order->get_id(),
            'phone'           => $phone,
            'email'           => $email,
            'first_name'      => $first_name,
            'last_name'       => $last_name,
            'address'         => $address,
            'city'            => $city,
            'state'           => $state,
            'postcode'        => $postcode,
            'country'         => $country,
            'order_frequency' => $order_frequency,
            'total_orders'    => $total_orders,
            'referral_source' => $referral_source,
            'fraud_score'     => $fraud_score,
            'total_spent'     => $total_spent,
            'last_order_date' => current_time('mysql'),
            'updated_at'      => current_time('mysql'),
        ];
    
        if ($existing_customer) {
            // Assign customer type/tags based on history
            $customer_data['customer_type'] = $this->assign_customer_tags($existing_customer);
    
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
    


    /**
     * Get total orders for a customer by phone or email
     */
    private function get_total_orders($billing_phone = null, $billing_email = null) {
        // Prioritize phone, then fallback to email
        $identifier = !empty($billing_phone) ? $billing_phone : $billing_email;
    
        if (empty($identifier)) {
            return 0; // No valid identifier, return 0
        }
    
        $args = [
            'status'      => ['wc-completed'],
            'limit'       => -1,
            'return'      => 'ids',
        ];
    
        if (!empty($billing_phone)) {
            $args['billing_phone'] = $billing_phone;
        } else if (!empty($billing_email)) {
            $args['billing_email'] = $billing_email;
        }
    
        // Fetch customer orders using WooCommerce function
        $customer_orders = wc_get_orders($args);
    
        return count($customer_orders)+1;
    }    
    
    /**
     * Calculate order frequency (Orders per day)
     */
    public function calculate_order_frequency($billing_phone=null, $billing_email=null) {
        // Prioritize phone, then fallback to email
        $identifier = !empty($billing_phone) ? $billing_phone : $billing_email;

        if (empty($identifier)) {
            return 0; // No valid identifier, return 0 frequency
        }

        $args = [
            'status'      => ['wc-completed', 'wc-processing'],
            'limit'       => -1,
            'return'      => 'ids'
        ];

        if($billing_phone){
            $args['billing_phone'] = $billing_phone;
        }else if($billing_email){
            $args['billing_email'] = $billing_email;
        }

        // Fetch all orders associated with this phone or email
        $customer_orders = wc_get_orders($args);

        $total_orders = count($customer_orders);

        if ($total_orders <= 1) {
            return $total_orders; // Single or no orders mean no frequency calculation needed
        }

        // Get first and last order
        $first_order = wc_get_order(min($customer_orders));
        $last_order = wc_get_order(max($customer_orders));

        if (!$first_order || !$last_order) {
            return 0; // Safety check
        }

        // Get timestamps for first and last orders
        $first_order_date = strtotime($first_order->get_date_created()->format('Y-m-d'));
        $last_order_date = strtotime($last_order->get_date_created()->format('Y-m-d'));

        // Calculate days between first and last order
        $days_between = ($last_order_date - $first_order_date) / (60 * 60 * 24);

        // Calculate order frequency (Orders per day)
        return $days_between > 0 ? round($total_orders / $days_between, 2) : $total_orders;
    }

    private function assign_customer_tags($existingCustomer) 
    {
        $total_orders = isset($existingCustomer['total_orders']) ? $existingCustomer['total_orders'] : 0;
        $order_frequency = isset($existingCustomer['order_frequency']) ? $existingCustomer['order_frequency'] : 0;

        return [
            $total_orders,
            $order_frequency
        ];

        $tags = 'fraud';
    
        // Assign "New" tag for first-time customers
        if ($total_orders == 1) {
            $tags = 'new';
        }
    
        // Assign "Regular" tag if the customer has placed multiple orders
        if ($total_orders > 1 && $order_frequency < 1) {
            $tags = 'returning';
        }
    
        // Assign "VIP" tag if the customer orders frequently
        if ($total_orders > 20 && $order_frequency < 1) {
            $tags = 'vip';
        }
    
        // Assign "Loyal" tag if the customer has placed a high number of orders
        if ($total_orders >= 10 && $order_frequency < 1) {
            $tags = 'loyal';
        }
    
        return $tags;
    }

    private function get_referral_source($order) {
        // Example: Get UTM source stored as meta
        return $order->get_meta('_wc_order_attribution_utm_source', true) ?: 'Direct';
    }

    private function get_total_spent($billing_phone = null, $billing_email = null) {
        // Validate input
        if (empty($billing_phone) && empty($billing_email)) {
            return 0; // No valid identifier, return 0
        }
    
        // Build WooCommerce query args
        $args = [
            'status'      => ['wc-completed'],
            'limit'       => -1, // Fetch all completed orders
        ];
    
        // Prioritize phone, then fallback to email
        if (!empty($billing_phone)) {
            $args['billing_phone'] = $billing_phone;
        } elseif (!empty($billing_email)) {
            $args['billing_email'] = $billing_email;
        }
    
        // Fetch all orders for this customer
        $customer_orders = wc_get_orders($args);
        
        // Calculate total spent amount
        $total_spent = 0;
        foreach ($customer_orders as $order) {
            $total_spent += (float) $order->get_total(); // Ensure value is numeric
        }
    
        return $total_spent;
    }
    

    private function calculate_fraud_score($order) {
        $score = 0;
    
        // Extract relevant order details
        $billing_phone = normalize_phone_number($order->get_billing_phone());
        $customer_email = $order->get_billing_email();
        $customer_ip = $order->get_customer_ip_address();
    
        // 1️⃣ **Calculate Courier Fraud Score (Using a Separate Function)**
        $score += $this->get_courier_fraud_score($order);
    
        // 2️⃣ **Mismatched Billing & Shipping Address → Potential fraud**
        if ($order->get_billing_address_1() !== $order->get_shipping_address_1()) {
            $score += 10;
        }
    
        // 3️⃣ **Email & Phone Number Usage → Multiple accounts using the same info**
        $duplicate_email = $this->check_duplicate_customer_data('email', $customer_email);
        $duplicate_phone = $this->check_duplicate_customer_data('phone', $billing_phone);
    
        if ($duplicate_email > 2) {
            $score += 15;
        } elseif ($duplicate_email > 1) {
            $score += 8;
        }
    
        if ($duplicate_phone > 2) {
            $score += 15;
        } elseif ($duplicate_phone > 1) {
            $score += 8;
        }
    
        // 4️⃣ **Blacklist Check → If customer email, phone, or IP is blacklisted**
        if ($this->is_blacklisted($billing_phone, $customer_email, $customer_ip)) {
            $score += 50; // High risk if blacklisted
        }
    
        // 5️⃣ **Multiple failed/canceled orders → Possible fraud**
        $failed_orders = $this->get_failed_orders_count($billing_phone, $customer_email);
        if ($failed_orders > 3) {
            $score += 20;
        } elseif ($failed_orders > 1) {
            $score += 10;
        }
    
        // Final fraud score (capped at 100)
        return min($score, 100);
    }

    private function get_courier_fraud_score($order) {
        $score = 0;
    
        // Fetch courier fraud data
        $fraud_data = 
        $fraud_data = customer_courier_fraud_data($order);
    
        if (empty($fraud_data) || !isset($fraud_data['report'])) {
            return $score; // No data available, return 0 fraud points
        }
    
        $total_orders = $fraud_data['report']['total_order'] ?? 0;
        $canceled_orders = $fraud_data['report']['cancel'] ?? 0;
        $success_rate = isset($fraud_data['report']['success_rate']) ? floatval(str_replace('%', '', $fraud_data['report']['success_rate'])) : 100;
    
        // 1️⃣ **High order frequency → Potential bulk fraudulent orders**
        if ($total_orders > 10 && ($success_rate < 50 || $canceled_orders > 3)) {
            $score += 20;
        } elseif ($total_orders > 5 && $success_rate < 70) {
            $score += 10;
        }
    
        // 2️⃣ **Multiple failed/canceled orders → Possible fraud**
        if ($canceled_orders >= 5) {
            $score += 30;
        } elseif ($canceled_orders >= 3) {
            $score += 15;
        }
    
        // 3️⃣ **New Customer + High Order Amount → Suspicious**
        if ($total_orders <= 1 && $order->get_total() > 5000) {
            $score += 25;
        } elseif ($total_orders > 1 && $order->get_total() > 10000) {
            $score += 15;
        }
    
        // 4️⃣ **Courier-Specific Fraud Checks**
        foreach ($fraud_data['report']['courier'] ?? [] as $courier) {
            $courier_success_rate = isset($courier['report']['success_rate']) ? floatval(str_replace('%', '', $courier['report']['success_rate'])) : 100;
            $courier_cancel_count = $courier['report']['cancel'] ?? 0;
    
            if ($courier_cancel_count > 2 && $courier_success_rate < 60) {
                $score += 10;
            }
    
            if ($courier_success_rate < 50) {
                $score += 15;
            }
        }
    
        return $score;
    }
    
    

    
    private function get_failed_orders_count($billing_phone, $billing_email) {
        $args = [
            'status'      => ['wc-failed', 'wc-cancelled'],
            'limit'       => -1,
            'return'      => 'ids'
        ];
    
        if ($billing_phone) {
            $args['billing_phone'] = $billing_phone;
        } elseif ($billing_email) {
            $args['billing_email'] = $billing_email;
        }
    
        return count(wc_get_orders($args));
    }

    private function check_duplicate_customer_data($type, $value) {
        global $wpdb;
        $column = $type === 'email' ? 'email' : 'phone';
    
        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name} WHERE {$column} = %s",
            $value
        ));
    }
    
    private function is_blacklisted($phone, $email, $ip) {
        global $wpdb;
        $table_name = $wpdb->prefix . __PREFIX .'block_list';
    
        $blacklist = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} WHERE ip_phone_or_email = %s OR ip_phone_or_email = %s OR ip_phone_or_email = %s",
            $phone, $email, $ip
        ));
    
        return (int) $blacklist > 0;
    }
    
}
