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
 * Ionize Tagmanager Navigation Class
 *
 * @package		Ionize
 * @subpackage	Libraries
 * @category	TagManager Libraries
 *
 */

require_once APPPATH.'libraries/Pages.php';


class TagManager_Navigation extends TagManager
{
	static protected $_current_language = NULL;

	public static $tag_definitions = array
	(
		'navigation' => 					'tag_navigation',
		'navigation:url' =>					'tag_navigation_url',
		'navigation:href' =>				'tag_navigation_href',
		'navigation:nav_title' =>			'tag_navigation_nav_title',
		'navigation:active_class' =>		'tag_simple_value',
		'navigation:is_active' =>			'tag_is_active',

		'tree_navigation' => 				'tag_tree_navigation',
		'tree_navigation:active_class' =>	'tag_simple_value',
		'tree_navigation:is_active' =>		'tag_is_active',

		'sub_navigation' => 				'tag_sub_navigation',
		'sub_navigation_title' => 			'tag_sub_navigation_title',

		// Languages
		'languages' =>				'tag_languages',
		'languages:language' =>		'tag_languages_language',
		'language' =>				'tag_language',

		'language:code' =>			'tag_language_code',
		'language:active_class' =>	'tag_simple_value',
		'language:default' =>		'tag_language_default',
		'language:online' =>		'tag_simple_value',
		'language:dir' =>			'tag_language_dir',

		'language:is_active' =>		'tag_is_active',
	);
	
	
	// ------------------------------------------------------------------------

	
	/**
	 * Navigation tag definition
	 * @usage	
	 *
	 */
	public static function tag_navigation(FTL_Binding $tag)
	{
		$cache = $tag->getAttribute('cache', TRUE);

		// Tag cache
		if ($cache == TRUE && ($str = self::get_cache($tag)) !== FALSE)
			return $str;

		// Final string to print out.
		$str = '';

		// Helper / No helper ?
		$helper = $tag->getAttribute('helper');
		
		// Get the asked lang if any
		$lang = $tag->getAttribute('lang');

		// Menu : Main menu by default
		$menu_name = $tag->getAttribute('menu', 'main');
		$id_menu = 1;

		foreach(self::registry('menus') as $menu)
		{
			if ($menu_name == $menu['name'])
				$id_menu = $menu['id_menu'];
		}
		
		// Navigation level. FALSE if not defined
		$asked_level = $tag->getAttribute('level', FALSE);

		// Display hidden navigation elements ?
		$display_hidden = $tag->getAttribute('display_hidden', FALSE);

		// Current page
		$current_page = self::registry('page');

		// Attribute : active CSS class
		$active_class = $tag->getAttribute('active_class', 'active');
		if (strpos($active_class, 'class') !== FALSE) $active_class= str_replace('\'', '"', $active_class);
		
		// Pages : Current lang OR asked lang code pages.
		$global_pages = ( ! is_null($lang) && Settings::get_lang() != $lang) ? Pages::get_pages($lang) : self::registry('pages');

		// Add the active class key
		$id_current_page = ( ! empty($current_page['id_page'])) ? $current_page['id_page'] : FALSE;
		
		$active_pages = Structure::get_active_pages($global_pages, $id_current_page);

		foreach($global_pages as &$page)
		{
			$page['title'] = $page['nav_title'] !='' ? $page['nav_title'] : $page['title'];
			// Add the active_class key
			$page['active_class'] = in_array($page['id_page'], $active_pages) ? $active_class : '';
			$page['is_active'] = in_array($page['id_page'], $active_pages) ? TRUE : FALSE;
			$page['id_navigation'] = $page['id_page'];
		}

		// Filter by menu and asked level : We only need the asked level pages !
		// $pages = array_filter($global_pages, create_function('$row','return ($row["level"] == "'. $asked_level .'" && $row["id_menu"] == "'. $id_menu .'") ;'));
		$pages = array();
		$parent_page = array();

		// Only conserve the menu asked pages
		foreach($global_pages as $key => $p)
		{
			if ($p['id_menu'] != $id_menu)
				unset($global_pages[$key]);
		}

		// Asked Level exists
		if ($asked_level !== FALSE)
		{
			foreach($global_pages as $p)
			{
				if ($p['level'] == $asked_level && $p['id_menu'] == $id_menu)
					$pages[] = $p;
			}
		}
		// Get navigation from current page
		else
		{
			foreach($global_pages as $p)
			{
				// Child pages of id_subnav
				if ($p['id_parent'] == $current_page['id_subnav'])
					$pages[] = $p;

				// Parent page is the id_subnav page
				if ($p['id_page'] == $current_page['id_subnav'])
					$parent_page = $p;
			}
		}
		
		// Filter on 'appears'=>'1'
		if ($display_hidden == FALSE)
			$pages = array_values(array_filter($pages, array('TagManager_Page', '_filter_appearing_pages')));

		// Get the parent page from one level upper
		if ($asked_level > 0)
		{
			$parent_pages = array();
			foreach($global_pages as $p)
			{
				if ($p['level'] == ($asked_level-1))
					$parent_pages[] = $p;
			}
			
			foreach($parent_pages as $p)
			{
				if ($p['active_class'] != '')
					$parent_page = $p;
			}
		}
		
		// Filter the current level pages on the link with parent page
		if ( ! empty($parent_page ))
		{
			$o_pages = $pages;
			$pages = array();
			foreach($o_pages as $p)
			{
				if ($p['id_parent'] == $parent_page['id_page'])
					$pages[] = $p;
			}
		}
		else
		{
			if ($asked_level > 0)
				$pages = array();
		}

		if ( $helper)
		{
			// Get helper method
			$helper_function = (substr(strrchr($helper, ':'), 1 )) ? substr(strrchr($helper, ':'), 1 ) : 'get_navigation';
			$helper = (strpos($helper, ':') !== FALSE) ? substr($helper, 0, strpos($helper, ':')) : 'navigation';

			// load the helper
			self::$ci->load->helper($helper);
			
			// Return the helper function result
			if (function_exists($helper_function))
			{
				// Set the helper
				$tag->setAttribute('helper', $helper.':'.$helper_function);

				// Process the helper
				$value = self::helper_process($tag, $pages, $helper.':'.$helper_function);

				$output = self::wrap($tag, $value);
				
				// Tag cache
				self::set_cache($tag, $output);
	
				return $output;
			}
			$error_message = 'Helper ' . $helper.':'.$helper_function.'() not found';
		}
		else
		{
			foreach($pages as $index => $p)
			{
				$tag->set('navigation', $p);
				$tag->set('page', $p);
				$tag->set('is_active', $p['is_active']);

				$tag->set('index', $index);
				$str .= $tag->expand();
			}

			$output = self::wrap($tag, $str);
			
			// Tag cache
			self::set_cache($tag, $output);

			return $output;
		}
		
		return self::show_tag_error($tag, $error_message);
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns first the navigation title
	 * If no one is defined, returns the page title
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_navigation_nav_title(FTL_Binding $tag)
	{
		$value = $tag->getValue('nav_title');

		if ($value == '')
			$value = $tag->getValue('title');

		$tag->set($tag->name, $value);

		return self::output_value($tag, $value);
	}


	// ------------------------------------------------------------------------


	public static function tag_sub_navigation_title(FTL_Binding $tag)
	{
		if ($tag->locals->_page['subnav_title']  != '')
		{
			return self::wrap($tag, $tag->locals->_page['subnav_title']);
		}
		else
		{
			foreach($tag->globals->pages as $page)
			{
				if ($page['id_page'] == $tag->locals->_page['id_subnav'])
				{
					return self::wrap($tag, $page['subnav_title']);
				}
			}
		}		
		return '';		
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Return a tree navigation based on the given helper.
	 * One helper is needed to use this tag.
	 * The default helper is /application/helpers/navigation_helper->get_tree_navigation()
	 * If you wish to change the
	 *
	 * @param	FTL_Binding object
	 *
	 * @return 	string
	 *
	 * @usage	<ion:tree_navigation [helper="navigation::your_helper_method"] />
	 *
	 */
	public static function tag_tree_navigation(FTL_Binding $tag)
	{
		// Page : Asked one through the page tag
		$page = $tag->get('page');

		// Current page
		if (is_null($page))
			$page = self::registry('page');

		// If 404 : Put empty vars, so the menu will prints out without errors
		/*
		if ( !isset($page['id_page']))
		{
			$page = array(
				'id_page' => '',
				'id_parent' => ''
			);
		}
		*/

		// Menu : Main menu by default
		$menu_name = $tag->getAttribute('menu', 'main');
		$id_menu = 1;

		foreach(self::registry('menus') as $menu)
		{
			if ($menu_name == $menu['name'])
			{
				$id_menu = $menu['id_menu'];
				break;
			}	
		}
		
		// Attribute level, else parent page level + 1
		$from_level = $tag->getAttribute('level', 0);

		// Depth
		$depth = $tag->getAttribute('depth', -1);

		// Attribute : active class, first_class, last_class
		$active_class = $tag->getAttribute('active_class', 'active');
		$first_class = $tag->getAttribute('first_class', '');
		$last_class = $tag->getAttribute('last_class', '');

		// Display hidden navigation elements ?
		$display_hidden = $tag->getAttribute('display_hidden', FALSE);

		// Includes articles as menu elements
		$with_articles = $tag->getAttribute('articles', FALSE);

		// Attribute : HTML Tree container ID & class attribute
		$id = $tag->getAttribute('id');
		if (strpos($id, 'id') !== FALSE) $id= str_replace('\'', '"', $id);

		$class = $tag->getAttribute('class');
		if (strpos($active_class, 'class') !== FALSE) $active_class= str_replace('\'', '"', $active_class);
		
		// Attribute : Helper to use to print out the tree navigation
		$helper =  $tag->getAttribute('helper', 'navigation');
		
		// Get helper method
		$helper_function = (substr(strrchr($helper, ':'), 1 )) ? substr(strrchr($helper, ':'), 1 ) : 'get_tree_navigation';
		$helper = (strpos($helper, ':') !== FALSE) ? substr($helper, 0, strpos($helper, ':')) : $helper;
		// load the helper
		self::$ci->load->helper($helper);

		// Page from locals : By ref because of active_class definition
		// $pages =  $tag->locals->_pages;
		$pages = self::registry('pages');

		/* Get the reference parent page ID
		 * Note : this is depending on the whished level.
		 * If the curent page level > asked level, we need to find recursively the parent page which has the good level.
		 * This is done to avoid tree cut when navigation to a child page
		 *
		 * e.g :
		 *
		 * On the "services" page and each subpage, we want the tree navigation composed by the sub-pages of "services"
		 * We are in the page "offer"
		 * We have to find out that the level 1 parent is "services"
		 *
		 *	Page structure				Level
		 *
		 *	home						0
		 *	 |_ about					1		
		 *	 |_ services				1		<- We want all the nested nav starting at level 1 from this parent page
		 *	 	   |_ development		2
		 *		   |_ design			2
		 *				|_ offer		3		<- We are here.
		 *				|_ portfolio	3	
		 */
		$page_level = (isset($page['level'])) ? $page['level'] : 0;

		// Asked Level exists
		$parent_page = array(
			'id_page' => ($from_level > 0) ? $page['id_page'] : 0,
			'id_parent' => isset($page['id_parent']) ? $page['id_parent'] : 0
		);

		if ($from_level !== FALSE)
		{
			$parent_page = array(
				'id_page' => ($from_level > 0) ? $page['id_page'] : 0,
				'id_parent' => isset($page['id_parent']) ? $page['id_parent'] : 0
			);
		}
		// Get navigation from current page
		else
		{
			foreach($pages as $p)
			{
				// Parent page is the id_subnav page
				if ($p['id_page'] == $page['id_subnav'])
					$parent_page = $p;
			}
		}

		// Find out the wished parent page 
		while ($page_level >= $from_level && $from_level > 0)
		{
			$potential_parent_page = array();
			foreach($pages as $p)
			{
				if($p['id_page'] == $parent_page['id_parent'])
				{
					$potential_parent_page = $p;
					break;
				}
			}
			if ( ! empty($potential_parent_page))
			{
				$parent_page = $potential_parent_page;
				$page_level = $parent_page['level'];
			}
			else
			{
				$page_level--;
			}
		}
		// Active pages array. Array of ID
		$active_pages = Structure::get_active_pages($pages, $page['id_page']);
		
		foreach($pages as $key => $p)
		{
			$pages[$key]['active_class'] = in_array($p['id_page'], $active_pages) ? $active_class : '';
		}

		// Filter on 'appears'=>'1'
		$nav_pages = $pages;
		if ($display_hidden === FALSE)
			$nav_pages = array_values(array_filter($pages, array('TagManager_Page', '_filter_appearing_pages')));

		$final_nav_pages = $nav_pages_list = array();
		foreach($nav_pages as $k => $np)
		{
			if ($np['id_menu'] == $id_menu )
			{
				$final_nav_pages[] = $np;
				$nav_pages_list[] = $np['id_page'];
			}
		}
		
		// Should we include articles ?
		$articles = FALSE;
		if ($with_articles == TRUE)
		{
			$entity = self::get_entity();
			$id_active_article = ($entity['type'] == 'article') ? $entity['id_entity'] : NULL;

			foreach($final_nav_pages as $key=>$p)
			{
				// TODO : Change for future "Articles" lib call
				$tag->set('page', $p);

				$articles = TagManager_Article::get_articles($tag);

				// Set active article
				if ( ! is_null($id_active_article))
				{
					foreach($articles as $akey => $a)
					{
						if ($a['id_article'] == $id_active_article)
						{
							$articles[$akey]['active_class'] = $active_class;
							$articles[$akey]['is_active'] = TRUE;

						}
					}
				}


				$final_nav_pages[$key]['articles'] = $articles;
			}
		}

		// Get the tree navigation array
		$tree = Structure::get_tree_navigation($final_nav_pages, $parent_page['id_page'], $from_level, $depth, $articles);

		// Return the helper function
		if (function_exists($helper_function))
			return call_user_func($helper_function, $tree, $id, $class, $first_class, $last_class);

	}


	// ------------------------------------------------------------------------


	/** 
	 * Return the URL of a navigation item.
	 *
	 * @param	FTL_Binding
	 *
	 * @return 	null|string
	 *
	 * @usage	<ion:languages [helper="helper:helper_method"]>
	 * 				...
	 * 			<ion:languages>
	 *
	 */
	public static function tag_navigation_url(FTL_Binding $tag)
	{
		$has_url = $tag->getValue('has_url');

		if (intval($has_url) == 1)
			return self::wrap($tag, $tag->getValue('absolute_url'));

		return '#';
	}


	/**
	 * Builds and return the href attribute
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_navigation_href(FTL_Binding $tag)
	{
		$has_url = $tag->getValue('has_url');

		if (intval($has_url) == 1)
		{
			$str = 'href="' . $tag->getValue('absolute_url') . '"';

			return $str;
		}

		return '';
	}


	/**
	 * Languages tag
	 * 
	 * @param	FTL_Binding
	 *
	 * @return 	null|string
	 *
	 * @usage	<ion:languages [helper="helper:helper_method"]>
	 * 				...
	 * 			<ion:languages>
	 *
	 */
	public static function tag_languages(FTL_Binding $tag)
	{
		$languages = (Authority::can('access', 'admin') && Settings::get('display_front_offline_content') == 1) ? Settings::get_languages() : Settings::get_online_languages();

		$page = self::registry('page');
		$article = self::registry('article');

		// Current active language class
		$active_class = $tag->getAttribute('active_class', 'active');

		// Ignore current language in output
		$ignore_current = $tag->getAttribute('ignore_current');

		// helper
		$helper = $tag->getAttribute('helper');

		$str = '';

		$tag->set('count', count($languages));

		foreach($languages as $idx => &$lang)
		{
			$lang_code = $lang['lang'];
			$p_data = $page['languages'][$lang_code];

			if ( $ignore_current == TRUE && $lang_code == Settings::get_lang('current'))
				continue;


			// Correct the Home page URL
			if ($p_data['online'] == 1 OR ($p_data['online'] == 0 && Authority::can('access', 'admin') && Settings::get('display_front_offline_content') == 1))
			{
				if ($page['home'] != 1 )
					$lang['absolute_url'] =	! empty($page['absolute_urls'][$lang_code]) ? $page['absolute_urls'][$lang_code] : base_url() . $lang_code;
				else
					$lang['absolute_url'] = base_url() . $lang_code;
			}
			else
			{
				$lang['absolute_url'] = NULL;
			}

			$lang['active_class'] = ($lang_code == Settings::get_lang('current')) ? $active_class : '';
			$lang['is_active'] = $lang_code == Settings::get_lang('current');
			$lang['id'] = $lang_code;

			if ( ! is_null($article))
			{
				$a_data = $article['languages'][$lang_code];
				if (
					! is_null($a_data['url']) && $a_data['online'] == 1
					OR ($a_data['online'] == 0 && Authority::can('access', 'admin') && Settings::get('display_front_offline_content') == 1)
				)
				{
					if ($page['home'] != 1 )
					{
						$lang['absolute_url'] .= '/'. $a_data['url'];
					}
					else
						$lang['absolute_url'] .= '/'.$page['urls'][$lang_code].'/'.$a_data['url'];
				}
				else
				{
					$lang['absolute_url'] = NULL;
				}
			}

			// Tag locals
			$tag->set('language', $lang);
			$tag->set('id', $lang_code);
			$tag->set('absolute_url', $lang['absolute_url']);
			$tag->set('active_class', $lang['active_class']);
			$tag->set('is_active', $lang['is_active']);
			$tag->set('index', $idx);

			if ( ! is_null($lang['absolute_url']))
				$str .= $tag->expand();
		}

		// Try to return the helper function result
		if ( $str != '' && ! is_null($helper))
		{
			$helper_function = (substr(strrchr($helper, ':'), 1 )) ? substr(strrchr($helper, ':'), 1 ) : 'get_language_navigation';
			$helper = (strpos($helper, ':') !== FALSE) ? substr($helper, 0, strpos($helper, ':')) : $helper;

			self::$ci->load->helper($helper);

			if (function_exists($helper_function))
			{
				$nav = call_user_func($helper_function, $languages);
			
				return self::wrap($tag, $nav);
			}
		}

		return self::wrap($tag, $str);
	}


	/**
	 * Language tag in the context of its parent 'languages"
	 *
	 * @param 	FTL_Binding $tag
	 *
	 * @return 	string
	 *
	 * @usage	<ion:languages>
	 * 				<ion:language>
	 * 					<ion:name />
	 * 				</ion:language>
	 * 			<ion:languages>
	 *
	 * 			Shortcut mode :
	 * 			<ion:languages>
	 * 				<ion:language:name />
	 * 			<ion:languages>
	 *
	 */
	public static function tag_languages_language(FTL_Binding $tag)
	{
		return $tag->expand();
	}


	/**
	 * Standalone language tag
	 *
	 * @param 	FTL_Binding $tag
	 *
	 * @return 	string
	 *
	 * @usage	<ion:language>
	 * 				<ion:code />
	 * 				<ion:name />
	 * 				<ion:url />
	 * 				<ion:is_default />
	 * 				<ion:is_active />
	 * 			</ion:language>
	 */
	public static function tag_language(FTL_Binding $tag)
	{
		if (is_null(self::$_current_language))
		{
			$page = self::registry('page');

			foreach(Settings::get_languages() as $language)
			{
				if ($language['lang'] == Settings::get_lang())
				{
					$language['id'] = $language['lang'];
					$language['absolute_url'] = $page['absolute_urls'][$language['lang']];
					self::$_current_language = $language;
					break;
				}
			}
		}

		$tag->set('language', self::$_current_language);

		return $tag->expand();
	}



	/**
	 * Returns the language code
	 * Example : en
	 *
	 * @param 	FTL_Binding $tag
	 *
	 * @return 	null|string
	 *
	 * @usage	<ion:language>
	 * 				<ion:code [tag="span" class="colored"] />
	 * 			</ion:language>
	 *
	 * 			Shortcut mode :
	 * 			<ion:language:code [tag="span" class="colored"] />
	 */
	public static function tag_language_code(FTL_Binding $tag)
	{
		return self::output_value($tag, $tag->getValue('lang'));
	}


	/**
	 * Returns language's direction : 'ltr' or 'rtl'
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_language_dir(FTL_Binding $tag)
	{
		$dir = $tag->getValue('direction');

		$dir = ($dir == 1 OR empty($dir)) ? 'ltr' : 'rtl';

		return self::output_value($tag, $dir);
	}


	/**
	 * Returns the default language
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_language_default(FTL_Binding $tag)
	{
		return self::output_value($tag, $tag->getValue('def'));
	}
}
