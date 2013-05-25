<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
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

class Tree_model extends Base_Model 
{

	/**
	 * Article Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();
	}


	// ------------------------------------------------------------------------


	/** 
	 * Return the menu items (pages) table
	 *
	 * @param 	array		SQL conditions
	 * @return 	array
	 *
	 */
	function get_pages($where)
	{
		$data = array();

		// Pages rules
		/*
		$rules = $this->get_group_concat_array(
			'id_element',
			array("resource like 'frontend/page%'"),
			'rule'
		);
		*/

		if (is_array($where) )
			$this->{$this->db_group}->where($where);
		
		$this->{$this->db_group}->order_by('page.ordering', 'ASC');
		
		$this->{$this->db_group}->select('page.*', false);
		$this->{$this->db_group}->select('page_lang.title,page_lang.nav_title,page_lang.url');
		$this->{$this->db_group}->join('page_lang', 'page_lang.id_page = page.id_page', 'inner');			

		$this->{$this->db_group}->where('page_lang.lang', Settings::get_lang('default'));
		
		$query = $this->{$this->db_group}->get('page');

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
	 * Get articles linked to one page
	 *
	 * @param	array	Array of pages
	 * @return	array
	 *
	 */
	function get_articles($where)
	{
		$data = array();
		
		// Do not return the articles linked to no page.
		if ($where['page_article.id_page'] != '0')
		{
			$this->{$this->db_group}->where($where);
		
			$this->{$this->db_group}->order_by('page_article.ordering', 'ASC');
		
			$this->{$this->db_group}->select('article.*', false);
			$this->{$this->db_group}->select('article_lang.title');
			$this->{$this->db_group}->select('page_article.*');
			$this->{$this->db_group}->select('article_type.id_type, article_type.type_flag, article_type.description as type_description');
			$this->{$this->db_group}->join('page_article', 'page_article.id_article = article.id_article', 'inner');			
			$this->{$this->db_group}->join('article_lang', 'article_lang.id_article = article.id_article', 'inner');			

			$this->{$this->db_group}->join('page', 'page_article.id_page = page.id_page', 'inner');			

			$this->{$this->db_group}->join('article_type', 'article_type.id_type = page_article.id_type', 'left outer');			
			$this->{$this->db_group}->where('article_lang.lang', Settings::get_lang('default'));

			$query = $this->{$this->db_group}->get('article');

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
	 *
	 * @param 	int	ID Menu
	 * @return 	array
	 *
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
