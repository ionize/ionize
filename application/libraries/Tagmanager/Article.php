<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
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
		'article:content' => 		'tag_simple_content',
		'article:active_class' => 	'tag_simple_value',
		'article:view' => 			'tag_simple_value',
		'article:next' => 			'tag_next_article',
		'article:prev' => 			'tag_prev_article',
		'article:type' => 			'tag_simple_value',
		'article:deny_code' => 		'tag_simple_value',
		'article:deny' => 			'tag_article_deny',

		'article:is_active' => 		'tag_is_active',
	);


	// ------------------------------------------------------------------------


	public static function get_article_by($field, $value, FTL_Binding $tag)
	{
		$where = array(
			$field => $value
		);

		// Get from DB
		$article = self::$ci->article_model->get(
			$where,
			$lang = Settings::get_lang()
		);

		return $article;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get Articles
	 * @TODO : 	Write local cache
	 *
	 * @param	FTL_Binding
	 * @return	Array
	 *
	 * 1. Try to get the articles from a special URI
	 * 2. Get the articles from the current page
	 * 3. Filter on the article name if the article name is in URI segment 1
	 *
	 */
	public static function get_articles(FTL_Binding $tag)
	{
		// Page. 1. Local one, 2. Current page (should never arrived except if the tag is used without the 'page' parent tag)
		$page = $tag->get('page');

		// Only get all articles (no limit to one page) if asked.
		// Filter by current page by default
		if (empty($page) && $tag->getAttribute('all') == NULL)
		{
			$page = self::registry('page');
		}

		// Set by Page::get_current_page()
		$is_current_page = isset($page['__current__']) ? TRUE : FALSE;

		// Pagination
		$tag_pagination = $tag->getAttribute('pagination');
		$ionize_pagination = $page['pagination'];

		// Authorizations
		$tag_authorization = $tag->getAttribute('authorization');

		// Type filter, limit, SQL filter
		$type = $tag->getAttribute('type');
		$nb_to_display = $tag->getAttribute('limit', 0);
		$filter = $tag->getAttribute('filter');

        if( ! is_null($filter) )
            $filter = self::process_filter($filter);

		// URL based process of special URI only allowed on current page
		$special_uri_array = self::get_special_uri_array();

		if ($is_current_page)
		{
			// Special URI process
			if ( ! is_null($special_uri_array))
			{
				foreach($special_uri_array as $_callback => $args)
				{
					if (method_exists(__CLASS__, 'add_articles_filter_'.$_callback))
						call_user_func(array(__CLASS__, 'add_articles_filter_'.$_callback), $tag, $args);
				}
			}
			// Deactivate "limit" if one pagination is set
			if ($tag_pagination OR $ionize_pagination) $nb_to_display = 0;
		}
		else
		{
			// Deactivate Ionize pagination (Only available of the current page)
			$ionize_pagination = NULL;

			// Deactivate limit if the "pagination" attribute is set
			if ($tag_pagination) $nb_to_display = 0;
		}

		// If pagination is only set by the tag : Call the pagination filter
		if ($tag_pagination)
		{
			if ( is_null($special_uri_array) OR ! array_key_exists('pagination', $special_uri_array))
				self::add_articles_filter_pagination($tag);
		}

		// from categories ?
		// @TODO : Find a way to display articles from a given category : filter ?
		$from_categories = $tag->getAttribute('from_categories');
		$from_categories_condition = ($tag->getAttribute('from_categories_condition') != NULL && $tag->attr['from_categories_condition'] != 'or') ? 'and' : 'or';

		/*
		 * Preparing WHERE on articles
		 * From where do we get the article : from a page, from the parent page or from the all website ?
		 *
		 */
		// Order. Default order : ordering ASC
		$order_by = $tag->getAttribute('order_by', 'id_page, ordering ASC');
		$where = array('order_by' => $order_by);

		// Add type to the where array
		if ( ! is_null($type))
		{
			if ($type == '') {
				$where['article_type.type'] = 'NULL';
				$type = NULL;
			}
			else
			{
				if (strpos($type, ',') !== FALSE)
				{
					$type = preg_replace('/\s+/', '', $type);
					$type = explode(',', $type);
					foreach($type as $k=>$t)
						if (empty($t))
							unset($type[$k]);

					$where['where_in'] = array('article_type.type' => $type);
				}
				else
				{
					$where['article_type.type'] = $type;
				}
			}
		}

		// Get only articles from the detected page
		if ( ! empty($page))
			$where['id_page'] = $page['id_page'];

		// Set Limit : First : pagination, Second : limit
		$limit = $tag_pagination ? $tag_pagination : $ionize_pagination;
		if ( ! $limit && $nb_to_display > 0) $limit = $nb_to_display;
		if ( $limit ) $where['limit'] = $limit;

		// Get from DB
		$articles = self::$ci->article_model->get_lang_list(
			$where,
			$lang = Settings::get_lang(),
			$filter
		);

		$articles = self::filter_articles($tag, $articles);

		// Filter on authorizations
		if (User()->get('role_level') < 1000)
		{
			$articles = self::_filter_articles_authorization($articles, $tag_authorization);
		}

		// Pagination needs the total number of articles, without the pagination filter
		// TODO : Integrates authorizations in articles count
		if ($tag_pagination OR $ionize_pagination)
		{
			$nb_total_articles = self::count_nb_total_articles($tag, $where, $filter);
			$tag->set('nb_total_items', $nb_total_articles);
		}

		self::init_articles_urls($articles);

		self::init_articles_views($articles);

		return $articles;
	}


	// ------------------------------------------------------------------------


	/**
	 * Return the number of articles, excluding the pagination filter.
	 *
	 * @param FTL_Binding
	 * @param array
	 * @param null|string
	 *
	 * @return int
	 *
	 */
	function count_nb_total_articles(FTL_Binding $tag, $where = array(), $filter=NULL)
	{
		$page = $tag->get('page');
		if (empty($page)) $page = self::registry('page');

		// Set by Page::get_current_page()
		$is_current_page = isset($page['__current__']) ? TRUE : FALSE;

		$special_uri_array = self::get_special_uri_array();

		// Nb articles for current page
		if ($is_current_page)
		{
			// Filters (except pagination)
			if (! is_null($special_uri_array))
			{
				foreach($special_uri_array as $_callback => $args)
				{
					if ($_callback != 'pagination' && method_exists(__CLASS__, 'add_articles_filter_'.$_callback))
						call_user_func(array(__CLASS__, 'add_articles_filter_'.$_callback), $tag, $args);
				}
			}
		}

		$nb_total_articles = self::$ci->article_model->count_articles(
			$where,
			$lang = Settings::get_lang(),
			$filter
		);

		return $nb_total_articles;
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds one category filter
	 *
	 * @param FTL_Binding $tag
	 * @param array       $args
	 *
	 */
	function add_articles_filter_category(FTL_Binding $tag, $args = array())
	{
		$category_name = ( ! empty($args[0])) ? $args[0] : NULL;

		if ( ! is_null($category_name))
		{
			self::$ci->article_model->add_category_filter(
				$category_name,
				Settings::get_lang()
			);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds one tag filter
	 *
	 * @param FTL_Binding $tag
	 * @param array       $args
	 *
	 */
	function add_articles_filter_tag(FTL_Binding $tag, $args = array())
	{
		$tag_name = ( ! empty($args[0])) ? $args[0] : NULL;

		if ( ! is_null($tag_name))
		{
			self::$ci->article_model->add_tag_filter($tag_name);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds one pagination filter
	 *
	 * @param FTL_Binding $tag
	 * @param array       $args
	 *
	 * @return null
	 */
	function add_articles_filter_pagination(FTL_Binding $tag, $args = array())
	{
		// Page
		$page = $tag->get('page');
		if (is_null($page)) $page = self::registry('page');

		$start_index = ! empty($args[0]) ? $args[0] : NULL;

		// Pagination : First : tag, second : page
		$pagination = $tag->getAttribute('pagination');
		if (is_null($pagination))
			$pagination = $page['pagination'];

		// Exit if no info about pagination can be found.
		if ( ! $pagination)
			return NULL;

		self::$ci->article_model->add_pagination_filter($pagination, $start_index);
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds one archives filter
	 *
	 * @param FTL_Binding $tag
	 * @param array       $args
	 */
	function add_articles_filter_archives(FTL_Binding $tag, $args = array())
	{
		// Month / year
		$year =  (! empty($args[0]) ) ?	$args[0] : FALSE;
		$month =  (! empty($args[1]) ) ? $args[1] : NULL;

		if ($year)
			self::$ci->article_model->add_archives_filter($year, $month);
	}


	// ------------------------------------------------------------------------


	/**
	 * Inits articles URLs
	 * Get the contexts of all given articles and define each article correct URL
	 *
	 * @param $articles
	 *
	 */
	public function init_articles_urls(&$articles)
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
			if ($article['link_type'] != '' )
			{
				// External
				if ($article['link_type'] == 'external')
				{
					$article['absolute_url'] = $article['link'];
				}

				// Email
				else if ($article['link_type'] == 'email')
				{
					$article['absolute_url'] = auto_link($article['link'], 'both', TRUE);
				}

				// Internal
				else
				{
					// Article
					if($article['link_type'] == 'article')
					{
						// Get the article to which this page links
						$rel = explode('.', $article['link_id']);
						$target_article = self::$ci->article_model->get_context($rel[1], $rel[0], Settings::get_lang('current'));

						// Of course, only if not empty...
						if ( ! empty($target_article))
						{
							// Get the article's parent page
							$parent_page = self::$ci->page_model->get_by_id($rel[0], Settings::get_lang('current'));

							if ( ! empty($parent_page))
								$article['absolute_url'] = $parent_page[$page_url_key] . '/' . $target_article['url'];
						}
					}
					// Page
					else
					{
						$target_page = self::$ci->page_model->get_by_id($article['link_id'], Settings::get_lang('current'));

						// If target page is offline, 'path' is not set
						if ( isset($target_page[$page_url_key]))
							$article['absolute_url'] = $target_page[$page_url_key];
						else
							$article['absolute_url'] = '#';
					}

					$article['relative_url'] = $article['absolute_url'];
					$article['relative_lang_url'] = $article['absolute_url'];

					// Correct the URL : Lang + Base URL
					if ( count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1' )
					{
						$article['absolute_url'] =  Settings::get_lang('current'). '/' . $article['absolute_url'];
						$article['relative_lang_url'] = $article['absolute_url'];
					}

					$article['absolute_url'] = base_url() . $article['absolute_url'];

				}
			}
			// Standard URL
			else
			{
				$article['relative_url'] = $article['relative_lang_url'] = $page[$page_url_key] . '/' . $url;;

				if ( count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1' )
				{

					$article['relative_lang_url'] = Settings::get_lang('current') . '/' . $article['relative_url'];
					$article['absolute_url'] = base_url() . $article['relative_lang_url'];
				}
				else
				{
					$article['absolute_url'] = base_url() . $article['relative_url'];
				}
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Inits, for each article, the view to use.
	 *
	 * @param $articles
	 *
	 */
	private function init_articles_views(&$articles)
	{
		$nb = count($articles);

		foreach ($articles as $k=>$article)
		{
			if (empty($article['view']))
			{
				if ($nb > 1 && ! empty($article['article_list_view']))
				{
					$articles[$k]['view'] = $article['article_list_view'];
				}
				else if (! empty($article['article_view']))
				{
					$articles[$k]['view'] = $article['article_view'];
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
		$cache = $tag->getAttribute('cache', TRUE);
		$key = $tag->getAttribute('key');

		// Tag cache
		if ($cache == TRUE && ($str = self::get_cache($tag)) !== FALSE)
			return $str;


		// Returned string
		$str = '';

		// Extend Fields tags
		self::create_extend_tags($tag, 'article');

		// 1. Registry (URL ask), Second : Stored one
		$_article = self::registry('article');

		// 2. Asked through one key ? (but no <ion:articles /> parent tag )
		if (empty($_article))
		{
			if ( ! is_null($key) && $tag->getDataParentName() != 'articles')
				$_article = self::get_article_by('name', $key, $tag);

			if (empty($_article))
				$_article = $tag->get('article');
		}

		$_articles = array();
		if ( ! empty($_article)) $_articles = array($_article);

		// Add data like URL to each article and render the article
		if ( ! empty($_articles))
		{
			$_articles = self::prepare_articles($tag, $_articles);
			$_article = $_articles[0];

			// Render the article
			$tag->set('article', $_article);
			$tag->set('index', 0);
			$tag->set('count', 1);

			// Parse the article's view if the article tag is single (<ion:article />)
			if($tag->is_single())
				$str = self::find_and_parse_article_view($tag, $_article);
			// Else expand the tag
			else
				$str = self::wrap($tag, $tag->expand());
		}

		// Tag cache
		self::set_cache($tag, $str);

		return $str;
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 * @param FTL_Binding $tag
	 * @return string
	 *
	 */
	public static function tag_article_deny(FTL_Binding $tag)
	{
		// Set this tag as "process tag"
		$tag->setAsProcessTag();

		// 1. Try to get from tag's data array
		$value = $tag->getValue('deny_code', 'article');

		$resource = 'frontend/article/' . $tag->getValue('id_article', 'article');

		if (Authority::cannot('access', $resource, NULL, TRUE))
		{
			return self::output_value($tag, $value);
		}
		else
		{
			if ($tag->getAttribute('is') == '')
			{
				self::$trigger_else = 0;
				return self::wrap($tag, $tag->expand());
			}
		}
		return '';
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
		$cache = $tag->getAttribute('cache', TRUE);

		// Tag cache
		if ($cache == TRUE && ($str = self::get_cache($tag)) !== FALSE)
			return $str;

		// Returned string
		$str = '';

		// Extend Fields tags
		self::create_extend_tags($tag, 'article');

		// Articles
		$_articles = self::get_articles($tag);
		$_articles = self::prepare_articles($tag, $_articles);

		// Tag data
		$count = count($_articles);
		$tag->set('count', $count);

		// Make articles in random order
		if ( $tag->getAttribute('random') == TRUE)
			shuffle($_articles);

		$tag->set('articles', $_articles);

		// Add data like URL to each article
		// and finally render each article
		foreach($_articles as $key => $article)
		{
			$tag->set('article', $article);

			// Set by self::prepare_articles()
			// $tag->set('index', $key);
			$tag->set('count', $count);

			$str .= $tag->expand();
		}

		// Experimental : To allow tags in articles
		// $str = $tag->parse_as_nested($str);

		$str = self::wrap($tag, $str);
		
		// Tag cache
		self::set_cache($tag, $str);
		
		return $str;
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
		$article = ! is_null($tag->get('article')) ? $tag->get('article') : NULL;

		// self::create_sub_tags($tag);

		if (
			!is_null($tag->getAttribute('render'))
			&& ! is_null($article)
		)
		{
			return self::find_and_parse_article_view($tag, $article);
		}

		return $tag->expand();
	}


	// ------------------------------------------------------------------------


	public static function tag_prev_article(FTL_Binding $tag)
	{
		$all = $tag->getAttribute('all');

		if ( ! is_null($all))
		{
			$str = '';
			$articles = self::get_adjacent_articles($tag, 'prev');

			foreach($articles as $_article)
			{
				$tag->set('data', $_article);
				$str .= $tag->expand();
			}

			return self::wrap($tag, $str);
		}
		else
		{
			$article = self::get_adjacent_article($tag, 'prev');
			$tag->set('data', $article);

			return self::process_prev_next_article($tag, $article);
		}
	}


	// ------------------------------------------------------------------------


	public static function tag_next_article(FTL_Binding $tag)
	{
		$all = $tag->getAttribute('all');

		if ( ! is_null($all))
		{
			$str = '';
			$articles = self::get_adjacent_articles($tag, 'next');

			foreach($articles as $_article)
			{
				$tag->set('data', $_article);
				$str .= $tag->expand();
			}

			return self::wrap($tag, $str);
		}
		else
		{
			$article = self::get_adjacent_article($tag, 'next');
			$tag->set('data', $article);

			return self::process_prev_next_article($tag, $article);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all previous or next articles regarding to the current one
	 *
	 * @param FTL_Binding $tag
	 * @param string      $mode
	 *
	 * @return array|null
	 */
	private static function get_adjacent_articles(FTL_Binding $tag, $mode='prev')
	{
		$found_articles = NULL;

		$articles = self::prepare_articles($tag, self::get_articles($tag));

		// Get the article : Fall down to registry if no one found in tag
		$article = $tag->get('article');

		// Get the current article pos
		$pos = NULL;
		foreach($articles as $key => $_article)
		{
			if ($_article['id_article'] == $article['id_article'])
			{
				$pos = $key;
				break;
			}
		}

		if ($mode == 'prev')
			$found_articles = array_slice($articles, 0, $pos);
		else
			$found_articles = array_slice($articles, $pos+1);

		return $found_articles;
	}


	// ------------------------------------------------------------------------


	private static function get_adjacent_article(FTL_Binding $tag, $mode='prev')
	{
		$found_article = NULL;

		$articles = self::prepare_articles($tag, self::get_articles($tag));

		// Get the article : Fall down to registry if no one found in tag
		$article = $tag->get('article');

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

		return $found_article;
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
	private static function process_prev_next_article(FTL_Binding $tag, $article = NULL)
	{
		$str = '';
		if ($article)
			$str = self::wrap($tag, $tag->expand());

		return $str;
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
		// Removed because of double execution
		// (also done by the Tagmanager)
		// $auto_link = $tag->getAttribute('auto_link', TRUE);

		// Last part of the URI
		$uri_last_part = array_pop(explode('/', uri_string()));

		$count = count($articles);

		foreach($articles as $key => $article)
		{
			// Force the view if the "view" attribute is defined
			if ( ! is_null($view))
				$articles[$key]['view'] = $view;

			$articles[$key]['active_class'] = '';

			$article_url = array_pop(explode('/', $article['url']));
			$articles[$key]['is_active'] = ($uri_last_part == $article_url);

			if (!is_null($tag->getAttribute('active_class')))
			{
				if ($uri_last_part == $article_url)
					$articles[$key]['active_class'] = $tag->attr['active_class'];
			}

			// Limit to x paragraph if the attribute is set
			if ( ! is_null($paragraph))
				$articles[$key]['content'] = tag_limiter($article['content'], 'p', intval($paragraph));

			// Autolink the content
//			if ($auto_link)
//				$articles[$key]['content'] = auto_link($articles[$key]['content'], 'both', TRUE);

			// Article's index
			$articles[$key]['index'] = $index++;

			// Article's count
			$articles[$key]['count'] = $count;

			// Article's ID
			$articles[$key]['id'] = $articles[$key]['id_article'];

		}

		return $articles;
	}


	// ------------------------------------------------------------------------

	/**
	 * Filters the articles regarding range.
	 *
	 */
	public static function filter_articles(FTL_Binding $tag, $articles)
	{
		// Range : Start and stop index, coma separated
		$range = $tag->getAttribute('range');
		if (!is_null($range))
			$range = explode(',', $range);

		// Number of wished displayed medias
		$limit = $tag->getAttribute('limit');

		$from = $to = FALSE;

		if (is_array($range))
		{
			$from = $range[0];
			$to = (isset($range[1]) && $range[1] >= $range[0]) ? $range[1] : FALSE;
		}

		// Return list ?
		// If set to "list", will return the list, coma separated.
		// Usefull for javascript
		// Not yet implemented
		$return = $tag->getAttribute('return', FALSE);

		if ( ! empty($articles))
		{
			// Range / Limit ?
			if ( ! is_null($range))
			{
				$length = ($to !== FALSE) ? $to + 1 - $from  : count($articles) + 1 - $from;

				if ($limit > 0 && $limit < $length) $length = $limit;

				$from = $from -1;

				$articles = array_slice($articles, $from, $length);
			}
			else if ($limit > 0)
			{
				$articles = array_slice($articles, 0, $limit);
			}


			// Other filters
			if ( ! empty($articles))
			{
				// $keys = array_keys($filtered_medias[0]);
				$attributes = $tag->getAttributes();
				$attributes = array_diff(array_keys($attributes), array('tag', 'class', 'type', 'order_by', 'range', 'limit', 'filter', 'return'));

				if ( ! empty($attributes))
				{
					$tmp_articles = $articles;
					$filtered_articles = array();

					foreach($attributes as $attribute)
					{
						$attribute_value = $tag->getAttribute($attribute);

						foreach($tmp_articles as $article)
						{
							if (isset($article[$attribute]))
							{
								if ($article[$attribute] == $attribute_value)
									$filtered_articles[] = $article;
							}
							else
								$filtered_articles[] = $article;
						}
					}

					$articles = $filtered_articles;
				}
			}
		}

		return $articles;
	}


	// ------------------------------------------------------------------------


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
		$tag_view = $tag->getAttribute('view');
		$article_view = ! is_null($tag_view) ? $tag_view : ( ! empty($article['view']) ? $article['view'] : NULL);

		if ( ! is_null($article_view))
		{
			// Force first the view defined by the tag
			if ( ! is_null($tag_view))
				$article['view'] = $tag_view;
			else
			{
				if (count($articles) == 1)
					$article['view'] = $page['article_view'];
				else
					$article['view'] = $page['article_list_view'];
			}
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


	// ------------------------------------------------------------------------


	private static function _filter_articles_authorization($articles, $filter_codes=NULL)
	{
		if ( is_string($filter_codes) ) $filter_codes = explode(',', $filter_codes);
		$codes = array();

		if ( is_array($filter_codes))
		{
			foreach($filter_codes as $code)
				$codes[] = trim($code);
		}

		if (in_array('all', $codes) && count($codes) == 1)
			return $articles;

		$return = array();

		foreach ($articles as $article)
		{
			$resource = 'frontend/article/' . $article['id_article'];

			if ( Authority::cannot('access', $resource, NULL, TRUE))
			{
				if (empty($codes))
					continue;

				if (in_array($article['deny_code'], $codes))
					$return[] = $article;
			}
			else
			{
				if (in_array('all', $codes))
					$return[] = $article;

				else if ( ! empty($codes))
					continue;

				else
					$return[] = $article;
			}
		}

		return $return;
	}

}

TagManager_Article::init();

