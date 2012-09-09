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
		'articles' => 				'tag_articles',
		'articles:article' => 		'tag_articles_article',
		'article' => 				'tag_article',

		'article:summary' => 		'tag_simple_value',
		'article:active_class' => 	'tag_simple_value',
		'article:view' => 			'tag_simple_value',

		'article:content' => 		'tag_article_content',
		'article:categories' => 	'tag_article_categories',

		'article:next' => 	'tag_next_article',
		'article:prev' => 	'tag_prev_article',



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
	function get_articles(FTL_Binding $tag)
	{

// @TODO : Write local cache

log_message('error', ' -- CALL : get_articles()' );

		$articles = array();

		// Local tag page
		$page = $tag->get('page');

		// This should never arrived except if the user uses the tag outside from one parent tag
		if (empty($page))
			$page = self::registry('page');

		// Pages from registry
		$pages = self::registry('pages');

		// Get the potential special URI
		$special_uri = TagManager_Page::get_special_uri();

		// Use Pagination
		// The "articles" tag must explicitely indicates it want to use pagination. 
		// This explicit declaration is done to avoid all articles tags on one page using the same pagination value.
		$use_pagination = $tag->getAttribute('pagination', FALSE);

		// Don't use the "article_list_view" setting set through Ionize
		// Todo : Remove ?
		// $keep_view = (isset($tag->attr['keep_view'])) ? TRUE : FALSE;

		// Use this view for each article if more than one article
		// TODO : Remove ?
		// $list_view = (isset($tag->attr['list_view'])) ? $tag->attr['list_view'] : FALSE;

		$type = $tag->getAttribute('type');

		// Number of article limiter
		$num = $tag->getAttribute('limit', 0);

		// filter & "with" tag compatibility
		// create a SQL filter
		$filter = $tag->getAttribute('filter', FALSE);

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
		$from_page = $tag->getAttribute('from');

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
			{
				$where['article_type.type'] = 'NULL';
				$type = NULL;
			}
			else
				$where['article_type.type'] = $type;
		}

		// If a page name or ID is set, returns only articles from this page
		if ($from_page !== NULL)
		{
			// Get the asked page details
			$asked_pages = explode(',', $from_page);

			$in_pages = array();
			
			// Check if the page code or ID of each page can be used for filter
			foreach($pages as $page)
			{
				if (in_array($page['name'], $asked_pages))
					$in_pages[] = $page['id_page'];
				elseif(in_array($page['id_page'], $asked_pages))
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
			$where += array('id_page' => $page['id_page']);
		}
		// Get only articles from current page
		else
		{
			$where['id_page'] = $page['id_page'];
		}

		/* Get the articles
		 *
		 */
		// If a special URI exists, get the articles from it.
		if ( ! is_null($special_uri) && is_null($from_page) && (is_null($type)))
		{
			if (method_exists(__CLASS__, 'get_articles_from_'.$special_uri))
				$articles = call_user_func(array(__CLASS__, 'get_articles_from_'.$special_uri), $tag, $where, $filter);
		}
		// Get all the page articles
		// If Pagination is active, set the limit. This articles result is the first page of pagination
		else 
		{
			// Set Limit
			$limit = ( ! empty($page['pagination']) && ($page['pagination'] > 0) ) ? $page['pagination'] : FALSE;

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

		self::init_articles_urls($articles);

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
	 * @param	FTL_Binding
	 * @param	Array		SQL Condition array
	 * @param	String		Filter string
	 *
	 * @return	Array	Array of articles
	 *
	 */
	function get_articles_from_pagination(FTL_Binding $tag, $where, $filter)
	{
		$page = $tag->get('page');
		
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
	 * @param	FTL_Binding
	 * @param	Array	SQL Condition array
	 * @param	String	Filter string
	 *
	 * @return	Array	Array of articles
	 *
	 */
	function get_articles_from_category(FTL_Binding $tag, $where, $filter)
	{
		$articles = array();

		$page = $tag->get('page');

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
		}
		return $articles;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get articles linked from a period
	 * Called if special URI "archives" is found. See tag_articles()
	 * Uses the self::$uri_segments var to determine the category name
	 *
	 * @param	FTL_Binding
	 * @param	Array	SQL Condition array
	 * @param	String	Filter string
	 *
	 * @return	Array	Array of articles
	 *
	 */
	function get_articles_from_archives(FTL_Binding $tag, $where, $filter)
	{
		$articles = array();
		
		$page = $tag->get('page');

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
	 * @param	FTL_Binding
	 * @param	Array	SQL Condition array
	 * @param	String	Filter string
	 *
	 * @return	Array	Array of articles
	 */
	function get_articles_from_one_article(FTL_Binding $tag, $where, $filter)
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
		// Page URL key to use
		$page_url_key = (config_item('url_mode') == 'short') ? 'url' : 'path';

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
								$article['url'] = $parent_page[$page_url_key] . '/' . $target_article['url'];
						}
					}
					// Page
					else
					{
						$target_page = self::$ci->page_model->get_by_id($page['link_id'], Settings::get_lang('current'));
						$article['url'] = $target_page[$page_url_key];
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

					$article['url'] = base_url() . Settings::get_lang('current') . '/' . $page[$page_url_key] . '/' . $url;
				}
				else
				{
					$article['url'] = base_url() . $page[$page_url_key] . '/' . $url;
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
	 *
	 * @return	String
	 *
	 */
	public static function tag_article(FTL_Binding $tag)
	{
		$cache = ($tag->getAttribute('cache') == 'off') ? FALSE : TRUE;

		// Tag cache
		if ($cache == TRUE && ($str = self::get_cache($tag)) !== FALSE)
			return $str;

		// Returned string
		$str = '';

		// Registry article : Catch from URL
		$_article = self::registry('article');
		if ( ! empty($_article))
			$_articles = array($_article);
		else
			$_articles = array($tag->get('article'));

		// Add data like URL to each article
		// and finally render each article
		if ( ! empty($_articles))
		{
			$_articles = self::prepare_articles($tag, $_articles);

			// Add articles to the tag
			$tag->set('articles', $_articles);

			$count = count($_articles);

			foreach($_articles as $key => $article)
			{
				// Render the article
				$tag->set('article', $article);
				$tag->set('index', $key);
				$tag->set('count', $count);

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
	 * Expand the articles
	 *
	 * @param	FTL_Binding object
	 *
	 * @return	String
	 *
	 */
	public static function tag_articles(FTL_Binding $tag)
	{
		$cache = ($tag->getAttribute('cache') == 'off') ? FALSE : TRUE;

		// Tag cache
		if ($cache == TRUE && ($str = self::get_cache($tag)) !== FALSE)
			return $str;

		// Returned string
		$str = '';

		/* Get the articles
		 *
		 */
		$_articles = self::get_articles($tag);

		$count = count($_articles);
		$tag->set('count', $count);

		$_articles = self::prepare_articles($tag, $_articles);
		$tag->set('articles', $_articles);

		// Make articles in random order
		if ( $tag->getAttribute('random') == TRUE)
			shuffle ($articles);

		// Stop here if asked : Needed by aggregation tags
		if ($tag->getAttribute('loop') === FALSE)
			return $tag->expand();

		// Add data like URL to each article
		// and finally render each article
		foreach($_articles as $key=>$article)
		{
			$tag->set('article', $article);
			$tag->set('index', $key);
			$tag->set('count', $count);

			$str .= $tag->expand();
		}

		// Experimental : To allow tags in articles
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
	 * @param	FTL_Binding
	 *
	 * @return	String
	 *
	 */
	public static function tag_articles_article(FTL_Binding $tag)
	{
		if (
			!is_null($tag->getAttribute('render'))
			&& !is_null($tag->get('article'))
		)
		{
			return self::find_and_parse_article_view($tag, $tag->get('article'));
		}

		return $tag->expand();
	}


	// ------------------------------------------------------------------------
	
	
	/**
	 * Returns HTML categories links wrapped by the given tag
	 *
	 * @param	FTL_Binding
	 *
	 * @return	string
	 *
	 * @usage	<ion:article:categories [tag="ul" child-tag="li" link="true" separator=" &bull; "] />
	 *
	 *
	 */
	public static function tag_article_categories(FTL_Binding $tag)
	{
		$data = array();

		$categories = TagManager_Category::get_categories($tag);

		// HTML Separator of each category
		$separator = $tag->getAttribute('separator', ' | ');
		
		// Make a link from each category or not. Default : TRUE
		$link = $tag->getAttribute('link', FALSE);

		// Field to return for each category.
		$field =  $tag->getAttribute('key', 'title');

		// Child tag : HTML tag for each element
		$child_tag =  $tag->getAttribute('child-tag');
		$child_class =  $tag->getAttribute('child-class');

		// Separator attribute is not compatible with child-tag
		if ( ! is_null($child_tag))
			$separator = FALSE;

		// Build the anchor array
		foreach($categories as $category)
		{
			$str = $category[$field];
			$tag->set('category', $category);

			if ($link == TRUE)
				$str = anchor($category['url'], $str);

			if ( ! is_null($child_tag))
			{
				// Replace the class and tag by the child tag & class
				$html_tag =  $tag->getAttribute('tag');
				$html_class =  $tag->getAttribute('class');

				$tag->setAttribute('tag', $child_tag);
				$tag->setAttribute('class', $child_class);

				// Process the child rendering
				$str = self::wrap($tag, $str);

				// Restore the tag & class for parent
				$tag->setAttribute('tag', $html_tag);
				$tag->setAttribute('class', $html_class);
			}

			$data[] = $str;
		}

		return self::wrap($tag, implode($separator, $data));
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the article content
	 *
	 * @param	FTL_Binding
	 *
	 * @return	String
	 *
	 */
	public static function tag_article_content(FTL_Binding $tag)
	{
		// paragraph limit ?
		$paragraph = $tag->getAttribute('paragraph');

		$content = $tag->getValue('content');

		// Limit to x paragraph if the attribute is set
		if ( ! is_null($paragraph))
			$content = tag_limiter($content, 'p', intval($paragraph));

		return self::wrap($tag, $content);
	}


	// ------------------------------------------------------------------------


	public static function tag_prev_article(FTL_Binding $tag)
	{
		$article = self::get_adjacent_article($tag, 'prev');
	
		return self::process_prev_next_article($tag, $article);
	}


	// ------------------------------------------------------------------------


	public static function tag_next_article(FTL_Binding $tag)
	{
		$article = self::get_adjacent_article($tag, 'next');
	
		return self::process_prev_next_article($tag, $article);
	}


	// ------------------------------------------------------------------------


	private static function get_adjacent_article(FTL_Binding $tag, $mode='prev')
	{
		$found_article = NULL;

		// Articles & Current article
		$articles = $tag->get('articles');
		$article = self::registry('article');

		$enum = ($mode=='prev') ? -1 : 1;
		
		foreach($articles as $key => $_article)
		{
			if ($_article['id_article'] == $article['id_article'])
			{
				if ( ! empty($articles[$key + $enum]))
				{
					$found_article = $articles[$key + $enum];
					break;
				}
			}
		}
		$tag->set('article', $found_article);
		
		return $found_article;
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
	static function set_parent_scope(FTL_Binding $tag)
	{
		$where = array();

		// ID page from where get the articles
		$id_page = NULL;

		// Tag attributes
		$level = $tag->getAttribute('level');
		$all_parents = ( $tag->getAttribute('all-parents') == TRUE) ? TRUE : FALSE;

		// Path IDs
		if ($all_parents)
			$path_ids = explode('/', $tag->locals->_page['full_path_ids']);
		else
			$path_ids = explode('/', $tag->locals->_page['path_ids']);

		// Level
		if (is_null($level))
			$level = -1;

		if ($level < 0)
			$level = $tag->locals->_page['level'] + $level;

		if (isset($path_ids[$level]))
			$id_page = $path_ids[$level];

		if ( ! is_null($id_page))
			$where['id_page'] = $id_page;

		return $where;
	}


	// ------------------------------------------------------------------------


	/**
	 * Processes the next / previous article tags result
	 * Internal use only.
	 *
	 * @param	FTL_Binding
	 * @param	array
	 *
	 * @return string
	 *	 
	 */
	private static function process_prev_next_article(FTL_Binding $tag, $article=NULL)
	{
		if ( ! is_null($article))
		{
			$value_key = $tag->getAttribute('display', 'title');
			$value = $tag->getValue($value_key);

			// Build the A HTML element ?
			// This is not compatible with the helper attribute, which need a "pure" value
			if (is_null($tag->getAttribute('helper')) && $tag->getAttribute('href') === TRUE)
			{
				$url = $tag->getValue('url');
				$value = self::create_href($tag, $url);
			}

			return self::wrap($tag, $value);
		}
		
		return '';
	}


	// ------------------------------------------------------------------------


	/**
	 * Prepare the articles array
	 *
	 * @param FTL_Binding	$tag
	 * @param Array 		$articles
	 *
	 * @return	Array		Articles
	 *
	 */
	private static function prepare_articles(FTL_Binding $tag, $articles)
	{
		// Articles index starts at 1.
		$index = 1;

		// view
		$view = $tag->getAttribute('view');

		// paragraph limit ?
		$paragraph = $tag->getAttribute('paragraph');

		// auto_link
		$auto_link = $tag->getAttribute('auto_link', TRUE);

		// Last part of the URI
		$uri_last_part = array_pop(explode('/', uri_string()));

		$count = count($articles);

		foreach($articles as $key => $article)
		{
			// Force the view if the "view" attribute is defined
			if ( ! is_null($view))
				$articles[$key]['view'] = $view;

			$articles[$key]['active_class'] = '';

			if (!is_null($tag->getAttribute('active_class')))
			{
				$article_url = array_pop(explode('/', $article['url']));
				if ($uri_last_part == $article_url)
				{
					$articles[$key]['active_class'] = $tag->attr['active_class'];
				}
			}

			// Limit to x paragraph if the attribute is set
			if ( ! is_null($paragraph))
				$articles[$key]['content'] = tag_limiter($article['content'], 'p', intval($paragraph));

			// Autolink the content
			if ($auto_link)
				$articles[$key]['content'] = auto_link($articles[$key]['content'], 'both', TRUE);

			// Article's index
			$articles[$key]['index'] = $index++;

			// Article's count
			$articles[$key]['count'] = $count;
		}

		return $articles;
	}


	/**
	 * Find and parses the article view
	 *
	 * @param 	FTL_Binding
	 * @param   array
	 *
	 * @return string
	 *
	 */
	private static function find_and_parse_article_view(FTL_Binding $tag, $article)
	{
		// Registered page
		$page = self::registry('page');

		// Local articles
		$articles = $tag->get('articles');

		// Try to get the view defined for article
		if ( $article['view'] == FALSE OR $article['view'] == '')
		{
			if (count($articles) == 1)
				$article['view'] = $page['article_view'];
			else
				$article['view'] = $page['article_list_view'];
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

// TagManager_Article::init();

