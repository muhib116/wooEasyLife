<?php
namespace WooEasyLife\Admin\DBTable;

if (!class_exists('AbandonCartTable')) :
class AbandonCartTable {
    public function __construct() {
        add_action('admin_notices', [$this, 'showAdminNotice']);
    }

    /**
     * Create the abandon_cart table
     */
    public function create() {
        global $wpdb;

        // Ensure the __PREFIX constant is defined
        if (!defined('__PREFIX')) {
            define('__PREFIX', 'woo_easy_life_');
        }

        // Define table name
        $table_name = $wpdb->prefix . __PREFIX . 'abandon_cart';
        $charset_collate = $wpdb->get_charset_collate();

        // SQL to create the table
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            session_id VARCHAR(255) NOT NULL,
            customer_email VARCHAR(255) DEFAULT NULL,
            customer_phone VARCHAR(20) DEFAULT NULL,
            customer_name VARCHAR(255) DEFAULT NULL,
            cart_contents LONGTEXT NOT NULL,
            total_value DECIMAL(10, 2) NOT NULL,
            billing_address TEXT DEFAULT NULL,
            shipping_address TEXT DEFAULT NULL,
            is_repeat_customer BOOLEAN NOT NULL DEFAULT 0,
            status ENUM('abandoned', 'recovered', 'active') NOT NULL DEFAULT 'abandoned',
            recovered_at DATETIME DEFAULT NULL,
            abandoned_at DATETIME NOT NULL NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";

        // Include the required file for dbDelta
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Execute the query
        dbDelta($sql);
    }

    /**
     * Drop the abandon_cart table
     */
    public function delete() {
        global $wpdb;

        // Ensure the __PREFIX constant is defined
        if (!defined('__PREFIX')) {
            define('__PREFIX', 'woo_easy_life_');
        }

        // Define table name
        $table_name = $wpdb->prefix . __PREFIX . 'abandon_cart';

        // Optional: Uncomment the next line to delete the table on plugin deactivation
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }

    /**
     * Show an admin notice if the table does not exist
     */
    public function showAdminNotice() {
        global $wpdb;

        // Ensure the __PREFIX constant is defined
        if (!defined('__PREFIX')) {
            define('__PREFIX', 'woo_easy_life_');
        }

        $table_name = $wpdb->prefix . __PREFIX . 'abandon_cart';

        // Check if the table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");

        if ($table_exists !== $table_name) {
            echo '<div class="notice notice-error is-dismissible">
                <p>' . esc_html__('The abandon cart table was not created. Please deactivate and reactivate the "WooEasyLife" plugin.', 'wooeasylife') . '</p>
            </div>';
        }
    }
}
endif;