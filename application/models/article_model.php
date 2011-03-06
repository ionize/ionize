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
 * Ionize Article Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Article
 * @author		Ionize Dev Team
 *
 */

class Article_model extends Base_model 
{

	public $category_table = 			'category';
	public $category_lang_table = 		'category_lang';
	public $article_category_table = 	'article_category';
	public $type_table = 				'article_type';
	public $page_table =				'page';
	public $page_lang_table =			'page_lang';
	public $parent_table =				'page_article';

	/* Contains table name wich should be used for each filter get.
	 * Purpose : Avoid Ambiguous SQL quey when 2 fields have the same name.
	 * ex : 'title' in category and 
	 *
	 */
	private $filter_field_ref = array(
		'title' => 'article_lang',
		'view' => 'page_article'
	);

	/**
	 * Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('article');
		$this->set_pk_name('id_article');
		$this->set_lang_table('article_lang');
	}


	// ------------------------------------------------------------------------


	/**
	 * Get array of articles
	 * 
	 * For each article, set the article date.
	 * The article date can be the creation date or the publish_on date if exists
	 *
	 * @access	public
	 * @param 	array	An associative array
	 * @param 	string	order_by field name
	 * @param 	boolean		Limit value
	 * @return	array	Array of records
	 *
	 */
	function get_list($where = false, $orderby = null, $limit=false)
	{
		$data = array();

		$this->db->select($this->lang_table.'.*');
		$this->db->join($this->lang_table, $this->lang_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table, 'inner');			
		$this->db->where($this->lang_table.'.lang', Settings::get_lang('default'));
		
		$data = parent::get_list($where, $orderby, $limit);
		
		// Set the correct publish date
		foreach ($data as $key=>$row)
		{
			$data[$key]['date']	= (isDate($row['publish_on'])) ? $row['publish_on'] : $row['created'];
		}
		
		return $data;
	}


	// ------------------------------------------------------------------------


	/** 
	 * Get one element
	 * @updated		04,09,2009
	 *
	 * @param	array	Where array
	 * @param	string	Lang code. Optional
	 * @return	array	Data array
	 *
	 */
	function get($where, $lang = NULL) 
	{
		$data = parent::get($where, $lang);

		// Set the correct publish date
		if ( !empty($data))
		{
			$data['date']	= (isDate($data['publish_on'])) ? $data['publish_on'] : $data['created'];
		}
		
		// Add context data
		
		return $data;
	}


	// ------------------------------------------------------------------------


	/** 
	 * Get article list with lang data
	 * Used by front-end to get the posts with lang data
	 *
	 */
	function get_lang_list($where=false, $lang=NULL, $limit=false, $like=false, $order_by=false, $filter=false)
	{
		$data = array();
	
		// Article ordering
		if ( $order_by === false )
		{
			$this->db->orderby($this->parent_table.'.ordering', 'ASC');
		}
		else
		{
			$order_settings = explode(' ', $order_by);
			$order_direction = (isset($order_settings[1]) && $order_settings[1] == 'DESC') ? 'DESC' : 'ASC';
			$order_field = $order_settings[0];
			
			if ($order_field == 'ordering')
			{
				$this->db->orderby($this->parent_table.'.'.$order_field, $order_direction);
			}
			else
			{
				$this->db->orderby($order_field, $order_direction);
			}
		}
		
		// Add the SQL publish filter if mandatory
		$this->filter_on_published(self::$publish_filter, $lang);

		// Filter on users filter
		if ( $filter !== false)
			$this->_set_filter($filter);

		// Add the 'date' field to the query
		$this->db->select('IF(publish_on !=0, publish_on, created ) AS date');


		// Query from base_model
//		$data = parent::get_lang_list($where, $lang, $limit, $like);


		// Make sure we have only one time each element
		$this->db->distinct();

		// Lang data
		if ( ! is_null($lang) || ! $lang == false)
		{
			$this->db->select($this->lang_table.'.*');
			$this->db->join($this->lang_table, $this->lang_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table, 'inner');			
			$this->db->where($this->lang_table.'.lang', $lang);
		}


		// Main data select						
		$this->db->select($this->table.'.*', false);

		// Join table select
		$this->db->select($this->parent_table.'.*', false);
		$this->db->join($this->parent_table, $this->parent_table.'.id_article = ' .$this->table.'.id_article', 'inner');
		
		// Add Type to query
		$this->db->select($this->type_table.'.type');
		$this->db->join($this->type_table, $this->parent_table.'.id_type = ' .$this->type_table.'.id_type', 'left');

		// Limit ?
		if ($limit !== false)
			(is_array($limit)) ? $this->db->limit($limit[1], $limit[0]) : $this->db->limit($limit);

		// Where ?
		if (is_array($where) )
		{
			foreach ($where as $key => $value)
			{
				// id_page must be searched in page_article table
				if (strpos($key, 'id_page') !== FALSE)
					$key = str_replace('id_page', $this->parent_table.'.id_page', $key);
				
				$protect = true;
				$null = false;

				if (in_array(substr($key, -2), array('in', 'is')) )
				{
					$protect = false;
				}
				// NULL value : Create an "where value is NULL" constraint
				if ($value == 'NULL')
				{
					$this->db->where($key. ' IS NULL', NULL, FALSE);
				}
				else
				{
					if (strpos($key, '.') > 0)
					{
						$this->db->where($key, $value, $protect);			
					}
					else
					{
						$this->db->where($this->table.'.'.$key, $value, $protect);
					}
				}
			}
		}
		
		// Like ?
		if ($like)
			$this->db->like($like);
		
		$query = $this->db->get($this->table);

		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
			$query->free_result();

			// Add linked medias to the "media" index of the data array		
			if (in_array($this->table, $this->with_media_table))
				$this->add_linked_media($data, $this->table, $lang);
					
			// Add extended fields if necessary
			$this->add_extend_fields($data, $this->table, $lang);
			
			// Add URLs for each language
			if ($this->table == 'page' OR $this->table == 'article')
				$this->add_lang_urls($data, $this->table, $lang);
		}

		// Add Categories to each article
		$categories = $art_cat = array();
		
		$this->db->join($this->category_lang_table, $this->category_table.'.id_category = ' .$this->category_lang_table.'.id_category', 'left');
		if ( ! is_null($lang))
		{
			$this->db->where($this->category_lang_table.'.lang', $lang);
		}
		$query = $this->db->get($this->category_table);
		
		if($query->num_rows() > 0)
		{
			$categories = $query->result_array();

			// Get categories articles table content
			$query = $this->db->get($this->article_category_table);
			
			// table of links between articles and categories
			if($query->num_rows() > 0) $art_cat = $query->result_array();			
		}

		// Add entry to each data array element
		foreach ($data as &$article)
		{
			$article['categories'] = array();
			if ( ! empty($categories))
			{
				foreach($art_cat as $cat)
				{
					if($article['id_article'] == $cat['id_article'])
						$article['categories'] = array_merge($article['categories'], array_filter($categories, create_function('$row', 'return $row["id_category"] == "'. $cat['id_category'] .'";')));
				}
			}
		}
		
		return $data;
	}

	// ------------------------------------------------------------------------


	/** 
	 * Get one article parent pages list
	 *
	 * @param 	int		Article's ID
	 *
	 */
	function get_pages_list($id_article)
	{
		$data = array();
	
		$this->db->where($this->pk_name, $id_article);
		
		// Order by ordering field
//		$this->db->orderby($this->parent_table.'.ordering', 'ASC');

		// Page table data
		$this->db->join($this->page_table, $this->parent_table.'.id_'.$this->page_table.' = ' .$this->page_table.'.id_'.$this->page_table, 'left');
		
		// Lang data
		$this->db->select($this->page_lang_table.'.*');
		$this->db->join($this->page_lang_table, $this->page_lang_table.'.id_'.$this->page_table.' = ' .$this->page_table.'.id_'.$this->page_table, 'inner');			
		$this->db->where($this->page_lang_table.'.lang', Settings::get_lang('default'));
		
		// Join table data
		$this->db->select($this->page_table.'.*');
		$this->db->select($this->parent_table.'.*');

		$query = $this->db->get($this->parent_table);
		
		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
			$query->free_result();
		}
		
		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns article's context data for a givven page
	 *
	 */
	function get_context($id_article, $id_page, $lang = NULL)
	{
		$data = array();
		
		$lang = (is_null($lang)) ? Settings::get_lang('default') : $lang;
		
		$this->db->select($this->table.'.*');
		$this->db->select($this->lang_table.'.*');
		$this->db->select($this->parent_table.'.*');

		$this->db->join($this->lang_table, $this->table.'.'.$this->pk_name.' = ' .$this->lang_table.'.'.$this->pk_name, 'inner');			
		$this->db->join($this->parent_table, $this->table.'.'.$this->pk_name.' = ' .$this->parent_table.'.'.$this->pk_name, 'inner');			
		$this->db->where(array($this->lang_table.'.lang' => $lang));
		$this->db->where(array($this->table.'.'.$this->pk_name => $id_article, $this->parent_table.'.id_page' => $id_page));

		$query = $this->db->get($this->table);

		if($query->num_rows() > 0)
		{
			$data = $query->row_array();
			$query->free_result();
		}

		return $data;
	}


	// ------------------------------------------------------------------------


	function get_all_context($id_article = NULL)
	{
		$data = array();

		if ( ! is_null($id_article))
		{
			$this->db->where('id_article', $id_article);
		}

		$query = $this->db->get($this->parent_table);

		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
		}
		
		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Add lang content to each article in the article list.
	 * This function is used for backend
	 *
	 * @param	Array	by ref. Array of articles
	 *
	 */
	function add_lang_data(&$articles = array())
	{
		// Add lang content to each article
		$articles_lang = $this->get_lang();
		
		foreach($articles as &$article)
		{
			$article['langs'] = array();
	
			$langs = array_values(array_filter($articles_lang, create_function('$row','return $row["id_article"] == "'. $article['id_article'] .'";')));
		
			foreach(Settings::get_languages() as $lang)
			{
				
				$article['langs'][$lang['lang']] = array_pop(array_filter($langs, create_function('$row','return $row["lang"] == "'. $lang['lang'] .'";')));
			}
		}
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Add view logical name to the article list.
	 * This function is used for backend
	 *
	 * @param	Array	by ref. Array of articles
	 * @param	Array	Array of views, as set in /themes/<the_theme>/config/views.php
	 *
	 */
	function add_view_name(&$articles = array(), $views)
	{
		foreach ($articles as &$article)
		{
			$article['view_name'] = lang('ionize_select_default_view');
			
			if($article['view'] && !empty($views['article'][$article['view']]))
			{
				$article['view_name'] = $views['article'][$article['view']];
			}
		}
	}

	// ------------------------------------------------------------------------


	/**
	 * Add type logical name to the article list.
	 * This function is used for backend
	 *
	 * @param	Array	by ref. Array of articles
	 *
	function add_type_name(&$articles = array(), $views)
	{
		foreach ($articles as &$article)
		{
			$article['view_name'] = lang('ionize_select_default_view');
			
			if($article['view'] && !empty($views['article'][$article['view']]))
			{
				$article['view_name'] = $views['article'][$article['view']];
			}
		}
	}
	 */


	// ------------------------------------------------------------------------

	
	/**
	 * Saves the article context
	 *
	 */
	function save_context($context_data)
	{
	/*
		$save_data = array();
		$fields = $this->db->list_fields($this->parent_table);
		
		foreach ($fields as $field)
		{
			if ( ! empty($data[$field])) $save_data[$field] = $data[$field];
		}
	*/	
//		$context_data = $this->clean_data($data, $this->parent_table);

		
		
		if ( ! empty($context_data['id_page']) && ! empty($context_data['id_article']));
		{
			$this->db->where('id_page', $context_data['id_page']);
			$this->db->where('id_article', $context_data['id_article']);
			
			return $this->db->update($this->parent_table, $context_data);
		}
		
		return 0;
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Unlink one article from one page
	 *
	 */
	function unlink($id_article, $id_page)
	{
		$this->db->where(array($this->parent_table.'.id_page'=>$id_page, $this->parent_table.'.id_article'=>$id_article));

		return $this->db->delete($this->parent_table);
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves the article
	 *
	 * @param 	array	Standard data table
	 * @param 	array	Lang depending data table
	 *
	 * @return	int		Articles saved ID
	 *
	 */
	function save($data, $dataLang) 
	{
		// New article : Created field
		if( ! $data['id_article'] )
			$data['created'] = $data['updated'] = date('Y-m-d H:i:s');
		// Existing article : Update date
		else
			$data['updated'] = date('Y-m-d H:i:s');


		// Dates
		$data['publish_on'] = ($data['publish_on']) ? getMysqlDatetime($data['publish_on']) : '0000-00-00';
		$data['publish_off'] = ($data['publish_off']) ? getMysqlDatetime($data['publish_off']) : '0000-00-00';
		$data['comment_expire'] = ($data['comment_expire']) ? getMysqlDatetime($data['comment_expire']) : '0000-00-00';
			

		// Article saving
		return parent::save($data, $dataLang);
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves ordering of articles for the given page
	 *
	 * @param	mixed	String of coma separated new order or array of order
	 * @return	string	Coma separated order
	 *
	function save_ordering($ordering, $parent = false, $id_parent = false)
	{
		if ( ! is_array($ordering))
		{
			$ordering = explode(',', $ordering);
		}
		$new_order = '';
		$i = 1;
		
		while (list ($rank, $id) = each ($ordering))	
		{
			$this->db->where($this->pk_name, $id);
			$this->db->set('ordering', $i++);
			
			// If parent table is defined, save ordering in the join table
			if ($parent !== false)
			{
				$this->db->update($parent.'_'.$this->table);
			}
			else
			{
				$this->db->update($this->table);
			}
					
			$new_order .= $id.",";
		}
		
		return substr($new_order, 0, -1);
	}
	 */


	// ------------------------------------------------------------------------


	/**
	 * Calls all integrity corrections functions
	 *
	 * @param	array		Article array
	 * @param	array		Article lang data array
	 *
	 */
	function correct_integrity($article, $article_lang)
	{
		$this->update_links($article, $article_lang);
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Updates all other articles / pages links when saving one article
	 *
	 * @param	array		Article array
	 * @param	array		Article lang data array
	 *
	 */
	function update_links($article, $article_lang)
	{
		$id_article = 	$article['id_article'];
		$article_lang = $article_lang[Settings::get_lang('default')];
		$link_name = 	($article_lang['title'] != '') ? $article_lang['title'] : $article['name'];
		
		// Update of pages which link to this article
		$sql = "update page as p
				set p.link = '".$link_name."'
				where p.link_type = 'article'
				and p.link_id = " . $id_article ;
		$this->db->query($sql);
	
		// Update of pages (lang table) wich links to this article
		$sql = "update page_lang as pl
					inner join page as p on p.id_page = pl.id_page
					inner join article_lang as al on al.id_article = p.link_id
				set pl.link = al.url
				where p.link_type = 'article'
				and pl.lang = al.lang
				and p.link_id = " . $id_article;

		$this->db->query($sql);
		
		// Update of articles which link to this article
		$sql = "update article as a1
				set a1.link = '".$link_name."'
				where a1.link_type = 'article'
				and a1.link_id = " . $id_article ;		
		$this->db->query($sql);
		
		// Update of articles (lang table) which link to this article
		$sql = "update article_lang as al
					inner join article as a on a.id_article = al.id_article
					inner join article_lang as a2 on a2.id_article = a.link_id
				set al.link = a2.url
				where a.link_type = 'article'
				and al.lang = a2.lang
				and a.link_id = " . $id_article;
		
		$this->db->query($sql);

	}

	
	// ------------------------------------------------------------------------


	/**
	 * Adds a link between an article and a page
	 * Called :
	 * 	- when drag / drop a page as parent on one article
	 *  - when drag / drop an article to a page
	 * 	- when saving an article for the first time
	 *
	 * @param	int		ID of the page
	 * @param	int		ID of the article
	 * @param	Array	Optional. Array of context data to insert to the join table.
	 *
	 */
	function link_to_page($id_page, $id_article, $context_data = array())
	{
		if ($this->exists(array('id_article' => $id_article, 'id_page' => $id_page), $this->parent_table) == FALSE)
		{
			$data = array(
				'id_article' => $id_article,
				'id_page' => $id_page
			);
						
			if ( ! empty($context_data) )
			{
				// Cleans the context data array by removing data not in context table
				$context_data = $this->clean_data($context_data, $this->parent_table);
				
				$data = array_merge($context_data, $data);				
			}
			
			// Ordering : If not set, set to the last
			if ( empty($data['ordering']))
			{
				$order_list = $this->get_lang_list(array('id_page' => $id_page));
				$data['ordering'] = count($order_list) + 1;
			}

			$this->db->insert($this->parent_table, $data);
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	
	// ------------------------------------------------------------------------

	
	/**
	 * Sets the context data
	 *
	 *
	 */
	
	// ------------------------------------------------------------------------


	/** 
	 * Delete one article
	 * also delete all joined element from join tables
	 *
	 * @param	int 	Article ID
	 * @return 	int		Affected rows number
	 */
	function delete($id)
	{
		$affected_rows = 0;
		
		// Check if article exists
		if( $this->exists(array($this->pk_name => $id)) )
		{
			// Article delete
			$affected_rows += $this->db->where($this->pk_name, $id)->delete($this->table);
			
			// Lang
			$affected_rows += $this->db->where($this->pk_name, $id)->delete($this->lang_table);
	
			// Linked medias
			$affected_rows += $this->db->where($this->pk_name, $id)->delete($this->table.'_media');
					
			// Categories
			$affected_rows += $this->db->where($this->pk_name, $id)->delete($this->table.'_'.$this->category_table);
		}
		
		return $affected_rows;
	}
	 

	// ------------------------------------------------------------------------

	/**
	 * Duplicates one article
	 *
	 * The duplication includes :
	 * - the lang data
	 * - the linked media
	 * - the extended fields values, if any
	 *
	 * @param		Integer		ID of the article to duplicate
	 * @param		Array		Array of new fields (id_page, name, order, etc...)
	 * @param		String		Article Order in the defined page. Can be "first" or "last"
	 * @returns		Integer		ID of the new article
	 *
	 */
	function duplicate($id_source, $data, $order)
	{
		$article = $this->get_row_array($id_source);
		
		// Only copy if we get an article...
		if ( ! empty($article))
		{
			// Set the creation date to today
			$article['created'] = date('Y-m-d H:i:s');
			
			// Get articles ordering in the new page
			$existing_ordering = $this->get_articles_ordering($data['id_page']);
			
			// Set the new ordering
			switch($order)
			{
				case 'first' :
					
					$this->shift_article_ordering($data['id_page']);
					$order = 1;
					break;
					
				case 'last' :
					
					$order = count($existing_ordering) + 1 ;
					break;
			}
			
			// Context data
			$article_context = array
			(
				'id_page' => $data['id_page'],
				'view' => $data['view'],
				'id_type' => $data['id_type'],
				'ordering' => $order
			);
			
			// Merge the data to the article array			
			$article = array_merge($article, $data);
			
			// Unset the article ID : Need to be inexistant to insert...
			unset($article['id_article']);
			
			// Insert the article
			$id_copy = $this->insert($article);


			/*
			 * Save the advanced data : lang, extended fields, medias, categories
			 *
			 */
			if ($id_copy)
			{
				$article_context['id_article'] = $id_copy;
				
				// Join table with page
				$this->db->insert($this->parent_table, $article_context);

				// Medias
				$this->db->where('id_article', $id_source);
				$query = $this->db->get('article_media');
				
				if ( $query->num_rows() > 0)
				{
					$result = $query->result_array();

					foreach($result as & $arr)
					{
						$arr['id_article'] = $id_copy;
						$this->db->insert('article_media', $arr);	
					}
				}				
				
				// Lang
				$this->db->where('id_article', $id_source);
				$query = $this->db->get('article_lang');

				if ( $query->num_rows() > 0)
				{
					$result = $query->result_array();
				
					foreach($result as & $arr)
					{
						$arr['id_article'] = $id_copy;
						
						// The URL for all languages is the new URL 
						$arr['url'] = $data['name'];
						
						$this->db->insert('article_lang', $arr);
					}
				}
				
				// Categories
				$this->db->where('id_article', $id_source);
				$query = $this->db->get('article_category');

				if ( $query->num_rows() > 0)
				{
					$result = $query->result_array();
				
					foreach($result as & $arr)
					{
						$arr['id_article'] = $id_copy;
						$this->db->insert('article_category', $arr);
					}
				}
				
				// Extended fields
				if (Settings::get('use_extend_fields') == '1')
				{
					$extend_fields = $this->get_extend_fields_definition();
	
					// Extend fields IDs
					$efids = array();
					foreach($extend_fields as $ef)
						$efids[] = $ef['id_extend_field'];

					if ( !empty($efids))
					{
						$this->db->where(array('id_parent'=>$id_source));
						$this->db->where_in('id_extend_field', $efids);
						$query = $this->db->get('extend_fields');
	
	
						if ( $query->num_rows() > 0)
						{
							$result = $query->result_array();
							foreach($result as & $arr)
							{
								$arr['id_extend_fields'] = '';
								$arr['id_parent'] = $id_copy;
								$this->db->insert('extend_fields', $arr);
							}
						}
					}
				}
				
				return $id_copy;
			}
		}
		
		return FALSE;
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Updates articles ordering for the given page ID
	 * 
	 * @param	Integer		ID of the parent page
	 * @param	Integer		Ordering value from wich start the reordering
	 * @return 	void
	 *
	 */
	function shift_article_ordering($id_page, $from = NULL)
	{
		$sql = 'UPDATE ' . $this->parent_table . ' SET ordering = ordering + 1 WHERE id_page=' .$id_page;
		
		if ( ! is_null($from))
		{
			$sql .= ' AND ordering >= ' . $from;
		}
		
		$this->db->query($sql);
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the article ordering array from a givven page
	 *
	 * @param	Integer		ID of the page
	 * @return	Array		Array of articles ID
	 *
	 */
	function get_articles_ordering($id_page)
	{
// $where=false, $lang=NULL, $limit=false, $like=false, $order_by=false, $filter=false
		$articles = $this->get_lang_list(array('id_page' => $id_page), Settings::get_lang('default'), false, false, $this->parent_table.'.ordering ASC');
		
		$order_list = array();
		
		if ( ! empty($articles))
		{
			foreach($articles as $a)
			{
				$order_list[] = $a['id_article'];
			}
		}
		
		return $order_list;
	}


	// ------------------------------------------------------------------------


	/**
	 * Set an article online / offline in a given context (page)
	 *
	 * @param	int			Page ID
	 * @param	int			Article ID
	 *
	 * @return 	boolean		New status
	 *
	 */
	function switch_online($id_page, $id_article)
	{
		// Current status
		$article = $this->get_context($id_article, $id_page);

		$status = $article['online'];
	
		// New status
		($status == 1) ? $status = 0 : $status = 1;

		// Save		
		$this->db->where($this->pk_name, $id_article);
		$this->db->where('id_page', $id_page);
		$this->db->set('online', $status);
		$this->db->update($this->parent_table);
		
		return $status;
	}


	// ------------------------------------------------------------------------


	/**
	 * Gets articles from one category
	 * Adds a SQL filter on the categories and calls Article_model->get_lang_list()
	 *
	 * @param	array	Array of condition to transmit to get_lang_list()
	 * @param	string	Category name
	 * @param	string	Lang code
	 * @param	int		Limit value, will be passed to get_lang_list()
	 * @param	string	Like string, will be passed to get_lang_list()
	 *
	 * return	array	Articles array
	 *
	 */	
	function get_from_category($where=false, $category, $lang, $limit=false, $like=false, $order_by=false, $filter=false)
	{
		$this->db->join('article_category t5', $this->table.'.id_article = t5.id_article', 'inner');
		$this->db->join('category t6', 't6.id_category = t5.id_category', 'inner');
		$this->db->join('category_lang t7', 't7.id_category = t6.id_category', 'inner');

		$this->db->where('t6.name', $category);
		$this->db->where('t7.lang', $lang);
		
		return $this->get_lang_list($where, $lang, $limit, $like, $order_by, $filter);
	}


	// ------------------------------------------------------------------------


	/**
	 * Gets articles from categories
	 * Adds a SQL filter on the categories and calls Article_model->get_lang_list()
	 *
	 * @param	array	Array of condition to transmit to get_lang_list()
	 * @param	array	Categories name array
	 * @param	string	Categories condition : 'or', 'and' 
	 * @param	string	Lang code
	 * @param	int		Limit value, will be passed to get_lang_list()
	 * @param	string	Like string, will be passed to get_lang_list()
	 *
	 * return	array	Articles array
	 *
	 */	
	function get_from_categories($where=false, $categories, $categories_condition, $lang, $limit=false, $like=false, $order_by=false, $filter=false)
	{

		$this->db->join('article_category tac', $this->table.'.id_article = tac.id_article', 'inner');
		$this->db->join('category tcat', 'tcat.id_category = tac.id_category', 'inner');
		$this->db->join('category_lang tcl', 'tcl.id_category = tcat.id_category', 'inner');

		$in_categories = "('".implode("','", $categories)."')";

		$this->db->where('tcat.name in ', $in_categories, false);
		$this->db->where('tcl.lang', $lang);
		
		// Unactivate $limit to preserve articles for categories filtering (3rd attribute)
		$articles = $this->get_lang_list($where, $lang, false, $like, $order_by, $filter);

		
		// Filter articles depending on conditions
		$articles = $this->filter_on_categories($articles, $categories, $categories_condition);

		// limit now
		if ($limit !== false)
		{
			if (is_array($limit))
			{
				$articles = array_slice($articles, $limit[0], $limit[1]);
			}
			else
			{
				$articles = array_slice($articles, 0, $limit);
			}
		}

		return $articles;
	}


	// ------------------------------------------------------------------------


	/**
	 * Gets the articles from a givven archive
	 *
	 * @param	Array		Array of condition to be used by the SQL query
	 * @param	String		Year, YYYY
	 * @param	String		Month, MM
	 * @param	String		Lang code
	 * @param	Integer		Limit the query to X articles
	 * @param	String		SQL Like
	 * @param	String		SQL Order by
	 * @param	String		Filter conditions
	 * @return	Array		Array of articles
	 *
	 */
	function get_from_archives($where=false, $year, $month, $lang, $limit=false, $like=false, $order_by=false, $filter=false)
	{
		$period = $year;
		
		// If month is not null
		if ( ! is_null($month))
		{
			// Compatibility with 'MONTH' SQL function : month < 10 without firts '0'
			$period = $year.intval($month);
			
			$this->db->having('CONCAT(YEAR(date), MONTH(date)) = \'' . $period .'\'' );
		}
		else
		{
			$this->db->having('YEAR(date) = \'' . $period .'\'' );
		}
		
		return $this->get_lang_list($where, $lang, $limit, $like, $order_by, $filter);
	}


	// ------------------------------------------------------------------------


	/**
	 * 
	 */
	function get_archives_list($where = false, $lang = NULL, $filter=false, $month = false)
	{
		$data = array();
	
		if ($month === true)
		{
			$this->db->select('if (publish_on != 0, CONCAT( YEAR( publish_on ) , MONTH( publish_on ) ), CONCAT( YEAR( created ) , MONTH( created ) )) AS period, count(1) as nb', false);
		}
		else
		{
			$this->db->select('if (publish_on != 0,  YEAR( publish_on ) , YEAR( created ) ) AS period, count(1) as nb', false);
		}
		
		$this->db->group_by('period');

		$this->db->select($this->parent_table.'.*', false);
		$this->db->join($this->parent_table, $this->parent_table.'.id_article = ' .$this->table.'.id_article', 'inner');

		// Lang data
		if ( ! is_null($lang))
		{
			$this->db->join($this->lang_table, $this->lang_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table, 'inner');			
			$this->db->where($this->lang_table.'.lang', $lang);
		}

		// Where ?
		if (is_array($where) )
		{
			foreach ($where as $key => $value)
			{
				// id_page must be searched in page_article table
				if (strpos($key, 'id_page') !== FALSE)
					$key = str_replace('id_page', $this->parent_table.'.id_page', $key);
				else
					$key = $this->table.'.'.$key;
				
				$this->db->where($key, $value);
			}
		}

		// Filter on users filter
		if ( $filter !== false)
			$this->_set_filter($filter);
		
		// The publish filter
		$this->filter_on_published(self::$publish_filter);

		$query = $this->db->get($this->table);

		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
			$query->free_result();
		}
		
		return $data;
	}


	// ------------------------------------------------------------------------


	function get_adjacent_article($current, $adjacent)
	{
		if ($adjacent == 'previous')
		{
			$this->db->select_max('ordering');
			$this->db->where('ordering <', $current['ordering']);
		}
		else
		{
			$this->db->select_min('ordering');
			$this->db->where('ordering >', $current['ordering']);
		}
		
		$this->db->select('name');
		
		$this->db->where('id_page', $current['id_page']);
		
		// The publish filter
		$this->filter_on_published(self::$publish_filter);
		
		$query = $this->db->get($this->table);
		
		if ($query->num_rows() > 0)
			return $query->row_array();

		return $false;              
	} 
	

	// ------------------------------------------------------------------------


	function count_articles($where=false, $lang=NULL, $filter=false)
	{
		// Filter on published
		$this->filter_on_published(self::$publish_filter, $lang);

		// Main data select						
		$this->db->from($this->table);

		$this->db->select($this->table.'.*', false);
		$this->db->join($this->parent_table, $this->parent_table.'.id_article = ' .$this->table.'.id_article', 'inner');

		// Lang data
		if ( ! is_null($lang))
		{
			$this->db->join($this->lang_table, $this->lang_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table, 'inner');			
			$this->db->where($this->lang_table.'.lang', $lang);
		}

		// Add Type to query
		$this->db->select($this->type_table.'.type');
		$this->db->join($this->type_table, $this->parent_table.'.id_type = ' .$this->type_table.'.id_type', 'left');

		// Where ?
		if (is_array($where) )
		{
			foreach ($where as $key => $value)
			{
				// id_page must be searched in page_article table
				if (strpos($key, 'id_page') !== FALSE)
					$key = str_replace('id_page', $this->parent_table.'.id_page', $key);
				else
					$key = $this->table.'.'.$key;
				
				$this->db->where($key, $value, false);
			}
		}

		// Filter on users filter
		if ( $filter !== false)
			$this->_set_filter($filter);
		
		// The publish filter
		$this->filter_on_published(self::$publish_filter);

		$nb = $this->db->count_all_results();

		return $nb;
	}


	function count_articles_from_category($where=false, $category, $lang, $filter=false)
	{
		$this->db->join('article_category t5', $this->table.'.id_article = t5.id_article', 'inner');
		$this->db->join('category t6', 't6.id_category = t5.id_category', 'inner');
		$this->db->join('category_lang t7', 't7.id_category = t6.id_category', 'inner');

		$this->db->where('t6.name', $category);
		$this->db->where('t7.lang', $lang);
		
		return $this->count_articles($where, $lang, $filter);
	}


	function count_articles_from_categories($where=false, $categories, $categories_condition, $lang, $filter=false)
	{
		$articles = $this->get_from_categories($where, $categories, $categories_condition, $lang, false, false, false, $filter);
	
		return count($articles);
	}


	function count_articles_from_archives($where=false, $year, $month, $lang, $filter=false)
	{
		$period  = $year;
		
		if ( ! is_null($month))
		{
			$period = intval($year).intval($month);

			// Add the 'date' field to the query 
			$this->db->where('( 
				(publish_on !=0 AND CONCAT( YEAR(publish_on), MONTH(publish_on) ) = \''.$period.'\' )
				OR
				(publish_on =0 AND CONCAT( YEAR(created), MONTH(created) ) = \''.$period.'\' )
				
			)');
		}
		else
		{
			$this->db->where('( 
				(publish_on !=0 AND YEAR(publish_on) = \''.$period.'\' )
				OR
				(publish_on =0 AND YEAR(created) = \''.$period.'\' )
				
			)');
		}

		return $this->count_articles($where, $lang, $filter);
	}


	// ------------------------------------------------------------------------


	protected function filter_on_published($on = true, $lang = NULL)
	{
		if ($on === true)
		{
			$this->db->where($this->parent_table.'.online', '1');		
	
			if ($lang !== NULL)
				$this->db->where($this->lang_table.'.online', '1');		
	
			$this->db->where('((publish_off > ', 'now()', false);
			$this->db->or_where('publish_off = ', '0)' , false);
		
			$this->db->where('(publish_on < ', 'now()', false);
			$this->db->or_where('publish_on = ', '0))' , false);
		}	
	}


	// ------------------------------------------------------------------------


	/**
	 * Filters a articles array on multiple categories
	 *
	 */
	protected function filter_on_categories($articles, $categories, $condition)
	{
		$filtered_articles = array();

		foreach($articles as $article)
		{
			$add = false;
			
			if( ! empty($article['categories']))
			{
				// Get the article categories names
				$art_cat = array();
				foreach($article['categories'] as $ac)
					$art_cat[] = $ac['name'];
				
				if ($condition == 'and')
				{
					$check = array_intersect($art_cat, $categories);

					if (count($check) == count($categories))
					{
						$add = true;
					}
				}
				else
				{
					foreach($categories as $c=>$cat)
					{
						if (in_array($cat, $art_cat))
							$add = true;
					}
				}
				if ($add == true)
					$filtered_articles[] = $article;
			}
		}
		
		return $filtered_articles;
	
	}



	// ------------------------------------------------------------------------


	/**
	 * Adds all SQL conditions requested by the filter to the current request
	 *
	 * @param	String		Filter
	 * @return 	void
	 *
	 */
	private function _set_filter($filter)
	{
		$filter = explode('|', $filter);
		
		foreach($filter as $condition)
		{
			/* Explode the condition string
			 * Condition string could look like :
			 * title :				means with 'title' not empty
			 * author != 'foo'		means with author != 'toto'
			 */ 
			$c = explode(':', $condition);

			$value = isset($c[1]) ? $c[1] : " !='' ";

			// If filter field name in $filter_field_ref, use the given table as field to avoid ambiguous SQL
			if (array_key_exists($c[0], $this->filter_field_ref))
			{
				$c[0] = $this->filter_field_ref[$c[0]].'.'.$c[0];
			}
			
			$this->db->where('('.$c[0]. ' '. $value.')');
		}	
	}


	// ------------------------------------------------------------------------



	/** These function are from the very old Ionize historical version
	 *  For the moment, the functionalities are not implemented into the new Ionize, but they will perhaps.
	 */

/*	
	
	function get_from_tag($where=false, $tag, $lang, $limit=false)
	{
		$this->db->select('t4.id_tag, t4.tag');	
		$this->db->from('article_tags t4');
		$this->db->join('article_entry_tags t3', 't4.id_tag = t3.id_tag', 'inner');
		$this->db->where('t1.id_article = t3.id_article');
		$this->db->where('t4.tag', $tag);
		
		return $this->get_lang_list($where, $lang, $limit);
	}




	/** Duplicate one article
	 *  @param	$id		ID of the article to duplicate
	 *  @param	$url	New unique name for the copied article
	function duplicate($id, $url)
	{
		$query = $this->db->query("SELECT id_article FROM article where id_article ='".$id."'");
		
		$is_existing = $this->db->query("SELECT id_article FROM article where name ='".$url."'");
		
		if($query->num_rows() > 0 && $is_existing->num_rows() < 1)
		{
			$article = $this->get($id);
			$article['id_article'] = '';
			$article['active'] = '';
			$article['name'] = $url;
			
			$data = $this->get_lang($id);
			
			$dataLang = array();
			foreach($this->system->languages as $code_langue=>$language)
			{
				foreach($data as $row)
				{
					if ($code_langue == $row['code_langue'])
					{
						$dataLang[$code_langue] = $row;
					}
				}
			}
			
			if ($id = $this->save($article, $dataLang))
			{
				return true;
			}
			return false;
		}
		return false;
	}
	 */


	/** Gets the comments list
	 *  
	 *  @param 	id_entry		Limit the request to this item.
	 *  @param 	where		Array. Conditions
	function get_comments($id_entry=false, $where=false)
	{
		$result = array();
	
		if($id_entry)
		{
			$this->db->where('t1.id_'.$this->table, $id_entry);
		}
		
		if ($where)
		{
			$this->db->where($where);
		}
		
		$this->db->select('t1.*, t2.name', FALSE);
		$this->db->join($this->table.' t2', 't1.id_'.$this->table.' = t2.id_'.$this->table);
		
		$query = $this->db->get($this->comment_table.' t1');
		
		if ($query->num_rows > 0)
		{
			$result = $query->result_array();
		}
	
		$query->free_result();
		
		return $result;
	}


	function get_comment($id_comment)
	{
		$result = array();
		
		$this->db->where('id_article_comment', $id_comment);
		
		$query = $this->db->get($this->comment_table);
		
		if ($query->num_rows == 1)
		{
			$result = $query->row_array();
		}
		
		$query->free_result();
		
		return $result;
	}
	
	
	function save_comment($data, $id=false)
	{
		// Insert
		if(!$id)
		{
			$data['created'] = getMysqlDatetime();
			$this->db->insert($this->comment_table, $data);
			$id = $this->db->insert_id();
		}
		// Update
		else
		{
			$data['updated'] = getMysqlDatetime();			
			$this->db->where('id_'.$this->comment_table, $id);
			$this->db->update($this->comment_table, $data);
		}
		
		return $id;
	}
	 */
}
/* End of file article_model.php */
/* Location: ./application/models/article_model.php */