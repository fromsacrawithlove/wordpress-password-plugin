<?php

/* Plugin Name: Force Password Reset
 * Description: This is a plugin that forces the users to change their password periodically adapted from the WP Force Password plugin.
 * 
 * Author: William Thames & Jhamere Wilson
 *  
 * Version: 1.1
 * Text Domain: wp-force-password
 * License:GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin path and url
define('Force_Password_URL', plugin_dir_url(__FILE__));
define('Force_Password_PATH', plugin_dir_path(__FILE__));

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Plugin activation hook 
if ( ! function_exists( 'wpfp_plugin_activate' ) ) {
    function wpfp_plugin_activate() {
        add_option('wpfp_plugin_do_activation_redirect', true);
    }
}
register_activation_hook(__FILE__, 'wpfp_plugin_activate');

// Plugin redirect page 
if ( ! function_exists( 'wpfp_plugin_redirect' ) ) {
    function wpfp_plugin_redirect() {
        if (get_option('wpfp_plugin_do_activation_redirect', false)) {
            delete_option('wpfp_plugin_do_activation_redirect');
            wp_redirect("admin.php?page=wpfp_page");
            exit;
        }
    }
}
add_action('admin_init', 'wpfp_plugin_redirect');

// Plugin uninstall hook
if ( ! function_exists( 'wpfp_plugin_uninstall' ) ) {
    function wpfp_plugin_uninstall() {  
        if (file_exists(Force_Password_PATH . 'includes/force-password-uninstall.php')) {
            require_once ( Force_Password_PATH . 'includes/force-password-uninstall.php');
        }
    }
}
register_uninstall_hook(__FILE__, 'wpfp_plugin_uninstall');

// Check plugin functions file exists
if (file_exists(Force_Password_PATH . 'includes/force-password-functions.php')) {
    require_once ( Force_Password_PATH . 'includes/force-password-functions.php');
}

/*
 * Enqueue Script for admin 
 */
if ( ! function_exists( 'wpfp_page_script' ) ) {
    function wpfp_page_script() {
        global $post_type;
        if (isset($_GET['page']) && $_GET['page'] == 'wpfp_page') {
            wp_enqueue_script('wpfp-admin-js', Force_Password_URL . 'assets/js/admin-main.js');
            wp_enqueue_script('wpfp-multiselect-js', Force_Password_URL . 'assets/js/jquery.multiselect.js');
            wp_enqueue_script('wpfp-validate-js', Force_Password_URL . 'assets/js/jquery.validate.min.js', array('jquery'));
            wp_enqueue_style('wpfp-admin-css', Force_Password_URL . 'assets/css/admin-style.css');
        }
    }
}
add_action('admin_enqueue_scripts', 'wpfp_page_script');

/*
 *  Plugin Setting Link 
 */
if ( ! function_exists( 'wpfp_settings_link' ) ) {
    function wpfp_settings_link($links_array, $wpfp_link) {
        if (strpos($wpfp_link, basename(__FILE__))) {
            array_unshift($links_array, '<a href="options-general.php?page=wpfp_page">' . __("Settings", "wp-force-password") . '</a>');
        }

        return $links_array;
    }
}

add_filter('plugin_action_links', 'wpfp_settings_link', 10, 2); 
