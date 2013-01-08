<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * IonConnect Class
 *
 * @package 	Ionize CMS
 * @subpackage 	IonConnect
 * @author 		Ionize Dev Team
 *				based on Martin Wernstahl <m4rw3r@gmail.com> and Christophe Prudent <info@toopixel.ch> Connect lib
 */
class Connect {

	/**
	 * Key used to encrypt passwords.
	 *
	 * @var string
	 */
	protected $encryption_key;

	/**
	 * Group slug for the newly created users.
	 *
	 * @var string
	 */
	public $default_user_group = 'users';

	/**
	 * Group slug for the pending users.
	 *
	 * The pending group contains users which
	 * haven't yet been activated via e-mail.
	 *
	 * @var string
	 */
	public $pending_user_group = 'pending';

	/**
	 * Group slug for the banned users.
	 *
	 * @var string
	 */
	public $banned_user_group = 'banned';
	
	/**
	 * Verify user by email
	 * @notice Not yet implemented
	 * @var bool
	 */
	public $verify_user = TRUE;
	
	/**
	 * If to redirect the user to a page which previously
	 * was blocked for the user after a login.
	 * 
	 * Eg.
	 * A user tries to visit a blocked page, then he is
	 * redirected to the login screen. Then he logs in,
	 * after he is redirected to the page he previously tried
	 * to visit.
	 * 
	 * Note: Does only work with the block type "redirect"
	 * 
	 * @var bool
	 */
	public $login_redirect_to_blocked = TRUE;

	/**
	 * Sets how Connect will act in the event of a blocked user.
	 * 
	 * @var string
	 */
	protected $on_restrict = 'message';
	
	/**
	 * The settings for the remember me feature.
	 *
	 * @var array
	 */
	public $remember_me = array(
							'on' 			=> TRUE,
							'duration' 		=> 604800, // 7 days
							'cookie_name' 	=> 'rememberconnect');
	/**
	 * The settings for the folder protection.
	 *
	 * @var array
	 */
	public $folder_protection = array();

	/**
	 * If to use the login tracker.
	 *
	 * @var bool
	 */
	public $enable_tracker = TRUE;

	/**
	 * The table storing the access attempt data.
	 *
	 * @var string
	 */
	public $tracker_table = 'login_tracker';

	/**
	 * How the scaling of the mathematical blocking function should be.
	 *
	 * This value scales the curve in height.
	 * Larger value = longer wait times.
	 * You can test the values in the demo application, to see how it will
	 * affect users and bots.
	 *
	 * Expression:
	 * f^e * s > t
	 *
	 * f  = failures
	 * s  = severeness
	 * e  = exponent (>= 1)
	 * t  = time since first attempt
	 *
	 * If the expression evaluates to true, the user is blocked
	 *
	 * To calculate how much time it is left to the next allowed login attempt:
	 * x  = s * f^e - t - 1
	 *
	 *  s = severness
	 *  e = exponent
	 *  t = time since first attempt
	 *  f = failures
	 *  x = time left
	 *
	 * @var float
	 */
	public $blocking_severeness = 1.0;

	/**
	 * The exponent of the blocking function.
	 *
	 * This value controls the slope of the curve.
	 * Larger value = steeper curve and the user will be blocked faster.
	 *
	 * Must be >= 1
	 *
	 * @var float
	 */
	public $blocking_exponent	= 1.75;

	/**
	 * Probability the tracker cleans the table of unused data, percentage.
	 *
	 * @var float
	 */
	public $tracker_cleaning_probability = 5;

	/**
	 * All tracker data older than this will be deleted, seconds.
	 *
	 * @var int
	 */
	public $tracker_clean_older_than = 86400;

	/**
	 * The current error code issued.
	 *
	 * @var false|string
	 */
	public $error = FALSE;
	
	/**
	 * Contains the current logged in user.
	 *
	 * @var false|User_record
	 */
	protected $current_user = FALSE;

	/**
	 * Local cache for groups
	 * (avoids multiple DB queries)
	 *
	 * @var array
	 */
	protected $groups = array();

	/**
	 * Contains the Connect instance.
	 *
	 * @var Connect
	 */
	private static $instance;


	// --------------------------------------------------------------------


	/**
	 * Initializes the Connect library.
	 * 
	 * Fetches the current user, if logged in or a remember me cookie exists.
	 * Also inits the internal objects.
	 *
	 */
	function __construct($config = array())
	{
		self::$instance =& $this;

		log_message('debug', "Connect Class Initialized");
		
		// Get CodeIgniter instance and load necessary libraries and helpers
		$CI =& get_instance();

		$CI->load->library('encrypt');
		if (function_exists('mcrypt_encrypt'))
		{
			$CI->encrypt->set_cipher(MCRYPT_BLOWFISH);
			$CI->encrypt->set_mode(MCRYPT_MODE_CFB);
		}
		
		$CI->load->library('session');
		$CI->load->helper('url');
		$CI->load->config('connect');
		
		$CI->lang->load('connect');
		$this->lang =& $CI->lang;
		
		$CI->load->model('connect_model', 'model', TRUE);
		$this->model =& $CI->model;

		// load settings
		foreach($config as $key => $val)
		{
			$this->$key = $val;
		}

		// Connect uses first the key defined in config/connect.php and then the one defined in config/config.php
		$this->encryption_key = config_item('encryption_key');
		
		if($this->remember_me['on'])
		{
			$CI->load->helper('cookie');
		}
		
		$this->session =& $CI->session;
		
		$user_pk = $this->model->users_pk;

		// if a user is already logged in, load him
		if($this->session->userdata($user_pk) !== FALSE)
		{
			$this->current_user = $this->model->find_user(array($user_pk => $this->session->userdata($user_pk)));
		}
		
		// if we have a remember me cookie - try to load it
		elseif($this->remember_me['on'] && get_cookie($this->remember_me['cookie_name']))
		{
			$data = get_cookie($this->remember_me['cookie_name']);
			
			// extract the hash and the encrypted string
			$hash = substr($data, 0, 14) . substr($data, -14);
			$str = substr($data, 14, -14);
			
			// match the hash
			if($hash == base64_encode(sha1($str . strrev($this->encryption_key), TRUE)))
			{
				// decrypt
				$array = unserialize($CI->encrypt->decode($str));
				
				// finally, does the person "look" the same, and is his stamp still on his hand?
				if($array['ip'] == $CI->input->ip_address() && $array['expiry_date'] > mktime() && isset($array[$user_pk]))
				{
					// log the user in
					$this->current_user = $this->model->find_user(array($user_pk => $array[$user_pk]));
					
					// did we get him?
					if($this->current_user)
					{
						// set session and last visit
						$this->session->set_userdata($user_pk, $this->current_user[$user_pk]);
						
						$this->model->update_last_visit($this->current_user);
						
						// refresh the remember me cookie
						$this->remember_me();
					}
					else
					{
						// user does not exist, remove cookie
						delete_cookie($this->remember_me['cookie_name']);
					}
				}
			}
			else
			{
				// alert the server admin that we've received a tampered cookie
				log_message('error', "Connect Class: Tampered remember me cookie received from ip ".$CI->input->ip_address());
				
				// just delete his cookie, we're evil to all the hackers ;)
				delete_cookie($this->remember_me['cookie_name']);
			}
		}
	}


	// --------------------------------------------------------------------
	

	/**
	 * Return error message or error status (true / false)
	 *
	 * You get it in the views and controllers : 
	 * echo $this->connect->error();
	 *
	 * @return bool | string
	 */
	public function error()
	{
		return $this->error;
	}


	// --------------------------------------------------------------------


	/**
	 * Set error message, can have dynamic data passed in
	 *
	 * @params string | array
	 * @return string
	 */
	public function set_error_message($line_key = '', $args = '')
	{
		if( ! is_array($args))
	    {
	        $args = array($args);
	    }
		
	    $line_key = $this->lang->line($line_key);
	    $message = vsprintf($line_key, $args);
		
		return $message;
	}


	// --------------------------------------------------------------------


	/**
	 * Login a user.
	 *
	 * @param string|array  An array or string that identifies the user
	 *                      Like array('email' => 'the email') or just the username
	 *                      Can also contain the password ("password" as key)
	 * @param string        The password to check (can be omitted if password is
	 * 					    stored in $identification)
	 * @param bool			If to remember the user, to auto-login next time
	 * @return bool
	 */
	public function login($identification, $password = FALSE, $remember = FALSE)
	{
		// if we have no password and an array, the password may be in the array
		if($password === FALSE && is_array($identification))
		{
			// get the remember me value, if it is in the array
			if(isset($identification['remember_me']))
			{
				$remember = $identification['remember_me'];
				unset($identification['remember_me']);
			}
			
			// we need at least a password and then another key to filter by
			if(count($identification) > 1 && isset($identification['password']))
			{
				$password = $identification['password'];
				unset($identification['password']);
			}
			else
			{
				// no password, or not enough data				
				$this->error = $this->set_error_message('connect_missing_parameters', implode(' and ', array_diff(array('username', 'email'), array_keys($identification))));
				
				return FALSE;
			}
		}

		// Tracker
		if($this->enable_tracker === TRUE)
		{
			$tracker = $this->tracker();
			
			if($this->blocked())
			{
				list($key, $id) = each($identification);
				$this->increment_failures($key, $id);
				
				$this->error = $this->set_error_message('connect_blocked', (is_numeric($this->time_left()) ? 'in '.$this->time_left().' seconds.' : 'later.'));
				
				return FALSE;
			}
		}
		
		$user = $this->model->find_user($identification);

		// did we get a user, and does the passwords match?
		if($user != FALSE && $password == $this->decrypt($user['password'], $user))
		{
			$this->current_user = $user;

			// Set session
			$this->session->set_userdata($this->model->users_pk, $user[$this->model->users_pk]);

			// Update the last visit
			$this->model->update_last_visit($user);
			
			// Set the remember_me cookie
			if($remember)
			{
				$this->remember_me();
			}
			
			// redirect to a previously blocked page, if it exists
			if($this->login_redirect_to_blocked && $this->session->userdata('connect_blocked_url'))
			{
				// get and then clean
				$url = $this->session->userdata('connect_blocked_url');
				$this->session->unset_userdata('connect_blocked_url');
				
				// redirect
				redirect($url, 'location', 302);
			}
			return TRUE;
		}
		else
		{
			if($this->enable_tracker)
			{
				list($key, $id) = each($identification);
				$this->increment_failures($key, $id);
			}
			
			$this->error = $this->set_error_message('connect_login_failed');
			
			return FALSE;
		}
	}


	// --------------------------------------------------------------------


	/**
	 * Logout, destroys user data in session but does not destroy session.
	 *
	 * @param  string  uri string to redirect to (Optional)
	 * @return void
	 */
	public function logout($redirect = FALSE)
	{
		$user_pk = $this->model->users_pk;

		$this->session->unset_userdata($user_pk);

		$this->current_user = FALSE;

		// Be sure this URL will be deleted
		$this->session->unset_userdata('connect_blocked_url');

		// also, wash away his stamp - so he cannot enter again without id
		if($this->remember_me['on'])
		{
			delete_cookie($this->remember_me['cookie_name']);
		}

		if($redirect)
		{
        	redirect($redirect);
		}
	}


	// --------------------------------------------------------------------


	/**
	 * Remembers the currently logged in user.
	 * 
	 * @return bool
	 */
	public function remember_me()
	{
		$user_pk = $this->model->users_pk;
		
		if( ! $this->logged_in() OR ! $this->remember_me['on'])
		{
			return FALSE;
		}
		
		$CI =& get_instance();
		
		$user = $this->get_current_user();
		
		$str = array($user_pk => $user[$user_pk],
					 'ip' => $CI->input->ip_address(),
					 'expiry_date' => mktime() + $this->remember_me['duration']);

		$str = $CI->encrypt->encode(serialize($str));
		$hash = base64_encode(sha1($str . strrev($this->encryption_key), TRUE));
		
		$cookie = substr($hash, 0, 14) .$str. substr($hash, -14);
		
		set_cookie($this->remember_me['cookie_name'], $cookie, $this->remember_me['duration']);
		
		return TRUE;
	}


	// --------------------------------------------------------------------


	/**
	 * Check if the user is logged in
	 *
	 * @return 	bool
	 */
	public function logged_in()
	{
		return $this->current_user != FALSE && isset($this->current_user[$this->model->users_pk]);
	}


	// --------------------------------------------------------------------


	/**
	 * Restricts the access where it is called.
	 *
	 * If access is allowed, this function will return to let the script
	 * continue to execute. Otherwise it will call Connect::deny() to
	 * abort the execution.
	 * If $return is true, this method will return true if the user is
	 * granted access, otherwise false.
	 *
	 * Access by group:
	 * If a user belongs to the group which is needed for access, he is
	 * granted access. He is also granted access if he belongs to a
	 * group with a higher access level than the required group.
	 * On the other hand, if he belongs to a group which has the same
	 * access level or less than the required group, he is denied access.
	 *
	 * Access by user:
	 * This restricts access to the users specified, no other users can
	 * be granted access.
	 * But group access has precedence over user access so a user need
	 * to be either in the required group (or in a group with a higher
	 * access level) or have a username which matches the list specified
	 * in the call to restrict().
	 * You can look at access by user as an exception for certain users
	 * to access without the need to match the group rule.
	 *
	 * Access by ip:
	 * This restricts the access to a specific ip (* wildcards allowed).
	 * If the IP matches, access is allowed. But if access isn't allowed
	 * by ip, restrict() proceeds to match username and group conditions
	 * (if any).
	 *
	 * Example:
	 * <code>
	 * $this->connect->restrict('administrators'); // restricts to administrators
	 * $this->connect->restrict(array('admins', 'moderators')); // restricts to two groups
	 * $this->connect->restrict(array('group' => 'admins', 'user' => 'johndoe')); // lets the user "johndoe" and all administrators access
	 * $this->connect->restrict(array('group' => array('admins', 'moderators), 'user' => 'johndoe')); // lets "johndoe" and two groups access
	 * $this->connect->restrict(array('user' => array('johndoe', 'johnsmith'))); // only gives access to "johndoe" and "johnsmith"
	 * $this->connect->restrict(array('group' => 'admins', 'ip' => '127.0.0.1')); // restricts to localhost or the group admins
	 * </code>
	 *
	 * @param  mixed The condition to be met, the default search
	 * 				 condition is by group slug, so if this parameter
	 * 				 is a string, it will restrict by group.
	 * 				 On the other hand, if it is an array, this method
	 * 				 will restrict by group or by user which depends
	 * 				 on the keys used. The value in the array can be an array,
	 * 				 to restrict to multiple groups/users
	 * 				    No keys: match group(s) slug
	 * 				    user key: match by username(s)
	 * 				    group key: match by group(s)
	 * 				    ip key: match by ip(s)
	 * 				    both (or all three) keys: match by either group, ip or username
	 * @param  bool   If this method should return and let execution
	 * 				  continue even if access is denied
	 * @return bool
	 */
	public function restrict($cond = 'users', $return = FALSE)
	{
		$CI =& get_instance();

		// normalize:
		if( ! is_array($cond))
			$cond = array($cond);

		// again:
		if( ! isset($cond['group']) && ! isset($cond['user']) && ! isset($cond['ip']))
			$cond = array('group' => $cond[0]);

		if(isset($cond['ip']))
		{
			// let's use PHP's fast method
			if(in_array($CI->input->ip_address(), (Array)$cond['ip']))
				return TRUE;

			// now we try with a slower one with support for wildcards
			$ip = explode('.', $CI->input->ip_address());

			if( ! empty($ip))
			{
				foreach((Array)$cond['ip'] as $to_match)
				{
					if(strpos($to_match, '*') === FALSE)
						continue;

					$segs = explode('.', $to_match);

					if(empty($segs))
					{
						continue;
					}

					foreach($ip as $i => $segment)
					{
						if($segment != $segs[$i] OR $segment != '*')
						{
							continue;
						}
					}

					return TRUE;
				}
			}
		}

		if(($user = $this->get_current_user()) == FALSE)
		{
			// You have no identification, get lost!
			if($return)
				return FALSE;

			$this->deny($cond);
		}

		// check for VIP guests...
		if(isset($cond['user']))
		{
			if(in_array($user['username'], (Array) $cond['user']))
			{
				return TRUE;
			}
		}

		// Check for the usual visitors...
		if(isset($cond['group']))
		{
			// fetch asked group
			$q = $CI->db->where_in('slug', (Array) $cond['group'])->get($this->model->groups_table);
			$asked_group = $q->row_array();

			if(
				$user['group'][$this->model->groups_pk] == $asked_group[$this->model->groups_pk]
				OR $user['group']['level'] > $asked_group['level']
			)
			{
				return TRUE;
			}
		}

		// deny access
		if($return)
			return FALSE;

		$this->deny($cond);
	}


	// --------------------------------------------------------------------


	/**
	 * An alias for restrict($cond, true), so it will be syntactically nicer.
	 *
	 * Does not abort execution of the script.
	 *
	 * Example:
	 * if($this->connect->is('admin'))
	 *     echo "You are admin!";
	 *
	 * @param  mixed  The condition parameter which is sent to restrict()
	 * @return bool
	 */
	public function is($cond = 'users')
	{
		return $this->restrict($cond, TRUE);
	}


    // --------------------------------------------------------------------


	/**
	 * An inverted alias for restrict($cond, true).
	 *
	 * Does not abort execution of the script.
	 *
	 * Example:
	 * if($this->connect->is_not('admin'))
	 *     echo "You are not an admin!";
	 *
	 * @param  mixed  The condition parameter which is sent to restrict()
	 * @return bool
	 */
    public function is_not($cond = 'users')
	{
		return ( ! $this->restrict($cond, TRUE));
	}


	// --------------------------------------------------------------------


	public function find_user($user_data = array(), $with_group = TRUE)
	{
		return $this->model->find_user($user_data, $with_group);
	}


	// --------------------------------------------------------------------


	/**
	 * Returns the user based on his login name
	 *
	 * @param	string			User logon
	 * @returns array/false		Array of user data or false if no user was found.
	 */
	public function get_user($username)
	{
		$user = $this->model->find_user($username);

		if($user)
			return $user;
			
		return FALSE;
	}


	// --------------------------------------------------------------------


	/**
	 * Get the curent logged in user data.
	 *
	 * @return User_record
	 */
	public function get_current_user()
	{
		return $this->current_user;
	}


	// --------------------------------------------------------------------


	/**
	 * Returns the group with the specified identification.
	 *
	 * @param  mixed  Slug, id or an array
	 * @return Group_record
	 */
	public function get_group($id)
	{
		if( ! is_numeric($id) && ! is_array($id))
		{
			$id = array('slug' => $id);
		}

		return $this->model->find_group($id);
	}


	// --------------------------------------------------------------------


	/**
	 * Check if the username or the email is duplicate.
	 *
	 * <code>
	 * // valitation usage:
	 * $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]|Connect::check_duplicate[username]');
	 * $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|Connect::check_duplicate[email]');
	 * </code>
	 *
	 * @param  string  The string to check
	 * @param  string  The type to check "username" or "email"
	 * @return bool
	 */
	public function check_duplicate($str, $type)
	{
		return $this->model->check_duplicate($str, $type);
	}


	// --------------------------------------------------------------------


	/**
	 * Register a user.
	 *
	 * @param $data The data to register the user with
	 * @return bool
	 */
	public function register($user_data = array())
	{
		$user_pk = $this->model->users_pk;
		$group_pk = $this->model->groups_pk;
		
		// need username and password to process further
		if( ! isset($user_data['username']) OR ! isset($user_data['email']) OR ! isset($user_data['password']))
//		if( isset($user_data['email']) OR ! isset($user_data['password']))
		{
			$this->error = $this->set_error_message('connect_missing_parameters', implode(', ', array_diff(array('username', 'email', 'password'), array_keys($user_data))));

			return FALSE;
		}

		// @TODO: Make also that depending on the config file, if the login is done by the email, then it must check for duplicate too, 
		// anyway the form validation will also take care of that problem before the connect lib is called.
		if ( ! $this->model->find_user($user_data['username']))
//		if ( ! $this->model->find_user($user_data['email']))
		{
			// Set the salt
			if( ! isset($user_data['salt']))
			    $user_data['salt'] = $this->get_salt();
		
			// Set the user's group
			if($this->verify_user)
			{
				$group = $this->get_group($this->pending_user_group);
			    $user_data['id_group'] = $group['id_group'];
			}
			else
			{
				$group = $this->get_group($this->default_user_group);
			    $user_data['id_group'] = $group['id_group'];
			}
			
			// Encrypt the password and prepare data for inserting
			$user_data['password'] = $this->encrypt($user_data['password'], $user_data);

			// User saved sucessfully?
			if($return = $this->model->save_user($user_data))
			{
			    $this->error = $user_data['password'];
				
				return $return;
			}
			else
			{
			    $this->error = $this->set_error_message('connect_user_save_impossible');
			}
		}
		else
		{
			$this->error = $this->set_error_message('connect_user_already_exists');
		}

		return FALSE;
	}


	/**
	 * Updates one User
	 * The user can be different from the current connected user.
	 *
	 * Important :
	 * - Only data which should be updated and the id_user must be send.
	 *
	 * @param array $user_data
	 *
	 * @return bool
	 *
	 */
	public function update($user_data = array())
	{
		$clear_password = NULL;

		// Update the password : Send in "clear" version
		if ( ! empty($user_data['password']) OR $user_data['password'] != '')
		{
			$clear_password = $user_data['password'];
			$user_data['salt'] = $this->get_salt();
			$user_data['password'] = $this->encrypt($user_data['password'], $user_data);
		}
		elseif (isset($user_data['password']))
			unset($user_data['password']);

		if ( isset($user_data['id_user']))
		{
			// Try to find one user with the same username but different ID
			$db_user = $this->model->find_user($user_data['username']);

			if ( ! empty($db_user) && $db_user['id_user'] != $user_data['id_user'])
			{
				$this->error = $this->set_error_message('connect_user_already_exists');
			}
			else
			{
				$db_user = $this->model->find_user(array('id_user' =>$user_data['id_user']));

				// Username has changed : the password needs to be refreshed
				if ($user_data['username'] != $db_user['username'])
				{
					$user_data['salt'] = $this->get_salt();
					$password = ! is_null($clear_password) ? $clear_password : $this->decrypt($db_user['password'], $db_user);
					$user_data['password'] = $this->encrypt($password, $user_data);
				}

				$nb = $this->model->update_user($user_data);

				if ($nb && $this->current_user)
					$this->current_user = $this->model->find_user($user_data['username']);

				return $nb;
			}
		}

		return FALSE;
	}


	// --------------------------------------------------------------------

	public function delete($user_data = array())
	{
		$user = $this->model->find_user($user_data);

		if ($user)
		{
			return $this->model->delete_user($user['id_user']);
		}
		return FALSE;
	}


	// --------------------------------------------------------------------


	/**
	 * Activates one user
	 *
	 * @param  string  The user to activate
	 * @param  string  The activation code
	 * @return bool
	 */
	public function activate($username, $code)
	{
		$user = $this->model->find_user($username);

		if($user && $code == $this->calc_activation_key($user))
		{
			$g = $this->model->find_group(array('slug' => $this->default_user_group));
			
			// Activate the user only if he is not banned (Security against using the activation link to unban)
			if($user['group']['level'] < $g['level'] && $user['group']['level'] > 0)
			{
				return $this->model->set_group($user, $g[$this->model->groups_pk]);
			}
		}

		return FALSE;
	}


	// --------------------------------------------------------------------


	/**
	 * Calculates the activation key for a certain user.
	 *
	 * @param  array	User data
	 * @return string
	 */
	public function calc_activation_key($user)
	{
		return sha1(sha1($user['username'] . $user['email'] . $user['password']).sha1($user['salt']));
	}


    // --------------------------------------------------------------------


	/**
	 * Prepare and sends the mails.
	 *
	 * @NOTICE : Not more used. The Connnect lib should not send mail. This is the final website job !!!
	 *
	 *
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return bool
	 private function send_mail($to, $message, $subject = 'Your account')
	 {
		$this->ci->load->library('email');

        $this->ci->email->from($this->admin_email, $this->admin_from_name);
		$this->ci->email->to($to);
        $this->ci->email->subject($subject);

        $message = $this->ci->load->view('access/emails/layout', $message, true);

		$this->ci->email->message($message);

		return $this->ci->email->send();
	 }
	 */



	// --------------------------------------------------------------------


	/**
	 * Denies the current page to be shown, performing a redirect or shows an error page.
	 *
	 * @param mixed The condition needed, default: a user is required
	 *              This parameter is takes the same data as Connect:require()
	 */
	public function deny($required_cond = FALSE)
	{
		$CI =& get_instance();
	
		switch($this->on_restrict)
		{
			case 'redirect':
				if($this->restrict_type_redirect['flash_msg'] != FALSE)
				{
					if($this->restrict_type_redirect['flash_use_lang'])
					{
						$str = $this->lang->line($this->restrict_type_redirect['flash_msg']);
					}
					else
					{
						$str = $this->restrict_type_redirect['flash_msg'];
					}

					$this->session->set_flashdata(array($this->restrict_type_redirect['flash_var'] => sprintf($str, $CI->uri->uri_string())));
				}
				
				// set data to allow redirect on login
				if( ! $this->logged_in() && $this->login_redirect_to_blocked)
				{
					$this->session->set_userdata('connect_blocked_url', current_url());
				}
				
				redirect($this->restrict_type_redirect['uri']);
				
				break;

			case '404':
				show_404();
				
				break;

			default:
				
				// send header and clear output
				$CI->output->set_status_header(403);
				$CI->output->set_output('');
				
				list($type, $value) = each($this->restrict_type_block);
				
				// what shall we do?
				switch($type)
				{
					// use a prefabricated sign?
					case 'view':
						$CI->load->view($value);
					break;
					
					// hire a painter?
					case 'lang':
						$CI->output->set_output($this->lang->line($value));
					break;
					
					// or just scribble something on the site with spraypaint?
					default:
						$CI->output->set_output($value);
				}
				
				// now get that forbidden sign up...
				$CI->output->_display();
		}

		// now everyone should get the **** out of here already!
		exit;
	}


	// --------------------------------------------------------------------


	/**
	 * Encrypts the password.
	 *
	 * @param  string		The password to encrypt
	 * @param  array		User data array
	 * @return string		Encrypted password
	 */
	public function encrypt($password, $user)
	{
		$CI =& get_instance();

		$hash 	= $CI->encrypt->sha1($user['username'] . $user['salt']);
		$key 	= $CI->encrypt->sha1($this->encryption_key . $hash);

		return $CI->encrypt->encode($password, substr($key, 0, 56));
	}


	// --------------------------------------------------------------------

	/**
	 * Decrypts the password.
	 *
	 * @param  string		The encrypted password
	 * @param  array		User data array
	 * @return string		Decrypted password
	 */
	public function decrypt($password, $user)
	{
		$CI =& get_instance();

		$hash 	= $CI->encrypt->sha1($user['username'] . $user['salt']);
		$key 	= $CI->encrypt->sha1($this->encryption_key . $hash);

		return $CI->encrypt->decode($password, substr($key, 0, 56));
	}

	
	// --------------------------------------------------------------------


	/**
	 * Generates a random salt value.
	 *
	 * @return String	Hash value
	 *
	 **/	
	public function get_salt()
	{
		return substr(md5(uniqid(rand(), TRUE)), 0, $this->salt_length);
	}


	/**
	 * Generates a random password.
	 *
	 * @param	int		Password length
	 * @return	String	Password value
	 *
	 **/	
	public function get_random_password($size = 8)
	{
		$vowels = 'aeiouyAEIOUY';
		$consonants = 'bcdfghjklmnpqrstvwxzBCDFGHJKLMNPQRSTVWXZ1234567890@#$';
	 
		$key = '';

		$alt = time() % 2;
		
		for ($i = 0; $i < $size; $i++) {
			if ($alt == 1) {
				$key .= $consonants[rand() % strlen($consonants)];
				$alt = 0;
			} else {
				$key .= $vowels[rand() % strlen($vowels)];
				$alt = 1;
			}
		}
		return $key;
	}

	// --------------------------------------------------------------------


	/**
	 * Set the default group.
	 * Usefull before creating a new user, to attach him to  defined group
	 *
	 * @param	string	Group name
	 * @return 	void
	 */
	public function set_default_user_group($group)
	{
		$this->default_user_group = $group;
	}


	// --------------------------------------------------------------------


	/**
	 * Returns the tracker array for the current user.
	 *
	 * @return Tracker_record
	 */
	public function tracker()
	{
		$CI =& get_instance();
	
		if( ! isset($this->tracker))
		{
			$this->tracker = array();
			
			// defaults
			$this->tracker['failures'] = 0;
			$this->tracker['first_time'] = time();

			// clean table, if the die wants
			srand(time());
			if((rand() % 100) < $this->tracker_cleaning_probability)
			{
				$CI->db->delete($this->tracker_table, array('first_time <' => time() - $this->tracker_clean_older_than));
			}

			// load data, if we have some
			$q = $CI->db->get_where($this->tracker_table, array('ip_address' => $CI->input->ip_address()), 1);

			$this->tracker = $q->num_rows() ? $q->row_array() : $this->tracker;
		}

		return $this->tracker;
	}
	

	// --------------------------------------------------------------------


	/**
	 * Increases the failure count for the current user.
	 * 
	 * Every once in a while, a log message is issued with the username/email
	 * tried and from which ip.
	 * 
	 * @param  string The id of the user (username/email)
	 * @return void
	 */
	public function increment_failures($key, $id)
	{
		$CI =& get_instance();
		
		$this->tracker['failures'] += 1;

		$this->model->save_tracker($this->tracker);
		
		$val = log($this->tracker['failures'], 10);
		
		if($val > 0 && $val % 1 == 0)
		{
			log_message('error', 'Connect: Many tries to login with the identification '.$key.':"'.$id.'" from ip "'.$CI->input->ip_address().'", try no '.$this->tracker['failures']);
		}
	}
	
	
	// --------------------------------------------------------------------


	/**
	 * Returns true if the user has a too large failure count.
	 *
	 * @return bool
	 */
	public function blocked()
	{
		$sum = pow($this->tracker['failures'], $this->blocking_exponent) / (time() - $this->tracker['first_time'] + 1) * $this->blocking_severeness;

		if($sum > 1)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	
	// --------------------------------------------------------------------


	/**
	 * Returns how many seconds there are left until the user can try to login again.
	 *
	 * Ignores values <= 1, because the user takes some time to enter and submit the form.
	 *
	 * @return float seconds
	 */
	public function time_left()
	{
		$sum = $this->blocking_severeness * pow($this->tracker['failures'], $this->blocking_exponent) + $this->tracker['first_time'] - time() - 1;

		return $sum > 1 ? ceil($sum) : FALSE;
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Get the instance of Connect Lib
	 *
	 */
	public static function get_instance()
	{
		if( ! isset(self::$instance))
		{
			// no instance present, create a new one
			$config = array();
			
			// include config
			if(file_exists(APPPATH.'config/connect.php'))
			{
				include APPPATH.'config/connect.php';
			}
			
			$dummy = new Connect($config);

			// put it in the loader
			$CI =& get_instance();
			
			$CI->load->_ci_loaded_files[] = APPPATH.'libraries/Connect.php';
		}
		
		return self::$instance;
	}
	
} // End of Connect class


// --------------------------------------------------------------------


/**
 * Returns the authentication object, short for Connect::get_instance().
 *
 * @return Connect
 */
function Connect()
{
	return Connect::get_instance();
}


// --------------------------------------------------------------------


/**
 * Initialize Connect and run the folder protection.
 *
 * @return void
 */
function init_connect()
{
	// get the connect object and the router object
	$connect = Connect::get_instance();
	$router =& load_class('Router');

	$dir = trim($router->directory, ' /\\');

	if(isset($dir))
	{
		// We have a subdir
		if(isset($connect->folder_protection[$dir]))
		{
			// send restriction settings to Connect::restrict()
			$connect->restrict($connect->folder_protection[$dir]);
		}
	}
}


/* End of file Connect.php */
/* Location: ./application/libraries/Connect.php */