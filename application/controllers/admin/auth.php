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

		// If the user is already logged and if he is in the correct minimum group, go to Admin
		if(User()->logged_in() && Authority::can('access', 'admin'))
		{
			redirect(base_url().$uri_lang.'/'.config_item('admin_url'));
		}

		if(User()->logged_in() && ! Authority::can('access', 'admin'))
		{
			redirect(base_url());
		}

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
				catch(Exception $e)
				{
					$this->login_errors = $e->getMessage();
				}
			}
			else
			{
				$this->login_errors = lang('ionize_login_error');
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
					</script>'
				;
				echo $html;
				exit();
				/*
				// Save options : as callback
								$this->callback[] = array(
					'fn' => 'ION.reload',
					'args' => array('url'=> config_item('admin_url'))
				);
				$this->response();
				*/
			}
			else if ( ! in_array($uri_lang, Settings::get('displayed_admin_languages')) OR $uri_lang != $default_admin_lang)
			{
				redirect(base_url().$default_admin_lang.'/'.config_item('admin_url').'/auth/login');
			}
		}

		$this->output('auth/login');
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
				'rules'   => 'trim|required|min_length[4]|xss_clean'
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
}
