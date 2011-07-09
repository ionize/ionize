<?php
class Usermanager_Tags
{
	/**
	 * Returns the requested action
	 *
	 * @usage	<ion:usermanager [request="minilogin"] /> 	// Shows the minilogin view
	 *
	 * 									  "login" 			// Shows the login view
	 *
	 * 									  "register" 		// Shows the register view
	 *
	 * 									  "activate" 		// Shows the activation view and activates one account
	 *
	 * 									  "profile" 		// Shows the profile view
	 *
	 * 									  "user" 	[id_user="<int>"] [attr="is_editor"] 		[is_like="<1/0>"] // Container-tag. If true, the inner html/tags will be shown
	 * 									   			[id_user="<int>"] [attr="is_logged_in"] 	[is_like="<1/0>"] // Container-tag. If true, the inner html/tags will be shown
	 * 									  		 	[id_user="<int>"] [attr="get_picture"] 		[field="<string>"] 	[dimensions="<s/m/l/xl/../original>"] // Returns an URL to the picture of the field "field" with the specified dimension
	 * 									  			[id_user="<int>"] [attr="id_user"] 			[from_default_value="<1/0>"] [from_user_field="<1/0>"] [from_post_data="<form-name>"] [is_like="<string>"] 
	 * 									  			[id_user="<int>"] [attr="join_date"] 		[from_default_value="<1/0>"] [from_user_field="<1/0>"] [from_post_data="<form-name>"] [is_like="<string>"] 
	 * 									  			[id_user="<int>"] [attr="last_visit"] 		[from_default_value="<1/0>"] [from_user_field="<1/0>"] [from_post_data="<form-name>"] [is_like="<string>"] 
	 * 									  			[id_user="<int>"] [attr="username"] 		[from_default_value="<1/0>"] [from_user_field="<1/0>"] [from_post_data="<form-name>"] [is_like="<string>"] 
	 * 									  			[id_user="<int>"] [attr="screen_name"] 		[from_default_value="<1/0>"] [from_user_field="<1/0>"] [from_post_data="<form-name>"] [is_like="<string>"] 
	 * 									  			[id_user="<int>"] [attr="email"] 			[from_default_value="<1/0>"] [from_user_field="<1/0>"] [from_post_data="<form-name>"] [is_like="<string>"] 
	 * 									  			[id_user="<int>"] [attr="id_group"] 		[from_default_value="<1/0>"] [from_user_field="<1/0>"] [from_post_data="<form-name>"] [is_like="<string>"] 
	 * 									  			[id_user="<int>"] [attr="<custom field>"] 	[from_default_value="<1/0>"] [from_user_field="<1/0>"] [from_post_data="<form-name>"] [is_like="<string>"] 
	 * 												// The user-request gives you lots of opportunities to retrieve user and form information
	 * 												// id_user is the id of the user, you want to retreive information of. If it's not set, the current user is used.
	 * 												// Besides attr="is_editor" (which is a container-tag only), every attr="" is a user-field, which are defined under $config['usermanager_user_model'].
	 * 												// If is_like isn't set ...
	 * 												// - the tag will return the requested field given in attr="" (e.g. attr="screen_name", attr="username", ...).
	 * 												// - from_default_value, from_user_field and from_post_data defines, where the data is taken from.
	 * 												//   -> If from_default_data is "1", the requested field will be filled with the default data from the config file
	 * 												//      - e.g. <ion:usermanager request="user"  attr="id_group" from_default_value="1" /> will return "4".
	 * 												//   -> If from_user_field is "1", the requested field will be filled with the user information, that are in the database (if presend).
	 * 												//      - e.g. <ion:usermanager request="user" attr="username" from_user_field="1" /> will return the saved username ("Mario" in my case).
	 * 												//   -> If from_post_data is filled, the requested field will be filled with the information from $_POST-values.
	 * 												//      - e.g. <ion:usermanager request="user" attr="email" from_post_data="login" /> will return the given email from the form "login".
	 * 												//      - "login" in this case is the hidden field "form_name" in the specified forms.
	 * 												// - if from_default_value, from_user_field and from_post_data are all set, they'll overwrite themselves in the very same order:
	 * 												//   -> e.g. <ion:usermanager request="user" attr="email" from_default_value="1" from_user_field="1" from_post_data="profile_save" />
	 * 												//      - The returned data will be constructed like this:
	 * 												//        1. First it'll be filled with the default data (if given)
	 * 												//        2. Then it'll be overridden by the user-field from the database (if given. If not given, default_value will be kept)
	 * 												// 		  3. Then it'll be overridden by the post-data from the profile-form (if given. If not given, the previous filled data will be used.)
	 * 												//      - You can use them in any combination. They'll only override if data is given. So you can set from_user_field and from_post_data in order
	 * 												//        to display the user-fields if there is no post-data given (like for profile editing and other editing forms).
	 * 												// If is_like is set
	 * 												// - there will be no data returned. Then it's used as a container-tag.
	 * 												// - from_default_value, from_user_field, from_post_data can be used anyway
	 * 												// - e.g. <ion:usermanager request="user" attr="username" from_user_field="1" is_like="Foobar"><p>You've got a cool name!</p></ion:usermanager>
	 * 												//   -> It'll display "<p>You've got a cool name!</p>" if the current user's username is "Foobar".
	 * 												// - This is also useful for checkboxes, selects, radio buttons, etc
	 * 												//   -> <input type="checkbox" name="newsletter" <ion:usermanager request='user' attr='newsletter' from_default_value="1"
	 * 												//                     from_user_field='1' is_like='1'>CHECKED</ion:usermanager>>
	 *                                              // If html_encode is 1 and is_like isn't set, the output will be run through htmlentities. html_encode is 1 by default.
	 *
	 * 									  "global" 	[attr="admin_url"] 				// Returns an absolute URL to the admin interface
	 * 									  		 	[attr="profile_url"] 			// Returns an absolute URL to the current users profile
	 * 									  		 	[attr="register_url"] 			// Returns an absolute URL to the registration form
	 * 									  		 	[attr="login_url"] 				// Returns an absolute URL to the login form
	 * 									  		 	[attr="activation_url"] 		// Returns an absolute URL to the account activation page.
	 * 									  		 	[attr="url"] 					// Returns an absolute URL to itself (used for forms, as they always point to the same page)
	 * 									  		 	[attr="login_field_name"] 		// Returns the login-field-name/-id. Either E-Mail or Username, depending on your config
	 * 									  		 	[attr="login_field_label"] 		// Returns the login-field-label Either E-Mail or Username, depending on your config
	 * 									  		 	[attr="email_as_username"] 		// Container-tag. Content will be shown if $config['email_as_username'] is true
	 * 									  		 	[attr="not_email_as_username"] 	// Container-tag. Content will be shown if $config['email_as_username'] is false
	 *
	 * 									  "form" 	[attr="has_errors"] [form_name="<form_name>"] [is_like="<1/0>"] 	// Container-tag. If true, the inner html/tags will be shown. If form_name is set, it'll only check for errors for the specified form.
	 * 									  		 	[attr="error_string"] 	// Returns an error string if has_errors is true
	 * 									  			[attr="has_notices"] [form_name="<form_name>"] [is_like="<1/0>"] 	// Container-tag. If true, the inner html/tags will be shown. If form_name is set, it'll only check for errors for the specified form.
	 * 									  		 	[attr="notice_string"] 	// Returns a notice string if has_notices is true
	 * 									  			[attr="has_success"] [form_name="<form_name>"] [is_like="<1/0>"] 	// Container-tag. If true, the inner html/tags will be shown. If form_name is set, it'll only check for errors for the specified form. Is true if data was saved.
	 * 									  		 	[attr="success_string"] 	// Returns a success string if has_success is true
	 */

	public static function index(FTL_Binding $tag)
	{
		$ci =  &get_instance();
		if (!isset($ci->usermanager_action))
			$ci->load->library("usermanager_action");
		
		// "request" attribute must exists, else displays module help
		if (!array_key_exists("request", $tag->attr))
			return $ci->usermanager_action->help();

		$ci->usermanager_action->pseudo_construct($tag);

		switch ($tag->attr['request'])
		{
			case "load":
				return $ci->usermanager_action->load($tag);
				break;
			case "minilogin":
				return $ci->usermanager_action->minilogin($tag);
				break;
			case "login":
				return $ci->usermanager_action->login($tag);
				break;
			case "register":
				return $ci->usermanager_action->register($tag);
				break;
			case "profile":
				return $ci->usermanager_action->profile($tag);
				break;
			case "user":
				return $ci->usermanager_action->user($tag);
			case "global":
				return $ci->usermanager_action->globals($tag);
				break;
			case "form":
				return $ci->usermanager_action->form($tag);
				break;
			default:
				return $ci->usermanager_action->help($tag);
		}
	}
}
