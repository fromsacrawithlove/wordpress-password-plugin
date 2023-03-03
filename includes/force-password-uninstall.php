<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Removing plugin options
 *
 */

delete_option('wpfp_reset_days');
delete_option('wpfp_access_user_role');
delete_option('wpfp_notification_message'); 
delete_option('wpfp_reset_url'); 
delete_option('wpfp_shut_off_email'); 
