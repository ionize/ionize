<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.7
 *
 */

/**
 * Ionize Pages Class
 *
 * @package		Ionize
 * @subpackage	Libraries
 * @category	TagManager Libraries
 *
 */
class Pages
{

	protected static $_inited = FALSE;

	protected static $user = FALSE;

	static $ci;
	

	function init()
	{
		if(self::$_inited)
		{
			return;
		}
		self::$_inited = TRUE;
		
		self::$ci =& get_instance(); 
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/* Returns all pages
	 * Used by the TagManager Page and the TagManager Navigation
	 *
	 */
	public static function get_pages($lang = NULL)
	{
		if ($lang == NULL) $lang = Settings::get_lang('current');

		self::$ci->load->model('page_model');

		$pages = self::$ci->page_model->get_lang_list(NULL, $lang);
				
		// Should never be displayed : no pages are set.
		if (empty($pages))
		{
			show_error('Internal error : <b>No pages found.</b><br/>Solution: <b>Create at least one online page.</b>', 500 );
			exit;
		}

		/* Spread authorizations from parents pages to chidrens.
		 * This adds the group ID to the childrens pages of a protected page
		 * If you don't want this, just uncomment this line.
		 */
		// self::$ci->page_model->spread_authorizations($pages);


		// Filter pages regarding the authorizations
		if (User()->get('role_level') < 1000)
			$pages = array_values(array_filter($pages, array(__CLASS__, '_filter_pages_authorization')));

		// Set all abolute URLs one time, for perf.
		self::init_absolute_urls($pages, $lang);

		return $pages;
	}

	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Inits the Absolutes URLs of each page
	 *
	 * @TODO : Rewrite the "absolute_urls" definition so that it takes the internal links in account.
	 *
	 *
	 */
	public static function init_absolute_urls(&$pages, $lang)
	{
		$short_mode = (config_item('url_mode') == 'short') ? TRUE : FALSE;

		$languages = (Authority::can('access', 'admin') && Settings::get('display_front_offline_content') == 1) ? Settings::get_languages() : Settings::get_online_languages();

		foreach ($pages as &$page)
		{
			// Set the page complete URL
			$page['absolute_url'] = '';
			$url = ($short_mode) ? $page['url'] : $page['path'];

			// Link
			if ($page['link_type'] != '' )
			{
				// External
				if ($page['link_type'] == 'external')
				{
					$page['absolute_url'] = $page['link'];
				}
				else if ($page['link_type'] == 'email')
				{
					$page['absolute_url'] = auto_link($page['link'], 'both', TRUE);
				}
				// Internal
				else
				{
					// Article
					if($page['link_type'] == 'article')
					{
						// Get the article to which this page links
						$rel = explode('.', $page['link_id']);
						$target_article = self::$ci->article_model->get_context($rel[1], $rel[0], $lang);

						// Of course, only if not empty...
						if ( ! empty($target_article))
						{
							// Get the article's parent page
							$page['absolute_url'] = '';
							
							foreach($pages as $p)
							{
								if ($p['id_page'] == $target_article['id_page'])
								{
									$p_url = ($short_mode) ? $p['url'] : $p['path'];
									$page['absolute_url'] = $p_url . '/' . $target_article['url'];
								}
							}
						}
					}
					// Page
					else
					{
						// Get the page to which the page links
						$page['absolute_url'] = '';
						
						foreach($pages as $p)
						{
							if ($p['id_page'] == $page['link_id'])
								$page['absolute_url'] = ($short_mode) ? $p['url'] : $p['path'];
						}
					}
					if ( count($languages) > 1 OR Settings::get('force_lang_urls') == '1' )
					{
						$page['absolute_url'] =  $lang. '/' . $page['absolute_url'];
					}
					$page['absolute_url'] = base_url() . $page['absolute_url'];

				}
			}
			else
			{
				if ( count($languages) > 1 OR Settings::get('force_lang_urls') == '1' )
				{
					// Home page : doesn't contains the page URL
					if ($page['home'] == 1 )
					{
						// Default language : No language code in the URL for the home page
						// Other language : The home page has the lang code in URL
						if (Settings::get_lang('default') != $lang)
						{
							$page['absolute_url'] = $lang;
						}
					}
					// Other pages : lang code in URL
					else
					{
						// If page URL if already set because of a link, don't replace it.
						// $url = ($short_mode) ? $page['url'] : $page['path'];
						$page['absolute_url'] = ($page['absolute_url'] != '') ? $lang . '/' . $page['absolute_url'] : $lang . '/' . $url;
					}
	
					$page['absolute_url'] = base_url() . $page['absolute_url'];
					
					// Set the lang code depending URL (used by language subtag)
					$page['absolute_urls'] = array();					
				}
				else
				{
					if ($page['home'] == 1)
					{
						$page['absolute_url'] = base_url();
					}
					else
					{
						// $url = ($short_mode) ? $page['url'] : $page['path'];
						$page['absolute_url'] = base_url() . $url;
					}
					// Set the lang code depending URL (used by language subtag)
					$page['absolute_urls'][$lang] = $page['absolute_url'];
				}
			}

			// Explode the concatenated URLs infos
			$page_url_langs = explode(';', $page['url_langs']);
			$page_url_path = explode(';', $page['url_paths']);

			foreach ($languages as $language)
			{
				if ($page['home'] == 1 )
				{
					// Default language : No language code in the URL for the home page
					if (Settings::get_lang('default') == $language['lang'])
					{
						$page['absolute_urls'][$language['lang']] = base_url();
					}
					// Other language : The home page has the lang code in URL
					else
					{
						$page['absolute_urls'][$language['lang']] = base_url() . $language['lang'];
					}
				}
				// Other pages : lang code in URL
				else
				{
					if ( ! $short_mode)
					{
						// The index of the processed lang code will be the index of the path
						$index = array_search($language['lang'], $page_url_langs);
						$url = ($index !== FALSE) ? $page_url_path[$index] : '';
						$page['absolute_urls'][$language['lang']] = base_url() . $language['lang'] . '/' . $url;
					}
					else
					{
						$page['absolute_urls'][$language['lang']] = base_url() . $language['lang'] . '/' . $page['urls'][$language['lang']];
					}
				}
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * @return string
	 */
	public static function get_home_page_url()
	{
		$url = base_url();

		if (Settings::get_lang('default') != Settings::get_lang())
			$url .= Settings::get_lang() . '/';

		return $url;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $page
	 *
	 * @return bool
	 */
	private static function _filter_pages_authorization($page)
	{
		$resource = 'frontend/page/' . $page['id_page'];

		if ( Authority::cannot('access', $resource, NULL, TRUE))
		{
			if (empty($page['deny_code']) OR $page['deny_code'] == '404' )
				return FALSE;
		}

		return TRUE;
	}
}

Pages::init();
