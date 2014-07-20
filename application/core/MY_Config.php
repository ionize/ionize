<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Martin Wernståhl on 2009-07-06.
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

/**
 * 
 */
class MY_Config extends CI_Config
{
	static $router = FALSE;
	
	// ------------------------------------------------------------------------

	/**
	 * Modified version of the original site_url(), which adds the current language key.
	 * 
	 * @param  string
	 * @return string
	 */
	public function site_url($uri = '')
	{
		if( ! self::$router)
		{
			self::$router = load_class('Router');
		}
		
		if(is_array($uri))
		{
			$uri = implode('/', $uri);
		}
		$index = $this->item('index_page') == '' ? '' : $this->slash_item('index_page');

		if($uri == '')
		{
			return $this->slash_item('base_url') .
				   $index .
				   self::$router->get_lang_key();
		}
		else
		{
			$suffix = ($this->item('url_suffix') == FALSE) ? '' : $this->item('url_suffix');

			return $this->slash_item('base_url') .
				   $index .
				   self::$router->get_lang_key() . '/' .
				   preg_replace("|^/*(.+?)/*$|", "\\1", $uri) .
				   $suffix;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an url to the specified uri for the specified language key.
	 * 
	 * @param  string
	 * @param  string
	 * @return string
	 */
	public function lang_url($lang_key, $uri = '')
	{
//		static $router;
		
//		if( ! $router)
		if( ! self::$router)
		{
			self::$router = load_class('Router');
		}
		
		$lkey = self::$router->validate_lang_key($lang_key);
		
		if( ! $lkey)
		{
			log_message('warning', 'Config: The language key "'.$lang_key.'" is not allowed or is not confgured.');
		}
		
		if(is_array($uri))
		{
			$uri = implode('/', $uri);
		}
		
		if($uri == '')
		{
			return $this->slash_item('base_url') .
				   $this->item('index_page').
				   ($lkey ? '/' . $lkey : '');
		}
		else
		{
			$suffix = ($this->item('url_suffix') == FALSE) ? '' : $this->item('url_suffix');
			return $this->slash_item('base_url') .
				   $this->slash_item('index_page') .
				   ($lkey ? $lkey . '/' : '') .
				   preg_replace("|^/*(.+?)/*$|", "\\1", $uri) .
				   $suffix;
		}
	}
}


/* End of file MY_Config.php */
/* Location: ./application/libraries/MY_Config.php */