<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize User Controller
 * Used to login / logout an user
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	User
 * @author		Ionize Dev Team
 *
 */

class User extends My_Admin 
{
	
	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		parent::__construct();
		
		// Reset the restrict array for this constructor to avoid the loop
		$this->connect->folder_protection = array();
		// Could also be written :
		// Connect()->folder_protection = array();
	}


	// ------------------------------------------------------------------------

	/**
	 * Default
	 *
	 */
	function index()
	{
        // By default, the controller will send the user to the login screen
		$this->login();
	}


	// ------------------------------------------------------------------------


	/**
	 * Logs one user on the admin panel
	 *
	 */
	function login()
	{
		$default_admin_lang = Settings::get_default_admin_lang();
		
		$uri_lang = Settings::get_uri_lang();

		// If the user is already logged and if he is in the correct minimum group, go to Admin
		if($this->connect->logged_in() && $this->connect->is('editors', true))
		{
			redirect(base_url().$uri_lang.'/'.config_item('admin_url'));
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
					$this->connect->login($_POST);
					redirect(base_url().$uri_lang.'/'.config_item('admin_url'));
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
			if ( ! in_array($uri_lang, Settings::get('displayed_admin_languages')) OR $uri_lang != $default_admin_lang)
			{
				redirect(base_url().$default_admin_lang.'/'.config_item('admin_url').'/user/login');
			}
		}

		$this->output('access/login');
	}


	// ------------------------------------------------------------------------


	/**
	 * Logout and redirect to the welcome controller.
	 *
	 */
	function logout()
	{
		// Delete the session
		session_unset('isLoggedIn');	
		session_destroy();
		
		unset($_SESSION);

        // Here is also the right place to set a flash message or send
        // a screen message to the user if you use the redirect feature.

		$default_admin_lang = Settings::get_default_admin_lang();
		
//		$uri_lang = Settings::get_uri_lang();

    	$this->connect->logout(base_url().$default_admin_lang.'/'.config_item('admin_url'));
	}


	// ------------------------------------------------------------------------


	/**
	 * Try to validate the user login form
	 *
	 */
	function _try_validate_login()
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

		return ($this->form_validation->run() === true);
	}
	
}

/* End of file user.php */
/* Location: ./application/controllers/admin/user.php */