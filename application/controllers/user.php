<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends Base_Controller {


	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		parent::__construct();

		/*
		 * to avoid the loop, we have to reset the restrict array in the constructor
		 */
		User()->folder_protection = array();

		$this->load->library('form_validation');

		$this->load->model(
			array(
				'user_model',
				'role_model'
			), '', TRUE);
	}


	// ------------------------------------------------------------------------


	/**
	 * Do nothing
	 *
	 */
	public function index()
	{
		echo ('');
		die();
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
			$user = $this->user_model->find_user($email);
			trace($user);
			trace('Received : ' . $activation_key);
			trace('Calculated : ' . User()->calc_activation_key($user));
			 */
			$this->template['title'] = lang('connect_activation_title');
			$this->template['message'] = lang('connect_user_activated_error');
			$this->output('user/activate');
		}
		else
		{
			$this->template['title'] = lang('connect_activation_title');
			$this->template['message'] = lang('connect_user_activated_message');
			$this->output('user/activate');
		}
	}
}
