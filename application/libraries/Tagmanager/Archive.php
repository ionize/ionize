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
 * Archive TagManager 
 *
 */
class TagManager_Archive extends TagManager
{
	public static $tag_definitions = array
	(
		'archives' =>				'tag_archives',
		'archive' =>				'tag_expand',
		'archive:is_active' => 		'tag_is_active',
		'archive:period' => 		'tag_simple_value',
		'archive:active_class' => 	'tag_simple_value',
		// TODO :
		// 'archive:current' => 		'tag_archive_current',
	);


	// ------------------------------------------------------------------------


	public static function get_archives(FTL_Binding $tag)
	{
		// Categories model
		self::$ci->load->model('article_model');

		// Page
		$page = $tag->get('page');
		if (is_null($page))
			$page = self::registry('page');

		// Period format. see : http://php.net/manual/fr/function.date.php
		$format = $tag->getAttribute('format', 'F');

		// Attribute : active class
		$active_class = $tag->getAttribute('active_class', 'active');

		// filter
		$filter = $tag->getAttribute('filter', FALSE);

        if( $filter != FALSE )
            $filter = self::process_filter($filter);

		// month
		$with_month = $tag->getAttribute('month');

		// order
		$order = $tag->getAttribute('order');
		$order = $order == 'ASC' ? 'period ASC' : 'period DESC';

		// Archive string : 'yyyy' or 'yyyy.mm'. Used for CSS active class
		$_archive_string = '';

		// Archive URI args
		$args = self::get_special_uri_array('archives');
		if ( ! empty($args))
		{
			$_archive_string = isset($args[0]) ? $args[0] : '';
			$_archive_string .= isset($args[1]) ? '.'.$args[1] : '';
		}

		// Archives URI segment, as set in the config file
		$archives_uri_segment = self::get_config_special_uri_segment('archives');

		// Get the archives
		$archives = self::$ci->article_model->get_archives_list
		(
			array('id_page' => $page['id_page']),
			Settings::get_lang(),
			$filter,
			$with_month,
			$order
		);

		// Translated period array
		$month_formats = array('D', 'l', 'F', 'M');

		$page_url = ! is_null($page) ? $page['absolute_url'] .'/' : Pages::get_home_page_url();

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

				// Translate the period month
				if (in_array($format, $month_formats))
					$period = lang(strtolower($period));

				$row['period'] =	$period . ' ' . $year;
				$row['url'] =		$page_url . $archives_uri_segment . '/' . $year . '/' . $month ;
				$row['active_class'] = ($year.'.'.$month == $_archive_string) ? $active_class : '';
			}
			else
			{
				$row['period'] =	$year;
				$row['url'] =		$page_url . $archives_uri_segment . '/' . $year;
				$row['active_class'] = ($year == $_archive_string) ? $active_class : '';
			}

			$row['is_active'] = ! empty($row['active_class']) ? TRUE : FALSE;
		}

		return $archives;
	}


	// ------------------------------------------------------------------------


	/**
	 * Archives tag
	 * Displays the links to each archive page
	 *
	 * @param	FTL_Binding
	 * @return 	string
	 *
	 * @usage	<ion:archives [month='true'] />
	 *
	 */
	public static function tag_archives(FTL_Binding $tag)
	{
		// Tag cache
		if (($str = self::get_cache($tag)) !== FALSE)
			return $str;

		$archives = self::get_archives($tag);

		// Tag expand
		$str = '';
		$count = count($archives);
		$tag->set('count', $count);

		// Child tags loop and expand
		foreach($archives as $key => $archive)
		{
			// Nb articles in this archive page
			$archive['nb_articles'] = $archive['nb'];
			$archive['index'] = $key;

			$tag->set('archive', $archive);
			$tag->set('nb_articles', $archive['nb']);
			$tag->set('index', $key);

			$str .= $tag->expand();
		}

		$output = self::wrap($tag, $str);

		// Tag cache
		self::set_cache($tag, $output);

		return $output;
	}
}
