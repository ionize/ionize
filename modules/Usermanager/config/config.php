<?php 

// The controller / view where you can log in
// e.g. www.domain.com/login
// In this case, it's "login"
$config['usermanager_login_url'] = "login";

// The controller / view where you can register
// e.g. www.domain.com/register
// In this case, it's "register"
$config['usermanager_register_url'] = "register";

// The controller / view to a user's profile
// e.g. www.domain.com/profile
// In this case, it's "profile"
$config['usermanager_profile_url'] = "profile";

// The controller / view to the activation
// e.g. www.domain.com/activate
// In this case, it's "activate"
$config['usermanager_activation_url'] = "activation";


// Use e-mail for login
// If true, it'll overwrite the usermanager_user_midel->username value (see below)
$config['usermanager_email_as_username'] = false;

// Display exact error messages for login
// If true, it'll write exact error messages for a login attempt (min_length, xss_clean, etc.)
// Otherwise it'll only return bad_login_information
$config['usermanager_display_login_errors'] = false;



// Some configuration regarding pictures, the user can upload
// "sizes" will be contrainted. If a picture is 500x1000, "xl" will be 250x500, "s" will be 25x50
// The first field in the array (e.g. 'picture' in this example) is the name for the field in $config['usermanager_user_model']
// "default" is returned by the picture-tag if no picture was found.
$config['usermanager_picture'] = array(
	"picture" => array("upload_path"   => APPPATH . "../files/useruploads",
					   "view_path"     => base_url() . "files/useruploads",
					   "allowed_mimes" => array("gif"	=>	"image/gif",
											    "jpeg"	=>	array("image/jpeg", "image/pjpeg"),
					  						    "jpg"	=>	array("image/jpeg", "image/pjpeg"),
					  						    "jpe"	=>	array("image/jpeg", "image/pjpeg"),
					  						    "png"	=>	array("image/png",  "image/x-png")),
					   "dimensions"    => array("profile"   => array(240, 180),
					  						    "avatar"    => array(60, 60),
					  						    "l"   => array(200, 200),
					  						    "xl"  => array(500, 500),
					  						    "xxl" => array(900, 900)),
					   "max_size" 	   => 1048576, // 1MB
				   	   "default" 	   => array("profile"   => base_url()."themes/".Settings::get('theme')."/assets/images/silhouette_small2.png",
					  						    "avatar"    => base_url()."themes/".Settings::get('theme')."/assets/images/silhouette_small2.png",
					  						    "l"   => base_url()."themes/".Settings::get('theme')."/assets/images/silhouette_small.png",
					  						    "xl"  => base_url()."themes/".Settings::get('theme')."/assets/images/silhouette_small.png",
					  						    "xxl" => base_url()."themes/".Settings::get('theme')."/assets/images/silhouette.png")),

	"logo"    => array("upload_path"   => APPPATH . "../files/useruploads",
					   "view_path"     => base_url() . "files/useruploads",
					   "allowed_mimes" => array("gif"	=>	'image/gif',
					  						    "jpeg"	=>	array('image/jpeg', 'image/pjpeg'),
					  						    "jpg"	=>	array('image/jpeg', 'image/pjpeg'),
					  						    "jpe"	=>	array('image/jpeg', 'image/pjpeg'),
					  						    "png"	=>	array('image/png',  'image/x-png')),
					   "dimensions"    => array("s"   => array(50, 100),
					  						    "m"   => array(100, 200),
					  						    "l"   => array(300, 400),
					  						    "xl"  => array(500, 600),
					  						    "xxl" => array(900, 1100)),
					   "max_size" 	   => 1048576, // 1MB
				   	   "default" 	   => array("s"   => base_url()."themes/".Settings::get('theme')."/assets/images/default_logo.png",
					  						    "m"   => base_url()."themes/".Settings::get('theme')."/assets/images/default_logo.png",
					  						    "l"   => base_url()."themes/".Settings::get('theme')."/assets/images/default_logo.png",
					  						    "xl"  => base_url()."themes/".Settings::get('theme')."/assets/images/default_logo.png",
					  						    "xxl" => base_url()."themes/".Settings::get('theme')."/assets/images/default_logo.png"))
);

// User Model
// Defines the user values, you want to store.
// Those system-fields are mandatory:
//  - username
//  - password
//  - screen_name
//  - email
//  - id_group
//
// The Values of each field mean the following:
//  - rules : CI form validation rules (required, min_length, max_length, exact_length, etc)
//  - special_field : 'id_user' : auto-fills the user-id (for custom tables e.g.). Automatically restricted.
//  				  'restricted' : can't be edited (auto-fill only).
//  				  'checkbox' : checkbox post data are processed a bit differently. if you use the field in html as a checkbox, please specify this field as a checkbox field.
//  				  'picture' : pictures, that are uploaded will be treated differently. Look at $conf['usermanager_picture'].
//  - default_value : Default value of the field. Is also pushed to the templates. Overrides 'required' rule. False means no default value. Use 0 for SQL-False
//  - save : String which represents the table, where the data is stored. 'users' is ionize's default user table.
//           Don't alter the 'users' table, but create a new one in /modules/Usermanager/config.xml.
//           False means it won't be stored (e.g. for fields, which are used for matches).
$config['usermanager_user_model'] = array
(
	/* Default fields */
	'username' => array(
		'rules' => 'trim|required|min_length[4]|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users'
	),
	'password' => array(
		'rules' => 'trim|required|min_length[4]|matches[password2]|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users'
	),
	'screen_name' => array( /*used as surname in this example*/
		'rules' => 'trim|required|min_length[2]|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users'
	),
	'email' => array(
		'rules' => 'trim|required|min_length[5]|valid_email|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users'
	),
	'id_group' => array(
		'rules' => '',
		'special_field' => 'restricted',
		'default_value' => '4',
		'save' => 'users'
	),
	// Antispam field
	'ck' => array(
		'rules' => 'antispam',
		'special_field' => false,
		'default_value' => false,
		'save' => false
	),
	/* /Default fields */

	/* Match fields */
	'password2' => array(
		'rules' => 'trim|required|min_length[4]|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => false
	),
	/* /Match fields */

	/* Additional fields */
	/* id_user is mandatory for the usermanager_usermodel to work */
	'id_user' => array(
		'rules' => '',
		'special_field' => 'id_user',
		'default_value' => false,
		'save' => 'users_info'
	),
	'title' => array(
		'rules' => 'trim|min_length[1]|xss_clean',
		'special_field' => false,
		'default_value' => '0',
		'save' => 'users_info'
	),
	'company' => array(
		'rules' => 'trim|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users_info'
	),
	'street' => array(
		'rules' => 'trim|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users_info'
	),
	'housenumber' => array(
		'rules' => 'trim|xss_clean|numeric',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users_info'
	),
	'zip' => array(
		'rules' => 'trim|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users_info'
	),
	'city' => array(
		'rules' => 'trim|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users_info'
	),
	'logo' => array(
		'rules' => 'trim|xss_clean',
		'special_field' => 'picture',
		'default_value' => false,
		'save' => 'users_info'
	),
	'newsletter' => array(
		'rules' => 'trim|xss_clean|max_length[1]',
		'special_field' => 'checkbox',
		'default_value' => '1',
		'save' => 'users_info'
	),
	'infomails' => array(
		'rules' => 'trim|xss_clean|max_length[1]',
		'special_field' => 'checkbox',
		'default_value' => '1',
		'save' => 'users_info'
	),
	'terms' => array(
		'rules' => 'trim|required|xss_clean|max_length[1]',
		'special_field' => 'checkbox',
		'default_value' => '1',
		'save' => 'users_info'
	),
	'position' => array(
		'rules' => 'trim|xss_clean',
		'special_field' => false,
		'default_value' => '1',
		'save' => 'users_info'
	),
	'picture' => array(
		'rules' => 'trim|xss_clean',
		'special_field' => 'picture',
		'default_value' => false,
		'save' => 'users_info'
	),
	'about_me' => array(
		'rules' => 'xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users_info'
	),
	'my_references' => array(
		'rules' => 'xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users_info'
	),
	'contact_person' => array(
		'rules' => 'trim|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users_info'
	),
	'website' => array(
		'rules' => 'trim|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users_info'
	),
	'experience' => array(
		'rules' => 'xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users_info'
	),
	'company_profile' => array(
		'rules' => 'trim|xss_clean|max_length[1]',
		'special_field' => false,
		'default_value' => '0',
		'save' => 'users_info'
	),
	'twitter' => array(
		'rules' => 'trim|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users_info'
	),
	'facebook' => array(
		'rules' => 'trim|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users_info'
	),
	'xing' => array(
		'rules' => 'trim|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users_info'
	),
	'linkedin' => array(
		'rules' => 'trim|xss_clean',
		'special_field' => false,
		'default_value' => false,
		'save' => 'users_info'
	)
	/* /Additional fields */
);

// Login fields and rules
$config['usermanager_login_model'] = array(
	// Email or username depends on usermanager_email_as_username
	'email' => 'trim|required|min_length[5]|valid_email|xss_clean',
	'username' => 'trim|required|min_length[4]|xss_clean',
	'password' => 'trim|required|min_length[4]|xss_clean',
);
