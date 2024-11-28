
<?php
// Check if WooCommerce is installed or active
add_action('admin_notices', 'check_woocommerce_installed');

function check_woocommerce_installed() {
    // Check if WooCommerce is active
    if (class_exists('WooCommerce')) {
        return; // WooCommerce is already installed and active
    }

    // Check if WooCommerce is installed but not activated
    $is_wooCommerce_installed = false;
    $plugins = get_plugins();
    foreach ($plugins as $plugin_file => $plugin_data) {
        if (strpos($plugin_file, 'woocommerce.php') !== false) {
            $is_wooCommerce_installed = true;
            break;
        }
    }

    if ($is_wooCommerce_installed) {
        // WooCommerce is installed but not activated
        echo '<div class="notice notice-warning is-dismissible">
                <p>WooCommerce is installed but not activated. <a class="text-green-500" href="' . esc_url(admin_url('plugins.php')) . '">Activate WooCommerce now</a>.</p>
              </div>';
    } else {
        // WooCommerce is not installed
        echo '<div class="notice notice-error is-dismissible">
                <p>WooCommerce is not installed. 
                    <a class="text-blue-500" href="' . esc_url(admin_url('plugin-install.php?s=woocommerce&tab=search&type=term')) . '">Install WooCommerce now</a>.
                </p>
              </div>';
    }
    return;
}