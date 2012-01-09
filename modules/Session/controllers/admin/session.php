<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Module admin controller
*
* @author	Partikule Studio
*
*/
class Session extends Module_Admin 
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
		$this->output('admin/session');
	}

	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Saves module's settings in the module's config/config.php file
	 *
	 */	
	function save_config()
	{
		$this->load->model('config_model');
		
		// The config model needs the module folder name to load the config.php file
		$this->config_model->open_file('config.php', $this->router->module_path);
		
		// Set the config items
		$this->config_model->set_config('module_session_allowed_variables', $this->input->post('module_session_allowed_variables' == '1'));
		
		// Save the config items
		if (FALSE === $this->config_model->save() )
		{
			$this->error(lang('ionize_message_operation_nok'));
		}
		else
		{
			// Updates the main panel
			$this->update[] = array(
				'element' => 'mainPanel',
				'url' => admin_url() . 'module/session/session/index',
				'title' => config_item('module_session_name')
			);
					
			$this->success(lang('ionize_message_settings_saved'));
		}
	}
	

}

