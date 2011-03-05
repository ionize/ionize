<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usermanager extends Module_Admin 
{
	/**
	 * Constructor
	 *
	 */
	function construct(){}


	// ------------------------------------------------------------------------


	/**
	 * Admin panel 
	 *
	 */
	function index()
	{
		$this->output('admin/usermanager');
	}

}
/* End of file usermanager.php */
/* Location: ./modules/Usermanager/controllers/admin/usermanager.php */