<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Session extends Base_Controller 
{
	public function __construct()
	{
		parent::__construct();

	}
	
	/**
	 * By default, set the posted vars if they are allowed
	 *
	 */
	public function index()
	{
		$allowed_vars = explode(',', config_item('module_session_allowed_variables'));

		foreach($_POST as $var => $value)
		{
			if (in_array($var, $allowed_vars))
			{
				$this->session->set_userdata($var, $value);
			}
		}
	}
	

}
