<?php
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.97
 *
 */

/**
 * Cache Class
 *
 * To play with cached files :
 *
		$cache_path = (config_item('cache_path') == '') ? BASEPATH.'cache/' : config_item('cache_path');
		
		foreach(Settings::get_languages() as $language)
		{
			$uri = base_url().'/'.$language['lang'].'/'.${$language['lang']}['url'];
			trace($uri);
			trace(md5($uri));
			trace($cache_path.md5($uri));
		}
 * 
 * To clear the cache, from any controller :
 *
 * Cache()->clear_cache();
 *
 */
class Cache
{
	var $CI;
	
	/**
	 * Contains the Cache instance.
	 *
	 */
	private static $instance;


	// --------------------------------------------------------------------
	
	
	/**
	 * 		
	 * Called through Cache() which return a singleton
	 *
	 */
	function __construct($config = array())
	{
		self::$instance =& $this;
	}


	// --------------------------------------------------------------------
	

	/**
	 * This function is called first
	 * But we don't have access to CI instance
	 *
	 */	 	 
	function display_cache_override()
	{
	}
	

	// --------------------------------------------------------------------
	

	/**
	 * Called just after each controller instanciation
	 * Displays or not the cache
	 *
	 */
	function post_controller_constructor_cache()
	{		
		$CI = &get_instance();
		
		$CFG =& load_class('Config');
		$URI =& load_class('URI');
		$OUT =& load_class('Output');
		
		if( ! empty($_POST) OR $URI->segments[1] == config_item('admin_url') )
		{
			// Regenerate the page
			return FALSE;
		}

		// Retrieve the complete URI with language code
		$URI->_fetch_uri_string();

		if ($OUT->_display_cache($CFG, $URI) == TRUE)
			exit;
	}
	

	// --------------------------------------------------------------------
	
	
	/**
	 * Clears the whole cache folder
	 *
	 */
	function clear_cache()
	{
		$cache_path = (config_item('cache_path') == '') ? BASEPATH.'cache/' : config_item('cache_path');
		
		if (is_dir($cache_path))
		{
			if ($dh = opendir($cache_path))
			{
				while (($file = readdir($dh)) !== false)
				{
					if($file!='..' && $file!='.' && $file!='index.html')
					{
						unlink($cache_path.$file);
					}
				}
			}
			closedir($dh);
		}
	}

	
	// --------------------------------------------------------------------
	
	
	/*
	 * These 3 methods will be used for tag result caching
	 * Each tag will or not manage its own cache, allowing some dynamic data,
	 * like the displayed connected username, not to cached.
	 *
	 * Because tags returns strings, the $OUT->_display_cache will not be mandatory anymore
	 *
	 */
	
	/**
	 * Checks if an element is cached
	 *
	 * @usage : Cache()->has(__METHOD__.$somegeneratedid)
	 *
	 */
	function has()
	{
	
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * Get one element cached file
	 *
	 * @usage : Cache()->get(__METHOD__.$somegeneratedid)
	 *
	 */
	function get()
	{
	
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * Create one element cache file
	 *
	 * @usage : Cache()->store(__METHOD__.$somegeneratedid, $result)
	 *
	 */
	function store()
	{
	
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * Get the instance of Cache Lib
	 *
	 */
	public static function get_instance()
	{
		if( ! isset(self::$instance))
		{
			// no instance present, create a new one
			$config = array();
			
			$dummy = new Cache();

			// put it in the loader
			$CI =& get_instance();
			
			$CI->load->_ci_loaded_files[] = APPPATH.'libraries/Cache.php';
		}
		
		return self::$instance;
	}


}

/**
 * Returns the cache object, short for Cache::get_instance().
 *
 * @return Cache
 *
 */
function Cache()
{
	return Cache::get_instance();
}

