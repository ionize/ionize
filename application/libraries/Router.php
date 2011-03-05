<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Created by Martin Wernståhl on 2009-04-29.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'finder/finder.php';

/**
 *
 */
class CI_Router
{
	public $config;
	public $routes 		= array();
	public $error_routes	= array();
	public $uri_protocol 	= 'auto';
	public $default_controller;
	public $scaffolding_request = FALSE; // Must be set to FALSE

	/**
	 * The name of the module currently in.
	 */
	public $module			= false;
	/**
	 * The path to the module.
	 */
	public $module_path		= false;
	/**
	 * Class and controller file name
	 */
	public $class			= '';
	/**
	 * Method to be called
	 */
	public $method			= 'index';
	/**
	 * Directory with the controller in.
	 */
	public $directory		= '';
	/**
	 * Language key to use.
	 */
	public $lang_key		= '';
	/**
	 * List of languages which can be used.
	 * 
	 * Format: array(lang_abbreviation => human_readable_english)
	 */
	public $lang_dict		= array();
	/**
	 * List of language abbreviations to ignore.
	 */
	public $lang_ignore		= array();


	function __construct()
	{
		// Load the config class
		$this->config = load_class('Config');
		
		// Loads the Ionizes language config file. This file is created by the lang admin section of Ionize
		$this->config->load('language');
		$this->config->load('ionize');

		$this->uri = load_class('URI');
		
		// default language abbreviation
		$this->lang_key = $this->config->item('language_abbr');

		// all available website languages
		$this->lang_dict = $this->config->item('lang_uri_abbr');
		
		// ignore lang array
		$this->lang_ignore = (Array) $this->config->item('lang_ignore');

		$this->_calc_modpath_apppath_diff();

		$this->_set_routing();

		log_message('debug', "Router Class Initialized");
	}

	/**
	 * Calculates the path difference between APPPATH and MODPATH, needed to be
	 * able to call controllers in modules.
	 * 
	 * The $this->modpath_diff is to be appended to the APPPATH constant, then
	 * that path will point to the modules dir.
	 * 
	 * @author Martin Wernståhl <m4rw3r@gmail.com>
	 */
	public function _calc_modpath_apppath_diff()
	{
		// Clean and split the paths
		$apppath = explode('/', trim(strtr(APPPATH, '\\', '/'), '/'));
		$modpath = explode('/', trim(strtr(MODPATH, '\\', '/'), '/'));
		
		$i = 0;
		$ac = count($apppath);
		$mc = count($modpath);
		
		for(; $i < $ac && $i < $mc; $i++)
		{
			if($apppath[$i] != $modpath[$i])
			{
				break;
			}
		}
		
		// Assemble the difference
		$this->modpath_diff = str_repeat('..'.DIRECTORY_SEPARATOR, $ac - $i).implode(DIRECTORY_SEPARATOR, array_slice($modpath, $i));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the routes.
	 *
	 * @return void
	 */
	public function _set_routing()
	{
		if($this->config->item('enable_query_strings') && isset($_GET[$this->config->item('controller_trigger')]))
		{
			// controller
			$this->class = trim($this->uri->_filter_uri($_GET[$this->config->item('controller_trigger')]));
			
			// module
			if(isset($_GET[$this->config->item('module_trigger')]))
			{
				$this->module = trim($this->uri->_filter_uri($_GET[$this->config->item('module_trigger')]));
				
				// check if it is installed
				include APPPATH . 'config/modules.php';
				
				if(in_array($this->module, array_keys($modules)))
				{
					// get path
					$this->module_path = $modules[$this->module];
				}
				else
				{
					// no module with that name exists
					$this->module = false;
				}
			}

			// method
			if(isset($_GET[$this->config->item('function_trigger')]))
			{
				$this->method = trim($this->uri->_filter_uri($_GET[$this->config->item('function_trigger')]));
			}
			
			// language
			if(isset($_GET[$this->config->item('language_trigger')]) &&
				$key = $this->valid_lang_key($_GET[$this->config->item('language_trigger')]))
			{
				$this->lang_key = $key;
				$this->apply_language();
			}
			else
			{
				// Browser detection : need to be rewritten
				// This function does not return the good language in case of root ("/") URL
				// It just returns the first found browser lang code, which can be incorrect.
				// $this->detect_language();

				// No browser detection : Ionize defined default lang code.
				$this->set_default_language();
			}
		}
		else
		{
			// Fetch the complete URI string
			$this->uri->_fetch_uri_string();
			
			// use the default controller
			if(empty($this->uri->uri_string))
			{
				$this->load_default_uri();
			}
			$this->uri->_remove_url_suffix();
			
			// clean the uri and explode it
			$this->explode_segments($this->uri->uri_string);
			
			// LANGUAGE:
			// check if we have a valid language key there
			if($key = $this->validate_lang_key($raw_key = current($this->uri->segments)))
			{
				$this->lang_key = $key;

				$this->apply_language();
				
				// remove the language key
				array_shift($this->uri->segments);
				
				if(empty($this->uri->segments))
				{
					// load the default uri again
					$this->load_default_uri();
					
					$this->uri->_remove_url_suffix();
					
					// clean the uri and explode it
					$this->explode_segments($this->uri->uri_string);
				}
				else
				{
					// remove the language key from the uri_string
					$this->uri->uri_string = '/' . preg_replace('/\/?'.preg_quote($raw_key).'\//', '', $this->uri->uri_string);
				}
			}
			else
			{
				// No browser detection : Ionize defined default lang code.
				$this->set_default_language();
				
				// Browser detection : need to be rewritten
				// This function does not return the good language in case of root ("/") URL
				// It just returns the first found browser lang code, which can be incorrect.
				// $this->detect_language();
			}
			// END LANGUAGE
			
			
			// get the generated module file and see if we can match to a module
			include APPPATH . 'config/modules.php';
			
			// get the first segment, to match to the modules
			$rmodule = current($this->uri->segments);
			
			// do we have a module with that name?
			if(in_array($rmodule, array_keys($modules)))
			{
				// yes, remove it and store it as a module, also remove the module name from the uri
				$this->module_path = $modules[array_shift($this->uri->segments)];
				$this->module = $rmodule;
			}
			// do we have an aliased controller?
			elseif(in_array($rmodule, array_keys($aliases)))
			{
				// yes, get module and controller, remove alias from the uri
				list($this->module_path, $c) = each($aliases[array_shift($this->uri->segments)]);
				$this->module = array_search($this->module_path, $modules);
				
				// add the controller back to it, so the match_to_routes() and find controller works as they should
				$this->uri->segments = array_merge(array($c), $this->uri->segments);
			}
			if($this->module_path)
			{
				// we have module, add the module to the cascade
				Finder::$paths = array_merge(array(MODPATH . $this->module_path . '/'), Finder::$paths);
			}
			$this->uri->segments = $this->match_to_routes($this->uri->segments);

			$this->find_controller($this->uri->segments);
				
			// add the module first so it is correct
			if($this->module_path)
			{
				$this->uri->segments = array_merge(array($this->module), $this->uri->segments);
			}
			
			// make them start with 1
			$this->uri->_reindex_segments();
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * A mashup of the two URI methods _explode_segments() and _filter_uri()
	 *
	 * @param  string
	 * @return void
	 */
	public function explode_segments($str)
	{
		$str = preg_replace("|/*(.+?)/*$|", "\\1", $str);
		
		if($str != '' && $this->config->item('permitted_uri_chars') != '')
		{
			if ( ! preg_match("|^[".$this->config->item('permitted_uri_chars')."/]+$|i", $str))
			{
				header('HTTP/1.1 400 Bad Request');
				show_error('The URI you submitted has disallowed characters.');
			}
		}
		
		// Convert programatic characters to entities
		$bad	= array('$', 		'(', 		')',	 	'%28', 		'%29');
		$good	= array('&#36;',	'&#40;',	'&#41;',	'&#40;',	'&#41;');

		$str = str_replace($bad, $good, $str);

		// remove segments
		foreach(explode('/', $str) as $val)
		{
			// Filter segments for security
			$val = trim($val);

			if($val != '')
			{
				$this->uri->segments[] = $val;
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Tries to match the segments to a route.
	 *
	 * @return array
	 */
	public function match_to_routes($segments)
	{
		// get the routes, reverse so we load the module's routes.php last - overwriting the others
		$files = array_reverse(Finder::find_file('routes', 'config', false, true));

		foreach($files as $f)
		{
			include($f);

			// merge the route : module's route overwrite other routes
			$this->routes = ( ! isset($route) OR ! is_array($route)) ? $this->routes : array_merge($this->routes, $route);

			unset($route);
		}

		// we may have an empty route here, because the first segment could have been a module
		if(empty($segments))
		{
			// $default = empty($routes['default_controller']) ? false : $routes['default_controller'];
			$default = empty($this->routes['default_controller']) ? false : $this->routes['default_controller'];

			if( ! $default)
			{
				show_error("Unable to determine what should be displayed. A default route has not been specified in the module '$this->module' routing file.");
			}

			$segments = explode('/', $default);
		}

		///////////////////////////////////////////
		// FROM CodeIgniter, slightly modified by Martin Wernstahl
		///////////////////////////////////////////

		// Turn the segment array into a URI string
		$uri = implode('/', $this->uri->segments);

		// Is there a literal match?  If so we're done
		if(isset($this->routes[$uri]))
		{
			return explode('/', $this->routes[$uri]);
		}

		// Loop through the route array looking for wild-cards
		foreach($this->routes as $key => $val)
		{
			// Convert wild-cards to RegEx
			$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

			// Does the RegEx match?
			if(preg_match('#^'.$key.'$#', $uri))
			{
				// Do we have a back-reference?
				if(strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
				{
					$val = preg_replace('#^'.$key.'$#', $val, $uri);
				}

				return explode('/', $val);
			}
		}

		return $this->uri->segments;
	}

	// ------------------------------------------------------------------------

	/**
	 * Finds the correct controller and creates the rerouted segments.
	 *
	 * @return void
	 */
	public function find_controller($segments)
	{
		$params = array();

		if( ! $this->module_path)
		{
			$path = APPPATH;
		}
		else
		{
			$path = MODPATH . $this->module_path . '/';
		}

		while( ! empty($segments))
		{


			if(file_exists($path . 'controllers/' . implode('/', $segments) . EXT))
			{
				$this->class = array_pop($segments);
				$this->directory = implode('/', $segments);


				// set the method if we have one
				if( ! empty($params))
				{
					$this->method = array_shift($params);
				}

				// create the rerouted array, controller/method/params to avoid large changes to CI
				$this->uri->rsegments = array_merge(array($this->class, $this->method), $params);

				return;
			}

			// make a more general search
			array_unshift($params, array_pop($segments));
		}


		// Yell!
		show_404(implode('/', $params));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Loads the default route from the routes.php config.
	 * 
	 * @return void
	 */
	public function load_default_uri()
	{
		include APPPATH . 'config/routes.php';
		
		$default = empty($route['default_controller']) ? false : $route['default_controller'];
		
		if( ! $default)
		{
			show_error("Unable to determine what should be displayed. A default route has not been specified in the routing file.");
		}

		$this->uri->uri_string = $default;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Detects the language automatically from the browser data.
	 * 
	 * @return void
	 */
	public function detect_language()
	{
		$input = load_class('Input');
		
		// If this script doesn't find a valid language, the really default one will be set
		$valid_language_found = false;

		// check browser's languages
		$accepted_langs = $input->server('HTTP_ACCEPT_LANGUAGE');

		if($accepted_langs !== false)
		{
			$accepted_langs = explode(",", strtolower($accepted_langs));

			foreach($accepted_langs as $lang)
			{
				// ignore everything after ';', and '-' if present
				$lang = array_shift(explode('-', array_shift(explode(';', $lang))));

				if($key = $this->validate_lang_key($lang))
				{
					$this->lang_key = $key;

					$this->apply_language();

					$valid_language_found = true;
					
					break;
				}
			}
		}
		
		if ($valid_language_found === false)
		{
			$this->lang_key = $this->config->item('default_lang');
			$this->apply_language();
		}
	}

	
	// ------------------------------------------------------------------------

	/**
	 * Set the default language regarding the URL (admin or not) if no URI lang segment is detected
	 *
	 * @return void
	 */
	public function set_default_language()
	{
		$segments = $this->uri->segment_array();

		// Check for admin language file
		if ($segments[0] == config_item('admin_url') OR (isset($segments[1]) && $segments[1] == config_item('admin_url')) )
		{
			$this->lang_key = $this->config->item('default_lang');
		}
		else
		{
			$this->lang_key = $this->config->item('language_abbr');
		}
		
		$this->apply_language();
	}

	
	// ------------------------------------------------------------------------

	/**
	 * Validates a language key and returns the filtered variant.
	 * 
	 * @param  scalar
	 * @return string|false
	 */
	public function validate_lang_key($key)
	{
		$key = strtolower(trim((String) $key, '/\\ '));
		
		$segments = $this->uri->segment_array();

// If installer warning, the users languages are detected ! 
// Not important, but not so clean, should be correctly implemented !


		// Check for admin language file
		if ($segments[0] == config_item('admin_url') OR (isset($segments[1]) && $segments[1] == config_item('admin_url')) )
		{
			if (is_file(APPPATH.'language/'.$key.'/admin_lang'.EXT))
			{
				return $key;
			}
			else
			{
				log_message('debug', 'Router: The key "'.$key.'" was not a valid admin language key.');
			}
		}
		// User defined languages
		else if(isset($this->lang_dict[$key]) && ! in_array($key, $this->lang_ignore))
		{
			return $key;		
		}
		else
		{
			log_message('debug', 'Router: The key "'.$key.'" was not a valid language key.');
			
			return false;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the configuration to the detected language.
	 * 
	 * @return void
	 */
	public function apply_language()
	{
		log_message('debug', 'Router: Applying the language key "'.$this->lang_key.'".');
		
		$this->config->set_item('language_abbr', $this->lang_key);
	//	$this->config->set_item('language', $this->lang_dict[$this->lang_key]);
	}

	// ------------------------------------------------------------------------

	public function fetch_controller_path()
	{
		if($this->module_path)
		{
			$path = MODPATH;
		}
		else
		{
			$path = APPPATH;
		}

		return $path . 'controllers/' . ($this->directory ? $this->directory . '/' : '') . $this->class . EXT;
	}

	// ------------------------------------------------------------------------

	public function fetch_module_uri_seg()
	{
		return $this->module;
	}

	// ------------------------------------------------------------------------

	public function fetch_class()
	{
//		return $this->class . '_Controller';
		return $this->class;
	}

	// ------------------------------------------------------------------------

	public function fetch_method()
	{
		return $this->method;
	}
	
	// ------------------------------------------------------------------------
	
	public function fetch_lang_key()
	{
		return $this->lang_key;
	}
	
	// ------------------------------------------------------------------------
	
	public function fetch_lang_name()
	{
		return $this->lang_dict[$this->lang_key];
	}

	// ------------------------------------------------------------------------

	public function fetch_directory()
	{
		if($this->module_path)
		{
			// First ".." is for the controllers folder, then we need to add that after the module path
			return '../'.$this->modpath_diff.'/'.$this->module_path.'/controllers/'.($this->directory ? $this->directory.'/' : '');
		}
		else
		{
			return ($this->directory ? $this->directory.'/' : '');
		}
	}

}


/* End of file MY_Router.php */
/* Location: ./application/libraries/MY_Router.php */