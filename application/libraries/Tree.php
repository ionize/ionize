<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.7
 */

// ------------------------------------------------------------------------

/**
 * Ionize, creative CMS Structure Library
 *
 * Builds array of pages + articles.
 *
 * @package		Ionize
 * @subpackage	Librairies
 * @category	Librairies
 * @author		Ionize Dev Team
 */

// ------------------------------------------------------------------------


/**
 * Provides shared function to work with pages structure
 *
 */
 
class Tree{

	var $setting = array();
	
	// Filter used by filtering function
	var $filter;


	/**
	 * Page filter function
	 * used by get_nested_structure() to filter childs pages for one page
	 *
	 */
	function page_parent_filter($row)
	{
		return $row['id_parent'] == $this->filter;
	}
	

	// ------------------------------------------------------------------------

	
	/**
	 * Article filter function
	 * used by get_nested_structure() to filter articles linked to current page
	 *
	 */
	function articles_parent_filter($row)
	{
		return $row['id_page'] == $this->filter;
	}

	
	// ------------------------------------------------------------------------
	
	
	/** 
	 * Get the nested pages array from DB result_array
	 * Recursive method
	 *
	 * @param	array	By ref. Array of pages
	 * @param	array	By ref. Array to feed
	 * @param	int		Parent page ID
	 * @param	int		Level to start
	 * @param	int		Level to end
	 * @param	array	Optional. Articles array
	 *
	 */
	function get_nested_structure(&$data, &$arr, $parent, $startDepth, $maxDepth, $articles=FALSE)
	{
		if ($maxDepth-- == 0) return;
		$index = 0;
		$startDepth++;
		
		if (is_array($data))
		{
			// $children = array_values(array_filter($data, create_function('$row','return $row["id_parent"] == "'. $parent .'";')));
			$children = array();
			foreach($data as $d)
			{
				if ($d['id_parent'] == $parent)
					$children[] = $d;
			}
			
			foreach ($children as $child)
			{
				$arr[$index] = $child;

				if ($articles)
				{
					// $arr[$index]['articles'] = array_values(array_filter($articles, create_function('$row','return $row["id_page"] == "'. $child['id_page'] .'";')));
					foreach($articles as $article)
					{
						if ($article['id_page'] == $child['id_page'])
							$arr[$index]['articles'][] = $article;
					}
				}
				
				Structure::get_nested_structure($data, $arr[$index]['children'], $child['id_page'], $startDepth, $maxDepth, $articles);
				$index++;
			}
		}
	} 
	
	
	// ------------------------------------------------------------------------
	
	
	/** 
	 *	Return the parent tree array
	 *  Childs are indented
	 *  Used by page/admin to show the parent select dropdown object
	 *  Array (
	 *			[id_page => name]
	 *		  )
	 *
	 */
	function get_parent_select($data, $id_page=false)
	{
		// Pages array
		$arr = array();
		
		$this->get_nested_structure($data, $arr, 0, 0, -1);
		
		return $this->_get_parent_select_items($arr, $id_page);
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns a flat array from nested pages.
	 * Used to fill a CI select dropdown box.
	 * called by Structure::get_parent_select()
	 *
	 * Admin front-end
	 *
	 * @param	array	Array of pages
	 * @param	int		ID of current edited page. This ID will not be included in the returned array
	 *
	 * @return	array	Simple array of pages
	 *
	 */
	function _get_parent_select_items($items, $id_page=false)
	{
		$tree = array();
		
		foreach($items as $key => $page)
		{
			$title = ($page['title'] != '') ? $page['title'] : $page['name'];
			
			if ($page['id_page'] != $id_page)
			{
				$space = "&#160;".str_repeat("&#160;&#160;", $page['level']);
	
				$space .= ($page['level'] > 0) ? "&#187;&#160;" : '';
	
				$tree[$page['id_page']] = $space.$title;
	
				if (!empty($page['children']))
					 $tree += $this->_get_parent_select_items($page['children'], $id_page);
			}
		}
		
		return $tree;
	}


	// ------------------------------------------------------------------------

	
	function get_tree_navigation($data, $id_parent, $startDepth=0, $maxDepth=-1, $articles=FALSE)
	{
		// Pages array
		$arr = array();
		
		// Return array
		$select_data = array();
		
		Structure::get_nested_structure($data, $arr, $id_parent, $startDepth, $maxDepth, $articles);
		
		return $arr;
	
	}


	// ------------------------------------------------------------------------


	/**
	 * Gets the array of active pages
	 * @param	mixed	ID of the page
	 *
	 *
	 */
	function get_active_pages($pages, $id_page)
	{
		$active_pages = array();
		
		// Page data
		// $page = array_values(array_filter($pages, create_function('$row','return $row["id_page"] == "'. $id_page .'";') ));
		$page = array();
		foreach($pages as $p)
		{
			if ($p['id_page'] == $id_page)
				$page = $p;
		}

		if ( ! empty($page))
		{
//			$page = $page[0];

			if ($page['id_parent'] != '0')
			{
				$active_pages += self::get_active_pages($pages, $page['id_parent']);
			}
			
			$active_pages[] = $id_page;
		}
		
		return $active_pages;
	}
}


/* End of file Tree.php */
/* Location: ./application/libraries/Tree.php */
