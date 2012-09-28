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
		// Message Language index, as set in language/xx/form_lang.php
		'success' => 'form_login_success_message',
		'error' => 'form_login_error_message',
		'not_found' => 'form_login_not_found_message',
		'not_activated' => 'form_login_not_activated_message',
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
	// Register Form
	'register' => array
	(
		'success' => 'form_register_success_message',
		// Emails which will be send when the form is properly processed
		'emails' => array
		(
			array
			(
				// Can be :
				// - Plain Email address : my.name@mydomain.com
				// - 'user' to send it to the email of the form
				// - 'website' to send it to the Email set in Ionize under Settings > Advanced > Email > Website
				'email' => 'website',
				// Language term index, as set in language/xx/form_lang.php
				'subject' => 'mail_website_registration_subject',
				// View file to use for the email
				'view' => 'mail/website_registration',
			),
			array
			(
				'email' => 'user',
				'subject' => 'mail_user_registration_subject',
				'view' => 'mail/user_registration',
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
		'success' => 	'form_profile_success_message',
		'error' => 		'form_profile_error_message',
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
	'password' => array
	(
		'success' => 	'form_password_success_message',
		'error' => 		'form_password_error_message',
		'not_found' => 	'form_password_not_found_message',
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
				'email' => 'user',
				'subject' => 'mail_user_password_subject',
				'view' => 'mail/user_password',
			),
		),
	),
);