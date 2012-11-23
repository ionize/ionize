<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.9
 */

// ------------------------------------------------------------------------

/**
 * Ionize Tracker Controller
 * Used to track backend panel user's activity
 * This is only used to give information about the current edited elements
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Tracker
 * @author		Ionize Dev Team
 *
 */

class Tracker extends My_Admin
{
	
	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		parent::__construct();

		$this->load->model('tracker_model', '', TRUE);
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 *
	 */
	function index()
	{
		$output = array();
		$user = $this->input->post('user');
		$elements = $this->input->post('elements');

		if ($user)
		{
			// Update tracker's user
			$this->tracker_model->update_user($user, $elements);

			// Get data
			$users = $this->tracker_model->get_users();

			foreach($users as &$user)
			{
				$elements = unserialize($user['elements']);
				$user['elements'] = $elements;
			}

			$output = array(
				'users' => $users
			);
		}

		$this->xhr_output($output);
	}



}

/* End of file user.php */
/* Location: ./application/controllers/admin/tracker.php */