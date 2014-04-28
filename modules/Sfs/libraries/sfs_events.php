<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sfs_Events
{
	protected static $ci;

	public function __construct()
	{
		// If the CI object is needed :
		self::$ci =& get_instance();

		// Config Events
		$config = Modules()->get_module_config('Sfs');
		$events = explode(',', $config['events']);

		// ionize < 1.0.4 hack : Be able to load module libraries
		$installed_modules = Modules()->get_installed_modules();
		foreach($installed_modules as $module)
			if (isset($module['folder'])) Finder::add_path(MODPATH.$module['folder'].'/');

		// Stop Forum Spam lib
		// @TODO : Enhance here to handle more services:
		// 1. Create one lib / service
		// 2. Rewrite the config system, which should have one key / service
		self::$ci->load->library('Sfs_Sfs');

		foreach($events as $event)
		{
			$event = trim($event);
			Event::register($event, array('Sfs_Sfs', 'on_post_check_before'));
		}
	}
}
