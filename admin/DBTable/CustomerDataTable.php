<?php
namespace WooEasyLife\Admin\DBTable;

if (!class_exists('CustomerDataTable')) :
class CustomerDataTable {
    public $table_name = '';
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . __PREFIX . 'customer_data';
        add_action('admin_notices', [$this, 'showAdminNotice']);
    }

    /**
     * Create the customer_data table
     */
    public function create() {
        global $wpdb;

        // Define table name
        $charset_collate = $wpdb->get_charset_collate();

        // SQL to create the table
        $sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            customer_id BIGINT UNSIGNED NULL, 
            order_id BIGINT UNSIGNED NOT NULL,
            phone VARCHAR(20) NULL,
            email VARCHAR(100) NULL,
            first_name VARCHAR(100) NULL,
            last_name VARCHAR(100) NULL,
            address TEXT NULL,
            city VARCHAR(100) NULL,
            state VARCHAR(100) NULL,
            postcode VARCHAR(20) NULL,
            country VARCHAR(10) NULL,
            tags TEXT NULL, 
            total_orders INT(11) DEFAULT 0,
            last_order_date DATETIME NULL,
            first_order_date DATETIME NULL,
            order_frequency VARCHAR(20) NULL,
            total_spent DECIMAL(10,2) DEFAULT 0.00,
            customer_type ENUM('new', 'returning', 'vip', 'fraud') DEFAULT 'new',
            referral_source VARCHAR(255) NULL,
            marketing_consent BOOLEAN DEFAULT 0,
            fraud_score INT(11) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_customer (customer_id, order_id, phone, email)
        ) $charset_collate;";

        // Include the required file for dbDelta
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Execute the query
        dbDelta($sql);
    }

    /**
     * Drop the customer_data table
     */
    public function delete() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS $this->table_name");
    }

    /**
     * Show an admin notice if the table does not exist
     */
    public function showAdminNotice() {
        global $wpdb;

        // Check if the table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$this->table_name'");

        if ($table_exists !== $this->table_name) {
            echo '<div class="notice notice-error is-dismissible">
                <p>' . esc_html__('The customer data table was not created. Please deactivate and reactivate the "WooEasyLife" plugin.', 'wooeasylife') . '</p>
            </div>';
        }
    }
}
endif;