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
 * Archive TagManager 
 *
 */
class TagManager_Archive extends TagManager
{
	public static $tag_definitions = array
	(
		'archives' =>				'tag_archives',
		'archive' =>				'tag_archive',
		'archive:period' => 		'tag_simple_value',
		'archives:active_class' => 	'tag_simple_value',
	);




	/**
	 * Get the archives tag
	 *
	 *
	 */
	public static function tag_archives($tag)
	{
		// Tag cache
		if (($str = self::get_cache($tag)) !== FALSE)
			return $str;
		
		// Page field to use as URL. For compat.
		$page_url = (config_item('url_mode') == 'short') ? 'url' : 'path';

		
		// Period format
		$format = (isset($tag->attr['format']) ) ? $tag->attr['format'] : 'F';

		// Attribute : active class
		$active_class = (isset($tag->attr['active_class']) ) ? $tag->attr['active_class'] : 'active';

		// filter
		$filter = (isset($tag->attr['filter']) ) ? $tag->attr['filter'] : FALSE;

		// month
		$with_month = (isset($tag->attr['with_month']) ) ? TRUE : FALSE;

		// order
		$order = (isset($tag->attr['order']) && $tag->attr['order'] == 'ASC' ) ? 'period ASC' : 'period DESC';

		// Current archive
		$arc_segment_pos = TagManager_Page::get_special_uri_segment();
		$current_archive = isset(self::$uri_segments[$arc_segment_pos + 1]) ? self::$uri_segments[$arc_segment_pos + 1] : '' ;
		$current_archive .= isset(self::$uri_segments[$arc_segment_pos + 2]) ? self::$uri_segments[$arc_segment_pos + 2] : '' ;

		// Get the archives infos		
		$archives = self::$ci->article_model->get_archives_list
		(
			array('id_page' => $tag->locals->_page['id_page']), 
			Settings::get_lang(),
			$filter,
			$with_month,
			$order
		);


		// Translated period array
		$month_formats = array('D', 'l', 'F', 'M');

		// Flip the URI config array to have the category index first
		$uri_config = self::$ci->config->item('special_uri');
		$uri_config = array_flip($uri_config);

		foreach ($archives as &$row)
		{
			$year = 	substr($row['period'],0,4);
			$month = 	substr($row['period'],4);
			
			if ($month != '')
			{
				$month = (strlen($month) == 1) ? '0'.$month : $month;

				$timestamp = mktime(0, 0, 0, $month, 1, $year);
    
				// Get date in the wished format
				$period = (String) date($format, $timestamp);

				if (in_array($format, $month_formats))
					$period = lang(strtolower($period));

				$row['period'] = $period . ' ' . $year;
				$row['url'] = base_url() . $tag->locals->_page[$page_url] . '/' . $uri_config['archives'] . '/' . $year . '/' . $month ;
				$row['lang_url'] = base_url() . Settings::get_lang() . '/' . $tag->locals->_page[$page_url] . '/' .  $uri_config['archives'] . '/' . $year . '/' . $month ;
				$row['active_class'] = ($year.$month == $current_archive) ? $active_class : '';
			}
			else
			{
				$row['period'] = $year;
				$row['url'] = base_url() . $tag->locals->_page[$page_url] . '/' . $uri_config['archives'] . '/' . $year;
				$row['lang_url'] = base_url() . Settings::get_lang() . '/' . $tag->locals->_page[$page_url] . '/' .  $uri_config['archives'] . '/' . $year;
				$row['active_class'] = ($year == $current_archive) ? $active_class : '';
			}
		}


		// Tag expand
		$str = '';

		foreach($archives as $archive)
		{
			$tag->locals->archive = $archive;
			$str .= $tag->expand();
			
		}

		// Tag cache
		self::set_cache($tag, $str);
		
		return $str;
	}


	// ------------------------------------------------------------------------


	public static function tag_archive(FTL_Binding $tag)
	{
		// Current archive
		$arc_segment = TagManager_Page::get_special_uri();
		$arc_segment_pos = TagManager_Page::get_special_uri_segment();

		$year = isset(self::$uri_segments[$arc_segment_pos + 1]) ? self::$uri_segments[$arc_segment_pos + 1] : '' ;
		$month = isset(self::$uri_segments[$arc_segment_pos + 2]) ? self::$uri_segments[$arc_segment_pos + 2] : '' ;

		$uri_config = self::$ci->config->item('special_uri');
		$uri_config = array_flip($uri_config);
		$archive_uri = $uri_config['archives'];

		if ($arc_segment == $archive_uri)
		{
			$timestamp = '';
			if ($year != '' && $month !='')
				$timestamp = mktime(0, 0, 0, $month, 1, $year);
			else if ($year != '')
				$timestamp = mktime(0, 0, 0, 0, 1, $year);

			if ($timestamp != '')
			{
				$date = (string) date('Y-m-d H:i:s', $timestamp);

				return self::format_date($tag, $date);
			}
		}

		return '';
	}


	// ------------------------------------------------------------------------


	/** 
	 * Deprecated, will be deleted in the next version 
	 * Use tag_archives_url
	 * @deprecated
	 */
	public static function tag_archives_lang_url($tag)
	{
		return ($tag->locals->archive['lang_url'] != '' ) ? $tag->locals->archive['lang_url'] : '' ;
	}
	
	
	public static function tag_archives_period($tag) { return ($tag->locals->archive['period'] != '' ) ? $tag->locals->archive['period'] : '' ; }
	public static function tag_archives_nb($tag) { return ($tag->locals->archive['nb'] != '' ) ? $tag->locals->archive['nb'] : '' ; }
	public static function tag_archives_active_class($tag) { return ($tag->locals->archive['active_class'] != '' ) ? $tag->locals->archive['active_class'] : '' ; }

}



