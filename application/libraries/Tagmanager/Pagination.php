<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.9
 *
 */


class TagManager_Pagination extends TagManager
{
	/**
	 * Tags callbacks definition array
	 *
	 * @var array
	 */
	public static $tag_definitions = array
	(
		'pagination' =>			'tag_pagination',
	);


	// ------------------------------------------------------------------------


	/**
	 * Returns the pagination base URL
	 * Adds all special URI element to the URL they're found
	 *
	 * @param FTL_Binding
	 * @return string
	 *
	 */
	public static function get_pagination_base_url(FTL_Binding $tag)
	{
		$pagination_base_uri = '';

		$page = $tag->get('page');
		if (is_null($page)) $page = self::registry('page');

		$special_uri_array = self::get_special_uri_array();

		if ( ! is_null($special_uri_array))
		{
			foreach($special_uri_array as $code => $args)
			{
				if ($code != 'pagination')
				{
					$pagination_base_uri .= '/' . self::get_config_special_uri_segment($code);
					$pagination_base_uri .= '/' . implode('/', $args);
				}
			}
		}

		$pagination_base_uri = $page['absolute_url'] . $pagination_base_uri .'/';
		$pagination_base_uri .= self::get_config_special_uri_segment('pagination');

		return $pagination_base_uri;
	}


	// ------------------------------------------------------------------------


	/**
	 * Return the Pagination lib config array
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return array
	 *
	 */
	public static function get_pagination_config(FTL_Binding $tag)
	{
		$pagination_config = array();

		// CSS class / id
		$html_class = $tag->getAttribute('class');
		if ( ! is_null($html_class))
			$html_class = ' class="' . $html_class .'" ';

		$html_id = $tag->getAttribute('id');
		if ( ! is_null($html_id))
			$html_id = ' class="' . $html_id .'" ';

		$cf = Theme::get_theme_path().'config/pagination'.EXT;
		if ( ! is_file($cf))
			$cf = APPPATH.'config/pagination'.EXT;

		if (is_file($cf))
		{
			$config = array();
			require($cf);
			$pagination_config = $config['pagination'];
			unset($config);
		}

		// Pagination config from tag
		if ( ! is_null($ptag = $tag->getAttribute('full_tag')) )
		{
			$pagination_config['full_tag_open'] = 		'<' . $ptag . $html_id . $html_class . '>';
			$pagination_config['full_tag_close'] = 		'</' . $ptag . '>';
		}
		if ( ! is_null($ptag = $tag->getAttribute('first_tag')) )
		{
			$pagination_config['first_tag_open'] = 		'<' . $ptag . '>';
			$pagination_config['first_tag_close'] = 	'</' . $ptag . '>';
		}
		if ( ! is_null($ptag = $tag->getAttribute('last_tag')) )
		{
			$pagination_config['last_tag_open'] = 		'<' . $ptag . '>';
			$pagination_config['last_tag_close'] = 		'</' . $ptag . '>';
		}
		if ( ! is_null($ptag = $tag->getAttribute('cur_tag')) )
		{
			$pagination_config['cur_tag_open'] = 		'<' . $ptag . '>';
			$pagination_config['cur_tag_close'] = 		'</' . $ptag . '>';
		}
		if ( ! is_null($ptag = $tag->getAttribute('next_tag'))  )
		{
			$pagination_config['next_tag_open'] = 		'<' . $ptag . '>';
			$pagination_config['next_tag_close'] = 		'</' . $ptag . '>';
		}
		if ( ! is_null($ptag = $tag->getAttribute('prev_tag')) )
		{
			$pagination_config['prev_tag_open'] = 		'<' . $ptag . '>';
			$pagination_config['prev_tag_close'] = 		'</' . $ptag . '>';
		}
		if ( ! is_null($ptag = $tag->getAttribute('num_tag')) )
		{
			$pagination_config['num_tag_open'] = 		'<' . $ptag . '>';
			$pagination_config['num_tag_close'] = 		'</' . $ptag . '>';
		}

		return $pagination_config;
	}


	// ------------------------------------------------------------------------


	/**
	 * Pagination tag
	 *
	 * Main class name, id, open tag, close tag, every options from cI in fact !
	 *
	 * @configuration
	 * 		/themes/<my_theme>/config/pagination.php
	 * 		Set the open / close HTML tags for each tag
	 *
	 * 		/themes/<my_theme>/language/xx/pagination_lang.php
	 * 		Set the translations items :
	 * 		- first_link
	 * 		- last_link
	 * 		- prev_link
	 * 		- next_link
	 *
	 */
	public static function tag_pagination(FTL_Binding $tag)
	{
		if ($tag->getParent()->get('__loop__') === FALSE)
		{
			return '';
		}

		// Avoid loop in this tag
		$tag->getParent()->set('__loop__', FALSE);

		// Tag cache
		if (($str = self::get_cache($tag)) !== FALSE)
			return $str;

		// Current page : 1. Asked page, 2. Down to current
		$page = $tag->get('page');
			if (is_null($page)) $page = self::registry('page');

		// Number of displayed articles : tag attribute has priority 1.
		$nb_to_display = $tag->getAttribute('pagination');
		if (is_null($nb_to_display))
			$nb_to_display = $page['pagination'];

		// Load CI Pagination Lib
		isset(self::$ci->pagination) OR self::$ci->load->library('pagination');

		// Current pagination page
		$args = self::get_special_uri_array('pagination');
		$cur_page = isset($args[0]) ? $args[0] : NULL;

		// Pagination tag config init
		$pagination_config = array_merge
		(
			self::get_pagination_config($tag),
			array (
				'base_url' => self::get_pagination_base_url($tag),
				'per_page' => $nb_to_display,
				'total_rows' => $tag->get('nb_total_items'),	// Got from parent tag data array
				'num_links' => 3,
				'cur_page' => $cur_page,
				'first_link' => lang('first_link'),				// "First" text : see /theme/your_theme/language/xx/pagination_lang.php
				'last_link' => lang('last_link'),				// "Last" text
				'next_link' => lang('next_link'),
				'prev_link' => lang('prev_link')
			)
		);

		// Pagination initialization
		self::$ci->pagination->initialize($pagination_config);

		// Create the links
		$page['pagination_links'] = self::$ci->pagination->create_links();

		// Tag cache
		self::set_cache($tag, $page['pagination_links']);

		return $page['pagination_links'];
	}
}
