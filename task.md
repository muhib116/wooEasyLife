👉🏻 kun kun user otp dise ar kun kun user otp dey nai tar akta list dekhate hobe phone number soho

on plugin deactive table and wp_options data can delete or not

order status manage from plugin order list

alertBox position fixed kore dekhate hobe
OTP resend korle loader jayna

Invoice a real courier id bosate hobe
invoice ar header a company info add korte hobe

one click Courier entry


Dashboard:
=========================
Top selling product list
Order source
Sales performance
Sales amount 
discount amount
Sales target set
SMS Cost calculation dashboard

=============================
manual order entry
Cash management (Low priority) from funnel liner
Marketing Tools (Low priority) from funnel liner



make the system responsive
plugins update
tutorial
help center
request a feature


order source add korte hobe:
add_action('woocommerce_checkout_update_order_meta', 'auto_detect_order_source', 10, 2);

function auto_detect_order_source($order_id, $data) {
    if (defined('REST_REQUEST') && REST_REQUEST) {
        update_post_meta($order_id, '_order_source', 'API');
    } elseif (is_admin()) {
        update_post_meta($order_id, '_order_source', 'Admin');
    } else {
        update_post_meta($order_id, '_order_source', 'Website');
    }
}