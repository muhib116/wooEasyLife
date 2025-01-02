<?php
namespace WooEasyLife\Admin\DBTable;

if (!class_exists('SmsHistoryTable')) :
class SMSHistoryTable {
    public function __construct() {
        add_action('admin_notices', [$this, 'showAdminNotice']);
    }

    /**
     * Create the sms_history table
     */
    public function create() {
        global $wpdb;

        // Ensure the __PREFIX constant is defined
        if (!defined('__PREFIX')) {
            define('__PREFIX', 'woo_easy_life_');
        }

        // Define table name
        $table_name = $wpdb->prefix . __PREFIX . 'sms_history';
        $charset_collate = $wpdb->get_charset_collate();

        // SQL to create the table
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            phone_number TEXT NOT NULL,
            message TEXT NOT NULL,
            status VARCHAR(255) NOT NULL,
            error_message TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";

        // Include the required file for dbDelta
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Execute the query
        dbDelta($sql);
    }

    /**
     * Drop the sms_history table
     */
    public function delete() {
        global $wpdb;

        // Ensure the __PREFIX constant is defined
        if (!defined('__PREFIX')) {
            define('__PREFIX', 'woo_easy_life_');
        }

        // Define table name
        $table_name = $wpdb->prefix . __PREFIX . 'sms_history';

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

        $table_name = $wpdb->prefix . __PREFIX . 'sms_history';

        // Check if the table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");

        if ($table_exists !== $table_name) {
            echo '<div class="notice notice-error is-dismissible">
                <p>' . esc_html__('The SMS history table was not created. Please deactivate and reactivate the "WooEasyLife" plugin.', 'wooeasylife') . '</p>
            </div>';
        }
    }
}
endif;