<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.92
 *
 */

/**
 * Ionize Tagmanager Class
 *
 * Gives a controller Ionize basic FTL tags 
 *
 * @package		Ionize
 * @subpackage	Libraries
 * @category	TagManager Libraries
 *
 */
require_once APPPATH.'libraries/ftl/parser.php';
require_once APPPATH.'libraries/ftl/arraycontext.php';

class TagManager
{
	protected static $_inited = FALSE;

	protected static $tags = array();
	
	protected static $module_folders = array();
	
	protected static $trigger_else = 0;
	
	static $ci;

//	protected static $_cache = array();

	/*
	 * Extended fields prefix. Needs to be the same as the one defined in /models/base_model
	 *
	 */
	protected static $extend_field_prefix = 'ion_';

	/**
	 * @var	FTL_ArrayContext
	 */
	public static $context;
	
	public static $tag_prefix = 'ion';

	public static $view = '';

	public static $uri_segments = array();


	/**
	 * The tags with their corresponding methods that this class provides (selector => methodname).
	 * 
	 * Add extra in subclasses to provide additional tags.
	 * 
	 * @var array
	 */
	public static $tag_definitions = array
	(
		// Generic tags
		// Common objects have at least these values, in DB or set through the tag method
		'id' => 			'tag_id',
		'url' => 			'tag_url',
		'get' => 			'tag_get',
		'index' => 			'tag_simple_value',
		'count' => 			'tag_simple_value',
		'name' => 			'tag_simple_value',
		'title' => 			'tag_simple_value',
		'subtitle' => 		'tag_simple_value',
		'date' => 			'tag_simple_date',

		// System / Core tags
//		'field' =>				'tag_field',
//		'list' =>				'tag_list',
		'config' => 			'tag_config',
		'base_url' =>			'tag_base_url',
		'partial' => 			'tag_partial',
		'widget' =>				'tag_widget',
		'translation' => 		'tag_translation',
		'site_title' => 		'tag_site_title',
		'meta_title' => 		'tag_meta_title',
		'meta_keywords' => 		'tag_meta_keywords',
		'meta_description' => 	'tag_meta_description',
		'setting' => 			'tag_setting',
		'uniq' =>				'tag_uniq',
		'if' =>					'tag_if',
		'else' =>				'tag_else',
		'set' =>				'tag_set',
		'jslang' =>				'tag_jslang',
		'browser' =>			'tag_browser',
		
	);


	// TESTS ------------------------------------------------------------------------

	/**
	 * Returns the "name" value if set
	 *
	 * @param 	FTL_Binding $tag
	 *
	 * @return 	null|string
	 *
	 * @usage	Can be used with several tags.
	 * 			<ion:language>
	 * 				<ion:name [tag="span" class="colored"] />
	 * 			</ion:language>
	 *
	 * 			Shortcut mode :
	 * 			<ion:language:name [tag="span" class="colored"] />
	 */
	public static function tag_simple_value(FTL_Binding $tag)
	{
		$value = $tag->getValue();

		if ( ! is_null($value))
			return self::wrap($tag, $value);

		return $value;
	}

	public static function tag_simple_date(FTL_Binding $tag)
	{
		$value = $tag->getValue();

		if ( ! is_null($tag->getAttribute('format')))
			$value = self::format_date($tag, $value);

		if ( ! is_null($value))
			return self::wrap($tag, $value);

		return $value;
	}

	/**
	 * Returns the object ID
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_id(FTL_Binding $tag)
	{
		$value = $tag->getValue();

		// Try with the DB key name
		if (is_null($value))
			$value = $tag->getValue('id_' . $tag->getParentName());

		return $value;
	}

	/**
	 * Returns the object absolute's URL
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return null
	 *
	 */
	public static function tag_url(FTL_Binding $tag)
	{
		$value = $tag->getValue('absolute_url');

		// Fall down to URL
		if (is_null($value))
			$value = $tag->getValue();

		return $value;
	}


	/**
	 * Returns one key from object
	 * @TODO	See how to implement alternatives if the value is null or empty string
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return null|string
	 *
	 */
	public static function tag_get(FTL_Binding $tag)
	{
		$key = $tag->getAttribute('key');
		$value = self::get_formatted_from_tag_data($tag, $key);

		// @TODO
		// if (is_null($value) && !is_null($tag->getAttribute('or')))

		return $value;
	}


	/**
	 * Return one formatted key from the direct parent tag or NULL if no data
	 *
	 * @param FTL_Binding $tag
	 * @param null        $key
	 *
	 * @return null|string
	 *
	 */
	public static function get_formatted_from_tag_data(FTL_Binding $tag, $key=NULL)
	{
		$value = self::get_from_tag_data($tag, $key);

		// If "format" attribute is defined, suppose the field is a date ...
		if ( ! is_null($tag->getAttribute('format')))
			$value = self::format_date($tag, $value);

		if ( ! is_null($value))
			return self::wrap($tag, $value);

		return $value;
	}


	/**
	 * Return one key from the tag data array or NULL if no data
	 * The tag is supposed to have one data array, which has the same
	 * name than his direct parent tag.
	 *
	 * Note :
	 *		To set a data array from one tag method, use $tag->set('data_array_name', $value);
	 *
	 * Example :
	 * In this example, we call the tag "get"
	 * Its parent is "page"
	 * page is supposed to have one data array called "page"
	 * <ion:page:get key="id_page" /> : returns the field "id_page" from parent "page"
	 *
	 * @param FTL_Binding $tag
	 * @param null        $key
	 *
	 * @return null
	 */
	public static function get_from_tag_data(FTL_Binding $tag, $key=NULL)
	{
		$value = $tag->getValue($key);

		if (is_null($value))
			$value = $tag->getValue(self::$extend_field_prefix.$key);

		return $value;
	}


	// /TESTS ------------------------------------------------------------------------


	/**
	 * Initializes the FTL Manager.
	 * 
	 * @return void
	 *
	 */
	public static function init()
	{
		if(self::$_inited)
		{
			return;
		}
		self::$_inited = TRUE;
		
		self::$ci =& get_instance(); 
		
		self::$context = new FTL_ArrayContext();

		// Inlude array of module definition. This file is generated by module installation in Ionize.
		// This file contains definition for installed modules only.
		include APPPATH.'config/modules.php';
		
		self::$uri_segments = explode('/', self::$ci->uri->uri_string());

		// Put modules arrays keys to lowercase
		if (!empty($modules))
			self::$module_folders = array_combine(array_map('strtolower', array_values($modules)), array_values($modules));
		
		// Loads automatically all installed modules tags
		foreach (self::$module_folders as $module)
		{
			self::autoload_module_tags($module.'_Tags');
		}
		
		// Load automatically all TagManagers defined in /libraries/Tagmanager
		$tagmanagers = glob(APPPATH.'libraries/Tagmanager/*'.EXT);
		
		foreach ($tagmanagers as $tagmanager)
		{
			self::autoload(array_pop(explode('/', $tagmanager)));
		}
		
		self::add_globals();
		self::add_tags();
		self::add_module_tags();
	}


	// ------------------------------------------------------------------------
	
	
	/**
	 * Autoloads tags from core TagManagers
	 * located in /libraries/Tagmanager
	 *
	 * @param	String	File name
	 *
	 */
	public static function autoload($file_name)
	{
		$class = 'tagmanager_' . strtolower(str_replace(EXT, '', $file_name));

		require_once APPPATH.'libraries/Tagmanager/'.$file_name;

		// Get public vars
		$vars = get_class_vars($class);

		$tag_definitions = $vars['tag_definitions'];

		foreach ($tag_definitions as $tag => $method)
		{
			// Regular tag declaration					
			self::$tags[$tag] = $class.'::'.$method;
		}
	}
	

	// ------------------------------------------------------------------------
	

	/**
	 * Autoloads tag carrying classes from modules.
	 * 
	 * @param  string	<module_name>_<tag_definition_file_name>
	 *
	 * @return bool
	 *
	 */
	public static function autoload_module_tags($class)
	{
		$class = strtolower($class);

		if(FALSE !== $p = strpos($class, '_'))
		{
			// Module name
			$module = substr($class, 0, $p);
			
			// Class file name (usually 'tags')
			$file_name = substr($class, $p + 1);
		}
		else
		{
			return FALSE;
		}

		/* If modules are installed : Get the modules tags definition
		 * Modules tags definition must be stored in : /modules/your_module/libraires/tags.php
		 * 
		 */
		if(isset(self::$module_folders[$module]))
		{
			// Only load the tags definition class if the file exists.
			if(file_exists(MODPATH.self::$module_folders[$module].'/libraries/'.$file_name.EXT))
			{
				require_once MODPATH.self::$module_folders[$module].'/libraries/'.$file_name.EXT;

				// Get tag definition class name
				$methods = get_class_methods($class);
				
				// Get public vars
				$vars = get_class_vars($class);
				
				// Store tags definitions into self::$tags
				// add module enclosing tag
				self::$tags[$module] = $class.'::index';

				foreach ($methods as $method)
				{
					$tag_name = explode('_', $method);

					if ($tag_name[0] == 'tag')
					{
						// Regular tag declaration
						$tag_name = $tag_name[1];

						// Use of module name as namespace for the module to avoid modules tags collision
						self::$tags[$module.':'.$tag_name] = $class.'::'.$method;
					}
				}

				// Load tags from the tag_definitions array : Overwrites auto-load
				$tag_definitions = ! empty($vars["tag_definitions"]) ? $vars["tag_definitions"] : array();

				foreach($tag_definitions as $scope => $method)
				{
					// Only loads scopes linked to one existing method
					if (in_array($method, $methods))
						self::$tags[$scope] = $class.'::'.$method;
				}

				return TRUE;
			}
			else
			{
				log_message('warning', 'Cannot find tag definitions for module "'.self::$module_folders[$module].'".');
			}
		}
		return FALSE;
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Adds tags from modules.
	 * 
	 * @return void
	 *
	 */
	public static function add_module_tags()
	{
		foreach(self::$tags as $selector => $callback)
		{
			self::$context->define_tag($selector, $callback);
		}
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Adds the tags for the current class and loaded classes
	 * 
	 * @param  FTL_Context
	 *
	 * @return void
	 *
	 */
	public final function add_tags()
	{
		foreach(self::$tag_definitions as $t => $m)
		{
			self::$context->define_tag($t, array(__CLASS__, $m));
		}
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Adds global tags to the context.
	 * 
	 * @param  FTL_Context
	 *
	 * @return void
	 *
	 */
	public function add_globals()
	{
		// Add all basic settings to the globals
		/*
		$settings = Settings::get_settings();	

		foreach($settings as $k=>$v)
		{
			// Do not add the languages array
			if ( ! is_array($v))
				$con->globals->$k = $v;	
		}
		*/

		// Stores vars
		// self::$context->globals->vars = array();
		
		// Global settings
		self::$context->set_global('site_title', Settings::get('site_title'));
		self::$context->set_global('google_analytics', Settings::get('google_analytics'));
		
		// Theme
		self::$context->set_global('theme', Theme::get_theme());
		self::$context->set_global('theme_url', base_url() . Theme::get_theme_path());
		
		// Current Lang code
		self::$context->set_global('current_lang', Settings::get_lang());
		
		// Menus
		self::register('menus', Settings::get('menus'));
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 * @param String
	 *
	 * @return string
	 */
	public static function parse($string)
	{
		$p = new FTL_Parser(self::$context, array('tag_prefix' => self::$tag_prefix));

		return $p->parse($string);
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 * @param null $view
	 * @param bool $return
	 *
	 * @return mixed|string
	 */
	public static function render($view = NULL, $return = FALSE)
	{
		$ci =& get_instance();

		// Loads the view to parse
		$view = ($view != NULL) ? $view : self::$view;
		$parsed = Theme::load($view);

		// We can now check if the file is a PHP one or a FTL one
		if (substr($parsed, 0, 5) == '<?php')
		{
			$parsed = $ci->load->view($view, array(), TRUE);
		}
		else
		{
			$parsed = self::parse($parsed, self::$context);

			if (Connect()->is('editors') && Settings::get('display_connected_label') == '1' )
			{
				$injected_html = $ci->load->view('core/logged_as_editor', array(), TRUE);
				
				$parsed = str_replace('</body>', $injected_html, $parsed);
			}
		}
		
		// Full page cache ?
		$page = self::registry('page');
		if (isset($page['_cached']))
		{
			/*
			 * Write the full page cache file
			 *
			 */
		}
		
		
		// Returns the result or output it directly
		if ($return)
			return $parsed;
		else
			$ci->output->set_output($parsed);

	}


	// ------------------------------------------------------------------------


	/**
	 * Adds a var to the global vars array
	 * Useful to send a variable to a tag.
	 *
	 * @param String 	Name
	 * @param String 	Value
	 *
	public static function set_global($name, $value)
	{
		self::$context->globals->vars[$name] = $value;
	}
	 */


	// ------------------------------------------------------------------------


	/**
	 *
	 *
	 * @param FTL_Binding 	tag
	 *
	 */
	public static function has_cache(FTL_Binding $tag)
	{
	} 		


	// ------------------------------------------------------------------------


	/**
	 * Cache or returns one tag cache
	 *
	 * @param	FTL_Binding		tag
	 *
	 * @return	String
	 *
	 */
	public static function get_cache(FTL_Binding $tag)
	{
		$id = self::get_tag_cache_id($tag);

		return Cache()->get($id);
	}


	// ------------------------------------------------------------------------

		
	/**
	 * Cache or returns one tag cache
	 *
	 * @param	FTL_Binding		tag
	 * @param	String
	 *
	 * @return	boolean / void
	 *
	 */
	public static function set_cache(FTL_Binding $tag, $output)
	{
		if (isset($tag->attr['nocache'])) return FALSE;
		
		$id = self::get_tag_cache_id($tag);
		
		Cache()->store($id, $output);
	} 		


	// ------------------------------------------------------------------------

	
	/**
	 * Memory request micro cache
	 * Stores one tag result in the local $_cache var
	 * Avoid calling 2 times one same process.
	 * 
	public static function set_micro_cache($tag)
	{
		$key = self::get_tag_cache_id($tag);
		
		self::$_cache[$key] = $value;
	}
	
	 */

	// ------------------------------------------------------------------------

	
	/**
	 * Returns one tag's micro cache
	 *
	public static function get_micro_cache($tag)
	{
		$key = self::get_tag_cache_id($tag);
	
		if ( ! empty(self::$_cache[$key]))
			return self::$_cache[$key];
		
		return FALSE;
	}
	 */


	// ------------------------------------------------------------------------

	
	/**
	 * Returns one tag unique ID, regarding the tag attributes
	 *
	 * @param	FTL_Binding		tag
	 *
	 * @return	String
	 *
	 */
	public static function get_tag_cache_id(FTL_Binding $tag)
	{
		if (isset($tag->attr['nocache'])) return FALSE;
	
		$ci =& get_instance();
		
		$uri =	config_item('base_url').				// replaced $ci->config->item(....
				Settings::get_lang('current').
				config_item('index_page').
				$ci->uri->uri_string();
		
		asort($tag->attr);
		
		$uri .= serialize($tag->attr);

		return $tag->name . $uri;
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Returns a dynamic attribute value
	 * Used with attributes which can get data from a database field.
	 *
	 * @param	FTL_Binding		tag
	 * @param	String			Attributes name
	 * @param	boolean			wished return. FALSE by default
	 *
	 * @return	Mixed	The attribute value of false if nothing is found
	 *
	 */
	protected static function get_attribute(FTL_Binding $tag, $attr, $return=FALSE)
	{
		// Try to get the couple array:field
		// "array" is the data array. For example "page" or "article"
		// $ar[0] : the data array name
		// $ar[1] : the field to get
		$attr = $tag->getAttribute($attr);
		if ( ! is_null($attr))
		{
			$ar = explode(':', $attr);

			// If no explode result, simply return the attribute value
			// In this case, the tag doesn't ask for a dynamic value, but just gives a value
			// (no ":" separator)
			if (!isset($ar[1]))
			{
				return $attr;
			}
	
			// Here, there is a field to get
			if (isset($tag->locals->$ar[0]))
			{
				// Element can be page, article, etc.
				$element = $tag->locals->$ar[0];
			
				// First : try to get the field in the standard fields
				// exemple : $tag->locals->_page[field]
				if ( ! isset($element[$ar[1]]))
				{
					// Second : Try to get the field in the extend fields
					// exemple : $tag->locals->_page[ion_field]
					if ( ! isset($element[self::$extend_field_prefix.$ar[1]]))
					{
						return FALSE;
					}
					else
					{
						// Try to get the value
						if ( ! empty($element[self::$extend_field_prefix.$ar[1]]))
						{
							return $element[self::$extend_field_prefix.$ar[1]];
						}
						return FALSE;
					}
				}
				else
				{
					// Try to get the value.
					// Else return false
					if ( ! empty($element[$ar[1]]))
					{
						return $element[$ar[1]];
					}
					else
					{
						return FALSE;
					}
				}
			}
		}

		return $return;
	}


	protected function registry($key)
	{
		return self::$context->registry($key);
	}


	protected function register($key, $value)
	{
		self::$context->register($key, $value);
	}




	// ------------------------------------------------------------------------
	// Tags definition
	// ------------------------------------------------------------------------




	// ------------------------------------------------------------------------


	/**
	 *
	 * @param	FTL_Binding		tag
	 *
	 * @return	string / void
	 *
	 */
	public static function tag_if(FTL_Binding $tag)
	{
		$keys = $tag->getAttribute('key');
		$expression = $tag->getAttribute('condition');

		$result = FALSE;
		self::$trigger_else = 0;

		if (!is_null($keys) && !is_null($expression))
		{
			$keys = explode('|', $keys);
			foreach($keys as $key)
			{
				$value = $tag->getValue($key);
				$expression = str_replace($key, $value, $expression);
			}

			$return = @eval("\$result = (".$expression.") ? TRUE : FALSE;");

			if ($return === NULL)
			{
				if ($result)
					return $tag->expand();
				else
				{
					self::$trigger_else++;
				}
			}
			else
			{
				return self::show_tag_error('if', 'Condition incorrect: if (' .$expression. ')');
			}
		}
		return '';
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 * @param	FTL_Binding		tag
	 *
	 * @return	String
	 *
	 */
	public function tag_else(FTL_Binding $tag)
	{
		log_message('error', 'trigger else : ' . self::$trigger_else);
		if(self::$trigger_else > 0)
		{
			self::$trigger_else--;

			return $tag->expand();
		}

		return '';
	}

	// ------------------------------------------------------------------------


	/**
	 * Stores a var int the given scope
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return String
	 *
	 * @usage	<ion:set var="foo" value="bar" scope="<local|global>" />
	 *
	 */
	public static function tag_set(FTL_Binding $tag)
	{
		$var = ( !empty ($tag->attr['var'])) ? $tag->attr['var'] : NULL;
		$scope = ( !empty ($tag->attr['scope'])) ? $tag->attr['scope'] : 'locals';
		$value = ( !empty ($tag->attr['value'])) ? $tag->attr['value'] : NULL;

		if ( ! is_null($var))
		{
			$tag->{$scope}->{$var} = $value;
		}
		
		return $value;
	}

	public static function store(FTL_Binding $tag)
	{

	}

	public static function retrieve(FTL_Binding $tag)
	{

	}

	// ------------------------------------------------------------------------
	
	
	/**
	 * Gets a stored var
	 * @usage	<ion:get var="foo" scope="<local|global>" />
	 *
	public static function tag_get(FTL_Binding $tag)
	{
		$var = ( !empty ($tag->attr['var'])) ? $tag->attr['var'] : NULL;
		$scope = ( !empty ($tag->attr['scope'])) ? $tag->attr['scope'] : 'locals';

		if ( ! is_null($var) && !empty($tag->{$scope}->vars[$var]))
		{
			return $tag->{$scope}->vars[$var];
		}
		
		return '';
	}
	 */


	// ------------------------------------------------------------------------


	/**
	 * Returns the base URL of the website, with or without lang code in the URL
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return String
	 *
	 */
	public static function tag_base_url(FTL_Binding $tag)
	{
		// Set all languages online if connected as editor or more
		if( Connect()->is('editors', TRUE))
		{
			Settings::set_all_languages_online();
		}

		if ($tag->getAttribute('lang') == TRUE)
		{
			if (count(Settings::get_online_languages()) > 1 )
			{
				// if the current lang is the default one : don't return the lang code
				if (Settings::get_lang() != Settings::get_lang('default'))
				{
					return base_url() . Settings::get_lang() .'/';
				}
			}
		}

		return base_url();
	}

	
	// ------------------------------------------------------------------------


	/**
	 * Returns one list from a given field
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	public static function tag_list(FTL_Binding $tag)
	{
		$objects = (isset($tag->attr['objects']) ) ? $tag->attr['objects'] : FALSE;
		$from = (isset($tag->attr['from']) ) ? $tag->attr['from'] : FALSE;
		$field = (isset($tag->attr['field']) ) ? $tag->attr['field'] : FALSE;
		$separator = (isset($tag->attr['separator']) ) ? $tag->attr['separator'] : ',';
		$filter = (isset($tag->attr['filter']) ) ? $tag->attr['filter'] : FALSE;
		$prefix = (isset($tag->attr['prefix']) ) ? $tag->attr['prefix'] : '';
		$filters = NULL;

		if ($objects != FALSE)
		{
			if ($from == FALSE)
			{
				$from = self::get_parent_tag($tag);
			}
			if ($from == FALSE)
			{
				$from = 'page';
			}
			
			$obj = isset($tag->locals->{$from}) ? $tag->locals->{$from} : NULL;

			if ( ! is_null($obj) && $field != FALSE)
			{
				if ( ! empty($obj[$objects]))
				{
					// Set the prefix
					$prefix = (function_exists($prefix)) ? call_user_func($prefix) : $prefix;

					// Prepare filtering
					if ($filter)
					{
						$filters = array();
						$operators = array ('!=', '=');
						
						$filter_list = explode(',', str_replace(' ', '', $filter));
						
						foreach ($operators as $op)
						{
							foreach($filter_list as $key => $fl)
							{
								$fr = explode($op, $fl);
								if ( $fr[0] !== $fl )
								{
									$filters[] = array($fr[0], $op, $fr[1]);
									unset($filter_list[$key]);
								}
							}
						}
					}
					
					$fields = array();
					foreach($filters as $filter)
					{
						// $fields += array_filter($obj[$objects], create_function('$row', 'return $row["'.$filter[0].'"]'.$filter[1].'="'.$filter[2].'";'));
						foreach($obj[$objects] as $ob)
						{
							// TODO : Rewrite
							// Because the operator isn't takken in account
							//
							// trace($ob[$filter[0]].$filter[1].'='.$filter[2]);
							$result = FALSE;
							eval("\$result = '" . $ob[$filter[0]]."'".$filter[1]."='".$filter[2]."';");

							if ($result)
								$fields[] = $ob;
						}
					}
						

					$return = array();
					foreach($fields as $row)
					{
						if ( ! empty($row[$field]))
						{
							$return[] = $prefix.$row[$field];
						}
					}
					// Safe about prefix
					unset($tag->attr['prefix']);
					return self::wrap($tag, implode($separator, $return));
				}
			}
		}
		
		return '';
	}
	*/


	// ------------------------------------------------------------------------
	
	
	/**
	 * Returns the count of an item collection
	 *
	 * @tag_attributes		'from' : 	collection name
	 *						'item' : 	items to count inside the collection
	 *						'filter' : 	Filter the items
	 * 
	 * @param			FTL_Binding		Tag
	 *
	 * @return 			Int	Number of items
	 *
	public static function tag_count(FTL_Binding $tag)
	{
		// Object type : page, article, media
		$from = (isset($tag->attr['from']) ) ? $tag->attr['from'] : self::get_parent_tag($tag);;

		// Item to count
		$items = (isset($tag->attr['items']) ) ? $tag->attr['items'] : FALSE;

		// Filter on one field
		$filter = (isset($tag->attr['filter']) ) ? $tag->attr['filter'] : FALSE;

		// Get the obj
		$obj = isset($tag->locals->{$from}) ? $tag->locals->{$from} : NULL;

		if ( ! is_null($obj) )
		{
			if($items != FALSE && isset( $obj[$items]) )
			{
				$items = $obj[$items];
			}
			else
			{
				$items = $obj;
			}
			if ($filter !== FALSE)
			{
				// Normalize egality
				$filter = preg_replace("#[=*]{1,12}#", '==', $filter);
		
				// Test condition
				$condition = preg_replace("#([\w]*)(\s*==\s*|\s*!==\s*)([a-zA-Z0-9'])#", '$row[\'\1\']\2\3', $filter);

				$items = @array_filter($items, create_function('$row','return ('.$condition.');'));

				if ($items == FALSE && ! is_array($items))
				{
					return self::show_tag_error($tag->name, '<b>Your filter contains an error : </b><br/>'.$filter);
				}
				
				return count($items);
			}
			return count($items);
		}
		return 0;
	}
	*/


	// ------------------------------------------------------------------------


	/**
	 * Get one field from a data array
	 * Used to get extended fields values
	 * First, this tag tries to get and extended field value.
	 * If nothing is found, he tries to get a core field value
	 * It is possible to force the core value by setting the "core" attribute to true
	 *
	 * @param	FTL_Binding		Tag
	 *
	 * @usage	<ion:field name="<field_name>" from="<table_name>" <core="true"> />
	 *
	 * @return	String	The field value
	 *
	public static function tag_field(FTL_Binding $tag)
	{
		// Object type : page, article, media
		$from = (isset($tag->attr['from']) ) ? $tag->attr['from'] : FALSE;
		
		// Name of the field to get
		$name = (isset($tag->attr['name']) ) ? $tag->attr['name'] : FALSE;
		
		// Format of the returned field (useful for dates)
		$format = (isset($tag->attr['format']) ) ? $tag->attr['format'] : FALSE;
		
		// Force to get the field name from core. To be used when the field has the same name as one core field
		$force_core = (isset($tag->attr['core']) && $tag->attr['core'] == TRUE ) ? TRUE : FALSE;

		// Current tag : parent tag
		if ($from == FALSE && $force_core == FALSE )
		{
			$from = self::get_parent_tag($tag);
		}

		$obj = isset($tag->locals->{$from}) ? $tag->locals->{$from} : NULL;

		if ( ! is_null($obj) && $name != FALSE)
		{
			$value = '';

			// If force core field value, return it.
			// Must be used in case one extend field has the same name than one core field.
			// @TODO : Think about one more clean solution.
			if ($force_core === TRUE && ! empty($obj[$name]))
			{
				// return self::wrap($tag, $obj[$name]);
				$value = self::get_value($from, $name, $tag);
				
				if ($value != '')
				{
					if ($format !== FALSE)
						return self::wrap($tag, self::format_date($tag, $value));
				
					return self::wrap($tag, $value);
				}
			}

			// Try to get the extend field value
			if ( isset($obj[self::$extend_field_prefix.$name]))
			{
				// If "format" attribute is defined, suppose the field is a date ...
				if ($format !== FALSE && $obj[self::$extend_field_prefix.$name] != '')
					return self::wrap($tag, (self::format_date($tag, $obj[self::$extend_field_prefix.$name])));

				return self::wrap($tag, $obj[self::$extend_field_prefix.$name]);
			}
			// Else, get the core field value
			else
			{
				// return self::wrap($tag, $obj[$name]);
				$value = self::get_value($from, $name, $tag);
				
				if ($value != '')
				{
					if ($format !== FALSE)
						return self::wrap($tag, self::format_date($tag, $value));
				
					return self::wrap($tag, $value);
				}
			}
		}

		return '';
	}
	*/


	// ------------------------------------------------------------------------

	
	/**
	 * Get the value of one tag key
	 * To be used in tag function. No direct use.
	 * Takes care about alternatives (attribute "or")
	 *
	 * @param 	String 			Local object to get the value from
	 * @param 	String 			Default asked key. If the tag if <ion:title />, this value must be 'title'
	 * @param 	FTL_Binding		The binded tag to parse
	 *
	 * @usage : $value = get_value('media', 'title', $tag)
	 *
	 * @TODO : Globalize this method ( done : used by tag_field())
	 *
	public static function get_value($obj, $key, $tag)
	{
		// thumb folder name (without the 'thumb_' prefix)
		$or = (isset($tag->attr['or']) ) ? explode(',', $tag->attr['or']) : FALSE;

		$value = ( ! empty($tag->locals->{$obj}[$key])) ? $tag->locals->{$obj}[$key] : '';

		if ($value == '' && $or !== FALSE)
		{
			foreach ($or as $alternative)
			{
				if ( ! empty($tag->locals->{$obj}[$alternative]))
				{
					$value = $tag->locals->{$obj}[$alternative];
					break;
				}
			}
		}
		
		return $value;
	}
	 */


	// ------------------------------------------------------------------------


	/**
	 * Loads a partial view from a FTL tag
	 * Callback function linked to the tag <ion:partial />
	 * 
	 * @param	FTL_Binding
	 * @return 	string
	 *
	 */
	public static function tag_partial(FTL_Binding $tag)
	{
		$view = $tag->getAttribute('view');

		// Compatibility reason
		if ( is_null($view) ) $tag->getAttribute('path');

		if ( ! is_null($view))
		{
			if( $tag->getAttribute('php') == TRUE)
			{
				$data = $tag->getAttribute('data', array());
				return self::$ci->load->view($view, $data, TRUE);
			}
			else
			{
				$file = Theme::load($view);
				return $tag->parse_as_nested($file);
			}
		}
		else
		{
			show_error('TagManager : Please use the attribute <b>"view"</b> when using the tag <b>partial</b>');
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Loads a widget
	 * Callback function linked to the tag <ion:widget />
	 * 
	 * @param	FTL_Binding
	 *
	 * @return	string
	 *
	 */
	public static function tag_widget(FTL_Binding $tag)
	{
		$name = $tag->getAttribute('name');
		
		return Widget::run($name, array_slice(array_values($tag->getAttributes()), 1));
	}


	// ------------------------------------------------------------------------


	/**
	 * Gets a translation value from a key
	 * Callback function linked to the tag <ion:translation />
	 * 
	 * @param	FTL_Binding
	 *
	 * @return 	string
	 *
	 */
	public static function tag_translation(FTL_Binding $tag)
	{
		// Kind of article : Get only the article linked to the given view
		$term = $tag->getAttribute('item');
		
		if (is_null($term))	$term = $tag->getAttribute('term');
		
		if ( ! is_null($term))
		{
			$autolink = ($tag->getAttribute('autolink') == FALSE) ? FALSE : TRUE;
		
			if (array_key_exists($term, self::$ci->lang->language) && self::$ci->lang->language[$term] != '') 
			{
				if ( ! $autolink)
					return self::wrap($tag, self::$ci->lang->language[$term]);

				return self::wrap($tag, auto_link(self::$ci->lang->language[$term], 'both', TRUE));
			}
			// Return the term index prefixed by "#" if no translation is found
			else
			{
				return '#'.$term;
			}
		}
		return '';
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Return a JSON object of all translation items and one "Lang" object which gives you access
	 * to the translations through "set" and "get" functions. 
	 *
	 * @param	FTL_Binding
	 * @return 	string
	 *
	 * @usage	Put this tag in the header / footer of your view : 
	 *			<ion:jslang [framework="jQuery" object="Lang"] />
	 *
	 *			Mootools example :
	 *
	 *			<div id="my_div"></div>
	 *
	 *			<script>
	 *				var my_text = Lang.get('my_translation_item');
	 *
	 *				$('my_div').set('text', my_text);
	 *			</script>
	 *
	 */
	public static function tag_jslang(FTL_Binding $tag)
	{
		// Returned Object name
		$object = $tag->getAttribute('object', 'Lang');

		// Files from where load the langs
		$files = ( ! is_null($tag->getAttribute('files'))) ? explode(',', $tag->getAttribute('files')) : array(Theme::get_theme());
		
		// JS framework
		$fm = $tag->getAttribute('framework', 'jQuery');
		
		// Returned language array
		$translations = array();
		
		// If $files doesn't contains the current theme lang name, add it !
		if ( ! in_array(Theme::get_theme(), $files) )
		{
			$files[] = Theme::get_theme();
		}
		
		if ((Settings::get_lang() != '') && !empty($files))
		{
			foreach ($files as $file)
			{
				$paths = array(
					APPPATH.'language/'.Settings::get_lang().'/'.$file.'_lang'.EXT,
					Theme::get_theme_path().'language/'.Settings::get_lang().'/'.$file.'_lang'.EXT
				);
				
				foreach ($paths as $path)
				{
					if (is_file($path) && '.'.end(explode('.', $path)) == EXT)
					{
						include $path;
						if ( ! empty($lang))
						{
							$translations = array_merge($translations, $lang);
							unset($lang);
						}
					}
				}
			}
		}
		$json = json_encode($translations);
		
		$js = "var $object = $json;";
		
		/*
		$.extend(Lang, {
			get: function(key) { return this[key]; },
			set: function(key, value) { this[key] = value;}
		});
		*/
		switch($fm)
		{
			case 'jQuery':
				$js .= "
					Lang.get = function (key) { return this[key]; };
					Lang.set = function(key, value) { this[key] = value;};
				";
				break;
			
			case 'mootools':
				$js .= "
					Lang.get = function (key) { return this[key]; };
					Lang.set = function(key, value) { this[key] = value;};
				";
				break;
		}
		
		return '<script type="text/javascript">'.$js.'</script>';
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Gets a config value from the CI config file
	 * Callback function linked to the tag <ion:config />
	 * 
	 * @param	FTL_Binding		The binded tag to parse
	 *
	 * @return 	string
	 *
	 * @usage	<ion:config key="<the_config_item>" />
	 *
	 * 			<ion:config key="<the_config_item>" is="<the_value">
	 * 				<p>HTML to display </p>
	 * 			</ion:config>
	 *
	 */
	public static function tag_config(FTL_Binding $tag)
	{
		$key = $tag->getAttribute('key');
		$is = $tag->getAttribute('is');
	
		if ( ! is_null($key))
		{
			if ( ! is_null($is) && config_item($key) == $is)
				return $tag->expand();
			
			return config_item($key);
		}
		return '';
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Returns on setting value
	 *
	 * @param	FTL_Binding		The binded tag to parse
	 * @return 	string
	 *
	 * @usage	<ion setting key="<the_setting_key>" />
	 *
	 */
	public static function tag_setting(FTL_Binding $tag)
	{
		// Setting item asked
		$key = $tag->getAttribute('key');

		if ( ! is_null($key))
			return Settings::get($key);

		return '';
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Returns the website title
	 * 
	 * @param  FTL_Binding
	 * @return string
	 */
	public static function tag_site_title(FTL_Binding $tag)
	{
		return self::wrap($tag, Settings::get('site_title'));
	}


	// ------------------------------------------------------------------------

	public static function tag_meta_title(FTL_Binding $tag)
	{
		$article = self::registry('article');

		if ( ! empty($article['meta_title']))
			return $article['meta_title'];

		else
		{
			$page = self::registry('page');
			if ( ! empty($page['meta_title']))
				return $page['meta_title'];
		}
		/*
		 * @TODO : Set it on the Admin side.
		 *
		 */
		return Settings::get('site_title');
	}


	/**
	 * Returns the local meta keywords if found, otherwise the global ones.
	 * 
	 * @param  FTL_Binding
	 *
	 * @return string
	 */
	public static function tag_meta_keywords(FTL_Binding $tag)
	{
		$article = self::registry('article');

		if ( ! empty($article['meta_keywords']))
			return $article['meta_keywords'];
		else
		{
			$page = self::registry('page');
			if ( ! empty($page['meta_keywords']))
				return $page['meta_keywords'];
		}

		return Settings::get('meta_keywords');
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the local meta description if found, otherwise the global ones.
	 * 
	 * @param  FTL_Binding
	 *
	 * @return string
	 */
	public static function tag_meta_description(FTL_Binding $tag)
	{
		$article = self::registry('article');

		if ( ! empty($article['meta_description']))
			return $article['meta_description'];
		else
		{
			$page = self::registry('page');
			if ( ! empty($page['meta_description']))
				return $page['meta_description'];
		}

		return Settings::get('meta_description');
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Returns one uniq number or string
	 * 
	 * @param  FTL_Binding
	 * @return string
	 */
	public static function tag_uniq(FTL_Binding $tag)
	{
		$type = $tag->getAttribute('type');
		
		if ( ! is_null($type) && $type == 'number')
			return time();
		else
			return md5(time());
	}	


	// ------------------------------------------------------------------------


	/**
	 * Browser check
	 * Checks the browser and display or not the tag content reagarding the result.
	 * 
	 * @param  	FTL_Binding		Tag
	 * @return 	string
	 *
	 * @usage	<ion:browser method="is_browser|is_mobile|is_robot|..." value="Safari|Firefox..." is="true|false" return="true">
	 *				...
	 *			</ion:browser>
	 *
	 * @see		http://codeigniter.com/user_guide/libraries/user_agent.html
	 *			for the method list
	 *
	 */
	public static function tag_browser(FTL_Binding $tag)
	{
		self::$ci->load->library('user_agent');
		
		$method = $tag->getAttribute('method');
		$value = $tag->getAttribute('value');
		$is = $tag->getAttribute('is');
		
		$return = $tag->getAttribute('return') == TRUE ? TRUE : FALSE;

		$result = NULL;
		
		if ( ! is_null($method))
		{
			if ( ! is_null($value))
				$result = self::$ci->agent->{$method}($value);
			else
				$result = self::$ci->agent->{$method}();
		}
		if ($result == $is)
		{
			if ( ! $return)
				return $tag->expand();
			else
				return $result;
		}
	}	


	// ------------------------------------------------------------------------
	
	
	/**
	 * Wraps a tag value depending on the given HTML tag
	 *
	 * @param	FTL_Binding
	 * @param	string
	 *
	 * @return 	string
	 *
	 * @usage : <ion:page:title tag="h1" class="red box" id="id" />
	 *
	 */
	protected static function wrap(FTL_Binding $tag, $value)
	{
		$open_tag = $close_tag = '';

		$html_tag = $tag->getAttribute('tag', FALSE);
		$class = $tag->getAttribute('class', '');
		$id = $tag->getAttribute('id', '');
		$prefix = $tag->getAttribute('prefix', '');
		$suffix = $tag->getAttribute('suffix', '');

		if ( ! empty($class)) $class = ' class="'.$class.'"';
		if ( ! empty($id)) $id = ' id="'.$id.'"';

		// helper
		$helper = $tag->getAttribute('helper', FALSE);

		// PHP : Process the value through the passed in function name.
		if ( ! empty($tag->attr['function'])) $value = self::php_process($value, $tag->attr['function'] );

		if ($helper !== FALSE)
			$value = self::helper_process($value, $helper);

		if ($html_tag !== FALSE)
		{
			$open_tag = '<' . $html_tag . $id . $class . '>';
			$close_tag = '</' . $html_tag .'>';
		}
		
		if ( ! empty ($value) )
			return $open_tag . $prefix . $value . $suffix . $close_tag;
		else
			return '';
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Format the given date and return the expanded tag
	 *
	 * @param	FTL_Binding		tag
	 * @param	String			date
	 *
	 * @return 	String
	 *
	 */
	protected static function format_date(FTL_Binding $tag, $date)
	{
		$date = strtotime($date);
		
		if ($date)
		{
			$format = $tag->getAttribute('format', 'Y-m-d H:i:s');

			if ($format != 'Y-m-d H:i:s')
			{
				if (lang('dateformat_'.$format) != '#dateformat_'.$format)
				{
				 	// Date translations are located in the files : /themes/your_theme/language/xx/date_lang.php
					$format = lang('dateformat_'.$format);
				}
			}

			$segments = explode(' ', $format);

			foreach($segments as $key => $segment)
			{
				$tmp = (String) date($segment, $date);

				if (preg_match('/D|l|F|M/', $segment))
					$tmp = lang(strtolower($tmp));

				$segments[$key] = $tmp;
			}
			
			return implode(' ', $segments);
		}

		return $tag->expand();
	}


	// ------------------------------------------------------------------------
	
	
	
	/**
	 * Return the parent tag name or 'page' if not found
	 *
	protected static function get_parent_tag(FTL_Binding $tag)
	{
		$tag_name = 'page';
		
		// Get the tag path
		$tag_path = explode(':', $tag->nesting());

		// Remove the current tag from the path
		array_pop($tag_path);

		// If no parent, the default parent is 'page'
		$obj_tag = (count($tag_path) > 0) ? array_pop($tag_path) : $tag_name;
		
		if ($obj_tag == 'partial') $obj_tag = array_pop($tag_path);
		
		// Parent name. Removes plural from parent tag name if any.
		if (substr($obj_tag, -1) == 's')
			$tag_name = substr($obj_tag, 0, -1);
		else if($obj_tag != '')
			$tag_name = $obj_tag;
		
		return $tag_name;
	}
	 */


	// ------------------------------------------------------------------------


	/**
	 * Process the input through the called functions and return the result
	 * 
	 * @param	Mixed				The value to process
	 * @param	String / Array		String or array of PHP functions
	 *
	 * @return	Mixed				The processed result
	 */
	protected static function php_process($value, $functions)
	{
		if ( ! is_array($functions))
			$functions = explode(',', $functions);
		
		foreach($functions as $func)
		{
			if (function_exists($func))
				$value = $func($value);
		}
		
		return $value;
	}


	// ------------------------------------------------------------------------


	/**
	 * Process the input through the called functions and return the result
	 * 
	 * @param	Mixed				The value to process
	 * @param	String / Array		String or array of PHP functions
	 *
	 * @return	Mixed				The processed result
	 */
	protected static function helper_process($value, $helper)
	{
		$helper = explode(':', $helper);

		$helper_name = ( ! empty($helper[0])) ? $helper[0] : FALSE;
		$helper_func = ( ! empty($helper[1])) ? $helper[1] : FALSE;
		
		$helper_args = ( ! empty($helper[2])) ? explode(",", $helper[2]) : array();
		
		if($helper_name !== FALSE && $helper_func !== FALSE)
		{
			self::$ci->load->helper($helper_name);
			
			array_unshift($helper_args, $value);

			if (function_exists($helper_func))
				$value = call_user_func_array($helper_func, $helper_args);
			else
				return self::show_tag_error('Tagmanager', 'Error when calling <b>'.$helper_name.'->'.$helper_func.'</b>. This helper function doesn\'t exist');
		}
		
		return $value;	
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Displays an error concerning one tag use
	 * 
	 * @param	String		Tag name (used in the template)
	 * @param	String		Message
	 * @param	String		Error template
	 *
	 * @return	String		Error message
	 *
	 */
	protected static function show_tag_error($tag_name, $message, $template = 'error_tag')
	{
		ob_start();
		include(APPPATH.'errors/'.$template.EXT);
		$buffer = ob_get_contents();

		ob_end_clean();
		return $buffer;
	}


	// ------------------------------------------------------------------------


	/**
	 * Loads a CI model
	 *
	 * @param	String		Model name to load
	 * @param	String		Logical model name
	 *
	 */
	protected static function load_model($model_name, $new_name='')
	{
		if (!isset(self::$ci->{$new_name})) self::$ci->load->model($model_name, $new_name, TRUE);
	}
}


TagManager::init();


/* End of file Tagmanager.php */
/* Location: /application/libraries/Tagmanager.php */