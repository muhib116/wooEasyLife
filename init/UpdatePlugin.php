<?php
namespace WooEasyLife\Init;

class UpdatePlugin
{
    private $plugin_slug;
    private $update_server_url;
    private $plugin_version;
    private $license_key;

    /**
     * Constructor to initialize the updater.
     *
     * @param string $plugin_version The current version of the plugin.
     * @param string $license_key    The license key for authorization.
     */
    public function __construct($plugin_version, $license_key)
    {
        $this->plugin_slug = 'woo-life-changer';
        $this->update_server_url = 'https://api.wpsalehub.com/api/get-metadata';
        $this->plugin_version = $plugin_version;
        $this->license_key = $license_key;

        // Add hooks for updates and plugin information
        add_filter('site_transient_update_plugins', [$this, 'check_for_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 10, 3);
    }

    /**
     * Check for plugin updates.
     *
     * @param object $transient The update transient object.
     * @return object The modified transient object.
     */
    public function check_for_update($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $response = wp_remote_get(
            $this->update_server_url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->license_key,
                ],
            ]
        );

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return $transient;
        }

        $update_data = json_decode(wp_remote_retrieve_body($response), true);

        if (
            isset($update_data['version'], $update_data['download_url']) &&
            version_compare($this->plugin_version, $update_data['version'], '<')
        ) {
            $transient->response[$this->plugin_slug . '/' . $this->plugin_slug . '.php'] = (object) [
                'slug'        => $this->plugin_slug,
                'new_version' => $update_data['version'],
                'url'         => $update_data['homepage'] ?? '',
                'package'     => $update_data['download_url'],
            ];
        }

        return $transient;
    }

    /**
     * Provide plugin information for the "View Details" popup.
     *
     * @param false|object|array $result The current plugin info.
     * @param string $action            The requested action.
     * @param object $args              The plugin arguments.
     * @return false|object|array The plugin info object or false if not handled.
     */
    public function plugin_info($result, $action, $args)
    {
        if ($action !== 'plugin_information' || $args->slug !== $this->plugin_slug) {
            return $result;
        }

        $response = wp_remote_get(
            $this->update_server_url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->license_key,
                ],
            ]
        );

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return $result;
        }

        $plugin_info = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($plugin_info['name'])) {
            $result = (object) [
                'name'          => $plugin_info['name'],
                'slug'          => $this->plugin_slug,
                'version'       => $plugin_info['version'],
                'author'        => $plugin_info['author'],
                'homepage'      => $plugin_info['homepage'],
                'download_link' => $plugin_info['download_url'],
                'requires'      => $plugin_info['requires'] ?? '',
                'tested'        => $plugin_info['tested'] ?? '',
                'requires_php'  => $plugin_info['requires_php'] ?? '',
                'sections'      => [
                    'description' => $plugin_info['sections']['description'] ?? '',
                    'changelog'   => $plugin_info['sections']['changelog'] ?? '',
                ],
            ];
        }

        return $result;
    }
}
