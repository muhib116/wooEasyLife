<?php
// Add Admin Menu Page
add_action('admin_menu', 'wel_add_menu');
function wel_add_menu() {
    add_menu_page(
        'WEL',
        'Woo Life Changer',
        'manage_options',
        'woo life changer',
        'wel_render_admin_page',
        'dashicons-smiley',
        100
    );
}

require_once __DIR__ . '/dashboard/index.php';
// Render Admin Page
function wel_render_admin_page() {
    echo '
    <style>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    </style>
    <div id="woo-easy-life" style="font-family: Poppins, sans-serif;">
    
    </div>'; // Vue app container
}