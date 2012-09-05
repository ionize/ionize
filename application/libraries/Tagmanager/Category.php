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
	protected static $categories = FALSE;

	public static $tag_definitions = array
	(
		'category' =>					'tag_category',
		'categories' => 				'tag_categories',
		'categories:url' => 			'tag_category_url',
		'categories:active_class' => 	'tag_category_active_class',
		'categories:title' => 			'tag_category_title',
		'categories:subtitle' => 		'tag_category_subtitle',	
		'categories:name' =>			'tag_category_name',
	);

	public static function init()
	{
		$uri = preg_replace("|/*(.+?)/*$|", "\\1", self::$ci->uri->uri_string);
		self::$uri_segments = explode('/', $uri);
	}


	/**
	 * Returns the categories regarding the given page
	 *
	 */
	function get_categories($tag, $page)
	{
		// Categories model
		isset(self::$ci->category_model) OR self::$ci->load->model('category_model');
		
		$page_url = (config_item('url_mode') == 'short') ? 'url' : 'path';

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
			$categories[$key]['url'] = 			base_url() . $page[$page_url] . '/' . $uri_config['category'] . '/' . $category['name'];
			$categories[$key]['lang_url'] = 	base_url() . Settings::get_lang() . '/' . $page[$page_url] . '/' . $uri_config['category'] . '/' . $category['name'];
			$categories[$key]['active_class'] = ($category['name'] == $category_uri) ? $active_class : '';
		}
	
		// Reorder array keys
		return array_values($categories);
	}
	





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

		$from_page = ( ! empty($tag->attr['from'])) ? TagManager_Page::get_page_by_id($tag->attr['from']) : self::$context->globals->_page;
		
		$uri_segments = self::$uri_segments;
		$category_uri = array_pop(array_slice($uri_segments, -1));

		// Categorie prefix in the returned string. Exemple "Category "
		$category_value = NULL;

		// Store categories in Globals, so no multiple time retrieve
		if (self::$categories === FALSE)
		{
			self::$categories = self::get_categories($tag, $from_page);
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

		$from_page = ( ! empty($tag->attr['from'])) ? TagManager_Page::get_page_by_id($tag->attr['from']) : self::$context->globals->_page;
	
		// Store of all categories
		if (self::$categories === FALSE)
		{
			self::$categories = self::get_categories($tag, $from_page);
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

	public static function tag_category_name($tag) { return self::wrap($tag, $tag->locals->category['name']); }


}

TagManager_Category::init();


