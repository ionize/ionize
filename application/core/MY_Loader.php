<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Martin Wernståhl on 2008-12-17
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

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
			$name = isset($this->_ci_varmap[$library]) ? $this->_ci_varmap[$library] : $library;

		$CI =& get_instance();
		if( ! isset($CI->$name))
		{
			$CI->$name = Lib($library, $params);
			$CI->_ci_classes[strtolower($name)] = $CI->$name;
		}
		/*
		else
			log_message('error', 'The library name you are loading is the name of a resource that is already being used: '.$name);
		*/
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
     * @param	string	the name of the class
     * @param	string	name for the model
     * @param	bool	database connection
     * @return	void
     */
    public function model($model, $name = '', $db_conn = FALSE)
    {
        if (is_array($model))
        {
            foreach ($model as $babe => $nickname)
            {
                if ( ! is_string($babe))
                {
                    $babe = $nickname;
                    $nickname = NULL;
                }
                $this->model($babe, $nickname, $db_conn);
            }
            return;
        }

        if ($model == '')
        {
            return;
        }

        $path = '';

        // Is the model in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($model, '/')) !== FALSE)
        {
            // The path is in front of the last slash
            $path = substr($model, 0, $last_slash + 1);

            // And the model name behind it
            $model = substr($model, $last_slash + 1);
        }

        if ($name == '')
        {
            $name = $model;
        }

        if (in_array($name, $this->_ci_models, TRUE))
        {
            return;
        }

        $CI =& get_instance();
        if (isset($CI->$name))
        {
            log_message('ERROR', 'The model name you are loading is the name of a resource that is already being used : ' . $name);
        }

        $model = strtolower($model);

        $installed_modules = Modules()->get_installed_modules();

        if(! empty($installed_modules))
            foreach($installed_modules as $module)
                $this->_ci_model_paths = array_unique(array_merge($this->_ci_model_paths, array(MODPATH.$module['folder'].'/')));

        foreach ($this->_ci_model_paths as $mod_path)
        {
            if ( ! file_exists($mod_path.'models/'.$path.$model.'.php'))
            {
                continue;
            }

            if ($db_conn !== FALSE AND ! class_exists('CI_DB'))
            {
                if ($db_conn === TRUE)
                {
                    $db_conn = '';
                }

                $CI->load->database($db_conn, FALSE, TRUE);
            }

            if ( ! class_exists('CI_Model'))
            {
                load_class('Model', 'core');
            }

            require_once($mod_path.'models/'.$path.$model.'.php');

            $model = ucfirst($model);

            $CI->$name = new $model();

            $this->_ci_models[] = $name;
            return;
        }

        // Could not find the model
        log_message('ERROR', 'Unable to locate the model you have specified : ' . $model);
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

}
