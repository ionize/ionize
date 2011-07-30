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
// require_once APPPATH.'libraries/Tagmanager.php';

class TagManager_Page extends TagManager
{	
	protected static $_inited = false;
	
	protected static $pagination_uri = '';
	
	protected static $user = FALSE;
	
	protected static $uri_segments = array();

	protected static $categories = FALSE;

	
	public static $tag_definitions = array
	(
		// Page
		'count' => 				'tag_count',
		'period' => 			'tag_period',
		'pagination' =>			'tag_pagination',
		'absolute_url' =>		'tag_absolute_url',
		'first_item' => 		'tag_first_item',
		'last_item' => 			'tag_last_item',
		'next_page' =>			'tag_next_page',
		'prev_page' =>			'tag_prev_page',
		'next_article' =>		'tag_next_article',
		'prev_article' =>		'tag_prev_article',
		
		// Languages
		'languages' =>				'tag_languages',
		'languages:language' =>		'tag_languages_language',
		'languages:name' =>			'tag_languages_name',
		'languages:code' =>			'tag_languages_code',
		'languages:active_class' =>	'tag_languages_active_class',
		'languages:url' =>			'tag_languages_url',
		
		// Page
		'id_page' => 			'tag_page_id',
		'page:name' => 			'tag_page_name',
		'page:url' => 			'tag_page_url',
		'title' => 				'tag_page_title',
		'subtitle' => 			'tag_page_subtitle',
		'meta_title' => 		'tag_page_meta_title',
		'content' =>			'tag_page_content',
		
		// Breadrumb
		'breadcrumb' =>			'tag_breadcrumb',
		
			
		// Articles
		'articles' => 				'tag_articles',
		'articles:article' => 		'tag_articles_article',
		'articles:id_article' => 	'tag_article_id',
		'articles:active_class' => 	'tag_article_active_class',
		'articles:view' => 			'tag_article_view',
		'articles:author' => 		'tag_article_author_name',
		'articles:author_email' => 	'tag_article_author_email',
		'articles:name' => 			'tag_article_name',
		'articles:title' => 		'tag_article_title',
		'articles:subtitle' => 		'tag_article_subtitle',
		'articles:meta_title' =>    'tag_article_meta_title',
		'articles:date' => 			'tag_article_date',
		'articles:content' => 		'tag_article_content',
		'articles:url' => 			'tag_article_url',
		'articles:link' => 			'tag_article_link',
		'articles:categories' => 	'tag_article_categories',
		'articles:readmore' => 		'tag_article_readmore',
		'articles:index' => 		'tag_article_index',
		'articles:count' => 		'tag_article_count',
		
		// Categories
		'category' =>					'tag_category',
		'categories' => 				'tag_categories',
		'categories:url' => 			'tag_category_url',
		'categories:active_class' => 	'tag_category_active_class',
		'categories:title' => 			'tag_category_title',
		'categories:subtitle' => 		'tag_category_subtitle',
		
		// Archives
		'archive' =>				'tag_archive',
		'archives' =>				'tag_archives',
		'archives:url' => 			'tag_archives_url',
		'archives:lang_url' => 		'tag_archives_lang_url',
		'archives:period' => 		'tag_archives_period',
		'archives:nb' => 			'tag_archives_nb',
		'archives:active_class' => 	'tag_archives_active_class'
	);


	// ------------------------------------------------------------------------


	/**
	 * 
	 * 
	 */
	public static function init()
	{
 		//parent::init('Page');
		
		self::$ci =& get_instance(); 

		// Article model
		self::$ci->load->model('article_model');
		self::$ci->load->model('page_model');

		// Helpers
		self::$ci->load->helper('text');

		$uri = preg_replace("|/*(.+?)/*$|", "\\1", self::$ci->uri->uri_string);
		self::$uri_segments = explode('/', $uri);


		/* Add all pages to the context
		 *
		 */
		$pages = self::$ci->page_model->get_lang_list(false, Settings::get_lang());

		/* Spread authorizations from parents pages to chidrens.
		 * This adds the group ID to the childrens pages of a protected page
		 * If you don't want this, just uncomment this line.
		 */
		if (Connect()->logged_in())
			self::$user = Connect()->get_current_user();
		 
		self::$ci->page_model->spread_authorizations($pages);

		// Filter pages regarding the authorizations
		$pages = array_values(array_filter($pages, array(__CLASS__, '_filter_pages_authorization')));


		// Add pages to the context
		if ( empty(self::$context->globals->pages))
		{
			self::$context->globals->pages = $pages;
			
			// Set all abolute URLs one time, for perf.
			self::init_absolute_urls();
		}
		
// Pagination URI
//		$uri_config = self::$ci->config->item('special_uri');
//		$uri_config = array_flip($uri_config);
//		self::pagination_uri = $uri_config['pagination'];
	

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
					$rel = explode('.', $page['link_id']);
					
					if ($page = array_get(self::$context->globals->pages, $rel[0], 'id_page'))
					{
						$articles =  self::$ci->article_model->get_lang_list
						(
							array('id_article' => $rel[1]), 
							Settings::get_lang()
						);
						self::init_articles_urls($articles);
						$article = array_shift($articles);
						
						redirect($article['url']);
					}
				}	
			}
		}
		
		self::$view = ($page['view'] != false) ? $page['view'] : Theme::get_default_view('page');
		
		self::render();
	}
	

	// ------------------------------------------------------------------------

	
	public function add_globals()
	{
		parent::add_globals();
		
		// Get current asked page
		self::$context->globals->page = self::get_current_page(self::$ci->uri->segment(3));
		
		// Show 404 if no page
		if(empty(self::$context->globals->page))
		{
			self::set_404();
		}
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

		self::$context->globals->page = self::get_current_page('404');
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
	 * Set all the absolute URLs, for pages but also for languages
	 *
	 *
	 */
	public function init_absolute_urls()
	{
		foreach (self::$context->globals->pages as &$page)
		{
			// Set the page complete URL
			$page['absolute_url'] = '';

			// Link
			if ($page['link_type'] != '' )
			{
				// External
				if ($page['link_type'] == 'external')
				{
					$page['absolute_url'] = $page['link'];
				}
				else if ($page['link_type'] == 'email')
				{
					$page['absolute_url'] = auto_link($page['link'], 'both', TRUE);
				}
				// Internal
				else
				{
					// Article
					if($page['link_type'] == 'article')
					{
						// Get the article to which this page links
						$rel = explode('.', $page['link_id']);
						$target_article = self::$ci->article_model->get_context($rel[1], $rel[0], Settings::get_lang('current'));

						// Of course, only if not empty...
						if ( ! empty($target_article))
						{
							// Get the article's parent page
							$page['absolute_url'] = '';
							
							foreach(self::$context->globals->pages as $p)
							{
								if ($p['id_page'] == $target_article['id_page'])
									$page['absolute_url'] = $p['url'] . '/' . $target_article['url'];
							}
						}
					}
					// Page
					else
					{
						// Get the page to which the page links
						// $target_page = array_values(array_filter($con->globals->pages, create_function('$row','return $row["id_page"] == "'. $page['link_id'] .'";')));
						// if ( ! empty($target_page))
						// {
						//	$page['absolute_url'] = $target_page[0]['url'];
						// }
						$page['absolute_url'] = '';
						
						foreach(self::$context->globals->pages as $p)
						{
							if ($p['id_page'] == $page['link_id'])
								$page['absolute_url'] = $p['url'];
						}
					}
					if ( count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1' )
					{
						$page['absolute_url'] =  Settings::get_lang(). '/' . $page['absolute_url'];
					}
					$page['absolute_url'] = base_url() . $page['absolute_url'];

				}
			}
			else
			{
				if ( count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1' )
				{
					// Home page : doesn't contains the page URL
					if ($page['home'] == 1 )
					{
						// Default language : No language code in the URL for the home page
						// Other language : The home page has the lang code in URL
						if (Settings::get_lang('default') != Settings::get_lang())
						{
							$page['absolute_url'] = Settings::get_lang();
						}
					}
					// Other pages : lang code in URL
					else
					{
						// If page URL if already set because of a link, don't replace it.
						$page['absolute_url'] = ($page['absolute_url'] != '') ? Settings::get_lang() . '/' . $page['absolute_url'] : Settings::get_lang() . '/' . $page['url'];
					}
	
					$page['absolute_url'] = base_url() . $page['absolute_url'];
					
					// Set the lang code depending URL (used by language subtag)
					$page['absolute_urls'] = array();
	
					foreach (Settings::get_online_languages() as $lang)
					{
						if ($page['home'] == 1 )
						{
							// Default language : No language code in the URL for the home page
							if (Settings::get_lang('default') == $lang['lang'])
							{
								$page['absolute_urls'][$lang['lang']] = base_url();
							}
							// Other language : The home page has the lang code in URL
							else
							{
								$page['absolute_urls'][$lang['lang']] = base_url() . $lang['lang'];
							}
						}
						// Other pages : lang code in URL
						else
						{
							$page['absolute_urls'][$lang['lang']] = base_url() . $lang['lang'] . '/' . $page['urls'][$lang['lang']];
						}
					}
				}
				else
				{

					if ($page['home'] == 1)
					{
						$page['absolute_url'] = base_url();
					}
					else
					{
						$page['absolute_url'] = base_url() . $page['url'];
					}
					// Set the lang code depending URL (used by language subtag)
					$page['absolute_urls'][Settings::get_lang()] = $page['absolute_url'];
				}
			}				
		}
	}
	
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Inits articles URLs
	 * Get the contexts of all given articles and deifne each article correct URL
	 *
	 */
	private function init_articles_urls(&$articles)
	{
		$articles_id = array();
		foreach($articles as $article)
		{
			$articles_id[] = $article['id_article'];
		}
		$pages_context = self::$ci->page_model->get_lang_contexts($articles_id, Settings::get_lang('current'));
		
		// Add pages contexts data to articles
		foreach($articles as &$article)
		{
			$contexts = array();
			foreach($pages_context as $context)
			{
				if ($context['id_article'] == $article['id_article'])
					$contexts[] = $context;
			}
			
			$page = array_shift($contexts);

			// Find the Main Parent
			if ( ! empty($contexts))
			{
				foreach($contexts as $context)
				{
					if ($context['main_parent'] == '1')
						$page = $context;
				}
			}
			$url = $article['url'];

//			$article['lang_url'] = 	base_url() . Settings::get_lang('current') . '/' . $page['url'] . '/' . $url;

			if ( count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1' )
			{
				$article['url'] = base_url() . Settings::get_lang('current') . '/' . $page['url'] . '/' . $url;
			}
			else
			{
				$article['url'] = base_url() . $page['url'] . '/' . $url;			
			}
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
	 * Returns asked page
	 *
	 * @param		FTLBinding		Tag
	 * @return		array			Asked page
	 *
	 */
	function get_asked_page($tag)
	{
		$from_page = (isset($tag->attr['from']) ) ? $tag->attr['from'] : FALSE;
	
		// All pages
		$pages =&  $tag->locals->pages;
	
		// Current page
		$page = $tag->locals->page;
	
		// If a page name is set, try to get it
		if ($from_page !== FALSE)
		{
			// Get the asked page details
//			$page = array_values(array_filter($pages, create_function('$row','return $row["name"] == "'. $from_page .'";')));
			foreach($pages as $p)
			{
				if ($p['name'] == $from_page)
					return $p;
			}
		}
		return $page;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get Articles
	 * @param	
	 *
	 * 1. Try to get the articles from a special URI
	 * 2. Get the articles from the current page
	 * 3. Filter on the article name if the article name is in URI segment 1
	 *
	 */
	function get_articles($tag)
	{
		$articles = array();

		// Page from locals
		$pages =& $tag->locals->pages;


		// Get the potential special URI
		$special_uri = (isset(self::$uri_segments[1])) ? self::$uri_segments[1] : FALSE;

		// Use Pagination
		// The "articles" tag must explicitely indicates it want to use pagination. 
		// This explicit declaration is done to avoid all articles tags on one page using the same pagination value.
		$use_pagination = (isset($tag->attr['pagination']) && $tag->attr['pagination'] == 'TRUE') ? TRUE : FALSE;

		// Don't use the "article_list_view" setting set through Ionize
		$keep_view = (isset($tag->attr['keep_view'])) ? TRUE : FALSE;

		// Use this view for each article if more than one article
		$list_view = (isset($tag->attr['list_view'])) ? $tag->attr['list_view'] : FALSE;


		// Number of article limiter
		$num = (isset($tag->attr['limit'])) ? self::get_attribute($tag, 'limit') : 0 ;
		if ($num == 0)
			$num = (isset($tag->attr['num'])) ? self::get_attribute($tag, 'num') : 0 ;

		// Get the special URI config array (see /config/ionize.php)
		$uri_config = self::$ci->config->item('special_uri');

		// filter & "with" tag compatibility
		// create a SQL filter
		$filter = (isset($tag->attr['filter']) && $tag->attr['filter'] != '') ? $tag->attr['filter'] : FALSE;

		/* Scope can be : 
		 * not defined : 	means current page
		 * "page" :			current page
		 * "parent" :		parent page
		 * "global" :		all pages from the website
		 * "pages" : 		one or more page names. Not done for the moment
		 *
		 */
		$scope = (isset($tag->attr['scope']) && $tag->attr['scope'] != '' ) ? $tag->attr['scope'] : FALSE;

		// from page name ?
		// $from_page = (isset($tag->attr['from']) && $tag->attr['from'] !='' ) ? $tag->attr['from'] : FALSE;
		$from_page = (isset($tag->attr['from']) && $tag->attr['from'] !='' ) ? self::get_attribute($tag, 'from') : FALSE;

		// from categories ? 
		$from_categories = (isset($tag->attr['from_categories']) && $tag->attr['from_categories'] != '') ? self::get_attribute($tag, 'from_categories') : FALSE;
		$from_categories_condition = (isset($tag->attr['from_categories_condition']) && $tag->attr['from_categories_condition'] != 'or') ? 'and' : 'or';

		// Order. Default order : ordering ASC
		$order_by = (isset($tag->attr['order_by']) && $tag->attr['order_by'] != '') ? $tag->attr['order_by'] : 'ordering ASC';

		/*
		 * Preparing WHERE on articles
		 * From where do we get the article : from a page, from the parent page or from the all website ?
		 *
		 */
		$where = array(
			'order_by' => $order_by
		);

		// Add type to the where array
		if ( isset ($tag->attr['type']) )
		{
			// Case of empty type declared
			if ($tag->attr['type'] == '')
			{
				$where['article_type.type'] = 'NULL';
			}
			else
			{
				$where['article_type.type'] = $tag->attr['type'];
			}
		}

		// If a page name is set, returns only articles from this page
		if ($from_page !== FALSE)
		{
			// Get the asked page details
			$asked_pages = explode(',', $from_page);

			$in_pages = array();
			
			// Check if one lang URL of each page can be used for filter
			foreach($pages as $page)
			{
				if (in_array($page['name'], $asked_pages))
					$in_pages[] = $page['id_page'];
			}

			// If not empty, filter articles on id_page
			if ( ! empty($in_pages))
			{
				$where['id_page in'] = '('.implode(',', $in_pages).')';
			}
			// else return nothing. Seems the asked page doesn't exists...
			else
			{
				return;
			}
		}
		else if ($scope == 'parent')
		{
			$where += self::set_parent_scope($tag);
		}
		else if ($scope == 'global')
		{
			$where += self::set_global_scope($tag);
		}
		// Get only articles from current page
		else
		{
			$where['id_page'] = $tag->locals->page['id_page'];
		}

		/* Get the articles
		 *
		 */
		// If a special URI exists, get the articles from it.
		if ($special_uri !== FALSE && array_key_exists($special_uri, $uri_config) && $from_page === FALSE)
		{
			if (method_exists(__CLASS__, 'get_articles_from_'.$uri_config[$special_uri]))
			{
				$articles = call_user_func(array(__CLASS__, 'get_articles_from_'.$uri_config[$special_uri]), $tag, $where, $filter);
			}
		}
		// This case is very special : getting one article through his name in the URL
		else if ($special_uri !== FALSE && !array_key_exists($special_uri, $uri_config) && $from_page == FALSE && $scope == FALSE)
		{
			$articles = self::get_articles_from_one_article($tag, $where, $filter);
			
			// Get articles from 404
			if ( empty($articles))
			{
				self::set_404();
				$articles = self::$ci->article_model->get_lang_list(
					array('id_page' => $tag->locals->page['id_page']),
					$lang = Settings::get_lang()
				);
			}
		}
		// Get all the page articles
		// If Pagination is active, set the limit. This articles result is the first page of pagination
		else 
		{
			// Set Limit
			$limit = ( ! empty($tag->locals->page['pagination']) && ($tag->locals->page['pagination'] > 0) ) ? $tag->locals->page['pagination'] : FALSE;
			
			if ($limit == FALSE && $num > 0) $limit = $num;
			
			$where['limit'] = $limit;
			
			if ($from_categories !== FALSE)
			{
				$articles = self::$ci->article_model->get_from_categories(
					$where,
					explode(',', $from_categories),
					$from_categories_condition,
					$lang = Settings::get_lang(),
					$filter
				);
			}
			else
			{
				$articles = self::$ci->article_model->get_lang_list(
					$where,
					$lang = Settings::get_lang(),
					$filter
				);
			}
		}
		
		// Correct the articles URLs
		if (count($articles) > 0)
		{
			self::init_articles_urls($articles);
		}
		
		
		// Here, we are in an article list configuration : More than one article, page display
		// If the article-list view exists, we will force the article to adopt this view.
		// Not so much clean to do that in the get_article funtion but for the moment just helpfull...
		if (count($articles) > 1 && $keep_view == FALSE)
		{
			if ( ! empty($tag->locals->page['article_list_view']) OR $list_view !== FALSE )
			{
				foreach ($articles as $k=>$article)
				{
					// Set the article view to the page "article-list" value view.
					if ($list_view !== FALSE)
					{
						$articles[$k]['view'] = $list_view;
					}
					else
					{
						$articles[$k]['view'] = $tag->locals->page['article_list_view'];
					}
				}
			}
		}

		return $articles;
	}


	// ------------------------------------------------------------------------

	/**
	 * Pagination articles
	 *
	 * @param	Array	Current page array
	 * @param	Array	SQL Condition array
	 * @param	String	order by condition
	 * @param	String	Filter string
	 *
	 * @return	Array	Array of articles
	 *
	 */
	function get_articles_from_pagination($tag, $where, $filter)
	{
		$page = & $tag->locals->page;
		
		$uri_segments = self::$uri_segments;
		$start_index = array_pop(array_slice($uri_segments, -1));

		// Load CI Pagination Lib
		isset(self::$ci->pagination) OR self::$ci->load->library('pagination');
	
		// Number of displayed articles / page
		// If no pagination : redirect to the current page
		$per_page = (isset($page['pagination']) && $page['pagination'] > 0) ? $page['pagination'] : redirect(self::$uri_segments[0]);

		// from categories ? 
		$from_categories = (isset($tag->attr['from_categories']) && $tag->attr['from_categories'] != '') ? self::get_attribute($tag, 'from_categories') : FALSE;
		$from_categories_condition = (isset($tag->attr['from_categories_condition']) && $tag->attr['from_categories_condition'] != 'or') ? 'and' : 'or';
		
		$where['offset'] = (int)$start_index;
		$where['limit'] =  (int)$per_page;
		
		if ($from_categories !== FALSE)
		{
			$articles = self::$ci->article_model->get_from_categories(
				$where,
				explode(',', $from_categories),
				$from_categories_condition,
				$lang = Settings::get_lang(),
				$filter
			);
		}
		else
		{
			$articles = self::$ci->article_model->get_lang_list(
				$where,
				$lang = Settings::get_lang(),
				$filter
			);
		}

		// Set the view
		// Rule : If page has article_list_view defined, used this one.
		if($page['article_list_view'] != FALSE)
		{
			foreach ($articles as $k=>$article)
			{
				$articles[$k]['view'] = $page['article_list_view'];
			}
		}

		return $articles;
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Get articles linked to a category
	 * Called if special URI "category" is found. See tag_articles()
	 * Uses the self::$uri_segments var to determine the category name
	 *
	 * @param	array	Current page array
	 * @param	Array	SQL Condition array
	 * @param	String	order by condition
	 * @param	String	Filter string
	 *
	 * @return	Array	Array of articles
	 *
	 */
	function get_articles_from_category($tag, $where, $filter)
	{
		$page = & $tag->locals->page;

		// Get the start index for the SQL limit query param : last part of the URL
		$uri_segments = self::$uri_segments;
		$start_index = array_pop(array_slice($uri_segments, -1));


		// If category name exists
		if (isset(self::$uri_segments[2]))
		{
			// Limit
			$where['offset'] = $start_index;
			if ((int)$page['pagination'] > 0) $where['limit'] =  (int)$page['pagination'];

			// Get the articles
			$articles = self::$ci->article_model->get_from_category
			(
				$where, 
				self::$uri_segments[2], 
				Settings::get_lang(),
				$filter
			);

			return $articles;
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Get articles linked from a period
	 * Called if special URI "archives" is found. See tag_articles()
	 * Uses the self::$uri_segments var to determine the category name
	 *
	 * @param	Array	Current page array
	 * @param	Array	SQL Condition array
	 * @param	String	Filter string
	 *
	 * @return	Array	Array of articles
	 *
	 */
	function get_articles_from_archives($tag, $where, $filter)
	{
		$page = & $tag->locals->page;

		$start_index = 0;

		// Get the start index for the SQL limit query param : last part of the URL only if the 4th URI segmenet (pagination) is set
		if (isset(self::$uri_segments[4]))
		{
			$uri_segments = self::$uri_segments;
			$start_index = array_pop(array_slice($uri_segments, -1));
		}

		// If year is set
		if (isset(self::$uri_segments[2]))
		{
			$year = self::$uri_segments[2];
		
			$month = isset(self::$uri_segments[3]) ? self::$uri_segments[3] : NULL;
			
			$where['offset'] = $start_index;
			if ((int)$page['pagination'] > 0) $where['limit'] =  (int)$page['pagination'];

			$articles =  self::$ci->article_model->get_from_archives
			(
				$where, 
				$year, 
				$month, 
				Settings::get_lang(),
				$filter
			);

			return $articles;
		}
	}
	

	// ------------------------------------------------------------------------

	
	/**
	 * Returns one named article from website
	 * In this case, the current pag s not important, the URL asked article will be displayed.
	 * Gives the ability to display a given article at any place of the website.
	 *
	 * @param	array	Current page array
	 * @param	Array	SQL Condition array
	 * @param	String	Filter string
	 *
	 * @return	Array	Array of articles
	 */
	function get_articles_from_one_article($tag, $where, $filter)
	{
		$page = & $tag->locals->page;
	
		$articles = array();
		
		$uri_segments = self::$uri_segments;
		$name = array_pop(array_slice($uri_segments, -1));

		$where = array(
			'article_lang.url' => $name,
			'limit' => 1
		);

		$articles =  self::$ci->article_model->get_lang_list
		(
			$where, 
			Settings::get_lang(),
			$filter
		);
				
		return $articles;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the categories regarding the given page
	 *
	 */
	function get_categories($tag, $page)
	{
		// Categories model
		isset(self::$ci->category_model) OR self::$ci->load->model('category_model');
	
		$active_class = (isset($tag->attr['active_class']) ) ? $tag->attr['active_class'] : 'active';
		
		// Asked category
		$uri_segments = self::$uri_segments;
		$category_uri = array_pop(array_slice($uri_segments, -1));

		// Get categories from this page articles
		$categories = self::$ci->category_model->get_categories_from_pages($page['id_page'], Settings::get_lang());
		
		// Flip the URI config array to have the category index first
		$uri_config = self::$ci->config->item('special_uri');
		$uri_config = array_flip($uri_config);
		
		// Add the URL to the category to each category row
		// Also add the active class		
		foreach($categories as $key => $category)
		{
			$categories[$key]['url'] = 			base_url() . $page['url'] . '/' . $uri_config['category'] . '/' . $category['name'];
			$categories[$key]['lang_url'] = 	base_url() . Settings::get_lang() . '/' . $page['url'] . '/' . $uri_config['category'] . '/' . $category['name'];
			$categories[$key]['active_class'] = ($category['name'] == $category_uri) ? $active_class : '';
		}
	
		// Reorder array keys
		return array_values($categories);
	}
	

	// ------------------------------------------------------------------------


	function set_parent_scope($tag)
	{
		$where = array();
		$in_pages = array();
	
		$id_parent = $tag->locals->page['id_parent'];
		
		/**
		 * NOT DONE FOR THE MOMENT
		 * IDEA :
		 * Use the parent tag to define a parent scope : 
		 * Means not only the article from the current page parent can be displayed, but also the 
		 * articles from parent / parent
		 *
		$scope_level = ( ! empty($tag->attr['scope_level'])) ? $tag->attr['scope_level'] : FALSE;
		
		// Scope level can be equal to 0 : first level
		if ($scope_level !== FALSE)
		{
		}
		 */
		
		// Get all pages ID where the parent is the current parent ID
		// Sister pages from current parent page
		$parents = self::$ci->page_model->get_list(array('id_parent' => $id_parent));

		// Page from locals
		$pages =&  $tag->locals->pages;

		foreach($parents as $page)
			$in_pages[] = $page['id_page'];
		
		// If not empty, filter articles on id_page
		if ( ! empty($in_pages))
			$where['id_page in'] = '('.implode(',', $in_pages).')';
			
		return $where;
	}


	// ------------------------------------------------------------------------
	

	function set_global_scope($tag)
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


	// ------------------------------------------------------------------------
	

	public static function tag_last_item($tag)
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
	

	public static function tag_first_item($tag)
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
	 * Returns the count of an item collection
	 *
	 * @tag_attributes		'from' : 	collection name
	 *						'item' : 	items to count inside the collection
	 *						'filter' : 	Filter the items
	 * 
	 * @param				FTL_Binding Object		Tag
	 *
	 * @returns 			Int	Number of items
	 *
	 */
	public static function tag_count($tag)
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


	// ------------------------------------------------------------------------


	/**
	 * Count the articles depending on the URI segments
	 * Detect if a special URI is used.
	 *
	 * @return	int		The number of count articles
	 *
	 */
	function count_articles($tag, $filter)
	{
		if ( ! isset($tag->locals->page['nb_articles']))
		{
			$nb = 0;
		
			// Check if articles comes from a special URI result
			$special_uri = isset(self::$uri_segments[1]) ? self::$uri_segments[1] : FALSE;
			$uri_config = self::$ci->config->item('special_uri');
	
			// Special URI
			// For example, to count articles from one archive
			if ($special_uri !== FALSE && array_key_exists($special_uri, $uri_config) && $uri_config[$special_uri] != 'pagination' )
			{
				// If special URI count method exists, use it !
				// That mean that foreach special URI, you need to define a method to count the articles
				// depending of this special URI.
				if (method_exists(__CLASS__, 'count_articles_from_'.$uri_config[$special_uri]))
					$nb = call_user_func(array(__CLASS__, 'count_articles_from_'.$uri_config[$special_uri]), $tag, $filter);
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
						$where = self::set_parent_scope($tag);
			
					if ($scope == 'global')
						$where = self::set_global_scope($tag);
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


	// ------------------------------------------------------------------------


	/**
	 * Count the articles from a given category
	 * Called by count_articles
	 *
	 * @return	int		The number of count articles
	 *
	 */
	function count_articles_from_category($tag, $filter)
	{
		$nb = 0;
		
		$category = isset(self::$uri_segments[2]) ? self::$uri_segments[2] : NULL;
		
		if ( ! is_null($category))
		{
			$nb = self::$ci->article_model->count_articles_from_category
			(
				array('id_page'=>$tag->locals->page['id_page']),
				$category,
				Settings::get_lang('current'),
				$filter
			);
		}
		return $nb;
	}


	// ------------------------------------------------------------------------


	/**
	 * Count the articles from archive
	 * Called by count_articles
	 *
	 * @return	int		The number of count articles
	 *
	 */
	function count_articles_from_archives($tag, $filter)
	{
		$nb = 0;
		
		$year = 	isset(self::$uri_segments[2]) ? self::$uri_segments[2] : NULL;
		$month = 	isset(self::$uri_segments[3]) ? self::$uri_segments[3] : NULL;
		
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


	// ------------------------------------------------------------------------


	/**
	 * Returns the pagination addon URI for categories pagination
	 *
	 * @return	String		The pagination addon URI
	 *
	 */
	function get_pagination_uri_addon_from_category()
	{
		$category_uri = 	self::$uri_segments[1];
		$category_name = 	self::$uri_segments[2];

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
		$archive_uri = self::$uri_segments[1];
	
		$year = isset(self::$uri_segments[2]) ? self::$uri_segments[2] : NULL;
		$month = isset(self::$uri_segments[3]) ? self::$uri_segments[3] : NULL;
		
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
	public function tag_absolute_url($tag)
	{
		return $tag->locals->page['absolute_url'];
	}


	// ------------------------------------------------------------------------


	/**
	 * Languages tag
	 * 
	 * @param	FTL_Binding		The binded tag to parse
	 *
	 */
	public static function tag_languages($tag)
	{
		$languages = Settings::get_online_languages();
		
		// Current active language class
		$active_class = (isset($tag->attr['active_class']) ) ? $tag->attr['active_class'] : 'active';

		// helper
		$helper = (isset($tag->attr['helper']) ) ? $tag->attr['helper'] : 'navigation';

		// No helper ?
		$no_helper = (isset($tag->attr['no_helper']) ) ? TRUE : FALSE;

		$str = '';

		foreach($languages as &$lang)
		{
			$tag->locals->lang = $lang;
			$tag->locals->url = $lang['url'] = $tag->locals->page['absolute_urls'][$lang['lang']];
			
			$tag->locals->active = $lang['active_class'] = ($lang['lang'] == Settings::get_lang('current') ) ? $active_class : '';

			if (Connect()->is('editors', TRUE) OR $lang['online'] == 1)
			{
				$str .= $tag->expand();
			}
		}

		// Get helper method
		$helper_function = (substr(strrchr($helper, ':'), 1 )) ? substr(strrchr($helper, ':'), 1 ) : 'get_language_navigation';
		$helper = (strpos($helper, ':') !== FALSE) ? substr($helper, 0, strpos($helper, ':')) : $helper;

		// load the helper
		self::$ci->load->helper($helper);

		// Return the helper function result
		if (function_exists($helper_function) && $no_helper === FALSE)
		{
			$nav = call_user_func($helper_function, $languages);
			
			return self::wrap($tag, $nav);
		}
		else
		{
			return self::wrap($tag, $str);
		}
	}


	// ------------------------------------------------------------------------
	

	/**
	 * Languages nested tags
	 * 
	 * @param	FTL_Binding		The binded tag to parse
	 *
	 */
	public static function tag_languages_language($tag) { return $tag->locals->lang['name']; }
	public static function tag_languages_name($tag) { return $tag->locals->lang['name']; }
	public static function tag_languages_code($tag) { return $tag->locals->lang['lang']; }
	public static function tag_languages_url($tag) { return $tag->locals->url; }
	public static function tag_languages_active_class($tag) { return $tag->locals->active; }


	// ------------------------------------------------------------------------


	/**
	 * Pagination tag
	 * 
	 * @todo : More options should be implemented !!!!
	 *
	 * Main class name, id, open tag, close tag, every options from cI in fact ! 
	 *
	 */
	public static function tag_pagination($tag)
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
	public static function tag_page_id($tag) { return self::wrap($tag, $tag->locals->page['id_page']); }
	public static function tag_page_name($tag) { return self::wrap($tag, $tag->locals->page['name']); }
    public static function tag_page_url($tag)	{ return self::wrap($tag, $tag->locals->page['url']); }
	public static function tag_page_subtitle($tag) { return self::wrap($tag, $tag->locals->page['subtitle']); }
	public static function tag_page_date($tag) { return self::format_date($tag, $tag->locals->page['date']); }


	// ------------------------------------------------------------------------
	

	/**
     * Return the page title
     *
     * @usage : <ion:title [tag="h2" from="parent" up="2" ] />
     *
     *            The "from" attribute works with th "up" attribute. up="2" means the parent from the parent, etc.
     *            If the "up" attribute isn't set when using "parent", it will be set to 1.Means the returned title will be the one of the parent page of th current page.
     *            
     * @returns     String        The page title, wrapped or not by the optional defined tags.
     *
     */
    public static function tag_page_title($tag)    
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
	
	
	public static function tag_page_meta_title($tag)
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
		
		if ( $meta_title == '' && ! empty($tag->locals->page['meta_title']) )
		{
			$meta_title = $tag->locals->page['meta_title'];
		}
		if ( $meta_title == '' && ! empty($tag->locals->page['title']) )
		{
			$meta_title = $tag->locals->page['title'];		
		}
		
		// Remove HTML tags from meta title
		$meta_title = strip_tags($meta_title);
		
		// Tag cache
		self::set_cache($tag, self::wrap($tag, $meta_title));
		
		return $meta_title;
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
	public static function tag_page_medias($tag)
	{
		$medias = ( ! empty($tag->locals->page['medias'])) ? $tag->locals->page['medias'] : FALSE;
		
		if ( $medias !== FALSE)
		{
//			return self::wrap($tag, self::get_medias($tag, $medias));
			return self::wrap($tag, TagManager_Media::get_medias($tag, $medias));
		}
		return ;
	}


	// ------------------------------------------------------------------------
	
	
	public static function tag_page_content($tag)
	{
		$content = ( ! empty($tag->locals->page['content'])) ? $tag->locals->page['content'] : '';

		return $content;
	}
	
	
	// ------------------------------------------------------------------------
	
	
	
	/*
	 * Articles / Article tags
	 * 
	 * 
	 */


	/**
	 * Returns the articles tag content
	 * 
	 * @param	FTL_Binding object 
	 * @return 
	 */
	public static function tag_articles($tag)
	{
		$cache = (isset($tag->attr['cache']) && $tag->attr['cache'] == 'off' ) ? FALSE : TRUE;

		// Tag cache
		if ($cache == TRUE && ($str = self::get_cache($tag)) !== FALSE)
			return $str;

		// Returned string
		$str = '';

		// Page from locals
		$pages =&  $tag->locals->pages;

		// paragraph limit ?
		$paragraph = (isset($tag->attr['paragraph'] )) ? self::get_attribute($tag, 'paragraph') : FALSE ;

		// auto_link
		$auto_link = (isset($tag->attr['auto_link']) && strtolower($tag->attr['auto_link'] == 'false') ) ? FALSE : TRUE ;

		// view
		$view = (isset($tag->attr['view']) ) ? $tag->attr['view'] : FALSE;

		// Last part of the URI
		$uri_last_part = array_pop(explode('/', uri_string()));
		
		/* Get the articles
		 *
		 */
		$articles = self::get_articles($tag);
		
		// Number of articles
		$count = count($articles);
		
		// Add data like URL to each article
		// and finally render each article
		if ( ! empty($articles))
		{
			// Articles index starts at 1.
			$index = 1;
		
			foreach($articles as $key => $article)
			{
				// Force the view if the "view" attribute is defined
				if ($view !== FALSE)
				{	
					$articles[$key]['view'] = $view;
				}
	
				$articles[$key]['active_class'] = '';
// Correct this				
				if (!empty($tag->attr['active_class']))
				{
					if ($uri_last_part == $article['url'])
					{
						$articles[$key]['active_class'] = $tag->attr['active_class'];
					}
				}

				// Limit to x paragraph if the attribute is set
				if ($paragraph !== FALSE)
					$articles[$key]['content'] = tag_limiter($article['content'], 'p', $paragraph);

				// Autolink the content
				if ($auto_link)
					$articles[$key]['content'] = auto_link($articles[$key]['content'], 'both', TRUE);
				
				
				// Article's index
				$articles[$key]['index'] = $index++;
				
				// Article's count
				$articles[$key]['count'] = $count;
			}

			// Set the articles
			$tag->locals->page['articles'] = $articles;
	
			$count = count($tag->locals->page['articles']);
			
			foreach($tag->locals->page['articles'] as $key=>$article)
			{
				// Render the article
				$tag->locals->article = $article;
				$tag->locals->index = $key;
				$tag->locals->count = $count;
				$str .= $tag->expand();
			}
		}

// Experimental : To allow tags in articles
// Needs to be improved 		
//		$str = $tag->parse_as_nested($str);
		
		$output = self::wrap($tag, $str);
		
		// Tag cache
		self::set_cache($tag, $output);
		
		return $output;
	}

	
	// ------------------------------------------------------------------------


	/**
	 * Returns the article tag content
	 * To be used inside an "articles" tag
	 * 
	 * @param	FTL_Binding object
	 * @return 
	 */
	public static function tag_articles_article($tag)
	{
		// View : Overwrite each defined article view by the passed one
		// It is possible to bypass the Article view by set it to ''
		$view = (isset($tag->attr['view'] )) ? $tag->attr['view'] : FALSE ;
		
		// Kind of article : Get only the article linked to the given view
		$type = (isset($tag->attr['type'] )) ? $tag->attr['type'] : FALSE ;
		
		// paragraph limit ?
		$paragraph = (isset($tag->attr['paragraph'] )) ? $tag->attr['paragraph'] : FALSE ;

		if ( ! empty($tag->locals->article))
		{
			// Current article (set by tag_articles() )
			$article = &$tag->locals->article;

			/*
			 * Article View
			 * If no view : First, try to get the pages defined article_list view
			 *				Second, get the pages defined article view
			 *				Else, get the default view
			 */
			if ($view === FALSE)
			{
				// The article defined view
				$view = $article['view'];

				// If article has no defined view : view to 0, nothing or FALSE
				if ( $view == FALSE OR $view == '')
				{				
					// First and second step : The page defined views for articles
					// Need to be discussed...
					$view = $tag->globals->page['article_view'] ? $tag->globals->page['article_view'] : $tag->globals->page['article_list_view'];
				}
			}
			
			// Paragraph limiter
			if ($paragraph !== FALSE)
			{
				$article['content'] = tag_limiter($article['content'], 'p', $paragraph);
			}

			// View rendering
			if (empty($view))
			{
				$view = Theme::get_default_view('article');
				
				// Returns the default view ('article') if found in the theme folder
				if (file_exists(Theme::get_theme_path().'views/'.$view.EXT))
				{
					return $tag->parse_as_nested(file_get_contents(Theme::get_theme_path().'views/'.$view.EXT));
				}
				return $tag->parse_as_nested(file_get_contents(APPPATH.'views/'.$view.EXT));
			}
			else
			{
				if ( ! file_exists(Theme::get_theme_path().'views/'.$view.EXT))
				{
					return self::show_tag_error($tag->name, '<b>Cannot find view file "'.Theme::get_theme_path().'views/'.$view.EXT.'".');
				}
				return $tag->parse_as_nested(file_get_contents(Theme::get_theme_path().'views/'.$view.EXT));
			}
		}
		return self::show_tag_error($tag->name, '<b>This article doesn\'t exists</b>');
	}

	
	// ------------------------------------------------------------------------


	public static function tag_article_id($tag) { return self::wrap($tag, $tag->locals->article['id_article']); }
	public static function tag_article_name($tag) { return self::wrap($tag, $tag->locals->article['name']); }
	public static function tag_article_title($tag) { return self::wrap($tag, $tag->locals->article['title']); }
	public static function tag_article_subtitle($tag) { return self::wrap($tag, $tag->locals->article['subtitle']); }
	public static function tag_article_date($tag) { return self::wrap($tag, self::format_date($tag, $tag->locals->article['date'])); }
	public static function tag_article_meta_title($tag) { return self::wrap($tag, strip_tags($tag->locals->article['meta_title'])); }
	public static function tag_article_active_class($tag) { return self::wrap($tag, $tag->locals->article['active_class']); }
	

	// ------------------------------------------------------------------------
	

	/**
	 * Returns informations about the link
	 *
	 */
	public static function tag_article_link($tag)
	{
		// paragraph limit ?
		$attr = (isset($tag->attr['attr'] )) ? $tag->attr['attr'] : FALSE ;
		
		if ($attr == FALSE)
		{
			return $tag->locals->article['link'];
		}
		else
		{
		
		}
		
		
		// return self::wrap($tag, $tag->locals->article['link']);
	}

	// ------------------------------------------------------------------------


	/**
	 * Returns the article content
	 *
	 */
	public static function tag_article_content($tag)
	{
		// paragraph limit ?
		$paragraph = (isset($tag->attr['paragraph'] )) ? (Int)self::get_attribute($tag, 'paragraph') : FALSE ;

		$content = $tag->locals->article['content'];

		// Limit to x paragraph if the attribute is set
		if ($paragraph !== FALSE)
			$content = tag_limiter($content, 'p', $paragraph);

		return self::wrap($tag, $content);
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the URL of the article, based or not on the lang
	 * If only one language is online, this tag will return the URL without the lang code
	 * To returns the lag code if you have only one language, set the "lang" attribute to TRUE
	 * If the link or the article is set, this tag will return the link instead of the URL to the article.
	 *
	 */
	public static function tag_article_url($tag) 
	{
		$url = '';
		
		// If lang attribute is set to TRUE, force the lang code to be in the URL
		// Usefull only if the website has only one language
		$lang_url = (isset($tag->attr['lang']) && $tag->attr['lang'] == 'TRUE' ) ? TRUE : FALSE;

		// If link, return the link
		if ($tag->locals->article['link_type'] != '' )
		{
			// External link
			if ($tag->locals->article['link_type'] == 'external')
			{
				return $tag->locals->article['link'];
			}
			
			// Mail link : TODO
			if ($tag->locals->article['link_type'] == 'email')
			{
				return auto_link($tag->locals->article['link'], 'both', TRUE);
			}
			
			
			// If link to one article, get the page to build the complete link
			if($tag->locals->article['link_type'] == 'article')
			{
				// Get the article to which this article links
				$rel = explode('.', $tag->locals->article['link_id']);
				$target_article = self::$ci->article_model->get_context($rel[1], $rel[0], Settings::get_lang('current'));
				
				// If more than one parent, links to to first found
				// Normally, target link articles should not be duplicated in the tree
				// $parent_page = array_values(array_filter($tag->globals->pages, create_function('$row','return $row["id_page"] == "'. $target_article['id_page'] .'";')));
				// $url = ( ! empty($parent_page[0])) ? $parent_page[0]['url'] . '/' . $target_article['url'] : '';
				$url = '';
				
				if ( ! empty($target_article))
				{
					foreach($tag->globals->pages as $p)
					{
						if ($p['id_page'] == $target_article['id_page'])
						{
							$url = $p['url']. '/' . $target_article['url'];
						}
					}
					
					if ( count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1' )
						$url = Settings::get_lang('current').'/'.$url;
					
					return base_url().$url;
				}
			}
			// This is a link to a page
			else
			{
				// Get the page to which the article links
				// $page = array_values(array_filter($tag->globals->pages, create_function('$row','return $row["id_page"] == "'. $tag->locals->article['link_id'] .'";')));
				// if ( ! empty($page[0]))
				// {
				// 	$page = $page[0];
				// 	return $page['absolute_url'];
				// }
				foreach($tag->globals->pages as $p)
				{
					if ($p['id_page'] == $tag->locals->article['link_id'])
						return $p['absolute_url'];
				}
			}
		}

		// Only returns the URL containing the lang code when languages > 1 or atribute lang set to TRUE
		if (count(Settings::get_online_languages()) > 1 OR $lang_url === TRUE)
		{
			$url = $tag->locals->article['lang_url'];
		}
		else
		{
			$url = $tag->locals->article['url'];
		}

		// Adds the suffix if defined in /application/config.php
		if ( config_item('url_suffix') != '' ) $url .= config_item('url_suffix');
		
		return $url;
	}


	// ------------------------------------------------------------------------

	
	public static function tag_article_view($tag) { return $tag->locals->article['view']; }


	/**
	 * Article medias tag definition
	 * Medias in one article context
	 *
	public static function tag_article_medias($tag)
	{
		$medias = $tag->locals->article['medias'];
//		return self::wrap($tag, self::get_medias($tag, $medias));
		return self::wrap($tag, TagManager_Media::get_medias($tag, $medias));
	}
	 */


	public static function tag_article_author_name($tag)
	{
		// Get the users if they're not defined
		if (!isset($tag->globals->users))
		{
			self::$ci->base_model->set_table('users');
			$tag->globals->users = self::$ci->base_model->get_list();
		}
		
		foreach($tag->globals->users as $user)
		{
			if ($user['username'] == $tag->locals->article['author'])
				return self::wrap($tag, $user['screen_name']);
		}

		return '';
	}


	public static function tag_article_author_email($tag)
	{
		// Get the users if they're not defined
		if (!isset($tag->globals->users))
		{
			self::$ci->base_model->set_table('users');
			$tag->globals->users = self::$ci->base_model->get_list();
		}
		
		foreach($tag->globals->users as $user)
		{
			if ($user['username'] == $tag->locals->article['author'])
				return self::wrap($tag, $user['email']);
		}

		return '';
	}


	public function tag_article_readmore($tag)
	{
		$term = (isset($tag->attr['term']) ) ? $tag->attr['term'] : '';
		$paragraph = (isset($tag->attr['paragraph'] )) ? $tag->attr['paragraph'] : FALSE ;


		if ( ! empty($tag->locals->article))
		{
			// Current article (set by tag_articles() )
//			$article = &$tag->locals->article;
		
//			$content = 	tag_limiter($article['content'], 'p', $paragraph);
			
//			if (strlen($content) < strlen($article['content']))
//			{
				return self::wrap($tag, '<a href="'.self::tag_article_url($tag).'">'.lang($term).'</a>'); 
//			}
//			else
//			{
//				return '';
//			}
		}
		else
		{
			return '';
		}
	}
	
	

	public static function tag_article_index($tag)
	{
		return $tag->locals->article['index'];
	}
	
	public static function tag_article_count($tag)
	{
		// Redirect to the global count tag if items is set as attribute. Means we want to count something else.
		if (isset($tag->attr['items']))
		{
			return self::tag_count($tag);
		}

		return $tag->locals->article['count'];
	}
	
	
	// ------------------------------------------------------------------------
	
	public static function tag_prev_article($tag)
	{
		$article = self::get_adjacent_article($tag, 'prev');
	
		return self::process_next_prev_article($tag, $article);
	}


	public static function tag_next_article($tag)
	{
		$article = self::get_adjacent_article($tag, 'next');
	
		return self::process_next_prev_article($tag, $article);
	}

	
	private static function get_adjacent_article($tag, $mode='prev')
	{
		$page = $tag->locals->page;
		
		$tag->attr['from'] = $page['name'];
		
		$articles = self::get_articles($tag);
		
		$uri = self::$uri_segments;
		$uri = array_pop($uri);
		
		$wished_article = array();
		
		$enum = ($mode=='prev') ? -1 : 1;
		
		foreach($articles as $key => $article)
		{
			if ($article['name'] == $uri)
			{
				if ( ! empty($articles[$key + $enum]))
				{
					$wished_article = $articles[$key + $enum];
					break;
				}
			}
		}
		
		return $wished_article;
	}
	

	/**
	 * Processes the next / previous article tags result
	 * Internal use only.
	 *	 
	 */
	private static function process_next_prev_article($tag, $article)
	{
		if ($article != FALSE)
		{
			// helper
			$helper = (isset($tag->attr['helper']) ) ? $tag->attr['helper'] : 'navigation';

			// Get helper method
			$helper_function = (substr(strrchr($helper, ':'), 1 )) ? substr(strrchr($helper, ':'), 1 ) : 'get_next_prev_article';
			$helper = (strpos($helper, ':') !== FALSE) ? substr($helper, 0, strpos($helper, ':')) : $helper;
	
			// Prefix ?
			$prefix = (!empty($tag->attr['prefix']) ) ? $tag->attr['prefix'] : '';
	
			// load the helper
			self::$ci->load->helper($helper);
			
			// Return the helper function result
			if (function_exists($helper_function))
			{
				$return = call_user_func($helper_function, $article, $prefix);
				
				return self::wrap($tag, $return);
			}
		}
		
		return '';
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
	public static function tag_next_page($tag)
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
	public static function tag_prev_page($tag)
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
	
	
	public static function tag_archive($tag)
	{
		// Current archive
		$year = isset(self::$uri_segments[2]) ? self::$uri_segments[2] : '' ;
		$month = isset(self::$uri_segments[3]) ? self::$uri_segments[3] : '' ;
		
		$uri_config = self::$ci->config->item('special_uri');
		$uri_config = array_flip($uri_config);
		$archive_uri = $uri_config['archives'];
		
		if (self::$uri_segments[1] == $archive_uri)
		{
			$timestamp = '';
			if ($year != '' && $month !='')
				$timestamp = mktime(0, 0, 0, $month, 1, $year);
			else if ($year != '')
				$timestamp = mktime(0, 0, 0, 0, 1, $year);
			
			if ($timestamp != '')
			{
				$date = (string) date('Y-m-d H:i:s', $timestamp);
	
				return self::format_date($tag, $date);
			}
		}
				
		return '';
	}


	/**
	 * Get the archives tag
	 *
	 *
	 */
	public static function tag_archives($tag)
	{
		// Tag cache
		if (($str = self::get_cache($tag)) !== FALSE)
			return $str;

		// Period format
		$format = (isset($tag->attr['format']) ) ? $tag->attr['format'] : 'F';

		// Attribute : active class
		$active_class = (isset($tag->attr['active_class']) ) ? $tag->attr['active_class'] : 'active';

		// filter
		$filter = (isset($tag->attr['filter']) ) ? $tag->attr['filter'] : FALSE;

		// month
		$with_month = (isset($tag->attr['with_month']) ) ? TRUE : FALSE;

		// order
		$order = (isset($tag->attr['order']) && $tag->attr['order'] == 'ASC' ) ? 'period ASC' : 'period DESC';

		// Current archive
		$current_archive = isset(self::$uri_segments[2]) ? self::$uri_segments[2] : '' ;
		$current_archive .= isset(self::$uri_segments[3]) ? self::$uri_segments[3] : '' ;

		// Get the archives infos		
		$archives = self::$ci->article_model->get_archives_list
		(
			array('id_page' => $tag->locals->page['id_page']), 
			Settings::get_lang(),
			$filter,
			$with_month,
			$order
		);


		// Translated period array
		$month_formats = array('D', 'l', 'F', 'M');

		// Flip the URI config array to have the category index first
		$uri_config = self::$ci->config->item('special_uri');
		$uri_config = array_flip($uri_config);

		foreach ($archives as &$row)
		{
			$year = 	substr($row['period'],0,4);
			$month = 	substr($row['period'],4);
			
			if ($month != '')
			{
				$month = (strlen($month) == 1) ? '0'.$month : $month;

				$timestamp = mktime(0, 0, 0, $month, 1, $year);
    
				// Get date in the wished format
				$period = (String) date($format, $timestamp);

				if (in_array($format, $month_formats))
					$period = lang(strtolower($period));

				$row['period'] = $period . ' ' . $year;
				$row['url'] = base_url() . $tag->locals->page['name'] . '/' . $uri_config['archives'] . '/' . $year . '/' . $month ;
				$row['lang_url'] = base_url() . Settings::get_lang() . '/' . $tag->locals->page['name'] . '/' .  $uri_config['archives'] . '/' . $year . '/' . $month ;
				$row['active_class'] = ($year.$month == $current_archive) ? $active_class : '';
			}
			else
			{
				$row['period'] = $year;
				$row['url'] = base_url() . $tag->locals->page['name'] . '/' . $uri_config['archives'] . '/' . $year;
				$row['lang_url'] = base_url() . Settings::get_lang() . '/' . $tag->locals->page['name'] . '/' .  $uri_config['archives'] . '/' . $year;
				$row['active_class'] = ($year == $current_archive) ? $active_class : '';
			}
		}


		// Tag expand
		$str = '';

		foreach($archives as $archive)
		{
			$tag->locals->archive = $archive;
			$str .= $tag->expand();
			
		}

		// Tag cache
		self::set_cache($tag, $str);
		
		return $str;
	}


	// ------------------------------------------------------------------------


	/**
	 * Archives tags callback functions
	 *
	 */
	public static function tag_archives_url($tag) 
	{ 
		// with lang code in the URL ?
		$lang = (isset($tag->attr['lang']) && $tag->attr['lang'] == 'TRUE') ? TRUE : FALSE ;

		if (isset($tag->attr['lang']) && $tag->attr['lang'] == 'TRUE')
		{
			// Only returns the URL containing the lang code when languages > 1
			if (count(Settings::get_online_languages()) > 1)
			{
				return $tag->locals->archive['lang_url'];
			}
		}
		return $tag->locals->archive['url']; 
	}


	/** 
	 * Deprecated, will be deleted in the next version 
	 * Use tag_archives_url
	 * @deprecated
	 */
	public static function tag_archives_lang_url($tag) { return ($tag->locals->archive['lang_url'] != '' ) ? $tag->locals->archive['lang_url'] : '' ; }
	
	
	public static function tag_archives_period($tag) { return ($tag->locals->archive['period'] != '' ) ? $tag->locals->archive['period'] : '' ; }
	public static function tag_archives_nb($tag) { return ($tag->locals->archive['nb'] != '' ) ? $tag->locals->archive['nb'] : '' ; }
	public static function tag_archives_active_class($tag) { return ($tag->locals->archive['active_class'] != '' ) ? $tag->locals->archive['active_class'] : '' ; }
	

	// ------------------------------------------------------------------------

	
	/**
	 * Displays the breacrumb : You are here !!!
	 * 
	 * @param	FTL_Binding object 
	 * @return	String	The parsed view
	 * 
	 */
	public static function tag_breadcrumb($tag)
	{
		// Anchor enclosing tag 
		$subtag_open = (isset($tag->attr['subtag'])) ? '<' . $tag->attr['subtag'] . '>' : '';
		$subtag_close = (isset($tag->attr['subtag'])) ? '</' . $tag->attr['subtag'] . '>' : '';
		
		$separator = (isset($tag->attr['separator']) ) ? htmlentities($tag->attr['separator']) : ' &raquo; ';

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
	
	
	/**
	 * Renders the <ion:articles /> last_article sub tag
	 *
	 */
	public static function tag_last_articles_article($tag)
	{
		return self::tag_articles_article($tag);
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Return the current category
	 * 
	 * @param	FTLBinding		Current tag
	 * @param	String			Wished returned value ('name', 'title', etc.)
	 *
	 */
	public static function tag_category($tag)
	{
		$field = ( ! empty($tag->attr['field'])) ? $tag->attr['field'] : NULL;

		$uri_segments = self::$uri_segments;
		$category_uri = array_pop(array_slice($uri_segments, -1));

		// Categorie prefix in the returned string. Exemple "Category "
		$category_value = NULL;

		// Store categories in Globals, so no multiple time retrieve
		if (self::$categories === FALSE)
		{
			self::$categories = self::get_categories($tag, self::get_asked_page($tag));
		}

		foreach(self::$categories as $category)
		{
			if ($category['name'] == $category_uri)
			{
				$category_value = ( ! empty($category[$field])) ? $category[$field] : $category_uri;
			}
		}
		if ( ! is_null($category_value))
		{
			return self::wrap($tag, $category_value);
		}
	}
	
	
	// ------------------------------------------------------------------------
	
	
// HERE : Add pagination : Number of page displayed on category view !!!!
// Could not be possible as the pagination tag don't know this Category tag attribute value (per_page)


	/**
	 * Categories tag
	 * Get the categories list from within the current page or globally
	 *
	 *
	 */
	public static function tag_categories($tag)
	{

		// Tag cache
		if (($str = self::get_cache($tag)) !== FALSE)
			return $str;
		
		// Store of all categories
		if (self::$categories === FALSE)
		{
			self::$categories = self::get_categories($tag, self::get_asked_page($tag));
		}

		// Tag expand
		$str = '';
		foreach(self::$categories as $category)
		{
			$tag->locals->category = $category;
			$str .= $tag->expand();
		}

		$output = self::wrap($tag, $str);
		
		// Tag cache
		self::set_cache($tag, $output);
		
		return $output;
	}

	
	// ------------------------------------------------------------------------


	/**
	 * Categories tags callback functions
	 *
	 */
	public static function tag_category_url($tag) 
	{ 
		// don't display the lang URL (by default)
		$lang_url = FALSE;

		// If lang attribute is set to TRUE, force the lang code to be in the URL
		// Usefull only if the website has only one language
		if (isset($tag->attr['lang']) && $tag->attr['lang'] == 'TRUE' )
		{
			$lang_url = TRUE;
		}

		// Only returns the URL containing the lang code when languages > 1
		// or atribute lang set to TRUE
		if (count(Settings::get_online_languages()) > 1 OR $lang_url === TRUE)
		{
			return $tag->locals->category['lang_url'];
		}
		
		return $tag->locals->category['url'];
	}



	public static function tag_category_active_class($tag) { return ($tag->locals->category['active_class'] != '' ) ? $tag->locals->category['active_class'] : '' ; }

    public static function tag_category_title($tag) { return self::wrap($tag, $tag->locals->category['title']); }

	public static function tag_category_subtitle($tag) { return self::wrap($tag, $tag->locals->category['subtitle']); }



	/**
	 * Returns HTML categories links wrapped by the given tag
	 *
	 * @TODO : 	Add the open and closing tag for each anchor.
	 *			Example : <li><a>... here is the anchor ... </a></li>
	 *
	 */
	public static function tag_article_categories($tag)
	{
		$data = array();
		
		// HTML Separatorof each category
		$separator = ( ! empty($tag->attr['separator'])) ? $tag->attr['separator'] : ' | ';	
		
		// Make a link from each category or not. Default : TRUE
		$link = ( ! empty($tag->attr['link']) && $tag->attr['link'] == 'false') ? FALSE : TRUE;	

		// Field to return for each category. "title" by default, but can be "name", "subtitle'
		$field =  ( ! empty($tag->attr['field'])) ? $tag->attr['field'] : 'title';

		// don't display the lang URL (by default)
		$lang_url = '';

		// Global tag and class, for memory
		$html_tag =  ( ! empty($tag->attr['tag'])) ? $tag->attr['tag'] : FALSE;
		$class =  ( ! empty($tag->attr['class'])) ? $tag->attr['class'] : FALSE;
		
		// Tag and class for each category, if set.
		$subtag =  ( ! empty($tag->attr['subtag'])) ? $tag->attr['subtag'] : FALSE;
		$subclass =  ( ! empty($tag->attr['subclass'])) ? ' class="'.$tag->attr['subclass'].'"' : FALSE;


		// If lang attribute is set to TRUE, force the lang code to be in the URL
		// Usefull only if the website has only one language
		if (isset($tag->attr['lang']) && $tag->attr['lang'] == 'TRUE' )
		{
			$lang_url = TRUE;
		}

		// Only returns the URL containing the lang code when languages > 1
		// or atribute lang set to TRUE
		if (count(Settings::get_online_languages()) > 1 OR $lang_url === TRUE)
		{
			$lang_url = Settings::get_lang().'/';
		}
		
		// Current page
		$page = $tag->locals->page;
	
			
		// Get the category URI segment from /config/ionize.php config file
		$uri_config = self::$ci->config->item('special_uri');
		$uri_config = array_flip($uri_config);

		$category_uri = $uri_config['category'];

		// Get the categories from current article
		$categories = $tag->locals->article['categories'];	

		// Build the anchor array
		foreach($categories as $category)
		{
			$category_string = '';
			
			
			if ($subtag !== FALSE)
			{
				// Set the local category, to get the class from current category
				$tag->locals->category = $category;
				$subclass = self::get_attribute($tag, 'subclass');
				$subtag = self::get_attribute($tag, 'subtag');
				
				// Replace the class and tag by the subclass and subtag
				$tag->attr['class'] = $subclass;
				$tag->attr['tag'] = $subtag;
	
				$category_string = self::wrap($tag, $category[$field]);
			}
			else
			{
				$category_string = $category[$field];
			}
			
			$url = anchor(base_url().$lang_url.$page['name'].'/'.$category_uri.'/'.$category['name'], $category_string);

			if ($link == TRUE)
				$category_string = $url;
			
			$data[] = $category_string;
			
// To make nested tags working...
//			$category['url'] = $url;
//			$tag->locals->category = $category;
//			$tag->expand();
			
		}

		$tag->attr['tag'] = $html_tag;
		$tag->attr['class'] = $class;
		
		return self::wrap($tag, implode($separator, $data));
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