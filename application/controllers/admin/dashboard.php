<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Dashboard Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.4
 */

class Dashboard extends MY_Admin {


	public function __construct()
	{
		parent::__construct();

        // Models
        $this->load->model(
            array(
                'page_model',
                'article_model',
                'user_model'
            ), '', TRUE);
	}


	function index()
	{
		// Articles
		$articles = $this->article_model->get_lang_list(
			array('order_by'=>'updated DESC'),
			Settings::get_lang('default')
		);

		// Last 10 articles
		$last_articles = array();
		$max = (count($articles) > 9) ? 10 : count($articles);
		$count = 0;
		if ( ! empty($articles))
		{
			foreach($articles as $article)
			{
				if (
					Authority::can('access', 'backend/menu/' . $article['id_menu'], NULL, TRUE)
					&& Authority::can('access', 'backend/page/' . $article['id_page'], NULL, TRUE)
					&& Authority::can('access', 'backend/article/' . $article['id_article'], NULL, TRUE)
				)
				{
					$last_articles[] = $article;
					$count++;
					if ($count == $max)
						break;
				}
			}
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
		
		// Last connected /registered users
		$logged_user_role = User()->get_role();

		$users = $this->user_model->get_list_with_role(
			array(
				'limit'=>'10',
				'order_by' =>
				'last_visit DESC',
				'last_visit <>' => ''
			)
		);

		$last_registered_users = $this->user_model->get_list_with_role(
			array(
				'limit'=>'10',
				'order_by' => 'join_date DESC',
	//			'role_level <= ' => $logged_user_role['role_level']
			)
		);
		
		
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

		// Put installed module list to template
		$installed_modules = Modules()->get_installed_modules();
		$modules = array();
		foreach ($installed_modules as $module)
		{
			if ($module['has_admin'] && Authority::can('access', 'module/'.$module['key']))
				$modules[] = $module;
		}


		//get messages from Backend and modules
		//each message is an array containing minimum:
		//"origin"	- string, eg. modulename, "backend", ...
		//"content"	- string, content to display in the messagebox
		//"icon"	- string, (optional) absolute web url for icon to display
		$messages = array();
		//- from backend
		//  TODO: add backend message announcer (eg. update check)

		//- from modules
		foreach( $installed_modules as $module )
		{
			//skip if even config says: no admin mode
			if( empty($module["has_admin"]) )
				continue;

			//load module admin-class
			//-----------------------
			$folder = ucfirst($module["folder"]);
			//skip if no admin controller exists
			if( !file_exists(MODPATH.$folder.'/controllers/admin/'.$module["uri"].EXT) )
				continue;
			//skip loading the file again if the class is already defined
			if( !class_exists($folder) )
				require_once(MODPATH.$folder.'/controllers/admin/'.$module["uri"].EXT);

			// add module path to finder
//			array_push(Finder::$paths, MODPATH.$folder.'/');

			//if possible, call the message getter
			$messageResult = array();
			if( is_callable(array($module["module"], 'getBackendMessages')) )
				$messageResult = call_user_func(array($module["module"], 'getBackendMessages'));
			//if we got back a single message - convert to multiple-message-array
			//as we allow multiple messages to get returned in one call
			if( is_array($messageResult) AND !is_array(current($messageResult)) )
				$messageResult = array($messageResult);
			//loop through all returned messages
			foreach($messageResult as $message)
				//only add the message if it contains the needed data
				if( is_array($message) and !empty($message["origin"]) and !empty($message["content"]) )
					$messages[] = $message;
		}




		$this->template['messages'] = $messages;
		
		$this->template['modules'] = $modules;

		$this->template['flags'] = $flags;
		
		$this->template['last_articles'] = $last_articles;
		$this->template['orphan_pages'] = $orphan_pages;
		$this->template['orphan_articles'] = $orphan_articles;
		$this->template['users'] = $users;	
		$this->template['last_registered_users'] = $last_registered_users;	


		$this->output('desktop/dashboard');
	}
}
