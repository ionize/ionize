<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Page Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Structure
 * @author		Ionize Dev Team
 *
 */

class Structure_model extends CI_Model 
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
	 * @param Integer	Menu ID
	 *
	 */
	function get($id_menu = NULL)
	{
		if ( ! is_null($id_menu))
			$this->db->where('id_menu', $id_menu);
		
		$this->db->order_by('page.ordering', 'ASC');
		
		$this->db->select('page.*', false);
		$this->db->select('page_lang.title','page_lang.url');
		$this->db->join('page_lang', 'page_lang.id_page = page.id_page', 'inner');			
		$this->db->where('page_lang.lang', Settings::get_lang('default'));
		
		$query = $this->db->get('page');

		if($query->num_rows() > 0)
			return $query->result_array();

		return false;
	}


	// ------------------------------------------------------------------------


	function get_articles()
	{
		$data = array();
		
		// Get all articles
		$this->db->order_by('page_article.ordering', 'ASC');
		
		$this->db->select('article.*', false);
		$this->db->select('article_lang.title');
		$this->db->select('page_article.*');
		$this->db->select('article_type.id_type, article_type.type_flag');
		$this->db->join('page_article', 'page_article.id_article = article.id_article', 'inner');			
		$this->db->join('article_lang', 'article_lang.id_article = article.id_article', 'inner');			
		$this->db->join('article_type', 'article_type.id_type = page_article.id_type', 'left outer');			
		$this->db->where('article_lang.lang', Settings::get_lang('default'));

		$query = $this->db->get('article');

		if($query->num_rows() > 0)
			$data = $query->result_array();

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
/* End of file structure_model.php */
/* Location: ./application/models/structure_model.php */