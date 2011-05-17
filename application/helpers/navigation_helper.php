<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 *
 */


// ------------------------------------------------------------------------


/**
 * Ionize Navigation Helpers
 *
 * @package		Ionize
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Ionize Dev Team
 *
 */


// ------------------------------------------------------------------------


/**
 * Returns a HTML UL formatted menu
 * Used by <ion:navigation level="x" /> to print out one level menu navigation
 *
 * @param	Array		Array of pages
 *
 * @return	String		HTML UL formatted string
 *
 */
if( ! function_exists('get_navigation'))
{
	function get_navigation($items)
	{
		$nav = '';
		
		foreach($items as $key => $page)
		{
			$active = ( ! empty($page['active_class'])) ? ' class="'.$page['active_class'].'"' : '';
			
			// Adds the suffix if defined in /application/config.php
			if ( config_item('url_suffix') != '' ) $url .= config_item('url_suffix');

			$nav .= '<li' . $active . '><a ' . $active . 'href="' . $page['absolute_url'] . '">'.$page['title']. '</a></li>';
		}
		
		return $nav;
	}
}

/**
 * Returns a HTML UL formatted nested tree menu from a pages nested array
 * Used by <ion:tree_navigation /> to print out a nested navigation
 *
 * @param	Array		Array of pages
 * @param	Array		Array of container UL (first one) attributes. Can contains 'id' and 'class'
 *
 * @return	String		HTML UL formatted string
 *
 */
if( ! function_exists('get_tree_navigation'))
{
	function get_tree_navigation($items, $lang_url=false, $id = NULL, $class = NULL)
	{
		// HTML Attributes
		$id = ( ! is_null($id) ) ? ' id="' . $id . '" ' : '';
		$class = ( ! is_null($class) ) ? ' class="' . $class . '" ' : '';

		$tree = '<ul' . $id . $class . '>';
		
		foreach($items as $key => $page)
		{
			$active = ( ! empty($page['active_class'])) ? ' class="'.$page['active_class'].'"' : '';

			$tree .= '<li><a'.$active.' href="' . $page['absolute_url'] . '">'.$page['title']. '</a>';
	
			if (!empty($page['children']))
				 $tree .= get_tree_navigation($page['children'], $lang_url);
			
			$tree .= '</li>';
			
		}
		
		$tree .= '</ul>';
		
		return $tree;
	}
}

/**
 * Returns a HTML UL formatted nested tree menu from a pages nested array
 * Used by <ion:languages /> to print out the languages menu
 *
 * @param	Array		Array of pages
 * @param	Array		Array of container UL (first one) attributes. Can contains 'id' and 'class'
 *
 * @return	String		HTML UL formatted string
 *
 */
if( ! function_exists('get_language_navigation'))
{
	function get_language_navigation($items)
	{
		$nav = '';
		
		foreach($items as $key => $lang)
		{
			$active = ( ! empty($lang['active_class'])) ? ' class="'.$lang['active_class'].'"' : '';
			
			$nav .= '<li' . $active . '><a ' . $active . 'href="' . $lang['url'] . '">' . $lang['name']. '</a></li>';
		}
		
		return $nav;
	}
}


/**
 * Returns the previous / next page enclosed in the given tag
 *
 */
if( ! function_exists('get_next_prev_page'))
{
	function get_next_prev_page($page, $prefix)
	{
		$prefix = (lang($prefix) != '#'.$prefix ) ? lang($prefix) : $prefix;
		
		$link = $prefix. '<a href="' . $page['absolute_url'] . '">' . $page['title']. '</a>';
		
		return $link;
	}
}


/* End of file navigation_helper.php */
/* Location: .application/helpers/navigation_helper.php */