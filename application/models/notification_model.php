<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.6
 */

// ------------------------------------------------------------------------

/**
 * Ionize Notification Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Notification
 * @author		Ionize Dev Team
 *
 */
class Notification_model extends Base_model
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('notification');
	}

	public function update_ionize_notifications($notifications)
	{
		if ( ! empty($notifications))
		{
			foreach($notifications as $n)
				$this->insert_ignore($n, $this->get_table());
		}
	}

	public function get_ionize_notifications()
	{
		$where = array(
			'read' => '0',
			'order_by' => 'date_creation DESC'
		);

		return parent::get_list($where, $this->get_table());
	}

	public function set_ionize_notification_as_read($item)
	{
		if ( !empty($item['id_notification']))
		{
			$where = array('id_notification' => $item['id_notification']);
			$data = array('read' => 1);
			$this->update($where, $data, $this->get_table());
		}
	}
}
