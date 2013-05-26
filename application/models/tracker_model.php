<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.9
 */

// ------------------------------------------------------------------------

/**
 * Ionize Tracker Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Tracker
 * @author		Ionize Dev Team
 *
 */

class Tracker_model extends Base_Model
{
	/**
	 * Tracker Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('tracker');
	}


	// ------------------------------------------------------------------------


	/**
	 * Updates users activities
	 *
	 * @param $user
	 * @param $elements
	 */
	public function update_user($user, $elements)
	{
		if ( ! empty($user['id_user']))
		{
			$this->delete(array('id_user' => $user['id_user']));

			$elements = serialize($elements);

			$data = array(
				'id_user' => $user['id_user'],
				'last_time' => date('Y-m-d H:i:s'),
				'ip_address' => self::$ci->input->ip_address(),
				'elements' => $elements
			);

			$this->insert($data);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns te user's activities data
	 * @return array
	 */
	public function get_users()
	{
		$this->{$this->db_group}->where('last_time > ', 'now()-5', FALSE);
		$this->{$this->db_group}->join('user', 'user.id_user = ' .$this->table.'.id_user', 'inner');
		$this->{$this->db_group}->select('user.screen_name, user.username, user.email');

		$users = $this->get_list();

		return $users;
	}

}