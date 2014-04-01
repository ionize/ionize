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
	/**
	 * Page Article Context table
	 * @var string
	 */
	public $context_table =		'page_article';

	/**
	 * @var string
	 */
	public $url_table =			'url';


	// ------------------------------------------------------------------------


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
		
		$this->type_table = 'type';
		$this->extend_field_table = 'extend_field';
		$this->extend_fields_table = 'extend_fields';
	}


	// ------------------------------------------------------------------------

	
	/** 
	 * Get one page
	 *
	 * @param	string		where array
	 * @param	string		Optional. Lang code
	 * @return	array		array of media
	 *
	 */
	function get($where, $lang = NULL)
	{
		$data = $this->get_lang_list($where, $lang);

		if ( ! empty($data))
			return $data[0];

		return array();
	}


	// ------------------------------------------------------------------------


	/**
	 * Get one page by its ID
	 *
	 * @param      $id
	 * @param null $lang
	 *
	 * @return array
	 *
	 */
	public function get_by_id($id, $lang = NULL)
	{
		return $this->get(array('page.id_page' => $id), $lang);
	}


	// ------------------------------------------------------------------------


	/**
	 * @param null $where
	 * @param null $lang
	 *
	 * @return array
	 */
	public function get_lang_list($where = NULL, $lang = NULL)
	{
		// Order by ordering field
		$this->{$this->db_group}->order_by($this->table.'.level', 'ASC');
		$this->{$this->db_group}->order_by($this->table.'.ordering', 'ASC');

		// Filter on published
		$this->filter_on_published(self::$publish_filter, $lang);
		
		// Add Url to the request
		if ($lang == NULL)
			$lang = Settings::get_lang('default');

		// URL paths
		$this->{$this->db_group}->select('url.path, url.path_ids, url.full_path_ids');
		$this->{$this->db_group}->join(
			$this->url_table. ' as url',
			$this->table.".id_page = url.id_entity AND ".
			   "(".
					"url.type = 'page' AND ".
					"url.active = 1 AND ".
					"url.lang = '". $lang ."'".
			   ")",
		   'left'
		);

		// Lang URL paths
		$this->{$this->db_group}->select("group_concat(url2.path separator ';') as url_paths");
		$this->{$this->db_group}->select("group_concat(url2.lang separator ';') as url_langs");
		$this->{$this->db_group}->join(
			$this->url_table . ' as url2',
			$this->table.".id_page = url2.id_entity AND ".
				"(".
					"url2.type = 'page' AND ".
					"url2.active = 1 ".
				")",
			'left'
		);
		$this->{$this->db_group}->group_by($this->table.'.id_page');

		return parent::get_lang_list($where, $lang);
	}
	
	

	// ------------------------------------------------------------------------


	/** 
	 * Saves one Page
	 *
	 * @param	array		Page data table
	 * @param	array		Page Lang depending data table
	 *
	 * @return	int			The inserted / updated page ID
	 *
	 */
	public function save($data, $lang_data)
	{
		// Dates
		$data = $this->_set_dates($data);

		// Correct level regarding to the parent
		if (isset($data['id_parent']))
		{
			$parent_array = $this->get_parent_array($data['id_parent']);
			$data['level'] = count($parent_array);
		}

		// Correct child pages
		if ( ! empty($data['id_page']))
		{
			$page = $this->get_by_id($data['id_page']);
			if ($page['id_menu'] != $data['id_menu'])
			{
				$this->update_pages_menu($data['id_page'], $data['id_menu']);
			}
		}

		// Clean metas data
		$lang_data = $this->_clean_meta_data($lang_data);

		// Base model save method call
		return parent::save($data, $lang_data);
	}


	// ------------------------------------------------------------------------


	/**
	 * Updates one page children page menu ID value
	 *
	 * @param $id_page
	 * @param $id_menu
	 *
	 */
	public function update_pages_menu($id_page, $id_menu)
	{
		$sql = "update page
				set id_menu='".$id_menu."'
				where id_parent = '".$id_page."'
				";
		$this->{$this->db_group}->query($sql);

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
	public function update_links($page, $page_lang)
	{
		$id_page = 		$page['id_page'];
		$page_lang = 	$page_lang[Settings::get_lang('default')];
		$link_name = 	($page_lang['title'] != '') ? $page_lang['title'] : $page['name'];

		// Update of pages wich links to this page
		$this->{$this->db_group}->set('link', $link_name);
		$this->{$this->db_group}->where(
			array(
				'link_type' => 'page',
				'link_id' => $id_page
			)
		);
		$this->{$this->db_group}->update('page');
	
		// Update of pages (lang table) wich links to this page
		$sql = "update page_lang as pl
					inner join page as p on p.id_page = pl.id_page
					inner join page_lang as p2 on p2.id_page = p.link_id
				set pl.link = p2.url
				where p.link_type='page'
				and pl.lang = p2.lang
				and p.link_id = " . $id_page;

		$this->{$this->db_group}->query($sql);
	
		// Update of articles which link to this page
		$this->{$this->db_group}->set('link', $link_name);
		$this->{$this->db_group}->where(
			array(
				'link_type' => 'page',
				'link_id' => $id_page
			)
		);
		$this->{$this->db_group}->update('page_article');
	}


	// ------------------------------------------------------------------------


	/**
	 * Updates the home page
	 * Set all pages home value to 0 except the passed page ID
	 *
	 * @param bool/int $id_page
	 *
	 * @return int
	 */
	public function update_home_page($id_page=FALSE)
	{
		if ($id_page !== FALSE)
		{
			$data = array(
				'home' => 0
			);
			$this->{$this->db_group}->where($this->pk_name.' !=', $id_page);
			
			$num_rows = $this->{$this->db_group}->update($this->table, $data);
			
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
	public function delete($id)
	{
		$affected_rows = 0;
		
		// Check if page exists
		if( $this->exists(array($this->pk_name => $id)) )
		{
			// Page delete
			$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id)->delete($this->table);
			
			// Lang
			$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id)->delete($this->lang_table);
	
			// Articles : Delete link between page and articles
			$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id)->delete('page_article');

			// Linked medias
			$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id)->delete($this->table.'_media');
			
			// Sub-pages to parent 0 (root) and menu 0 (orphan)
			$data = array(
				'id_parent' => 0,
				'id_menu' => 0
			);
			$affected_rows += $this->{$this->db_group}->where('id_parent', $id)->update($this->table, $data);
			
			// URLs
			$where = array(
				'type' => 'page',
				'id_entity' => $id
			);
			$affected_rows += $this->{$this->db_group}->where($where)->delete('url');
		}
		
		return $affected_rows;
	}

	 
	// ------------------------------------------------------------------------


	/**
	 * Returns groups as simple array
	 * used to feed selectbox of groups
	 *
	 */
	public function get_roles_select()
	{
		return $this->get_items_select(
			'role',
			'role_name',
			NULL,
			NULL,
			lang('ionize_select_everyone'),
			'role_level DESC'
		);
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the current groups from parent element
	 *
	 * @param $parent_id
	 *
	 * @return array
	 */
	public function get_current_groups($parent_id)
	{
		return $this->get_joined_items_keys('user_groups', $this->table, $parent_id);
	}


	// ------------------------------------------------------------------------


	/** 
	 * Returns one page parents array
	 *
	 * @param	int		Page ID
	 * @param	array	Empty data array.
	 * @param	string	Lang code
	 *
	 * @return	array	Parent array
	 */
	public function get_parent_array($id_page, $data = array(), $lang = NULL)
	{
		$result = $this->get_by_id($id_page, $lang);

		if (isset($result['id_parent']) && $result['id_parent'] != 0 )
		{
			$data = $this->get_parent_array($result['id_parent'], $data, $lang);
		}
		
		if ( ! empty($result))
			$data[] = $result;

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns one page children array
	 *
	 * @param	int		Page ID
	 * @param	array	Empty data array.
	 * @param	string	Lang code
	 *
	 * @return	array	Children pages array
	 */
	public function get_children_array($id_page, $data = array(), $lang = NULL)
	{
		$children = $this->get_lang_list(array('id_parent' => $id_page), $lang);

		if ( ! empty($children))
		{
			foreach($children as $page)
			{
				$data = array_merge($this->get_children_array($page['id_page'], $data, $lang), $data);
			}
		}

		if ( ! empty($children))
			$data = array_merge($children, $data);

		return $data;
	}


	public function get_children_ids($id_page, $including_id_page=FALSE)
	{
		$ids = $including_id_page == TRUE ? array($id_page) : array();

		$children = $this->get_children_array($id_page);

		foreach($children as $page)
			$ids[] = $page['id_page'];

		return $ids;
	}


	/**
	 * @param        $id_page
	 * @param string $separator
	 * @param null   $lang
	 *
	 * @return string
	 */
	public function get_breadcrumb_string($id_page, $separator=' > ', $lang=NULL)
	{
		if ( is_null($lang))
			$lang = Settings::get_lang('default');

		$pages = $this->get_parent_array($id_page, array(), $lang);

		$breadcrump = array();
		foreach($pages as $page)
		{
			$breadcrump[] = ( ! empty($page['title'])) ? $page['title'] : $page['name'];
		}

		return implode($separator, $breadcrump);
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves one page URLs paths
	 *
	 * @param	int		Page id
	 * @return	int		Number of inserted / updated Urls
	 *
	 */
	public function save_urls($id_page)
	{
		$CI =& get_instance();
		$CI->load->model('url_model', '', TRUE);
		
		$nb = 0;

		// Clean old URL
		$this->url_model->delete('page', $id_page);

		// Asked page process
		foreach($this->get_languages() as $l)
		{
			$parents_array = $this->get_parent_array($id_page, array(), $l['lang']);

			$url = array();

			// Full path IDs to the article
			$full_path_ids = array();
				
			// Path IDs to the article (does not include page which hasn't one URL)
			$path_ids = array();
			
			foreach($parents_array as $page)
			{
				$full_path_ids[] = $page['id_page'];

				if ($page['has_url'] == '1')
				{
					$url[] = $page['url'];
					$path_ids[] = $page['id_page'];
				}
			}
			
			if ( ! empty($url))
			{
				$data = array(
					'url' => implode('/', $url),
					'path_ids' => implode('/', $path_ids),
					'full_path_ids' => implode('/', $full_path_ids)
				);
				
				$nb = $CI->url_model->save_url('page', $l['lang'], $id_page, $data);
			}
		}
		
		// Articles linked to this page process
		$this->save_linked_articles_urls($id_page);
		
		// Process childs
		$childs = $this->get_list(array('id_parent'=> $id_page));
		foreach($childs as $page)
		{
			$nb += $this->save_urls($page['id_page']);
		}

		return $nb;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $id_page
	 */
	public function save_linked_articles_urls($id_page)
	{
        // Models
		$CI =& get_instance();
        $CI->load->model(
            array(
                'article_model',
                'url_model'
            ), '', TRUE);

		$articles = $this->get_list(array('id_page' => $id_page, 'main_parent' => '1'), 'page_article');
		
		foreach($articles as $article)
		{
			// Clean old URL
			$CI->url_model->delete('article', $article['id_article']);

			$CI->article_model->save_urls($article['id_article']);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Rebuild all the Url of all / one page
	 * If no page id is given, rebuilds all the URLs
	 *
	 * @param	int		Optional. Page id
	 * @return	int		Number of inserted / updated Urls
	 *
	 */
	public function rebuild_urls($id_page = NULL)
	{
		$nb = 0;

		if ( ! is_null($id_page))
		{
			$nb = $this->save_urls($id_page);
		}
		else
		{
			$pages = $this->get_list(array('id_parent' => '0'));

			foreach($pages as $page)
			{
				$nb += $this->save_urls($page['id_page']);
			}
		}
		
		return $nb;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all contexts page's lang data as an array of pages.
	 *
	 * @param	Mixed		ID of one article / Array of articles IDs
	 * @param	string		Lang code
	 *
	 * @return	array		Array of articles
	 *
	 */
	public function get_lang_contexts($id_article, $lang)
	{
		$data = array();
		
		if ( ! empty($id_article))
		{
			$this->{$this->db_group}->select($this->table.'.*');
			$this->{$this->db_group}->select($this->lang_table.'.*');
			$this->{$this->db_group}->select($this->context_table.'.*');
	
			$this->{$this->db_group}->join($this->lang_table, $this->table.'.'.$this->pk_name.' = ' .$this->lang_table.'.'.$this->pk_name);			
			$this->{$this->db_group}->join($this->context_table, $this->table.'.'.$this->pk_name.' = ' .$this->context_table.'.'.$this->pk_name);			

			// Join to URL
			$this->{$this->db_group}->select('url.path');
			$this->{$this->db_group}->join('url', $this->table.'.'.$this->pk_name.' = url.id_entity', 'inner');			
			$this->{$this->db_group}->where(array('url.type' => 'page', 'active' => 1));

			if ( ! is_null($lang))
				$this->{$this->db_group}->where(array('url.lang' => $lang));			

			$this->{$this->db_group}->where(array($this->lang_table.'.lang' => $lang));
			
			if ( ! is_array($id_article) )
				$this->{$this->db_group}->where(array($this->context_table.'.id_article' => $id_article));
			else
				$this->{$this->db_group}->where($this->context_table.'.id_article in (' . implode(',', $id_article) . ')');
	
			$query = $this->{$this->db_group}->get($this->table);
	
			if($query->num_rows() > 0)
			{
				$data = $query->result_array();
			}
		}
		return $data;
	}


	// ------------------------------------------------------------------------


	/** 
	 * Spread authorizations from parents to children pages
	 *
	 * @param	array	By ref. The pages array
	 * @param	int		The current parent page ID
	 *
	public function spread_authorizations(&$pages, $id_parent=0)
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
	 */


	public function spread_authorizations(&$pages, $id_parent=0)
	{
		if ( ! empty($pages))
		{
			$children = array_filter($pages, create_function('$row','return $row["id_parent"] == "'. $id_parent .'";'));

			foreach ($children as $key=>$child)
			{
				$resource = 'frontend/page/' . $child['id_page'];

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
	 * @param bool $on
	 * @param null $lang
	 */
	protected function filter_on_published($on = TRUE, $lang = NULL)
	{
		if ($on === TRUE)
		{
			$this->{$this->db_group}->where($this->table.'.online', '1');
	
			if ($lang !== NULL && count(Settings::get_online_languages()) > 1)
				$this->{$this->db_group}->where($this->lang_table.'.online', '1');		
	
			$this->{$this->db_group}->where('((publish_off > ', 'now()', FALSE);
			$this->{$this->db_group}->or_where('publish_off = ', '0)' , FALSE);
		
			$this->{$this->db_group}->where('(publish_on < ', 'now()', FALSE);
			$this->{$this->db_group}->or_where('publish_on = ', '0))' , FALSE);
		}	
	}


	// ------------------------------------------------------------------------


	/**
	 * Cleans the meta_keywords and meta_description and returns the cleaned data array
	 *
	 * @param $data
	 *
	 * @return mixed
	 *
	 */
	protected function _clean_meta_data($data)
	{
		foreach($data as $lang => $row)
		{
			foreach($row as $key => $value)
			{
				if ($key == 'meta_description')
					$data[$lang][$key] = preg_replace('[\"]', '', $value);

				if ($key == 'meta_keywords')
					$data[$lang][$key] = preg_replace('/[\"\.;]/i  ', '', $value);
			}
		}
		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Sets the correct dates to the page array
	 *
	 * @param $data
	 *
	 * @return array
	 */
	protected function _set_dates($data)
	{
		$data['publish_on'] = (isset($data['publish_on']) && $data['publish_on']) ? getMysqlDatetime($data['publish_on'], Settings::get('date_format')) : '0000-00-00';
		$data['publish_off'] = (isset($data['publish_off']) && $data['publish_off']) ? getMysqlDatetime($data['publish_off'], Settings::get('date_format')) : '0000-00-00';
		$data['logical_date'] = (isset($data['logical_date']) && $data['logical_date']) ? getMysqlDatetime($data['logical_date'], Settings::get('date_format')) : '0000-00-00';

		// Creation date
		if( ! $data['id_page'] OR $data['id_page'] == '' )
			$data['created'] = date('Y-m-d H:i:s');
		// Update date
		else
			$data['updated'] = date('Y-m-d H:i:s');

		return $data;
	}
}
