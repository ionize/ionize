<?php

/**
 * User Class
 *
 * @package 	Ionize CMS
 * @subpackage 	User
 * @author 		Ionize Dev Team
 *				based on Martin Wernstahl <m4rw3r@gmail.com> work.
 */

namespace Ionize {

	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class User {

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
		private $user_default_role = '';
		private $user_pending_role = '';
		private $user_deactivated_role= '';
		private $user_banned_role = '';

		/**
		 * Verify user by email
		 * @notice Not yet implemented
		 * @var bool
		 */
		public $verify_user = TRUE;



		/**
		 * Sets how many seconds a confirmation code is valid
		 *
		 * @var string
		 */
		public $confirmation_code_lifetime = 86400; // 1 day


		/**
		 * The settings for the remember me feature.
		 *
		 * @var array
		 */
		public $remember = array(
			'on' 			=> TRUE,
			'duration' 		=> 604800, // 7 days
			'code' 			=> 'rememberconnect',
			'identity' 		=> 'rememberidentity'
			);
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
		 * Current logged in user.
		 *
		 * @var null
		 */
		protected $user = NULL;

		protected $role = NULL;

		/**
		 * Contains the User instance.
		 *
		 * @var User
		 */
		private static $instance;


		/**
		 * CodeIgniter instance.
		 *
		 * @var CI
		 */
		private static $ci;


		private $tracker = NULL;


		// --------------------------------------------------------------------


		/**
		 * Constructor
		 *
		 * Fetches the current user, if logged in or a remember me cookie exists.
		 * Also inits the internal objects.
		 *
		 */
		function __construct($config = array())
		{
			log_message('debug', "User Class Initialized");

			self::$instance =& $this;

			// Get CodeIgniter instance and load necessary libraries and helpers
			self::$ci =& get_instance();

			self::$ci->load->library('encrypt');

			if (function_exists('mcrypt_encrypt'))
			{
				self::$ci->encrypt->set_cipher(MCRYPT_BLOWFISH);
				self::$ci->encrypt->set_mode(MCRYPT_MODE_CFB);
			}

			// Session
			self::$ci->load->library('session');
			$this->session =& self::$ci->session;

			self::$ci->load->helper('url');
			self::$ci->load->config('user');

			self::$ci->lang->load('user');
			$this->lang =& self::$ci->lang;

            // Models
            self::$ci->load->model(
                array(
                    'user_model',
                    'role_model'
                ), '', TRUE);

			$this->model =& self::$ci->user_model;
			$this->role_model =& self::$ci->role_model;

			// load settings
			foreach($config as $key => $val)
			{
				$this->$key = $val;
			}

			$this->encryption_key = config_item('encryption_key');

			if($this->remember['on'])
				self::$ci->load->helper('cookie');

			$user_pk = $this->model->getPkName();

			// if a user is already logged in, load him
			if($this->session->userdata($user_pk) !== FALSE)
			{
				$this->user = $this->model->find_user(array($user_pk => $this->session->userdata($user_pk)));
			}
			// usage of remember-cookie is enabled
			elseif($this->remember['on']) {
				self::$ci->load->helper('cookie');
				$identity = get_cookie($this->remember['identity']);
				$code = get_cookie($this->remember['code']);

				// if we have a remember me cookie - load and process it
				if( $identity && $code) {
					//find the user with that identity AND remember code
					//in our case identity is "username"
					$user = $this->model->find_user(array("username" => $identity, "remember_code" => $code));

					//we found a user
					if( $user ) {
						//this is our user - log the user in
						$this->user = $user;

							// set session and last visit
							$this->session->set_userdata($user_pk, $this->user[$user_pk]);

							$this->model->update_last_visit($this->user);

							// refresh the remember me cookie
						$this->remember( $user[$user_pk] );
					} else {
						// user not found
						delete_cookie($this->remember['identity']);
						delete_cookie($this->remember['code']);
					}
				}
				else
				{
					// alert the server admin that we've received a tampered cookie
					log_message('error', "User Class: Tampered remember me cookie received from ip ".self::$ci->input->ip_address());

					// just delete his cookie, we're evil to all the hackers ;)
					delete_cookie($this->remember['identity']);
					delete_cookie($this->remember['code']);
				}
			}
			$this->get_role();
		}


		// --------------------------------------------------------------------


		/**
		 * Login a user.
		 *
		 * @param string|array  Array or string that identifies the user
		 *                      Like array('email' => 'the email') or just the email
		 *                      Can also contain the password ("password" as key)
		 * @param string        Password to check (can be omitted if password is
		 * 					    stored in $identification)
		 * @param bool			If to remember the user, to auto-login next time
		 * @return bool
		 */
		public function login($identification, $password = NULL, $remember = FALSE)
		{
			// if we have no password and an array, the password may be in the array
			if($password === NULL && is_array($identification))
			{
				// get the remember me value, if it is in the array
				if(isset($identification['remember']))
				{
					$remember = $identification['remember'];
					unset($identification['remember']);
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

			// Login Tracker
			if($this->enable_tracker === TRUE)
			{
				$tracker = $this->tracker();

				if($this->is_blocked())
				{
					list($key, $id) = each($identification);
					$this->increment_failures($key, $id);

					$this->error = $this->set_error_message('connect_blocked', (is_numeric($this->time_left()) ? 'in '.$this->time_left().' seconds.' : 'later.'));

					return FALSE;
				}
			}

			$user = $this->model->find_user($identification);
			//compute the password hash
			self::$ci =& get_instance();
			self::$ci->load->library('phpass');
			//we will not hash the password as we do not know the
			//salt used for the hash in the database
			//therefor "phpass->check" exists, it calculates the password hash
			//based on the salt in the password from the database



			// did we get a user, and does the passwords match?
			if($user != FALSE && self::$ci->phpass->check($password, $user['password']))
			{
				$this->user = $user;
				$this->get_role();

				// Set session
				$this->session->set_userdata($this->model->getPkName(), $user[$this->model->getPkName()]);

				// Update the last visit
				$this->model->update_last_visit($user);

				// Set the remember cookie
				if($remember) $this->remember( $user[$this->model->getPkName()] );

				// Event
				\Event::fire('User.login');

				// redirect to a previously blocked page, if it exists
				/*
				 * TODO : Find another way to do that
				 *
				if($this->login_redirect_to_blocked && $this->session->userdata('connect_blocked_url'))
				{
					// get and then clean
					$url = $this->session->userdata('connect_blocked_url');
					$this->session->unset_userdata('connect_blocked_url');

					// redirect
					redirect($url, 'location', 302);
				}
				*/
				return TRUE;
			}
			else
			{
				if($this->enable_tracker)
				{
					list($key, $id) = each($identification);
					$this->increment_failures($key, $id);
				}

				// Event
				\Event::fire('User.login.error');

				$this->error = $this->set_error_message('connect_login_failed');

				return FALSE;
			}
		}


		// --------------------------------------------------------------------


		/**
		 * Logout, destroys user data in session but does not destroy session.
		 *
		 * @param  bool  uri string to redirect to (Optional)
		 * @return void
		 *
		 */
		public function logout($redirect = FALSE)
		{
			$user_pk = $this->model->getPkName();

			$this->session->unset_userdata($user_pk);

			$this->user = $this->role = NULL;

			// Be sure this URL will be deleted
			$this->session->unset_userdata('connect_blocked_url');

			// also, wash away his stamp - so he cannot enter again without id
			self::$ci->load->helper('cookie');
			if(get_cookie($this->remember['code']))
				delete_cookie($this->remember['code']);
			if(get_cookie($this->remember['identity']))
				delete_cookie($this->remember['identity']);

			// Event
			\Event::fire('User.logout');

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
		public function remember()
		{
			$user_pk = $this->model->getPkName();

			if( ! $this->logged_in() OR ! $this->remember['on'])
				return FALSE;

			$user = $this->get_current_user();

			//generate a remembercode - stored in the database for later
			//comparison
			$remember_code = sha1($user["password"]);
			$this->model->update_user(array($user_pk=>$id,"remember_code"=>$remember_code));

			self::$ci->load->helper('cookie');
			//set new cookies for the current user
			set_cookie(array(
				'name'   => $this->remember_me['identity'],
				'value'  => $user["username"],
				'expire' => $this->remember_me['duration']
			));

			set_cookie(array(
				'name'   => $this->remember_me['code'],
				'value'  => $remember_code,
				'expire' => $this->remember_me['duration']
			));

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
			return ($this->user != NULL && isset($this->user[$this->model->getPkName()]));
		}


		// --------------------------------------------------------------------


		/**
		 * Get one user's field
		 *
		 * @param $key
		 *
		 * @return null
		 */
		public function get($key)
		{
			// User's key
			if ($this->user && isset($this->user[$key]))
				return $this->user[$key];

			// Role key
			if ($this->role && isset($this->role[$key]))
				return $this->role[$key];

			return NULL;
		}


		// --------------------------------------------------------------------


		/**
		 * Shortcut for get('id_user');
		 *
		 * @return null
		 */
		public function getId()
		{
			return $this->get('id_user');
		}


		// --------------------------------------------------------------------


		/**
		 * Returns the user's data array.
		 *
		 * @return null
		 */
		public function get_user()
		{
			return $this->user;
		}


		// --------------------------------------------------------------------


		/**
		 * Returns the user's role data array
		 * If not set, try to get the current user's role.
		 *
		 * @return null
		 */
		public function get_role()
		{
			if ( $this->user && ! $this->role)
			{
				$role = $this->role_model->get($this->user['id_role']);

				if ( ! empty($role))
					$this->role = $role;
			}
			else if ( ! $this->role)
			{
				$role = $this->role_model->get(array('role_code' => 'guest'));

				if ( ! empty($role))
					$this->role = $role;
			}
			return $this->role;
		}


		// --------------------------------------------------------------------


		/**
		 * Alias for has_role()
		 *
		 * @param string|array $role
		 *
		 * @return mixed
		 */
		public function is($role)
		{
			if (is_array($role))
			{

			}
			else
			{
				return $this->has_role($role);
			}
		}


		// --------------------------------------------------------------------


		/**
		 *
		 * @param  mixed  $roles	Role code or array of roles codes
		 * @return bool
		 */
		/**
		 * @param $roles
		 *
		 * @return bool
		 */
		public function is_not($roles)
		{
			if ( ! is_array($roles))
				$roles = array($roles);

			$is_not = TRUE;

			foreach ($roles as $role)
			{
				if ($this->has_role($role))
					$is_not = FALSE;
			}
			return $is_not;
		}


		// --------------------------------------------------------------------


		public function has_role($role)
		{
			if ($this->role)
			{
				if ($this->role['role_code'] == $role)
					return TRUE;

				if ($this->role['role_name'] == $role)
					return TRUE;
			}
			return FALSE;
		}


		// --------------------------------------------------------------------


		/**
		 * Register a user.
		 *
		 * @param array $user_data The data to register the user with
		 *
		 * @return bool
		 */
		public function register($user_data = array())
		{
			$user_pk = $this->model->pk_name();

			// need username and password to process further
			if( ! isset($user_data['username']) OR ! isset($user_data['email']) OR ! isset($user_data['password']))
//			if( isset($user_data['email']) OR ! isset($user_data['password']))
			{
				$this->error = $this->set_error_message('connect_missing_parameters', implode(', ', array_diff(array('username', 'email', 'password'), array_keys($user_data))));
				return FALSE;
			}

			// User doesn't exist : Create it
			if ( ! $this->model->find_user($user_data['email']))
			{
				// Set the user's group
				$role_code = $this->verify_user ? $this->user_pending_role : $this->user_default_role;

				/*
				 * Instead of using an encryption, we know hash the password and when
				 * logging in we compare the users password hash versus the database one
				 *
				 * Instead of using MD5/Sha... we use PHPass to make bruteforce
				 * attacking a bit harder.
				 *
				 * Attention: there is no need to use a "salt" as the library takes care of
				 * that itself.
				 */
				$cleanPassword = $user_data['password'];
				$user_data['password'] = $this->generate_hash( $user_data['password'] );

				//if activation is needed, store a activation code
				if($this->verify_user)
					$user_data['activation_code'] = $this->calc_user_confirmation_key( $user_data );
				else
					$user_data['activation_code'] = "";

				// User saved sucessfully?
				if($return = $this->model->save($user_data, $role_code))
				{
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
			if ( !empty($user_data['password']) )
				$user_data['password'] = $this->generate_hash($user_data['password']);
			elseif (isset($user_data['password']))
				unset($user_data['password']);

			if ( isset($user_data['id_user']))
			{
				// Try to find one user with the same email but different ID
				$db_user = $this->model->find_user($user_data['email']);

				if ( ! empty($db_user) && $db_user['id_user'] != $user_data['id_user'])
				{
					$this->error = $this->set_error_message('connect_user_already_exists');
				}
				else
				{
					$db_user = $this->model->find_user(array('id_user' =>$user_data['id_user']));

					$nb = $this->model->save($user_data);

					// Reset the current user
					if ($nb && $this->current_user)
					$this->user = $this->model->find_user($user_data['email']);

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
				return $this->model->delete($user['id_user']);
			}
			return FALSE;
		}


		// --------------------------------------------------------------------


		/**
		 * Activates one user
		 *
		 * @param  string  Email from the user to activate
		 * @param  string  Activation code
		 * @param  string  Role code, if any
		 * @return bool
		 *
		 */
		public function activate($email, $code, $role_code = '')
		{
			$user = $this->model->find_user($email);

			if($user && $code == $user["activation_code"])
			{
				$user_role = $this->role_model->get($user['id_role']);

				if ( ! in_array($user_role['role_code'], array($this->user_deactivated_role, $this->user_banned_role)))
				{
					$id_user = $user[$this->model->pk_name];

					$role_code = $role_code != '' ? $role_code : $this->user_default_role;

					return $this->model->set_role($id_user, $role_code);
				}
			}

			return FALSE;
		}

		// --------------------------------------------------------------------

		/**
		 * Resets one user's password
		 *
		 * A new password has to be provided in the case of further
		 * processing needs a clear password. If you do not provide
		 * one, the function generates one
		 *
		 * @param  string  The email of the user whose password to reset
		 * @param  string  The confirmation code
		 * @param  string  The new password
		 * @return array   array containing "password" and "result" ("OK", "ERROR", "ERROR_NOT_FOUND", "ERROR_CODE_TO_OLD")
		 */
		public function reset_password($email, $code, $new_password = "") {
			if( empty($email) OR empty($code) )
				return array("password"=>"", "result"=>"ERROR");

			//only give back users with the corresponding email AND code
			$user = $this->find_user(array('email' => $email, 'forgotten_password_code' => $code));

			if( $user ) {
				//check if the code is still valid (or to old)
				if( $this->confirmation_time_expired( $user["forgotten_password_time"] ) )
					return array("password"=>"", "result"=>"ERROR_CODE_TO_OLD");

				//if no password is given, we generate one
				if( empty($new_password) )
					$new_password = $this->get_random_password(8);

				//set the user password to a clear one instead of the hash
				$user['password'] = $new_password;

				//reset forgotten-data: invalidates further requests with same data
				$user['forgotten_password_code'] = sha1($this->get_random_password(16));
				$user['forgotten_password_time'] = "0";

				//finally set the new password in the users db entry
				if( $this->update($user) )
					return array("password"=>$new_password, "result"=>"OK");
			}

			//to get a better fitting error message we will check if the user even exists
			$user = $this->find_user(array('email' => $email));
			if( $user )
				return array("password"=>"", "result"=>"ERROR");
			else
				return array("password"=>"", "result"=>"ERROR_NOT_FOUND");
		}


		// --------------------------------------------------------------------


		/**
		 * Calculates a confirmation key for a certain user.
		 *
		 * Can be used for activation of accounts, confirmation of requests...
		 * But: take care that @param contains all needed fields!
		 *
		 * @param  array	User data
		 * @return string
		 */
		public function calc_user_confirmation_key($user)
		{
			//generate a random part of the key (repeated some times)
			$key = "";
			for($i=0;$i<25;$i++) { $key = sha1( $key.microtime() ); }

			//hardened hash generation, no salt needed (library takes care)
			//this time we use a short sha1-hash to avoid characters not allowed
			//in urls (we want to provide clickable urls instead of letting a user
			//put code into boxes)
			return sha1( $this->generate_hash( $key . $user['username'] . $user['email'] . $user['password'] ) );
		}

		// --------------------------------------------------------------------


		/**
		 * Checks whether the given datetime is "to old"
		 *
		 * If the confirmation lifetime is set to 0, codes wont expire
		 *
		 * @param  int	a time()-equivalent time
		 * @return bool
		 */
		public function confirmation_time_expired($time)
		{
			if( $this->confirmation_code_lifetime == 0 )
				return FALSE;

			return (time() + $this->confirmation_code_lifetime) < intval($time);
		}

		// --------------------------------------------------------------------

		/**
		 * Generates a hash of the given password.
		 *
		 * @param  string		The password to encrypt
		 * @return string		hash of the password
		 */
		public function generate_hash($password)
		{
			//hardened hash generation, no salt needed (library takes care)
			self::$ci->load->library('phpass');
			return self::$ci->phpass->hash($password);
		}

		// --------------------------------------------------------------------

		/**
		 * Check if one key of the user already exists.
		 *
		 * <code>
		 * // valitation usage:
		 * $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]|User::exists[username]');
		 * $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|User::exists[email]');
		 * </code>
		 *
		 * @param  string  The string to check
		 * @param  string  The type to check "username" or "email"
		 * @return bool
		 */
		public function exists($value, $key)
		{
			return $this->model->exists(array($key => $value));
		}


		// --------------------------------------------------------------------


		/**
		 * Restricts the access where it is called.
		 *
		 * If access is allowed, this function will return to let the script
		 * continue to execute. Otherwise it will call User::deny() to
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
		 * User()->restrict('administrators'); // restricts to administrators
		 * User()->restrict(array('admins', 'moderators')); // restricts to two groups
		 * User()->restrict(array('group' => 'admins', 'user' => 'johndoe')); // lets the user "johndoe" and all administrators access
		 * User()->restrict(array('group' => array('admins', 'moderators), 'user' => 'johndoe')); // lets "johndoe" and two groups access
		 * User()->restrict(array('user' => array('johndoe', 'johnsmith'))); // only gives access to "johndoe" and "johnsmith"
		 * User()->restrict(array('group' => 'admins', 'ip' => '127.0.0.1')); // restricts to localhost or the group admins
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
			// normalize:
			if( ! is_array($cond))
				$cond = array($cond);

			// again:
			if( ! isset($cond['role']) && ! isset($cond['user']) && ! isset($cond['ip']))
				$cond = array('role' => $cond[0]);

			// IP restriction
			if(isset($cond['ip']))
			{
				// Allow access
				if(in_array(self::$ci->input->ip_address(), (Array)$cond['ip']))
					return TRUE;

				// now we try with a slower one with support for wildcards
				$ip = explode('.', self::$ci->input->ip_address());

				if( ! empty($ip))
				{
					foreach((Array)$cond['ip'] as $to_match)
					{
						if(strpos($to_match, '*') === FALSE)
							continue;

						$segs = explode('.', $to_match);

						if(empty($segs))
							continue;

						foreach($ip as $i => $segment)
						{
							if($segment != $segs[$i] OR $segment != '*')
								continue;
						}

						return TRUE;
					}
				}
			}

			// No user : Deny
			if( is_null($this->user))
			{
				if($return)
					return FALSE;

				$this->deny($cond);
			}

			// VIP
			if(isset($cond['user']))
			{
				if(in_array($this->user['email'], (Array) $cond['user']))
					return TRUE;
			}

			// No role : Deny
			if (is_null($this->role))
			{
				if($return)
					return FALSE;

				$this->deny($cond);
			}

			// Role
			if(isset($cond['role']))
			{
				if (in_array($this->role['role_code'], $cond['role']))
					return TRUE;
			}

			// deny access
			if($return)
				return FALSE;

			$this->deny($cond);
		}


		// --------------------------------------------------------------------


		/**
		 * Denies the current page to be shown, performing a redirect or shows an error page.
		 *
		 */
		public function deny()
		{
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

						$this->session->set_flashdata(array($this->restrict_type_redirect['flash_var'] => sprintf($str, self::$ci->uri->uri_string())));
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
					self::$ci->output->set_status_header(403);
					self::$ci->output->set_output('');

					list($type, $value) = each($this->restrict_type_block);

					// what shall we do?
					switch($type)
					{
						// use a prefabricated sign?
						case 'view':
							self::$ci->load->view($value);
							break;

						// hire a painter?
						case 'lang':
							self::$ci->output->set_output($this->lang->line($value));
							break;

						// or just scribble something on the site with spraypaint?
						default:
							self::$ci->output->set_output($value);
					}

					// now get that forbidden sign up...
					self::$ci->output->_display();
			}

			// now everyone should get the **** out of here already!
			exit;
		}


		// --------------------------------------------------------------------

		public function disable_folder_protection()
		{
			$this->folder_protection = array();
		}

		// --------------------------------------------------------------------


		/**
		 * Calculates the activation key for a certain user.
		 *
		 * @param  array	User data
		 * @param  string	Role code
		 * @return string
		 */
		public function calc_activation_key($user, $role_code='')
		{
			return sha1(sha1($role_code .$user['email'] . $user['password']).sha1($user['salt']));
		}


		// --------------------------------------------------------------------


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
		 * Set the default role.
		 * Useful before creating a new user, to attach him to  defined group
		 *
		 * @param	string	$role_code	Role code
		 * @return 	void
		 *
		 */
		public function set_default_role($role_code)
		{
			$this->user_default_role = $role_code;
		}


		// --------------------------------------------------------------------


		/**
		 * Returns the tracker array for the current user.
		 *
		 * @return Tracker_record
		 */
		public function tracker()
		{
			if( is_null($this->tracker))
			{
				$this->tracker = array();

				// defaults
				$this->tracker['failures'] = 0;
				$this->tracker['first_time'] = time();

				// clean table, if the die wants
				srand(time());
				if((rand() % 100) < $this->tracker_cleaning_probability)
				{
					self::$ci->db->delete($this->tracker_table, array('first_time <' => time() - $this->tracker_clean_older_than));
				}

				// load data, if we have some
				$query = self::$ci->db->get_where($this->tracker_table, array('ip_address' => self::$ci->input->ip_address()), 1);

				$this->tracker = $query->num_rows() ? $query->row_array() : $this->tracker;
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
		 * @param  string
		 * @return void
		 */
		/**
		 * @param $key Id of the user (username/email)
		 * @param $id
		 */
		public function increment_failures($key, $id)
		{
			$this->tracker['failures'] += 1;

			$this->model->save_tracker($this->tracker);

			$val = log($this->tracker['failures'], 10);

			if($val > 0 && $val % 1 == 0)
			{
				log_message('error', 'User: Many tries to login with the identification '.$key.':"'.$id.'" from ip "'.self::$ci->input->ip_address().'", try no '.$this->tracker['failures']);
			}
		}


		// --------------------------------------------------------------------


		/**
		 * Returns true if the user has a too large failure count.
		 *
		 * @return bool
		 */
		public function is_blocked()
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
		 * Set error message, can have dynamic data passed in
		 *
		 * @param string $line_key
		 * @param string $args
		 *
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
		 * Return error message or error status (true / false)
		 *
		 * You get it in the views and controllers :
		 * echo User()->error();
		 *
		 * @return bool | string
		 */
		public function error()
		{
			return $this->error;
		}


		// --------------------------------------------------------------------

		/**
		 * Get the instance of the Lib
		 *
		 */
		public static function get_instance()
		{
			if( ! isset(self::$instance))
			{
				// no instance present, create a new one
				$config = array();

				// include config
				if(file_exists(APPPATH.'config/user.php'))
				{
					include APPPATH.'config/user.php';
				}

				new User($config);

				self::$ci->load->_ci_loaded_files[] = APPPATH.'core/User.php';
			}

			return self::$instance;
		}
	}
}

// --------------------------------------------------------------------

namespace {

	/**
	 * Returns the authentication object, short for User::get_instance().
	 *
	 * @return Ionize\User
	 */
	function User()
	{
		return Ionize\User::get_instance();
	}


	// --------------------------------------------------------------------


	/**
	 * Initialize User and run the folder protection.
	 *
	 * @return void
	 */
	function init_folder_protection()
	{
		$user = Ionize\User::get_instance();
		$router =& load_class('Router');

		$dir = trim($router->directory, ' /\\');

		if(isset($dir))
		{
			if(isset($user->folder_protection[$dir]))
			{
				// Add Role of the logged in user if the Role has access
				if ($user->logged_in() && Authority::can('access', $dir))
				{
					$role = $user->get_role();
					$user->folder_protection[$dir]['role'] = array($role['role_code']);
				}

				$user->restrict($user->folder_protection[$dir]);
			}
		}
	}
}
