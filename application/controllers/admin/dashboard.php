<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.4
 */

// ------------------------------------------------------------------------

/**
 * Ionize Dashboard Controller
 * Displays the dashboard
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Controllers
 * @author		Ionize Dev Team
 */
class Dashboard extends MY_Admin {


	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('page_model', '', true);
		$this->load->model('article_model', '', true);
		$this->load->model('users_model', '', true);
	}


	function index()
	{
		// Articles
		$articles = $this->article_model->get_list(array('order_by'=>'updated DESC'));

		// Last 5 articles
		$last_articles = array();
		$max = (count($articles) > 4) ? 5 : count($articles);
		if ( ! empty($articles))
		{
			for ($i=0; $i<$max; $i++)
			{
				$last_articles[] = $articles[$i];
			}
		}

		// Get all contexts : links between pages and articles
		$page_article = $this->article_model->get_all_context();

		// Get pages
		$pages = $this->page_model->get_lang_list(false, Settings::get_lang('default'));

		// Add page name to each context & feed the linked articles array
		$linked_articles = array();
		
		foreach($page_article as &$pa)
		{
			if ( array_search($pa['id_article'], $linked_articles) === FALSE)
			{
				$linked_articles[] = $pa['id_article'];
			}
			$page = array_values(array_filter($pages, create_function('$row','return $row["id_page"] == "'. $pa['id_page'] .'";')));
			$pa['page'] = (!empty($page) ? $page[0] : array() );
		}

		// Orphan articles
		$orphan_articles = array();
		foreach ($articles as $article)
		{
			if ( array_search($article['id_article'], $linked_articles) === FALSE)
			{
				$orphan_articles[] = $article;
			}
			
		}

		// Orphan pages
		$orphan_pages = $this->page_model->get_lang_list(array('id_menu' => '0', 'order_by'=>'name ASC'), Settings::get_lang('default'));
		
		// Last connected users
//		$users = array_filter($this->connect->model->get_users(array('limit'=>'10', 'level > ' => '999'), array($this, '_filter_users'));
		$users = $this->connect->model->get_users(array('limit'=>'10', 'order_by' => 'last_visit DESC', 'last_visit <>' => ''));

		$last_registered_users = $this->connect->model->get_users(array('limit'=>'10', 'order_by' => 'join_date DESC'));
		
		
		// Updates on last articles
		foreach($last_articles as &$article)
		{
			// User name update
			foreach($users as $user)
			{
				if($user['username'] == $article['updater']) $article['updater'] = $user['screen_name'];
				if($user['username'] == $article['author']) $article['author'] = $user['screen_name'];
			}
			
			// Article's pages...
			$article['pages'] = array_values(array_filter($page_article, create_function('$row','return $row["id_article"] == "'. $article['id_article'] .'";')));
		}

		// Updates on orphan pages
		foreach($orphan_pages as & $page)
		{
			// User name update
			foreach($users as $user)
			{
				if($user['username'] == $page['updater']) $page['updater'] = $user['screen_name'];
				if($user['username'] == $page['author']) $page['author'] = $user['screen_name'];
			}
		}
		
		
		// Updates on orphan articles
		foreach($orphan_articles as & $article)
		{
			// User name update
			foreach($users as $user)
			{
				if($user['username'] == $article['updater']) $article['updater'] = $user['screen_name'];
				if($user['username'] == $article['author']) $article['author'] = $user['screen_name'];
			}
		}
		
		// Flags
		$settings = Settings::get_settings();
		
		$flags = array();
		foreach ($settings as $key=>$setting)
		{
			if (strpos($key, 'flag') !== FALSE && $setting !='')
			{
				$flags[substr($key, -1)] = $setting;
			}
		}

		// Modules
		$modules = array();
		include APPPATH . 'config/modules.php';
		$config_files = glob(MODPATH . '*/config.xml');

		// Module data to put to template
		$moddata = array();
		
		// Get all modules from folders
		if (!empty($config_files))
		{
			foreach($config_files as $file)
			{
				$xml = simplexml_load_file($file);
				
				// Module folder
				preg_match('/\/([^\/]*)\/config.xml$/i', $file, $matches);
				$folder = $matches[1];
	
				$uri = (String) $xml->uri_segment;
	
				// Only add 
				// - installed modules (in $module var of config/modules.php)
				// - module with admin part
				if (in_array($folder, $modules) && $xml->has_admin == 'true')
				{
					// Store data
					$moddata[$uri] = array(
							'name'			=> (String) $xml->name,
							'uri_segment'	=> (String) $xml->uri_segment,
							'description'	=> (String) $xml->description,
							'folder'		=> $folder,
							'file'			=> $file,
							'access_group'	=> (String) $xml->access_group
					);
	
					// Get the user segment
					foreach($modules as $segment => $f)
					{
						if ($f == $folder)
							$moddata[$uri]['uri_segment'] = $segment; 
					}
				}
			}
		}
				
		// Put installed module list to template
		$this->template['modules'] = $moddata;
		
		$this->template['flags'] = $flags;
		
		$this->template['last_articles'] = $last_articles;
		$this->template['orphan_pages'] = $orphan_pages;
		$this->template['orphan_articles'] = $orphan_articles;
		$this->template['users'] = $users;	
		$this->template['last_registered_users'] = $last_registered_users;	


$this->load->library('structure');
$pages = $this->page_model->get_lang_list(false, Settings::get_lang('default'));

/*
$tree = array();
$this->structure->get_nested_structure($pages, $tree, 0, 0);
trace($tree);
*/
foreach($pages as $page)
{
	foreach(Settings::get_languages() as $language)
	{
		$breacrumbs = $this->get_breadcrumb_array($page, $pages, $language['lang']);
		$url = '';
		for($i=0; $i<count($breacrumbs); $i++)
		{
			$url .= '/' . $breacrumbs[$i]['url'];
		}
		trace($url);
	}
}		

		
		$this->output('dashboard');		
	}

/* TEST

 */
	function get_breadcrumb_array($page, $pages, $lang, $data = array())
	{
		$parent = NULL;
		
		if (isset($page['id_parent']) ) // && $page['id_parent'] != '0')
		{
			// Find the parent
			for($i=0; $i<count($pages) ; $i++)
			{
				if ($pages[$i]['id_page'] == $page['id_parent'])
				{
					$parent = $pages[$i];
					$data = self::get_breadcrumb_array($parent, $pages, $lang, $data);
					break;
				}
			}
			
			$data[] = $page;
		}
		return $data;
	}


}
/* End of file dashboard.php */
/* Location: ./application/admin/controllers/dashboard.php */