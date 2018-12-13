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

		self::$ci->load->model('settings_model');
	}

	public function update_ionize_notifications($notifications)
	{
		$codes = parent::get_group_concat_array('code');
		$version = Settings::get('ionize_version');

		if ( ! empty($notifications))
		{
			foreach($notifications as $n)
			{
				$code = isset($n['code']) ? $n['code'] : '';

				$v_start = !empty($n['version_start']) ? $n['version_start'] : 'all';
				$v_end = !empty($n['version_end']) ? $n['version_end'] : 'all';
				$comply = $v_start == 'all' || (version_compare($version, $n['version_start'], '>=') && version_compare($version, $v_end, '<='));

				if ( ! in_array($code, $codes) && $comply)
				{
					if (isset($n['id_notification'])) unset($n['id_notification']);

					$this->insert_ignore($n, $this->get_table());
				}
			}
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

	public function get_networked_ionize_notifications()
	{
		$data = array(
			'notifications' => $this->get_ionize_notifications(),
			'version' => self::$ci->settings_model->get_setting_value('last_version'),
		);

		return $data;
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

	public function set_code_as_read($code)
	{
		$where = array('code' => $code);
		$data = array('read' => 1);
		$this->update($where, $data, $this->get_table());
	}

	public function should_refresh()
	{
		$last_refresh = Settings::get('last_notification_refresh');

		if (empty($last_refresh) OR dateDiff($last_refresh, NULL, 'day') > 0)
			return TRUE;

		return FALSE;
	}


	public function create_notification($title, $message, $code=NULL, $category=NULL, $update=FALSE)
	{
		$category = is_null($category) ? 'Message' : $category;
		$code = is_null($code) ? 'ionize' : $code;

		$done = false;

		$where = array(
			'title' => $title,
			'category' => $category,
			'code' => $code,
			'read' => '0'
		);

		$data = array(
			'date_creation' => date('Y-m-d H:i:s'),
			'content' => $message
		);

		if ($update)
		{
			$existing = parent::get_row_array($where);

			if ( ! empty($existing))
			{
				parent::update($where, $data);
				$done = TRUE;
			}
		}

		if ( ! $done)
		{
			$data = array_merge($where, $data);
			parent::insert($data);
		}
	}

	public function set_refreshed()
	{
		self::$ci->settings_model->set_setting('last_notification_refresh', date('Y-m-d H:i:s'));
	}

	public function set_last_version($version)
	{
		self::$ci->settings_model->set_setting('last_version', $version);
	}
}
