<?php
namespace WooEasyLife\Admin\DBTable;

if(!class_exists('FraudTable')) :
class FraudTable{
    public function __construct()
    {
        add_action('admin_notices', [$this, 'showAdminNotice']);
    }

    public function create() {
        global $wpdb;
    
        // Define table name
        $table_name = $wpdb->prefix . __PREFIX . 'fraud_customers'; // Ensure __PREFIX__ is defined
        $charset_collate = $wpdb->get_charset_collate();
        // SQL to create the table
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            customer_id BIGINT UNSIGNED NOT NULL UNIQUE,
            report LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";
    
        // Include the required file for dbDelta
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    
        // Execute the query
        dbDelta($sql);
    }
    

    public function delete() {
        // die('die from delete');
        
        /**
         * Optional: Drop the fraud customers table if needed
         * Uncomment the following lines to enable table deletion
         */
        
        global $wpdb;
        $table_name = $wpdb->prefix . __PREFIX . 'fraud_customers';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }

    public function showAdminNotice() {
        global $wpdb;

        $table_name = $wpdb->prefix . __PREFIX . 'fraud_customers';

        // Check if the table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");

        if ($table_exists !== $table_name) {
            echo '<div class="notice notice-error is-dismissible">
                <p>' . esc_html__('The fraud customers table was not created. Please deactivate and reactivate the "WooEasyLife" plugin.', 'wooeasylife') . '</p>
            </div>';
        }
    }
}
endif;