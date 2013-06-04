<?php
/*
|--------------------------------------------------------------------------
| Ionize Form library Language file
|
| Copy this file to /themes/<my_theme/language/xx/form_lang.php
| to replace these translations with your one.
|
| IMPORTANT :
| Do not modify this file.
| It will be overwritten when migrating to a new Ionize release.
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Labels
| To be used for form labels.
| Also used by Form Validation to display "human" name for each field in the errors messages.
| Declared as "label" for each fields of forms set in /config/ionize.php
|--------------------------------------------------------------------------
*/
$lang['form_label_email'] = 'Email';
$lang['form_label_name'] = 'Name';
$lang['form_label_firstname'] = 'First Name';
$lang['form_label_lastname'] = 'Last Name';
$lang['form_label_subject'] = 'Subject';
$lang['form_label_message'] = 'Message';
$lang['form_label_screen_name'] = 'Screen Name';
$lang['form_label_username'] = 'User name';
$lang['form_label_birthdate'] = 'Birthdate';
$lang['form_label_gender'] = 'Gender';
$lang['form_label_gender_female'] = 'Female';
$lang['form_label_gender_male'] = 'Male';
$lang['form_label_gender_unisex'] = 'I prefer not to say';
$lang['form_label_login'] = 'Login';
$lang['form_label_password'] = 'Password';
$lang['form_label_password_confirmation'] = 'Password confirm';
$lang['form_label_delete_account'] = 'Delete account';

/*
|--------------------------------------------------------------------------
| Buttons
|--------------------------------------------------------------------------
*/
$lang['form_button_send'] = "Send";
$lang['form_button_save'] = "Save";
$lang['form_button_register'] = "Register";
$lang['form_button_login'] = "Login";
$lang['form_button_logout'] = "Logout";
$lang['form_button_post'] = "Post";
$lang['form_button_answer'] = "Answer";
$lang['form_button_save_profile'] = "Save Profile";
$lang['form_button_password_back'] = "Get password back";

/*
|--------------------------------------------------------------------------
| Emails
|--------------------------------------------------------------------------
*/
// Registration : Email to Admin
$lang['mail_website_registration_subject'] = "Someone registered on the website";
$lang['mail_website_registration_message'] = "Here are the details of this new member.";

// Registration : Email to user
$lang['mail_user_registration_subject'] = "Registration on %s";
$lang['mail_user_registration_intro'] = "Dear %s,";
$lang['mail_user_registration_message'] = "You just registered on <b>%s</b>.<br/>Here are your login information.";
$lang['mail_user_registration_activate'] = "Before login, you need to activate your account through this link :";

// Forgot password request : Email to user
$lang['mail_user_forgot_password_request_subject'] = "You forgot your password for your account on %s ?";
$lang['mail_user_forgot_password_request_intro'] = "Dear %s,";
$lang['mail_user_forgot_password_request_message'] = "You just asked for one new password to access to the website <b>%s</b>.<br/>Ifyou really wanted to get a new password generated, just visit:";
$lang['mail_user_forgot_password_request_hint'] = 'If you did not request the reset of your password, ignore this Email. Please contact us if you receive this Email multiple times.';
// Forgot password confirm : Email to user
$lang['mail_user_forgot_password_confirm_subject'] = "Your new login data for your account on %s";
$lang['mail_user_forgot_password_confirm_intro'] = "Dear %s,";
$lang['mail_user_forgot_password_confirm_message'] = "Here is your new login data to access the website <b>%s</b>.<br/>";

// Contact : Email to Admin
$lang['mail_website_contact_subject'] = "Message from Contact Form";
$lang['mail_website_contact_message'] = "One visitor let you a message through the website contact form.";

// Contact : Email to user
$lang['mail_user_contact_subject'] = "Thank you for your message to %s";
$lang['mail_user_contact_intro'] = "Dear %s,";
$lang['mail_user_contact_message'] = "Thank you for your message.<br/>We will answer you very quickly.";

// Message about automatic message
$lang['mail_automatic_message_warning'] = "This message was automatically generated. Please do not answer.";


/*
|--------------------------------------------------------------------------
| Messages
| Success messages for contact / login / registration forms
| Declared as 'success' for each form in form setup : /config/ionize.php
|--------------------------------------------------------------------------
*/
$lang['form_not_logged'] = "You're not logged in.";

$lang['form_login_success_message'] = "You successfully logged in.";
$lang['form_login_error_message'] = "Error : Check your login / password.";
$lang['form_login_not_found_message'] = "User not found.";
$lang['form_login_not_activated_message'] = "This account is not activated. Check your emails and click on the activation link.";

$lang['form_register_success_message'] = "You successfully registered.";
$lang['form_register_error_message'] = "Error : Registration not successful.";

$lang['form_profile_success_message'] = "Profile data saved";
$lang['form_profile_error_message'] = "This user already exists. Please change your username or email";
$lang['form_profile_account_deleted'] = "Account deleted";

$lang['form_password_error_message'] = "Oups, one error occured.";
$lang['form_password_not_found_message'] = "This email seems not to be in our system";
$lang['form_password_success_message'] = "One email with you new password has just been sent to you.";

$lang['form_contact_error_title'] = 'Oups, we got an error.';
$lang['form_contact_error_message'] = 'perhaps just some missing fields. Please check the form...';
$lang['form_contact_success_title'] = 'Your message was sent successfully !';
$lang['form_contact_success_message'] = 'Thank you for your message, we will answer you very quickly !';


/*
|--------------------------------------------------------------------------
| Validation Errors
| of custom callbacks
| Must look like : $lang['form_error_<field_key>'] = 'Error message'
|
| For custom form validation translation, copy the wished file from
| /system/language/xx/form_validation_lang.php to your theme language folder
| /themes/my_theme/language/xx/form_validation_lang.php and adapt it.
|
|--------------------------------------------------------------------------
| $lang['form_error_upload'] = "Something went wrong while uploading the files.";
|
*/

