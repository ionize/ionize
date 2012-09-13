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
 * Page TagManager 
 *
 */
require_once APPPATH.'libraries/Pages.php';

class TagManager_Page extends TagManager
{
	protected static $user = FALSE;
	
	/**
	 * Entity from URL.
	 * usually 'page' or 'article'
	 *
	 * @var string
	 */
	protected static $_entity = NULL;
	

	
	public static $tag_definitions = array
	(
		// pages
		'pages' => 				'tag_pages',

		// Page
		'page' => 				'tag_page',

		'next_page' =>			'tag_next_page',
		'prev_page' =>			'tag_prev_page',
		'next_article' =>		'tag_next_article',
		'prev_article' =>		'tag_prev_article',

		'breadcrumb' =>			'tag_breadcrumb',
	);


	// ------------------------------------------------------------------------


	/**
	 * 
	 * 
	 */
	public static function init()
	{
		self::$ci =& get_instance();

		// Article model
		self::$ci->load->model('article_model');
		self::$ci->load->model('page_model');
		self::$ci->load->model('url_model');

		// Helpers
		self::$ci->load->helper('text');

		// Pages, Page
		self::register('pages', Pages::get_pages());
		self::register('page', self::get_current_page());

		// Current page
		$page = self::registry('page');

		if ( ! empty($page['link']))
		{
			// External redirect
			if ($page['link_type'] == 'external')
			{
				redirect($page['link']);
				die();
			}
			// Internal redirect
			else
			{
				self::$ci->load->helper('array_helper');

				// Page
				if ($page['link_type'] == 'page')
				{
					if ($page = array_get(self::registry('pages'), $page['link_id'], 'id_page'))
					{
						redirect($page['absolute_url']);
					}
				}
				// Article
				if ($page['link_type'] == 'article')
				{
					if (count(self::get_uri_segments()) == 1)
					{
						redirect($page['absolute_url']);
					}
				}	
			}
		}
		
		// Can we get one article from the URL ?
		$entity = self::get_entity();
		if ($entity['type'] == 'article')
		{
			$article = self::$ci->article_model->get_by_id($entity['id_entity'], Settings::get_lang());
		}

		if ( ! empty($article))
			self::register('article', $article);

		self::$view = self::_get_page_view($page);

		self::render();
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
			self::$_entity = self::$ci->url_model->get_by_url(self::$ci->uri->uri_string());

		return self::$_entity;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the current page.
	 * 
	 * @return	array			Array of the page data. Can be empty.
	 *
	 */
	public static function get_current_page()
	{
		$page = NULL;

		$uri = self::$ci->uri->uri_string();

		// Ignore the page named 'page' and get the home page
		if ($uri == '')
		{
			$page = self::get_home_page();
		}
		else
		{
			if (config_item('url_mode') == 'short')
			{
				$page = self::get_page_by_code(self::$ci->uri->segment(3));
			}
			else
			{
				// Asked entity : Page or article
				$entity = self::get_entity();

				// Article
				if ( ! empty($entity['type']) && $entity['type'] == 'article')
				{
					$paths = explode('/', $entity['path_ids']);
					$id_page = $paths[count($paths)-2];
					
					$page = self::get_page_by_id($id_page);
				}

				// Special URI : category, archive, pagination
 				else if ( ! is_null(self::get_special_uri_array()))
 				{
 					$uri = self::get_page_path_from_special_uri();

					if ($uri == '')
						$page = self::get_home_page();
					else
						$page = self::get_page_by_url($uri);
				}

				// Return the found page
				else if ( ! empty($entity['id_entity']))
				{
					$page = self::get_page_by_id($entity['id_entity']);
				}

				else
					$page = self::get_module_page();
			}
		}
		if (is_null($page) OR empty($page))
		{
			$page = self::get_page_by_code('404');
			self::set_404_output();
		}

		// Add index to identify current page
		$page['__current__'] = TRUE;

		return $page;
	}


	// ------------------------------------------------------------------------


	/**
	 * Return the last part of the URI
	 * @NOTE : 	NOT USED
	 * 			2012.09.11
	 *
	 * @return 	string|null
	function get_last_uri_part()
	{
		$uri_segments = self::get_uri_segments();

		if ( ! is_null($uri_segments))
			return array_pop(array_slice($uri_segments, -1));

		return NULL;
	}
	 */


	// ------------------------------------------------------------------------


	/**
	 * Returns the page path without the special URI path
	 *
	 * @return string
	 *
	 */
	function get_page_path_from_special_uri()
	{
		$uri_config = array_flip(self::$ci->config->item('special_uri'));
		$special_uri_array = self::get_special_uri_array();
		$uri_string = '';

		foreach ($special_uri_array as $code => $args)
		{
			$arg_string = implode('/', $args);
			$uri_string .= '/'.$uri_config[$code] . '/' . $arg_string;
		}

		$uri_string = trim($uri_string, '/');
		$page_path = str_replace($uri_string, '', self::$ci->uri->uri_string());
		$page_path = trim($page_path, '/');

		return $page_path;
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Get the website Home page
	 * The Home page is the first page from the main menu (ID : 1)
	 * 
	 * @return	Array			Home page data array or an empty array if no home page is found
	 *
	 */
	public static function get_home_page()
	{
		$pages = self::registry('pages');

		if( ! empty($pages))
		{
			foreach($pages as $page)
			{
				if ($page['home'] == 1)
				{
					return $page;
				}
			}
			
			// No Home page found : Return the first page of the menu 1
			foreach($pages as $p)
			{
				if ($p['id_menu'] == 1)
					return $p;
			}
		}

		return array();
	}


	// ------------------------------------------------------------------------


	/**
	 * Get one page regarding to its name
	 * 
	 * @param	string	Page name
	 * @return	array	Page data array
	 */
	public static function get_page_by_code($page_code)
	{
		foreach(self::$context->registry('pages') as $page)
		{
			if ($page['name'] == $page_code)
				return $page;
		}
	
		return array();	
	}


	// ------------------------------------------------------------------------


	/**
	 * Get one page from its URL
	 * 
	 * @param	string	Page name
	 * @return	array	Page data array
	 *
	 */
	public static function get_page_by_url($url)
	{
		foreach(self::$context->registry('pages') as $p)
		{
			if ($p['path'] == $url)
				return $p;
		}
	
		return array();	
	}


	// ------------------------------------------------------------------------


	/**
	 * Get one page from its ID
	 *
	 * @param	string	Page ID
	 * @return	array	Page data array
	 *
	 */
	public static function get_page_by_id($id_page)
	{
		foreach(self::registry('pages') as $p)
		{
			if ($p['id_page'] == $id_page)
				return $p;
		}

		return array();
	}


	// ------------------------------------------------------------------------


	/**
	 * Get one potential module page
	 *
	 * @return array
	 *
	 */
	public function get_module_page()
	{
		// Limit the array to not consider the first "page" segment.
		$segments = self::get_uri_segments();

		while( ! empty($segments))
		{
			array_pop($segments);
			$uri_string = implode('/', $segments);
			$page = self::get_page_by_url($uri_string);

			if (! empty($page) && $page['used_by_module'] == TRUE)
				return $page;
		}

		return array();
	}


	// ------------------------------------------------------------------------


	/**
	 * Set 404 page
	 *
	 * @return void
	 *
	 */
	public function set_404_output()
	{	
		self::$ci->output->set_header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");

		$ext = array_pop(explode('.', array_pop(self::$ci->uri->segment_array())));

		if ( ! empty($ext) && in_array($ext, array('css','js','jpg')))
		{
			self::$ci->output->set_output('');
			self::$ci->output->_display();
			die();
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Orders the pages array regarding the editors logical order.
	 * This function do not nest the pages.
	 * Internal use only.
	 *
	 * @param	Array	Array of pages to order
	 * @param	Array	Array to feed. Will contain the ordered pages.
	 * @param	int		ID page from which to start
	 *
	 */
	private static function order_pages(&$data, &$arr, $parent=0)
	{
		if (is_array($data))
		{
			$children = array();
			foreach($data as $k=>$v)
			{
				if ($v['id_parent'] == $parent)
					$children[] = $v;
			}

			foreach ($children as $child)
			{
				$arr[] = $child;
				self::order_pages($data, $arr, $child['id_page']);
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the breadcrumb data
	 *
	 * @param	Array	The starting page
	 * @param	Array	All the pages
	 * @param	String	Current language code
	 * @param	Array
	 *
	 *
	 * @return	Array	Array of pages name (in the current language)
	 *
	 */
	private static function get_breadcrumb_array($page, $pages, $lang, $data = array())
	{
		$parent = NULL;

		if (isset($page['id_parent']) ) // && $page['id_parent'] != '0')
		{
			// Find the parent
			for($i=0; $i<count($pages) ; $i++)
			{
				if ($pages[$i]['id_page'] == $page['id_parent'])
				{
					$parent = $pages[$i];
					$data = self::get_breadcrumb_array($parent, $pages, $lang, $data);
					break;
				}
			}

			$data[] = $page;
		}
		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Return an adjacent page
	 * Internal use
	 *
	 * @param	FTL_Binding object
	 * @param	String				Mode. 'prev' or 'next'
	 *
	 * @return	Mixed				Page array or FALSE if no page was found.
	 *
	 */
	private static function get_adjacent_page(FTL_Binding $tag, $mode='prev')
	{
		$mode = ($mode=='prev') ? -1 : 1;

		$menu_name = $tag->getAttribute('menu');
		$menu_name = is_null($menu_name) ? 'main' : $menu_name;
		$id_menu = 1;

		$current_page = self::$context->registry('page');

		foreach(self::$context->registry('menus') as $menu)
		{
			if ($menu_name == $menu['name'])
			{
				$id_menu = $menu['id_menu'];
			}
		}

		$level = is_null($tag->getAttribute('level')) ? 0 : $tag->getAttribute('level');

		// Order the pages.
		/*
		 *  pages_ordered is not set !
		 *
		$ordered_pages = array();
		if ( empty($tag->globals->pages_ordered))
		{
			self::order_pages($tag->globals->pages, $ordered_pages);
			$tag->globals->pages = $ordered_pages;
			$tag->globals->pages_ordered = TRUE;
		}
		 */

		// Filter by menu and asked level : We only need the asked level pages !
		// $pages = array_filter($global_pages, create_function('$row','return ($row["level"] == "'. $level .'" && $row["id_menu"] == "'. $id_menu .'") ;'));
		$pages = array();
		foreach(self::$context->registry('pages') as $p)
		{
			if ($p['level'] == $level && $p['id_menu'] == $id_menu)
				$pages[] = $p;
		}

		// Filter on 'appears'=>'1'
		$pages = array_values(array_filter($pages, array(__CLASS__, '_filter_appearing_pages')));

		foreach($pages as $idx => $page)
		{
			if ($page['id_page'] == $current_page['id_page'])
			{
				if (!empty($pages[$idx + $mode]))
				{
					return $pages[$idx + $mode];
				}
			}
		}

		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Processes the next / previous page tags result
	 * Internal use only.
	 *
	 */
	private static function process_next_prev_page(FTL_Binding $tag, $page)
	{
		if ($page != FALSE)
		{
			// helper
			$helper = $tag->getAttribute('helper', 'navigation');

			// Get helper method
			$helper_function = (substr(strrchr($helper, ':'), 1 )) ? substr(strrchr($helper, ':'), 1 ) : 'get_next_prev_page';
			$helper = (strpos($helper, ':') !== FALSE) ? substr($helper, 0, strpos($helper, ':')) : $helper;

			// Prefix ?
			$prefix = $tag->getAttribute('prefix', '');

			// load the helper
			self::$ci->load->helper($helper);

			// Return the helper function result
			if (function_exists($helper_function))
			{
				$return = call_user_func($helper_function, $page, $prefix);

				return self::wrap($tag, $return);
			}
		}

		return '';
	}


	// -- TAGS DEFINITION ------------------------------------------------------------------------


	public static function tag_page(FTL_Binding $tag)
	{
		$cache = ($tag->getAttribute('cache') == 'off') ? FALSE : TRUE;

		// Tag cache
		if ($cache == TRUE && ($str = self::get_cache($tag)) !== FALSE)
			return $str;

		// Returned string
		$str = '';

		$id = $tag->getAttribute('id');
		if ( is_null($id))
		{
			$page = self::$context->registry('page');
		}
		else
		{
			if (strval((int)$id) == (string) $id)
				$page = self::get_page_by_id($id);
			else
				$page = self::get_page_by_code($id);
		}

		if ( ! empty($page))
		{
			$tag->set('page', $page);
			$tag->set('index', 0);
			$tag->set('count', 1);

			$str .= $tag->expand();
			$str = self::wrap($tag, $str);

			// Tag cache
			self::set_cache($tag, $str);
		}
		return $str;
	}

	/**
	 * @static
	 *
	 * @param $tag
	 *
	 * @return mixed
	 */
	public static function tag_pages(FTL_Binding $tag)
	{
		$cache = ($tag->getAttribute('cache') == 'off') ? FALSE : TRUE;

		// Tag cache
//		if ($cache == TRUE && ($str = self::get_cache(FTL_Binding $tag)) !== FALSE)
//			return $str;

		// Returned string
		$str = '';

		$parent = $tag->getAttribute('parent');
		$mode = ( ! is_null($tag->getAttribute('mode'))) ? $tag->getAttribute('mode') : 'flat';
		$levels = $tag->getAttribute('levels');
		$parent_page = NULL;

		if ( ! is_null($parent))
		{
			if (strval((int)$parent) == (string) $parent)
				$parent_page = self::get_page_by_id($parent);
			else
				$parent_page = self::get_page_by_code($parent);
		}

		if ( ! empty($parent_page))
		{
			if ($mode == 'tree')
				$pages = Structure::get_tree_navigation($tag->globals->pages, $parent_page['id_page']);
			else
				$pages = Structure::get_nested_structure($tag->globals->pages, array(), $parent_page['id_page']);
		}
		else
		{
			$pages = self::registry('pages');
		}

		$count = count($pages);

		foreach($pages as $key => $page)
		{
			// Render the article
			$tag->set('page', $page);
			$tag->set('index', $key);
			$tag->set('count', $count);
			$str .= $tag->expand();
		}
		$output = self::wrap($tag, $str);

		// Tag cache
		self::set_cache($tag, $output);

		return $output;
	}



	// ------------------------------------------------------------------------


	/**
	 * Next page tag
	 * @usage		<ion:next_page [ prefix="Next page : " menu="main|system|..." level="0|1|..." helper="helper_name:function_name" ] />
	 *				Attributes : 
	 *				prefix :	Prefix to add before the next page anchor. Can be free text or a static translation item index.
	 *				menu :		By default will be "main"
	 *				level :		The wished pages level to consider
	 *				helper :	Will be "navigation_helper:get_next_prev_page" by default.
	 *							This calls the function "get_next_prev_page" in the helper /application/helpers/navigation_helper.php"
	 *
	 */
	public static function tag_next_page(FTL_Binding $tag)
	{
		$page = self::get_adjacent_page($tag, 'next');
	
		return self::process_next_prev_page($tag, $page);
	}


	// ------------------------------------------------------------------------


	/**
	 * Previous page tag
	 * @usage		<ion:next_page [ prefix="Previous page : " menu="main|system|..." level="0|1|..." helper="helper_name:function_name"] />
	 *				Attributes : 
	 *				prefix :	Prefix to add before the previous page anchor. Can be free text or a static translation item index.
	 *				menu :		By default will be "main"
	 *				level :		The wished pages level to consider
	 *				helper :	Will be "navigation_helper:get_next_prev_page" by default.
	 *							This calls the function "get_next_prev_page" in the helper /application/helpers/navigation_helper.php"
	 *
	 */
	public static function tag_prev_page(FTL_Binding $tag)
	{
		$page = self::get_adjacent_page($tag, 'prev');
	
		return self::process_next_prev_page($tag, $page);
	}


	// ------------------------------------------------------------------------


	/**
	 * Displays the breacrumb : You are here !!!
	 *
	 * @param	FTL_Binding object
	 * @return	String	The parsed view
	 *
	 */
	public static function tag_breadcrumb(FTL_Binding $tag)
	{
		// Anchor enclosing tag
		$subtag_open = ( ! is_null($tag->getAttribute('subtag'))) ? '<' . $tag->getAttribute('subtag') . '>' : '';
		$subtag_close = ( ! is_null($tag->getAttribute('subtag'))) ? '</' . $tag->getAttribute('subtag') . '>' : '';

		$separator = $tag->getAttribute('separator', ' &raquo; ');
		if ($separator != ' &raquo; ')
			$separator = htmlentities(html_entity_decode($separator));

		$starting_level = $tag->getAttribute('starting_level', FALSE);

		// Pages && page
		$pages = self::$context->registry('pages');
		$page = self::$context->registry('page');

		// Get the Breadcrumbs array
		$lang = Settings::get_lang();
		$breadcrumb = self::get_breadcrumb_array($page, $pages, $lang );

		// Filter appearing pages
		$breadcrumb = array_values(array_filter($breadcrumb, array(__CLASS__, '_filter_appearing_pages')));

		if ($starting_level != FALSE)
		{
			$new_breadcrumb = array();
			foreach($breadcrumb as $b)
			{
				if ($b['level'] >= $starting_level)
					$new_breadcrumb[] = $b;
			}
			$breadcrumb = $new_breadcrumb;
		}

		// Build the links
		$return = '';

		for($i=0; $i<count($breadcrumb); $i++)
		{
			$url = $breadcrumb[$i]['absolute_url'];

			// Adds the suffix if defined
			if ( config_item('url_suffix') != '' ) $url .= config_item('url_suffix');

			$return .= ($return != '') ? $separator : '';

			$return .= $subtag_open . '<a href="'.$url.'">'.$breadcrumb[$i]['title'].'</a>' . $subtag_close;
		}

		return self::wrap($tag, $return);
	}



	// ------------------------------------------------------------------------


	/**
	 * Filters page which should appear
	 * used by self::tag_navigation()
	 *
	 * @param	array
	 *
	 * @return 	bool
	 *
	 */
	public static function _filter_appearing_pages($row)
	{
		return ($row['appears'] == 1);
	}


	// ------------------------------------------------------------------------


	/**
	 * Return TRUE if the user can see the element
	 *
	 * @param array
	 *
	 * @return bool
	 *
	 */
	private function _filter_pages_authorization($row)
	{
		// If the page group != 0, then get the page group and check the restriction
		if($row['id_group'] != 0)
		{
			self::$ci->load->model('connect_model');
			$element_group = FALSE;
			
			$groups = self::$ci->connect_model->get_groups();
			
			// Get the page group
			foreach($groups as $group)
			{
				if ($group['id_group'] == $row['id_group']) $element_group = $group;
			} 

			// If the current connected user has access to the page return TRUE
			if (self::$user !== FALSE && $element_group != FALSE && self::$user['group']['level'] >= $element_group['level'])
				return TRUE;
			
			return FALSE;
		}
		return TRUE;
	}


	// ------------------------------------------------------------------------

	/**
	 * Get the view for the asked page
	 *
	 * @param array
	 *
	 * @return bool|String
	 *
	 */
	private static function _get_page_view($page)
	{
		$view = FALSE;

		$article = self::registry('article');

		if ( ! empty($article))
			$view = ($page['view_single'] != FALSE) ? $page['view_single'] : $page['view'];
		else
			$view = $page['view'];

		$view_path = Theme::get_theme_path().'views/'.$view.EXT;

		// Return the Ionize core view
		if ( ! file_exists($view_path) OR empty($view))
			$view = Theme::get_default_view('page');

		return $view;
	}

}


/* End of file Page.php */
/* Location: /application/libraries/Tagmanager/Page.php */