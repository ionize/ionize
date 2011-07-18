<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.7
 */

// ------------------------------------------------------------------------

/**
 * Ionize Tree Model
 * Used to display the structure tree (pages and articles tree)
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Structure
 * @author		Ionize Dev Team
 *
 */

class Tree_model extends CI_Model 
{

	/**
	 * Article Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}


	// ------------------------------------------------------------------------


	/** 
	 * Return the menu items (pages) table
	 *
	 * @param Array		SQL conditions
	 *
	 */
	function get_pages($where)
	{
		$data = array();

		if (is_array($where) )
			$this->db->where($where);
		
		$this->db->order_by('page.ordering', 'ASC');
		
		$this->db->select('page.*', false);
		$this->db->select('page_lang.title,page_lang.nav_title,page_lang.url');
		$this->db->join('page_lang', 'page_lang.id_page = page.id_page', 'inner');			
		$this->db->where('page_lang.lang', Settings::get_lang('default'));
		
		$query = $this->db->get('page');

		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
			
			// Some cleaning for tree
			foreach($data as &$p)
			{
				$p['title'] = strip_tags(html_entity_decode($p['title']));
				$p['nav_title'] = strip_tags(html_entity_decode($p['nav_title']));
			}			
		}

		return $data;
	}


	// ------------------------------------------------------------------------

	/**
	 *
	 * @param	Array	Array of pages
	 *
	 */
	function get_articles($where)
	{
		$data = array();
		
		// Do not return the articles linked to no page.
		if ($where['page_article.id_page'] != '0')
		{
			$this->db->where($where);
		
			$this->db->order_by('page_article.ordering', 'ASC');
		
			$this->db->select('article.*', false);
			$this->db->select('article_lang.title');
			$this->db->select('page_article.*');
			$this->db->select('article_type.id_type, article_type.type_flag');
			$this->db->join('page_article', 'page_article.id_article = article.id_article', 'inner');			
			$this->db->join('article_lang', 'article_lang.id_article = article.id_article', 'inner');			

			$this->db->join('page', 'page_article.id_page = page.id_page', 'inner');			

			$this->db->join('article_type', 'article_type.id_type = page_article.id_type', 'left outer');			
			$this->db->where('article_lang.lang', Settings::get_lang('default'));

			$query = $this->db->get('article');

			if($query->num_rows() > 0)
			{
				$data = $query->result_array();

				// Some cleaning for tree
				foreach($data as &$a)
				{
					$a['title'] = strip_tags(html_entity_decode($a['title']));
				}
			}
		}
		return $data;
	}

	// ------------------------------------------------------------------------


	/** 
	 * Return the users menus, but not the 2 system ones : main and system.
	 * @param $menu  Menu name
	 *
	 */
	function get_users_menus($id_menu = NULL)
	{
		$data = array();
		
		$this->db->where_not_in('id_menu', array('1', '2'));
		$query = $this->db->get('menu');
		
		if($query->num_rows() > 0)
			$data = $query->result_array();
			
		return $data;
	}	
	
}
/* End of file tree_model.php */
/* Location: ./application/models/tree_model.php */