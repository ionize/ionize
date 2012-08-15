<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.8
 *
 */


/**
 * Article TagManager 
 *
 */
class TagManager_Article extends TagManager
{
	public static $tag_definitions = array
	(
		'article' => 				'tag_article',
		'article:id_article' => 	'tag_article_id',
		'article:active_class' => 	'tag_article_active_class',
		'article:view' => 			'tag_article_view',
		'article:author' => 		'tag_article_author_name',
		'article:author_email' => 	'tag_article_author_email',
		'article:name' => 			'tag_article_name',
		'article:title' => 		    'tag_article_title',
		'article:subtitle' => 		'tag_article_subtitle',
		'article:summary' => 		'tag_article_summary',
		'article:meta_title' =>     'tag_article_meta_title',
		'article:date' => 			'tag_article_date',
		'article:content' => 		'tag_article_content',
		'article:url' => 			'tag_article_url',
		'article:link' => 			'tag_article_link',
		'article:categories' => 	'tag_article_categories',
		'article:readmore' => 		'tag_article_readmore',

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
		'articles:summary' => 		'tag_article_summary',
		'articles:meta_title' =>    'tag_article_meta_title',
		'articles:date' => 			'tag_article_date',
		'articles:content' => 		'tag_article_content',
		'articles:url' => 			'tag_article_url',
		'articles:link' => 			'tag_article_link',
		'articles:categories' => 	'tag_article_categories',
		'articles:readmore' => 		'tag_article_readmore',
		'articles:index' => 		'tag_article_index',
		'articles:count' => 		'tag_article_count',
	);

	// ------------------------------------------------------------------------
	
	
	public static function init()
	{
		self::$uri_segments = explode('/', self::$ci->uri->uri_string());	
/*
		$uri = preg_replace("|/*(.+?)/*$|", "\\1", self::$ci->uri->uri_string);
		self::$uri_segments = explode('/', $uri);
*/
	}

	
	/**
	 * Get Articles
	 *
	 * @param	FTL_Binding
	 * @return	Array
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
		$special_uri = TagManager_Page::get_special_uri();

		// Use Pagination
		// The "articles" tag must explicitely indicates it want to use pagination. 
		// This explicit declaration is done to avoid all articles tags on one page using the same pagination value.
		$use_pagination = (isset($tag->attr['pagination']) && $tag->attr['pagination'] == 'TRUE') ? TRUE : FALSE;

		// Don't use the "article_list_view" setting set through Ionize
		// Todo : Remove ?
		// $keep_view = (isset($tag->attr['keep_view'])) ? TRUE : FALSE;

		// Use this view for each article if more than one article
		// TODO : Remove ?
		// $list_view = (isset($tag->attr['list_view'])) ? $tag->attr['list_view'] : FALSE;

		$type = $tag->getAttribute('type');

		// Number of article limiter
		$num = (isset($tag->attr['limit'])) ? self::get_attribute($tag, 'limit') : 0 ;
		if ($num == 0)
			$num = (isset($tag->attr['num'])) ? self::get_attribute($tag, 'num') : 0 ;

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
		$scope = $tag->getAttribute('scope');

		// from page name ?
		$from_page = $tag->getAttribute('from') ;

		// from categories ? 
		$from_categories = $tag->getAttribute('from_categories');
		$from_categories_condition = ($tag->getAttribute('from_categories_condition') != NULL && $tag->attr['from_categories_condition'] != 'or') ? 'and' : 'or';

		/*
		 * Preparing WHERE on articles
		 * From where do we get the article : from a page, from the parent page or from the all website ?
		 *
		 */
		// Order. Default order : ordering ASC
		$order_by = ($tag->getAttribute('order_by') != NULL) ? $tag->attr['order_by'] : 'ordering ASC';
		$where = array('order_by' => $order_by);

		// Add type to the where array
		if ($type !== NULL)
		{
			if ($type == '')
				$where['article_type.type'] = 'NULL';
			else
				$where['article_type.type'] = $type;
		}

		// If a page name is set, returns only articles from this page
		if ($from_page !== NULL)
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
				$where['where_in'] = array('id_page' => $in_pages);
			}
			// else return nothing. Seems the asked page doesn't exists...
			else
			{
				return $articles;
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
		else if ($scope == 'this')
		{
			$where += array('id_page' => $tag->locals->page['id_page']);
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
		if ( ! is_null($special_uri) && $from_page === FALSE && $type === FALSE)
		{
			if (method_exists(__CLASS__, 'get_articles_from_'.$special_uri))
			{
				$articles = call_user_func(array(__CLASS__, 'get_articles_from_'.$special_uri), $tag, $where, $filter);
			}
		}
		// Get all the page articles
		// If Pagination is active, set the limit. This articles result is the first page of pagination
		else 
		{
			// Set Limit
			$limit = ( ! empty($tag->locals->page['pagination']) && ($tag->locals->page['pagination'] > 0) ) ? $tag->locals->page['pagination'] : FALSE;

			if ($limit == FALSE && $num > 0) $limit = $num;

			if ($limit !== FALSE) $where['limit'] = $limit;

			if ($from_categories !== NULL)
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
		
		$nb_articles = count($articles);
		// Correct the articles URLs
//		if ($nb_articles > 0)
//		{
			self::init_articles_urls($articles);
//		}
		
		// Here, we are in an article list configuration : More than one article, page display
		// If the article-list view exists, we will force the article to adopt this view.
		// Not so much clean to do that in the get_article function but for the moment just helpful...
		foreach ($articles as $k=>$article)
		{
			if (empty($article['view']))
			{
				if ($nb_articles > 1 && ! empty($article['article_list_view']))
				{
					$articles[$k]['view'] = $article['article_list_view'];
				}
				else if (! empty($article['article_view']))
				{
					$articles[$k]['view'] = $article['article_view'];
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
		
		// URI of the category segment
		$cat_segment_pos = TagManager_Page::get_special_uri_segment();
		
		$cat_code =  (! empty(self::$uri_segments[$cat_segment_pos + 1]) ) ? 
						self::$uri_segments[$cat_segment_pos + 1] : 
						FALSE;
		if ($cat_code)
		{
			// Limit
			$where['offset'] = $start_index;
			if ((int)$page['pagination'] > 0) $where['limit'] =  (int)$page['pagination'];

			// Get the articles
			$articles = self::$ci->article_model->get_from_category
			(
				$where, 
				$cat_code, 
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
		$articles = array();
		
		$page = & $tag->locals->page;

		$start_index = 0;

		// Get the start index for the SQL limit query param : last part of the URL only if the 4th URI segmenet (pagination) is set
// TODO
// TODO
		if (isset(self::$uri_segments[4]))
		{
			$uri_segments = self::$uri_segments;
			$start_index = array_pop(array_slice($uri_segments, -1));
		}
// /TODO

		$arc_segment_pos = TagManager_Page::get_special_uri_segment();

		// Year : one index after the seg. pos
		$year =  (! empty(self::$uri_segments[$arc_segment_pos + 1]) ) ? 
					self::$uri_segments[$arc_segment_pos + 1] : 
					FALSE;
		
		// Month : 2 index after the seg. pos. NULL because of SQL query
		$month =  (! empty(self::$uri_segments[$arc_segment_pos + 2]) ) ? 
					self::$uri_segments[$arc_segment_pos + 2] : 
					NULL;
		
		if ($year)
		{
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
		}
		
		return $articles;
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
	 * Get one article from the URL
	 *
	 * @param 	array 	row from URL table
	 *
	 * @return array
	 */
	public static function get_article_from_url($article_url = array())
	{
		$article = array();

		if ( empty($article_url))
			$article_url = self::$ci->url_model->get_by_url(self::$ci->uri->uri_string());

		if ($article_url['type'] == 'article')
			$article = self::$ci->article_model->get_by_id($article_url['id_entity'], Settings::get_lang('current'));

		return $article;
	}

	// ------------------------------------------------------------------------

	/**
	 * Inits articles URLs
	 * Get the contexts of all given articles and define each article correct URL
	 *
	 */
	private function init_articles_urls(&$articles)
	{
		// Page URL index to use
		$page_url = (config_item('url_mode') == 'short') ? 'url' : 'path';

		// Array of all articles IDs
		$articles_id = array();
		foreach($articles as $article)
			$articles_id[] = $article['id_article'];
		
		// Articles contexts of all articles
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

			// Get the context of the Main Parent
			if ( ! empty($contexts))
			{
				foreach($contexts as $context)
				{
					if ($context['main_parent'] == '1')
						$page = $context;
				}
			}

			// Basic article URL : its lang URL (without "http://")
			$url = $article['url'];

			// Link ?
			if ($page['link_type'] != '' )
			{
				// External
				if ($page['link_type'] == 'external')
				{
					$article['url'] = $page['link'];
				}

				// Email
				else if ($page['link_type'] == 'email')
				{
					$article['url'] = auto_link($page['link'], 'both', TRUE);
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
							$parent_page = self::$ci->page_model->get_by_id($rel[0], Settings::get_lang('current'));

							if ( ! empty($parent_page))
								$article['url'] = $parent_page[$page_url] . '/' . $target_article['url'];
						}
					}
					// Page
					else
					{
						$target_page = self::$ci->page_model->get_by_id($page['link_id'], Settings::get_lang('current'));
						$article['url'] = $target_page[$page_url];
					}

					// Correct the URL : Lang + Base URL
					if ( count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1' )
					{
						$article['url'] =  Settings::get_lang('current'). '/' . $article['url'];
					}
					$article['url'] = base_url() . $article['url'];

				}
			}
			// Standard URL
			else
			{
				if ( count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1' )
				{
					$article['url'] = base_url() . Settings::get_lang('current') . '/' . $page[$page_url] . '/' . $url;
				}
				else
				{
					$article['url'] = base_url() . $page[$page_url] . '/' . $url;
				}
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the current article content
	 * Try first to get the current article (from URL)
	 * Then the article from locals.
	 *
	 * @param	FTL_Binding object
	 * @return	String
	 *
	 */
	public static function tag_article($tag)
	{
		$cache = (isset($tag->attr['cache']) && $tag->attr['cache'] == 'off' ) ? FALSE : TRUE;

		// Tag cache
		if ($cache == TRUE && ($str = self::get_cache($tag)) !== FALSE)
			return $str;

		// Returned string
		$str = '';

		$_article = TagManager_Page::get_article();

		if ( ! empty($_article))
		{
			$_articles = array($_article);
		}
		else
			$_articles = array($tag->locals->article);
			//$_articles = $tag->locals->page['articles'];

		// Add data like URL to each article
		// and finally render each article
		if ( ! empty($_articles))
		{
			$_articles = self::prepare_articles($tag, $_articles);

			$count = count($_articles);

			foreach($_articles as $key=>$article)
			{
				// Render the article
				$tag->locals->article = $article;
				$tag->locals->index = $key;
				$tag->locals->count = $count;

				// Parse the article's view if the article tag is single (<ion:article />)
				if($tag->is_single())
				{
					$str .= self::find_and_parse_article_view($tag, $article);
				}
				// Else expand the tag
				else
				{
					$str .= $tag->expand();
				}
			}
		}

		$output = self::wrap($tag, $str);

		// Tag cache
		self::set_cache($tag, $output);

		return $output;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the articles tag content
	 *
	 * @param	FTL_Binding object
	 * @return	String
	 *
	 */
	public static function tag_articles($tag)
	{
		$cache = (isset($tag->attr['cache']) && $tag->attr['cache'] == 'off' ) ? FALSE : TRUE;

		// Tag cache
		if ($cache == TRUE && ($str = self::get_cache($tag)) !== FALSE)
			return $str;

		// Returned string
		$str = '';

		/* Get the articles
		 *
		 */
		$articles = self::get_articles($tag);

		// Make articles in random order
		$random = (isset($tag->attr['random'])) ? (bool) $tag->attr['random'] : FALSE;
		if($random) shuffle ($articles);
		
		// Add data like URL to each article
		// and finally render each article
		if ( ! empty($articles))
		{
			self::prepare_articles($tag, $articles);

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
		// $str = $tag->parse_as_nested($str);
		
		$output = self::wrap($tag, $str);
		
		// Tag cache
		self::set_cache($tag, $output);
		
		return $output;
	}

	
	// ------------------------------------------------------------------------


	/**
	 * Returns the article tag content
	 * To be used inside an "articles" tag
	 * Only looks for locals->article
	 *
	 * @param	FTL_Binding object
	 * @return	String
	 */
	public static function tag_articles_article($tag)
	{
		if ( ! empty($tag->locals->article))
		{
			// Current article (set by tag_articles() )
			$article = &$tag->locals->article;

			return self::find_and_parse_article_view($tag, $article);
		}
		return self::show_tag_error($tag->name, '<b>This article doesn\'t exists</b>');
	}


	// ------------------------------------------------------------------------


	public static function tag_article_id($tag) { return self::wrap($tag, $tag->locals->article['id_article']); }

	public static function tag_article_name($tag)
	{
		if ( ! empty($tag->attr['from']))
		{
			$tag->attr['name'] = 'name';
			return self::tag_field($tag);
		}
		return self::wrap($tag, self::get_value('article', 'name', $tag));
	}
	
	public static function tag_article_title($tag)
	{
		if ( ! empty($tag->attr['from']))
		{
			$tag->attr['name'] = 'title';
			return self::tag_field($tag);
		}
		return self::wrap($tag, self::get_value('article', 'title', $tag));
	}
	
	public static function tag_article_subtitle($tag)
	{
		if ( ! empty($tag->attr['from']))
		{
			$tag->attr['name'] = 'subtitle';
			return self::tag_field($tag);
		}
		return self::wrap($tag, self::get_value('article', 'subtitle', $tag));
	}

	public static function tag_article_summary($tag)
	{
		if ( ! empty($tag->attr['from']))
		{
			$tag->attr['name'] = 'summary';
			return self::tag_field($tag);
		}
		return self::wrap($tag, self::get_value('article', 'summary', $tag));
	}

	
	public static function tag_article_date($tag)
	{ 
		if ( ! empty($tag->attr['from']))
		{
			$tag->attr['name'] = 'date';
			return self::tag_field($tag);
		}
		return self::wrap($tag, self::format_date($tag, $tag->locals->article['date']));
	}
	
	public static function tag_article_meta_title($tag)
	{
		if ( ! empty($tag->attr['from']))
		{
			$tag->attr['name'] = 'meta_title';
			return self::tag_field($tag);
		}
		return self::wrap($tag, self::get_value('article', 'meta_title', $tag));
//		return self::wrap($tag, strip_tags($tag->locals->article['meta_title']));
	}

	public static function tag_article_active_class($tag) { return self::wrap($tag, $tag->locals->article['active_class']); }
	

	// ------------------------------------------------------------------------
	
	
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

		// Page URL index to use
		$page_url = (config_item('url_mode') == 'short') ? 'url' : 'path';
		
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

		$categories = ( ! empty($tag->locals->article['categories'])) ? $tag->locals->article['categories'] : array();

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
			
			$url = anchor(base_url().$lang_url.$page[$page_url].'/'.$category_uri.'/'.$category['name'], $category_string);

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
		
		// Page URL index to use
		$page_url = (config_item('url_mode') == 'short') ? 'url' : 'path';

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
							$url = $p[$page_url]. '/' . $target_article['url'];
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

		$url = $tag->locals->article['url'];

		// Adds the suffix if defined in /application/config.php
		if ( config_item('url_suffix') != '' ) $url .= config_item('url_suffix');
		
		return $url;
	}


	// ------------------------------------------------------------------------

	
	public static function tag_article_view($tag) { return $tag->locals->article['view']; }



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
			return self::wrap($tag, '<a href="'.self::tag_article_url($tag).'">'.lang($term).'</a>'); 
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

	// ------------------------------------------------------------------------

	/**
	 * Set the parent scope
	 *
	 * @param	Object  FTL_Binding object
	 *
	 * @return  Array   Where condition
	 *                  If the "level" attribute isn't correct, returns the current page condition
	 *
	 * @usage
	 * <ion articles scope="parent" level="-1" all-parents="<TRUE/FALSE>">
	 *
	 * The "level" attribute can be positive, negative, or not set.
	 * If negative, the level will be defined by comparison to the current page level.
	 * For example, if the current page has the level 4, level="-3" will consider the
	 * parent page which has the level "1".
	 * If positive, it will return the parent page at the exact asked level.
	 * For example, if the current page is at level 4, level="2" will consider the
	 * parent page which is at level 2.
	 * Default value : -1 (one level up parent)
	 *
	 * The "all" attribute is used to set if the full parent path is to be used, means
	 * including the pages which has not the flag "has URL" checked in Ionize.
	 * Default value : FALSE
	 *
	 */
	static function set_parent_scope($tag)
	{
		$where = array();

		// ID page from where get the articles
		$id_page = NULL;

		// Tag attributes
		$level = $tag->getAttribute('level');
		$all_parents = ( $tag->getAttribute('all-parents') == TRUE) ? TRUE : FALSE;

		// Path IDs
		if ($all_parents)
			$path_ids = explode('/', $tag->locals->page['full_path_ids']);
		else
			$path_ids = explode('/', $tag->locals->page['path_ids']);

		// Level
		if (is_null($level))
			$level = -1;

		if ($level < 0)
			$level = $tag->locals->page['level'] + $level;

		if (isset($path_ids[$level]))
			$id_page = $path_ids[$level];

		if ( ! is_null($id_page))
			$where['id_page'] = $id_page;

		return $where;
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

	/**
	 * Prepare the articles array
	 *
	 * @static
	 *
	 * @param FTL_Binding	$tag
	 * @param Array 		$articles
	 *
	 * @return	Array		Articles
	 *
	 */
	private static function prepare_articles($tag, $articles)
	{
		// Articles index starts at 1.
		$index = 1;

		// view
		$view = (isset($tag->attr['view']) ) ? $tag->attr['view'] : FALSE;

		// paragraph limit ?
		$paragraph = (isset($tag->attr['paragraph'] )) ? self::get_attribute($tag, 'paragraph') : FALSE ;

		// auto_link
		$auto_link = (isset($tag->attr['auto_link']) && strtolower($tag->attr['auto_link'] == 'false') ) ? FALSE : TRUE ;

		// Last part of the URI
		$uri_last_part = array_pop(explode('/', uri_string()));

		$count = count($articles);

		foreach($articles as $key => $article)
		{
			// Force the view if the "view" attribute is defined
			if ($view !== FALSE)
				$articles[$key]['view'] = $view;

			$articles[$key]['active_class'] = '';
// Correct this
			if (!empty($tag->attr['active_class']))
			{
				$article_url = array_pop(explode('/', $article['url']));
				if ($uri_last_part == $article_url)
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

		return $articles;
	}


	private static function find_and_parse_article_view(FTL_Binding $tag, $article)
	{
		// Try to get the view defined for article
		if ( $article['view'] == FALSE OR $article['view'] == '')
		{
			if (count($tag->locals->page['articles']) == 1)
				$article['view'] = $tag->globals->page['article_view'];
			else
				$article['view'] = $tag->globals->page['article_list_view'];
		}

		// Default article view
		if (empty($article['view']))
			$article['view'] = Theme::get_default_view('article');

		// View path
		$view_path = Theme::get_theme_path().'views/'.$article['view'].EXT;

		// Return the Ionize default's theme view
		if ( ! file_exists($view_path))
		{
			$view_path = Theme::get_theme_path().'views/'.Theme::get_default_view('article').EXT;
			if ( ! file_exists($view_path))
				$view_path = APPPATH.'views/'.Theme::get_default_view('article').EXT;
		}

		return $tag->parse_as_nested(file_get_contents($view_path));
	}
}

TagManager_Article::init();

