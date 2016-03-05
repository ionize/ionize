<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.9
 *
 */

/**
 * Content Type TagManager
 *
 */
class TagManager_Content_type extends TagManager
{
	/**
	 * Categories local storage
	 * @var array
	 */
	protected static $_content_types = NULL;


	public static $tag_definitions = array
	(
		'content_type' => 						'tag_content_type',
		'content_type:articles' => 				'tag_content_type_articles',
		'content_type:articles:article' => 		'tag_article',
	);


	public static function tag_content_type(FTL_Binding $tag)
	{
		self::$ci->load->model(
			array(
				'content_type_model',
			),
			'',
			TRUE
		);



		return $tag->expand();
	}



	public static function tag_content_type_articles(FTL_Binding $tag)
	{
		$content_type = self::_get_by_name($tag->getAttribute('content_type'));

		if ( ! is_null($content_type))
		{
			$cache = $tag->getAttribute('cache', TRUE);

			// Tag cache
			if ($cache == TRUE && ($str = self::get_cache($tag)) !== FALSE)
				return $str;

			// Returned string
			$str = '';

			// Extend Fields tags
			self::create_extend_tags($tag, 'article');

			$_articles = self::get_articles($tag, $content_type);
			$_articles = TagManager_Article::prepare_articles($tag, $_articles);

			log_message('error', print_r(self::$_content_types, TRUE));
			log_message('error', print_r($_articles, TRUE));




			return 'toto';
		}

		return '';
	}


	public static function get_articles(FTL_Binding $tag, $content_type)
	{
		// Authorizations
		$tag_authorization = $tag->getAttribute('authorization');
		$limit = $tag->getAttribute('limit');

		// Type filter, limit, SQL filter
		$type = $tag->getAttribute('type');
		$nb_to_display = $tag->getAttribute('limit', 0);
		$filter = $tag->getAttribute('filter');

		if( ! is_null($filter) )
			$filter = self::process_filter($filter);

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
			if ($type == '')
			{
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

		// Get only articles from content type
		// $where['id_page'] = $page['id_page'];

		// $content_type

		// Set Limit : First : pagination, Second : limit
		if ( $nb_to_display > 0) $limit = $nb_to_display;
		if ( $limit ) $where['limit'] = $limit;

		// Get from DB
		$articles = self::$ci->article_model->get_lang_list(
			$where,
			$lang = Settings::get_lang(),
			$filter
		);

		$articles = TagManager_Article::filter_articles($tag, $articles);

		// Filter on authorizations
		if (User()->get('role_level') < 1000)
		{
			$articles = TagManager_Article::filter_articles_authorization($articles, $tag_authorization);
		}

		$articles = self::$ci->article_model->init_articles_urls($articles);

		return $articles;
	}


	public static function _get_by_name($name)
	{
		$content_types = self::_get_content_types();
		$return = NULL;


		foreach($content_types as $_content_type)
		{
			if ($_content_type['name'] == $name)
				$return = $_content_type;
		}

		return $return;
	}


	public static function _get_content_types()
	{
		if (self::$_content_types == NULL)
		{
			self::$_content_types = self::$ci->content_type_model->get_list();
		}

		return self::$_content_types;
	}

}