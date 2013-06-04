<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Forms configuration
|--------------------------------------------------------------------------
|
| For each form, set the emails to be send and the validation rules
|
*/
$config['forms'] = array
(
	// Login Form
	'login' => array
	(
		'process' => 'TagManager_User::process_data',
		// Redirection after process. Can be 'home' or 'referer' for the $_SERVER['HTTP_REFERER'] value.
		// If not set, doesn't redirect
		'redirect' => 'referer',
		// Message Language index, as set in language/xx/form_lang.php
		'messages' => array
		(
			'success' => 'form_login_success_message',
			'error' => 'form_login_error_message',
			'not_found' => 'form_login_not_found_message',
			'not_activated' => 'form_login_not_activated_message',
		),
		'fields' => array
		(
			'email' => array(
				// CI rules
				'rules' => 'trim|required|min_length[5]|valid_email|xss_clean',
				// Label translated index, as set in language/xx/form_lang.php
				// Will be used to display the label name in error messages
				'label' => 'form_label_email',
			),
			'password' => array(
				'rules' => 'trim|required|min_length[4]|xss_clean',
				'label' => 'form_label_password',
			),
		)
	),

	'logout' =>array
	(
		'process' => 'TagManager_User::process_data',
		'redirect' => 'home',
	),

	// Register Form
	'register' => array
	(
		'process' => 'TagManager_User::process_data',
		'redirect' => 'referer',
		'messages' => array
		(
			'success' => 'form_register_success_message',
			'error' => 'form_register_error_message',
		),
		// Emails which will be send when the form is properly processed
		'emails' => array
		(
			array
			(
				// Values can be :
				// - One plain Email address : my.name@mydomain.com
				// - 'form' to send it to the email of the form data
				// - 'website' to send it to the Email set in Ionize under Settings > Advanced > Email > Website
				'email' => 'contact',
				// Language term index, as set in language/xx/form_lang.php
				'subject' => 'mail_website_registration_subject',
				// View file to use for the email
				'view' => 'mail/register/to_admin',
			),
			array
			(
				'email' => 'form',
				'subject' => 'mail_user_registration_subject',
				'view' => 'mail/register/to_user',
			),
		),
		// Each field of the form
		'fields' => array
		(
			'firstname' => array(
				'rules' => 'trim|required|xss_clean',
				'label' => 'form_label_firstname',
			),
			'lastname' => array(
				'rules' => 'trim|xss_clean',
				'label' => 'form_label_lastname',
			),
			'screen_name' => array(
				'rules' => 'trim|xss_clean',
				'label' => 'form_label_screen_name',
			),
			'email' => array(
				'rules' => 'trim|required|min_length[5]|valid_email|xss_clean',
				'label' => 'form_label_email',
			),
			'password' => array(
				'rules' => 'trim|required|min_length[4]|xss_clean',
				// 'rules' => 'trim|required|min_length[4]|matches[password2]|xss_clean',
				'label' => 'form_label_password',
			),
			/*
			'password2' => array(
				'rules' => 'trim|required|min_length[4]|xss_clean',
				'label' => 'form_label_password_confirmation',
				// If set to FALSE, ths field will not be saved to DB
				'save' => FALSE,
			),
			*/
			/*
			'birthdate' => array(
				'rules' => 'trim|xss_clean',
				'label' => 'form_label_birthdate',
			),
			'website' => array(
				'rules' => 'trim|xss_clean',
				'label' => 'form_label_website',
			),
			*/
		),
	),

	'profile' => array
	(
		'process' => 'TagManager_User::process_data',
		'redirect' => 'referer',
		'messages' => array
		(
			'success' => 	'form_profile_success_message',
			'error' => 		'form_profile_error_message',
			'deleted' => 	'form_profile_account_deleted',
			'not_logged' =>	'form_not_logged',
		),
		'fields' => array
		(
			'firstname' => array(
				'rules' => 'trim|required|xss_clean',
				'label' => 'form_label_firstname',
			),
			'lastname' => array(
				'rules' => 'trim|xss_clean',
				'label' => 'form_label_lastname',
			),
			'screen_name' => array(
				'rules' => 'trim|xss_clean',
				'label' => 'form_label_screen_name',
			),
			'email' => array(
				'rules' => 'trim|required|min_length[5]|valid_email|xss_clean',
				'label' => 'form_label_email',
			),
			'gender' => array(
				'type' => 'radio',
				'rules' => 'required',
				'label' => 'form_label_gender',
			),
			'birthdate' => array(
				'label' => 'form_label_birthdate',
			),
			'password' => array(
				'rules' => 'trim|min_length[4]|xss_clean',
				'label' => 'form_label_password',
			),
		),
	),
	//someone wants to get a new password send (needs confirmation first)
	'forgot_password_request' => array
	(
		'process' => 'TagManager_User::process_data',
		'messages' => array
		(
			'success' => 	'form_forgot_password_request_success_message',
			'error' => 		'form_forgot_password_request_error_message',
			'not_found' => 	'form_forgot_password_request_not_found_message',
		),
		'fields' => array
		(
			'email' => array(
				'rules' => 'trim|required|min_length[5]|valid_email|xss_clean',
				'label' => 'form_label_email',
			),
		),
		'emails' => array
		(
			array
			(
				'email' => 'form',
				'subject' => 'mail_user_forgot_password_request_subject',
				'view' => 'mail/user_forgot_password_request',
			),
		),
	),
	//someone clicked on a forgot-password-link
	'forgot_password_confirm' => array
	(
		'process' => 'TagManager_User::process_data',
		'messages' => array
		(
			'success' => 	'form_forgot_password_confirm_success_message',
			'error' => 		'form_forgot_password_confirm_error_message',
			'not_found' => 	'form_forgot_password_confirm_not_found_message',
		),
		'fields' => array
		(
			'email' => array(
				'rules' => 'trim|required|min_length[5]|valid_email|xss_clean',
				'label' => 'form_label_email',
			),
			'confirmation_code' => array(
				'rules' => 'trim|required|min_length[5]|xss_clean',
				'label' => 'form_label_confirmation_code',
			),
		),
		'emails' => array
		(
			array
			(
				'email' => 'form',
				'subject' => 'mail_user_forgot_password_confirm_subject',
				'view' => 'mail/user_forgot_password_confirm',
			),
		),
	),
);