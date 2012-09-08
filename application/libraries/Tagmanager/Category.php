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
 * Category TagManager 
 *
 */
class TagManager_Category extends TagManager
{
	/**
	 * Categories local storage
	 *
	 */
	protected static $categories = array();


	/**
	 * Current visited category URI
	 *
	 */
	protected static $category_uri = NULL;


	public static $tag_definitions = array
	(
		'categories' => 				'tag_categories',
		'category' =>					'tag_category',
		'category:is_active' => 		'tag_is_active',
		'category:active_class' => 		'tag_simple_value',
	);

	public static function init()
	{
		$uri = preg_replace("|/*(.+?)/*$|", "\\1", self::$ci->uri->uri_string);
		self::$uri_segments = explode('/', $uri);
	}


	/**
	 * Returns the categories
	 *
	 * @param	FTL_Binding
	 * @param	array/null
	 *
	 * @return	array
	 *
	 */
	protected function get_categories(FTL_Binding $tag, $page = NULL)
	{
		// Categories model
		// isset(self::$ci->category_model) OR self::$ci->load->model('category_model');
		self::$ci->load->model('category_model');

		$page_url = '';

		// CSS class to use for the current category
		$active_class = $tag->getAttribute('active_class', 'active');

		// Asked category
		$category_uri = self::get_category_uri();

		// Get categories from this page articles
		if ( ! is_null($page))
		{
			$page_url = $page['absolute_url'] .'/';
			$categories = self::$ci->category_model->get_categories_from_pages($page['id_page'], Settings::get_lang());
		}
		// No page set : The URL of each category will look like base URL of the website.
		else
		{
			$page_url = Pages::get_home_page_url();

			$categories = self::$ci->category_model->get_lang_list(
				array('order_by' => 'ordering ASC'),
				Settings::get_lang()
			);
		}

		// Flip the URI config array to have the category index first
		$uri_config = array_flip(self::$ci->config->item('special_uri'));

		// Add the URL to the category to each category row
		// Also add the active class
		foreach($categories as $key => $category)
		{
			$categories[$key]['url'] = 			$page_url . $uri_config['category'] . '/' . $category['name'];
			$categories[$key]['lang_url'] = 	$page_url . $uri_config['category'] . '/' . $category['name'];

			// Active category ?
			$categories[$key]['active_class'] = ($category['name'] == $category_uri) ? $active_class : '';
			$categories[$key]['is_active'] = 	($category['name'] == $category_uri) ? TRUE : FALSE;
		}
	
		// Reorder array keys
		return array_values($categories);
	}


	/**
	 * Returns the current category URI
	 *
	 * @return string
	 *
	 */
	protected function get_category_uri()
	{
		if ( is_null(self::$category_uri))
		{
			$uri_segments = self::$uri_segments;
			self::$category_uri = array_pop(array_slice($uri_segments, -1));
		}
		return self::$category_uri;
	}


	// ------------------------------------------------------------------------
	
	
// HERE : Add pagination : Number of page displayed on category view !!!!
// Could not be possible as the pagination tag don't know this Category tag attribute value (per_page)


	/**
	 * Categories tag
	 * Get the categories list from within the current page or globally
	 *
	 * @param	FTL_Binding
	 *
	 * @return	string
	 *
	 * @usage	<ion:categories all="true">
	 * 				...
	 * 			</ion:categories>
	 *
	 */
	public static function tag_categories(FTL_Binding $tag)
	{
		// Tag cache
		if (($str = self::get_cache($tag)) !== FALSE)
			return $str;

		// Local storage key
		$lsk = 'all';

		// Get all categories ?
		$page = $tag->get('page');
		if (is_null($page))
			$lsk = $page['name'];

		if ( ! isset(self::$categories[$lsk]))
			self::$categories[$lsk] = self::get_categories($tag, $page);

		// Tag expand
		$str = '';
		$count = count(self::$categories[$lsk]);
		$tag->set('count', $count);

		// Stop here if asked : Needed by aggregation tags
		if ($tag->getAttribute('loop') === FALSE)
			return $tag->expand();

		foreach(self::$categories[$lsk] as $key => $category)
		{
			$category['index'] = $key;
			$category['count'] = $count;

			$tag->set('category', $category);
			$tag->set('count', $count);
			$tag->set('index', $key);

			$str .= $tag->expand();
		}

		$output = self::wrap($tag, $str);
		
		// Tag cache
		self::set_cache($tag, $output);
		
		return $output;
	}

	/**
	 * On category tag
	 *
	 * @param	FTL_Binding
	 *
	 * @return	string
	 *
	 * @usage	<ion:categories>
	 * 				<ion:category current="true">
	 * 					<ion:title />
	 * 					<ion:name />
	 * 					<ion:url />
	 * 				<ion:category>
	 *			</ion:categories>
	 *
	 * 			or :
	 * 			<ion:categories>
	 * 				<ion:category:title current="true">
	 * 				<ion:category:name current="true">
	 * 				<ion:category:url current="true">
	 *			</ion:categories>
	 *
	 *
	 */
	public static function tag_category(FTL_Binding $tag)
	{
		$str = '';

		$limit_to_current = $tag->getAttribute('current');

		$category_uri = self::get_category_uri();

		$category = $tag->get('category');

		// Limit to the current category
		if (! is_null($limit_to_current))
		{
			if ($category['name'] == $category_uri)
			{
				$str = $tag->expand();
				return self::wrap($tag, $str);
			}
			return '';
		}
		else
		{
			$str = $tag->expand();
			return self::wrap($tag, $str);
		}
	}

}

TagManager_Category::init();


