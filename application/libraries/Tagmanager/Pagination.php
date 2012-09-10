<?php
	/**
	 * Ionize
	 *
	 * @package		Ionize
	 * @author		Ionize Dev Team
	 * @license		http://ionizecms.com/doc-license
	 * @link		http://ionizecms.com
	 * @since		Version 0.9.9
	 *
	 */


class TagManager_Pagination extends TagManager
{
	/**
	 * Pagination URI
	 *
	 */
	protected static $pagination_uri = NULL;


	public static $tag_definitions = array
	(
		'pagination' =>			'tag_pagination',
	);


	// ------------------------------------------------------------------------


	/**
	 * Returns the current category URI
	 *
	 * @return string
	 *
	 */
	public static function get_pagination_uri()
	{
		if ( is_null(self::$pagination_uri))
		{
			$uri_segments = self::$uri_segments;
			self::$pagination_uri = array_pop(array_slice($uri_segments, -1));
		}
		return self::$pagination_uri;
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
		// Tag cache
		if (($str = self::get_cache($tag)) !== FALSE)
			return $str;

		// Current considered page
		$page = $tag->get('page');

		// Pagination configuration array
		$pagination_config = array();

		// Number of displayed articles : tag attribute has priority 1.
		$nb_to_display = $tag->getAttribute('pagination');
		if (is_null($nb_to_display))
			$nb_to_display = $page['pagination'];

		// Get the special URI config array (see /config/ionize.php)
		$uri_config = array_flip(self::$ci->config->item('special_uri'));
		$pagination_uri = $uri_config['pagination'];

		// CSS class / id
		$html_class = $tag->getAttribute('class');
		if ( ! is_null($html_class))
			$html_class = ' class="' . $html_class .'" ';

		$html_id = $tag->getAttribute('id');
		if ( ! is_null($html_id))
			$html_id = ' class="' . $html_id .'" ';

		// Load CI Pagination Lib
		isset(self::$ci->pagination) OR self::$ci->load->library('pagination');

		// Pagination theme config
		$cf = Theme::get_theme_path().'config/pagination'.EXT;
		if ( ! is_file($cf))
			$cf = APPPATH.'config/pagination'.EXT;

		if (is_file($cf))
		{
			$config = array();
			require_once($cf);
			$pagination_config = $config['pagination'];
			unset($config);
		}

		// Pagination config from tag
		if ( ! is_null($ptag = $tag->getAttribute('full_tag')) ) {
			$pagination_config['full_tag_open'] = 		'<' . $ptag . $html_id . $html_class . '>';
			$pagination_config['full_tag_close'] = 		'</' . $ptag . '>';
		}
		if ( ! is_null($ptag = $tag->getAttribute('first_tag')) ) {
			$pagination_config['first_tag_open'] = 		'<' . $ptag . '>';
			$pagination_config['first_tag_close'] = 	'</' . $ptag . '>';
		}
		if ( ! is_null($ptag = $tag->getAttribute('last_tag')) ) {
			$pagination_config['last_tag_open'] = 		'<' . $ptag . '>';
			$pagination_config['last_tag_close'] = 		'</' . $ptag . '>';
		}
		if ( ! is_null($ptag = $tag->getAttribute('cur_tag')) ) {
			$pagination_config['cur_tag_open'] = 		'<' . $ptag . '>';
			$pagination_config['cur_tag_close'] = 		'</' . $ptag . '>';
		}
		if ( ! is_null($ptag = $tag->getAttribute('next_tag'))  ) {
			$pagination_config['next_tag_open'] = 		'<' . $ptag . '>';
			$pagination_config['next_tag_close'] = 		'</' . $ptag . '>';
		}
		if ( ! is_null($ptag = $tag->getAttribute('prev_tag')) ) {
			$pagination_config['prev_tag_open'] = 		'<' . $ptag . '>';
			$pagination_config['prev_tag_close'] = 		'</' . $ptag . '>';
		}
		if ( ! is_null($ptag = $tag->getAttribute('num_tag')) ) {
			$pagination_config['num_tag_open'] = 		'<' . $ptag . '>';
			$pagination_config['num_tag_close'] = 		'</' . $ptag . '>';
		}

		// Current page
		$uri_segments = self::$uri_segments;
		$cur_page = (in_array($pagination_uri, self::$uri_segments)) ? array_pop(array_slice($uri_segments, -1)) : 1;

		// Pagination tag config init
		$pagination_config = array_merge(
			$pagination_config,
			array (
				'base_url' => $page['absolute_url'] . '/'. $pagination_uri,
				'per_page' => $nb_to_display,
				'total_rows' => $tag->get('nb_total_articles'),
				'num_links' => 3,
				'cur_page' => $cur_page,
				'first_link' => lang('first_link'),			// "First" text : see /theme/your_theme/language/xx/pagination_lang.php
				'last_link' => lang('last_link'),			// "Last" text
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
