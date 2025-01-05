<?php
namespace WooEasyLife\Frontend;

class OrderSource {
    public function __construct() {
        // Triggered when a new order is created, regardless of the source (frontend, API, or admin).
        add_action('woocommerce_new_order', [$this, 'auto_detect_order_source']);
    }

    public function auto_detect_order_source($order_id) {
        global $wpdb;

        // Determine the order source
        $order_source = '';
        if (defined('REST_REQUEST') && REST_REQUEST) {
            $order_source = 'API';
        } elseif (is_admin()) {
            $order_source = 'Admin';
        } else {
            $order_source = 'Website';
        }

        // Insert or update the order source in the wc_orders_meta table
        $table_name = $wpdb->prefix . 'wc_orders_meta';
        $existing_meta = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_id FROM $table_name WHERE order_id = %d AND meta_key = '_order_source'",
                $order_id
            )
        );

        if ($existing_meta) {
            $wpdb->update(
                $table_name,
                ['meta_value' => $order_source],
                ['meta_id' => $existing_meta],
                ['%s'],
                ['%d']
            );
        } else {
            $wpdb->insert(
                $table_name,
                [
                    'order_id'   => $order_id,
                    'meta_key'   => '_order_source',
                    'meta_value' => $order_source,
                ],
                ['%d', '%s', '%s']
            );
        }
    }
}