<?php
// Enqueue Scripts and Styles
add_action('admin_enqueue_scripts', 'wel_enqueue_scripts');
$manifest_path = plugin_dir_path(__DIR__) . 'vue-project/dist/.vite/manifest.json';
$manifest = json_decode(file_get_contents($manifest_path), true);
$css_file_name = $manifest['src/main.ts']['css'][0] ?? null;
$js_file_name = $manifest['src/main.ts']['file'] ?? null;

function wel_enqueue_scripts($hook_suffix) {
    global $manifest_path;
    global $js_file_name;
    global $css_file_name;

    if (file_exists($manifest_path)) {
        if ($js_file_name) {
            wp_enqueue_script(
                'woo-easy-life',
                plugins_url('vue-project/dist/' . $js_file_name, __DIR__),
                [],
                null,
                true
            );
        }

        if ($css_file_name) {
            wp_enqueue_style(
                'woo-easy-life-style',
                plugins_url('vue-project/dist/' . $css_file_name, __DIR__),
                [],
                null
            );
        }
    }

    // Pass data to Vue
    wp_localize_script('woo-easy-life', 'WELData', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('wel_nonce'),
    ]);
}


add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if ('woo-easy-life' === $handle) {
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}, 10, 3);