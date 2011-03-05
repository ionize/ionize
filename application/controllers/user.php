<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends Base_Controller {


	function __construct()
	{
		parent::__construct();

		/*
		 * to avoid the loop, we have to reset the restrict array in the constructor
		 * Access()->somevar = array();
		 * somevar is the same as the configuration option
		 */
		Connect()->folder_protection = array();

		$this->load->library('form_validation');
		
		// Set individual errors delimiters to nothing
		$this->form_validation->set_error_delimiters('','');
	}


	// ------------------------------------------------------------------------


	/**
	 * By default, the controller will send the user to the login screen
	 *
	 */
	function index()
	{
		$this->login();
	}


	// ------------------------------------------------------------------------


	/**
	 * Logs one user
	 *
	 */
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


	/**
	 * Anti spam
	 * If the field "name" is empty (not filled by javascript), message that javascript is needed
	 * Ensure that most of the bots could not use the form
	 *
	 */
	function antispam($str)
	{
		if ($str != config_item('form_antispam_key'))
		{
			$this->form_validation->set_message('antispam', lang('contact_form_javascript_needed'));
			return false;
		}
		else
		{
			return true;
		}
	}	


	// ------------------------------------------------------------------------


	private function _try_validate_login()
	{
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
		
		return ($this->form_validation->run() === true);
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
