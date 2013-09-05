<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Module Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

class Module extends MY_admin
{
	
	public $modules_folder = 'modules';


	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}


	// ------------------------------------------------------------------------


	/**
	 * Loads a module controller
	 * Receives the module name and proccess the URI
	 *
	 *
	 *
	 */
	function _remap($module_name)
	{
		// Delete the segments before the module name
		$mod_uri = array_slice($this->uri->segments, 3);

		// Get the controller, the called func name and the args
		$module_controller = $mod_uri[0];
		
		$module_func = 'index';
		if (isset($mod_uri[1]))
			$module_func = $mod_uri[1];
		
		$module_args = array_slice($mod_uri, 2);

		// Module path
		$module_path = MODPATH.ucfirst($module_name).'/';
		
		// Add the module path to the finder
		array_unshift(Finder::$paths, $module_path);

		// Includes the module Class file
		if ( ! class_exists($module_controller) && file_exists($module_path.'controllers/admin/'.$module_controller.EXT))
		{
			include($module_path.'controllers/admin/'.$module_controller.EXT);
		}
		else
		{
			echo('Class <b>' . ucfirst($module_controller) . '</b> not found in :<br/><b>'. $module_path.'controllers/admin/</b>');
			die();
		}

		// Create an instance of the module controller
		$obj = new $module_controller($this);
		
		// Loads module language file, if exists
//		if (is_file($module_path.'language/'.$this->config->item('default_lang_code').'/'.$module_name.'_lang.php'))
//		{
			$this->lang->load($module_name, Settings::get_lang('current'));
//		}
//		else
//		{
//			trace('warning: no language file for this module in : '. $module_path.'language/'.$this->config->item('default_lang_code').'/');
//		}

		call_user_func_array(array($obj, $module_func), $module_args);
	}
}
