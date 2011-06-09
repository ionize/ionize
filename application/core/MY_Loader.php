<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Martin Wernståhl on 2008-12-17
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

//require_once strtr(dirname(__FILE__), '\\', '/') .'/../libraries/finder/finder.php';
//require_once strtr(dirname(__FILE__), '\\', '/') .'/../libraries/finder/accessors.php';
//require_once strtr(dirname(__FILE__), '\\', '/') .'/../libraries/Theme.php';

require_once 'finder/finder.php';
require_once 'finder/accessors.php';
require_once 'Theme.php';

/**
 * Custom loader that utilizes the cascading filesystem.
 *
 * @author Martin Wernstahl
 */
class MY_Loader extends CI_Loader{

	/**
	 * Constructor
	 *
	 * Sets the path to the view files and gets the initial output buffering level
	 *
	 * @access	public
	 */
	/*function CI_Loader()
	{
		$this->_ci_is_php5 = (floor(phpversion()) >= 5) ? TRUE : FALSE;
		$this->_ci_view_path = APPPATH.'views/';
		$this->_ci_ob_level  = ob_get_level();

		log_message('debug', "Loader Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Class Loader
	 *
	 * This function lets users load and instantiate classes.
	 * It is designed to be called from a user's app controllers.
	 *
	 * @access	public
	 * @param	string	the name of the class
	 * @param	mixed	the optional parameters
	 * @param	string	an optional object name
	 * @return	void
	 */


	function library($library = '', $params = null, $name = null)
	{
		if(is_array($library))
		{
			foreach((Array) $library as $n => $l)
			{
				if(is_numeric($n))
				{
					$n = $l;
				}

				$this->library($l, null, $n);
			}
		}


		if(empty($name))
		{
			$name = isset($this->_ci_varmap[$library]) ? $this->_ci_varmap[$library] : $library;
		}

		$CI =& get_instance();
		if( ! isset($CI->$name))
		{
//			show_error('The library name you are loading is the name of a resource that is already being used: '.$name);
			$CI->$name = Lib($library, $params);
			$CI->_ci_classes[strtolower($name)] = $CI->$name;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Loads a class file from the libraries folder.
	 *
	 * Contrary to library(), it does not instantiate the class.
	 *
	 * @access public
	 * @param string The file name ( + optional path)
	 * @return void
	 */
	function class_file($file = '')
	{
		Finder::load_file($file, 'libraries');
	}

	// --------------------------------------------------------------------

	/**
	 * Model Loader
	 *
	 * This function lets users load and instantiate models.
	 *
	 * @access	public
	 * @param	string	the name of the class
	 * @param	string	name for the model
	 * @param	bool	database connection
	 * @return	void
	 */
	function model($model, $name = '', $db_conn = FALSE)
	{
		$CI =& get_instance();

// Why show the user a message ? Simply do nothing should be more appropriate.
		if(isset($CI->$name))
		{
			return;
//			show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
		}

		if (is_array($model))
		{
			foreach($model as $name => $babe)
			{
				if( ! is_numeric($name))
				{
					$this->model($babe, $name);
				}
				else
				{
					$this->model($babe);
				}
			}

			return;
		}
		
		if(empty($name))
		{
			$name = end(explode('/', $model));
		}


		if (in_array($name, $this->_ci_models, TRUE))
		{
			return;
		}
		
		$CI->$name = Model($model, $db_conn);

		$this->_ci_models[] = $name;
	}


	// --------------------------------------------------------------------

	/**
	 * Load View
	 *
	 * This function is used to load a "view" file.  It has three parameters:
	 *
	 * 1. The name of the "view" file to be included.
	 * 2. An associative array of data to be extracted for use in the view.
	 * 3. TRUE/FALSE - whether to return the data or load it.  In
	 * some cases it's advantageous to be able to return data so that
	 * a developer can process it in some way.
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	function view($view, $vars = array(), $return = FALSE)
	{
		return View($view, $vars, $return);
	}

	// --------------------------------------------------------------------

	/**
	 * Load File
	 *
	 * This is a generic file loader
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	/*function file($path, $return = FALSE)
	{

	}

	// --------------------------------------------------------------------

	/**
	 * Set Variables
	 *
	 * Once variables are set they become available within
	 * the controller class and its "view" files.
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	/*function vars($vars = array(), $val = '')
	{

	}

	// --------------------------------------------------------------------

	/**
	 * Load Helper
	 *
	 * This function loads the specified helper file.
	 *
	 * @access	public
	 * @param	mixed
	 * @return	void
	 */
	function helper($helpers = array())
	{
		foreach((Array)$helpers as $helper)
		{
			Helper($helper);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Load Plugin
	 *
	 * This function loads the specified plugin.
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function plugin($plugins = array())
	{
		foreach((Array)$plugins as $plugin)
		{
			Plugin($plugin);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Loads a language file
	 *
	 * @access	public
	 * @param	array
	 * @param	string
	 * @return	void
	 */
	/*
	function language($file = array(), $lang = '')
	{
		$l = Lib('lang');

		foreach((Array)$file as $f)
		{
			$l->line($f, $lang);
		}
	}
	*/

	/**
	 * Loads language files for scaffolding
	 *
	 * @access	public
	 * @param	string
	 * @return	arra
	 */
	/*function scaffold_language($file = '', $lang = '', $return = FALSE)
	{

	}

	// --------------------------------------------------------------------

	/**
	 * Loads a config file
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	/*function config($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{

	}

	// --------------------------------------------------------------------

	/**
	 * Scaffolding Loader
	 *
	 * This initializing function works a bit different than the
	 * others. It doesn't load the class.  Instead, it simply
	 * sets a flag indicating that scaffolding is allowed to be
	 * used.  The actual scaffolding function below is
	 * called by the front controller based on whether the
	 * second segment of the URL matches the "secret" scaffolding
	 * word stored in the application/config/routes.php
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	/*function scaffolding($table = '')
	{

	}

	// --------------------------------------------------------------------

	/**
	 * Loader
	 *
	 * This function is used to load views and files.
	 * Variables are prefixed with _ci_ to avoid symbol collision with
	 * variables made available to view files
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	/*function _ci_load($_ci_data)
	{

	}

	// --------------------------------------------------------------------

	/**
	 * Load class
	 *
	 * This function loads the requested class.
	 *
	 * @access	private
	 * @param 	string	the item that is being loaded
	 * @param	mixed	any additional parameters
	 * @param	string	an optional object name
	 * @return 	void
	 */
	/*function _ci_load_class($class, $params = NULL, $object_name = NULL)
	{

	}

	// --------------------------------------------------------------------

	/**
	 * Instantiates a class
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @param	string	an optional object name
	 * @return	null
	 */
	/*function _ci_init_class($class, $prefix = '', $config = FALSE, $object_name = NULL)
	{

	}

	// --------------------------------------------------------------------

	/**
	 * Autoloader
	 *
	 * The config/autoload.php file contains an array that permits sub-systems,
	 * libraries, plugins, and helpers to be loaded automatically.
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	function _ci_autoloader()
	{
		Lib(false); // load all that has been loaded by the front controller
		parent::_ci_autoloader();
	}

	// --------------------------------------------------------------------

	/**
	 * Assign to Models
	 *
	 * Makes sure that anything loaded by the loader class (libraries, plugins, etc.)
	 * will be available to models, if any exist.
	 *
	 * @access	private
	 * @param	object
	 * @return	array
	 */
	/*function _ci_assign_to_models()
	{

	}*/
}


/* End of file MY_Loader.php */
/* Location: /cygdrive/x/LOL/0web_dev/wwwroot/CIUA/trunk/public_html/application/libraries/MY_Loader.php */