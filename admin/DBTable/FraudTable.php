<?php
namespace WooEasyLife\Admin\DBTable;

if(!class_exists('FraudTable')) :
class FraudTable{
    public $table_name = '';
    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . __PREFIX . 'fraud_customers'; // Ensure __PREFIX__ is defined

        add_action('admin_notices', [$this, 'showAdminNotice']);
    }

    public function create() {
        global $wpdb;
    
        $charset_collate = $wpdb->get_charset_collate();
        // SQL to create the table
        $sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            customer_id VARCHAR(16) NOT NULL UNIQUE,
            report LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";
    
        // Include the required file for dbDelta
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    
        // Execute the query
        dbDelta($sql);
    }
    

    public function delete() {
        /**
         * Optional: Drop the fraud customers table if needed
         * Uncomment the following lines to enable table deletion
         */
        
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS $this->table_name");
    }

    public function showAdminNotice() {
        global $wpdb;

        // Check if the table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$this->table_name'");

        if ($table_exists !== $this->table_name) {
            echo '<div class="notice notice-error is-dismissible">
                <p>' . esc_html__('The fraud customers table was not created. Please deactivate and reactivate the "WooEasyLife" plugin.', 'wooeasylife') . '</p>
            </div>';
        }
    }
}
endif;