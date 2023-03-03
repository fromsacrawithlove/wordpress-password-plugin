<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/*
 * Registers a new options menu page under Settings.
 */
if ( ! function_exists( 'wpfp_plugin_menu' ) ) {
    function wpfp_plugin_menu() {
        add_menu_page(
            __('WP Force Password', 'wp-force-password'), __('WP Force Password', 'wp-force-password'), 'manage_options', 'wpfp_page', 'wpfp_plugin_menu_setting'
        );
    }
}
add_action('admin_menu', 'wpfp_plugin_menu');

/*
 * Plugin options settings  
 */
if ( ! function_exists( 'wpfp_plugin_menu_setting' ) ) {
    function wpfp_plugin_menu_setting() {
        ?>
        <div class="wrap">
            <h1><?php _e("WP Force Password", "wp-force-password"); ?></h1> 
            <form method="post" action="options.php" id="wp-force-password-form">
                <div class="wpfperrorTxt"></div>       
                <?php
                settings_fields('wpfp-group');
                $wpfp_access_user_role = get_option('wpfp_access_user_role');
                ?>   
                <table class="form-table wpfp-form-table">   
                    <tr valign="top">
                        <th scope="row"><?php _e("Password Reset Days", "wp-force-password"); ?><span class="force-password-required" aria-required="true"> *</span></th>
                        <td><input type="number" name="wpfp_reset_days" id="wpfp_reset_days" value="<?php echo esc_attr(get_option('wpfp_reset_days')); ?>" min="3" required=""/></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e("Select User Roles to Allow the Change Expiry Password", "wp-force-password"); ?><span class="force-password-required" aria-required="true"> *</span></th>
                        <td>
                            <select name="wpfp_access_user_role[]" id="wpfp_access_user_role" multiple="multiple" required=""> 

                                <?php
                                $editable_roles = get_editable_roles();
                                foreach ($editable_roles as $role => $details) {
                                    $name = translate_user_role($details['name']);
                                    // Preselect specified role.
                                    if (!empty($name)) {
                                        if (!empty($wpfp_access_user_role)) {
                                            echo '  <option value="' . esc_attr($role) . '" ' .
                                            (in_array($role, $wpfp_access_user_role) ? "selected" : "") .
                                            '>' . $name . '</option>' . "\n";
                                        } else {
                                            echo '  <option value="' . esc_attr($role) . '">' . $name . '</option>' . "\n";
                                        }
                                        
                                    }
                                }
                                ?>
                            </select>     
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e("Disable Reminder Emails on the Staging Site", "wp-force-password"); ?></th>
                        <td>
                            <input type="checkbox" name="wpfp_shut_off_email" value="1"<?php checked( 1 == esc_attr(get_option('wpfp_shut_off_email')) ); ?> />
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e("Expiry Password Change Notification Message", "wp-force-password"); ?></th>
                        <td>
                            <textarea placeholder="Please change your password to continue using this website." id="wpfp_notification_message" name="wpfp_notification_message"><?php echo esc_attr(get_option('wpfp_notification_message')); ?></textarea>
                        </td>
                    </tr>   

                    <tr valign="top" id="col_wpfp_reset_days" <?php if(get_option('wpfp_shut_off_email') == 1){echo 'style="display:none"';}?>>
                        <th scope="row"><?php _e("Change Reset Password Url", "wp-force-password"); ?></th>
                        <td>
                            <input type="text" placeholder="Please change your password reset url." id="wpfp_reset_days" name="wpfp_reset_url" value="<?php echo esc_attr(get_option('wpfp_reset_url')); ?>">
                        </td>
                    </tr>

                </table>

                <?php submit_button(); ?>

            </form>

        </div>

        <?php
    }
}
/*
 * Register plugin options settings
 */
if ( ! function_exists( 'wpfp_plugin_settings' ) ) {
    function wpfp_plugin_settings() {
        register_setting('wpfp-group', 'wpfp_reset_days');
        register_setting('wpfp-group', 'wpfp_access_user_role');
        register_setting('wpfp-group', 'wpfp_shut_off_email');
        register_setting('wpfp-group', 'wpfp_notification_message');
        register_setting('wpfp-group', 'wpfp_reset_url');
    }
}
add_action('admin_init', 'wpfp_plugin_settings');

/*
 * Force Password User profile update
 */
if ( ! function_exists( 'wpfp_save_profile_fields' ) ) {
    function wpfp_save_profile_fields() {
        global $current_user;
        $user_id = $current_user->ID;
        $userid = sanitize_text_field($_REQUEST['user_id']);
        $wpfp_days = get_option('wpfp_reset_days');

        if (!empty($wpfp_days)) {
            if (isset($_POST['pass1']) && !empty($_POST['pass1'])) {

                $start_date = date('Y-m-d H:i:s', current_time('timestamp'));
                $end_date = date('Y-m-d H:i:s', strtotime($start_date . " + $wpfp_days days"));

                update_user_meta($userid, 'wpfp_expiry_date', $end_date);
                /* meta key delete if exist */ 
                $wpfp_email_reminder_date = get_user_meta($userid, 'wpfp_email_reminder_date', 'true');
                if (!empty($wpfp_email_reminder_date)) {
                    delete_user_meta($userid, 'wpfp_email_reminder_date');
                }
            }
        }

        // Update usermeta Enable/disable force password reset value  

        if (!empty($userid)) {
            if (isset($_POST['enable_wpfp']) && !empty($_POST['enable_wpfp'])) {
                update_user_meta($userid, 'enable_wpfp', 'yes');
            } else {
                update_user_meta($userid, 'enable_wpfp', 'no');
            }
        } else {
            if (isset($_POST['enable_wpfp']) && !empty($_POST['enable_wpfp'])) {
                update_user_meta($user_id, 'enable_wpfp', 'yes');
            } else {
                update_user_meta($user_id, 'enable_wpfp', 'no');
            }
        }
    }
}
add_action('personal_options_update', 'wpfp_save_profile_fields');
add_action('edit_user_profile_update', 'wpfp_save_profile_fields');

/*
 * Force Password User Register
 */
if ( ! function_exists( 'wpfp_save_user_register' ) ) {
    function wpfp_save_user_register($user_id) {
        $wpfp_days = get_option('wpfp_reset_days');
        if (!empty($wpfp_days)) {
            $start_date = date('Y-m-d H:i:s', current_time('timestamp'));
            $end_date = date('Y-m-d H:i:s', strtotime($start_date . " + $wpfp_days days"));
            update_user_meta($user_id, 'wpfp_expiry_date', $end_date);
        }
    }
}
add_action('user_register', 'wpfp_save_user_register');

/*
 * Save Password Reset 
 */

# When reseting password in wp-login
if ( ! function_exists( 'wpfp_action_password_reset' ) ) {
    function wpfp_action_password_reset($user, $pass) {
        $user_id = $user->ID;
        $wpfp_days = get_option('wpfp_reset_days');

        if (!empty($wpfp_days)) {
            $start_date = date('Y-m-d H:i:s', current_time('timestamp'));
            $end_date = date('Y-m-d H:i:s', strtotime($start_date . " + $wpfp_days days"));
            update_user_meta($user_id, 'wpfp_expiry_date', $end_date);
        }
    }
}
add_action('password_reset', 'wpfp_action_password_reset', 10, 2);

/*
 * Get current User Role
 */
if ( ! function_exists( 'wpfp_get_current_user_role' ) ) {
    function wpfp_get_current_user_role() {
        global $current_user;
        $user_id = $current_user->ID;
        $user = new WP_User($user_id);
        if (!empty($user->roles) && is_array($user->roles)) {
            foreach ($user->roles as $role)
                return $role;
        }
    }
}
/*
 *  Get Force Password User status 
 */

if (function_exists('wpfp_get_current_user_role')) {

    function wpfp_get_user_status() {
        global $current_user;
        $user_id = $current_user->ID;
        $role = wpfp_get_current_user_role();
        $wpfp_access_user_role = get_option('wpfp_access_user_role');

        $start_date = date('Y-m-d H:i:s', current_time('timestamp'));
        $expiry_end_date = get_user_meta($current_user->ID, 'wpfp_expiry_date', true);
        $enable_wpfp = get_user_meta($user_id, 'enable_wpfp', true);

        if (!empty($wpfp_access_user_role) && in_array($role, $wpfp_access_user_role)) {
            if (!empty($expiry_end_date) && (strtotime($expiry_end_date) <= strtotime($start_date))) {
                if (($enable_wpfp == 'yes') || ($enable_wpfp == '')) {
                    return true;
                }
            }
        }
    }

}

/*
 * Force Password template redirect
 */
if ( ! function_exists( 'wpfp_redirect_dashboard' ) ) {
    function wpfp_redirect_dashboard() {
        if (function_exists('wpfp_get_user_status')) {
            $user_status = wpfp_get_user_status();
            if (!empty($user_status) && ($user_status == true)) {
                wp_redirect(get_bloginfo('url') . '/wp-login.php?action=lostpassword');
                exit;
            }
        }
    }
}

add_action('template_redirect', 'wpfp_redirect_dashboard');

/*
 * Force Password screen redirect
 */
if ( ! function_exists( 'wpfp_redirect_fontend' ) ) {
    function wpfp_redirect_fontend() {
        if (function_exists('wpfp_get_user_status')) {
            $user_status = wpfp_get_user_status();
            if (!empty($user_status) && ($user_status == true)) {
                $screen = get_current_screen();
                if ('profile' == $screen->base)
                    return;
                wp_redirect(admin_url('profile.php') . "#wpfp");
                exit;
            }
        }
    }
}
add_action('current_screen', 'wpfp_redirect_fontend');

/*
 * Force Password admin Notification 
 */
if ( ! function_exists( 'wpfp_notice_password' ) ) {
    function wpfp_notice_password() {
        if (function_exists('wpfp_get_user_status')) {
            $user_status = wpfp_get_user_status();
            $wpfp_notification_message = get_option('wpfp_notification_message');

            if (!empty($wpfp_notification_message)) {
                $message = $wpfp_notification_message;
            } else {
                $message = "Please change your password to continue using this website.";
            }
            if (!empty($user_status) && ($user_status == true)) {
                printf(
                        '<div class="error"><h3>%s</h3></div>', __($message, 'wp-force-password')
                );
            }
        }
    }
}

add_action('admin_notices', 'wpfp_notice_password');

/*
 * Force Password Notification  
 * Custom Password reset message to WordPress login page
 */
if ( ! function_exists( 'wpfp_reset_password_message' ) ) {
    function wpfp_reset_password_message($message) {
        if (function_exists('wpfp_get_user_status')) {
            $user_status = wpfp_get_user_status();
            $wpfp_notification_message = get_option('wpfp_notification_message');

            if (!empty($user_status) && ($user_status == true)) {
                if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'lostpassword') {
                    if (!empty($wpfp_notification_message)) {
                        $message = $wpfp_notification_message;
                    } else {
                        $message = "Please change your password to continue using this website.";
                    }
                    printf(
                            '<div id="login_error"><h3>%s</h3></div>', __($message, 'wp-force-password')
                    );
                }
            } else {
                return $message;
            }
        }
    }
}
add_filter('login_message', 'wpfp_reset_password_message');

/*
 * Add the Checkbox field to the user profile page enable/disable force password reset feature 
 */
if ( ! function_exists( 'wpfp_show_extra_profile_fields' ) ) {
    function wpfp_show_extra_profile_fields($user) {
        if (function_exists('wpfp_get_current_user_role')) {
            global $current_user;
            $user_id = $current_user->ID;
            $wpfp_access_user_role = get_option('wpfp_access_user_role');

            if (!empty($_REQUEST['user_id'])) {
                $userid = sanitize_text_field($_REQUEST['user_id']);
                $user_meta = get_userdata($userid);
                $role = $user_meta->roles[0];
            } else {
                $role = wpfp_get_current_user_role();
            }

            if (!empty($wpfp_access_user_role) && in_array($role, $wpfp_access_user_role)) {
                if (!empty($_REQUEST['user_id'])) {
                    $userid = sanitize_text_field($_REQUEST['user_id']);
                    $enable_wpfp = get_user_meta($userid, 'enable_wpfp', true);
                } else {
                    $enable_wpfp = get_user_meta($user_id, 'enable_wpfp', true);
                }
                ?>
                <h3><?php _e('WP Force Password', 'wp-force-password'); ?></h3>

                <table class="form-table" id="wpfp">
                    <tr class="enable-wpfp-wrap">  
                        <th scope="row"><?php _e('Enable Force Password', 'wpfp') ?></th>
                        <td><input type="checkbox" name="enable_wpfp" value="yes" <?php
                            if ($enable_wpfp == 'yes') {
                                echo "checked=checked";
                            } else if ($enable_wpfp == '') {
                                echo "checked=checked";
                            }
                            ?>><?php _e('Yes', 'wp-force-password'); ?></td>
                    </tr>
                </table>
                <?php
            }
        }
    }
}
add_action('show_user_profile', 'wpfp_show_extra_profile_fields');
add_action('edit_user_profile', 'wpfp_show_extra_profile_fields');

/*
 * Reminder Email Password Expired
 */
if ( ! function_exists( 'wpfp_reminder_email' ) ) {
  function wpfp_reminder_email() {
    global $wpdb;

    $admin_email = get_option('admin_email');
    $wpfp_reset_url = get_option('wpfp_reset_url');
    $wpfp_shut_off_email = get_option('wpfp_shut_off_email');
    $site_url = " <a href='" . get_bloginfo('url') . "'>" . get_bloginfo('url') . "</a>";
    $site_url_login = " <a href='" . get_bloginfo('url') . "/wp-login.php?action=lostpassword" . "'>" . get_bloginfo('url') . "/wp-login.php?action=lostpassword" . "</a>";
    $start_date = date('Y-m-d H:i:s', current_time('timestamp'));

    /* disable the reminder email functionality on the staging */
      if (empty($wpfp_shut_off_email)) {
                    
      /* Get user data by meta query  */
      $args = array(
          'meta_query' => array(
              array(
                  'key' => 'wpfp_expiry_date',
                  'value' => $start_date,
                  'compare' => '<='
              ),
              array(
                  'key' => 'wpfp_email_reminder_date',
                  'compare' => 'NOT EXISTS'
              )
          )
      );

      $users_data = get_users($args);

      if (!empty($users_data)) {

        foreach ($users_data as $data) {

          $user_id = $data->ID;
          $user_email = $data->user_email;
          $user_name = $data->user_login;
          $wpfp_email_reminder_date = get_user_meta($user_id, 'wpfp_email_reminder_date', true);

          $enable_wpfp = get_user_meta($user_id, 'enable_wpfp', true);

          $role = $data->roles[0];
          $wpfp_access_user_role = get_option('wpfp_access_user_role');
          $reset_url_label = __("Click Here to Reset Password, ", "wp-force-password") . $site_url_login;
          if(!empty($wpfp_reset_url)){ 
              $reset_url_label = " <a href='" . $wpfp_reset_url . "'>" . __("Click Here", "wp-force-password") . "</a>";
          }

          if (!empty($wpfp_access_user_role) && in_array($role, $wpfp_access_user_role)) {

            if (($enable_wpfp == 'yes') || ($enable_wpfp == '')) {

              if (empty($wpfp_email_reminder_date)) {

                $to = $user_email;
                $subject = __("Password Expired", 'wp-force-password');
                $body = "<html><body><div style='padding: 15px 30px;font-weight: 600;'><h3 style='color:#E91E63;'>" . __("Hello, ", "wp-force-password") . $user_name . "</h3>
                  <p style='color:#000;font-size:18px;'>" . __("Your password has been expired please update it. ", "wp-force-password") . " </p> 
                  <p style='color:#080;font-size:18px;'>" . __("User Name : ", "wp-force-password") . $user_name . "</p>
                  <p style='color:#080;font-size:18px;'>" . __("Email : ", "wp-force-password") . $user_email . "</p>    
                  <p style='color:#080;font-size:18px;'>" . $reset_url_label . "</p>     
                  <p style='color:#cd2653;font-size:18px;'>" . __("Thanks, ", "wp-force-password") . " </p> 
                  <p style='color:#cd2653;font-size:18px;'>" . __("Admin", "wp-force-password") . " </p></div></body></html>";

                $subject_admin = __("User Password Expired", 'wp-force-password');
                $body_admin = "<html><body><div style='padding: 15px 30px;font-weight: 600;'><h3 style='color:#E91E63;'>" . __("Hello, Admin", "wp-force-password") . "</h3>
                  <p style='color:#000;font-size:18px;'>" . __("Below user's password has expired. ", "wp-force-password") . " </p> 
                  <p style='color:#080;font-size:18px;'>" . __("User Name : ", "wp-force-password") . $user_name . "</p>
                  <p style='color:#080;font-size:18px;'>" . __("Email : ", "wp-force-password") . $user_email . "</p>    
                  <p style='color:#080;font-size:18px;'>" . __("Site URL, ", "wp-force-password") . $site_url . " </p>     
                  <p style='color:#cd2653;font-size:18px;'>" . __("Thanks, ", "wp-force-password") . " </p> 
                  <p style='color:#cd2653;font-size:18px;'>" . __("Admin", "wp-force-password") . " </p></div></body></html>";

                $headers = array('Content-Type: text/html; charset=UTF-8');

                $sent_email_success = wp_mail($to, $subject, $body, $headers);

                if ($sent_email_success) {
                  wp_mail($admin_email, $subject_admin, $body_admin, $headers);
                  /* update reminder date meta key */    
                  update_user_meta($user_id, 'wpfp_email_reminder_date', $start_date);
                }
              }
            }
          }
        }
      }
    } /* disable reminder functionlity on staging */
  }
}
add_action('wp', 'wpfp_reminder_email');