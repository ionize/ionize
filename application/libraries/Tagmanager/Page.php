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
 * Page TagManager 
 *
 */
require_once APPPATH.'libraries/Pages.php';

class TagManager_Page extends TagManager
{
	protected static $user = FALSE;

	protected static $http_status_code = array(
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not Found',
	);


	/**
	 * Ordered pages, used by get_adjacent_page()
	 * @var null
	 */
	protected static $ordered_pages = NULL;


	public static $tag_definitions = array
	(
		'pages' => 				'tag_pages',
		'page' => 				'tag_page',
		'page:view' => 			'tag_page_view',
		'page:next' =>			'tag_next_page',
		'page:prev' =>			'tag_prev_page',
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

		// Models
        self::$ci->load->model(
            array(
                'article_model',
                'page_model',
                'url_model'
            ), '', TRUE);

		// Helpers
		self::$ci->load->helper('text');

		// Pages, Page
		self::register('pages', Pages::get_pages());
		self::register('page', self::get_current_page());

		// Current page
		$page = self::registry('page');

		// Last option : Even the 404 wasn't found...
		if (empty($page['id_page']))
		{
			echo 'Not found';
			die();
		}

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
		if ( $entity['type'] == 'article')
		{
			$article = self::$ci->article_model->get_by_id($entity['id_entity'], Settings::get_lang());
			$articles = array($article);
			TagManager_Article::init_articles_urls($articles);
			$article = $articles[0];
		}

		if ( ! empty($article))
			self::register('article', $article);

		// Event : On before render
		$event_data = array(
			'entity' => $entity,
			'article' => self::registry('article')
		);
		Event::fire('Page.render.before', $event_data);

		self::$view = self::_get_page_view($page);

		self::render();
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
				$page = self::get_page_by_short_url(self::$ci->uri->segment(3));
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
			self::set_400_output(404);
		}
		else
		{
			$resource = 'frontend/page/' . $page['id_page'];

			if ( Authority::cannot('access', $resource, NULL, TRUE))
			{
				$http_code = $page['deny_code'];
				$page = self::get_page_by_code($page['deny_code']);
				self::set_400_output($http_code);
			}
		}

		// Add index to identify current page
		$page['__current__'] = TRUE;

		return $page;
	}


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


	public static function get_relative_parent_page($page, $rel = -1, $include_hiddens = FALSE)
	{
		$parent = array();

		$p_arr = $include_hiddens ? explode('/', $page['full_path_ids']) : explode('/', $page['path_ids']);

		$idx = count($p_arr) -1 + $rel;

		if (isset($p_arr[$idx]))
		{
			$parent = self::get_page_by_id($p_arr[$idx]);
		}

		return $parent;
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
	 * Get one page from its short URL
	 *
	 * @param	string	Page short URL
	 * @return	array	Page data array
	 *
	 */
	public static function get_page_by_short_url($url)
	{
		foreach(self::$context->registry('pages') as $p)
		{
			if ($p['url'] == $url)
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
	 * Set 400 page
	 *
	 * @return void
	 * @param int $code
	 */
	public function set_400_output($code = 404)
	{
		self::$ci->output->set_header($_SERVER["SERVER_PROTOCOL"]." ".$code." ".self::$http_status_code[$code]);

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

		// $current_page = self::$context->registry('page');
		// Get the current page: Fall down to registry if no one found in tag
		$current_page = $tag->get('page');

		foreach(self::registry('menus') as $menu)
		{
			if ($menu_name == $menu['name'])
			{
				$id_menu = $menu['id_menu'];
			}
		}

		$level = is_null($tag->getAttribute('level')) ? 0 : $tag->getAttribute('level');

		// Order the pages, because the are not.
		if (is_null(self::$ordered_pages))
		{
			self::$ordered_pages = array();
			self::order_pages(self::registry('pages'), self::$ordered_pages);
		}

		// Filter by menu and asked level : We only need the asked level pages !
		$pages = array();
		foreach(self::$ordered_pages as $p)
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
	 * @param FTL_Binding $tag
	 * @param array
	 *
	 * @return string
	 */
	private static function process_prev_next_page(FTL_Binding $tag, $page = NULL)
	{
		$str = '';
		if ($page)
			$str = self::wrap($tag, $tag->expand());

		return $str;
	}


	// -- TAGS DEFINITION ------------------------------------------------------------------------


	/**
	 * @param FTL_Binding $tag
	 *
	 * @return String
	 *
	 */
	public static function tag_page(FTL_Binding $tag)
	{
		$cache = $tag->getAttribute('cache', TRUE);

		// Tag cache
		if ($cache == TRUE && ($str = self::get_cache($tag)) !== FALSE)
			return $str;

		// Returned string
		$str = '';

		$id = $tag->getAttribute('id');
		$parent = $tag->getAttribute('parent');

		// No ID
		if ( is_null($id) )
			if (is_null($tag->get('page')))
				$page = self::$context->registry('page');
			else
				$page = $tag->get('page');
		else
		{
			if (strval((int)$id) == (string) $id)
				$page = self::get_page_by_id($id);
			else
				$page = self::get_page_by_code($id);
		}

		// Extend Fields tags
		self::create_extend_tags($tag, 'page');

		// Get the asked parent page : From current page or from page ID
		if (!is_null($parent))
		{
			$all_parents = ( $tag->getAttribute('all-parents') == TRUE) ? TRUE : FALSE;

			// Path IDs
			if ($all_parents)
				$path_ids = explode('/', $page['full_path_ids']);
			else
				$path_ids = explode('/', $page['path_ids']);

			if ($parent == 0) $parent = -1;
			if ($parent > 0) $parent = -$parent;

			$level = $page['level'] + $parent;

			if (isset($path_ids[$level]))
			{
				$page = self::get_page_by_id($path_ids[$level]);
			}
			else
			{
				// One parent was asked, but no one was found : no page and that's it.
				$page = NULL;
			}
		}

		if ( ! empty($page))
		{
			$tag->set('page', $page);
			$tag->set('index', 0);
			$tag->set('count', 1);

			if ( ! is_null($tag->getAttribute('render')))
			{
				$current_page = self::$context->registry('page');

				if (
					$page['id_page'] != $current_page['id_page']
					&& self::_get_page_view($page) != self::_get_page_view($current_page)
				)
				{
					return self::find_and_parse_page_view($tag, $page);
				}
				else
				{
					return '';
				}
			}

			$str .= $tag->expand();

			// Tag cache
			self::set_cache($tag, $str);
		}
		return $str;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the current used page view
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_page_view(FTL_Binding $tag)
	{
		return self::output_value($tag, self::$view);
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns pages from one given parent page
	 *
	 * @TODO	Check and finish writing
	 * 			Planned for 1.0
	 *
	 * @param FTL_Binding
	 *
	 * @return mixed
	 */
	public static function tag_pages(FTL_Binding $tag)
	{
		$cache = $tag->getAttribute('cache', TRUE);

		// Tag cache
//		if ($cache == TRUE && ($str = self::get_cache(FTL_Binding $tag)) !== FALSE)
//			return $str;

		// Returned string
		$str = '';

		$parent = $tag->getAttribute('parent');
		$mode = $tag->getAttribute('mode', 'flat');
		$levels = $tag->getAttribute('levels');
		$menu_name = $tag->getAttribute('menu');
		$parent_page = NULL;
		// Display hidden navigation elements ?
		$display_hidden = $tag->getAttribute('display_hidden', FALSE);
		$limit = $tag->getAttribute('limit');

		if ( ! is_null($parent))
		{
			if (strval(abs((int)$parent)) == (string)$parent)
				$parent_page = self::get_page_by_id($parent);
			else if(substr($parent, 0, 1) == '-')
				$parent_page = self::get_relative_parent_page(self::registry('page'), $parent, $display_hidden);
			else if($parent == 'this')
				$parent_page = self::registry('page');
			else
				$parent_page = self::get_page_by_code($parent);
		}

		$data = self::registry('pages');

		if ( ! empty($parent_page))
		{
			if ($mode == 'tree')
				$pages = Structure::get_tree_navigation($data, $parent_page['id_page']);
			else
			{
				$pages = array();
				Structure::get_nested_structure($data, $pages, $parent_page['id_page']);
			}
		}
		else
		{
			$pages = self::registry('pages');
		}

		// Limit pages to a certain level
		if ( ! is_null($levels))
		{
			$levels = (int)$levels;

			for($i = count($pages) - 1; $i >= 0; $i--) {
				if($pages[$i]['level'] > $levels)
					unset($pages[$i]);
			}

			$pages = array_values($pages);
		}

		// Limit pages to a certain menu
		if ( ! is_null($menu_name))
		{
			// By default main menu
			$id_menu = 1;
			foreach(self::registry('menus') as $menu)
			{
				if ($menu_name == $menu['name'])
					$id_menu = $menu['id_menu'];
			}

			for($i = count($pages) - 1; $i >= 0; $i--) {
				if($pages[$i]['id_menu'] != $id_menu)
					unset($pages[$i]);
			}

			$pages = array_values($pages);
		}

		if ($display_hidden == FALSE)
			$pages = array_values(array_filter($pages, array('TagManager_Page', '_filter_appearing_pages')));

		if ( ! is_null($limit))
			$pages = array_slice($pages, 0, $limit);

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
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
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
		$tag->set('data', $page);

		return self::process_prev_next_page($tag, $page);
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
		$tag->set('data', $page);

		return self::process_prev_next_page($tag, $page);
	}


	// ------------------------------------------------------------------------


	/**
	 * Displays the breacrumb : You are here !
	 *
	 * @param	FTL_Binding object
	 * @return	String	The parsed view
	 *
	 * @usage	<ion:breadcrumb [level="2" separator=" &bull; " tag="ul" class="breadcrumb" child-tag="li" child-class="breadcrump-item"] />
	 *
	 */
	public static function tag_breadcrumb(FTL_Binding $tag)
	{
		// Child tag : HTML tag for each element
		$child_tag =  $tag->getAttribute('child-tag');
		$child_class =  $tag->getAttribute('child-class');
		$child_class = ! is_null($child_class) ? ' class="'.$child_class.'"': '';

		// Child tag cosmetic
		$child_tag_open = ! is_null($child_tag) ? '<' . $child_tag . $child_class . '>' : '';
		$child_tag_close = ! is_null($child_tag) ? '</' . $child_tag .'>' : '';

		$separator = $tag->getAttribute('separator', '&nbsp;&raquo;&nbsp;');
		$separator_tag = $tag->getAttribute('separator_tag');
		$separator_class = $tag->getAttribute('separator_class');
		$separator_class = ! is_null($separator_class) ? ' class="'.$separator_class.'"': '';

		if (!is_null($separator_tag))
		{
			$separator = '<' . $separator_tag . $separator_class . '>'.$separator.'</' . $separator_tag .'>';
		}

		$level = $tag->getAttribute('level', FALSE);

		// Pages && page
		$pages = self::$context->registry('pages');
		$page = self::$context->registry('page');

		// Get the Breadcrumbs array
		$lang = Settings::get_lang();
		$breadcrumb = self::get_breadcrumb_array($page, $pages, $lang );

		// Filter appearing pages
		$breadcrumb = array_values(array_filter($breadcrumb, array(__CLASS__, '_filter_appearing_pages')));

		if ($level != FALSE)
		{
			$new_breadcrumb = array();
			foreach($breadcrumb as $b)
			{
				if ($b['level'] >= $level)
					$new_breadcrumb[] = $b;
			}
			$breadcrumb = $new_breadcrumb;
		}

		// Build the links
		$return = '';

		// Add Home page ?
		if ($tag->getAttribute('home') == TRUE && $page['home'] == 0)
		{
			$home_page = self::get_home_page();
			$url = $home_page['absolute_url'];
			$return .= $child_tag_open . '<a href="'.$url.'">'.$home_page['title'].'</a>' . $separator . $child_tag_close;
		}

		// Pages
		$nb_pages = count($breadcrumb);
		for($i=0; $i<$nb_pages; $i++)
		{
			$url = $breadcrumb[$i]['absolute_url'];

			// Adds the suffix if defined
			if ( config_item('url_suffix') != '' ) $url .= config_item('url_suffix');

			$return .= $child_tag_open . '<a href="'.$url.'">'.$breadcrumb[$i]['title'].'</a>' ;
			if ($i<($nb_pages-1))
				$return .= $separator;

			$return .= $child_tag_close;
		}

		// Current Article ?
		if ($tag->getAttribute('article') == TRUE)
		{
			$article = self::registry('article');

			if ($article)
			{
				$separator = ($return != '') ? $separator : '';
				$return .= $child_tag_open . $separator .$article['title'] . $child_tag_close;
			}
		}

		// Prefix process
		$return = self::prefix_suffix_process($return, $tag->getAttribute('prefix'));

		return self::wrap($tag, $return);
	}


	// ------------------------------------------------------------------------


	/**
	 * Find and parses the page view
	 *
	 * @param 	FTL_Binding
	 * @param   array
	 *
	 * @return string
	 *
	 */
	private static function find_and_parse_page_view(FTL_Binding $tag, $page)
	{
		// Default page view
		if (empty($page['view']))
			$page['view'] = Theme::get_default_view('page');

		// View path
		$view_path = Theme::get_theme_path().'views/'.$page['view'].EXT;

		// Return the Ionize default's theme view
		if ( ! file_exists($view_path))
		{
			$view_path = Theme::get_theme_path().'views/'.Theme::get_default_view('page').EXT;
			if ( ! file_exists($view_path))
				$view_path = APPPATH.'views/'.Theme::get_default_view('page').EXT;
		}

		return $tag->parse_as_nested(file_get_contents($view_path));
	}




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
	 * Get the view for the asked page
	 *
	 * @param array
	 *
	 * @return bool|String
	 *
	 */
	private static function _get_page_view($page)
	{
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
