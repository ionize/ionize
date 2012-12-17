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
| To be used for form lables.
| Also used by Form Validation to display "human" name for each field in the errors messages.
| Declared as "label" for each fields of forms set in /config/ionize.php
|--------------------------------------------------------------------------
*/
$lang['form_label_email'] = 'Email';
$lang['form_label_name'] = 'Name';
$lang['form_label_firstname'] = 'First Name';
$lang['form_label_lastname'] = 'Last Name';
$lang['form_label_screen_name'] = 'Screen Name';
$lang['form_label_username'] = 'User name';
$lang['form_label_birthdate'] = 'Birthdate';
$lang['form_label_gender'] = 'Gender';
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

/*
|--------------------------------------------------------------------------
| Emails
|--------------------------------------------------------------------------
*/
// Registration : Email to the website email
$lang['mail_website_registration_subject'] = "Someone registered on the website";
$lang['mail_website_registration_message'] = "Here are the details of this new member.";

// Registration : Email to user
$lang['mail_user_registration_subject'] = "Registration on %s";
$lang['mail_user_registration_intro'] = "Dear %s,";
$lang['mail_user_registration_message'] = "You just registered on <b>%s</b>.<br/>Here are your login information.";
$lang['mail_user_registration_activate'] = "Before login, you need to activate your account through this link :";

// New Password : Email to user
$lang['mail_user_password_subject'] = "New password for your account on %s";
$lang['mail_user_password_intro'] = "Dear %s,";
$lang['mail_user_password_message'] = "You just asked for one new password to access to the website <b>%s</b>.<br/>Here are your new login information:";

/*
|--------------------------------------------------------------------------
| Messages
| Success messages for login / registration forms
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

$lang['form_password_error_message'] = "One error happens.";
$lang['form_password_not_found_message'] = "This email seems not to be in our system";
$lang['form_password_success_message'] = "One email with you new password has just been sent to you.";

/*
|--------------------------------------------------------------------------
| Validation Errors
|--------------------------------------------------------------------------
*/
$lang['form_error_javascript_required'] = "You need to have javascript activated to send this form.";
$lang['form_error_spam'] = "Thank you for your good Spam !";
$lang['form_error_required'] = "The <strong>%s</strong> field is required.";
$lang['form_error_isset'] = "The <strong>%s</strong> field must have a value.";
$lang['form_error_valid_email'] = "The <strong>%s</strong> field must contain a valid email address.";
$lang['form_error_valid_emails'] = "The <strong>%s</strong> field must contain all valid email addresses.";
$lang['form_error_valid_url'] = "The <strong>%s</strong> field must contain a valid URL.";
$lang['form_error_valid_ip'] = "The <strong>%s</strong> field must contain a valid IP.";
$lang['form_error_min_length'] = "The <strong>%s</strong> field must be at least %s characters in length.";
$lang['form_error_max_length'] = "The <strong>%s</strong> field can not exceed %s characters in length.";
$lang['form_error_exact_length'] = "The <strong>%s</strong> field must be exactly %s characters in length.";
$lang['form_error_alpha'] = "The <strong>%s</strong> field may only contain alphabetical characters.";
$lang['form_error_alpha_numeric'] = "The <strong>%s</strong> field may only contain alpha-numeric characters.";
$lang['form_error_alpha_dash'] = "The <strong>%s</strong> field may only contain alpha-numeric characters, underscores, and dashes.";
$lang['form_error_numeric'] = "The <strong>%s</strong> field must contain only numbers.";
$lang['form_error_is_numeric'] = "The <strong>%s</strong> field must contain only numeric characters.";
$lang['form_error_integer'] = "The <strong>%s</strong> field must contain an integer.";
$lang['form_error_matches'] = "The <strong>%s</strong> fields do not match.";
$lang['form_error_is_natural'] = "The <strong>%s</strong> field must contain only positive numbers.";
$lang['form_error_is_natural_no_zero']	= "The <strong>%s</strong> field must contain a number greater than zero.";
$lang['form_error_restricted_field'] = "Data were transmitted, which are not allowed.";
$lang['form_error_terms'] = "You have to accept the terms of usage.";
$lang['form_error_upload_something'] = "Something went wrong while uploading the files.";
$lang['form_error_upload_file_size'] = "The uploaded file needn't be larger than 1 MB.";
$lang['form_error_upload_file_type'] = "Only JPEGs, PNGs and GIFs are allowed.";

