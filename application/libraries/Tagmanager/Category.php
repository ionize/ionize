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
 * Category TagManager 
 *
 */
class TagManager_Category extends TagManager
{
	/**
	 * Categories local storage
	 * @var array
	 */
	protected static $categories = array();

	/**
	 * Current visited category URI
	 * @var null
	 */
	protected static $category_uri = NULL;

	/**
	 * Tags callbacks definition array
	 *
	 * @var array
	 */
	public static $tag_definitions = array
	(
		'categories' => 				'tag_categories',
		'category' =>					'tag_expand',
		'category:is_active' => 		'tag_is_active',
		'category:active_class' => 		'tag_simple_value',
		'category:current' =>			'tag_category_current',
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

			// Check if the element has one category array (eg. for Articles)
			if (isset($element['categories']))
			{
				$categories = $element['categories'];

				// Fix the 'nb' key (nb_articles using this category)
				foreach($categories as $key=>$category)
					$categories[$key]['nb'] = '1';
			}
			// If no element categories, get page used categories
			else
			{
				if($element_name == 'page')
					$id_page = ! is_null($page) ? $page['id_page'] : NULL;
				else
					$id_page = NULL;

				$categories = self::$ci->category_model->get_categories_list(
					$id_page,
					Settings::get_lang()
				);
			}

			$page_url = ! is_null($page) ? trim($page['absolute_url'], '/') .'/' : Pages::get_home_page_url();

			$category_uri_segment = self::get_config_special_uri_segment('category');

			// Add the URL to the category to each category row
			// Also add the active class
			foreach($categories as $key => $category)
			{
				$categories[$key]['url'] = 			$page_url . $category_uri_segment . '/' . $category['name'];
				$categories[$key]['lang_url'] = 	$page_url . $category_uri_segment . '/' . $category['name'];

				// Active category ?
				$categories[$key]['active_class'] = ($category['name'] == $asked_category_name) ? $active_class : '';
				$categories[$key]['is_active'] = ! empty($categories[$key]['active_class']) ? TRUE : FALSE;
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
	 *				...
	 * 			</ion:categories>
	 *
	 */
	public static function tag_categories(FTL_Binding $tag)
	{
		// Tag cache
		if (($str = self::get_cache($tag)) !== FALSE)
			return $str;

		$categories = self::get_categories($tag);

		$str = '';
		$count = count($categories);
		$tag->set('categories', $categories);
		$tag->set('count', $count);

		// Child tags loop and expand
		foreach($categories as $key => $category)
		{
			$category['index'] = $key;
			$category['nb_articles'] = $category['nb'];

			$tag->set('category', $category);
			$tag->set('nb_articles', $category['nb']);
			$tag->set('index', $key);

			$str .= $tag->expand();
		}

		$output = self::wrap($tag, $str);

		// Tag cache
		self::set_cache($tag, $output);

		return $output;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the current URL asked category
	 *
	 * @param 	FTL_Binding $tag
	 *
	 * @return 	string
	 *
	 */
	public static function tag_category_current(FTL_Binding $tag)
	{
		// Asked category
		$url_category_name = self::get_asked_category_uri();

		// Category detail
		if ( ! is_null($url_category_name))
		{
			// Categories model
			self::$ci->load->model('category_model');

			$category = self::$ci->category_model->get
			(
				array('name' => $url_category_name),
				Settings::get_lang()
			);
			$tag->set('current', $category);
		}
		else
		{
			$tag->set('current', array());
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
	public static function tag_article_categories(FTL_Binding $tag)
	{
		$data = array();

		$categories = self::get_categories($tag);

		// HTML Separator of each category
		$separator = $tag->getAttribute('separator', ' | ');

		// Make a link from each category or not. Default : FALSE
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
	 */


}
