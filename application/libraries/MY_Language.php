<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class MY_Language extends CI_Language
{
	function MY_Language()
    {
        parent::CI_Language();
	}
	
	/**
	 * Load a language file
	 * Modified to take the config->default_language in account
	 *
	 * @access	public
	 * @param	mixed	the name of the language file to be loaded. Can be an array
	 * @param	string	the language (english, etc.)
	 * @return	mixed
	 */
	function load($langfile = '', $idiom = '', $return = FALSE)
	{
		$CI =& get_instance();
		
		// REMOVED EXT ON THE LINE BELOW, Martin Wernståhl
		$langfile = str_replace(EXT, '', str_replace('_lang.', '', $langfile)).'_lang';

		if (in_array($langfile, $this->is_loaded, TRUE))
		{
			return;
		}

		if ($idiom == '')
		{
			if (isset($CI->config))
			{
				$deft_lang = $CI->config->item('language_abbr');
				$idiom = ($deft_lang == '') ? 'en' : $deft_lang;
			}
			// So Installer can output CI errors through MY_Language
			else
			{
				$idiom = 'english';
			}
		}

		// find the files to load, allow extended lang files
		$files = Finder::find_file($idiom . '/' . $langfile, 'language', 99);

		if(empty($files))
		{
			// Try with the last defualt language... 
			$idiom = $CI->config->item('default_language');
			$files = Finder::find_file($idiom . '/' . $langfile, 'language', 99);
			
			/*
			 * Do not display the error, so the views can be loaded, even the content isn't translated
			 *
			*/
			if(empty($files))
			{
				show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
			}
		}
		
		// reverse the array, so we let the extending language files load last
		foreach(array_reverse($files) as $f)
		{
			include $f;
		}
		// End addition
		
		if ( ! isset($lang))
		{
			log_message('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);
			return;
		}
		
		if ($return == TRUE)
		{
			return $lang;
		}
		
		$this->is_loaded[] = $langfile;
		$this->language = array_merge($this->language, $lang);
		unset($lang);

		log_message('debug', 'Language file loaded: language/'.$idiom.'/'.$langfile);
		return TRUE;
	}
}

