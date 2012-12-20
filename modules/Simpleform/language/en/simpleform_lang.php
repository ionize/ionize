<?php

$lang['module_simpleform_about'] = "This module adds simple forms to your website";
$lang['module_simpleform_doc_title'] = "How to setup this module";
$lang['module_simpleform_doc_content'] = "
	<h4>1. Setup the config.php :</h4>
	<p>Each Form is declared in the file <b>/modules/Simpleform/config/config.php</b><p>
	<pre>
// XXXXX must be your form name, in lowercase.
// Example : \$config['simpleform_contact_email'] = 'yourname@yourdomain.tld';

\$config['simpleform_XXXXX_email'] = 'yourname@yourdomain.tld';

// Title of the mail : Index of the module's translation file : Simpleform/language/xxx/simpleform_lang.php
// In this case, \$lang['module_simpleform_email_title'] will be used as email title for this form

\$config['simpleform_XXXXX_email_title'] = 'module_simpleform_email_title';

// Mail view to use when sending form data (without .php extension.
// Must be placed in /modules/Simpleform/views

\$config['simpleform_XXXXX_email_view'] = 'mail';

// Fields and rules for the form

\$config['simpleform_XXXXX'] = array(
	'name' => 'trim|required|min_length[4]|xss_clean',
	'email' => 'trim|required|min_length[5]|valid_email|xss_clean',
	'message' => 'required|xss_clean',
	'city' => 'antispam'
);
	</pre>

	<h4>2. Create the page / article containing the form</h4>
	<p>
		To create a form, simply create a page view, declare it and link it to a page (this is also possible with an article view)
	</p>
	<p>
		This view will contains calls to Simpleforms tags. <br/>
		Have a look at <b>views/form_view.php</b> to get inspired.
	</p>
	
	<h4>3. Modify the libraries/simpleform_action.php file</h4>
	<p>
		The method called <b>process_data()</b> processes the form if needed.<br/>
		Modify it to fit to your needs or to handle more than one form.
	</p>
	
";

$lang['module_simpleform_field_email'] = "Email";
$lang['module_simpleform_field_name'] = "Name";
$lang['module_simpleform_field_firstname'] = "First name";
$lang['module_simpleform_field_lastname'] = "Last name";
$lang['module_simpleform_field_username'] = "Username";
$lang['module_simpleform_field_password'] = "Password";
$lang['module_simpleform_field_password2'] = "Password rpt.";
$lang['module_simpleform_field_title'] = "Title";
$lang['module_simpleform_field_title_mr'] = "Mr.";
$lang['module_simpleform_field_title_ms'] = "Ms.";
$lang['module_simpleform_field_infomails_desc'] = "I want to receive information via email.";
$lang['module_simpleform_field_newsletter_desc'] = "I want to receive the newsletter via email.";
$lang['module_simpleform_field_terms_desc'] = "I have read and accepted the terms of usage.";
$lang['module_simpleform_field_terms'] = "Terms";
$lang['module_simpleform_field_company'] = "Company";
$lang['module_simpleform_field_street'] = "Street";
$lang['module_simpleform_field_city'] = "City";
$lang['module_simpleform_field_country'] = "Country";
$lang['module_simpleform_field_housenumber'] = "Housenumber";
$lang['module_simpleform_field_zip'] = "ZIP";
$lang['module_simpleform_field_website'] = "Website URL";
$lang['module_simpleform_field_subject'] = "Subject";
$lang['module_simpleform_field_message'] = "Message";
$lang['module_simpleform_all_fields_required'] = "All fields are required";

$lang['module_simpleform_button_send'] = "Send";
$lang['module_simpleform_button_save'] = "Save";

$lang['module_simpleform_text_error'] = "Oops, something went wrong...";
$lang['module_simpleform_text_success'] = "Your message was sent successfully !";
$lang['module_simpleform_text_thanks'] = "Thank you for you message. We will give an answer very quickly.";

$lang['module_simpleform_text_vip_success'] = "Your VIP request was successfully sent !";
$lang['module_simpleform_text_vip_thanks'] = "We will give an answer very quickly.";

$lang['module_simpleform_email_title'] = "Someone sent you a message from the website";
$lang['module_simpleform_vip_email_title'] = "VIP Request !";


$lang['module_simpleform_error_javascript_required'] = "You need to have javascript activated to send this form.";
$lang['module_simpleform_error_spam'] = "Thank you for your good Spam !";
$lang['module_simpleform_error_required'] = "The <strong>%s</strong> field is required.";
$lang['module_simpleform_error_isset'] = "The <strong>%s</strong> field must have a value.";
$lang['module_simpleform_error_valid_email'] = "The <strong>%s</strong> field must contain a valid email address.";
$lang['module_simpleform_error_valid_emails'] = "The <strong>%s</strong> field must contain all valid email addresses.";
$lang['module_simpleform_error_valid_url'] = "The <strong>%s</strong> field must contain a valid URL.";
$lang['module_simpleform_error_valid_ip'] = "The <strong>%s</strong> field must contain a valid IP.";
$lang['module_simpleform_error_min_length'] = "The <strong>%s</strong> field must be at least %s characters in length.";
$lang['module_simpleform_error_max_length'] = "The <strong>%s</strong> field can not exceed %s characters in length.";
$lang['module_simpleform_error_exact_length'] = "The <strong>%s</strong> field must be exactly %s characters in length.";
$lang['module_simpleform_error_alpha'] = "The <strong>%s</strong> field may only contain alphabetical characters.";
$lang['module_simpleform_error_alpha_numeric'] = "The <strong>%s</strong> field may only contain alpha-numeric characters.";
$lang['module_simpleform_error_alpha_dash'] = "The <strong>%s</strong> field may only contain alpha-numeric characters, underscores, and dashes.";
$lang['module_simpleform_error_numeric'] = "The <strong>%s</strong> field must contain only numbers.";
$lang['module_simpleform_error_is_numeric'] = "The <strong>%s</strong> field must contain only numeric characters.";
$lang['module_simpleform_error_integer'] = "The <strong>%s</strong> field must contain an integer.";
$lang['module_simpleform_error_matches'] = "The <strong>%s</strong> fields do not match.";
$lang['module_simpleform_error_is_natural'] = "The <strong>%s</strong> field must contain only positive numbers.";
$lang['module_simpleform_error_is_natural_no_zero']	= "The <strong>%s</strong> field must contain a number greater than zero.";
$lang['module_simpleform_error_restricted_field'] = "Data were transmitted, which are not allowed.";
$lang['module_simpleform_error_terms'] = "You have to accept the terms of usage.";
$lang['module_simpleform_error_upload_something'] = "Something went wrong while uploading the files.";
$lang['module_simpleform_error_upload_file_size'] = "The uploaded file needn't be larger than 1 MB.";
$lang['module_simpleform_error_upload_file_type'] = "Only JPEGs, PNGs and GIFs are allowed.";


