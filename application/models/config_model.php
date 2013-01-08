<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Config_model extends Base_model 
{

	/*
	 * Content of the config file to alter
	 *
	 */
	static protected $content = NULL;
	
	/*
	 * Complete path to the config folder
	 *
	 */
	static protected $path = NULL;

	/*
	 * Name of the config file
	 *
	 */
	static protected $config_file = NULL;


	// --------------------------------------------------------------------

	
	/**
	 * Constructor
	 *
	 * @param	string  Path to the config file folder
	 *
	 */
	public function __construct()
	{
		// Ionize Core config file
		if (is_dir(realpath(APPPATH.'config')))
		{
			self::$path = realpath(APPPATH.'config').'/';
		}
		parent::__construct();

		log_message('debug', __CLASS__ . " Class Initialized");
	}
	

	// --------------------------------------------------------------------


	/**
	 * Opens one config file
	 *
	 * @param	string			config file name
	 * @param 	null|string		Module name
	 */
	public function open_file($config_file, $module = NULL)
	{
		// Module config file ?
		if ( !is_null($module) && is_dir(realpath(MODPATH.$module.'/config')))
		{
			self::$path = realpath(MODPATH.$module.'/config').'/';		
		}

		// Gets the content of the asked file
		if (is_file(realpath(self::$path.$config_file)))
		{
			self::$content = @file_get_contents(self::$path.$config_file);
			
			self::$config_file = $config_file;
		}
	}
	

	// --------------------------------------------------------------------


	/**
	 * Sets a config value
	 *
	 * @param $key
	 * @param $val
	 *
	 * @return bool
	 */
	public function set_config($key, $val)
	{
		if ( ! is_null(self::$content))
		{
			$pattern = '%(?sx)
				(
					\$'."config
					\[(['\"])
					(".$key.")
					\\2\]
					\s*=\s*
				)
				(.+?);
			%";
			
			$type = gettype($val);
			
			if ($type == 'string')
			{
				if (strtolower($val) == 'true')
					$val = var_export(TRUE, TRUE);

				else if (strtolower($val) == 'false')
					$val = var_export(FALSE, TRUE);

				else $val = "'".$val."'";
			}
			if ($type == 'boolean') $val = ($val ? var_export(TRUE, TRUE) : var_export(FALSE, TRUE) );

			if ($type == 'array')
			{
				$val = preg_replace("/[0-9]+ \=\>/i", '', var_export($val, TRUE));
				$val = str_replace("\n", "\r\n", $val);
			}

			self::$content = preg_replace($pattern, "\\1$val;", self::$content);

			return TRUE;
		}
		
		return FALSE;
	}
	

	// --------------------------------------------------------------------


	/**
	 * Change a config value
	 *
	 * @param	String	The config file name
	 * @param	String	key to change
	 * @param	Mixed	value to set to the key
	 * @param	String	Module name, in case of a module config file
	 *
	 * @return bool|int
	 */
	public function change($config_file, $key, $val, $module = NULL)
	{
		self::open_file($config_file, $module);
		
		self::set_config($key, $val);
		
		return self::save();
	}

	
	// --------------------------------------------------------------------


	/**
	 * Saves the config file
	 *
	 * @return bool
	 *
	 */
	public function save()
	{
		if ( ! is_null(self::$content))
		{
			$ret = @file_put_contents(self::$path.self::$config_file, self::$content);

			if ( ! $ret)
			{
				return FALSE;
			}
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file config_model.php */
/* Location: ./application/models/config_model.php */