<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
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
	 * Current tag context
	 *
	 * @var	FTL_ArrayContext
	 */
	public static $context;

	/**
	 * Tags prefix
	 * @var string
	 */
	public static $tag_prefix = 'ion';

	public static $view = '';

	/**
	 * URI segment array
	 * @var array
	 */
	public static $uri_segments = NULL;

	/**
	 * Segment index of $ci->uri->uri_string() of the special URI
	 * @var int
	 */
	public static $special_uri_segment_index = NULL;

	/**
	 * Special URI segment array
	 * @var array
	 */
	public static $special_uri_segments = NULL;

	/**
	 * Special URI string
	 * @var string
	 */
	public static $special_uri = NULL;

	/**
	 * Special URI array of internal function / args
	 * @var null|array
	 */
	public static $special_uri_array = NULL;


	/**
	 * Information about URI : Asked object...
	 *
	 * @var null
	 *
	 */
	public static $uri_info = NULL;

	/**
	 * Entity from URL.
	 * usually 'page' or 'article'
	 *
	 * @var string
	 */
	public static $_entity = NULL;

	/**
	 * Shutdown callback
	 * Currently used if one expression evaluation hangs
	 * @var null
	 */
	public static $shutdown_callback = NULL;
	public static $shutdown_callback_args = NULL;

	/**
	 * Declared forms
	 *
	 * @var null
	 */
	public static $forms = NULL;


	/**
	 * The current posting form name if any
	 * @var null/string
	 */
	public static $posting_form_name = NULL;

	/**
	 * Array of parents for which tags are already be defined by create_extend_tags()
	 *
	 * @var array
	 */
	private static $has_extend_tags = array();

	/**
	 * Array of Extend Fields definition
	 * array
	 * (
	 * 		'extend_field_name' => Array Extend Field Definition
	 * )
	 * @var null
	 */
	private static $extends_def = array();

	private static $cache_tag_occ = array();

	protected static $html_tag_attributes = array(
		'id',
		'class',
		'dir',
		'for',
		'title',
		'lang',
		'spellcheck',
		'style',
		'tabindex',
	);

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
		'count' => 			'tag_count',
		'get_length' =>		'tag_get_length',
		'name' => 			'tag_simple_value',
		'title' => 			'tag_simple_value',
		'subtitle' => 		'tag_simple_value',
		'description' => 	'tag_simple_value',
		'date' => 			'tag_simple_date',
		'created' => 		'tag_simple_date',
		'updated' => 		'tag_simple_date',
		'extend' =>			'tag_extend',
		'list' =>			'tag_list',
		'link_type' =>		'tag_simple_value',

		// System / Core tags
		'config' => 			'tag_config',
		'base_url' =>			'tag_base_url',
		'home_url' =>			'tag_home_url',
		'lang_url' =>			'tag_lang_url',
		'uri' =>				'tag_uri',
		'uri:entity' =>			'tag_simple_value',
		'partial' => 			'tag_partial',
		'widget' =>				'tag_widget',
		'translation' => 		'tag_lang',					// Alias for <ion:lang />
		'lang' => 				'tag_lang',
		'site_title' => 		'tag_site_title',
		'meta_title' => 		'tag_meta_title',
		'meta_keywords' => 		'tag_meta_keywords',
		'meta_description' => 	'tag_meta_description',
		'google_analytics' => 	'tag_google_analytics',
		'setting' => 			'tag_setting',
		'uniq' =>				'tag_uniq',
		'if' =>					'tag_if',
		'else' =>				'tag_else',
		'set' =>				'tag_set',
		'jslang' =>				'tag_jslang',
		'browser' =>			'tag_browser',
		'session' =>			'tag_session',
		'session:set' =>		'tag_session_set',
		'session:get' =>		'tag_session_get',
		'request' =>			'tag_request',			//make it a "process" tag
		'request:post' =>		'tag_request_post',
		'request:getpost' =>	'tag_request_getpost',
		'request:get' =>		'tag_request_get',
		'attr' =>				'tag_attr',
		'partial:attr' =>		'tag_partial_attr',

		// Development tags
		'trace' =>				'tag_trace',
		'nesting' =>			'tag_nesting',

	);


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

		self::get_uri_segments();

		/*
		 * Previously : Loaded before core Tagmenagers
		 *
		// Put modules arrays keys to lowercase
		if (!empty($modules))
			self::$module_folders = array_combine(array_map('strtolower', array_values($modules)), array_values($modules));

		// Loads automatically all installed modules tags
		foreach (self::$module_folders as $module)
		{
			self::autoload_module_tags($module.'_Tags');
		}
		*/

		// Load automatically all TagManagers defined in /libraries/Tagmanager
		$tagmanagers = glob(APPPATH.'libraries/Tagmanager/*'.EXT);
		$theme_tagmanagers = glob(FCPATH.Theme::get_theme_path().'libraries/Tagmanager/*'.EXT);
		if ( ! empty($theme_tagmanagers))
			$tagmanagers = array_merge($tagmanagers, $theme_tagmanagers);

		foreach ($tagmanagers as $tagmanager)
			self::autoload($tagmanager);

		// Put modules arrays keys to lowercase
		if (!empty($modules))
			self::$module_folders = array_combine(array_map('strtolower', array_values($modules)), array_values($modules));

		// Loads automatically all installed modules tags
		foreach (self::$module_folders as $module)
		{
			self::autoload_module_tags($module.'_Tags');
		}

		self::add_globals();
		self::add_tags();
		self::add_module_tags();

		self::process_form();

		register_shutdown_function(
			array('TagManager', 'call_shutdown')
		);
	}


	// ------------------------------------------------------------------------


	/**
	 * Processes potential posted form.
	 *
	 * Done before any tag rendering, so the processing methods can set classes data before
	 * all other tags use them.
	 * Example : When the user logs in, the User() class need to set the current user
	 * so the <ion:user /> tag can get this information independently from <ion:form tag />
	 *
	 */
	public static function process_form()
	{
		// Posting form name
		if (self::$ci->input->post('form'))
			self::$posting_form_name = self::$ci->input->post('form');
		else
			return;

		// Get forms settings
		if ($f = self::$posting_form_name)
		{
			$forms = self::get_form_settings();

			if ( !empty($forms[$f]['process']))
			{
				// Create one dummy parent tag
				$tag = new FTL_Binding(self::$context, self::$context->globals, 'init', array(), NULL);

				call_user_func($forms[$f]['process'], $tag);
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the config settings for one form name
	 *
	 * @param $form_name
	 *
	 * @return null/array
	 *
	 */
	public static function get_form_settings($form_name = NULL)
	{
		if (is_null(self::$forms))
		{
			// Get forms settings
			$forms = config_item('forms');

			if (is_file($file = Theme::get_theme_path().'config/forms.php'))
			{
				include($file);
				if ( ! empty($config['forms']))
				{
					$forms = array_merge($forms, $config['forms']);
					unset($config);
				}
			}
			self::$forms = $forms;
		}

		if ( ! is_null($form_name) && isset(self::$forms[$form_name]))
		{
			return self::$forms[$form_name];
		}

		return self::$forms;
	}


	// ------------------------------------------------------------------------


	/**
	 * Autoloads tags from core TagManagers
	 * located in /libraries/Tagmanager
	 *
	 * @param	String	File name
	 *
	 */
	public static function autoload($file_path)
	{
		require_once $file_path;

		$file_name = array_pop(explode('/', $file_path));
		$class = 'tagmanager_' . strtolower(str_replace(EXT, '', $file_name));

		// Get public vars
		$vars = get_class_vars($class);

		// Merge tags definition
		$tag_definitions = $vars['tag_definitions'];

		if ( ! empty($tag_definitions))
		{
			foreach ($tag_definitions as $tag => $method)
			{
				// Regular tag declaration
				self::$tags[$tag] = $class.'::'.$method;
			}
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
		// changed strpos to strrpos - allow underscore in modules names
		if(FALSE !== $p = strrpos($class, '_'))
		{
			// Module name
			$module = substr($class, 0, $p);

			// Class file name (usually 'tags')
			// $file_name = substr($class, $p + 1);
			$file_name = strtolower($class);
		}
		else
			return FALSE;

		// If modules are installed : Get the modules tags definition
		// Modules tags definition must be stored in : /modules/your_module/libraires/<your_module>_tags.php
		$module_key = strtolower($module);
		if(isset(self::$module_folders[$module_key]))
		{
			// Only load the tags definition class if the file exists.
			if(file_exists(MODPATH.self::$module_folders[$module_key].'/libraries/'.$file_name.EXT))
			{
				require_once MODPATH.self::$module_folders[$module_key].'/libraries/'.$file_name.EXT;

				// Get tag definition class name
				$methods = get_class_methods($class);

				// Get public vars
				$vars = get_class_vars($class);

				// Store tags definitions into self::$tags
				// add module enclosing tag
				self::$tags[$module_key] = $class.'::index';

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
				log_message('warning', 'Cannot find tag definitions for module "'.self::$module_folders[$module_key].'".');
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
		// Global settings
		self::$context->set_global('site_title', Settings::get('site_title'));

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
	 * Return one formatted key from the direct parent tag or NULL if no data
	 *
	 * @param FTL_Binding $tag
	 * @param null        $key
	 *
	 * @return null|string
	 *
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
	 */


	// ------------------------------------------------------------------------


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
		// Loads the view to parse
		$view = ($view != NULL) ? $view : self::$view;

		log_message('debug', 'Tagmanager::render() : Render of the view : ' . $view);

		$parsed = Theme::load($view);

		// We can now check if the file is a PHP one or a FTL one
		if (substr($parsed, 0, 5) == '<?php')
		{
			$parsed = self::$ci->load->view($view, array(), TRUE);
		}
		else
		{
			$parsed = self::parse($parsed, self::$context);

			if (User()->logged_in() && Authority::can('access', 'admin') && Settings::get('display_connected_label') == '1')
			{
				$injected_html = self::$ci->load->view('core/logged_as_editor', array(), TRUE);

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
			self::$ci->output->set_output($parsed);

	}


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
		$cache = $tag->getAttribute('cache', TRUE);

		if ( ! $cache)
			return FALSE;

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
		$attr = $tag->getAttributes();
		asort($attr);
		$attr = json_encode($attr, true);

		$uri =	config_item('base_url').				// replaced $ci->config->item(....
				Settings::get_lang('current').
				config_item('index_page').
				self::$ci->uri->uri_string().
				':'.$tag->nesting().
				$attr
		;

		$uri_idx = base64_encode($uri);

		if (empty(self::$cache_tag_occ[$uri_idx]))
			self::$cache_tag_occ[$uri_idx] = 1;
		else
			self::$cache_tag_occ[$uri_idx] += 1;

		$uri = $uri_idx . self::$cache_tag_occ[$uri_idx];

		return $uri;
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
	*/


	protected function registry($key, $array = NULL)
	{
		return self::$context->registry($key, $array);
	}


	protected function register($key, $value, $array = NULL)
	{
		self::$context->register($key, $value, $array);
	}


	// ------------------------------------------------------------------------


	/**
	 * Return the internal special URI code
	 * See config/ionize.php -> $config['special_uri']
	 *
	 * Also sets : self::$special_uri_segment
	 *
	 * Archives : 	page/subpage/archive/2012/07 : segments -2
	 * Category : 	page/subpage/category/webdesign : segments -1
	 * Pagination : page/subpage/page/5 : segments -1
	 *
	 */
	public static function get_special_uri()
	{
		if ( is_null(self::$special_uri))
		{
			$uri_config = self::$ci->config->item('special_uri');
			$segments = self::get_uri_segments();
			$segment_index = count($segments) - 1;

			while( ! empty($segments))
			{
				$segment = array_pop($segments);

				if (array_key_exists($segment, $uri_config))
				{
					self::$special_uri_segment_index = $segment_index;
					self::$special_uri = $uri_config[$segment];
					break;
				}
				$segment_index--;
			}
		}
		return self::$special_uri;
	}

	// ------------------------------------------------------------------------


	/**
	 * Return the special URI segment index regarding to self::$ci->uri->segment_array()
	 * When the key isn't passed, the function tries to get one special URI
	 * from all the keys stored in the config file and stores the result.
	 * IMPORTANT : To not set the key is to use with care.
	 *
	 * @param	string		Special URI key.
	 * @return 		int		Segment index
	 *
	 *
	 */
	public static function get_special_uri_segment_index($key = NULL)
	{
		// Return the index from $key
		if ( ! is_null($key))
		{
			// $uri_config = self::$ci->config->item('special_uri');
			$searched_segment = self::get_config_special_uri_segment($key);
			$segments = self::get_uri_segments();
			$segment_index = count($segments) - 1;

			while( ! empty($segments))
			{
				$segment = array_pop($segments);

				if ($segment == $searched_segment)
				{
					return $segment_index;
				}
				$segment_index--;
			}
			return NULL;
		}
		// Try to find one special URI and stores its index.
		else
		{
			if ( is_null(self::$special_uri))
			{
				self::get_special_uri();
			}
			return self::$special_uri_segment_index;
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the special URI segments.
	 * If no key is passed, returns the first found special uri segments.
	 *
	 * Example : 	URI string : /my_page/archives/2012/08
	 *				Returned segments :
	 * 				array(2012, 08)
	 *
	 * @param 	string $key
	 *
	 * @return 	array|null
	 *
	 */
	public static function get_special_uri_segments($key = NULL)
	{
		if ( ! is_null($key))
		{
			$index = self::get_special_uri_segment_index($key);
			$segments = array_slice(self::get_uri_segments(), $index + 1);
			return $segments;
		}
		else
		{
			if ( is_null(self::$special_uri_segments))
			{
				self::$special_uri_segments = array_slice(self::get_uri_segments(), self::get_special_uri_segment_index() + 1);
			}
			return self::$special_uri_segments;
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Return the special URI array or just one special URI args array.
	 *
	 * /application/config/ionize.php defines the special URIs and the corresponding user's chosen URIs :
	 * $config['special_uri'] = array(
	 *		'user_chosen_uri' => 'internal_uri'
	 * );
	 *
	 * This method checks the URL segments, looks for "user_chosen_uri" and returns one array containing,
	 * for each "internal_uri", the asked args.
	 * Example of return :
	 * array(
	 * 		'internal_uri' => array(
	 * 			0 => 'foo'
	 * 			1 => 5
	 * 		)
	 * );
	 *
	 * @param null|string	If set, return the args array of the given "internal_uri"
	 *
	 * @return array|null
	 *
	 */
	public static function get_special_uri_array($key = NULL)
	{
		if (is_null(self::$special_uri_array))
		{
			$uri_config = self::$ci->config->item('special_uri');
			$segments = self::get_uri_segments();
			$segment_index = count($segments) - 1;

			$found_uri_segments = array();

			while( ! empty($segments))
			{
				$segment = array_pop($segments);

				if (array_key_exists($segment, $uri_config))
				{
					if (is_null(self::$special_uri_array))
						self::$special_uri_array = array();

					self::$special_uri_array[$uri_config[$segment]] = array_reverse($found_uri_segments);
					$found_uri_segments = array();
				}
				else
				{
					$found_uri_segments[] = $segment;
				}
				$segment_index--;
			}
			if (is_array(self::$special_uri_array))
				self::$special_uri_array = array_reverse(self::$special_uri_array);
		}

		if ( ! is_null($key) && is_array(self::$special_uri_array) && array_key_exists($key, self::$special_uri_array))
			return self::$special_uri_array[$key];

		return self::$special_uri_array;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the current segment array, without the default controller and function names
	 *
	 * @return string|null
	 *
	 */
	public static function get_uri_segments()
	{
		if ( is_null(self::$uri_segments))
		{
			$segments = self::$ci->uri->segment_array();
			$segments = array_slice($segments, 2);
			self::$uri_segments = $segments;
		}
		return self::$uri_segments;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the special URI as it is set in $config['special_uri']
	 * for one given special URI code
	 *
	 * see : /application/config/ionize.php
	 *
	 * @param 	string
	 *
	 * @return	string
	 *
	 */
	public static function get_config_special_uri_segment($key)
	{
		$uri_config = array_flip(self::$ci->config->item('special_uri'));

		if (isset($uri_config[$key]))
			return $uri_config[$key];

		return NULL;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the current entity asked by the URL ('page' or 'article')
	 *
	 * @return array
	 *
	 */
	public function get_entity()
	{
		if ( is_null(self::$_entity))
		{
			self::$_entity = self::$ci->url_model->get_by_url(self::$ci->uri->uri_string());
		}
		return self::$_entity;
	}


	// ------------------------------------------------------------------------
	// Tags definition
	// ------------------------------------------------------------------------


	/**
	 * Used for all tags which must return one tag stored value.
	 * @TODO : Check if really needed
	 *
	 * Stored value :
	 * Value set with : $tag->set('my_value', $my_value);
	 *
	 * @param 	FTL_Binding $tag
	 *
	 * @return 	string
	 *
	 */
	public static function tag_stored_value(FTL_Binding $tag)
	{
		$key = $tag->name;
		$is = $tag->getAttribute('is');
		$expression = $tag->getAttribute('expression');

		$value = $tag->get($key);

		if ( ! is_null($value))
			return self::wrap($tag, $value);
	}


	// ------------------------------------------------------------------------


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
	 *
	 * 			Test mode :
	 * 			<ion:language:code is="fr">This will be displayed if code = 'fr'</ion:language:code>
	 *
	 * 			Expression test mode :
	 * 			<ion:articles:article:index expression="index%3==0">
	 * 				This will be displayed every 3 articles
	 * 			</ion:articles:article:index>
	 *
	 * @note	The tag is supposed to have one data array which has the
	 * 			same name than the tag's parent.
	 * 			In the above example :
	 * 			- The tag <ion:name /> is looking for one data array called 'language'
	 * 			- In this data array, the tag <ion:name /> will return the 'name' index
	 *
	 */
	public static function tag_simple_value(FTL_Binding $tag)
	{
		// Optional : data array from where to get the data
		$from = $tag->getAttribute('from');

		// 1. Try to get from tag's data array
		$value = $tag->getValue(NULL, $from);

		// 2. Fall down to tag locals storage
		if (is_null($value))
			$value = $tag->get($tag->name);
		else
		{
			// Add to local storage, so other tags can use it
			$tag->set($tag->name, $value);
		}

		return self::output_value($tag, $value);
	}


	// ------------------------------------------------------------------------


	public static function tag_simple_content(FTL_Binding $tag)
	{
		// Optional : data array from where to get the data
		$from = $tag->getAttribute('from');

		// 1. Try to get from tag's data array
		$value = $tag->getValue(NULL, $from);

		// 2. Fall down to tag locals storage
		if (is_null($value))
			$value = $tag->get($tag->name);
		else
		{
			// Add to local storage, so other tags can use it
			$tag->set($tag->name, $value);
		}

        $int_link = strpos($value, '{{');

        if (FALSE !== $int_link)
		{
            $value = self::$ci->url_model->parse_internal_links(
				$value,
				$tag->getAttribute('link_key'),
				$tag->getAttribute('link_title')
			);
		}

		self::load_model('media_model');
		$value = self::$ci->media_model->parse_content_media_url($value);

		$autolink = $tag->getAttribute('autolink', TRUE);
		if ( $autolink)
			$value = auto_link($value);

		return self::output_value($tag, $value);
	}


	// ------------------------------------------------------------------------


	public static function tag_simple_date(FTL_Binding $tag)
	{
		// Optional : data array from where to get the data
		$from = $tag->getAttribute('from');

		$value = $tag->getValue(NULL, $from);

		$value = self::format_date($tag, $value);

		// Add to local storage, so other tags can use it
		$tag->set($tag->name, $value);

		// Process PHP, helper, prefix/suffix
		$value = self::process_value($tag, $value);

		if ( ! is_null($value))
			return self::wrap($tag, $value);

		return $value;
	}


	// ------------------------------------------------------------------------


	/**
	 * Sets the extend tag parent
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 * @usage	<ion:page:extend />
	 * 			<ion:article:extend />
	 */
	public static function tag_extend(FTL_Binding $tag)
	{
		$parent = $tag->getParentName();

		$tag->set('__extend_parent__', $parent);

		return $tag->expand();
	}


	// ------------------------------------------------------------------------


	/**
	 * Individual extend tag
	 * Stores the value 'extend' : Definition of the asked Extend Field
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 * @usage	<ion:extend:extend_name />
	 *
	 */
	public static function tag_extend_field(FTL_Binding $tag)
	{
		$extend_field_name = $tag->getName();

		if (isset(self::$extends_def[$extend_field_name]))
		{
			$extend = self::$extends_def[$extend_field_name];

			$tag->set('extend', $extend);

			return $tag->expand();
		}
		return '';
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns one key of the Extend Field definition array
	 * Generic tag defined by self::create_extend_tags()
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 * @usage	<ion:extend:extend_name:label />
	 * 			<ion:extend:extend_name:type />
	 * 			<ion:extend:extend_name:default_value />
	 *
	 */
	public static function tag_extend_field_definition_key(FTL_Binding $tag)
	{
		$extend = $tag->get('extend');
		$key = $tag->getName();

		$value = isset($extend[$key]) ? $extend[$key] : NULL;

		if ( ! is_null($value))
		{
			return self::output_value($tag, $value);
		}

		return '';
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns one Extend Field value
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_extend_field_value(FTL_Binding $tag)
	{
		// Extend Field Definition, as set by self::create_extend_tags()
		$extend = $tag->get('extend');
		$key = $extend['name'];

		$value = $tag->getValue(self::$extend_field_prefix . $key, $extend['parent']);

		/*
		 * $extend['type'] possible values :
		 *  1 : Input
		 *  2 : Textarea
		 *  3 : Textarea with Editor
		 *  4 : Checkboxes
		 *  5 : Radio boxes
		 *  6 : Select
		 *  7 : Datetime
		 */
		switch ($extend['type'])
		{
			// TextArea
			case '2':
			case '3':
				$value = self::$ci->url_model->parse_internal_links(
					$value,
					$tag->getAttribute('link_key'),
					$tag->getAttribute('link_title')
				);
				self::load_model('media_model');
				$value = self::$ci->media_model->parse_content_media_url($value);
				break;

			case '4':
				break;

			case '7':
				$value = self::format_date($tag, $value);
				break;

			default:
				break;
		}

		$tag->set($tag->getName(), $value);

		return self::output_value($tag, $value);
	}


	// ------------------------------------------------------------------------


	/**
	 * Available options for the Extend Field if his type is checkbox, radiobox, select
	 *
	 * @param 	FTL_Binding $tag
	 * @return 	string
	 *
	 */
	public static function tag_extend_field_options(FTL_Binding $tag)
	{
		$str = '';

		$extend = $tag->get('extend');

		if (in_array($extend['type'], array('4', '5', '6')))
		{
			$all_values = explode("\n", $extend['value']);

			foreach($all_values as $value)
			{
				$val_label = explode(':', $value);
				$tag->set('value', $val_label[0]);
				$tag->set('label', $val_label[1]);
				$str .= self::wrap($tag, $tag->expand());
			}
		}
		return $str;
	}


	/**
	 *
	 * @TODO : 	Modify this method for the 1.0 release
	 * 			Should return the select, radio, checkbox values
	 *
	 * @param 	FTL_Binding $tag
	 * @return	string
	 */
	public static function tag_extend_field_values(FTL_Binding $tag)
	{
		$str = '';

		$extend = $tag->get('extend');

		if (in_array($extend['type'], array('4', '5', '6')))
		{
			// All available values for this multi-value field
			$all_values = explode("\n", $extend['value']);
			$values = array();

			foreach($all_values as $value)
			{
				$val_label = explode(':', $value);
				$values[$val_label[0]] = $val_label[1];
			}
			// Values selected by the user
			$selected_values = explode(',', $tag->getValue(self::$extend_field_prefix . $extend['name'], $extend['parent']));

			foreach($selected_values as $selected_value)
			{
				foreach($values as $value => $label)
				{
					if ($value == $selected_value)
					{
						$tag->set('value', $value);
						$tag->set('label', $label);
						$str .= self::wrap($tag, $tag->expand());
					}
				}
			}
		}

		return $str;
	}


	public static function tag_extend_field_medias(FTL_Binding $tag)
	{
		$str = '';

		$grandParentName = $tag->getParent()->getParentName();

		// Medias asked aren't supposed to be the one linked to the extend
		if ( ! in_array($grandParentName, array('items', 'item', 'extend')))
		{
			return TagManager_Media::tag_medias($tag);
		}
		else
		{
			$extend = $tag->get('extend');

			// Medias
			if ($extend['type'] == '8')
			{
				self::load_model('media_model');

				// Static items already have the 'content' index set.
				// Classical extends have not
				$ids = ! empty($extend['content']) ?
					$extend['content'] :
					$tag->getValue(self::$extend_field_prefix . $extend['name'], $extend['parent']);

				if (strlen($ids) > 0)
				{
					// Tag attributes
					$type = $tag->getAttribute('type');
					$limit = $tag->getAttribute('limit', 0);
					$filter = $tag->getAttribute('filter');

                    if( ! is_null($filter) )
                        $filter = self::process_filter($filter);

					$ids_array = explode(',', $ids);

					$where = array(
						'where_in' => array(self::$ci->media_model->get_table().'.id_media' => $ids_array),
						'order_by' => "field(" . self::$ci->media_model->get_table() . ".id_media, ". $ids . ")"
					);

					if ( ! is_null($type)) $where['type'] = $type;
					if ( $limit ) $where['limit'] = $limit;

					$medias = self::$ci->media_model->get_lang_list(
						$where,
						Settings::get_lang('current'),
						$filter
					);

					//
					// From here :
					// Same than TagManager_Media::tag_medias()
					//

					// Extend Fields tags
					self::create_extend_tags($tag, 'media');

					// Medias lib, to process the "src" value
					self::$ci->load->library('medias');

					// Filter the parent's medias
					$medias = TagManager_Media::filter_medias($tag, $medias);

					$count = count($medias);
					$tag->set('count', $count);

					// Make medias in random order
					if ( $tag->getAttribute('random') == TRUE) shuffle ($medias);

					// Process additional data : src, extension
					foreach($medias as $key => $media)
					{
						if ($media['provider'] !='')
							$src = $media['path'];
						else
							$src = base_url() . $media['path'];

						if ($media['type'] == 'picture')
						{
							$settings = TagManager_Media::get_src_settings($tag);

							if ( ! empty($settings['size']))
								$src = self::$ci->medias->get_src($media, $settings, Settings::get('no_source_picture'));
						}
						$medias[$key]['src'] = $src;
					}

					$tag->set('medias', $medias);

					foreach($medias as $key => $media)
					{
						// Each media has its index and the number of displayed media
						$media['index'] = $key + 1;
						$media['count'] = $count;

						$tag->set('media', $media);
						$tag->set('count', $count);
						$tag->set('index', $key);

						$str .= $tag->expand();
					}
				}
				return self::wrap($tag, $str);
			}
		}

		return $str;
	}


	public static function tag_extend_field_links(FTL_Binding $tag)
	{
		$str = '';

		$extend = $tag->get('extend');

		// Link
		if ($extend['html_element_type'] == 'link')
		{
			// Static items already have the 'content' index set.
			// Classical extends have not
			$ids = ! empty($extend['content']) ?
				$extend['content'] :
				$tag->getValue(self::$extend_field_prefix . $extend['name'], $extend['parent']);

			// Static items && Elements : The extend field instance is known.
			if ( ! empty($extend['content']))
			{
				$ids = $extend['content'];
				$id_parent = $extend['id_parent'];
			}
			// Classical extends : The extend instance is unknown
			else
			{
				$dataParent = $tag->getParent()->getParent()->getParent()->getData();
				$id_parent = ! empty($dataParent['id']) ?
					$dataParent['id'] :
					( ! empty($dataParent['id'.$extend['parent']]) ? $dataParent['id'.$extend['parent']] : NULL);
			}


			if (strlen($ids) > 0)
			{
				self::load_model('extend_field_model');

				$limit = $tag->getAttribute('limit', 0);
				$where = array();
				if ( $limit ) $where['limit'] = $limit;

				$links = self::$ci->extend_field_model->get_extend_link_list(
					$extend['id_extend_field'],
					$extend['parent'],
					$id_parent,
					Settings::get('current'),
					$where
				);

				$count = count($links);
				$tag->set('count', $count);

				if ( count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1' )
					$base_url = base_url() . Settings::get_lang('current'). '/';
				else
					$base_url = base_url();

				foreach($links as $key => $link)
				{
					$target_uri = ! empty($link['target_url']) ? $link['target_url'] : $link['entity_url'];
					$links[$key]['absolute_url'] = $base_url . $target_uri;
				}

				foreach($links as $key => $link)
				{
					$links[$key]['medias'] = $link['data']['medias'];
				}
				$tag->set('links', $links);

				foreach($links as $idx => $link)
				{
					// Each media has its index and the number of displayed media
					$link['index'] = $idx + 1;
					$link['count'] = $count;
					$tag->set('count', $count);
					$tag->set('index', $link['index']);

					// Medias
					$link['medias'] = $link['data']['medias'];
					$tag->set('links', $link);

					foreach($link as $key => $value)
					{
						$tag->set($key, $value);
					}

					$tag->set('url', $link['absolute_url']);

					$str .= $tag->expand();
				}
			}
			return self::wrap($tag, $str);
		}

		return $str;
	}


	/**
	 * Simply expand the tag.
	 * If declared as tag_expand, the tag will simply expand its children
	 *
	 * @param 	FTL_Binding
	 *
	 * @return 	string
	 *
	 * @usage	In tag definition array
	 *
	 */
	public static function tag_expand(FTL_Binding $tag)
	{
		return $tag->expand();
	}


	// ------------------------------------------------------------------------


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

		return self::wrap($tag, $value);
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the object absolute's URL
	 *
	 * @param 	FTL_Binding $tag
	 *
	 * @return 	string
	 *
	 * @usage	<ion:url [href="TRUE" title="title/name" attributes="rel=3" popup="TRUE" ] />
	 *
	 */
	public static function tag_url(FTL_Binding $tag)
	{
		// Optional : data array from where to get the data
		$value = NULL;
		$from = $tag->getAttribute('from');
		$type = $tag->getAttribute('type');
		$lang = $tag->getAttribute('lang');

		// 1. Try to get from tag's data array
		//	if ( ! is_null($from))
		$value = $tag->getValue('absolute_url', $from);

		// 2. Try to get from parent tag
		if (is_null($value))
		{
			$parent = $tag->getParent();
			$value = $parent->getValue('absolute_url');
		}

		if ( ! is_null($type) && ! is_null($value))
		{
			switch($type)
			{
				case 'relative':
					$value = str_replace(self::get_base_url(), '', $value);
					if (
						! is_null($lang)
						 && (count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1')
					)
						$value = Settings::get_lang('current') . '/' . $value;
					break;

				case 'element':
					$value = explode('/', $value);
					$value = array_pop($value);
					if (
						! is_null($lang)
						&& (count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1')
					)
						$value = Settings::get_lang('current') . '/' . $value;
					break;
			}
		}

		// 2. Fall down to tag locals storage
		if (is_null($value))
		{
			$value = $tag->getValue();
		}
		else
		{
			// Add to local storage, so other tags can use it
			$tag->set($tag->name, $value);
		}

		// No data array has any URL : Return the page or article URL.
		if (is_null($value))
		{
			$page = self::registry('page');
			$article = self::registry('article');
			if ( ! empty($article))
			{
				$value = $article['url'];
			}
			else if ( ! empty($page))
			{
				$value = $page['absolute_url'];
			}
		}

		// Creates one A HTML element if the tag attribute "href" is set to true
		$value = self::create_href($tag, $value);

		return self::output_value($tag, $value);
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns information about the current URI
	 * - Kind of object
	 *
	 * @param FTL_Binding $tag
	 *
	 */
	public static function tag_uri(FTL_Binding $tag)
	{
		if (is_null(self::$uri_info))
		{
			$entity = self::get_entity();

			self::$uri_info = array(
				'entity' =>  $entity['type']
			);
		}
		$tag->set('uri', self::$uri_info);

		return $tag->expand();
	}


	// ------------------------------------------------------------------------


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

		// Value set through <ion:set ../>
		$value = $tag->get($key, 'global');

		// Try to get the Extend Field value
		if (is_null($value))
		{
			$key = $tag->getAttribute('key');
			$value = $tag->getValue($key);
		}

		// If value is array then need an item or tag expand
		if(is_array($value))
		{
			$item = $tag->getAttribute('item');

			// If has the item in array
			if(array_key_exists($item, $value))
			{
				$value = $value[$item];

			}
			elseif($item != "")
			{
				// If no has but have set one item key
				$value = "bad array key";
			}
			else
			{
				// If no have set item key then expand with data
				$array = $value; $value = "";

				foreach($array as $key => $val)
				{
					$tag->set('key', $key);
					$tag->set('value', $val);

					$value .= $tag->expand();
				}
			}
		}

		return self::output_value($tag, $value);
	}




	// ------------------------------------------------------------------------


	/**
	 * Return the number of elements in the data array.
	 * The data array correponding tag has to :
	 * 1. Set the 'count' value
	 * 2. Test if the attribute 'loop' is set and not loop if TRUE
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return null|string
	 *
	 */
	public static function tag_count(FTL_Binding $tag)
	{
		if ($tag->getParent()->get('__loop__'.$tag->nesting()) !== FALSE)
		{
			$tag->getParent()->set('__loop__'.$tag->nesting(), FALSE);
			return self::tag_simple_value($tag);
		}

		return '';
	}


	// ------------------------------------------------------------------------


	/**
	 * @TODO : Replace with the expression test with the most generic eval_expression() method
	 * @param	FTL_Binding		tag
	 *
	 * @return	string / void
	 *
	 */
	public static function tag_if(FTL_Binding $tag)
	{
		// Set this tag as "process tag"
		$tag->setAsProcessTag();

		$keys = $tag->getAttribute('key');
		$expression = $_orig_expression = $tag->getAttribute('expression');

		$return = NULL;
		$result = FALSE;

		// Make an array from keys
		$keys = explode(',', $keys);
		$test_value = NULL;

		foreach($keys as $idx => $key)
		{
			$key = trim($key);

			// 1. Try to get the value from tag's data array
			$value = $tag->getValue($key);

			// 2. Fall down to to tag's locals
			if (is_null($value))
				$value = $tag->get($key);

			if ($idx == 0 && strpos($expression, $key) === FALSE)
				$expression = $key . $expression;

			if (is_string($value))
				$value = addslashes($value);

			$test_value = (is_string($value) OR is_null($value)) ? "'".$value."'" : $value;

			$expression = str_replace($key, $test_value, $expression);

		}

		// If at least one tested value was not NULL
		if ( ! is_null($test_value))
		{
			$return = @eval("\$result = (".$expression.") ? TRUE : FALSE;");
		}
		if ($return === NULL OR is_null($test_value))
		{
			if ($result)
			{
				if (self::$trigger_else > 0)
					self::$trigger_else = 0;
				return self::wrap($tag, $tag->expand());
			}
			else
				self::$trigger_else++;
		}
		else
		{
			return self::show_tag_error($tag, 'Condition incorrect: ' .$_orig_expression);
		}
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
		// Set this tag as "process tag"
		$tag->setAsProcessTag();

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
	 * @return void
	 *
	 * @usage	<ion:set var="foo" value="bar" />
	 *
	 */
	public static function tag_set(FTL_Binding $tag)
	{
		$tag->set($tag->getAttribute('key'), $tag->getAttribute('value'), 'global');
	}


	// ------------------------------------------------------------------------


	/**
	 * Expands the tag content if the element is active
	 *
	 * @param 	FTL_Binding $tag
	 *
	 * @return 	string
	 *
	 * @usage	In tag method :
	 *			$tag->set('is_active', TRUE);
	 *
	 * 			In views :
	 * 			<ion:my_tag:is_active>
	 * 				This will be displayed if active
	 * 			</ion:my_tag:is_active>
	 *
	 * 			<ion:my_tag:is_active is='false' >
	 * 				This will be displayed if not active
	 * 			</ion:my_tag:is_active>
	 *
	 *
	 */
	public static function tag_is_active(FTL_Binding $tag)
	{
		$is_active = ($tag->getAttribute('is') === FALSE) ? FALSE : TRUE;

		if ($is_active == $tag->get('is_active') OR $is_active == $tag->getValue('is_active'))
			return $tag->expand();

		return '';
	}


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
		return base_url();
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the Home URL
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_home_url(FTL_Binding $tag)
	{
		return self::get_home_url();
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the lang URL
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_lang_url(FTL_Binding $tag)
	{
		// Set all languages online if connected as editor or more
		if (Authority::can('access', 'admin') && Settings::get('display_front_offline_content') == 1)
		{
			Settings::set_all_languages_online();
		}

		if (count(Settings::get_online_languages()) > 1 )
		{
			return base_url() . Settings::get_lang() .'/';
		}

		return base_url();
	}


	// ------------------------------------------------------------------------

	/**
	 * Returns the parent tag collection items in list
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_list(FTL_Binding $tag)
	{
		// Stop is already processed
		if ($tag->getParent()->get('__loop__') === FALSE)
			return '';

		$data = array();

		// Collection to consider : parent tag name
		$collection_name = $tag->getParentName();
		$collection = $tag->get($collection_name);

		// HTML Separator of each collection item
		$separator = $tag->getAttribute('separator', ', ');

		if ( ! empty($collection))
		{
			// Create one HTML A element, pointing to the element item URL, if any.
			$link = $tag->getAttribute('link', FALSE);

			// Field to return for each collection item.
			$key =  $tag->getAttribute('key', 'title');

			// Child tag : HTML tag for each collection item
			$child_tag =  $tag->getAttribute('child-tag');
			$child_class =  $tag->getAttribute('child-class');

			// Separator attribute is not compatible with child-tag
			if ( ! is_null($child_tag))
				$separator = FALSE;

			// Build the anchor array
			foreach($collection as $item)
			{
				// Return something if the item key exists
				if (isset($item[$key]))
				{
					$value = $item[$key];

					if ($link == TRUE && isset($item['url']))
					{
						$value = anchor($item['url'], $value);
					}

					if ( ! is_null($child_tag))
					{
						// Replace the class and tag by the child tag & class
						$html_tag =  $tag->getAttribute('tag');
						$html_class =  $tag->getAttribute('class');

						$tag->setAttribute('tag', $child_tag);
						$tag->setAttribute('class', $child_class);

						// Process the child rendering
						$value = self::wrap($tag, $value);

						// Restore the tag & class for parent
						$tag->setAttribute('tag', $html_tag);
						$tag->setAttribute('class', $html_class);
					}

					$data[] = $value;
				}
			}
		}

		// Lock loop
		$tag->getParent()->set('__loop__', FALSE);

		return self::output_value($tag, implode($separator, $data));
	}


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
		// Set this tag as "process tag"
		$tag->setAsProcessTag();

		$view = $tag->getAttribute('view');

		// Compatibility reason
		if ( is_null($view) ) $tag->getAttribute('path');

		if ( ! is_null($view))
		{
			$attributes = $tag->getAttributes();
			foreach($attributes as $key => $value)
			{
				self::register($key, $value, 'attr');
			}

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


	public static function tag_attr(FTL_Binding $tag)
	{
		$attr_key = $tag->getAttribute('key');

		$attributes = $tag->getParent()->getAttributes();

		$value = isset($attributes[$attr_key]) ? $attributes[$attr_key]  : '';

		return self::output_value($tag, $value);
	}


	// ------------------------------------------------------------------------


	public static function tag_partial_attr(FTL_Binding $tag)
	{
		$attr_key = $tag->getAttribute('key');

		$value = self::registry($attr_key, 'attr');

		return self::output_value($tag, $value);
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
	public static function tag_lang(FTL_Binding $tag)
	{
		// Kind of article : Get only the article linked to the given view
		$key = $tag->getAttribute('key');

		if (is_null($key)) $key = $tag->getAttribute('term');
		if (is_null($key)) $key = $tag->getAttribute('item');

		$swap = $tag->getAttribute('swap');
		if ( ! is_null($swap))
			$swap = self::get_lang_swap($tag, $swap);

		if ( ! is_null($key))
		{
			$random = $tag->getAttribute('random');

			if ( ! is_null($random))
			{
				$keys = explode(',', $key);
				$index = rand(0, sizeof($keys)-1);
				$key = trim($keys[$index]);
			}

			$autolink = $tag->getAttribute('autolink', TRUE);

			$line = lang($key, $swap);

			if ( ! $autolink)
				return self::wrap($tag, $line);

			return self::wrap($tag, auto_link($line, 'both', TRUE));
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


	/**
	 * Returns the current meta title
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 * @usage	<ion:meta_title with_site_title="true" position="before/after" separator=" - " />
	 *
	 */
	public static function tag_meta_title(FTL_Binding $tag)
	{
		$title = NULL;

		$page = self::registry('page');
		$article = self::registry('article');

		$with_site_title = $tag->getAttribute('with_site_title');

		if ( ! empty($article))
		{
			if ( ! empty($article['meta_title']))
				$title = $article['meta_title'];
			else if ( ! empty($article['title']))
				$title = $article['title'];
		}
		else
		{
			if ( ! empty($page['meta_title']))
				$title = $page['meta_title'];
			else if ( ! empty($page['title']))
				$title = $page['title'];
		}

		if ( $with_site_title )
		{
			$position = $tag->getAttribute('position', 'before');
			$separator =$tag->getAttribute('separator', ' - ');

			if ( ! is_null($title))
			{
				if ($position == 'before')
				{
					$title = Settings::get('site_title') . $separator . $title;
				}
				else
				{
					$title =  $title . $separator . Settings::get('site_title');
				}
			}
			else
			{
				$title = Settings::get('site_title');
			}
		}

		$title = strip_tags($title);

		return $title;
	}


	// ------------------------------------------------------------------------


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

		$description = NULL;

		if ( ! empty($article['meta_description']))
			$description = $article['meta_description'];
		else
		{
			$page = self::registry('page');
			if ( ! empty($page['meta_description']))
				$description = $page['meta_description'];
		}

		if ( is_null($description))
			$description = Settings::get('meta_description');

		return $description;
	}


	// ------------------------------------------------------------------------


	public static function index(FTL_Binding $tag)
	{
		$str = $tag->expand();
		return $str;
	}


	/**
	 * Returns the Google Analytics Tracking code
	 * View : /application/views/google/tracking
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return mixed
	 *
	 */
	public static function tag_google_analytics(FTL_Binding $tag)
	{
		if (ENVIRONMENT == 'production')
		{
			$tracking_id = Settings::get('google_analytics_id');

			// Load the tracking view
			if ($tracking_id != FALSE && $tracking_id != '')
			{
				$html = self::$ci->load->view(
					'google/tracking',
					array('tracking_id' => $tracking_id),
					TRUE
				);
				return $html;
			}
			// Returns the complete tracking code
			else
			{
				return Settings::get('google_analytics');
			}
		}
		return '';
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

		$return = $tag->getAttribute('return') == FALSE ? FALSE : TRUE;

		$result = NULL;

		if ( ! is_null($method))
		{
			if ( ! is_null($value))
				$result = self::$ci->agent->{$method}($value);
			else
				$result = self::$ci->agent->{$method}();
		}
		else
		{
			$result = self::$ci->agent->browser();
		}

		// set the value
		$tag->set('browser', $result);
		$tag->expand();
		return self::output_value($tag, $result);
	}



	// ------------------------------------------------------------------------

	/**
	 * Sets the <ion:request>-tag as process tag
	 *
	 * Expands the tag so that sub requests like <ion:request:get ...> can
	 * get contain is/expression-fields
	 *
	 */
	public static function tag_request(FTL_Binding $tag) {
		// Set this tag as "process tag"
		$tag->setAsProcessTag();
		return $tag->expand();
	}

	// ------------------------------------------------------------------------

	/**
	 * returns get/post values
	 *
	 * is a helper function for internal use, return values are XSS-cleaned
	 */
	private static function _tag_request_getpost(FTL_Binding $tag, $mode="post") {
		$key = $tag->getAttribute('key');

		if( is_null($key) )
			return '';

		if( $mode=="post" )
			return self::output_value($tag, self::$ci->input->post($key, true));
		elseif( $mode=="getpost" )
			return self::output_value($tag, self::$ci->input->get_post($key, true));
		else
			//return self::wrap($tag, self::$ci->input->get($key, true));
			return self::output_value($tag, self::$ci->input->get($key, true));
	}

	// ------------------------------------------------------------------------

	/**
	 * returns the value of a given $_GET value
	 * Expands or not the tag if the "if" or "expression" attributes are set
	 *
	 * @usage	<ion:request:get key="get_var_name" [is="foo" expression="get_var_name == 'bar'" ] />
	 *
	 */
	public static function tag_request_get(FTL_Binding $tag) {
		return self::_tag_request_getpost($tag, "get");
	}

	// ------------------------------------------------------------------------

	/**
	 * returns the value of a given $_POST value
	 * Expands or not the tag if the "if" or "expression" attributes are set
	 *
	 * @usage	<ion:request:post key="post_var_name" [is="foo" expression="post_var_name == 'bar'" ] />
	 *
	 */
	public static function tag_request_post(FTL_Binding $tag) {
		return self::_tag_request_getpost($tag, "post");
	}

	// ------------------------------------------------------------------------

	/**
	 * returns the value of a given $_GET or $_POST value
	 * Expands or not the tag if the "if" or "expression" attributes are set
	 *
	 * @usage	<ion:request:getpost key="getpost_var_name" [is="foo" expression="getpost_var_name == 'bar'" ] />
	 *
	 */
	public static function tag_request_getpost(FTL_Binding $tag) {
		return self::_tag_request_getpost($tag, "getpost");
	}


	// ------------------------------------------------------------------------


	public static function tag_session(FTL_Binding $tag)
	{
		// Set this tag as "process tag"
		$tag->setAsProcessTag();

		if( ! isset(self::$ci->session))
			self::$ci->load->library('session');

		return $tag->expand();
	}


	// ------------------------------------------------------------------------


	/**
	 * Displays one session variable
	 * Expands or not the tag if the "if" or "expression" attributes are set
	 *
	 * @usage	<ion:session:get key="session_var_name" [is="foo" expression="session_var_name == 'bar'" ] />
	 *
	 */
	public static function tag_session_get(FTL_Binding $tag)
	{
		$key = $tag->getAttribute('key');

		if ( ! is_null($key))
		{
			return self::output_value($tag, self::$ci->session->userdata($key));
		}
		return '';
	}


	// ------------------------------------------------------------------------


	/**
	 * Set one session variable
	 *
	 * @usage	<ion:session:set key="foo" value="bar" />
	 * 			<ion:session:set key="title" />
	 *
	 */
	public static function tag_session_set(FTL_Binding $tag)
	{
		// Set this tag as "process tag"
		$tag->setAsProcessTag();

		// $tag->setAttribute('return', FALSE);

		$key = $tag->getAttribute('key');
		$value = $tag->getAttribute('value');

		if ( ! is_null($key))
		{
			// Set the given value
			if ( ! is_null($value))
			{
				$from = $tag->getAttribute('from', $tag->getName());

				if (strpos($value, 'extend:') === 0)
				{
					// Get asked extend name
					$arr = explode(':', $value);
					$field = ( isset($arr[1])) ? $arr[1] : NULL;
					$var = ( isset($arr[2])) ? $arr[2] : NULL;

					// Extends definition
					$extend_fields_definitions = self::$ci->base_model->get_extend_fields_definition($from, Settings::get_lang('current'));
					$extend_field = NULL;
					foreach($extend_fields_definitions as $def)
					{
						if ($def['name'] == $field)
							$extend_field = $def;
					}

					if ( ! is_null($extend_field))
					{
						// Get value
						if ((is_null($var) OR ! isset($extend_field[$var])) OR $var=='value')
						{
							$value = $tag->getValue(self::$extend_field_prefix . $field, $from);
						}
						else
						{
							$value = isset($extend_field[$var]) ? $extend_field[$var] : NULL;
						}
					}
					else
					{
						$msg = 'Error in tag '.$tag->nesting().': "' . $value . '" is not correct';
						log_message('error', $msg);
						$value = NULL;
					}
				}
				else
				{
					$value = $tag->getValue($value, $from);
				}

				self::$ci->session->set_userdata($key, $value);
			}
			else
			{
				$parent = $tag->getDataParent();

				if ( ! is_null($parent))
				{
					if ($value = $parent->get($parent->name))
						self::$ci->session->set_userdata($key, $value);
				}
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Outputs the dump of one tag local variable
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_trace(FTL_Binding $tag)
	{
		$value = NULL;

		$key = $tag->getAttribute('key');
		$parent = $tag->getAttribute('parent');

		if (is_null($key))
		{
			$value = $tag->get($tag->getParentName());
		}
		else
		{
			if ( ! is_null($parent))
			{
				$parent= $tag->getParent($parent);
				$value = $parent->get($key);
			}
			else
				$value = $tag->get($key);
		}

		$str = '<pre>'.print_r($value, TRUE).'</pre>';
		return $str;
	}


	// ------------------------------------------------------------------------


	public static function tag_nesting(FTL_Binding $tag)
	{
		return $tag->nesting();
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the Home URL
	 *
	 * @return string
	 *
	 */
	public static function get_home_url()
	{
		// Set all languages online if connected as editor or more
		if (Authority::can('access', 'admin') && Settings::get('display_front_offline_content') == 1)
		{
			Settings::set_all_languages_online();
		}

		if (count(Settings::get_online_languages()) > 1 )
		{
			// if the current lang is the default one : don't return the lang code
			if (Settings::get_lang() != Settings::get_lang('default'))
			{
				return base_url() . Settings::get_lang();
			}
		}

		return base_url();
	}


	// ------------------------------------------------------------------------


	public static function get_base_url()
	{
		if (Authority::can('access', 'admin') && Settings::get('display_front_offline_content') == 1)
		{
			Settings::set_all_languages_online();
		}

		if (count(Settings::get_online_languages()) > 1 )
		{
			return base_url() . Settings::get_lang() .'/';
		}

		return base_url();
	}


	// ------------------------------------------------------------------------


	/**
	 * Creates and return one formatted HTML A element
	 *
	 * @param 	FTL_Binding
	 * @param	string
	 *
	 * @return 	string
	 *
	 */
	public static function create_href(FTL_Binding $tag, $url)
	{
		if ($tag->getAttribute('href') === TRUE)
		{
			if (validate_url($url))
			{
				$title = $url;
				$title_key = $tag->getAttribute('display', 'title');
				$attributes = $tag->getAttribute('attributes');

				if ( ! is_null($tag->getValue($title_key)))
					$title = $tag->getValue($title_key);

				if ($tag->getAttribute('popup') === TRUE)
					$url = anchor_popup($url, $title, $attributes);
				else
					$url = anchor($url, $title, $attributes);
			}
		}
		return $url;
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
		$html_tag = $tag->getAttribute('tag');

		// Inform the parent that the value has already been wrapped
		if ($html_tag && $parent = $tag->getParent())
			$parent->setAttribute('__wrap_called__', TRUE);

		if ($tag->getAttribute('__wrap_called__') !== TRUE)
		{
			$open_tag = $close_tag = '';

			$class = $tag->getAttribute('class', '');
			$id = $tag->getAttribute('id', '');

			if ( ! empty($class)) $class = ' class="'.$class.'"';
			if ( ! empty($id)) $id = ' id="'.$id.'"';

			$html_attributes = self::get_html_tag_attributes($tag);

			if ($html_tag)
			{
				$open_tag = '<' . $html_tag . $html_attributes . '>';
				$close_tag = '</' . $html_tag .'>';
			}

			$tag->removeAttributes(array('tag','class','id'));

			if ( ! empty ($value) )
			{
				return $open_tag . $value . $close_tag;
			}
			else
				return '';
		}
		return $value;
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
		// Distinguish datetime form date DB values
		$default_format = (strlen($date) > 10) ? 'Y-m-d H:i:s' : 'Y-m-d';

		$date = strtotime($date);

		if ($date)
		{
			$format = $tag->getAttribute('format', $default_format);

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
	 * @TODO : 	Project function
	 * 			Should return the HTMl attributes as formatted string
	 * 			Gives ability to write one expression to get dynamic data
	 *			Not so usable ...
	 *
	 * @param 	FTL_Binding $tag
	 * @param 	string      $attr
	 *
	public static function process_html_tag_attributes(FTL_Binding $tag, $attr='tag-attributes')
	{
		$expression = $tag->getAttribute($attr);

		if ( ! is_null($expression))
		{
			$keys = explode('|', $keys);
			foreach($keys as $key)
			{
				// 1. Try to get the value from tag's data array
				$value = $tag->getValue($key);

				// 2. Fall down to to tag's locals
				if (is_null($value))
					$value = $tag->get($key);

				$expression = str_replace($key, $value, $expression);
			}

			$return = @eval("\$result = (".$expression.") ? TRUE : FALSE;");
		}
	}
	 */


	// ------------------------------------------------------------------------


	/**
	 * Processes and outputs one simple value.
	 *
	 * 1. Check for expression and comparison attributes
	 * 2. Execute process_value() if no expression / comparison
	 * 3. Execute wrap() and outputs the value
	 *
	 * @param	FTL_Binding
	 * @param	mixed
	 *
	 * @return string
	 *
	 */
	public static function output_value(FTL_Binding $tag, $value)
	{
		$is = $tag->getAttribute('is');
		$in = $tag->getAttribute('in');
		$is_not = $tag->getAttribute('is_not');
		$expression = $tag->getAttribute('expression');

		// "is" and "expression" cannot be used together.
		if ( ! is_null($is) )
		{
			// Do not pass the attribute to child
			$tag->removeAttribute('is');

			if (strtolower($is) == 'true') $is = TRUE;
			if (strtolower($is) == 'false') $is = FALSE;

			if ($value == $is)
			{
				if (self::$trigger_else > 0)
					self::$trigger_else = 0;

				return self::wrap($tag, $tag->expand());
			}
			else
			{
				self::$trigger_else++;
				return '';
			}
		}
		else if ( ! is_null($in) )
		{
			// Do not pass the attribute to child
			$tag->removeAttribute('in');

			$in = explode(',', $in);

			foreach($in as $i)
			{
				$i = trim($i);

				if (strtolower($i) == 'true') $is = TRUE;
				if (strtolower($i) == 'false') $is = FALSE;

				if ($value == $i)
				{
					if (self::$trigger_else > 0)
						self::$trigger_else = 0;

					return self::wrap($tag, $tag->expand());
				}
			}
			self::$trigger_else++;
			return '';
		}
		else if ( ! is_null($is_not) )
		{
			$tag->removeAttribute('is_not');
			if (strtolower($is_not) == 'true') $is_not = TRUE;
			if (strtolower($is_not) == 'false') $is_not = FALSE;
			if ($value != $is_not)
			{
				if (self::$trigger_else > 0)
					self::$trigger_else = 0;

				return self::wrap($tag, $tag->expand());
			}
			else
			{
				self::$trigger_else++;
				return '';
			}
		}
		else if ( ! is_null($expression) )
		{
			// Do not pass the attribute to child
			$tag->removeAttribute('expression');

			$result = self::eval_expression($tag, $expression);

			switch($result)
			{
				case TRUE:
					if (self::$trigger_else > 0)
						self::$trigger_else = 0;

					return self::wrap($tag, $tag->expand());
					break;

				case FALSE:
					self::$trigger_else++;
					return '';
					break;

				case NULL:
					return self::show_tag_error($tag, 'Condition incorrect: ' . $expression);
			}
		}
		else if ( ! is_null($value))
		{
			// Process PHP, helper, prefix/suffix
			$value = self::process_value($tag, $value);

			// Make sub tags like "nesting" or "trace" working
			$tag->expand();

			return self::wrap($tag, $value);
		}

		return $tag->expand();
	}

    // ------------------------------------------------------------------------

    /**
     * Processes the filter value
     *
     * @param null $filter
     * @return mixed|null
     */
    public static function process_filter($filter=NULL)
    {
        if ( ! is_null($filter))
        {
            $filter = str_replace('.gt', '>', $filter);
            $filter = str_replace('.lt', '<', $filter);
            $filter = str_replace('.eq', '==', $filter);
            $filter = str_replace('.neq', '!=', $filter);
        }

        return $filter;
    }


	// ------------------------------------------------------------------------


	/**
	 * Processes the value through PHP function, helper, prefix/suffix
	 * Adds the following attributes to the tags using this method :
	 * - function
	 * - helper
	 * - prefix
	 * - suffix
	 *
	 * @param FTL_Binding $tag
	 * @param             $value
	 *
	 * @return Mixed|string
	 *
	 */
	public static function process_value(FTL_Binding $tag, $value)
	{
		if ( ! is_null($value))
		{
			// Text process
			$value = self::process_string($tag, $value);

			// PHP : Process the value through the passed in function name.
			$value = self::php_process($value, $tag->getAttribute('function') );

			// Helper
			$value = self::helper_process($tag, $value, $tag->getAttribute('helper'));

			// Prefix / Suffix
			$value = self::prefix_suffix_process($value, $tag->getAttribute('prefix'));
			$value = self::prefix_suffix_process($value, $tag->getAttribute('suffix'),2);
		}

		return $value;
	}


	// ------------------------------------------------------------------------


	/**
	 * Processes one string by adding text helper attributes
	 *
	 * @param	FTL_Binding 	$tag
	 * @param	String			$value
	 *
	 * @usage	<ion:content />
	 *
	 */
	public function process_string(FTL_Binding $tag, $value)
	{
		// paragraph & words limit ?
		$paragraph = $tag->getAttribute('paragraph');
		$words = $tag->getAttribute('words');
		$chars = $tag->getAttribute('characters');
		$ellipsize = $tag->getAttribute('ellipsize');

		// Limit to x paragraph if the attribute is set
		if ( ! is_null($paragraph))
			$value = tag_limiter($value, 'p', intval($paragraph));

		// Limit to x words
		if ( ! is_null($words))
			$value = word_limiter($value, $words);

		// Limit to x characters
		if ( ! is_null($chars))
			$value = character_limiter($value, $chars);

		// Ellipsize the text
		// See : http://codeigniter.com/user_guide/helpers/text_helper.html
		if ( ! is_null($ellipsize))
		{
			$ellipsize = explode(',', $ellipsize);
			if ( ! isset($ellipsize[0])) $ellipsize[0] = 32;
			if ( ! isset($ellipsize[1])) $ellipsize[1] = .5;
			if (floatval($ellipsize[1]) > 0.99)	$ellipsize[1] = 0.99;
			$value = ellipsize($value, intval($ellipsize[0]), floatval($ellipsize[1]));
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
	protected static function php_process($value, $functions)
	{
		if ( ! is_null($functions))
		{
			if ( ! is_array($functions))
				$functions = explode(',', $functions);

			foreach($functions as $func)
			{
				if (function_exists($func))
					$value = $func($value);
			}
		}
		return $value;
	}


	// ------------------------------------------------------------------------


	/**
	 * Process the input through the called functions and return the result
	 *
	 * @param	FTL_Binding
	 * @param	Mixed				The value to process
	 * @param	String / Array		String or array of PHP functions
	 *
	 * @return	Mixed				The processed result
	 */
	protected static function helper_process(FTL_Binding $tag, $value, $helper)
	{
		if ( ! is_null($helper))
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
					return self::show_tag_error($tag, 'Error when calling <b>'.$helper_name.'->'.$helper_func.'</b>. This helper function doesn\'t exist');
			}
		}

		return $value;
	}


	// ------------------------------------------------------------------------


	/**
	 * Add one prefix / suffix to the given value
	 * If the prefix or suffix looks like a translation call, try to translate
	 *
	 * @usage		prefix="Read more about : "
	 * 				prefix="lang('read_more_about')"
	 *
	 * 				In this case, '.' separates segments :
	 * 				prefix="'&bull; '. lang('post_in_categories') . ' : '"
	 *
	 * @param string
	 * @param string		Prefix / Suffix
	 * @param int 			1 : prefix mode, 2 : suffix mode
	 *
	 * @return string
	 *
	 */
	protected static function prefix_suffix_process($value, $string, $mode=1)
	{
		if ( ! is_null($string))
		{
			$segments = explode('.', $string);
			$prefix_suffix = '';

			$lang_reg = "%lang\('([-_ \w:]+?)'\)%";

			foreach($segments as $segment)
			{
				$translated_string = NULL;

				$segment = preg_replace_callback(
					$lang_reg,
					'self::lang_preg_replace_callback',
					$segment
				);

				$prefix_suffix .= $segment;
			}

			$value = $mode == 1 ? $prefix_suffix . $value : $value . $prefix_suffix;
		}
		return $value;
	}


	// ------------------------------------------------------------------------


	public static function get_html_tag_attributes(FTL_Binding $tag)
	{
		$attributes = $tag->getAttributes();
		$html_attributes = '';

		foreach ($attributes as $key =>$value)
		{
			if (
				in_array($key, self::$html_tag_attributes)
				OR substr($key,0,5) == 'data-'
			)
			{
				$html_attributes .= ' '.$key.'="'.$value.'" ';
			}
		}
		return $html_attributes;
	}

	// ------------------------------------------------------------------------


	/**
	 * Evaluates one expression
	 *
	 * @param FTL_Binding
	 * @param $expression
	 *
	 * @return bool|null		TRUE if the evaluation returns TRUE
	 * 							FALSE if the evaluation returns FALSE
	 * 							NULL if the evaluation can't be done (error in expression or $value NULL)
	 */
	protected static function eval_expression(FTL_Binding $tag, $expression)
	{
		// PHP error handling method
		self::register_shutdown(
			'self::handle_eval_shutdown',
			$tag
		);

		// Result and return
		$return = NULL;
		$result = FALSE;

		// If no key, we compare the value of the tag name
		$keys = $tag->getAttribute('key');
		if (is_null($keys))
			$keys = $tag->name;

		// Make an array from keys
		$keys = explode(',', $keys);
		$test_value = NULL;

		foreach($keys as $idx => $key)
		{
			$key = trim($key);
			// 1. Try to get the value from tag's data array
			$value = $tag->getValue($key);

			$expression = str_replace('.gt', '>', $expression);
			$expression = str_replace('.lt', '<', $expression);
			$expression = str_replace('.eq', '==', $expression);
			$expression = str_replace('.neq', '!=', $expression);

			// Not convinced...
			// $expression = str_replace('.leqt', '<=', $expression);
			// $expression = str_replace('.geqt', '>=', $expression);

			// 2. Fall down to to tag's locals
			if (is_null($value))
				$value = $tag->get($key);

			if ($idx == 0 && strpos($expression, $key) === FALSE)
				$expression = $key . $expression;
			$test_value = ( (! $value == (string)(float)$value) OR is_null($value) OR $value=='') ? "'".$value."'" : $value;

			// if (gettype($test_value) == 'string' && $test_value == '') $test_value = "'".$test_value."'";

			$expression = str_replace($key, $test_value, $expression);
		}

		// If at least one tested value was not NULL
		if ( ! is_null($test_value))
		{
			$return = @eval("\$result = (".$expression.") ? TRUE : FALSE;");

		}

		if ($return === NULL OR is_null($test_value))
		{
			if ($result)
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return NULL;
		}
	}


	// ------------------------------------------------------------------------


	public static function create_extend_tags(FTL_Binding $tag, $parent)
	{
		if ( ! isset(self::$has_extend_tags[$parent]))
		{
			$extend_fields_definitions = self::$ci->base_model->get_extend_fields_definition($parent, Settings::get_lang('current'));

			foreach ($extend_fields_definitions as $field)
			{
				self::$extends_def[$field['name']] = $field;
				self::$context->define_tag($parent.':extend:'.$field['name'], array(__CLASS__, 'tag_extend_field'));
				self::$context->define_tag($parent.':extend:'.$field['name'].':label', array(__CLASS__, 'tag_extend_field_definition_key'));
				self::$context->define_tag($parent.':extend:'.$field['name'].':type', array(__CLASS__, 'tag_extend_field_definition_key'));
				self::$context->define_tag($parent.':extend:'.$field['name'].':default_value', array(__CLASS__, 'tag_extend_field_definition_key'));
				self::$context->define_tag($parent.':extend:'.$field['name'].':value', array(__CLASS__, 'tag_extend_field_value'));

				self::$context->define_tag($parent.':extend:'.$field['name'].':options', array(__CLASS__, 'tag_extend_field_options'));
				self::$context->define_tag($parent.':extend:'.$field['name'].':options:label', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag($parent.':extend:'.$field['name'].':options:value', array(__CLASS__, 'tag_simple_value'));

				self::$context->define_tag($parent.':extend:'.$field['name'].':values', array(__CLASS__, 'tag_extend_field_values'));
				self::$context->define_tag($parent.':extend:'.$field['name'].':values:label', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag($parent.':extend:'.$field['name'].':values:value', array(__CLASS__, 'tag_simple_value'));

				self::$context->define_tag($parent.':extend:'.$field['name'].':medias', array(__CLASS__, 'tag_extend_field_medias'));
				self::$context->define_tag($parent.':extend:'.$field['name'].':links', array(__CLASS__, 'tag_extend_field_links'));

			}

			self::$has_extend_tags[$parent] = TRUE;
		}
	}


	// ------------------------------------------------------------------------


	public static function create_sub_tags(FTL_Binding $tag, $key=NULL, $prefix=NULL)
	{
		$key = ! is_null($key) ? $key : $tag->getName();

		$data = ! is_null($tag->get($key)) ? $tag->get($key) : NULL;

		$prefix = ! is_null($prefix) ? $prefix.':' : '';

		if ( ! empty($data))
		{
			$names = array_keys($data);

			foreach($names as $name)
			{
				if (
					! is_array($data[$name])
					&& ! isset(self::$tags[$name])
					&& ! isset(self::$tags[$key.':'.$name])
				)
				{
					self::$context->define_tag($prefix.$key.':'.$name, array(__CLASS__, 'tag_simple_value'));
				}
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Registers one method as shutdown function
	 * Needed because register_shutdown_function() stacks functions
	 *
	 * @param $callback
	 * @param $tag
	 *
	 */
	private static function register_shutdown($callback, $tag)
	{
		if(is_callable($callback))
		{
			self::unregister_shutdown();
			self::$shutdown_callback = $callback;
			self::$shutdown_callback_args = $tag;
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Unregister the shutdown function
	 *
	 */
	private static function unregister_shutdown()
	{
		self::$shutdown_callback = NULL;
		self::$shutdown_callback_args = NULL;
	}


	// ------------------------------------------------------------------------


	/**
	 * Calls the registered shutdown method
	 * This method is registered as "shutdown function" by self::init()
	 *
	 */
	public static function call_shutdown()
	{
		if ( ! is_null(self::$shutdown_callback))
		{
			$callback = self::$shutdown_callback;
			if(is_callable($callback))
			{
				call_user_func($callback, self::$shutdown_callback_args);
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Handles the eval_expression() PHP fatal error.
	 * Called when the expression evaluation generates one fatal error
	 *
	 * @param FTL_Binding $tag
	 *
	 */
	public static function handle_eval_shutdown(FTL_Binding $tag)
	{
		$error = error_get_last();
		if($error !== NULL)
		{
			trace($tag->getAttributes());
			$msg = self::show_tag_error($tag,
				'PHP error : ' . $error['message'] . '<br/>' .
				'in expression : ' . $tag->getAttribute('expression') . '<br/>' .
				'file : ' . $error['file']
				//	. '<br/>PHP original error : <br/>'.
				//	$error['message'] . ' in ' . $error['file']
			);
			echo $msg;
			die();
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Displays an error concerning one tag use
	 *
	 * @param	FTL_Binding
	 * @param	String		Message
	 * @param	String		Error template
	 *
	 * @return	String		Error message
	 *
	 */
	protected static function show_tag_error(FTL_Binding $tag, $message, $template = 'error_tag')
	{
		// Build the tag string as written in the view
		$attributes = $tag->getAttributes();
		$attr_str = '';
		foreach($attributes as $key=> $value)
			$attr_str .= ' '.$key .'="'.$value.'"';

		// Used by APPPATH.'errors/'.$template.EXT
		$tag_name = '&lt;'.self::$tag_prefix .':' .$tag->nesting() .' ' . $attr_str .'>';

		ob_start();
		include(APPPATH.'errors/'.$template.EXT);
		$buffer = ob_get_contents();

		ob_end_clean();
		return $buffer;
	}


	// ------------------------------------------------------------------------


	protected static function get_lang_swap(FTL_Binding $tag, $swap)
	{
		// $swap = $tag->getAttribute('swap');

		if ( ! is_null($swap))
		{
			$swap = explode(',', $swap);
			$swap = array_map('trim', $swap);

			// Try to get internal swap values
			foreach($swap as &$str)
			{
				if (strpos($str, '::') !== FALSE)
				{
					$seg = explode('::', $str);

					// The asked key must be set
					if ( ! empty($seg[1]))
					{
						// Get from global value
						if ($seg[0] == 'global')
						{
							$str = self::$context->get_global($seg[1]);
						}
						// Get from parent tag
						else
						{
							$parent = NULL;

							// Parent not set : current parent
							if ($seg[0] == '')
								$parent = $tag->getDataParent();
							else
								$parent = $tag->getParent($seg[0]);

							if ( ! is_null($parent))
								$str = $parent->getValue($seg[1], $parent->name);
							else
								$str = '';
						}
					}
				}
			}
		}
		return $swap;
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
		$found = Finder::find_file($model_name, 'models');
		if ( ! empty($found))
			self::$ci->load->model($model_name, $new_name, TRUE);
			// if (!isset(self::$ci->{$new_name})) self::$ci->load->model($model_name, $new_name, TRUE);
	}

	// ------------------------------------------------------------------------


	private static function lang_preg_replace_callback($matches)
	{
		if ( ! empty($matches[1]))
			return lang($matches[1]);
		else
			return '';
	}

}


TagManager::init();


/* End of file Tagmanager.php */
/* Location: /application/libraries/Tagmanager.php */