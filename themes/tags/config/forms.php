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
	// My form example
	'myform' => array
	(
		// Messages Language index, as set in language/xx/form_lang.php
		'success' => 'form_myform_success_message',
		'error' => 'form_myform_error_message',
		'fields' => array
		(
			'name' => array(
				'rules' => 'trim|required|min_length[5]|xss_clean',
				'label' => 'form_label_name',
			),
			'company' => array(
				'rules' => 'trim|required|min_length[5]|xss_clean',
				'label' => 'form_myform_label_company',
			),
			'email' => array(
				'rules' => 'trim|required|min_length[5]|valid_email|xss_clean',
				'label' => 'form_label_email',
			),
		)
	),
);