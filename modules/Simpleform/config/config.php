<?php 

$config['module_simpleform_name'] = "Simpleform";


/*
|--------------------------------------------------------------------------
| Settings for the form called "contact"
|--------------------------------------------------------------------------
|
| The form name must be set in an hidden field called "form_name" of the form
|
*/

// Email to which the form data will be set
// See libraries/simpleform_action.php->process_data()
$config['simpleform_contact_email'] = '';

// Title of the mail : Index of the module's translation file : Simpleform/language/xxx/simpleform_lang.php
$config['simpleform_contact_email_title'] = 'module_simpleform_email_title';

// Mail view to use when sending form data (without .php extension.
// Must be placed in MODPATH/Simpleform/views
$config['simpleform_contact_email_view'] = 'mail';

// Fields and rules for the form
$config['simpleform_contact'] = array(
	'name' => 'trim|required|min_length[4]|xss_clean',
	'email' => 'trim|required|min_length[5]|valid_email|xss_clean',
	'message' => 'required|xss_clean',
	'city' => 'antispam'
);


/*
|--------------------------------------------------------------------------
| Settings for the form called "xxx"
|--------------------------------------------------------------------------
|
| The form name must be set in an hidden field called "form_name" of the form
|
*/
$config['simpleform_xxx_email'] = '';

$config['simpleform_xxx_email_title'] = 'module_simpleform_xxx_email_title';

$config['simpleform_xxx_email_view'] = 'mail';

$config['simpleform_xxx'] = array(
	'name' => 'trim|required|min_length[4]|xss_clean',
	'email' => 'trim|required|min_length[5]|valid_email|xss_clean',
	'message' => 'required|xss_clean',
	'city' => 'antispam'
);

