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


		// --------------------------------------------------------------------


		public function __construct()
		{
			self::$instance =& $this;

			log_message('debug', 'Class Module initialized');

			static::get_modules();
		}


		// --------------------------------------------------------------------


		/**
		 * Set the permissions through the Authority class
		 *
		 * @param string $role_code
		 *
		 */
		public static function set_role_permissions($role_code = NULL)
		{
			if ( ! is_null($role_code))
			{
				$modules = static::get_modules();

				foreach($modules as $folder => $module)
				{
					if ( ! empty($module['permissions']))
					{
						foreach($module['permissions'] as $resource_key => $resource)
						{
							$resource_key = 'module/' . strtolower($folder) . '/' . $resource_key;

							if ( isset($resource[$role_code]) && is_array($resource[$role_code]))
							{
								$rules = $resource[$role_code];

								foreach($rules as $permission => $actions)
								{
									if ($permission == 'allow' OR $permission = 'deny')
									{
										$actions = explode(',', $actions);

										foreach ($actions as $action)
										{
											$action = trim($action);
											$permission == 'allow' ? \Authority::allow($action, $resource_key) : \Authority::deny($action, $resource_key);
										}
									}
								}
							}
						}
					}
				}
			}
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

									if (in_array($folder_name, $modules))
									{
										$config['installed'] = TRUE;
										static::$installed_modules[$folder_name] = $config;
									}

									if (is_null(static::$modules))
										static::$modules = array();

									static::$modules[$folder_name] = $config;
									unset($config);
								}
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
	 * Returns the authentication object, short for User::get_instance().
	 *
	 * @return Ionize\User
	 */
	function Modules()
	{
		return Ionize\Modules::get_instance();
	}

}