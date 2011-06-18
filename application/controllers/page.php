<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 *
 */

/**
 * Default Ionize Controller
 * Displays all pages
 *
 */
class Page extends Base_Controller
{
	// Current user
	public $user = false;

	// All groups
	protected $groups = false;
	
	public $uri_segment;
	
	
	
	function index()
	{
		// Get the page / article array
		$this->uri_segment = func_get_args();

		require_once APPPATH.'libraries/ftl/parser.php';
		require_once APPPATH.'libraries/ftl/arraycontext.php';
		require_once APPPATH.'libraries/Tagmanager.php';
		require_once APPPATH.'libraries/Tagmanager/Page.php';
		
		// Context
		$c = new FTL_ArrayContext();
			
		// Get all groups
		$this->groups = $this->connect->model->get_groups();
		
		// Get the current user (used by autorization filtering)
		if ($this->connect->logged_in())
			$this->user = $this->connect->get_current_user();

		// Page TagManager instanciation
		new TagManager_Page($c);

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

		// Outputs the page view with Base_Controller->render()
		$this->render($view, $c);
	}
}


/* End of file page.php */
/* Location: ./application/controllers/page.php */