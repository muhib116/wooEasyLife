<script>
    function updateBillingPhone(billingPhone) {
        console.log(billingPhone, woo_easy_life_woo_easy_life_ajax_obj)
        // Make an AJAX POST request to update WooCommerce session
        // fetch(ajax_object.ajaxurl, {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/x-www-form-urlencoded',
        //     },
        //     body: new URLSearchParams({
        //         action: 'update_billing_phone', // Action name defined in the PHP hook
        //         billing_phone: billingPhone    // Pass the phone number as a parameter
        //     }),
        // })
        //     .then(response => response.json())
        //     .then(data => {
        //         if (data.success) {
        //             console.log('Billing phone updated successfully:', data.data.billing_phone);
        //         } else {
        //             console.error('Error updating billing phone:', data.data.message);
        //         }
        //     })
        //     .catch(error => {
        //         console.error('AJAX request failed:', error);
        //     });
    }

    // Example usage
    
    
    
    setTimeout(() => {   
        updateBillingPhone('01712345678');
        // const cookies = document.cookie.split('; ');
        // const sessionCookie = cookies.find(cookie => cookie.startsWith('woocommerce_session_'));
        // // woocommerce_cart_hash=a36f66e5a7df946c1da359f11bf9e512
        // console.log({cookies})
        // if (sessionCookie) {
        //     const sessionId = sessionCookie.split('=')[1]; // Extract the session value
        // }
        
        // const checkoutForm = document.querySelector("form[name=checkout]")
        // console.log(checkoutForm)
    }, 2000)
</script>