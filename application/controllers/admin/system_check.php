<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.7
 */

// ------------------------------------------------------------------------

/**
 * Ionize System Check Controller
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	System
 * @author		Ionize Dev Team
 *
 */

class System_check extends MY_admin
{
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		// Models
		$this->load->model('menu_model', '', true);
		$this->load->model('page_model', '', true);
		
		// Libraries
		$this->load->library('structure');
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/** 
	 * Displays the System Diagnostic Panel
	 * @param	string		Menu ID
	 *
	 */
	function index() 
	{
		$this->output('system_check');
	}
	
	
	/**
	 * Called through XHR
	 * Launches the checks
	 *
	 */
	function start_check()
	{
		$this->callback = array(
			array (
				'fn' => 'MUI.showSpinner'
			),
			array (
				'fn' => 'ION.JSON',
				'args' => array	(
					'system_check/check_pages_levels'
				)
			)
		);

		
		$this->response();
		
		
	}
	
	function check_pages_levels()
	{
		$this->callback = array(
			array (
					'fn' => 'ION.notification',
					'args' => array	(
						'success',
						'Check complete !'
					)
			)
		);
		
		$this->response();
	}
	
}


/* End of file system_check.php */
/* Location: ./application/controllers/admin/system_check.php */