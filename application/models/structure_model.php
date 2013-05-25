<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Structure Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Structure
 * @author		Ionize Dev Team
 *
 */

class Structure_model extends Base_Model 
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
	 * @param null $id_menu
	 *
	 * @return array|bool
	 */
	function get($id_menu = NULL)
	{
		if ( ! is_null($id_menu))
			$this->{$this->db_group}->where('id_menu', $id_menu);
		
		$this->{$this->db_group}->order_by('page.ordering', 'ASC');
		
		$this->{$this->db_group}->select('page.*', FALSE);
		$this->{$this->db_group}->select('page_lang.title','page_lang.url');
		$this->{$this->db_group}->join('page_lang', 'page_lang.id_page = page.id_page', 'inner');			
		$this->{$this->db_group}->where('page_lang.lang', Settings::get_lang('default'));
		
		$query = $this->{$this->db_group}->get('page');

		if($query->num_rows() > 0)
			return $query->result_array();

		return FALSE;
	}


	// ------------------------------------------------------------------------


	function get_articles()
	{
		$data = array();
		
		// Get all articles
		$this->{$this->db_group}->order_by('page_article.ordering', 'ASC');
		
		$this->{$this->db_group}->select('article.*', FALSE);
		$this->{$this->db_group}->select('article_lang.title');
		$this->{$this->db_group}->select('page_article.*');
		$this->{$this->db_group}->select('article_type.id_type, article_type.type_flag');
		$this->{$this->db_group}->join('page_article', 'page_article.id_article = article.id_article', 'inner');			
		$this->{$this->db_group}->join('article_lang', 'article_lang.id_article = article.id_article', 'inner');			
		$this->{$this->db_group}->join('article_type', 'article_type.id_type = page_article.id_type', 'left outer');			
		$this->{$this->db_group}->where('article_lang.lang', Settings::get_lang('default'));

		$query = $this->{$this->db_group}->get('article');

		if($query->num_rows() > 0)
			$data = $query->result_array();

		return $data;
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Return the users menus, but not the 2 system ones : main and system.
	 * @param null $id_menu
	 *
	 * @return array
	 */
	function get_users_menus($id_menu = NULL)
	{
		$data = array();
		
		$this->{$this->db_group}->where_not_in('id_menu', array('1', '2'));
		$query = $this->{$this->db_group}->get('menu');
		
		if($query->num_rows() > 0)
			$data = $query->result_array();
			
		return $data;
	}
}
