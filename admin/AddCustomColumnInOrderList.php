<?php
namespace WooEasyLife\Admin;

class AddCustomColumnInOrderList {
    public function __construct()
    {
        // Add custom column order list table. WooCommerce - 7.0.0 version
        add_filter('manage_edit-shop_order_columns', [$this, 'wooeasylife_add_custom_order_column']);
        add_action('manage_shop_order_posts_custom_column', [$this, 'wooeasylife_populate_custom_order_column']);
        
        // Add custom column content order list table. WooCommerce- Latest version
        add_filter('woocommerce_shop_order_list_table_columns', [$this, 'wooeasylife_add_custom_order_column'] );
        add_action( 'woocommerce_shop_order_list_table_custom_column', [$this, 'wooeasylife_populate_custom_order_column'], 10, 2 );
    }

    /**
     * Add a custom column to the WooCommerce Orders table.
     */
    public function wooeasylife_add_custom_order_column($columns) {
        // Insert a new column after the Order Status column
        $new_columns = [];
        foreach ($columns as $key => $column) {
            $new_columns[$key] = $column;
            if ('order_status' === $key) {
                $new_columns['fraud-data'] = __('Fraud Data', 'wooeasylife');
            }
        }
        return $new_columns;
    }

    /**
     * Populate the custom column with data.
     */
    public function wooeasylife_populate_custom_order_column($column, $post_id) {
        if ('fraud-data' === $column) {
            $order = wc_get_order($post_id); // Get the order object

            // Example: Retrieve and display custom meta data
            $custom_data = $order->get_meta('_custom_meta_key', true); // Replace with your custom meta key
            echo $custom_data ? esc_html($custom_data) : __('N/A', 'wooeasylife');
        }
    }

    /**
    * Make the custom column sortable (optional).
    */
    public function wooeasylife_make_custom_column_sortable($columns) {
        $columns['custom_column'] = 'custom_column';
        return $columns;
    }

    /**
     * Adjust the query for sorting the custom column.
     */
    function wooeasylife_sort_custom_order_column($query) {
        if (!is_admin() || 'shop_order' !== $query->get('post_type')) {
            return;
        }

        $orderby = $query->get('orderby');
        if ('custom_column' === $orderby) {
            $query->set('meta_key', '_custom_meta_key'); // Replace with your custom meta key
            $query->set('orderby', 'meta_value');
        }
    }
}

