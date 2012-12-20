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
			$active = ( ! empty($page['active_class'])) ? ' class="'.$page['active_class'].'" ' : '';
			
			$title = ($page['nav_title'] != '') ? $page['nav_title'] : $page['title'];
			
			// Adds the suffix if defined in /application/config.php
			if ( config_item('url_suffix') != '' ) $url .= config_item('url_suffix');

			$nav .= '<li' . $active . '><a ' . $active . 'href="' . (($page['has_url'] != 0) ? $page['absolute_url'] : '#') . '">'.$title. '</a></li>';
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
	function get_tree_navigation($items, $id = NULL, $class = NULL, $first_class = NULL, $last_class = NULL)
	{
		// HTML Attributes
		$id = ( ! is_null($id) ) ? ' id="' . $id . '" ' : '';
		$class = ( ! is_null($class) ) ? ' class="' . $class . '" ' : '';

		$tree = '<ul' . $id . $class . '>';

		foreach($items as $key => $page)
		{
			if ($key !== 'articles')
			{
				$class = array();
				if (( ! empty($page['active_class']))) $class[] = $page['active_class'];
				if ($key == 0 && ! is_null($first_class)) $class[] = $first_class;
				if ($key == (count($items) - 1) && ! is_null($last_class)) $class[] = $last_class;
				
				$class = ( ! empty($class)) ? ' class="'.implode(' ', $class).'"' : '';

				$title = ($page['nav_title'] != '') ? $page['nav_title'] : $page['title'];
				
				$tree .= '<li'.$class.'><a'.$class.' href="' . (($page['has_url'] != 0) ? $page['absolute_url'] : '#') . '">'.$title. '</a>';
		
				if (!empty($page['children']))
					 $tree .= get_tree_navigation($page['children']);
				
	
				if (!empty($page['articles']))
				{
					$tree .= '<ul' . $id . $class . '>';
					
					foreach($page['articles'] as $article)
					{
						$class = array();
						if (( ! empty($article['active_class']))) $class[] = $article['active_class'];
						if ($key == 0 && ! is_null($first_class)) $class[] = $first_class;
						if ($key == (count($page['articles']) - 1) && ! is_null($last_class)) $class[] = $last_class;
						
						$class = ( ! empty($class)) ? ' class="'.implode(' ', $class).'"' : '';

						$tree .= '<li'.$class.'><a'.$class.' href="' . $article['url'] . '">'.$article['title']. '</a></li>';
					}
					$tree .= '</ul>';
				}
				
				
				$tree .= '</li>';
			}
		}

		if ( ! empty($items['articles']))
		{
			foreach($items['articles'] as $article)
			{
				$class = array();
				if (( ! empty($article['active_class']))) $class[] = $article['active_class'];
				if ($key == 0 && ! is_null($first_class)) $class[] = $first_class;
				if ($key == (count($items['articles']) - 1) && ! is_null($last_class)) $class[] = $last_class;
						
				$class = ( ! empty($class)) ? ' class="'.implode(' ', $class).'"' : '';

				$tree .= '<li'.$class.'><a'.$class.' href="' . $article['url'] . '">'.$article['title']. '</a></li>';
			}
		}
		
		$tree .= '</ul>';

		return $tree;
	}
}

if( ! function_exists('get_language_navigation'))
{
	/**
	 * Returns a HTML UL formatted nested tree menu from a pages nested array
	 * Used by <ion:languages /> to print out the languages menu
	 *
	 * @deprecated
	 *
	 * @param	Array		Array of pages
	 * @param	Array		Array of container UL (first one) attributes. Can contains 'id' and 'class'
	 *
	 * @return	String		HTML UL formatted string
	 *
	 */
	function get_language_navigation($items)
	{
		$nav = '';

		foreach($items as $lang)
		{
			$active = ( ! empty($lang['active_class'])) ? ' class="'.$lang['active_class'].'" ' : '';
			
			$nav .= '<li' . $active . '><a ' . $active . 'href="' . $lang['url'] . '">' . $lang['name']. '</a></li>';
		}
		
		return $nav;
	}
}


if( ! function_exists('get_next_prev_page'))
{
<<<<<<< HEAD
	/**
	 * Returns the previous / next page enclosed in the given tag
	 *
	 * @deprecated	Use the <ion:page:next /> and <ion:page:prev /> tags
	 *
	 * @param $page
	 * @param $prefix
	 *
	 * @return string
	 */
	function get_next_prev_page($page, $prefix)
=======
	function get_next_prev_page($page, $prefix, $term, $class)
>>>>>>> 37ae275c480b6d3e0b24d07a92920ce8f2b8b12e
	{
		$prefix = (lang($prefix) != '#'.$prefix ) ? lang($prefix) : $prefix;
		
		$title = ($page['nav_title'] != '') ? $page['nav_title'] : $page['title'];
		$term = ($term != '' ) ? lang($term) : $title;

		$link = $prefix. '<a ' . $class . ' href="' . $page['absolute_url'] . '" title="' . $title .'">' . $term . '</a>';
		
		return $link;
	}
}


if( ! function_exists('get_next_prev_article'))
{
<<<<<<< HEAD
	/**
	 * Returns the previous / next article enclosed in the given tag
	 *
	 * @deprecated	Use the <ion:article:next /> and <ion:article:prev /> tags
	 *
	 */
	function get_next_prev_article($article, $prefix)
=======
	function get_next_prev_article($article, $prefix, $term, $class)
>>>>>>> 37ae275c480b6d3e0b24d07a92920ce8f2b8b12e
	{
		$term = ($term != '' ) ? lang($term) : $article['title'];
		$prefix = (lang($prefix) != '#'.$prefix ) ? lang($prefix) : $prefix;
		
		$link = $prefix. '<a ' . $class . ' href="' . $article['url'] . '" title="' . $article['title'] . '">' . $term. '</a>';
		
		return $link;
	}
}


/* End of file navigation_helper.php */
/* Location: .application/helpers/navigation_helper.php */