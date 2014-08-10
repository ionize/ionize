<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Theme Forms configuration
|--------------------------------------------------------------------------
|
| This forms config array will be merged with /application/config/forms.php
| You can overwrite standard forms definition by creating your own definition
| for the form you wish to overwrite.
|
*/
$config['forms'] = array
(
	// Contact form
	'contact' => array
	(
		// The method which will process the form
		// The function name has no importance, it must only be in the declared Tagmanager class
		// and be "public static"
		'process' => 'TagManager_Contact::process_data',

		// Redirection after process. Can be 'home' or 'referer' for the $_SERVER['HTTP_REFERER'] value.
		// If not set, doesn't redirect
		'redirect' => 'referer',

		// Messages Language index, as set in language/xx/form_lang.php
		'messages' => array(
			'success' => 'form_alert_error_message',
			'error' => 'form_alert_success_message',
		),
		'emails' => array
		(
			// To Site Administrator
			array
			(
				// Send the mail to the address filled in in the 'email' input of the form
				// Values can be :
				// - One plain Email address : my.name@mydomain.com
				// - 'form' to send it to the email of the form data
				// - 'site' to send it to the Email set in Ionize under Settings > Advanced > Email > Website
				// - 'contact' to send it to the Email set in Ionize under Settings > Advanced > Email > Contact
				// - 'info' to send it to the Email set in Ionize under Settings > Advanced > Email > Info
				// - 'technical' to send it to the Email set in Ionize under Settings > Advanced > Email > Info
				'email' => 'contact',

				// Translation item index
				'subject' => 'mail_website_contact_subject',

				// Used view : Located in /themes/your_theme/mail/contact.php
				'view' => 'mail/contact/to_admin',
			),
			// Send to user
			array
			(
				'email' => 'form',
				'subject' => 'mail_user_contact_subject',
				'view' => 'mail/contact/to_user',
			),
		),
		// Form definition: fields and rules
		'fields' => array
		(
			'name' => array
			(
				// CI validation rules
				'rules' => 'trim|required|min_length[3]|xss_clean',
				// Label translated index, as set in language/xx/form_lang.php
				// Will be used to display the label name in error messages
				'label' => 'form_label_firstname',
			),
			'email' => array(
				'rules' => 'trim|required|valid_email|xss_clean',
				'label' => 'form_label_email',
			),
			'topic' => array(
				'rules' => 'trim|required|xss_clean',
				'label' => 'form_label_topic',
			),
			'message' => array(
				'rules' => 'trim|required|xss_clean',
				'label' => 'form_label_message',
			)
		)
	),


	// Ajax Contact form
	// Displayed and processed by the module Ajaxform
	'contact_ajax' => array
	(
		// The method which will process the form
		// Originally in libraries/ajaxform_process.php, this class can be copied in your theme libraries folder
		'process' => 'Ajaxform_Process::process_contact',

		// Messages Language index, as set in language/xx/form_lang.php
		'messages' => array(
			'success' => 'form_alert_error_message',
			'error' => 'form_alert_success_message',
		),
		'emails' => array
		(
			// To Site Administrator
			array
			(
				// Send the mail to the address filled in in the 'email' input of the form
				// Values can be :
				// - One plain Email address : my.name@mydomain.com
				// - 'form' to send it to the email of the form data
				// - 'site' to send it to the Email set in Ionize under Settings > Advanced > Email > Website
				// - 'contact' to send it to the Email set in Ionize under Settings > Advanced > Email > Contact
				// - 'info' to send it to the Email set in Ionize under Settings > Advanced > Email > Info
				// - 'technical' to send it to the Email set in Ionize under Settings > Advanced > Email > Info
				'email' => 'contact',

				// Translation item index
				'subject' => 'mail_website_contact_subject',

				// Used view : Located in /themes/your_theme/mail/contact.php
				'view' => 'mail/contact/to_admin_ajax',
			),
			// Send to user
			array
			(
				'email' => 'form',
				'subject' => 'mail_user_contact_subject',
				'view' => 'mail/contact/to_user_ajax',
			),
		),
		// Form definition: fields and rules
		'fields' => array
		(
			'name' => array
			(
				// CI validation rules
				'rules' => 'trim|required|min_length[3]|xss_clean',
				// Label translated index, as set in language/xx/form_lang.php
				// Will be used to display the label name in error messages
				'label' => 'form_label_firstname',
			),
			'email' => array(
				'rules' => 'trim|required|valid_email|xss_clean',
				'label' => 'form_label_email',
			),
			'message' => array(
				'rules' => 'trim|required|xss_clean',
				'label' => 'form_label_message',
			)
		)
	),
);