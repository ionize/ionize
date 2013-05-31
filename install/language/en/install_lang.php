<?php

$lang = array();

/*
|--------------------------------------------------------------------------
| General
|--------------------------------------------------------------------------
*/
$lang['title_ionize_installation'] = 'Ionize Installation';
$lang['title_welcome'] = 'Welcome to Ionize !';

$lang['title_system_check'] = 		'System check result';
$lang['title_database_settings'] = 	'Database settings';
$lang['title_user_account'] = 	'Admin user account';
$lang['title_default_language'] = 	'Default language';
$lang['title_sample_data'] = 	'Install the sample website?';

$lang['button_next_step'] = 		'Next step';
$lang['button_skip_next_step'] = 	'Skip & Next step';
$lang['button_save_next_step'] = 	'Save & Go to Next step';
$lang['button_install_test_data'] = 	"Install test data";
$lang['button_start_migrate'] = 		'Start database migration';

$lang['nav_check'] = 'System check';
$lang['nav_db'] = 'Database';
$lang['nav_settings'] = 'Settings';
$lang['nav_end'] = 'End';
$lang['nav_data'] = 'Demo data';


/*
|--------------------------------------------------------------------------
| System check
|--------------------------------------------------------------------------
*/
$lang['php_version'] = 			'PHP >= 5';
$lang['php_version_found'] = 	'PHP version';
$lang['mysql_support'] = 		'MySQL support';
$lang['mysql_version_found'] = 	'MySQL version';
$lang['file_uploads'] = 		'File upload';
$lang['mcrypt'] = 				'PHP Mcrypt Lib';
$lang['gd_lib'] = 				'PHP GD Lib';
$lang['write_config_dir'] = 	'<b>/application/config/</b>';
$lang['write_files'] = 			'<b>/files/*</b>';
$lang['write_themes'] = 		'<b>/themes/*</b>';
$lang['config_check_errors'] = 	'Some base requirement are not OK.<br/>Please correct them to continue the installation.';
$lang['welcome_text'] = 		"<p>The following steps will help you to install Ionize.</p><p>Here are the results of the basic requirements check.<br/>If one requirement isn't OK, please correct it and refresh this page once it is corrected.</p>";
$lang['write_check_text'] = 	"<p>The following folders and files need to be writable...</p>";
$lang['title_folder_check'] = 	"These folders needs to be writable";
$lang['title_files_check'] = 	"These files needs to be writable";

/*
|--------------------------------------------------------------------------
| Database
|--------------------------------------------------------------------------
*/
$lang['database_driver'] = 			'Driver';
$lang['database_hostname'] = 		'Hostname';
$lang['database_name'] = 			'Database';
$lang['database_username'] = 		'User';
$lang['database_password'] = 		'Password';
$lang['database_create'] = 			'Create the database';
$lang['title_database_create'] = 	'Database creation';
$lang['db_create_text'] = 			"<p>Please fill your database settings.<br/>In case of upgrade, Ionize will detect the version you're running and upgrade the DB.</p><p><strong>Important:</strong> If you're upgrading, please first make a backup of your DB.</p>";
$lang['db_create_prerequisite'] = 			"The user needs to have the right to create database.<br/>If your database already exists, don't check it.";
$lang['database_error_missing_settings'] = 	'Some information is missing.<br/>Please fill all fields !';
$lang['database_success_install'] = 		'<b class="ex">The database was successfully installed.</b>';
$lang['database_success_install_no_settings_needed'] = 		'<b class="ex">Database OK.</b><br/>As the database already exists, the website settings step will be skipped.';
$lang['database_success_migrate'] = 		'<b class="ex">The database was successfully upgraded.</b>';
$lang['database_error_coud_not_connect'] = 		'Connection to the database fails with the provided settings.';
$lang['database_error_database_dont_exists'] = 		"The database doesn't exist !";
$lang['database_error_writing_config_file'] = 		"<b>Error :</b><br/>The file <b style=\"color:#000;\">/application/config/database.php</b> could not be written!<br/>Check your permissions.";
$lang['database_error_coud_not_write_database'] = 		"<b>Error :</b><br/> Impossible to write data in the database.<br/>Check your DB permissions.";
$lang['database_error_coud_not_create_database'] = "The installer cannot create the database. Check your database name or your rights";
$lang['database_error_no_ionize_tables'] = 			"The database you selected seems not to be an Ionize database. Please check again.";
$lang['database_error_no_users_to_migrate'] = 		"To user account to upgrade";
$lang['database_migration_from'] = 			'This database needs an upgrade.<br/>Upgrade from version : ';
$lang['database_migration_text'] = 		"<p class=\"error\"><b>NOTICE :</b><br/> The database will now be upgraded.<b><br/>Please backup your database before this upgrade.</p>";


/*
|--------------------------------------------------------------------------
| Settings
|--------------------------------------------------------------------------
*/
$lang['lang_code'] = 		'Code (2 chars)';
$lang['lang_name'] = 		'Label';
$lang['settings_default_lang_title'] = 		'Default language';
$lang['settings_default_lang_text'] = 		'Your website needs a default language.<br/>You can visit <a target="_blank" href="http://en.wikipedia.org/wiki/ISO_639-1">the Wikipedia ISO 639-1 page</a> for more information about language codes.';
$lang['settings_error_missing_lang_code'] = "The lang code is mandatory";
$lang['settings_error_missing_lang_name'] = "The lang name is mandatory";
$lang['settings_error_lang_code_2_chars'] = "The lang code must be on 2 chars. Example : \"en\"";
$lang['settings_error_write_rights'] = "No write rights on <b>/application/config/language.php</b>. Please check the PHP rights on this file.";
$lang['settings_error_write_rights_config'] = "No write rights on <b>/application/config/config.php</b>. Please check the PHP rights on this file.";
$lang['settings_error_admin_url'] = "The admin URL must be an alphanumerical string, without spaces or special chars";
$lang['settings_admin_url_title'] = 		'Administration panel URL';
$lang['settings_admin_url_text'] = 		'It is strongly recommended to change the default one.';
$lang['admin_url'] = 'Admin URL';

/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
*/
$lang['user_introduction'] = 	'You will connect to the Administration panel with this login.';
$lang['username'] = 			'Login (min. 4 chars)';
$lang['screen_name'] = 			'Full name';
$lang['email'] = 				'Email';
$lang['password'] = 			'Password (min. 4 chars)';
$lang['password2'] = 			'Confirm password';
$lang['user_error_missing_settings'] = 			'Please fill all fields !';
$lang['user_error_not_enough_char'] = 			'Login and Password must be at least 4 char length!';
$lang['user_error_email_not_valid'] = 			'Email seems not to be valid. Please correct.';
$lang['user_error_passwords_not_equal'] = 		'Password and confirmation password are not equal.	';
$lang['user_info_admin_exists'] = 		'An administrator user already exists in the database.<br/>You can skip this step if you wish not to create or update an Admin account.<br/><br/>IMPORTANT : You need to copy the encryption key from your old website to be able to login if you already have users in your DB:<br/>See: /application/config/config.php -> $config[\'encryption_key\']';
$lang['encryption_key'] = 			'Encryption Key';
$lang['encryption_key_text'] = 		"Ionize needs an 128 bits Encryption Key.<br />This key will encode the users account and all sensitive data.<br/>It will be written in the <b>/application/config/config.php</b> file.";
$lang['no_encryption_key_found'] = 	"The encryption key was not found. the user account were not migrated. <b>You must create a new Admin user</b>.";


/*
|--------------------------------------------------------------------------
| Data
|--------------------------------------------------------------------------
*/
$lang['data_install_intro'] = 	"<p>If this is the first time you use Ionize, it's strongly recommended you install the sample website.<br/>This website includes : </p><ul><li>A complete set of data, useful to test Ionize,</li><li>1 working example theme</li></ul>";
$lang['title_skip_this_step'] = "Skip this step";

$lang['title_finish'] = 		'Installation completed';
$lang['finish_text'] = 			'<b>IMPORTANT</b>: <br/>You have to delete the "<b>/install</b>" folder manually before accessing the website or the admin section.';
$lang['button_go_to_admin'] = 	'Go to admin';
$lang['button_go_to_site'] = 	'Go to website';
