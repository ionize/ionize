<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
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
	public static function get_pages($lang = FALSE)
	{
		if ($lang == FALSE) $lang = Settings::get_lang();
	
		$pages = self::$ci->page_model->get_lang_list(false, $lang);
		
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
		if (Connect()->logged_in())
			self::$user = Connect()->get_current_user();
		 
		self::$ci->page_model->spread_authorizations($pages);

		// Filter pages regarding the authorizations
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
		foreach ($pages as &$page)
		{
			// Set the page complete URL
			$page['absolute_url'] = '';

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
									$page['absolute_url'] = $p['url'] . '/' . $target_article['url'];
								}
							}
						}
					}
					// Page
					else
					{
						// Get the page to which the page links
						// $target_page = array_values(array_filter($con->globals->pages, create_function('$row','return $row["id_page"] == "'. $page['link_id'] .'";')));
						// if ( ! empty($target_page))
						// {
						//	$page['absolute_url'] = $target_page[0]['url'];
						// }
						$page['absolute_url'] = '';
						
						foreach($pages as $p)
						{
							if ($p['id_page'] == $page['link_id'])
								$page['absolute_url'] = $p['url'];
						}
					}
					if ( count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1' )
					{
						$page['absolute_url'] =  $lang. '/' . $page['absolute_url'];
					}
					$page['absolute_url'] = base_url() . $page['absolute_url'];

				}
			}
			else
			{
				if ( count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1' )
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
						$page['absolute_url'] = ($page['absolute_url'] != '') ? $lang . '/' . $page['absolute_url'] : $lang . '/' . $page['url'];
					}
	
					$page['absolute_url'] = base_url() . $page['absolute_url'];
					
					// Set the lang code depending URL (used by language subtag)
					$page['absolute_urls'] = array();
					
					/*
					foreach (Settings::get_online_languages() as $language)
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
							$page['absolute_urls'][$language['lang']] = base_url() . $language['lang'] . '/' . $page['urls'][$language['lang']];
						}
					}
					*/
				}
				else
				{

					if ($page['home'] == 1)
					{
						$page['absolute_url'] = base_url();
					}
					else
					{
						$page['absolute_url'] = base_url() . $page['url'];
					}
					// Set the lang code depending URL (used by language subtag)
					$page['absolute_urls'][$lang] = $page['absolute_url'];
				}
			}


			foreach (Settings::get_online_languages() as $language)
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
					$page['absolute_urls'][$language['lang']] = base_url() . $language['lang'] . '/' . $page['urls'][$language['lang']];
				}
			}
			
		}
	}
	
	
	// ------------------------------------------------------------------------
	
	
	private static function _filter_pages_authorization($row)
	{
		// If the page group != 0, then get the page group and check the restriction
		if($row['id_group'] != 0)
		{
			self::$ci->load->model('connect_model');
			$page_group = FALSE;
			
			$groups = self::$ci->connect_model->get_groups();
			
			// Get the page group
			foreach($groups as $group)
			{
				if ($group['id_group'] == $row['id_group']) $page_group = $group;
			} 

			// If the current connected user has access to the page return TRUE
			if (self::$user !== FALSE && $page_group != FALSE && self::$user['group']['level'] >= $page_group['level'])
				return TRUE;
			
			// If nothing found, return FALSE
			return FALSE;
		}
		return TRUE;
	}

	
}

Pages::init();

/* End of file Pages.php */
/* Location: /application/libraries/Pages.php */