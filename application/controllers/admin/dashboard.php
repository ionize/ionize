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
		// $articles = $this->article_model->get_list(array('order_by'=>'updated DESC'));

		$articles = $this->article_model->get_lang_list(
			array('order_by'=>'updated DESC'),
			Settings::get_lang('default')
		);

		// Last 10 articles
		$last_articles = array();
		$max = (count($articles) > 9) ? 10 : count($articles);
		if ( ! empty($articles))
		{
			for ($i=0; $i<$max; $i++)
				$last_articles[] = $articles[$i];
		}

		// Orphan articles
		$orphan_articles = array();
		foreach ($articles as $article)
		{
			if ( ! $article['id_page'])
				$orphan_articles[] = $article;
		}

		// Orphan pages
		$orphan_pages = $this->page_model->get_lang_list(array('id_menu' => '0', 'order_by'=>'name ASC'), Settings::get_lang('default'));
		
		// Last connected users
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

			$pages = $this->page_model->get_parent_array($article['id_page'], array(), Settings::get_lang('default'));
			$breadcrumb = array();
			foreach($pages as $page)
			{
				$breadcrumb[] = ( ! empty($page['title'])) ? $page['title'] : $page['name'];
			}
			$article['breadcrumb'] = implode(' > ', $breadcrumb);
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


		$this->output('dashboard');		
	}

}
/* End of file dashboard.php */
/* Location: ./application/admin/controllers/dashboard.php */