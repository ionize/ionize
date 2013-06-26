<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Created on 2008 Dec 18
 * by Martin Wernstahl <m4rw3r@gmail.com>
 */


// --------------------------------------------------------------------

/**
 * Load View
 *
 * This function is used to load a "view" file.
 *
 * @access	public
 * @param	string	The name of the "view" file to be included
 * @param	array 	An associative array of data to be extracted for use in the view
 * @param	bool	TRUE/FALSE - whether to return the data or load it.  In
 * some cases it's advantageous to be able to return data so that
 * a developer can process it in some way
 * @return	void
 */
function View($file, $vars = array(), $return = false)
{
	// Set up the finder to also use the theme folder:
	array_unshift(Finder::$paths, Theme::get_theme_path());

	// Set the path to the requested file
	$files = Finder::find_file($file, 'views', false);
	
	// remove the path we added earlier, so it won't cause any disturbances later:
	array_shift(Finder::$paths);

	if(empty($files))
	{
		show_error('Unable to load the requested file: '.$file);
	}

	$view = array_shift($files);

	$loader = Lib('loader');

	return $loader->_ci_load(array('_ci_path' => $view, '_ci_vars' => $loader->_ci_object_to_array($vars), '_ci_return' => $return));
}

// --------------------------------------------------------------------

/**
 * Loads a library and returns it's instance.
 *
 * @param string	The library name/path
 * @param array		The configuration
 * @return object
 */
function Lib($library, $config = null)
{
	static $libraries = array();

	if($library === false)
	{
		// init default loaded classes here
		$classes = array(
							'config'		=> 'config',
							'input'			=> 'input',
							'benchmark'		=> 'benchmark',
							'uri'			=> 'uri',
							'output'		=> 'output',
							'lang'			=> 'lang',
							'router'		=> 'router'
							);

		$CI =& get_instance();

		foreach($classes as $var)
		{
			$libraries[$var] =& $CI->$var;
		}

		$libraries['load'] =& $CI->load;
		$libraries['loader'] =& $CI->load;

		return null;
	}

	$library = strtolower($library);

	// already loaded?
	if(isset($libraries[$library]))
	{
		return $libraries[$library];
	}

	if( ! $res = Finder::load_file($library, 'libraries'))
	{
		log_message('error', "Unable to load the requested class file: libraries/".$library);
		show_error("Unable to load the requested class file: libraries/".$library);
	}

	if($config === null)
	{
		foreach(Finder::get_config_file($library) as $c)
		{
			include_once $c;
		}
	}

	if(($p = strrpos($library, '/')) !== false)
	{
		$library = substr($library, $p + 1);
	}

	if($res === 'SUBCLASS')
	{
		$name = Finder::$subclass_prefix . $library;
	}
	else
	{
		if(class_exists('CI_' . $library))
		{
			$name = 'CI_' . $library;
		}
		else
		{
			$name = $library;
		}
	}

	// Is the class name valid?
	if ( ! class_exists($name))
	{
		log_message('error', "Accessors : Non-existent class: ".$name);
		show_error("Accessors : Non-existent class: ".$name);
	}

	if($config !== null)
	{
		$libraries[$library] = new $name($config);
	}
	else
	{
		$libraries[$library] = new $name;
	}

	return $libraries[$library];
}

/**
 * Static accessor for all models.
 *
 * @param string		The path to the file relative to the models dir
 * @param string		The database connection to use
 * @return object
 */
function &Model($model, $db_conn = '')
{
	static $models = array();

	// already loaded?
	if(isset($models[$model]))
	{
		return $models[$model];
	}

	if( ! class_exists('Model'))
	{
		load_class('Model', 'core');
	}

	if( ! Finder::load_file($model, 'models', false))
	{
		show_error('Unable to locate the model you have specified: '.$model);
	}
	
	if(($p = strrpos($model, '/')) !== false)
	{
		$model = substr($model, $p + 1);
	}
	
//	$model .= '_Model';

	$models[$model] = new $model();

	// load the database
	if($db_conn !== false)
	{
		$models[$model]->db = Lib('loader')->database($db_conn === true ? '' : $db_conn, true);
	}

	// reference other libs
//	$models[$model]->_assign_libraries();

	return $models[$model];
}

// --------------------------------------------------------------------

/**
 * Loads a helper.
 *
 * @param  string
 * @return void
 */
function Helper($helper)
{
	static $helpers = array();

	$helper = strtolower(str_replace(EXT, '', str_replace('_helper', '', $helper)).'_helper');

	if(in_array($helper, $helpers))
	{
		return;
	}

	$files = Finder::find_file($helper, 'helpers', 100);

	if(empty($files))
	{
		show_error('Unable to load the requested file: helpers/'.$helper.EXT);
	}

	foreach($files as $f)
	{
		include_once($f);
	}

	$helpers[] = $helper;

	log_message('debug', 'Helper loaded: '.$helper);
}

// --------------------------------------------------------------------

/**
 * Loads a plugin.
 *
 * @param  string
 * @return void
 */
function Plugin($plugin)
{
	static $plugins = array();

	$plugin = str_replace(EXT, '', str_replace('_pi', '', $plugin)).'_pi';

	if(in_array($plugin, $plugins))
	{
		return;
	}

	$files = Finder::find_file($plugin, 'plugins', 100);

	if(empty($files))
	{
		show_error('Unable to load the requested file: plugins/'.$plugin.EXT);
	}

	foreach($files as $f)
	{
		include_once($f);
	}

	$plugins[] = $plugin;

	log_message('debug', 'Plugin loaded: '.$plugin);
}

/* End of file accessors.php */
/* Location: ./application/libraries/Loader/accessors.php */