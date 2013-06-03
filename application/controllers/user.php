<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends Base_Controller {

	/**
	 * Group ID to put the activated users in.
	 *
	 * @var int
	 *
	 */
	protected $activation_group_id = 4;


	function __construct()
	{
		parent::__construct();

		/*
		 * to avoid the loop, we have to reset the restrict array in the constructor
		 * Access()->somevar = array();
		 * somevar is the same as the configuration option
		 */
		User()->folder_protection = array();

		$this->load->library('form_validation');
		
		// Set individual errors delimiters to nothing
		$this->form_validation->set_error_delimiters('','');
	}


	// ------------------------------------------------------------------------


	/**
	 * By default, the controller will send the user to the login screen
	 *
	 */
	public function index()
	{
		echo ('user');

		// $this->login();
	}


	// ------------------------------------------------------------------------


	/**
	 * Activates one user account
	 *
	 */
	public function activate($email, $activation_key)
	{
		$result = User()->activate($email, $activation_key);

		if ( ! $result)
		{
			/*
			 * To debug activation
			 *
			$user = Connect()->find_user($email);
			trace($user);
			trace('Received : ' . $activation_key);
			trace('Calculated : ' . User()->calc_user_confirmation_key($user));
			 */

			echo ('Activation code not valid.');
		}
		else
		{
			echo 'User activated';
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Confirms the request of a user to get a new password
	 *
	 * this is a simple "sample" output, if you want it prettified,
	 * use a special users-page in your theme which uses the form-tags
	 * and their handlers.
	 *
	 */
	public function forgot_password_confirm($email="", $confirmation_code="")
	{
		$result = User()->reset_password($email, $confirmation_code);
		if($result["result"] == "OK") {
			print "Passwort successfully reset to: ".$result["password"];
			return TRUE;
		}

		print "Passwort reset failed.";
		return FALSE;
	}

	// ------------------------------------------------------------------------


	/**
	 * Logs one user
	 * TODO : To rewrite.
	 * The main idea is it is able to log one user through Ajax.
	 *
	function login()
	{
		if( ! empty($_POST))
		{
			if($this->_try_validate_login())
			{
				// Deleting vars not present in the "users" table (Access lib)
				unset($_POST['submit']);
				unset($_POST['check']);

                // Syntax talks from itself, isn't it? :)
                // The login method will check for a 'remember_me' value
                // If found it will remember the user until he log out.
                // Remember time is specified time in the access config file (default is 7 days)
				try
				{
					Connect()->login($_POST);
				}
				catch(Exception $e)
				{
					// Put the validation_errors string message to the flash session
					$this->session->set_flashdata('validation_errors', $e->getMessage());

					// Put the CodeIgniter validation_errors string message to the flash session
					$this->session->set_flashdata('field_data', $this->form_validation->_field_data);
				}
			}
			else
			{
				// Put the validation_errors string message to the flash session
				$this->session->set_flashdata('validation_errors', $this->form_validation->error_string());
			
				// Put the CodeIgniter form field data array to the flash session
				$this->session->set_flashdata('field_data', $this->form_validation->_field_data);
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	*/


	// ------------------------------------------------------------------------


	/**
	 * Logout the user and redirect to referer URL
	 *
	 */
	function logout()
	{
		Connect()->logout(base_url().Settings::get_lang());   	
 	}



	// ------------------------------------------------------------------------


	private function _try_validate_login()
	{
		/*
		 * TODO :
		 * Should get the rules from "login" form defined in the config array.
		 *
		 */
		$rules = array(
			array(
				'field'   => 'check',
				'label'   => 'check',
				'rules'   => 'callback_antispam'
			),
			array(
				'field'   => 'username',
				'label'   => lang('form_label_username'),
	    		'rules'   => 'trim|required|xss_clean'
			),
			array(
				'field'   => 'password',
				'label'   => lang('form_label_password'),
				'rules'   => 'trim|required|xss_clean'
			)
		);
		
		$this->form_validation->set_rules($rules);
		
		return ($this->form_validation->run() === TRUE);
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
