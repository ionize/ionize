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
class TagManager_Tag extends TagManager
{
	/**
	 * Tags local storage
	 * @var array
	 */
	protected static $tags = array();


	/**
	 * Tags callbacks definition array
	 *
	 * @var array
	 */
	public static $tag_definitions = array
	(
		'tags' => 				'tag_tags',
		'tag' =>				'tag_expand',
		'tag:nb' =>				'tag_simple_value',
		'tag:is_active' => 		'tag_is_active',
		'tag:active_class' => 	'tag_simple_value',
		'tag:current' =>		'tag_tag_current',
	);



	// ------------------------------------------------------------------------


	/**
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_tags(FTL_Binding $tag)
	{
		// Get tags from element or DB
		$tags = self::get_tags($tag);

		$str = '';
		$count = count($tags);
		$tag->set('tags', $tags);
		$tag->set('count', $count);

		// Child tags loop and expand
		foreach($tags as $key => $t)
		{
			$t['index'] = $key;

			$tag->set('tag', $t);
			$tag->set('index', $key);
			$tag->set('nb_articles', $t['nb']);

			$str .= $tag->expand();
		}

		// $output = self::wrap($tag, $str);
		$output =  $str;

		// Tag cache
		self::set_cache($tag, $output);

		return $output;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the current URL asked tag
	 *
	 * @param 	FTL_Binding $tag
	 *
	 * @return 	string
	 *
	 */
	public static function tag_tag_current(FTL_Binding $tag)
	{
		// Asked category
		$url_tag_name = self::get_asked_tag_uri();

		// Category detail
		if ( ! is_null($url_tag_name))
		{
			// Categories model
			self::$ci->load->model('tag_model');

			$t = self::$ci->tag_model->get(
				array('tag_name' => urldecode($url_tag_name))
			);

			if ( ! empty($t))
				$t['title'] = $t['tag_name'];

			$tag->set('current', $t);
		}
		else
		{
			$tag->set('current', array());
		}

		return $tag->expand();
	}


	// ------------------------------------------------------------------------


	/**
	 * @param FTL_Binding $tag
	 *
	 * @return mixed
	 *
	 */
	public static function get_tags(FTL_Binding $tag)
	{
		// Categories model
		self::$ci->load->model('tag_model');

		// Current page
		$page = $tag->get('page');

		// Local storage key
		$lsk = '__all__';

		// Get the local cache data
		$element_name = $tag->getParentName();
		$element = $tag->get($element_name);
		if ( ! is_null($element)) $lsk = '__' . $element_name . '__' . $element['name'];

		// Set the local cache data
		if ( ! isset(self::$tags[$lsk]))
		{
			// CSS class to use for the current tag
			$active_class = $tag->getAttribute('active_class', 'active');

			// Asked tag
			$asked_tag_name = self::get_asked_tag_uri();

			// Check if the element has one tag array (eg. for Articles)
			if (isset($element['tags']))
			{
				$tags = $element['tags'];

				// Fix the 'nb' key (nb_articles using this category)
				foreach($tags as $key=>$category)
					$tags[$key]['nb'] = '1';
			}
			// @TODO: Not implemented
			// If no element categories, get page used tags
			else
			{
				if($element_name == 'page')
					$id_page = ! is_null($page) ? $page['id_page'] : NULL;
				else
					$id_page = NULL;

				$tags = self::$ci->tag_model->get_page_articles_list($id_page);
			}

			$page_url = ! is_null($page) ? trim($page['absolute_url'], '/') .'/' : Pages::get_home_page_url();

			$tag_uri_segment = self::get_config_special_uri_segment('tag');

			// Add the URL to the tag to each tag row
			// Also add the active class
			foreach($tags as $key => $t)
			{
				$tags[$key]['url'] = $page_url . $tag_uri_segment . '/' . $t['tag_name'];
				$tags[$key]['lang_url'] = $page_url . $tag_uri_segment . '/' . $t['tag_name'];

				// Active tag ?
				$tags[$key]['active_class'] = ($t['tag_name'] == $asked_tag_name) ? $active_class : '';
				$tags[$key]['is_active'] = 	($t['tag_name'] == $asked_tag_name) ? TRUE : FALSE;
			}

			self::$tags[$lsk] = array_values($tags);
		}

		return self::$tags[$lsk];
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the current tag URI
	 *
	 * @return string
	 *
	 */
	public static function get_asked_tag_uri()
	{
		$tag_array = self::get_special_uri_array('tag');
		if ( ! empty($tag_array[0]))
			return $tag_array[0];

		return NULL;
	}

}