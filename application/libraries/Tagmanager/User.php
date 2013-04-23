<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.9
 *
 */


/**
 * User TagManager
 *
 */
class TagManager_User extends TagManager
{
	/**
	 * TRUE if the form data were processed
	 * @var bool
	 */
	protected static $processed = FALSE;


	/**
	 * Stores the current user
	 *
	 * @var null
	 *
	 */
	protected static $user = NULL;


	/**
	 * Stores the current user's group
	 *
	 * @var null
	 *
	 */
	protected static $group = NULL;


	/**
	 * Only few tags are described here
	 * Common tags are set dynamically, from DB fields.
	 *
	 * @var array
	 */
	public static $tag_definitions = array
	(
		// User data
		'user' => 					'tag_user',
		'user:name' => 				'tag_user_name',
		'user:activation_key' =>	'tag_simple_value',
		'user:group' => 			'tag_user_group',
		'user:group:name' => 		'tag_user_group_name',
		'user:group:title' => 		'tag_user_group_title',

		// Expands the tag if the user is logged in
		'user:logged' =>		'tag_user_logged',
	);


	// ------------------------------------------------------------------------


	/**
	 * Parent <ion:user /> tag
	 *
	 * @param 	FTL_Binding
	 *
	 * @return 	string
	 *
	 */
	public static function tag_user(FTL_Binding $tag)
	{
		self::load_model('users_model');
		self::load_model('group_model');

		// Do these once
		if (self::$processed === FALSE)
		{
			// To avoid looping if process data calls "<ion:user />" again
			self::$processed = TRUE;

			// Set dynamics tags
			$user_fields = self::$ci->users_model->field_data();
			$group_fields = self::$ci->group_model->field_data();

			foreach($user_fields as $field => $info)
			{
				if (in_array($info['type'], array('date', 'datetime', 'timestamp')))
					self::$context->define_tag('user:' . $field, array(__CLASS__, 'tag_simple_date'));
				else
					self::$context->define_tag('user:' . $field, array(__CLASS__, 'tag_simple_value'));
			}
			// Group data are also available in the user array
			foreach($group_fields as $field => $info)
			{
				self::$context->define_tag('user:group:' . $field, array(__CLASS__, 'tag_simple_value'));
			}

			// Set the current user
			self::$user = Connect()->get_current_user();
		}

		// Do this every time the tag is called
		if (self::$user) {
			$tag->set('user', self::$user);
			$tag->set('group', self::$user['group']);
		}

		return $tag->expand();
	}


	// ------------------------------------------------------------------------


	/**
	 * Expands the children if the user is logged in.
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_user_logged(FTL_Binding $tag)
	{
		$tag->setAsProcessTag();

		$is = $tag->getAttribute('is');

		if (is_null($is)) $is = TRUE;

		if (Connect()->logged_in() == $is)
		{
			if (self::$trigger_else > 0)
				self::$trigger_else--;
			return $tag->expand();
		}
		else
		{
			self::$trigger_else++;
			return '';
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the found name of the user
	 * 1. Screen name if set
	 * 2. Firstname and Lastname if screen name not set
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_user_name(FTL_Binding $tag)
	{
		$value = $tag->getValue('screen_name');
		if (is_null($value) OR $value == '')
		{
			$value = $tag->getValue('firstname') . ' ' . $tag->getValue('lastname');
		}
		return self::output_value($tag, $value);
	}


	// ------------------------------------------------------------------------


	/**
	 * Current user's group
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_user_group(FTL_Binding $tag)
	{
		if (isset(self::$user['group']))
			$tag->set('group', self::$user['group']);

		return $tag->expand();
	}


	// ------------------------------------------------------------------------


	/**
	 * More logical key for 'slug' in group table.
	 * @TODO : Correct 'slug' in DB and replace it by 'name'
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_user_group_name(FTL_Binding $tag)
	{
		$name = $tag->getValue('slug', 'group');

		return self::output_value($tag, $name);
	}


	// ------------------------------------------------------------------------


	/**
	 * More logical key for 'group_name' in group table.
	 * @TODO : Correct 'group_name' in DB and replace it by 'title'
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_user_group_title(FTL_Binding $tag)
	{
		$group_name = $tag->getValue('group_name', 'group');

		return self::output_value($tag, $group_name);
	}


	// ------------------------------------------------------------------------


	/**
	 * Processes the form POST data.
	 * This method is declared as form "process" method in /application/config/forms.php for each form.
	 * We could declare one method / form, but we decided to process all user's form with this one.
	 *
	 * @param 	FTL_Binding		'init' tag (not the user one because this method is run before any tag parsing)
	 *							This tag is supposed to be only used to send Emails.
	 * 							With this tag, Emails views have access to the global tags, but not to any other
	 * 							object tag.
	 * @return 	void
	 *
	 */
	public static function process_data(FTL_Binding $tag)
	{
		$form_name = self::$ci->input->post('form');

		if ($form_name)
		{
			switch ($form_name)
			{
				// Logout
				case 'logout':

					if (Connect()->logged_in())
					{
						// Potentially redirect to the page setup in /application/config/forms.php
						$redirect = TagManager_Form::get_form_redirect();

						Connect()->logout($redirect);
					}
					break;

				// Login
				case 'login':

					if (TagManager_Form::validate('login'))
					{
						if ( ! Connect()->logged_in())
						{
							$email = self::$ci->input->post('email');
							$db_user = Connect()->find_user(array('email'=>$email));

							if ($db_user)
							{
								// Account not allowed to login
								if ($db_user['group']['level'] < 100)
								{
									$message = TagManager_Form::get_form_message('not_activated');
									TagManager_Form::set_additional_error('login', $message);
								}
								else
								{
									$user = array(
										'email' => $email,
										'password' => self::$ci->input->post('password')
									);

									$result = Connect()->login($user);

									if ($result)
									{
										// Potentially redirect to the page setup in /application/config/forms.php
										$redirect = TagManager_Form::get_form_redirect();
											if ($redirect !== FALSE) redirect($redirect);

										// If redirect is commented, this success message will be available.
										$message = TagManager_Form::get_form_message('success');
										TagManager_Form::set_additional_success('login', $message);
									}
									else
									{
										$message = TagManager_Form::get_form_message('error');
										TagManager_Form::set_additional_error('login', $message);
									}
								}
							}
							else
							{
								$message = TagManager_Form::get_form_message('not_found');
								TagManager_Form::set_additional_error('login', $message);
							}
						}
					}
					break;

				// Register
				case 'register':

					if (TagManager_Form::validate('register'))
					{
						// Get user's allowed fields
						$fields = TagManager_Form::get_form_fields('register');
						if ( is_null($fields))
							show_error('No definition for the form "register"');

						$fields = array_fill_keys($fields, FALSE);
						$user = array_merge($fields, self::$ci->input->post());

						// Compliant with Connect, based on username
						$user['username'] = $user['email'];
						$user['join_date'] = date('Y-m-d H:i:s');
						$cleanPassword = $user["password"];

						if ( ! Connect()->register($user))
						{
							$message = TagManager_Form::get_form_message('error');
							TagManager_Form::set_additional_error('register', $message);
						}
						else
						{
							// Get the user saved in DB
							$user = Connect()->get_user($user['username']);

							if (is_array($user))
							{
								// Create data array and Send Emails
								$data = array_merge($user, $user['group']);
								$data["password"] = $cleanPassword;
								TagManager_Email::send_form_emails($tag, 'password', $data);

								$message = TagManager_Form::get_form_message('success');
								TagManager_Form::set_additional_success('register', $message);

								// Potentially redirect to the page setup in /application/config/forms.php
								$redirect = TagManager_Form::get_form_redirect();
								if ($redirect !== FALSE) redirect($redirect);
							}
							else
							{
								$message = TagManager_Form::get_form_message('error');
								TagManager_Form::set_additional_error('register', $message);
							}
						}
					}
					break;

				// Someone requests the reset of the user's password
				case 'forgot_password_request':

					if (TagManager_Form::validate('forgot_password_request'))
					{
						$user = Connect()->find_user(array(
							'email' => self::$ci->input->post('email')
						));

						if ($user) {
							// generate a confirmation code which will be
							// emailed to the user.
							// function needs: username, email, password and id
							$confirmation_code = Connect()->calc_user_confirmation_key($user);

							//we do not want to update the password... remove it
							unset($user["password"]);

							//store the data within $user.
							//time can be used to give that code a lifetime...
							$user["forgotten_password_code"] = $confirmation_code;
							$user["forgotten_password_time"] = time();

							if ( ! Connect()->update($user))
							{
								$message = TagManager_Form::get_form_message('error');
								TagManager_Form::set_additional_error('forgot_password_request', $message);
							}
							else
							{
								//merge back group information for easier access
								$data = array_merge($user, $user['group']);
								// Send Emails
								TagManager_Email::send_form_emails($tag, 'forgot_password_request', $data);

								$message = TagManager_Form::get_form_message('success');
								TagManager_Form::set_additional_success('forgot_password_request', $message);

								// Potentially redirect to the page setup in /application/config/forms.php
								$redirect = TagManager_Form::get_form_redirect();
								if ($redirect !== FALSE) redirect($redirect);
							}
						}
						else
						{
							$message = TagManager_Form::get_form_message('not_found');
							TagManager_Form::set_additional_error('forgot_password_request', $message);
						}
					}

					break;

				//someone is confirming a forgotten password request
				case 'forgot_password_confirm':
					if (TagManager_Form::validate('forgot_password_confirm'))
					{
						//function returns an array with "result"-code and "password"
						$result = Connect()->reset_password(
										self::$ci->input->post('email'),
										self::$ci->input->post('forgotten_password_code')
									);
						switch(strtoupper($result["result"])) {
							case "OK":
										// Get the user again so emails can contain that data
										$user = Connect()->find_user( array("email"=>self::$ci->input->post('email')) );
										// Put the clear password to the user's data, for the email
										$user['password'] = $result["password"];

										// Send Emails
										$data = array_merge($user, $user['group']);
										TagManager_Email::send_form_emails($tag, 'forgot_password_confirm', $data);

										$message = TagManager_Form::get_form_message('success');
										TagManager_Form::set_additional_success('forgot_password_confirm', $message);
										TagManager_Form::set_additional_success('password', $result["password"]);

										// Potentially redirect to the page setup in /application/config/forms.php
										$redirect = TagManager_Form::get_form_redirect();
										if ($redirect !== FALSE) redirect($redirect);
										break;
							case "ERROR":
										$message = TagManager_Form::get_form_message('error');
										TagManager_Form::set_additional_error('forgot_password_confirm', $message);
										break;
							//the code is no longer valid (to old)
							case "ERROR_CODE_TO_OLD":
										$message = TagManager_Form::get_form_message('to_old');
										TagManager_Form::set_additional_error('forgot_password_confirm', $message);
										break;
							//no user was found for that is no longer valid (to old)
							case "ERROR_NOT_FOUND":
										$message = TagManager_Form::get_form_message('not_found');
										TagManager_Form::set_additional_error('forgot_password_confirm', $message);
										break;
						}
					}

					break;

				// Activate account
				case 'activation':

					// Done through one old plain CI controller for the moment.
					// Adding tags for this task adds more complexity for nothing
					// (create one page, set the page in Ionize... this all is not needed for account activation)
					break;

				// Save profile
				case 'profile':

					// Lost connection
					if(($current_user = Connect()->get_current_user()) == FALSE)
					{
						$message = TagManager_Form::get_form_message('not_logged');
						TagManager_Form::set_additional_error('profile', $message);
						return FALSE;
					}

					// Delete the profile
					if (self::$ci->input->post('delete'))
					{
						$result = Connect()->delete($current_user);

						$message = TagManager_Form::get_form_message('deleted');
						TagManager_Form::set_additional_success('profile', $message);

						// Potentially redirect to the page setup in /application/config/forms.php
						$redirect = TagManager_Form::get_form_redirect();
						Connect()->logout($redirect);
					}
					else
					{
						if (TagManager_Form::validate('profile'))
						{
							$fields = TagManager_Form::get_form_fields('profile');
							if ( is_null($fields))
								show_error('No definition for the form "profile"');

							$fields = array_fill_keys($fields, FALSE);
							$user = array_merge($fields, self::$ci->input->post());

							// Compliant with Connect, based on username
							$user['username'] = $user['email'];
							$user['id_user'] = $current_user['id_user'];

							// Checkboxes and multiselect
							foreach($user as $key => $data)
							{
								if (is_array($data))
									$user[$key] = implode(',', $data);
							}

							$result = Connect()->update($user);

							// If error here, it can only be on the email, which already exists in the DB
							if ( ! $result)
							{
								$message = TagManager_Form::get_form_message('error');
								TagManager_Form::set_additional_error('email', $message);
							}
							else
							{
								$message = TagManager_Form::get_form_message('success');
								TagManager_Form::set_additional_success('profile', $message);

								// Potentially redirect to the page setup in /application/config/forms.php
								$redirect = TagManager_Form::get_form_redirect();
								if ($redirect !== FALSE) redirect($redirect);
							}
						}
					}
					break;
			}
		}
	}
}