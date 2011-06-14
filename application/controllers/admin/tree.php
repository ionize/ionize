<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.7
 */

// ------------------------------------------------------------------------

/**
 * Ionize Tree Controller
 * Provides Ionizes Structure Tree
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Controllers
 * @author		Ionize Dev Team
 *
 */
class Tree extends MY_Admin {


	public function __construct()
	{
		parent::__construct();

		$this->connect->restrict('editors');

		// Helpers
		$this->load->helper('text_helper');
		
		// Models		
		$this->load->model('tree_model', '', true);
		$this->load->model('menu_model', '', true);
		$this->load->model('page_model', '', true);
		$this->load->model('article_model', '', true);
		
		// Librairies
//		$this->load->library('structure');
	}

	
	/**
	 * Tree init.
	 * Displays the tree view, which will call each menu tree builder
	 *
	 */
	function index()
	{
		$nb_elements = $this->page_model->count_all() + $this->article_model->count_all();
		
		// Activate only if nb_elements > x
//		if ($nb_elements > config_item(''))
		
		
		// Menus : All menus
		$menus = $this->menu_model->get_list(array('order_by'=>'ordering ASC'));

		$this->template['menus'] = $menus;
		
		$this->output('tree');

	}
	
	
	/**
	 * Gets one parent page / menu tree
	 *
	 */
	function get()
	{
		// Parent page
		$id_parent = $this->input->post('id_parent');

		// Menu (optional, at startup)
		$id_menu = $this->input->post('id_menu');
		
		// Pages
		$pages = $this->tree_model->get_pages(array('id_menu' => $id_menu, 'id_parent' => $id_parent));
		
		// Articles
		$articles = array();
		$articles = $this->tree_model->get_articles(array(
			'page_article.id_page' => $id_parent
			,'page.id_menu' => $id_menu
		));
		
		$this->response(array('pages' => $pages, 'articles' => $articles));
	}
}

/* End of file tree.php */
/* Location: ./application/admin/controllers/tree.php */