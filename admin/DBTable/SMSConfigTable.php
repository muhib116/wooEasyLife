<?php
namespace WooEasyLife\Admin\DBTable;

if (!class_exists('SmsConfigTable')) :
class SMSConfigTable {
    public function __construct() {
        add_action('admin_notices', [$this, 'showAdminNotice']);
    }

    /**
     * Create the sms_config table
     */
    public function create() {
        global $wpdb;

        // Ensure the __PREFIX constant is defined
        if (!defined('__PREFIX')) {
            define('__PREFIX', 'woo_easy_life_');
        }

        // Define table name
        $table_name = $wpdb->prefix . __PREFIX . 'sms_config';
        $charset_collate = $wpdb->get_charset_collate();

        // SQL to create the table
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            status VARCHAR(255) NOT NULL,
            phone_number VARCHAR(255) NULL,
            message LONGTEXT NOT NULL,
            message_for ENUM('admin', 'customer') NOT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";              
        

        // Include the required file for dbDelta
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Execute the query
        dbDelta($sql);
        $this->insertDefaultData();
    }
    private function insertDefaultData() {
        global $wpdb;
    
        // Define table name
        $table_name = $wpdb->prefix . __PREFIX . 'sms_config';
    
        // Check if the table already contains records
        $record_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    
        if ($record_count > 0) {
            return; // If records exist, exit the function
        }
    
        // Insert default data
        $default_data = [
            [
                'status'      => 'wc-processing',
                'message_for' => 'admin',
                'phone_number' => '',
                'message'     => 'New order by $customer_name ($customer_phone), for "$product_name" at $site_name.\n\nSuccess rate: $customer_success_rate\nTotal bill: $total_amount.',
                'is_active'   => 0
            ],
            [
                'status'      => 'wc-processing',
                'message_for' => 'customer',
                'phone_number' => '',
                'message'     => 'Hi $customer_name, your order for "$product_name" has been placed at $site_name.\n\nTotal bill: $total_amount.\n\nFor any assistance: $admin_phone.\nThank you!',
                'is_active'   => 0
            ]
        ];
    
        foreach ($default_data as $data) {
            // Insert each default row
            $wpdb->insert(
                $table_name,
                $data,
                [
                    '%s', // status
                    '%s', // message_for
                    '%s', // phone_number
                    '%s', // message
                    '%d', // is_active
                ]
            );
        }
    }
    

    /**
     * Drop the sms_config table
     */
    public function delete() {
        global $wpdb;

        // Ensure the __PREFIX constant is defined
        if (!defined('__PREFIX')) {
            define('__PREFIX', 'wooeasylife_');
        }

        // Define table name
        $table_name = $wpdb->prefix . __PREFIX . 'sms_config';

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
            define('__PREFIX', 'wooeasylife_');
        }

        $table_name = $wpdb->prefix . __PREFIX . 'sms_config';

        // Check if the table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");

        if ($table_exists !== $table_name) {
            echo '<div class="notice notice-error is-dismissible">
                <p>' . esc_html__('The SMS configuration table was not created. Please deactivate and reactivate the "WooEasyLife" plugin.', 'wooeasylife') . '</p>
            </div>';
        }
    }
}
endif;