<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

/**
 * Ionize Config Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Config
 * @author		Ionize Dev Team
 *
 */
class Config_model extends Base_model
{
	/**
	 * Content of the config file to alter
	 * @var null
	 */
	static protected $content = NULL;
	
	/**
	 * Complete path to the config folder
	 * @var null|string
	 */
	static protected $path = NULL;

	/**
	 * Name of the config file
	 * @var null
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
		if ( !is_null($module) && is_dir(realpath(MODPATH.ucfirst($module).'/config')))
		{
			self::$path = realpath(MODPATH.ucfirst($module).'/config').'/';
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
	 * @param      $key
	 * @param      $val
	 * @param null $module_key
	 *
	 * @return bool
	 */
	public function set_config($key, $val, $module_key=NULL)
	{
		if ( ! is_null(self::$content))
		{
			if ( is_null($module_key))
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
			}
			else
			{
				$pattern = "%([\"'](".$key.")[\"'][\s]*?=>[\s]*?)((?:(true|false),)|([\"'](.*?)[\"']))%";
				// $pattern = "%([\"'](".$key.")[\"'][\s]*?=>[\s]*?)(.*?,)%";
			}

			$type = gettype($val);

			if ($type == 'string')
			{
				if (strtolower($val) == 'true')
					$val = var_export(TRUE, TRUE);

				else if (strtolower($val) == 'false')
					$val = var_export(FALSE, TRUE);

				else $val = "'".$val."'";

				if ((strtolower($val) == 'true' OR strtolower($val) == 'false') && ! is_null($module_key))
					$val .= ',';
			}
			if ($type == 'boolean')
			{
				$val = ($val ? var_export(TRUE, TRUE) : var_export(FALSE, TRUE) );

				if ( ! is_null($module_key))
					$val .= ',';
			}

			if ($type == 'array')
			{
				$val = preg_replace("/[0-9]+ \=\>/i", '', var_export($val, TRUE));
				$val = str_replace("\n", "\r\n", $val);
			}

			/* Debug
				log_message('error', print_r($pattern, true));
				preg_match($pattern, self::$content, $matches);
				log_message('error', print_r($matches, true));
			*/

			if ( is_null($module_key))
				self::$content = preg_replace($pattern, "\\1$val;", self::$content);
			else
				self::$content = preg_replace($pattern, "\\1$val", self::$content);

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
		
		self::set_config($key, $val, $module);
		
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
