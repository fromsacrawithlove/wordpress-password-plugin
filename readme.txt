== To Do ==

Implement email reminder option that will send a user  a reminder to update their password after x amount of days.

=== Force Password Reset ===

Requires at least: WordPress 4.0 or higher
Tested up to: 6.1.1
Requires PHP: 5.6 or higher
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This is a plugin that forces the users to change their password periodically adapted from the WP Force Password plugin.


== Description ==

This plugin is ideal for security reasons, provides password update reminders.

In the plugin settings, you can set Password Reset Days, and Select User roles will be required to reset their password regularly.

If the user password has expired this plugin redirects the login user to the admin screen profile.php page and the front end login lost password page wp-login.php to force change their password.

And also notice is displayed informing they must require to change their password to continue using this website.

 
Features of the Force Password plugin:
    * Add password reset days
    * Select user roles to allow the change expiry password
    * Enable/Disable reset password feature for any user
    * Force users to update expiry password
    * Reminder email notification for password expired
  


== Installation ==

Installation Steps

1. Upload the folder to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the admin WP Force Password Menu to enable plugin options


== Frequently Asked Questions ==

= How to use the WP Force Password plugin? =

In the plugin settings, you can set Password Reset Days, and Select User roles will be required to reset their password regularly.
ex: wp-admin > WP Force Password

= How we can enable or disable the force password reset feature for any user? = 

Admin can edit any user profile or any user have an option on the profile page to enable or disable the force password reset feature.
In the plugin settings, the only selected user roles can use enable or disable the force password reset feature.
ex: wp-admin > profile.php > Enable Force Password

= How to update the expiry password? =

If the user's password has been expired then after login it will automatically redirect to the admin screen profile page to see change expiry password notification then update the expiry password.
ex: wp-admin > profile.php > New Password
and in the site, front end login lost password page to see change expiry password notification then update the expiry password.
ex: wp-login.php?action=lostpassword



== Screenshots ==

1. Plugin setting options to allow the users role for force password reset feature
2. Enable or Disable reset password feature for any user 
3. Profile page change expiry password notification
4. Front end log in lost your password page change expiry password notification


== Changelog ==

= 1.0 =
First Stable Release. Upgrade to next for more features immediately 
