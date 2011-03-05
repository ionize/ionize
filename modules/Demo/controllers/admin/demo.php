<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Demo Module Admin Controller
 *
 * This is the Ionize's admin panel for this module
 *
 * @author		Ionize Dev Team
 *
 *
 */

class Demo extends Module_Admin 
{
	/**
	 * Constructor
	 *
	 */
	function construct(){}


	// ------------------------------------------------------------------------


	/**
	 * Admin panel
	 * Called from the modules list
	 *
	 */
	function index()
	{
		$this->output('admin/demo');
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Saves module's settings in /modules/Demo/config/config.php file
	 *
	 */	
	function save_config()
	{
		$this->load->model('config_model');
		
		// The config model needs the module folder name to load the config.php file
		$this->config_model->open_file('config.php', $this->router->module_path);
		
		// Set the config items
		$this->config_model->set_config('module_demo_true_false', ($this->input->post('module_demo_true_false') == '1') ? TRUE : FALSE);
		$this->config_model->set_config('module_demo_string', $this->input->post('module_demo_string'));
		
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
				'url' => admin_url() . 'module/demo/demo/index',
				'title' => config_item('module_name')
			);
					
			$this->success(lang('ionize_message_settings_saved'));
		}
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Saves module's database fields
	 * Add fields if they don't exist
	 *
	 */
	function save_database_fields()
	{
	}

}
/* End of file usermanager.php */
/* Location: ./modules/Usermanager/controllers/admin/usermanager.php */