<?php
/*
|--------------------------------------------------------------------------
| Ionize Connect library Language file
|
| This lang file can be replaced by a theme one.
| Simply copy this file in the folder /themes/your_theme/language/xx/
| and modify the translations elements.
|
|--------------------------------------------------------------------------
*/

// Main library language elements
$lang['connect_login_failed'] = 'The login information you provided could not be authenticated. Either the username or the password you entered are wrong. Please try again.';
$lang['connect_access_denied'] = 'You have been denied access to %s';
$lang['connect_missing_parameters']	= 'The parameter(s) %s was missing';
$lang['connect_parameter_error'] = 'The parameter passed to %s is wrong.';
$lang['connect_user_save_impossible'] = 'We were not able to save your data into our system, please try again or contact us.';
$lang['connect_user_already_exists'] = 'There is already a user existing in our system with the same data. Please try to use another username or email address.';
$lang['connect_blocked'] = 'You have been blocked because of too many failed login, please try again %s';
$lang['connect_cannot_ban_yourself'] = 'You cannot ban yourself.';
$lang['connect_register_success'] = 'You have successfully registered.';
$lang['connect_register_success_verify_user'] = 'You have successfully registered but we must verify your data. An e-mail has been sent to you, please check it and click the activation link in the message to activate your account.';

// Activation mail to Admin
$lang['connect_admin_mail_subject'] = 'Registration';
$lang['connect_admin_mail_title'] = 'Registration';
$lang['connect_admin_mail_intro'] = 'A user just registered to the website.';
$lang['connect_admin_mail_nom'] = 'Name';
$lang['connect_admin_mail_login'] = 'Login';
$lang['connect_admin_mail_email'] = 'Email';
$lang['connect_admin_mail_activation_link'] = 'Activation link';

// Activation mail to User
$lang['connect_user_mail_subject'] = 'Your registration';
$lang['connect_user_mail_activated'] = 'Account activated';
$lang['connect_act_user_mail_title'] = 'Welcome !';
$lang['connect_act_user_mail_intro'] = 'You just registered to our website and we thank you.';
$lang['connect_act_user_mail_text'] = 'To confirm your registration, click on this activation link.';
$lang['connect_act_user_mail_activation_link'] = 'Activation link';

// Registration confirmation mail to User
$lang['connect_wait_user_mail_title'] = 'Welcome !';
$lang['connect_wait_user_mail_intro'] = 'You just registered to our website and we thank you.';
$lang['connect_wait_user_mail_text'] = 'Your account will be activated by the administrator quickly.';

// Registration views
$lang['connect_user_registration_title'] = 'Registration successfull';
$lang['connect_user_registration_message'] = 'You will get an email with your registration informations and instruction to confirm your registration';

// Activation views
$lang['connect_home_page'] = 'Home page';
$lang['connect_activation_title'] = 'Account activation';
$lang['connect_user_activated_message'] = 'Your account is activated.<br/>You can connect from the Home page';
$lang['connect_user_activated_error'] = 'Something went wrong with the account you are trying to activate. Maybe have you already activated it, or maybe are you using the wrong informations? Try to login with your account informations, or verify the email we\'ve sent to you and try again.';

$lang['connect_admin_activated_message'] = 'This account is activated now.<br/>A mail was just sent to the user to inform him.';
$lang['connect_admin_activated_error'] = 'Something went wrong with the account you are trying to activate.Maybe have you already activated it, or maybe are you using the wrong informations?';

