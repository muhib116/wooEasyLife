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
            global $wpdb;
    
            // Get the order object
            $order = wc_get_order($post_id);
            if (!$order) {
                echo __('N/A', 'wooeasylife');
                return;
            }
    
            // Retrieve the customer ID associated with the order
            $billing_phone = $order->get_billing_phone();
            if (!$billing_phone) {
                echo __('Guest Order', 'wooeasylife');
                return;
            }
    
            // Log the customer ID
            error_log('Customer ID: ' . $billing_phone);
    
            // Fetch fraud data from the custom table
            $table_name = $wpdb->prefix . 'woo_easy_life_fraud_customers';
            $fraud_data = $wpdb->get_row(
                $wpdb->prepare("SELECT report FROM $table_name WHERE customer_id = %d", $billing_phone),
                ARRAY_A
            );
    
            if ($fraud_data && isset($fraud_data['report'])) {
                // Decode the JSON report
                $report = json_decode($fraud_data['report'], true);
                $success_rate = $report[0]['report']['success_rate'];
                $progress_bar = '
                    <style>
                        .fraud-history-container .progress-bar{
                            background: red;
                            height: 3px;
                            margin: 25px 0 25px;
                            position: relative;
                            div{
                                height: 100%;
                                width: 10%;
                                background: #22c55d;
                                position: relative;
                            }
                            span{
                                position: absolute;
                                font-size: 8px;
                                background: #22c55d;
                                bottom: 100%;
                                margin-bottom: 2px;
                                left: calc(100% - 30px);
                                border: 1px solid #3334;
                                padding: 0px 2px;
                                border-radius: 2px;
                                color: white;
                                line-height: 14px;
                            }
                            span.cancel {
                                bottom: unset;
                                top: 100%;
                                margin-top: 8px;
                                left: unset;
                                right: 10px;
                                background: #ef4444;
                                z-index: 9999;
                            }
                        }
                    </style>

                    <div class="fraud-history-container"><div class="progress-bar">
                        <div style="width: '.$success_rate.'">
                            <span>
                                '.$success_rate.'
                            </span>
                        </div>';

                        if(100 - (int)$success_rate){
                            $progress_bar .= '<span class="cancel">' . 100 - (int)$success_rate.'%</span>';
                        }
                    $progress_bar .= '</div></div>';

                echo $progress_bar;
            } else {
                echo __('No fraud data found', 'wooeasylife');
            }
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

