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
// require_once APPPATH.'libraries/Tagmanager/Media.php';
require_once APPPATH.'libraries/Pages.php';

class TagManager_Page extends TagManager
{	
	protected static $_inited = FALSE;
	
	protected static $pagination_uri = '';
	
	protected static $user = FALSE;
	
	protected static $categories = FALSE;
	
	protected static $_article = array();
	
	// Entity asked by the URL (usually 'page' or 'article')
	protected static $_entity = NULL;
	
	protected static $_special_uri = NULL;
	
	// Int. Segment index of $ci->uri->uri_string() of the special URI
	protected static $_special_uri_segment = NULL;
	
	
	public static $tag_definitions = array
	(
		// pages
		'pages' => 				'tag_pages',
		'pages:title' => 		'tag_page_title',
		'pages:subtitle' => 	'tag_page_subtitle',
		'pages:url' => 			'tag_page_url',
		'pages:content' => 		'tag_page_content',

		// Page
		'period' => 			'tag_period',
		'pagination' =>			'tag_pagination',
		'absolute_url' =>		'tag_absolute_url',
		'first_item' => 		'tag_first_item',
		'last_item' => 			'tag_last_item',
		'next_page' =>			'tag_next_page',
		'prev_page' =>			'tag_prev_page',
		'next_article' =>		'tag_next_article',
		'prev_article' =>		'tag_prev_article',

		'page' => 				'tag_page',

		'id_page' => 			'tag_page_id',
		'page:name' => 			'tag_page_name',
		'page:url' => 			'tag_page_url',
		'title' => 				'tag_page_title',
		'subtitle' => 			'tag_page_subtitle',
		'meta_title' => 		'tag_page_meta_title',
		'content' =>			'tag_page_content',



		// Breadrumb
		'breadcrumb' =>			'tag_breadcrumb',
	);


	// ------------------------------------------------------------------------


	/**
	 * 
	 * 
	 */
	public static function init()
	{
 		// parent::init('Page');
		self::$ci =& get_instance(); 

		// Article model
		self::$ci->load->model('article_model');
		self::$ci->load->model('page_model');
		self::$ci->load->model('url_model');

		// Helpers
		self::$ci->load->helper('text');

		self::$uri_segments = explode('/', self::$ci->uri->uri_string());

		// Get pages and add them to the context
		self::$context->globals->pages = Pages::get_pages();

// Pagination URI
//		$uri_config = self::$ci->config->item('special_uri');
//		$uri_config = array_flip($uri_config);
//		self::pagination_uri = $uri_config['pagination'];
	
		// Set self::$context->globals->page
		self::add_globals();

		// Current page
		$page = self::$context->globals->page;

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
					if ($page = array_get(self::$context->globals->pages, $page['link_id'], 'id_page'))
					{
						redirect($page['absolute_url']);
					}
				}
				// Article
				if ($page['link_type'] == 'article')
				{
					if (count(self::$uri_segments) == 1)
					{
						redirect($page['absolute_url']);
					}
				}	
			}
		}
		
		// Can we get one article from the URL ?
		$article = array();

		$article_url = self::get_entity();
		if ($article_url['type'] == 'article')
		{
			$article = TagManager_Article::get_article_from_url($article_url);
		}

		if ( ! empty($article))
			self::$_article = $article;

		self::$view = self::get_page_view($page);

		self::render();
	}
	

	// ------------------------------------------------------------------------

	
	public function add_globals()
	{
		parent::add_globals();

		// Get current asked page
		self::$context->globals->page = self::get_current_page();

		// Show 404 if no page
		if(empty(self::$context->globals->page))
		{
			self::set_404();
		}
	}


	/**
	 * Returns the current entity asked by the URL ('page' or 'article')
	 *
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
	 * Get the current page data.
	 * 
	 * @param	FTL_Context		FTL_ArrayContext array object
	 * @param	string			Page name
	 * @return	array			Array of the page data. Can be empty.
	 *
	 */
	public static function get_current_page()
	{
		$uri = self::$ci->uri->uri_string();

		// Ignore the page named 'page' and get the home page
		if ($uri == 'page')
		{
			return self::get_home_page();
		}
		else
		{
			if (config_item('url_mode') == 'short')
			{
				return self::get_page(self::$ci->uri->segment(3));
			}
			else
			{
				// return self::get_page(self::$ci->uri->segment(3));
				// Asked entity : Page or article
				$entity = self::get_entity();

				// Article
				if ( ! empty($entity['type']) && $entity['type'] == 'article')
				{
					$paths = explode('/', $entity['path_ids']);
					$id_page = $paths[count($paths)-2];
					
					return self::get_page_by_id($id_page);
				}

				// Special URI : category, archive, pagination
 				if ( self::get_special_uri())
 				{
 					$uri = self::get_page_path_from_special_uri();
					return self::get_page_by_url($uri);
				}

				// Return the found page
				if ( ! empty($entity['id_entity']))
					return self::get_page_by_id($entity['id_entity']);

				// Module page in parents page ?
				// If nothing : returns empty array -> 404
				return self::get_module_page();
			}
		}
	}
	
	
	/**
	 * Return the internal special URI code
	 * See config/ionize.php -> $config['special_uri']
	 *		
	 * Archives : 	page/subpage/archive/2012/07 : segments -2 
	 * Category : 	page/subpage/category/webdesign : segments -1
	 * Pagination : page/subpage/page/5 : segments -1
	 *
	 */
	function get_special_uri()
	{
		if ( is_null(self::$_special_uri))
		{
			$uri_config = self::$ci->config->item('special_uri');
			$segments = self::$ci->uri->segment_array();
			
			// Limit the array to the potential special URI, and avoid taking the first "page" segment.
			$segment_index = count($segments) - 3;
			$segments = array_slice($segments, 2);

			while( ! empty($segments))
			{
				$segment = array_pop($segments);
				if ($segment_index !=0 && array_key_exists($segment, $uri_config))
				{
					self::$_special_uri_segment = $segment_index;
					self::$_special_uri = $uri_config[$segment];
					break;
				}
				$segment_index--;
			}
		}
		return self::$_special_uri;
	}
	
	/**
	 * Return the special URI segment index regarding to self::$ci->uri->segment_array()
	 *
	 * @return 		int		Segment index
	 *
	 *
	 */
	function get_special_uri_segment()
	{
		if ( is_null(self::$_special_uri))
		{
			self::get_special_uri();
		}
		return self::$_special_uri_segment;
	}
	
	
	/**
	 * Returns the page path without the special URI path
	 *
	 *
	 */
	function get_page_path_from_special_uri()
	{
		$special_uri = self::get_special_uri();

		return implode('/', array_slice(self::$ci->uri->segment_array(), 2, $special_uri  ));
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Get the website Home page
	 * The Home page is the first page from the main menu (ID : 1)
	 * 
	 * @param	FTL_Context		FTL_ArrayContext array object
	 * @return	Array			Home page data array or an empty array if no home page is found
	 */
	public static function get_home_page()
	{
		if( ! empty(self::$context->globals->pages))
		{
			foreach(self::$context->globals->pages as $page)
			{
				if ($page['home'] == 1)
				{
					return $page;
				}
			}
			
			// No Home page found : Return the first page of the menu 1
			foreach(self::$context->globals->pages as $p)
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
	public static function get_page($page_name)
	{
		foreach(self::$context->globals->pages as $p)
		{
			if ($p['url'] == $page_name)
				return $p;
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
		foreach(self::$context->globals->pages as $p)
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
		foreach(self::$context->globals->pages as $p)
		{
			if ($p['id_page'] == $id_page)
				return $p;
		}

		return array();
	}

	/**
	 * Get one page from its code
	 *
	 * @param	string	Page code
	 * @return	array	Page data array
	 *
	 */
	public static function get_page_by_code($code)
	{
		foreach(self::$context->globals->pages as $p)
		{
			if ($p['name'] == $code)
				return $p;
		}

		return array();
	}


	// ------------------------------------------------------------------------


	public function get_module_page()
	{
		$segments = self::$ci->uri->segment_array();

		// Limit the array to not consider the first "page" segment.
		$segments = array_slice($segments, 2);

		while( ! empty($segments))
		{
			array_pop($segments);
			$uri_string = implode('/', $segments);
			$page = self::get_page_by_url($uri_string);

			if (! empty($page) && $page['used_by_module'] == TRUE)
			{
				return $page;
			}
		}

		return array();
	}


	// ------------------------------------------------------------------------

	
	function get_article()
	{
		return self::$_article;
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Set base data for a 404 page
	 *
	 */
	public function set_404()
	{	
		self::$ci->output->set_header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");

		$ext = array_pop(explode('.', array_pop(self::$ci->uri->segment_array())));

		if ( ! empty($ext) && in_array($ext, array('css','js','jpg')))
		{
			self::$ci->output->set_output('');
			self::$ci->output->_display();
			die();
		}

		self::$context->globals->page = self::get_page('404');
	}	


	// ------------------------------------------------------------------------


	/**
	 * Set data for a page
	 *
	 */
	public function set_page_data($data)
	{
		self::$context->globals->page = array(
			'id_page' => ( ! empty($data['id_page'])) ? $data['id_page'] : 0,
			'view' => ( ! empty($data['view'])) ? $data['view'] : '',
			'title' => ( ! empty($data['title'])) ? $data['title'] : '',
			'level' => ( ! empty($data['level'])) ? $data['level'] : '',
			'link' => ( ! empty($data['link'])) ? $data['link'] : '',
			'content' => ( ! empty($data['content'])) ? $data['content'] : ''
		);
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
	

	function set_global_scope(FTL_Binding $tag)
	{
		$where = array();
		$in_pages = array();

		// Page from locals
		$pages =&  $tag->locals->pages;

		// Get only articles from autorized pages
		foreach($pages as $page)
			$in_pages[] = $page['id_page'];

		$where['id_page in'] = '('.implode(',', $in_pages).')';
		
		return $where;
	}

	public static function tag_page(FTL_Binding $tag)
	{
		$cache = (isset($tag->attr['cache']) && $tag->attr['cache'] == 'off' ) ? FALSE : TRUE;

		// Tag cache
		if ($cache == TRUE && ($str = self::get_cache($tag)) !== FALSE)
			return $str;

		// Returned string
		$str = '';

		$id = $tag->getAttribute('id');

		if (strval((int)$id) == (string) $id)
			$page = self::get_page_by_id($id);
		else
			$page = self::get_page_by_code($id);

		if ( ! empty($page))
		{
			// Render the article
			$tag->locals->page = $page;
			$tag->locals->index = 0;
			$tag->locals->count = 1;
			$str .= $tag->expand();
		}
		$output = self::wrap($tag, $str);

		// Tag cache
		self::set_cache($tag, $output);

		return $output;
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
		$cache = (isset($tag->attr['cache']) && $tag->attr['cache'] == 'off' ) ? FALSE : TRUE;

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
			$pages = $tag->locals->pages;
		}

		$count = count($pages);

		foreach($pages as $key => $page)
		{
			// Render the article
			$tag->locals->page = $page;
			$tag->locals->index = $key;
			$tag->locals->count = $count;
			$str .= $tag->expand();
		}
		$output = self::wrap($tag, $str);

		// Tag cache
		self::set_cache($tag, $output);

		return $output;
	}

	// ------------------------------------------------------------------------
	

	public static function tag_last_item(FTL_Binding $tag)
	{
		$value = (isset($tag->attr['value']) ) ? $tag->attr['value'] : TRUE;
		
		if ( ! empty ($tag->locals->index) && ! empty($tag->locals->count))
		{
			if ( ($tag->locals->index + 1) == $tag->locals->count)
			{
				return $value;
			}
		}
	}
	

	// ------------------------------------------------------------------------
	

	public static function tag_first_item(FTL_Binding $tag)
	{
		$value = (isset($tag->attr['value']) ) ? $tag->attr['value'] : TRUE;
		
		if ( ! empty ($tag->locals->index))
		{
			if ($tag->locals->index == 0)
			{
				return $value;
			}
		}
	}
	
	

	// ------------------------------------------------------------------------


	/**
	 * Count the articles depending on the URI segments
	 * Detect if a special URI is used.
	 *
	 * @return	int		The number of count articles
	 *
	function count_articles($tag, $filter)
	{
		if ( ! isset($tag->locals->page['nb_articles']))
		{
			$nb = 0;
		
			// Check if articles comes from a special URI result
			$special_uri = self::get_special_uri();
			$uri_config = self::$ci->config->item('special_uri');
	
			// Special URI
			// For example, to count articles from one archive
			if ($special_uri && $special_uri != 'pagination' )
			{
				// If special URI count method exists, use it !
				// That mean that foreach special URI, you need to define a method to count the articles
				// depending of this special URI.
				if (method_exists(__CLASS__, 'count_articles_from_'.$special_uri))
					$nb = call_user_func(array(__CLASS__, 'count_articles_from_'.$special_uri), $tag, $filter);
			}
			// Only one article is displayed
			// The special URI is the article name
			else if ($special_uri !== FALSE && ( ! array_key_exists($special_uri, $uri_config) ))
			{
				return 1;
			}
			// No special URI
			else 
			{
				$where = array();

				// from categories ? 
				$from_categories = (isset($tag->attr['from_categories']) && $tag->attr['from_categories'] != '') ? self::get_attribute($tag, 'from_categories') : FALSE;
				$from_categories_condition = (isset($tag->attr['from_categories_condition']) && $tag->attr['from_categories_condition'] != 'or') ? 'and' : 'or';
			
				// Get the scope set to the pagination tag
				$scope = (isset($tag->attr['scope']) && $tag->attr['scope'] != '' ) ? $tag->attr['scope'] : FALSE;
				
				if ($scope !== FALSE)
				{
					if ($scope == 'parent')
						$where = self::set_parent_scope(FTL_Binding $tag);
			
					if ($scope == 'global')
						$where = self::set_global_scope(FTL_Binding $tag);
				}
				else
				{
					$where = array('id_page'=>$tag->locals->page['id_page']);
				}

				
				// Reduce to the categories
				if ($from_categories !== FALSE)
				{
					$nb = self::$ci->article_model->count_articles_from_categories(
						$where, 
						explode(',', $from_categories), 
						$from_categories_condition, 
						Settings::get_lang('current'), 
						$filter
					);
				}
				else
				{
					// Count all articles in the current page : SQL
					$nb = self::$ci->article_model->count_articles(
						$where,
						Settings::get_lang('current'),
						$filter
					);
				}
			}
			return $nb;
		}
		else
		{
			return $tag->locals->page['nb_articles'];
		}
	}
	 */


	// ------------------------------------------------------------------------


	/**
	 * Count the articles from a given category
	 * Called by count_articles
	 *
	 * @return	int		The number of count articles
	 *
	function count_articles_from_category($tag, $filter)
	{
		$nb = 0;

		$category_uri = self::get_special_uri();
		$cat_segment_pos = TagManager_Page::get_special_uri_segment();
		$category_name = 	self::$uri_segments[$cat_segment_pos + 1];
		
		
		if ( $category_name)
		{
			$nb = self::$ci->article_model->count_articles_from_category
			(
				array('id_page'=>$tag->locals->page['id_page']),
				$category_name,
				Settings::get_lang('current'),
				$filter
			);
		}
		return $nb;
	}
	 */


	// ------------------------------------------------------------------------


	/**
	 * Count the articles from archive
	 * Called by count_articles
	 *
	 * @return	int		The number of count articles
	 *
	function count_articles_from_archives($tag, $filter)
	{
		$nb = 0;

		$arc_segment_pos = TagManager_Page::get_special_uri_segment();
		$year = isset(self::$uri_segments[$arc_segment_pos + 1]) ? self::$uri_segments[$arc_segment_pos + 1] : NULL ;
		$month = isset(self::$uri_segments[$arc_segment_pos + 2]) ? self::$uri_segments[$arc_segment_pos + 2] : NULL ;
		
		if ( ! is_null($year))
		{
			$nb = self::$ci->article_model->count_articles_from_archives(
				array('id_page'=>$tag->locals->page['id_page']),
				$year,
				$month,
				Settings::get_lang('current'),
				$filter
			);
		}

		return $nb;
	}
	 */


	// ------------------------------------------------------------------------


	/**
	 * Returns the pagination addon URI for categories pagination
	 *
	 * @return	String		The pagination addon URI
	 *
	 */
	function get_pagination_uri_addon_from_category()
	{
		$category_uri = self::get_special_uri();
		$cat_segment_pos = TagManager_Page::get_special_uri_segment();
		$category_name = 	self::$uri_segments[$cat_segment_pos + 1];

		return $category_uri . '/' . $category_name .'/';
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the pagination addon URI for archives pagination
	 *
	 * @return	String		The pagination addon URI
	 *
	 */
	function get_pagination_uri_addon_from_archives()
	{
		$archive_uri = self::get_special_uri();
		
		$arc_segment_pos = TagManager_Page::get_special_uri_segment();

		$year = isset(self::$uri_segments[$arc_segment_pos + 1]) ? self::$uri_segments[$arc_segment_pos + 1] : NULL ;
		$month = isset(self::$uri_segments[$arc_segment_pos + 2]) ? self::$uri_segments[$arc_segment_pos + 2] : NULL ;
	
		if ( ! is_null($year))
		{
			$archive_uri .= '/' .  $year;

			if ( ! is_null($month))
			{
				$archive_uri .= '/' .  $month;
			}				
		}

		return $archive_uri .'/';
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the page absolute URL
	 *
	 */
	public static function tag_absolute_url(FTL_Binding $tag)
	{
		return $tag->locals->page['absolute_url'];
	}


	// ------------------------------------------------------------------------



	// ------------------------------------------------------------------------


	/**
	 * Pagination tag
	 * 
	 * @todo : More options should be implemented !!!!
	 *
	 * Main class name, id, open tag, close tag, every options from cI in fact ! 
	 *
	 */
	public static function tag_pagination(FTL_Binding $tag)
	{
		// Tag cache
		if (($str = self::get_cache($tag)) !== FALSE)
			return $str;

		/* 
		 * Tag attributes
		 */
		// Pagination configuration array
		$pagination_config = array();
		
		// Number of total articles
		$total_articles = 0;
	
		// Number of displayed articles : tag attribute has priority 1.
		$per_page = (isset($tag->attr['per_page']) && is_int( (int) $tag->attr['per_page']) ) ? $tag->attr['per_page'] : FALSE;

		if ($per_page === FALSE)
			$per_page = (isset($tag->locals->page['pagination']) && $tag->locals->page['pagination'] > 0) ? $tag->locals->page['pagination'] : FALSE;

		// Filter
		$filter = (isset($tag->attr['filter'])) ? $tag->attr['filter'] : FALSE;

		// Order. No default order
		$order_by = (isset($tag->attr['order_by']) && $tag->attr['order_by'] != '') ? $tag->attr['order_by'] : FALSE;


		/*
		 * Pagination URL
		 * Pagination tag has to determine if a special URI is used in order to build the pagination base_url
		 *
		 */
		$base_url = '';
		$uri_addon = '';
		
		// Get the potential special URI
		$special_uri = (isset(self::$uri_segments[1])) ? self::$uri_segments[1] : FALSE;

		// Get the special URI config array (see /config/ionize.php)
		$uri_config = self::$ci->config->item('special_uri');
		$uri_config2 = array_flip(self::$ci->config->item('special_uri'));
		$pagination_uri = $uri_config2['pagination'];

		// If a special URI exists and is different from pagination URI, get the special URI to the pagination URL
		// Calling a special function for that is mandatory as each special URI can have different numbers of parameters
		if ($special_uri !== FALSE && $special_uri != $pagination_uri && array_key_exists($special_uri, $uri_config))
		{
			if (method_exists(__CLASS__, 'get_pagination_uri_addon_from_'.$uri_config[$special_uri]))
			{
				$uri_addon = call_user_func(array(__CLASS__, 'get_pagination_uri_addon_from_'.$uri_config[$special_uri]));
			}
		}

		/*
		 * URI building : Lang URI or not....
		 *
		 */
		// don't display the lang URL (by default)
		// If lang attribute is set to TRUE, force the lang code to be in the URL
		// Usefull only if the website has only one language
		$lang_url = (isset($tag->attr['lang']) && $tag->attr['lang'] == 'TRUE' ) ? TRUE : FALSE;
		
		if (count(Settings::get_online_languages()) > 1 OR $lang_url === TRUE)
		{
			$base_url = base_url() . Settings::get_lang('current') . '/'. $tag->locals->page['url'] . '/' . $uri_addon . $pagination_uri;
		}
		else
		{
			$base_url = base_url() . $tag->locals->page['url'] . '/' . $uri_addon . $pagination_uri;		
		}
		
		/*
		 * Pagination tag result design
		 */
		$class = (isset($tag->attr['class'])) ? ' class="' . $tag->attr['class'] .'" ' : '';
		$id = (isset($tag->attr['id'])) ? ' id="' . $tag->attr['id'] .'" ' : '';

		// Article count :
		$tag->locals->page['nb_articles'] = self::count_articles($tag, $filter);

		// Pagination setup
		if ($per_page > 0 && $tag->locals->page['nb_articles'] > $per_page)
		{
			// Load CI Pagination Lib
			isset(self::$ci->pagination) OR self::$ci->load->library('pagination');
			
			// Pagination theme config
			$config = array();
			$cf = Theme::get_theme_path().'config/pagination.php';
			if (is_file($cf))
			{
				require($cf);
				$pagination_config = $config;
				unset($config);
			}	

			// Pagination config from tag
			if (isset($tag->attr['full_tag'])) {
				$pagination_config['full_tag_open'] = 		'<' . $tag->attr['full_tag'] . $id . $class . '>';
				$pagination_config['full_tag_close'] = 		'</' . $tag->attr['full_tag'] . '>';			
			}
			if (isset($tag->attr['first_tag'])) {
				$pagination_config['first_tag_open'] = 		'<' . $tag->attr['first_tag'] . '>';
				$pagination_config['first_tag_close'] = 	'</' . $tag->attr['first_tag'] . '>';
			}
			if (isset($tag->attr['last_tag'])) {
				$pagination_config['last_tag_open'] = 		'<' . $tag->attr['last_tag'] . '>';
				$pagination_config['last_tag_close'] = 		'</' . $tag->attr['last_tag'] . '>';
			}
			if (isset($tag->attr['cur_tag'])) {
				$pagination_config['cur_tag_open'] = 		'<' . $tag->attr['cur_tag'] . '>';
				$pagination_config['cur_tag_close'] = 		'</' . $tag->attr['cur_tag'] . '>';
			}
			if (isset($tag->attr['next_tag'])) {
				$pagination_config['next_tag_open'] = 		'<' . $tag->attr['next_tag'] . '>';
				$pagination_config['next_tag_close'] = 		'</' . $tag->attr['next_tag'] . '>';
			}
			if (isset($tag->attr['prev_tag'])) {
				$pagination_config['prev_tag_open'] = 		'<' . $tag->attr['prev_tag'] . '>';
				$pagination_config['prev_tag_close'] = 		'</' . $tag->attr['prev_tag'] . '>';
			}
			if (isset($tag->attr['num_tag'])) {
				$pagination_config['num_tag_open'] = 		'<' . $tag->attr['num_tag'] . '>';
				$pagination_config['num_tag_close'] = 		'</' . $tag->attr['num_tag'] . '>';
			}

			// Current page
			$uri_segments = self::$uri_segments;
			$cur_page = (in_array($pagination_uri, self::$uri_segments)) ? array_pop(array_slice($uri_segments, -1)) : 1;

			// Pagination tag config init
			$pagination_config = array_merge($pagination_config,
				array
				(
					'base_url' => $base_url,
					'per_page' => $per_page,
					'total_rows' => $tag->locals->page['nb_articles'],
					'num_links' => 3,
					'cur_page' => $cur_page,
					'first_link' => lang('first_link'),			// "First" text : see /theme/your_theme/language/xx/pagination_lang.php
					'last_link' => lang('last_link'),			// "Last" text
					'next_link' => lang('next_link'),
					'prev_link' => lang('prev_link')
				)
			);

			// Pagination initialization
			self::$ci->pagination->initialize($pagination_config); 

			// Create the links
			$tag->locals->page['pagination_links'] = self::$ci->pagination->create_links();

			// Tag cache
			self::set_cache($tag, $tag->locals->page['pagination_links']);
		
			return $tag->locals->page['pagination_links'];
		}
	}

	
	// ------------------------------------------------------------------------


	/*
	 * Page tags
	 * 
	 * 
	 */
/*
    public static function tag_page(FTL_Binding $tag)    
    {
		$field = ( ! empty($tag->attr['field'])) ? $tag->attr['field'] : NULL;
		
		$page = $tag->locals->page;
		
        // Is the asked title from another page ?
        $from = (isset($tag->attr['from'])) ? $tag->attr['from'] : FALSE ;

        if ($from == 'parent')
        {
            $up = (isset($tag->attr['up'])) ? $tag->attr['up'] : 1 ;
            
            // Get the Breadcrumbs array
            $breacrumbs = self::get_breadcrumb_array($tag->locals->page, $tag->locals->pages, Settings::get_lang() );
            
            // Filter appearing pages
            $breacrumbs = array_values(array_filter($breacrumbs, array(__CLASS__, '_filter_appearing_pages')));
            
            // Reverse the breadcrumbs array
            $breacrumbs = array_reverse($breacrumbs);
            
            if ( ! empty($breacrumbs[$up]))
            {
 				$page = $breacrumbs[$up];
            }
        }

        if ( ! is_null($field))
        	return self::wrap($tag, $page[$field]);
        
        return '';

	}
*/
	
	public static function tag_page_id(FTL_Binding $tag) { return self::wrap($tag, $tag->locals->page['id_page']); }
	public static function tag_page_name(FTL_Binding $tag) { return self::wrap($tag, $tag->locals->page['name']); }
    public static function tag_page_url(FTL_Binding $tag)	{ return self::wrap($tag, $tag->locals->page['url']); }
	public static function tag_page_subtitle(FTL_Binding $tag) { return self::wrap($tag, $tag->locals->page['subtitle']); }
	public static function tag_page_date(FTL_Binding $tag) { return self::format_date($tag, $tag->locals->page['date']); }


	// ------------------------------------------------------------------------
	

	/**
     * Return the page title
     *
     * @usage : <ion:title [tag="h2" from="parent" up="2" ] />
     *
     *            The "from" attribute works with th "up" attribute. up="2" means the parent from the parent, etc.
     *            If the "up" attribute isn't set when using "parent", it will be set to 1.Means the returned title will be the one of the parent page of the current page.
     *            
     * @returns     String        The page title, wrapped or not by the optional defined tags.
     *
     */
    public static function tag_page_title(FTL_Binding $tag)    
    {
        // Is the asked title from another page ?
        $from = (isset($tag->attr['from'])) ? $tag->attr['from'] : FALSE ;

        if ($from == 'parent')
        {
            $up = (isset($tag->attr['up'])) ? $tag->attr['up'] : 1 ;
            
            // Get the Breadcrumbs array
            $breacrumbs = self::get_breadcrumb_array($tag->locals->page, $tag->locals->pages, Settings::get_lang() );
            
            // Filter appearing pages
            $breacrumbs = array_values(array_filter($breacrumbs, array(__CLASS__, '_filter_appearing_pages')));
            
            // Reverse the breadcrumbs array
            $breacrumbs = array_reverse($breacrumbs);
            
            if ( ! empty($breacrumbs[$up]))
            {
                return self::wrap($tag, $breacrumbs[$up]['title']);
            }
        }
        
        return self::wrap($tag, $tag->locals->page['title']);
    }
    

	// ------------------------------------------------------------------------
	
	
	public static function tag_page_meta_title(FTL_Binding $tag)
	{
		// Tag cache
		if (($str = self::get_cache($tag)) !== FALSE)
			return $str;

		$meta_title = '';

		// Get the potential special URI
		$uri_config = self::$ci->config->item('special_uri');
		$special_uri = (isset(self::$uri_segments[1])) ? self::$uri_segments[1] : FALSE;
		
		if ($special_uri !== FALSE && ! array_key_exists($special_uri, $uri_config) )
		{
			// Try to find an article with the name of the last part of the URL.
			$uri_segments = self::$uri_segments;
			$name = array_pop(array_slice($uri_segments, -1));

			$article =  self::$ci->article_model->get(
				array('name' => $name), 
				Settings::get_lang()
			);

			if ( ! empty ($article['meta_title']))
				$meta_title = $article['meta_title'];
		}
		
		// First, try to get the meta title
		if ( $meta_title == '' && ! empty($tag->locals->page['meta_title']) )
		{
			$meta_title = $tag->locals->page['meta_title'];
		}

		// If no meta title, get the title as alternative
		if ( $meta_title == '' && ! empty($tag->locals->page['title']) )
		{
			$meta_title = $tag->locals->page['title'];		
		}
		
		// Remove HTML tags from meta title
		$meta_title = strip_tags($meta_title);
		
		// Tag cache
		self::set_cache($tag, self::wrap($tag, $meta_title));
		
		return self::wrap($tag, $meta_title);
	}


	// ------------------------------------------------------------------------
	

	/**
	 * Returns the medias tag content
	 * 
	 * @return
	 * @attributes	range	Range of media to display. Starts at 0.
	 *						If only one number is provided, returns all the medias from this index 
	 *						if the attribute "num" is not set
	 * 						example of use : 	<ion:medias range="2,4" />
	 *											<ion:medias range="2" />
	 *
	 *				num		Number of pictures to display.
	 *						Combined to the "range" attribute, you can display x medias from a given start index.
	 * 						example of use : 	Display 2 first medias : 
	 *					 						<ion:medias num="2" />
	 *											Display 3 medias starting from index 2 :
	 *											<ion:medias range="2" num="3" />
	 *
	 *
	 */
	public static function tag_page_medias(FTL_Binding $tag)
	{
		$medias = ( ! empty($tag->locals->page['medias'])) ? $tag->locals->page['medias'] : FALSE;
		
		if ( $medias !== FALSE)
		{
			return self::wrap($tag, TagManager_Media::get_medias($tag, $medias));
		}
		return ;
	}


	// ------------------------------------------------------------------------
	
	
	public static function tag_page_content(FTL_Binding $tag)
	{
		$content = ( ! empty($tag->locals->page['content'])) ? $tag->locals->page['content'] : '';

		return $content;
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
	private static function get_adjacent_page($tag, $mode='prev')
	{
		$mode = ($mode=='prev') ? -1 : 1;
		
		$menu_name = isset($tag->attr['menu']) ? $tag->attr['menu'] : 'main';
		$id_menu = 1;
		foreach($tag->globals->menus as $menu)
		{
			if ($menu_name == $menu['name'])
			{
				$id_menu = $menu['id_menu'];
			}	
		}

		$level = isset($tag->attr['level']) ? $tag->attr['level'] : 0;
		
		$current_page =& $tag->locals->page;
		
		// Order the pages.		
		$ordered_pages = array();
		if ( empty($tag->globals->pages_ordered))
		{
			self::order_pages($tag->globals->pages, $ordered_pages);
			$tag->globals->pages = $ordered_pages;
			$tag->globals->pages_ordered = TRUE;
		}
		
		$global_pages = $tag->globals->pages;
		
		// Filter by menu and asked level : We only need the asked level pages !
		// $pages = array_filter($global_pages, create_function('$row','return ($row["level"] == "'. $level .'" && $row["id_menu"] == "'. $id_menu .'") ;'));
		$pages = array();
		foreach($global_pages as $p)
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
	 * Processes the next / previous page tags result
	 * Internal use only.
	 *	 
	 */
	private static function process_next_prev_page($tag, $page)
	{
		if ($page != FALSE)
		{
			// helper
			$helper = (isset($tag->attr['helper']) ) ? $tag->attr['helper'] : 'navigation';

			// Get helper method
			$helper_function = (substr(strrchr($helper, ':'), 1 )) ? substr(strrchr($helper, ':'), 1 ) : 'get_next_prev_page';
			$helper = (strpos($helper, ':') !== FALSE) ? substr($helper, 0, strpos($helper, ':')) : $helper;
	
			// Prefix ?
			$prefix = (!empty($tag->attr['prefix']) ) ? $tag->attr['prefix'] : '';
	
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


	// ------------------------------------------------------------------------
	
	

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
		$subtag_open = (isset($tag->attr['subtag'])) ? '<' . $tag->attr['subtag'] . '>' : '';
		$subtag_close = (isset($tag->attr['subtag'])) ? '</' . $tag->attr['subtag'] . '>' : '';
		
		$separator = (isset($tag->attr['separator']) ) ? htmlentities(html_entity_decode($tag->attr['separator'])) : ' &raquo; ';

		$starting_level = (isset($tag->attr['starting_level']) ) ? $tag->attr['starting_level'] : FALSE;

		// Current page ID
		$current_page_id = $tag->globals->page['id_page'];
		
		// Get the Breadcrumbs array
		$lang = Settings::get_lang();
		$breacrumbs = self::get_breadcrumb_array($tag->locals->page, $tag->locals->pages, $lang );
		
		// Filter appearing pages
		$breacrumbs = array_values(array_filter($breacrumbs, array(__CLASS__, '_filter_appearing_pages')));
		
		if ($starting_level != FALSE)
		{
			// $breacrumbs =  array_values(array_filter($breacrumbs, create_function('$row','return $row["level"] >= '. $starting_level .';')));
			$new_breadcrumbs = array();
			foreach($breacrumbs as $b)
			{
				if ($b['level'] >= $starting_level)
					$new_breadcrumbs[] = $b;
			}
			$breacrumbs = $new_breadcrumbs;
		}

		// Build the links
		$return = '';

		for($i=0; $i<count($breacrumbs); $i++)
		{
			$url = $breacrumbs[$i]['absolute_url'];
			
			// Adds the suffix if defined
			if ( config_item('url_suffix') != '' ) $url .= config_item('url_suffix');

			$return .= ($return != '') ? $separator : '';

			$return .= $subtag_open . '<a href="'.$url.'">'.$breacrumbs[$i]['title'].'</a>' . $subtag_close;
		}
		
		return self::wrap($tag, $return);
	}
	
	

	/**
	 * Returns the breadcrumb data
	 *
	 * @param	Array	The starting page
	 * @param	Array	All the pages
	 * @param	String	Current language code
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


	private static function get_page_view($page)
	{
		$view = FALSE;

		if ( ! empty(self::$_article))
			$view = ($page['view_single'] != FALSE) ? $page['view_single'] : $page['view'];
		else
			$view = $page['view'];

		$view_path = Theme::get_theme_path().'views/'.$view.EXT;

		// Return the Ionize core view
		if ( ! file_exists($view_path) OR empty($view))
			$view = Theme::get_default_view('page');

		return $view;
	}
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Renders the <ion:articles /> last_article sub tag
	 *
	 */
	public static function tag_last_articles_article(FTL_Binding $tag)
	{
		return self::tag_articles_article($tag);
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Filters page which should appear
	 * used by self::tag_navigation()
	 *
	 */
	public static function _filter_appearing_pages($row)
	{
		return ($row['appears'] == 1);
	}


	// ------------------------------------------------------------------------


	private function _filter_pages_authorization($row)
	{
		// If the page group != 0, then get the page group and check the restriction
		if($row['id_group'] != 0)
		{
			self::$ci->load->model('connect_model');
			$page_group = FALSE;
			
			$groups = self::$ci->connect_model->get_groups();
			
			// Get the page group
			foreach($groups as $group)
			{
				if ($group['id_group'] == $row['id_group']) $page_group = $group;
			} 

			// If the current connected user has access to the page return TRUE
			if (self::$user !== FALSE && $page_group != FALSE && self::$user['group']['level'] >= $page_group['level'])
				return TRUE;
			
			// If nothing found, return FALSE
			return FALSE;
		}
		return TRUE;
	}

}


/* End of file Page.php */
/* Location: /application/libraries/Tagmanager/Page.php */