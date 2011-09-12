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
 * @category	Admin settings
 * @author		Ionize Dev Team
 *
 */

class Page_model extends Base_model 
{

	public $context_table =	'page_article';


	/**
	 * Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('page');
		$this->set_pk_name('id_page');
		$this->set_lang_table('page_lang');
		
		$this->extend_field_table = 'extend_field';
		$this->extend_fields_table = 'extend_fields';
		
	}


	// ------------------------------------------------------------------------


	function get_lang_list($where=false, $lang=NULL)
	{
		// Order by ordering field
		$this->db->order_by($this->table.'.level', 'ASC');
		$this->db->order_by($this->table.'.ordering', 'ASC');

		// Filter on published
		$this->filter_on_published(self::$publish_filter, $lang);

		return parent::get_lang_list($where, $lang);
	}


	// ------------------------------------------------------------------------


	/** 
	 * Saves one Page
	 *
	 * @param	array		Page data table
	 * @param	array		Page Lang depending data table
	 *
	 * @return	string		The inserted / updated page ID
	 *
	 */
	function save($data, $lang_data)
	{	
		// Dates
		$data['publish_on'] = ($data['publish_on']) ? getMysqlDatetime($data['publish_on']) : '0000-00-00';
		$data['publish_off'] = ($data['publish_off']) ? getMysqlDatetime($data['publish_off']) : '0000-00-00';
		$data['logical_date'] = ($data['logical_date']) ? getMysqlDatetime($data['logical_date']) : '0000-00-00';

		// Creation date
		if( ! $data['id_page'] OR $data['id_page'] == '' )
		{
			$data['created'] = date('Y-m-d H:i:s');
		}
		// Update date
		else
		{
			$data['updated'] = date('Y-m-d H:i:s');			
		}

		// Be sure URLs are unique
		$this->set_unique_urls($lang_data, $data['id_page']);


		// Clean metas data
		foreach($lang_data as $lang => $row)
		{
			foreach($row as $key => $value)
			{
				if ($key == 'meta_description')
					$lang_data[$lang][$key] = preg_replace('[\"]', '', $value);

				if ($key == 'meta_keywords')
					$lang_data[$lang][$key] = preg_replace('/[\"\.;]/i  ', '', $value);
			}
		}

		// Base model save method call
		return parent::save($data, $lang_data);
	}


	// ------------------------------------------------------------------------


	/**
	 * Calls all integrity corrections functions
	 *
	 * @param	array		Article array
	 * @param	array		Article lang data array
	 *
	 */
	function correct_integrity($page, $page_lang)
	{
		$this->update_links($page, $page_lang);
		
		$this->update_pages_menu($page['id_page'], $page['id_menu']);
	}


	// ------------------------------------------------------------------------

	
	function update_pages_menu($id_page, $id_menu)
	{
		$sql = "update page
				set id_menu='".$id_menu."'
				where id_parent = '".$id_page."'
				";
		$this->db->query($sql);

		// Get childs and start again
		$childs = $this->get_list(array('id_parent' => $id_page));
		
		if ( ! empty($childs))
		{
			foreach($childs as $child)
			{
				$this->update_pages_menu($child['id_page'], $id_menu);
			}
		}
	}

	
	// ------------------------------------------------------------------------


	/**
	 * Updates all other articles / pages links when saving one page
	 *
	 * @param	array		Article array
	 * @param	array		Article lang data array
	 *
	 */
	function update_links($page, $page_lang)
	{
		$id_page = 		$page['id_page'];
		$page_lang = 	$page_lang[Settings::get_lang('default')];
		$link_name = 	($page_lang['title'] != '') ? $page_lang['title'] : $page['name'];

		// Update of pages wich links to this page
		$this->db->set('link', $link_name);
		$this->db->where(
			array(
				'link_type' => 'page',
				'link_id' => $id_page
			)
		);
		$this->db->update('page');
	
		// Update of pages (lang table) wich links to this page
		$sql = "update page_lang as pl
					inner join page as p on p.id_page = pl.id_page
					inner join page_lang as p2 on p2.id_page = p.link_id
				set pl.link = p2.url
				where p.link_type='page'
				and pl.lang = p2.lang
				and p.link_id = " . $id_page;

		$this->db->query($sql);
	
		// Update of articles which link to this page
		$this->db->set('link', $link_name);
		$this->db->where(
			array(
				'link_type' => 'page',
				'link_id' => $id_page
			)
		);
		$this->db->update('page_article');
		

		// Update of articles (lang table) which link to this page
		/*
		$sql = "update article_lang as al
					inner join article as a on a.id_article = al.id_article
					inner join page_lang as p on p.id_page = a.link_id
				set al.link = p.url
				where a.link_type='page'
				and al.lang = p.lang
				and a.link_id = " . $id_page;
		
		$this->db->query($sql);
		*/
	}


	// ------------------------------------------------------------------------


	/**
	 * Updates the home page
	 * Set all pages home value to 0 except the passed page ID
	 *
	 * @param	Int		Page ID to exclude
	 * @returns	Int		Nuber of affected rows
	 *
	 */
	function update_home_page($id_page=false)
	{
		if ($id_page !== false)
		{
			$data = array(
				'home' => 0
			);
			$this->db->where($this->pk_name.' !=', $id_page);
			
			$num_rows = $this->db->update($this->table, $data);
			
			return $num_rows;
		}
		
		return 0;
	}


	// ------------------------------------------------------------------------


	/** 
	 * Delete page and all linked articles
	 *
	 * also delete all joined element from join tables
	 *
	 * @param	int 	Page ID
	 *
	 * @return 	int		Affected rows number
	 */
	function delete($id)
	{
		$affected_rows = 0;
		
		// Check if page exists
		if( $this->exists(array($this->pk_name => $id)) )
		{
			// Page delete
			$affected_rows += $this->db->where($this->pk_name, $id)->delete($this->table);
			
			// Lang
			$affected_rows += $this->db->where($this->pk_name, $id)->delete($this->lang_table);
	
			// Articles : Delete link between page and articles
			$affected_rows += $this->db->where($this->pk_name, $id)->delete('page_article');

			// Linked medias
			$affected_rows += $this->db->where($this->pk_name, $id)->delete($this->table.'_media');
			
			// Sub-pages to parent 0 (root) and menu 0 (orphan)
			$data = array(
				'id_parent' => 0,
				'id_menu' => 0
			);
			$affected_rows += $this->db->where('id_parent', $id)->update($this->table, $data);
			
		}
		
		return $affected_rows;
	}

	 
	// ------------------------------------------------------------------------


	/**
	 * Returns groups as simple array
	 * used to feed selectbox of groups
	 *
	 */
	function get_groups_select()
	{
		return $this->get_items_select('user_groups', 'group_name', lang('ionize_select_everyone'), 'level DESC');
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the current groups from parent element
	 *
	 * @param	string	parent name
	 * @param	int		parent ID
	 *
	 */
	function get_current_groups($parent_id)
	{
		return $this->get_joined_items_keys('user_groups', $this->table, $parent_id);
	}


	// ------------------------------------------------------------------------


	/** 
	 * Returns one page parents array
	 *
	 * @param	string	Page ID
	 * @param	array	Empty data array.
	 *
	 * @return	array	Parent array
	 */
	function get_parent_array($id_page, $data=array(), $lang = FALSE)
	{
		$result = $this->get($id_page, $lang);

		if (isset($result['id_parent']) && $result['id_parent'] != 0 )
		{
			$data = $this->get_parent_array($result['id_parent'], $data, $lang);
		}
		
		if (!empty($result))
		{
			/*
			$data[] = array(
				'id_page'	=> $result['id_page'],
				'name' => $result['name']
			);
			*/
			$data[] = $result;
		}					
		
		return $data;
	}


	/**
	 * Returns all contexts page's lang data as an array of pages.
	 *
	 * @param	Mixed		ID of one article / Array of articles IDs
	 * @param	string		Lang code
	 *
	 * @return	array		Array of articles
	 *
	 */
	function get_lang_contexts($id_article, $lang)
	{
		$data = array();
		
		if ( ! empty($id_article))
		{
			$this->db->select($this->table.'.*');
			$this->db->select($this->lang_table.'.*');
			$this->db->select($this->context_table.'.*');
	
			$this->db->join($this->lang_table, $this->table.'.'.$this->pk_name.' = ' .$this->lang_table.'.'.$this->pk_name);			
			$this->db->join($this->context_table, $this->table.'.'.$this->pk_name.' = ' .$this->context_table.'.'.$this->pk_name);			
	
			$this->db->where(array($this->lang_table.'.lang' => $lang));
			
			if ( ! is_array($id_article) )
				$this->db->where(array($this->context_table.'.id_article' => $id_article));
			else
				$this->db->where($this->context_table.'.id_article in (' . implode(',', $id_article) . ')');
	
			$query = $this->db->get($this->table);
	
			if($query->num_rows() > 0)
			{
				$data = $query->result_array();
			}
		}
		return $data;
	}


	// ------------------------------------------------------------------------


	/** 
	 * Spread autorisations from parents to children pages
	 *
	 * @param	array	By ref. The pages array
	 * @param	int		The current parent page ID
	 *
	 */
	function spread_authorizations(&$pages, $id_parent=0)
	{
		if ( ! empty($pages))
		{
			$children = array_filter($pages, create_function('$row','return $row["id_parent"] == "'. $id_parent .'";'));
			
			foreach ($children as $key=>$child)
			{		
				if ($id_parent != 0)
				{
					// Get the parent page
					$parent = array_values(array_filter($pages, create_function('$row','return $row["id_page"] == "'. $id_parent .'";')));
					$parent = $parent[0];
					
					// Set authorization group from parent to child in the ref pages array
					$pages[$key]['id_group'] = $parent['id_group'];
				}				
				
				$this->spread_authorizations($pages, $child['id_page']);
			}
		}	
	}


	// ------------------------------------------------------------------------


	/** 
	 * Filters the pages on published one
	 *
	 */
	protected function filter_on_published($on = true, $lang = NULL)
	{
		if ($on === true)
		{
			$this->db->where($this->table.'.online', '1');		
	
			if ($lang !== NULL && count(Settings::get_online_languages()) > 1)
				$this->db->where($this->lang_table.'.online', '1');		
	
			$this->db->where('((publish_off > ', 'now()', false);
			$this->db->or_where('publish_off = ', '0)' , false);
		
			$this->db->where('(publish_on < ', 'now()', false);
			$this->db->or_where('publish_on = ', '0))' , false);
		}	
	}

}
/* End of file page_model.php */
/* Location: ./application/models/page_model.php */