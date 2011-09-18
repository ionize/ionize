<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Created on 2008 Dec 18
 * by Martin Wernstahl <m4rw3r@gmail.com>
 */

// get subclass_prefix
Finder::$subclass_prefix = config_item('subclass_prefix');

/**
 * Finds files in the directory structure.
 *
 * @author Martin Wernstahl
 */
class Finder{

	/**
	 * The paths to search in, also controls the cascades.
	 *
	 * Most important first.
	 * Default: APPPATH, BASEPATH
	 *
	 * @var array
	 */
	static $paths = array(APPPATH, BASEPATH);

	/**
	 * Stores all filenames that load_file() has found, and if they are subclasses or not.
	 *
	 * @var array
	 */
	static $loaded_files = array();

	/**
	 * The subclass prefix.
	 *
	 * @var string
	 */
	static $subclass_prefix = 'MY_';

	// --------------------------------------------------------------------

	/**
	 * Loads a file.
	 *
	 * Cascades over the paths which are defined and also search for subclasses.
	 *
	 * @param string	The file path (without .php)
	 * @param string	The type of file (determines which directory it resides in)
	 * @param bool		If to search for subclasses
	 *
	 * @return bool|string String if it is a subclass
	 */
	static public function load_file($path, $type = '', $allow_subclass = true)
	{
		// add slashes
		$temp_type = ( ! empty($type)) ? $type.'/' : '';

		// is it already loaded?
		if(isset(self::$loaded_files[$temp_type.$path.EXT]))
		{
			log_message('debug', $path." class already loaded. Second attempt ignored.");

			return self::$loaded_files[$temp_type.$path.EXT];
		}

		$files = array_reverse(self::find_file($path, $type, $allow_subclass == true ? 1 : 0));

		// have we found a class?
		if(empty($files))
		{
			// no
			log_message('error', "Unable to load the requested class: ".$path);

			return false;
		}

		foreach($files as $f)
		{
			require_once $f;
		}

		// have we found a subclass?
		if(count($files) == 1)
		{
			// no
			return self::$loaded_files[$temp_type.$path.EXT] = true;
		}
		else
		{
			// yes
			return self::$loaded_files[$temp_type.$path.EXT] = 'SUBCLASS';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Returns an array of matching config files.
	 *
	 * @param string The path to the file relative to the config directiry
	 * @return array Containing the paths to the files
	 */
	static public function get_config_file($path)
	{
		// reverse it so we get the subclass last, if it exists
		return array_reverse(self::find_file($path, 'config', 100));
	}

	// --------------------------------------------------------------------
	/**
	 * Adds a path to the static path var
	 *
	 * @param string The path to add
	 */
	static public function add_path($path)
	{
		array_unshift(self::$paths, $path);
	}

	// --------------------------------------------------------------------

	/**
	 * Finds the files requested.
	 *
	 * Subclass is first, then comes the base-class
	 * (only one in the array if there are no subclasses)
	 * Empty array if nothing was found
	 *
	 * @param string	The path to the file relative to the "type" directory
	 * @param string	The type of file (determines which directory it resides in)
	 * @param int		The number of subclasses that are allowed
	 * @param bool		If to present all file findings
	 * @return array	With the found files, subfile first and then base-file (if they exists)
	 */
	static public function find_file($path, $type = '', $allow_subclasses = 1, $all = false)
	{
		// separate path and filename
		$path = explode('/', $path);
		$file = strtolower(array_pop($path));
		$path = implode('/', $path);

		// add slashes
		$path .= ( ! empty($path)) ? '/' : '';
		$type .= ( ! empty($type)) ? '/' : '';

		$found_subclasses = 0;
		$found = array();

		$filenames = array(ucfirst($file), $file);

		// check all the paths
		foreach(self::$paths as $p)
		{
			$dir = $p . $type . $path;

			// check both Library and library
			foreach($filenames as $file)
			{
				// do we have a subclass?
				if($allow_subclasses > $found_subclasses && file_exists($dir . self::$subclass_prefix . $file . EXT))
				{
					$found[] = $dir . self::$subclass_prefix . $file.EXT;
					$found_subclasses++;
					
					// do not try to load the next file, it will only create problems with PHP trying to redeclare methods
					break;
				}
				elseif(file_exists($dir . $file . EXT))
				{
					$found[] = $dir . $file . EXT;

					// should we find all occurances?
					if( ! $all)
					{
						// no
						break 2;
					}
					
					// do not try to load the next file, it will only create problems with PHP trying to redeclare methods
					break;
				}
			}
		}

		return $found;
	}
}

/* End of file finder.php */
/* Location: ./application/libraries/Finder/finder.php */