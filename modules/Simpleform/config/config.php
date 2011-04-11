<?php 

// <ion:usermanager request="global" attr="success_url" />
$config['simpleform_success_url'] = "success";


/**
 * Fields and rules from the "contact" form
 * The form name must be set in an hidden field called "form_name" of the form
 *
 */
$config['simpleform_contact'] = array(
	'email' => 'trim|required|min_length[5]|valid_email|xss_clean',
	'name' => 'trim|required|min_length[4]|xss_clean',
	'message' => 'required|xss_clean',
	'city' => 'antispam'
);

