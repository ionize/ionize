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
		'category' =>					'tag_expand',
		'category:is_active' => 		'tag_is_active',
		'category:active_class' => 		'tag_simple_value',
	);


	// ------------------------------------------------------------------------


	/**
	 * Returns the categories
	 *
	 * @param	FTL_Binding
	 * @param	array/null
	 *
	 * @return	array
	 *
	 */
	public static function get_categories(FTL_Binding $tag)
	{
		// Categories model
		self::$ci->load->model('category_model');

		// Current page
		$page = $tag->get('page');

		// Local storage key
		$lsk = '__all__';

		// Get the local cache data
		$element_name = $tag->getParentName();
		$element = $tag->get($element_name);
		if ( ! is_null($element)) $lsk = '__' . $element_name . '__' . $element['name'];

		// Set the local cache data
		if ( ! isset(self::$categories[$lsk]))
		{
			// CSS class to use for the current category
			$active_class = $tag->getAttribute('active_class', 'active');

			// Asked category
			$asked_category_name = self::get_asked_category_uri();

			// Check if the element has one category array
			if (isset($element['categories']))
			{
				$categories = $element['categories'];
			}
			// Get categories from this page articles
			else if ( ! is_null($page))
			{
				$categories = self::$ci->category_model->get_categories_from_pages(
					$page['id_page'],
					Settings::get_lang()
				);
			}
			// No page set : The URL of each category will look like base URL of the website.
			else
			{
				$categories = self::$ci->category_model->get_lang_list(
					array('order_by' => 'ordering ASC'),
					Settings::get_lang()
				);
			}

			$page_url = ! is_null($page) ? $page['absolute_url'] .'/' : Pages::get_home_page_url();

			$category_uri_segment = self::get_config_special_uri_segment('category');

			// Add the URL to the category to each category row
			// Also add the active class
			foreach($categories as $key => $category)
			{
				$categories[$key]['url'] = 			$page_url . $category_uri_segment . '/' . $category['name'];
				$categories[$key]['lang_url'] = 	$page_url . $category_uri_segment . '/' . $category['name'];

				// Active category ?
				$categories[$key]['active_class'] = ($category['name'] == $asked_category_name) ? $active_class : '';
				$categories[$key]['is_active'] = 	($category['name'] == $asked_category_name) ? TRUE : FALSE;
			}

			self::$categories[$lsk] = array_values($categories);
		}

		return self::$categories[$lsk];
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the current category URI
	 *
	 * @return string
	 *
	 */
	public static function get_asked_category_uri()
	{
		$category_array = self::get_special_uri_array('category');
		if ( ! empty($category_array[0]))
			return $category_array[0];

		return NULL;
	}


	// ------------------------------------------------------------------------


	/**
	 * Categories tag
	 * Get the categories list from within the current page or globally
	 *
	 * @param	FTL_Binding
	 *
	 * @return	string
	 *
	 * @usage	<ion:categories>
	 *
	 * 			</ion:categories>
	 *
	 */
	public static function tag_categories(FTL_Binding $tag)
	{
		// Tag cache
		if (($str = self::get_cache($tag)) !== FALSE)
			return $str;

		$categories = self::get_categories($tag);

		// Tag expand
		$str = '';
		$count = count($categories);
		$tag->set('count', $count);

		// Stop here if asked : Needed by aggregation tags
		if ($tag->getAttribute('loop') === FALSE)
			return $tag->expand();

		// Child tags loop and expand
		foreach($categories as $key => $category)
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
}
