<?php
// add_action( 'woocommerce_checkout_create_order', 'store_customer_ip_address', 10, 1 );

// function store_customer_ip_address( $order ) {
//     $customer_ip = WC_Geolocation::get_ip_address();
//     $order->update_meta_data( '_customer_ip_address', $customer_ip );
//     $order->save();
// }

// ADDING 2 NEW COLUMNS WITH THEIR TITLES (before "Total" and "Actions" columns)
add_filter( 'manage_edit-shop_order_columns', 'add_admin_order_list_custom_column' );
function add_admin_order_list_custom_column($columns)
{
    $columns['my-column1'] = 'Title1';
    $columns['my-column2'] = 'Title2';
    return $columns;
}