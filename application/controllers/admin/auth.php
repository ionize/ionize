<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 * Auth Controller
 * User authentication
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

class Auth extends My_Admin
{
	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		parent::__construct();

		// Reset the restriction
		User()->disable_folder_protection();

		// Disable xhr protection on index : let the desktop load
		$this->disable_xhr_protection();
	}


	// ------------------------------------------------------------------------


	public function index()
	{
		$this->login();
	}


	// ------------------------------------------------------------------------


	/**
	 * Logs one user on the admin panel
	 *
	 */
	public function login()
	{
		$default_admin_lang = Settings::get_default_admin_lang();

		// TODO :
		// - Replace by : config_item('uri_lang_code');
		// - Remove / Rewrite Settings::get_uri_lang()
		$uri_lang = Settings::get_uri_lang();

		$error = NULL;

		// If the user is already logged and if he is in the correct minimum group, go to Admin
		if(User()->logged_in() && Authority::can('access', 'admin'))
		{
			redirect(base_url().$uri_lang.'/'.config_item('admin_url'));
		}

		if(User()->logged_in() && ! Authority::can('access', 'admin'))
		{
			User()->logout();
			$error = lang('ionize_login_error_no_access');
		}

		if (is_null($error))
		{
			if( ! empty($_POST))
			{
				unset($_POST['submit']);

				if($this->_try_validate_login())
				{
					// User can log with email OR username
					if (strpos($_POST['username'], '@') !== FALSE)
					{
						$email = $_POST['username'];
						unset($_POST['username']);
						$_POST['email'] = $email;
					}

					try
					{
						User()->login($_POST);
						redirect(base_url().$uri_lang.'/'.config_item('admin_url').'/auth/login');
					}
					catch (Exception $e)
					{
						$error = $e->getMessage();
					}
				}
				else
				{
					$error = lang('ionize_login_error');
				}
			}
			else
			{
				if ($this->is_xhr())
				{
					$html = '
						<script type="text/javascript">
							var url = "'.config_item('admin_url').'";
							top.location.href = url;
						</script>';
					echo $html;
					exit();
				}
				else if (!in_array($uri_lang, Settings::get('displayed_admin_languages')) OR $uri_lang != $default_admin_lang)
				{
					redirect(base_url().$default_admin_lang.'/'.config_item('admin_url').'/auth/login');
				}
			}
		}

		$this->template['error'] = $error;
		$this->template['background_pictures'] = $this->_get_login_background_pictures();

		$this->output('auth/login');
	}


	// ------------------------------------------------------------------------


	public function xhr_login()
	{
		$user = $this->input->post('username');
		$pass = $this->input->post('password');

		if($this->_try_validate_login())
		{
			$data = array('password' => $pass);

			// User can log with email OR username
			if (strpos($user, '@') !== FALSE)
				$data['email'] = $user;
			else
				$data['username'] = $user;

			try
			{
				if (User()->login($data))
					$this->success(lang('ionize_message_operation_ok'));
				else
					$this->error(lang('ionize_login_error'));
			}
			catch(Exception $e)
			{
				$this->error($e->getMessage());
			}
		}
		else
		{
			$this->error(lang('ionize_login_error'));
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Logout and redirect to the welcome controller.
	 *
	 */
	public function logout()
	{
		if ( ! empty($_SESSION))
		{
			// Delete the session
			session_unset('isLoggedIn');
			if ($this->_is_session_started())
				session_destroy();
		}
		unset($_SESSION);

		// Here is also the right place to set a flash message or send
		// a screen message to the user if you use the redirect feature.

		$default_admin_lang = Settings::get_default_admin_lang();

		User()->logout(base_url().$default_admin_lang.'/'.config_item('admin_url'));
	}


	// ------------------------------------------------------------------------


	/**
	 * Try to validate the user login form
	 *
	 */
	private function _try_validate_login()
	{
		$this->load->library('form_validation');

		$rules = array(
			array(
				'field'   => 'username',
				'label'   => 'Username',
				'rules'   => 'trim|required|xss_clean'
			),
			array(
				'field'   => 'password',
				'label'   => 'Password',
				'rules'   => 'trim|required|min_length[4]|xss_clean'
			)
		);

		$this->form_validation->set_rules($rules);

		return ($this->form_validation->run() === TRUE);
	}

	private function _is_session_started()
	{
		if ( version_compare(phpversion(), '5.4.0', '>=') ) {
			return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
		} else {
			return session_id() === '' ? FALSE : TRUE;
		}

		return FALSE;
	}

	private function _get_login_background_pictures()
	{
		if ( ! is_dir(DOCPATH.'files/login_background/'))
			return array();

		$files = @scandir(DOCPATH.'files/login_background/');
		$files = is_array($files) ? array_diff($files, array('..', '.')) : array();

		if ( ! empty($files))
		{
			array_walk($files, function(&$value, $key) {
				$value = base_url(). 'files/login_background/' . $value;
			});

			$files = array_values($files);
		}
		else
		{
			$files = array();
		}

		return $files;
	}
}
