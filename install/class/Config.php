<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class ION_Config
{
	/*
	 * Content of the config file to alter
	 *
	 */
	static protected $content = NULL;
	
	/*
	 * Complete path to the config file
	 *
	 */
	static protected $path = NULL;


	// --------------------------------------------------------------------

	
	/**
	 * Constructor
	 *
	 * @param	string  Path to the config file folder
	 * @param	string  Config file name
	 *
	 */
	public function __construct($path, $config_file)
	{
		if (is_file(realpath($path.$config_file)))
		{
			self::$path = realpath($path.$config_file);
			self::$content = @file_get_contents(self::$path);
		}
		else
		{
		
			return FALSE;
		}
	}
	

	// --------------------------------------------------------------------


	/**
	 * Sets a config value
	 *
	 */
	public function set_config($key, $val)
	{
		if ( ! is_null(self::$content))
		{
//			$pattern = '%(config\[\''.$key.'\'\] = \')(.*)(\';)%';
//			self::$content = preg_replace($pattern, '$1'.$val. '$3', self::$content );

			$pattern = '%(config\[\''.$key.'\'\] = )(\'?.*\'?)(;)%';

			$type = gettype($val);
			
			if ($type == 'string')
			{
				$val = "'".$val."'";
			}
			if ($type == 'boolean') $val = ($val ? TRUE : (int) FALSE);
			
			self::$content = preg_replace($pattern, '${1}'.$val. '${3}', self::$content );

			return TRUE;
		}
		
		return FALSE;
	}
	
	
	// --------------------------------------------------------------------


	/**
	 * Saves the config file
	 *
	 */
	public function save()
	{
		if ( ! is_null(self::$content))
		{
			$ret = @file_put_contents(self::$path, self::$content);

			if ( ! $ret)
			{
				return FALSE;
			}
			return TRUE;
		}
		return FALSE;
	}

}


