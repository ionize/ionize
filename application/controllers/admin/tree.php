<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Tree Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.7
 */

class Tree extends MY_Admin {

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		// Helpers
		$this->load->helper('text_helper');
		
		// Models
        $this->load->model(
            array(
                'tree_model',
                'menu_model',
                'page_model',
                'article_model'
            ), '', TRUE);
	}

	
	/**
	 * Tree init.
	 * Displays the tree view, which will call each menu tree builder
	 *
	 */
	public function index()
	{
		// TODO : Limit the number of displayed articles in the tree
		// $nb_elements = $this->page_model->count_all() + $this->article_model->count_all();

		if ( Authority::can('access', 'admin/tree'))
		{
			// Menus : All menus
			$menus = $this->menu_model->get_list(array('order_by'=>'ordering ASC'));
	
			$this->template['menus'] = $menus;
			
			$this->output('tree/tree');
		}
	}


	/**
	 * Serves the tree browser
	 *
	 */
	public function browser()
	{
		// Menus : All menus
		$menus = $this->menu_model->get_list(array('order_by'=>'ordering ASC'));

		$this->template['menus'] = $menus;

		$this->output('tree/browser');
	}


	/**
	 * Gets one parent page / menu tree
	 *
	 */
	public function get()
	{
		// Parent page
		$id_parent = $this->input->post('id_parent');

		// Menu (optional, at startup)
		$id_menu = $this->input->post('id_menu');
		
		// Pages
		$pages = $this->tree_model->get_pages(array(
			'id_menu' => $id_menu,
			'id_parent' => $id_parent)
		);
		
		// Articles
		$articles = $this->tree_model->get_articles(array(
			'page_article.id_page' => $id_parent
			,'page.id_menu' => $id_menu
		));
		
		$this->response(array('pages' => $pages, 'articles' => $articles));
	}


	/**
	 * Returns JSON object of one entity
	 *
	 */
	public function get_entity()
	{
		// Contains ether a page dot article string, ether one page or article ID
		$rel = $this->input->post('rel');
		$type = $this->input->post('type');

		$rel = explode('.', $rel);
		$id_article = ! empty($rel[1]) ? $rel[1] : NULL;
		$id_page = $rel[0];

		// returned entity array
		$entity = array();
		$page = array();

		switch($type)
		{
			case 'article':
				if ($id_article)
				{
					// Get article
					$article = $this->article_model->get(
						array(
							'id_page' => $id_page,
							'id_article' => $id_article
						),
						Settings::get_lang('default')
					);

					// Get the corresponding page
					if ( ! empty($article))
					{
						$page = $this->page_model->get_by_id($id_page, Settings::get_lang('default'));
					}
					if (!empty($article) && !empty($page))
					{
						$entity = array
						(
							'page' =>  $page,
							'article' => $article
						);
					}
				}
				break;

			case 'page':
				$page = $this->page_model->get_by_id($id_page, Settings::get_lang('default'));
				$entity = array
				(
					'page' =>  $page
				);

				break;
		}
		$this->response($entity);

	}
}
