<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Rss admin controller
*
* The controller that handles actions 
* related to Rss module admin.
*
* @author	Ionize Dev Team
*/
class Sfs extends Module_Admin
{
	/**
	* Constructor
	*
	* @access	public
	* @return	void
	*/
	function construct()
	{
	}

	/**
	* Admin panel
	* Called from the modules list.
	*
	* @access	public
	* @return	parsed view
	*/
	function index()
	{
		$this->output('admin/sfs');
	}

	/**
	* Saves the config.php settings
	*
	* @access	public
	* @return	mixed
	*/
	function save_config()
	{
		// Model used to write config.php
		$this->load->model('config_model', '', TRUE);
		
		// Settings to save
		$settings = array(
			'api_key',
			'track',
			'evidence_input',
			'username_input',
			'events',
		);

		foreach($settings as $field)
		{
			$setting = $this->input->post($field);

			// Checkbox to false
			if ($field == 'track' &&  ! $setting)
				$setting = 'false';

			// log_message('error', $field . ':' . $setting);

			// Try to change the setting
			if($this->config_model->change('config.php', $field, addslashes($setting), 'Sfs') == FALSE)
			{
				// Error message
				$this->error(lang('module_sfs_error_writing_config_file'));
				
				die();
			}
		}
		
		// Answer
		$this->success(lang('ionize_message_operation_ok'));				
	}


	public function test()
	{
		$this->load->library('Sfs_Sfs', '', 'sfs');

		$this->template['result'] = $this->sfs->test_api(
			array(
				'api_key' => $this->input->post('api_key'),
				'evidence_input' => $this->input->post('evidence_input'),
				'username_input' => $this->input->post('username_input'),
			)
		);

		$this->output('admin/test');
	}
	
}

