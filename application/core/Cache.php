<?php
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
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
	
	private static $cache_expiration = 0;
		
	/**
	 * Contains the Cache instance.
	 *
	 */
	private static $instance;


	/**
	 * Cache path
	 *
	 */
	private static $cache_path;


	// --------------------------------------------------------------------
	
	
	/**
	 * 		
	 * Called through Cache() which return a singleton
	 *
	 */
	function __construct($cache_time = 0)
	{
		$this->cache_expiration = $cache_time;
		
		// If the cache folder isn't set in config.php, let's use the default CI one
		$this->cache_path = (config_item('cache_path') == '') ? APPPATH.'cache/' : config_item('cache_path');

		self::$instance =& $this;
	}


	// --------------------------------------------------------------------
	

	/**
	 * This function is called at startup (no CI instance access at this time)
	 * Cancels the internal CI startup Output()->_display_cache call.
	 *
	 */	 	 
	function display_cache_override()
	{
		return;
	}
	

	// --------------------------------------------------------------------
	

	/**
	 * Called just after each controller instanciation
	 * Displays or not the full page cache
	 *
	 */
	function post_controller_constructor_cache()
	{		
		$CI = &get_instance();
		
		$CFG =& load_class('Config');
		$URI =& load_class('URI');
		$OUT =& load_class('Output');

		// No cache if : 
		// - If some POST data are sent
		// - Admin URL
		// - User logged in
		if( ! empty($_POST) OR User()->logged_in() != FALSE OR $URI->segments[1] == config_item('admin_url'))
		{
			// Regenerate the page
			return FALSE;
		}

		// Retrieve the complete URI with language code
		$URI->_fetch_uri_string();

		/*
		 * HERE : 	Outputs the Cache()->get_page_cache()
		 * 			If cache file not found returns false, but do not write the cache file
		 *			Writin gof the cache file is done by Base_Controller()->render() depending on the page settings.
		 *
		 */

/*
		if ($OUT->_display_cache($CFG, $URI) == TRUE)
			exit;
*/
	}
	

	// --------------------------------------------------------------------
	
	
	/**
	 * Clears the whole cache folder
	 *
	 */
	function clear_cache()
	{
		if (is_dir($this->cache_path))
		{
			if ($dh = opendir($this->cache_path))
			{
				while (($file = readdir($dh)) !== false)
				{
					if(is_file($this->cache_path.$file) && $file!='..' && $file!='.' && $file!='index.html' && $file!='.htaccess')
					{
						unlink($this->cache_path.$file);
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
	function get($id)
	{
		// Do not cache for editors.
		if (User()->is('editors')) return FALSE;
	
		if ( ! is_dir($this->cache_path) OR ! is_really_writable($this->cache_path))
		{
			return FALSE;
		}

		$filepath = $this->cache_path.md5($id);

		if ( ! @file_exists($filepath))
			return FALSE;
	
		if ( ! $fp = @fopen($filepath, FOPEN_READ))
			return FALSE;
			
		flock($fp, LOCK_SH);
		
		$cache = '';
		if (filesize($filepath) > 0)
		{
			$cache = fread($fp, filesize($filepath));
		}
	
		flock($fp, LOCK_UN);
		fclose($fp);
					
		// Strip out the embedded timestamp		
		if ( ! preg_match("/(\d+TS--->)/", $cache, $match))
		{
			return FALSE;
		}

		// Has the file expired? If so we'll delete it.
		if (time() >= trim(str_replace('TS--->', '', $match['1'])))
		{ 		
			@unlink($filepath);
			log_message('debug', "Cache file has expired. File deleted");
			return FALSE;
		}

		// Display the cache
		return str_replace($match['0'], '', $cache);
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * Create one element cache file
	 *
	 * @usage : Cache()->store(__METHOD__.$somegeneratedid, $result)
	 *
	 */
	function store($id, $output)
	{
		if ($this->cache_expiration > 0 && User()->is_not('editors'))
		{
			$CI =& get_instance();	

			if ( ! is_dir($this->cache_path) OR ! is_really_writable($this->cache_path))
				return;

			$filepath = $this->cache_path . md5($id);
			
			if ( ! $fp = @fopen($filepath, FOPEN_WRITE_CREATE_DESTRUCTIVE))
			{
				
				log_message('error', "Unable to write cache file: ".$filepath);
				return;
			}
			
			$expire = time() + ($this->cache_expiration * 60);
	
			if (flock($fp, LOCK_EX))
			{
				fwrite($fp, $expire.'TS--->'.$output);
				flock($fp, LOCK_UN);
			}
			else
			{
				log_message('error', "Unable to secure a file lock for file at: ".$filepath);
				return;
			}
			fclose($fp);
			@chmod($this->cache_path, DIR_WRITE_MODE);
	
			log_message('debug', "Cache file written: ".$filepath);
		}
	}
	
	
	/**
	 * Writes a full page cache file
	 *
	 */
	function write_page_cache()
	{
	
	}
	
	
	/**
	 * Returns the cached page file content
	 *
	 */
	function get_page_cache()
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
			// put it in the loader
			$CI =& get_instance();

			$dummy = new Cache(config_item('cache_expiration'));

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

