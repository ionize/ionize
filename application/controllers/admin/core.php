<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * Core controller
 * Basic Ionize functions
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

class Core extends MY_Admin {


	public function __construct()
	{
		parent::__construct();
	}


	function index()
	{
        // By default, the controller will send the user to the login screen
		$this->login();
	}
	
	
	/**
	 * Gets the website current structure and ouput it as a tree
	 *
	 */
	function get_structure()
	{
		// Text Helper
		$this->load->helper('text_helper');
		

        // Models
        $this->load->model(
            array(
                'menu_model',
                'structure_model',
                'article_model'
            ), '', TRUE);

		
		// Structure librairy
		$this->load->library('structure');

		// Get all articles from DB
		$articles = $this->structure_model->get_articles();

		// Menus : All menus
		$menus = $this->menu_model->get_list(array('order_by'=>'ordering ASC'));

		foreach($menus as &$menu)
		{
			$menu['items'] = array();
			$menu_items = $this->structure_model->get($menu['id_menu']);

			$this->structure->get_nested_structure($menu_items, $menu['items'], 0,	0, -1, $articles);
		}
		
		$this->template['menus'] = $menus;
		
		$this->output('structure');
	}
	

	/**
	 * Get main informations about settings
	 * Used during developement
	 *
	 *
	 */
	function get_info()
	{
		// $this->output('info');
	}
}
