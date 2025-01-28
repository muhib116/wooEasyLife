<?php
namespace WooEasyLife;

class PluginLifecycleHandle {
    public $handleDBTable;
    public $initClass;

    public function __construct() {
        register_activation_hook(__FILE__, [$this, 'woo_easy_life_activation_function']);
        register_deactivation_hook(__FILE__, [$this, 'woo_easy_life_deactivation_function']);
        register_uninstall_hook(__FILE__, [$this, 'woo_easy_life_uninstall_function']);
        add_action('init', [$this, 'updatePlugin']);

        $this->handleDBTable = new Admin\DBTable\HandleDBTable();
        $this->initClass = new Init\InitClass();
    }


    public function woo_easy_life_activation_function()
    {
        ob_start(); // Start output buffering

        if (empty(get_option(__PREFIX.'license'))) update_option(__PREFIX.'license', ['key'=> ""]);
        if (empty(get_option(__PREFIX.'balance'))) update_option(__PREFIX.'balance', '200');

        // Save a flag to indicate the table was created
        if (empty(get_option(__PREFIX.'plugin_installed'))) update_option(__PREFIX.'plugin_installed', true);

        $this->handleDBTable->create();
        $this->initClass->create_static_statuses();
        $this->initClass->save_default_config();

        ob_end_clean(); // Clear any unexpected output
    }

    public function woo_easy_life_deactivation_function()
    {
        global $config_data;

        if($config_data['clear_data_when_deactivate_plugin']){
            $this->cleanPluginData();
        }
    }

    public function woo_easy_life_uninstall_function()
    {
        $this->cleanPluginData();
    }

    private function cleanPluginData() {
        if (get_option(__PREFIX.'license') !== false) delete_option(__PREFIX.'license');
        if (get_option(__PREFIX.'balance') !== false) delete_option(__PREFIX.'balance');
        if (get_option(__PREFIX.'config') !== false) delete_option(__PREFIX.'config');

        // Remove plugin-specific options
        if (get_option(__PREFIX.'plugin_installed') !== false) delete_option(__PREFIX.'plugin_installed');
        if (get_option(__PREFIX.'custom_order_statuses') !== false) delete_option(__PREFIX.'custom_order_statuses');
        $this->handleDBTable->delete();
        delete_wc_orders_meta_by_key('_courier_data');
        delete_wc_orders_meta_by_key('_status_history');
    }

    public function updatePlugin() 
    {
        global $license_key;
        new  Init\UpdatePlugin($this->get_current_plugin_version(), $license_key);
    }


    private function get_current_plugin_version() {
        // Define the path to the plugin file
        $plugin_file = plugin_dir_path(__FILE__) . basename(__FILE__);

        // Check if the file exists
        if (file_exists($plugin_file)) {
            // Retrieve the plugin data
            $plugin_data = get_file_data($plugin_file, array('Version' => 'Version'));

            // Return the version if available
            return isset($plugin_data['Version']) ? $plugin_data['Version'] : null;
        }

        return null; // Return null if the file does not exist
    }
}