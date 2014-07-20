<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Notification Controller
 * Gives infos about the release and some important notification messages
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.6
 */

class Notification extends MY_admin
{
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		// Models
		$this->load->model(
			array(
				'notification_model',
			),
			'',
			TRUE
		);
	}


	// ------------------------------------------------------------------------


	public function get_ionize_notifications()
	{
		if ($this->notification_model->should_refresh())
		{
			$this->load->library('curl');
			$this->curl->option(CURLOPT_USERAGENT, $this->input->user_agent());
			$this->curl->option(CURLOPT_RETURNTRANSFER, TRUE);

			$h = $this->input->request_headers();
			$h = array_change_key_case($h);

			$headers = array(
				'X-source: ionize',
				'X-version: ' . Settings::get('ionize_version'),
				'X-host: ' . (isset($h['host']) ? $h['host'] : ''),
			);

			if ( ! empty($h['accept-language']))
				$headers[] = 'accept-language:' . $h['accept-language'];

			$this->curl->option(CURLOPT_HTTPHEADER, $headers);

			$result = $this->curl->simple_get('http://ionizecms.com/ionize_notification');
			$result = json_decode($result, TRUE);

			$this->notification_model->set_refreshed();

			if ( ! empty($result))
			{
				if ( ! empty($result['version']))
					$this->notification_model->set_last_version($result['version']);

				if ( ! empty($result['notifications']))
					$this->notification_model->update_ionize_notifications($result['notifications']);

				$this->xhr_output($result);
			}
			else
				$this->xhr_output(array());
		}
		else
		{
			$result = $this->notification_model->get_networked_ionize_notifications();
			$this->xhr_output($result);
		}
	}


	// ------------------------------------------------------------------------


	public function get_local_notifications()
	{
		// $category = $this->input->post('category');

		// log_message('app', print_r($category, TRUE));

		$notifications = $this->notification_model->get_ionize_notifications();

		$this->xhr_output($notifications);
	}


	// ------------------------------------------------------------------------


	public function set_ionize_notification_as_read()
	{
		$post = $this->input->post();

		$this->notification_model->set_ionize_notification_as_read($post);

		$this->xhr_output(array());
	}
}
