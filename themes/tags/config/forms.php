<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Theme Forms configuration
|--------------------------------------------------------------------------
|
| This forms config array will be merged with /application/config/forms.php
| You can overwrite standard forms definition by creating your own deifnition
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
			'success' => 'form_message_success',
			'error' => 'form_message_error',
		),
		'emails' => array
		(
			array
			(
				// Send the mail to the address filled in in the 'email' input of the form
				// Values can be :
				// - One plain Email address : my.name@mydomain.com
				// - 'form' to send it to the email of the form data
				// - 'website' to send it to the Email set in Ionize under Settings > Advanced > Email > Website
				'email' => 'form',

				// Translation item index
				'subject' => 'mail_contact_subject',

				// Used view : Located in /themes/your_theme/mail/contact.php
				'view' => 'mail/contact',
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
				'label' => 'form_label_name',
			),
			'company' => array(
				'rules' => 'trim|required|xss_clean',
				'label' => 'form_myform_label_company',
			),
			'email' => array(
				'rules' => 'trim|required|valid_email|xss_clean',
				'label' => 'form_label_email',
			),
			// Example of radio inputs : "You heard about our company on..."
			'heard' => array(
				// Can be 'radio', 'checkbox', 'select' or not set for input and textarea
				'type' => 'radio',
				'rules' => 'required',
				'label' => 'form_label_heard_on',
			)
		)
	),
);