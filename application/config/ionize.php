<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Ionize Version
|--------------------------------------------------------------------------
|
*/
$config['version'] = '0.9.8';


/*
|--------------------------------------------------------------------------
| Ionize URLs
|--------------------------------------------------------------------------
| For compatibility reason.
| If your website uses Ionize < 0.9.8 and your pages are well referenced
| keep 'short' URLs
| Else, put 'long' URLs, which will be the future default way of URLs display
|
| 'full' : 	Full URL. Example : http://yourdomain/page/subpage
| 'short' : Small URL. Example : http://yourdomain/subpage
|
*/
$config['url_mode'] = 'full';


/* 
|--------------------------------------------------------------------------
| Available filemanagers
|--------------------------------------------------------------------------
| Javascript filemanagers.
| Must be useable with tinyMCE and idealy in standalone mode.
| In standalone mode, the filemanager is used by /javascript/ionizeMediaManager.js (addMedia method).
| 2 filemanagers are currently supported :
| - filemanager :		Moxiecode MceFilemanager / ImageManager (licensed module, not provided with ionize)
| - tinyBrowser :		http://www.lunarvis.com/
|
| A third filemanager implementation is in study : 
| - ezFilemanager :		http://www.webnaz.net/ezfilemanager/
|
| All the filemanagers must be put in the directory :
| /javascript/tinymce/jscripts/tiny_mce/plugins
|
| If you wish to add another one, look at /javascript/ionizeMediaManager.js to the methods : 
| - toggleFileManager()
| - toggleImageManager
*/
$config['filemanagers'] = array('mootools-filemanager');


/* 
|--------------------------------------------------------------------------
| Available texteditors
|--------------------------------------------------------------------------
| 
| Defines the installed text editors. Default is TinyMCE.
| CKEditor is still experimental and works best with kcfinder.
|
*/
$config['texteditors'] = array('tinymce');


/*
|--------------------------------------------------------------------------
| Ionize Special URI definition
|--------------------------------------------------------------------------
|
| Special URI setup
| Usee this array to define which URI segment to use for special URIs
| These URI are used for dedicated function like 
| - getting articles by category,
| - limit the number of displayed articles on one page (pagination)
| _ Getting articles by time period (acrhives)
|
| Array ( 'user_chosen_uri' => 'internal_uri' );
|
| Notice : Don't change the 'internal_uri' on standard functionnalities without knowing what you do ! 
*/
$config['special_uri'] = array(	'category' => 'category',
								'page' => 'pagination',
								'archive' => 'archives',
							  );


/**
|--------------------------------------------------------------------------
| Antispam key
| Wil be written by JS in one hidden field of the form
| If not present when form post : spam
|--------------------------------------------------------------------------
|
*/
$config['form_antispam_key'] = "yourAntiSpamKey_ShouldContainsNumbersAndChars";


/**
|--------------------------------------------------------------------------
| Medias base folder
|--------------------------------------------------------------------------
|
| This value is set here so external libraries can access to this data
| Ionize uses the file_path stored in database.
| Take care : Changing manually this value will not change the media folder.
| Changing the medias folder from Ionize UI will change this value
|
*/
$config['files_path'] = 'files/';


/*
|--------------------------------------------------------------------------
| Cache Expiration
|--------------------------------------------------------------------------
|
| Number of minutes you wish the page to remain cached between refreshes.
| 0 / false : disables the cache system.
| The cache folder set in config.php in the setting "cache_path" must exists
| and be writable.
|
*/
$config['cache_expiration'] = '0';


/*
|--------------------------------------------------------------------------
| Maintenance allowed IPs
|--------------------------------------------------------------------------
|
| These IPs are allowed to see the front-end website when the website
| is in maintenance mode.
| Values here are automatically set by the Settings Advanced panel
|
*/
$config['maintenance'] = false;

$config['maintenance_ips'] = array (
);


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
		// Success message Language index, as set in language/xx/form_lang.php
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
				'rules' => 'trim|xss_clean',
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

/* End of file ionize.php */
/* Location: ./application/config/ionize.php */