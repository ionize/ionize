<?php
/**
 * Ionize Search module tags
 *
 * This class define the Search module tags
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 1.02
 *
 *
 */


/**
 * Search TagManager 
 *
 */
class Search_Tags extends TagManager
{
	protected static $_articles = NULL;

	/**
	 * Tags definition
	 *
	 * @var array
	 *
	 */
	public static $tag_definitions = array
	(
		'search:form' => 				'tag_search_form',
		'search:display' => 			'tag_search_display',
		'search:results' => 			'tag_search_results',
		'search:realm' =>				'tag_simple_value',
		'search:results:result' => 		'tag_expand',
		'search:results:count' => 		'tag_search_results_count',

		// title, subtitle are common tags, set by TagManager.
		// content, summary are not
		'search:results:result:content' => 		'tag_simple_value',
		'search:results:result:nb_words' => 	'tag_simple_value',
		'search:results:result:page_url' => 	'tag_url',
	);


	/**
	 * Base search module tag
	 * The index function of this class refers to the <ion:search /> tag
	 * In other words, this function makes the <ion:search /> tag available as main module parent tag
	 * for all other tags defined in this class.
	 *
	 * @usage	<ion:search >
	 *			...
	 *			</ion:search>
	 *
	 */
	public static function index(FTL_Binding $tag)
	{
		// POST realm
		$realm = self::$ci->input->post('realm');

		// CI returns FALSE. But FALSE is a value !
		if ($realm == FALSE)
			$realm = NULL;

		$tag->set('realm', $realm);

		return $tag->expand();
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Display the search form
	 *
	 * @usage	<ion:search:form />
	 *
	 */
	public static function tag_search_form(FTL_Binding $tag)
	{
		// $searchForm_action = (isset($tag->attr['result_page']) ) ? $tag->attr['result_page'] : '';
		// $tag->locals->result_page = $searchForm_action;
		
		$tag->expand();
			
		// the tag returns the content of this view :
		return $tag->parse_as_nested(file_get_contents(MODPATH.'Search/views/search_form'.EXT));
	}

	
	// ------------------------------------------------------------------------

	
	/**
	 * Display the results view
	 *
	 * @usage	<ion:search:display />
	 *
	 */
	public static function tag_search_display(FTL_Binding $tag)
	{	
		$tag->expand();
			
		// the tag returns the content of this view :
		return $tag->parse_as_nested(file_get_contents(MODPATH.'Search/views/search_result'.EXT));
	}

	
	// ------------------------------------------------------------------------


	/**
	 * Search results tag
	 * Parent tag for results
	 *
	 * @param	FTL_Binding
	 * @return 	string
	 * @usage	<ion:search:results>
	 *
	 */
	public static function tag_search_results(FTL_Binding $tag)
	{
		$str = '';

		// POST realm
		$realm = $tag->get('realm');

		$tag->set('count', 0);

		if ($realm !== FALSE && $realm != '')
		{
			// Get the results
			if (is_null(self::$_articles))
			{
				// Loads the serach module model
				self::$ci->load->model('search_model', '', TRUE);

				$articles = self::$ci->search_model->get_articles($realm);

				if ( ! empty($articles))
				{
					// arrays of keys, for multisorting
					$knum = $kdate = array();
					$unique = array();

					foreach($articles as $key => &$article)
					{
						// remove duplicates
						if(!in_array($article['id_article'], $unique)) {
							$unique[] = $article['id_article'];
						
							// set number of found words
							preg_match_all('#'.$realm.'#i', $article['title'].' '.$article['content'], $match);
							$num = count($match[0]);

							$article['nb_words'] = $knum[$key] = $num;
							$kdate[$key] = strtotime($article['date']);
						
						} else {
							unset($articles[$key]);
						}
						
					}

					// Sort the results by realm occurences DESC first, by date DESC second.
					array_multisort($knum, SORT_DESC, SORT_NUMERIC, $kdate, SORT_DESC, SORT_NUMERIC, $articles);
				}

				// Init the articles URLs
				TagManager_Article::init_articles_urls($articles);

				// Adds the page URL to each article
				self::init_pages_urls($articles);

				self::$_articles = $articles;
			}

			// Add the number of result to the tag data
			$count = count(self::$_articles);

			$tag->set('count', $count);
			$tag->set('results', self::$_articles);


			foreach(self::$_articles as $key => $_article)
			{
				// The tag should at least do 1 expand to get the child "loop" attribute
				if ($tag->getAttribute('loop') === FALSE)
				{
					return $tag->expand();
				}
				else
				{
					$tag->set('result', $_article);
					$tag->set('count', $count);
					$tag->set('index', $key);

					$str .= $tag->expand();
				}
			}
		}

		// Expand the tag if no articles : Allows the children tags to be processed even not results were found
		// Must not be done if articles or this add one unwanted expand.
		if (empty(self::$_articles))
			$str .= $tag->expand();

		return $str;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the number of results
	 *
	 * @param	FTL_Binding
	 * @return 	string
	 */
	public static function tag_search_results_count(FTL_Binding $tag)
	{
		$tag->getParent()->setAttribute('loop', FALSE);
		return self::tag_simple_value($tag);
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds the page URL to the articles array
	 *
	 * @param $articles
	 */
	protected static function init_pages_urls(&$articles)
	{
		foreach($articles as &$article)
		{
			$segments = explode('/', $article['url']);
			array_pop($segments);
			$article['page_url'] = implode('/', $segments) . '/';
		}
	}
}
