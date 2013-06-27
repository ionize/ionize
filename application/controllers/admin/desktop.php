<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Desktop Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

class Desktop extends MY_Admin
{
	public function __construct()
	{
		parent::__construct();
	}


	// ------------------------------------------------------------------------


	public function index()
	{
		// Disable xhr protection on index : let the desktop load
		$this->disable_xhr_protection();

		$modules = array();
		include APPPATH . 'config/modules.php';
		$this->template['modules'] = $modules;

		$this->get('desktop/desktop');
	}


	// ------------------------------------------------------------------------


	/**
	 * Displays the backend header
	 *
	 */
	public function get_header()
	{
		$this->get('desktop/desktop_header');
	}


	// ------------------------------------------------------------------------


	/** 
	 * Gets a simple view
	 *
	 * @param	bool|string		View name, without extension
	 *
	 */
	public function get($view = FALSE)
	{
		$post = $this->input->post();

		if (is_array($post))
			$this->template = array_merge($this->template, $post);

		$this->template['view'] = $view;
		
		$args = func_get_args();
		$args = implode('/', $args);

		$this->disable_xhr_protection();
		$this->output($args);
	}


	// ------------------------------------------------------------------------


	/**
	 * Opens a help window
	 *
	 */
	public function help()
	{
		$table = $this->input->post('table');
		$title = $this->input->post('title');

		$this->load->model($table.'_model', '', TRUE);
		
		$this->template['data'] = $this->{$table.'_model'}->get_list();
		$this->template['table'] = $table;

		$this->template['title'] = $title;
		
		$this->output('desktop/help');
	}
}
