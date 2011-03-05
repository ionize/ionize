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
		$articles = $this->article_model->get_list(false, 'updated DESC');

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
		$orphan_pages = $this->page_model->get_lang_list(array('id_menu' => '0'), Settings::get_lang('default'), false, false, 'name ASC');
		
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