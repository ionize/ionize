<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize, creative CMS Settings Library
 *
 * Stores and retrieves the main website settings
 * Languages, current language, website meta, etc.
 * in an array and manage it.
 *
 * @package		Ionize
 * @subpackage	Librairies
 * @category	Librairies
 * @author		Ionize Dev Team
 */

class Settings
{
	public static $settings = array();
	
	public static $online_languages = array();

	public static $mimes = FALSE;


	/**
	 * Sets one setting
	 *
	 * @param	string	the setting key
	 * @param	string	the setting value	 
	 *
	 */
	public static function set($key, $value)
	{
		self::$settings[$key] = $value;		
	}

	// ------------------------------------------------------------------------


	/**
	 * Get one setting
	 *
	 * @param	string	the wished setting key
	 * @param	string	lang code. 
	 *					The settings for the current language are set as normal settings, so this param is not necessary to get them.
	 *					This param is usefull if the "lang" array is feeded with the languages settings for each language
	 *					Ex : Setting::lang = array(
	 *											'en' => array(
	 *														'meta-description' => 'Some text',
	 *														'meta-keywords' => 'word 1'
	 *													)
	 *										 )
	 *
	 * @return mixed	The setting value
	 */
	public static function get($key, $lang = FALSE)
	{
		if ($lang !== FALSE)
		{
			return isset(self::$settings[$lang][$key]) ? self::$settings[$lang][$key] : FALSE;
		}
		return (isset(self::$settings[$key]) ? self::$settings[$key] : FALSE);
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the setting array
	 *
	 * @return array	The settings array as array of settig key => value
	 *
	 */
	public static function get_settings()
	{
		return self::$settings;
	}


	// ------------------------------------------------------------------------


	/**
	 * Set settings from a list of arrays
	 * Usefull when set settings from a result_array() database array;
	 *
	 * @param	array	List of arrays
	 * @param	string	the field name to use as setting key in each array of the list
	 * @param	string	the field name to use as setting value in each array of the list
	 *	 
	 */ 
	public static function set_settings_from_list($list, $key_field, $value_field)
	{
		foreach($list as $index=>$table)
		{
			self::$settings[$table[$key_field]] = $table[$value_field];
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $list
	 * @param $key_field
	 * @param $value_field
	 */
	public static function set_lang_settings($list, $key_field, $value_field)
	{
		foreach($list as $index=>$table)
		{
			self::$settings[$table['lang']][$table[$key_field]] = $table[$value_field];
		}
		
	}


	// ------------------------------------------------------------------------


	/** 
	 * Sets the languages property
	 * $this->languages stores all the languages from the DB (website languages)
	 * as an associative array with all languages settings (online, default, etc.)
	 * This is mandatory to check if a language is online or such things
	 *
	 * @param	array	languages array
	 */
	public static function set_languages($languages)
	{
		// Stores languages keys to Settings
		self::set('languages', $languages);
	}


	// ------------------------------------------------------------------------


	public static function validate_detected_lang_code($admin=FALSE)
	{
		$ci =& get_instance();
		$validated = FALSE;
		$detected_lang_code = config_item('detected_lang_code');
		if ( ! $admin)
		{
			foreach(self::get_online_languages() as $item)
			{
				if ($item['lang'] == $detected_lang_code)
					$validated = TRUE;
			}
		}
		else
		{
			foreach(self::get('displayed_admin_languages') as $lang_code)
			{
				if ($lang_code == $detected_lang_code)
					$validated = TRUE;
			}
		}

		if ( ! $validated)
		{
			if ( ! $admin)
				$ci->config->set_item('detected_lang_code', config_item('default_lang_code'));
			else
				$ci->config->set_item('detected_lang_code', config_item('default_admin_lang'));

			log_message('error', 'Settings: Corrected detected lang code to: ' . config_item('detected_lang_code'));
		}

		self::set('detected_lang_code', config_item('detected_lang_code'));
	}


	// ------------------------------------------------------------------------


	public static function is_language_online($lang_code)
	{
		$is_online = FALSE;
		foreach(self::get_online_languages() as $lang)
		{
			if ($lang['lang'] == $lang_code)
			{
				$is_online = TRUE;
				break;
			}
		}
		return $is_online;
	}


	// ------------------------------------------------------------------------


	/** 
	 * Returns all the languages
	 * (online or not)
	 *
	 * @return array	The languages array
	 *
	 */
	public static function get_languages()
	{
		return self::get('languages');
	}


	// ------------------------------------------------------------------------


	/** 
	 * Returns the array of online languages
	 *
	 * @return array	The languages array
	 *
	 */
	public static function get_online_languages()
	{
		if (empty(self::$online_languages))
		{
			$languages = self::get('languages');
			
			foreach($languages as $lang)
			{
				if ($lang['online'] == '1')
				{
					self::$online_languages[] = $lang;
				}
			}
		}
		
		return self::$online_languages;
		
	}


	// ------------------------------------------------------------------------


	/**
	 * Sets all the languages online
	 *
	 */
	public static function set_all_languages_online()
	{
		$languages = self::get('languages');
		
		self::$online_languages = array();
		
		foreach($languages as $lang)
		{
			$lang['online'] = 1;
			self::$online_languages[] = $lang;
		}
	}


	// ------------------------------------------------------------------------


	public static function get_default_admin_lang()
	{
		$default_admin_lang = self::get('default_admin_lang');
		$displayed_admin_lang = self::get('displayed_admin_languages');
		
		// Correct the default Admin panel language
		if ( ! in_array($default_admin_lang, $displayed_admin_lang))
			$default_admin_lang = config_item('language');
		
		return $default_admin_lang;
	}


	// ------------------------------------------------------------------------


	public static function get_uri_lang()
	{
		$str = preg_replace("|/*(.+?)/*$|", "\\1", str_replace(base_url(), '', current_url()));
		$uri_segments = explode('/', $str);
		$uri_lang = current($uri_segments);
		
		return $uri_lang;	
	}
	
	
	// ------------------------------------------------------------------------


	public static function get_thumbs()
	{
		$thumbs = array();
		
		foreach(self::$settings as $key => $setting)
		{
			if(substr($key, 0, 5) == 'thumb')
			{
				$thumbs[] = $setting;
			}
		}
		return $thumbs;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the website (visitor side) language code regarding to the type
	 *
	 * @param	string	Wished lang code. Optional.
	 *					'first' :	returns the first language code (depending on the language ordering in DB) 
	 *					'default' : returns the default website language code
	 *					'current' : returns the current website language code
	 *
	 *					If no type is given, returns the current lang code
	 *		
	 * @return string|null	The lang code, NULL if no one is found
	 *
	 */
	public static function get_lang($type = 'current')
	{
		$lang = NULL;
	
		$languages = self::$settings['languages'];

		switch ($type)
		{
			case 'first':

				$lang = $languages[0]['lang'];
				break;

			case 'default':
				
				foreach($languages as $l)
				{
					if ($l['def'] == '1')
						$lang = $l['lang'];
				}
				
				// If no default lang set, returns the Config file default one
				if (is_null($lang))
					$lang = config_item('default_lang_code');

				break;

			case 'current':

				$lang = self::$settings['detected_lang_code'];
				break;
		}
		return $lang;
	}


	// ------------------------------------------------------------------------


	public static function get_mimes_types()
	{
		if (self::$mimes == FALSE)
		{
			$mimes_ionize = array();
			if (@require_once(APPPATH.'config/mimes_ionize'.EXT))
			{
				self::$mimes = $mimes_ionize;
				unset($mimes_ionize);
			}
		}

		return self::$mimes;
	}


	// ------------------------------------------------------------------------


	public static function get_allowed_extensions($type = FALSE)
	{
		$allowed_extensions = array();

		$mimes_ionize = self::get_mimes_types();

		$filemanager_file_types = explode(',', self::get('filemanager_file_types'));

		if ($type == FALSE)
		{
			foreach($mimes_ionize as $type)
			{
				foreach($type as $ext => $mime)
				{
					if (in_array($ext, $filemanager_file_types))
						$allowed_extensions[] = $ext;
				}
			}
		}
		else
		{
			if ( ! empty($mimes_ionize[$type]))
			{
				foreach($mimes_ionize[$type] as $ext => $mime)
				{
					if (in_array($ext, $filemanager_file_types))
						$allowed_extensions[] = $ext;
				}
			}
		}
		
		return $allowed_extensions;
	}


	// ------------------------------------------------------------------------


	public static function get_allowed_mimes()
	{
		$allowed_mimes = array();

		$mimes_ionize = self::get_mimes_types();

		$filemanager_file_types = explode(',', self::get('filemanager_file_types'));
		
		foreach($mimes_ionize as $type)
		{
			foreach($type as $ext => $mime)
			{
				if (is_array($mime))
				{
					foreach($mime as $item)
					{
						if ( ! in_array($item, $allowed_mimes) && in_array($ext, $filemanager_file_types))
							$allowed_mimes[] = $item;
					}
				}
				else
				{
					if ( ! in_array($mime, $allowed_mimes) && in_array($ext, $filemanager_file_types))
						$allowed_mimes[] = $mime;
				}
			}
		}
		return $allowed_mimes;
	}
}


/* End of file Settings.php */
/* Location: ./application/libraries/Settings.php */
