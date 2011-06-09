<?php
/*
 * Created by Martin Wernståhl on 2010-01-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Page extends Base_Controller
{
	// Current user
	public $user = false;

	// All groups
	protected $groups = false;
	

	protected $installed_tagmanagers = array(
			'Pages',
			'Archives',
			'Category',
	);

//	protected $default_page_view = 			'default/page';
//	protected $default_navigation_view = 	'default/navigation';
//	protected $default_categories_view = 	'default/categories';
//	protected $default_archives_view = 		'default/archives';

	public $uri_segment;
	
	function index()
	{
		// Get the page / article array
		$this->uri_segment = func_get_args();

		$this->load->model('page_model');

		require_once APPPATH.'libraries/ftl/parser.php';
		require_once APPPATH.'libraries/ftl/arraycontext.php';

		require_once APPPATH.'libraries/Tagmanager.php';

		require_once APPPATH.'libraries/Tagmanager/Page.php';
		require_once APPPATH.'libraries/Tagmanager/Element.php';
		require_once APPPATH.'libraries/Tagmanager/Form.php';
		require_once APPPATH.'libraries/Tagmanager/Login.php';
		
		// Context
		$c = new FTL_ArrayContext();
			
		// Get all groups
		$this->groups = $this->connect->model->get_groups();
		
		// Get the current user (used by autorization filtering)
		if ($this->connect->logged_in())
			$this->user = $this->connect->get_current_user();

		/* Add all pages to the context : Usefull for having just one request for Pages result 
		 * wich will be used by Page Tag manager and Navigation manager.
		 */
		$pages = $this->page_model->get_lang_list(false, Settings::get_lang());

		/* Spread authorizations from parents pages to chidrens.
		 * This adds the group ID to the childrens pages of a protected page
		 * If you don't want this, just uncomment this line.
		 */
		$this->page_model->spread_authorizations($pages);

		// Not needed for the moment.
		// Think about a further implementation.
		// $this->access->restrict();

		// Filter pages regarding the authorizations
		$pages = array_values(array_filter($pages, array($this, '_filter_authorization')));
		
		// get special tag manager:
		if(in_array($file = ucfirst(strtolower($this->uri->segment(3))), $this->installed_tagmanagers))
		{
			if (is_file(APPPATH.'libraries/Tagmanager/'.$file.EXT))
			{
				require_once APPPATH.'libraries/Tagmanager/'.$file.EXT;
			
				$class = 'TagManager_'.$file;
			
				$m = new $class($this, $c, $pages);
			}
		}
		// Standard page tag manager
		else
		{
			// Page tag manager
			$m = new TagManager_Page($this, $c, $pages);
			$m->add_globals($c);
			$m->add_tags($c);

			// Element tag manager
			$f = new TagManager_Element($this);
			$f->add_globals($c);
			$f->add_tags($c);
			
			// Form tag manager
			$f = new TagManager_Form($this);
			$f->add_globals($c);
			$f->add_tags($c);
			
			// Login tag manager
			$l = new TagManager_Login($this);
			$l->add_globals($c);
			$l->add_tags($c);
		}
		
		// Add tags from modules
		// Automatically add each function of the class [Your_module]_Tags defined in the [your_module]/libraries/tags.php
		TagManager::add_plugin_tags($c);

		// Get the asked page
		$page = $c->globals->page;

		/*
		 * If asked page is a link to another page, redirect to the linked one
		 */
		if ( ! empty($c->globals->page['link']))
		{
			// Online languages are defined by MY_Controller
			$lang = (count(Settings::get_online_languages()) > 1 ) ? Settings::get_lang('current').'/' : '';

			$domain = (!empty($c->globals->page['link_type'])  && $c->globals->page['link_type'] == 'external') ? '' : base_url();

			redirect($domain.$lang.$c->globals->page['link']);
		}

		// Get the page view
		$view = ($c->globals->page['view'] != false) ? $c->globals->page['view'] : Theme::get_default_view('page');

		// Outputs the page view with Base_Controller2->render()
		$this->render($view, $c);
	}


	private function _filter_authorization($row)
	{
		// If the page group != 0, then get the page group and check the restriction
		if($row['id_group'] != 0)
		{
			$page_group = false;
			
			// Get the page group
			foreach($this->groups as $group)
			{
				if ($group['id_group'] == $row['id_group']) $page_group = $group;
			} 

			// If the current connected user has access to the page return true
			if ($this->user !== false && $page_group != false && $this->user['group']['level'] >= $page_group['level'])
				return true;
			
			// If nothing found, return false
			return false;
		}
		return true;
	}
}


/* End of file page.php */
/* Location: ./application/controllers/page.php */