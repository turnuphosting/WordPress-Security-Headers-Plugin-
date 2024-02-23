<?php
/*
Plugin Name: TurnUpSecurity HTTP Headers
Plugin URI: https://turnupsecurityshield.com/
Description: TurnUpSecurity HTTP Headers plugin allows you to enable HTTP headers from the settings page.
Version: 1.0
Author: TurnUpHosting
Author URI: https://turnuphosting.com/web-design/
License: GPL2
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

// Add a menu item under the Settings menu
add_action('admin_menu', 'http_headers_toggler_menu');

function http_headers_toggler_menu() {
    add_options_page('TurnUpSecurity HTTP Headers', 'TurnUpSecurity HTTP Headers', 'manage_options', 'http-headers-toggler', 'http_headers_toggler_settings_page');
}

// Render the settings page
function http_headers_toggler_settings_page() {
    ?>
    <div class="wrap">
        <h1>TurnUpSecurity HTTP Headers</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('http_headers_toggler');
            do_settings_sections('http-headers-toggler');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register and sanitize the settings
add_action('admin_init', 'http_headers_toggler_settings');

function http_headers_toggler_settings() {
    register_setting('http_headers_toggler', 'http_headers_toggler_options', 'http_headers_toggler_sanitize_options');

    add_settings_section('http_headers_toggler_section', 'HTTP Headers', 'http_headers_toggler_section_callback', 'http-headers-toggler');

    add_settings_field('http_headers_toggler_enable', 'Enable HTTP Headers', 'http_headers_toggler_enable_callback', 'http-headers-toggler', 'http_headers_toggler_section');
}

// Sanitize the options
function http_headers_toggler_sanitize_options($input) {
    $sanitized_input = array();

    if (isset($input['enable'])) {
        $sanitized_input['enable'] = sanitize_text_field($input['enable']);
    }

    return $sanitized_input;
}

// Render the section callback
function http_headers_toggler_section_callback() {
    echo '<p>Toggle the HTTP headers below:</p>';
}

// Render the enable callback
function http_headers_toggler_enable_callback() {
    $options = get_option('http_headers_toggler_options');
    $enable = isset($options['enable']) ? $options['enable'] : '';

    echo '<input type="checkbox" name="http_headers_toggler_options[enable]" value="1" ' . checked(1, $enable, false) . ' />';
}

// Set additional HTTP headers
add_action('send_headers', 'http_headers_toggler_set_headers');

function http_headers_toggler_set_headers() {
    $options = get_option('http_headers_toggler_options');
    $enable = isset($options['enable']) ? $options['enable'] : '';

    if ($enable) {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('X-Content-Type-Options: nosniff');
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Content-Security-Policy: upgrade-insecure-requests');
        header('Permissions-Policy: geolocation=(self "' . get_home_url() . '"), microphone=()');
    }
}

