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
	 *
	 * @var null
	 */
	// protected static $user = NULL;

	/**
	 * @var bool
	 */
	protected static $processed = FALSE;

	protected static $got_user = FALSE;

	public static $tag_definitions = array
	(
		// Basic data
		'user' => 				'tag_user',
		'user:name' => 			'tag_user_name',
		'user:join_date' =>		'tag_simple_date',
		'user:last_visit' =>	'tag_simple_date',
		'user:email' => 		'tag_simple_value',
		'user:firstname' => 	'tag_simple_value',
		'user:lastname' => 		'tag_simple_value',
		'user:gender' => 		'tag_simple_value',
		'user:birth_date' => 	'tag_simple_value',

		// Advanced
		/*
		 * User should be able to :
		 * - login / logout
		 * - register
		 *
		 */
		'user:login' =>			'tag_user_login',
		'user:register' =>		'tag_user_register',
		'user:logged' =>		'tag_user_logged',

	);


	/**
	 * @param 	FTL_Binding
	 *
	 * @return 	string
	 *
	 */
	public static function tag_user(FTL_Binding $tag)
	{
		self::load_model('users_model');

		// Processes Form data
		if (self::$processed === FALSE)
		{
			self::process_data($tag);
			self::$processed = TRUE;
		}

		// Get the current user : Only once
		if (self::$got_user === FALSE)
		{
			$user = Connect()->get_current_user();

			if ($user)
				$tag->set('user', $user);

			self::$got_user = TRUE;
		}

		return self::wrap($tag, $tag->expand());
	}


	public static function tag_user_login(FTL_Binding $tag)
	{
	}


	public static function tag_user_logged(FTL_Binding $tag)
	{
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
	 * Returns the screen name of the user (complete name)
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_user_name(FTL_Binding $tag)
	{
		return self::output_value($tag, $tag->getValue('screen_name'));
	}



	protected function process_data(FTL_Binding $tag)
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
						Connect()->logout();
						$tag->remove('user');
					}
					break;

				// Login
				case 'login':
					if ( ! Connect()->logged_in())
					{
						$user = array(
							'email' => self::$ci->input->post('email'),
							'password' => self::$ci->input->post('password')
						);
						Connect()->login($user);
					}
					break;

				// Register
				case 'register':

					// Get user's allowed fields
					$fields = TagManager_Form::get_form_fields('register');
					if ( is_null($fields))
						show_error('No definition for the form "register"');

					$fields = array_fill_keys($fields, FALSE);
					$user = array_merge($fields, self::$ci->input->post());

					// Compliant with Connect, based on username
					$user['username'] = $user['email'];
					$user['join_date'] = date('Y-m-d H:i:s');

//					if ( ! Connect()->register($user))
//					{
//						trace(Connect()->error);
//					}
//					else
//					{
//						$user = Connect()->get_user($user['username']);

//						$activation_key = Connect()->calc_activation_key($user);
//trace('activaion key :' . $activation_key);
						// Send Emails
						$emails = TagManager_Form::get_form_emails('register');
						$website_email = Settings::get('site_email') ? Settings::get('site_email') : NULL;

						foreach($emails as $email_setting)
						{
trace($email_setting);
							$email = $email_setting['email'];

							// Get potential website / user email
							switch($email)
							{
								case 'website':
									$email = (Settings::get('site_email') != '') ? Settings::get('site_email') : NULL;
									break;

								case 'user':
									$email = $user['email'];
									break;
							}

							// Send the email
							if ( ! is_null($email))
							{
								// Email Lib
								if ( ! isset(self::$ci->email)) self::$ci->load->library('email');

								// Subject / From / To
								self::$ci->email->subject(lang($email_setting['subject']));
								self::$ci->email->from($website_email, Settings::get("site_title"));
								self::$ci->email->to($email);

								// View
								$view_file = Theme::load($email_setting['view']);
trace($view_file);

							}
						}
//					}
					break;

				// Get password back
				case 'password_back':
					break;

				// Activate account
				case 'activation':
					break;

				// Save profile
				case 'profile':
					break;
			}
		}
		trace('post');

		$post = self::$ci->input->post();

		trace($post);
	}


	/**
	 * Returns the form fields as defined in $config['forms'][$form_name]
	 * If no definition, returns NULL
	 *
	 * @param string
	 * @param bool
	 *
	 * @return array|null
	 *
	 */
	protected function _get_user_model_fields($form_name, $all = FALSE)
	{
		$forms = config_item('forms');
		$form = isset($forms[$form_name]) ? $forms[$form_name] : NULL;

		$fields = array();

		if (is_null($form)) return NULL;

		if ($all == TRUE)
			$fields = array_keys($form['fields']);
		else
		{
			foreach ($form['fields'] as $key => $field)
				if (!isset($field['save']) OR $field['save'] != FALSE)
					$fields[] = $key;
		}
		if (empty($fields))
			return NULL;

		return $fields;
	}
}