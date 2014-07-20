<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Created by Martin Wernståhl on 2009-04-29.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'finder/finder.php';
require_once strtr(dirname(__FILE__), '\\', '/') .'/../helpers/trace_helper.php';

/**
 *
 */
class CI_Router
{
	public $config;
	public $routes = array();
	public $error_routes = array();
	public $uri_protocol = 'auto';
	public $default_controller = NULL;
	public $scaffolding_request = FALSE; // Must be set to FALSE

	/*
	 * The name of the module currently in.
	 */
	public $module = FALSE;

	/*
	 * Path to the module.
	 */
	public $module_path	= FALSE;

	/*
	 * Class and controller file name
	 */
	public $class = '';

	/*
	 * Method to be called
	 */
	public $method = 'index';

	/*
	 * Directory with the controller in.
	 */
	public $directory = '';

	/*
	 * Language key to use.
	 */
	public $lang_key = '';

	/*
	 * Raw detected key.
	 */
	private $raw_key = '';

	/*
	 * List of languages which can be used.
	 * 
	 * Format: array(lang_abbreviation => human_readable_english)
	 */
	public $lang_dict = array();


	public $lang_online = array();


	function __construct()
	{
		// Load the config class
		$this->config = load_class('Config');
		
		// Loads the Ionizes language config file. This file is created by the lang admin section of Ionize
		$this->config->load('language');
		$this->config->load('ionize');

		$this->uri = load_class('URI');
		
		// default language abbreviation
		$this->lang_key = $this->config->item('default_lang_code');

		// all available website languages
		$this->lang_dict = $this->config->item('available_languages');
		
 		$this->lang_online = $this->config->item('online_languages');

		$this->_calc_modpath_apppath_diff();

		$this->_set_routing();

		// If maintenance mode, don't go further
		if (config_item('maintenance') == 1 && $this->uri->segment(1) != config_item('admin_url'))
		{
			if ( ! in_array($_SERVER['REMOTE_ADDR'], config_item('maintenance_ips')))
			{
				$content = "Website in maintenance";
				
				if (file_exists(FCPATH.'maintenance.html'))
				{
					$content = file_get_contents(FCPATH.'maintenance.html');
				}
				elseif (file_exists(APPPATH.'views/core/maintenance.php')) 
				{
					$path = APPPATH.'views/core/maintenance.php';
					
					ob_start();
					if ((bool) @ini_get('short_open_tag') === FALSE AND config_item('rewrite_short_tags') == TRUE)
					{
						echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($path))));
					}
					else
					{
						include($path); 
					}
					ob_end_flush();
					die();
				}					
				
				echo $content;
				die();
			}
		}
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
				
				if(in_array($this->module, array_keys($modules)) && ( ! in_array($this->module, $disable_controller)))
				{
					// get path
					$this->module_path = $modules[$this->module];
				}
				else
				{
					// no module with that name exists
					$this->module = FALSE;
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
				// Lang detection : Cookie, Browser
				$this->detect_language();
			}
		}
		else
		{
			// Fetch the complete URI string
			$this->uri->_fetch_uri_string();
			
			// use the default controller
			if(empty($this->uri->uri_string))
				$this->uri->uri_string = '';

			$this->uri->_remove_url_suffix();

			// clean the uri and explode it
			$this->explode_segments($this->uri->uri_string);

			$this->raw_key = current($this->uri->segments);

			// Language key : check if we have a valid language key there
			if($key = $this->validate_lang_key($this->raw_key))
			{
				if (count($this->uri->segments) == 1 && $key == $this->config->item('default_lang_code'))
					$this->redirect_home_to_base_url();

				$this->lang_key = $key;

				$this->apply_language();
				
				// remove the language key
				array_shift($this->uri->segments);

				if(empty($this->uri->segments))
				{
					$this->uri->uri_string = '';
				}
				else
				{
					// remove the language key from the uri_string
					$this->uri->uri_string = strstr($this->uri->uri_string, '/');
				}
			}
			else
			{
				// Lang detection : Cookie, Browser
				$this->detect_language();

				// Home Page : Redirect to detected lang ?
				if ($this->raw_key == '')
				{
					$this->redirect_home_to_lang_url();
				}
			}
			// END Language key
			
			
			// get the generated module file and see if we can match to a module
			include APPPATH . 'config/modules.php';
			
			// get the first segment, to match to the modules
			$rmodule = current($this->uri->segments);
			
			// do we have a module with that name?
			if(in_array($rmodule, array_keys($modules)) && ( ! in_array($rmodule, $disable_controller)))
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
				$this->uri->segments[] = $val;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Tries to match the segments to a route.
	 * @param	array
	 * @return	array
	 */
	public function match_to_routes($segments)
	{
		// get the routes, reverse so we load the module's routes.php last - overwriting the others
		$files = array_reverse(Finder::find_file('routes', 'config', FALSE, TRUE));

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
			$default = empty($this->routes['default_controller']) ? FALSE : $this->routes['default_controller'];

			if( ! $default)
			{
				show_error("Unable to determine what should be displayed. A default route has not been specified in the module '$this->module' routing file.");
			}

			// $segments = explode('/', $default);
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
	 * @param	array
	 * @return  void
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
					while($params[0] =='')
						array_shift($params);

					$this->method = array_shift($params);
				}

				// create the rerouted array, controller/method/params to avoid large changes to CI
				$this->uri->rsegments = array_merge(array($this->class, $this->method), $params);

				return;
			}

			// make a more general search
			array_unshift($params, array_pop($segments));
		}

		// Get the 404 override
		if ( ! empty($this->routes['404_override']))
		{
			$x = explode('/', $this->routes['404_override']);
			$method = isset($x[1]) ? $x[1] : 'index';
			$this->class = $x[0];
			$this->method = $method;
			$this->uri->rsegments = array($this->class, $this->method);
			return;
		}

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
		
		$default = empty($route['default_controller']) ? FALSE : $route['default_controller'];
		
		if( ! $default)
		{
			show_error("Unable to determine what should be displayed. A default route has not been specified in the routing file.");
		}

		$this->uri->uri_string = $default;
	}


	// ------------------------------------------------------------------------


	public function get_default_controller()
	{
		if (is_null($this->default_controller))
		{
			include APPPATH . 'config/routes.php';
			$this->default_controller = $route['default_controller'];
		}

		return $this->default_controller;
	}

	// ------------------------------------------------------------------------


	public function get_lang_key()
	{
		return $this->lang_key;
	}

	// ------------------------------------------------------------------------


	public function get_raw_key()
	{
		return $this->raw_key;
	}

	// ------------------------------------------------------------------------

	public function is_home()
	{
		return empty($this->raw_key);
	}

	// ------------------------------------------------------------------------

	/**
	 * Set the default language regarding the URL (for admin)
	 * and regarding the Cookie or the Browser's user's language
	 *
	 * @return void
	 *
	 */
	public function detect_language()
	{
		$segments = array_values($this->uri->segment_array());

		// Check for admin language file
		if ((isset($segments[0]) && $segments[0] == config_item('admin_url')) OR (isset($segments[1]) && $segments[1] == config_item('admin_url')) )
		{
			$this->lang_key = $this->config->item('default_admin_lang');
		}
		else
		{
			$selected_language = NULL;

			// Case of Home page with Cookie : The asked lang code is the default one
			$selected_language = ( ! empty($_COOKIE['ion_selected_language'])) ? $_COOKIE['ion_selected_language'] : NULL ;

			if( ! is_null($selected_language))
			{
				// Use lang preference from cookie
				$this->lang_key = $selected_language;
			}
			else
			{
				// Define user lang by browser preferences
				$browser_lang = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : '';

				if(
					$browser_lang != ''
					&& $browser_lang != $this->config->item('default_lang_code')
					&& !empty($this->lang_dict[$browser_lang])
					&& !empty($this->lang_online[$browser_lang])
				)
					$this->lang_key = $browser_lang;
				else
					$this->lang_key = $this->config->item('default_lang_code');
			}
		}
		
		$this->apply_language();
	}

	
	// ------------------------------------------------------------------------

	/**
	 * Checks and redirect properly the Home page to the URL
	 * containing the lang code, if needed
	 *
	 * Note :
	 * Inside pages redirect could not work as $rawkey set by _set_routing can be another lang.
	 *
	 */
	public function redirect_home_to_lang_url()
	{
		if (
			count($this->lang_online) > 1 &&
			$this->lang_key != $this->config->item('default_lang_code')
		)
		{
			$url = config_item('base_url').$this->lang_key;

			log_message('debug', 'Router : Detected lang code : ' . $this->lang_key);
			log_message('debug', 'Router : Redirect to : '. $url);

			// 302 Found
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: '.$url, TRUE, 301);
		}
	}


	public function redirect_home_to_base_url()
	{
		$url = config_item('base_url');

		// 302 Found
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: '.$url, TRUE, 301);
	}


	/**
	 * Validates a language key and returns the filtered variant.
	 * 
	 * @param  scalar
	 * @return string|FALSE
	 */
	public function validate_lang_key($key)
	{
		$key = strtolower(trim((String) $key, '/\\ '));
		
		$segments = array_values($this->uri->segment_array());

		// If installer warning, the users languages are detected !
		// Not important, but not so clean, should be correctly implemented !

		// Admin lang key
		if (
			(isset($segments[0]) && $segments[0] == config_item('admin_url'))
			OR (
				isset($segments[1])
				&& $segments[1] == config_item('admin_url')
			)
		)
		{
			if (is_file(APPPATH.'language/'.$key.'/admin_lang'.EXT))
			{
				return $key;
			}
			else
			{
				log_message('debug', 'Router: The key "'.$key.'" is not a valid admin language key.');
			}
		}
		// User defined languages
		else if
		(
			! empty($this->lang_dict[$key])
			&& ! empty($this->lang_online[$key])
		)
		{
			return $key;
		}
		else
		{
			log_message('debug', 'Router: The key "'.$key.'" is not a valid language key.');
		}

		return FALSE;
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
		
		$this->config->set_item('detected_lang_code', $this->lang_key);
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
		return $this->class;
	}

	// ------------------------------------------------------------------------

	public function fetch_method()
	{
		return $this->method;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @deprecated		Use get_lang_key() instead
	 *
	 * @return string
	 */
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
/* Location: ./application/core/Router.php */