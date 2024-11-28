<?php
// Notify users to update the Life Changer plugin
add_action('admin_notices', 'notify_our_plugin_update');

function notify_our_plugin_update() {
    // Get update information for plugins
    $plugin_updates = get_site_transient('update_plugins');

    // Replace with your plugin slug
    $plugin_slug = 'life-changer/life-changer.php';

    // Check if our plugin has an update
    if (!empty($plugin_updates->response) && isset($plugin_updates->response[$plugin_slug])) {
        // Get the plugin data
        $plugin_data = $plugin_updates->response[$plugin_slug];

        // Build the message
        $message = sprintf(
            'A new version of <strong>%s</strong> is available. Please <a href="%s">update to version %s</a> to ensure compatibility and access to new features.',
            esc_html($plugin_data->slug),
            esc_url(admin_url('update-core.php')),
            esc_html($plugin_data->new_version)
        );

        // Display the admin notice
        echo '<div class="notice notice-warning is-dismissible">
                <p>' . $message . '</p>
              </div>';
    }
}
