<?php
/**
 * Modules Class
 *
 * @package 	Ionize CMS
 * @subpackage 	User
 * @author 		Ionize Dev Team
 *
 */

namespace Ionize {


	if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	class Modules {

		private static $ci;

		/**
		 * Contains the Modules instance.
		 *
		 * @var Modules
		 */
		private static $instance;

		/**
		 * All modules definition
		 *
		 * @var null|array
		 */
		private static $modules = NULL;


		/**
		 * Installed modules definition
		 *
		 * @var null|array
		 */
		private static $installed_modules = NULL;


		private static $resources = NULL;


		// --------------------------------------------------------------------


		public function __construct()
		{
			self::$instance =& $this;

			log_message('debug', 'Class Module initialized');

			static::get_modules();
		}


		// --------------------------------------------------------------------


		public static function get_module_config($module_folder)
		{
			$modules = static::get_modules();

			if (isset($modules[ucfirst($module_folder)]))
			{
				return $modules[ucfirst($module_folder)];
			}
			return array();
		}


		// --------------------------------------------------------------------


		public static function get_module_config_from_uri($module_uri)
		{
			$module_folder = NULL;

			// Modules from config file
			$modules = $aliases = $disable_controller = array();
			include APPPATH . 'config/modules.php';

			if(in_array($module_uri, array_keys($modules)) && ( ! in_array($module_uri, $disable_controller)))
				$module_folder = $modules[$module_uri];

			$all_modules = static::get_modules();

			if (isset($all_modules[ucfirst($module_folder)]))
			{
				return $all_modules[ucfirst($module_folder)];
			}
			return array();
		}


		// --------------------------------------------------------------------


		public static function get_resources()
		{
			if ( is_null(static::$resources))
			{
				$resources = array();

				$modules = static::get_installed_modules();

				foreach($modules as $module_key => $module)
				{
					$module_key = strtolower($module_key);

					$base_module_resource = 'module/' . $module_key;

					// Basic Module resource (root)
					$resources[] = array(
						'id_resource' => $base_module_resource,
						'id_parent' => '',
						'resource' => $base_module_resource,
						'actions' => '',
						'title' => $module['name'],
						'description' => '',
					);

					if (isset($module['resources']))
					{
						foreach($module['resources'] as $resource => $data)
						{
							// Root module actions
							if ( empty($resource))
							{
								$resources[0]['id_resource'] = $base_module_resource;
								$resources[0]['actions'] = ! empty($data['actions']) ? $data['actions'] : '';
								$resources[0]['title'] = ! empty($data['title']) ? $data['title'] : '';
								$resources[0]['description'] = ! empty($data['description']) ? $data['description'] : '';
							}
							else
							{
								$resources[] = array(
									'id_resource' => $base_module_resource .'/' . $resource,
									'id_parent' => ! empty($data['parent']) ? $base_module_resource .'/'.$data['parent'] : $base_module_resource,
									'resource' => $base_module_resource .'/' . $resource,
									'actions' => ! empty($data['actions']) ? $data['actions'] : '',
									'title' => ! empty($data['title']) ? $data['title'] : '',
									'description' => ! empty($data['description']) ? $data['description'] : $resource,
								);
							}
						}
					}
				}
				static::$resources = $resources;
			}

			return static::$resources;
		}


		// --------------------------------------------------------------------


		/**
		 * Get all modules
		 *
		 * @return array|null
		 *
		 */
		public static function get_modules()
		{
			if (is_null(static::$modules))
			{
				static::$modules = array();
				static::$installed_modules = array();

				// Installed modules, stored in application/config/modules.php
				$modules = array();
				include(APPPATH.'config/modules.php');

				// All modules folders
				$folders = glob(MODPATH.'*');

				if ( ! empty($folders))
				{
					foreach($folders as $folder)
					{
						if (is_dir($folder))
						{
							$file = $folder .'/config/config.php';
							if (is_file($file))
							{
								$config = include($file);

								if ( isset($config) && is_array($config))
								{
									$folder_name = array_pop(explode('/', $folder));

									$config['path'] = $folder;
									$config['folder'] = $folder_name;
									$config['key'] = strtolower($folder_name);
									$config['installed'] = FALSE;
									if ( ! isset($config['uri'])) $config['uri'] = $config['key'];

									if (in_array($folder_name, $modules))
									{
										$config['installed'] = TRUE;
										$config['front_uri'] = array_search($folder_name, $modules);
										static::$installed_modules[$folder_name] = $config;
									}

									static::$modules[$folder_name] = $config;
								}
								unset($config);
							}
						}
					}
				}
			}

			return static::$modules;
		}


		// --------------------------------------------------------------------


		/**
		 * Returns installed modules
		 *
		 * @return array|null
		 *
		 */
		public static function get_installed_modules()
		{
			static::get_modules();

			return static::$installed_modules;
		}


		// --------------------------------------------------------------------


		/**
		 * Get the instance of the Lib
		 *
		 */
		public static function get_instance()
		{
			if( ! isset(self::$instance))
			{
				new Modules();

				self::$ci->load->_ci_loaded_files[] = APPPATH.'core/Modules.php';
			}

			return self::$instance;
		}
	}
}

// --------------------------------------------------------------------

namespace {

	/**
	 * Returns the authentication object, short for Modules::get_instance().
	 *
	 * @return Ionize\Modules
	 */
	function Modules()
	{
		return Ionize\Modules::get_instance();
	}

}