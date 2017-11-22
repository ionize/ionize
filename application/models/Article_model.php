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
	public $url_table =					'url';
	public $user_table = 				'users';
	public $menu_table = 				'menu';
	public $tag_table = 				'tag';
	public $tag_join_table = 			'article_tag';

	/* Contains table name which should be used for each filter get.
	 * Purpose : Avoid Ambiguous SQL query when 2 fields have the same name.
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
	 * Get one article
	 *
	 * @param	string		$where
	 * @param	string		$lang	Optional. Lang code
	 * @return	array		array
	 */
	public function get($where, $lang = NULL)
	{
		$data = $this->get_lang_list($where, $lang);

		if ( ! empty($data))
			return $data[0];

		return array();
	}


	// ------------------------------------------------------------------------


	/**
	 * Get one article by its ID
	 *
	 * @param	int			$id_article
	 * @param 	null|string	$lang
	 * @return	array
	 */
	public function get_by_id($id_article, $lang = NULL)
	{
		return $this->get(array('article.id_article' => $id_article), $lang);
	}

	// ------------------------------------------------------------------------


	/**
	 * Get array of articles
	 * 
	 * For each article, set the article date.
	 * The article date can be the creation date or the publish_on date if exists
	 *
	 * @access	public
	 * @param 	array	$where 	An associative array
	 * @return	array			Array of records
	 *
	 */
	public function get_list($where = array(), $table = NULL)
	{
		$this->{$this->db_group}->select($this->lang_table.'.*');
		$this->{$this->db_group}->join($this->lang_table, $this->lang_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table, 'inner');			
		$this->{$this->db_group}->where($this->lang_table.'.lang', Settings::get_lang('default'));
		
		$data = parent::get_list($where);
		
		// Set the correct publish date
		foreach ($data as $key=>$row)
		{
			$data[$key]['date']	= (isDate($row['publish_on'])) ? $row['publish_on'] : $row['created'];
		}
		
		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all articles with all lang data
	 *
	 * @param	array	$where
	 * @return	array
	 */
	public function get_all_lang_list($where = array())
	{
		$data = array();

		$this->{$this->db_group}->select($this->lang_table.'.*');
		$this->{$this->db_group}->join($this->lang_table, $this->lang_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table, 'inner');

		// URLs
		$this->{$this->db_group}->select('url.canonical, url.path, url.path_ids, url.full_path_ids');
		$this->{$this->db_group}->join(
			$this->url_table. ' as url',
			$this->table.'.id_article = url.id_entity AND '.
			'('.
				'url.active = 1 AND '.
				"url.type = 'article' AND ".
				'url.lang = '. $this->lang_table .'.lang'.
			')',
			'left'
		);

		$this->{$this->db_group}->select("
			group_concat(page.id_page separator ';') as page_ids,
			page_lang.title as page_title
		");
		$this->{$this->db_group}->join(
			'page_article',
			$this->table.'.id_article = page_article.id_article',
			'left'
		);
		$this->{$this->db_group}->join(
			'page',
			'page.id_page = page_article.id_page',
			'left'
		);
		$this->{$this->db_group}->join(
			'page_lang',
			'page_lang.id_page = page.id_page AND article_lang.lang = page_lang.lang',
			'left'
		);

		$this->{$this->db_group}->group_by($this->table.'.id_article');
		$this->{$this->db_group}->group_by($this->lang_table.'.lang');

		$articles = parent::get_list($where);

		foreach($articles as $article)
		{
			if ( empty($data[$article['id_article']][$article['lang']]))
			{
				if ( ! isset($data[$article['id_article']])) $data[$article['id_article']] = array();

				if ($article['lang'] == Settings::get_lang('default'))
					$data[$article['id_article']]['data'] = $article;

				$data[$article['id_article']][$article['lang']] = $article;
			}
		}

		return $data;
	}


	// ------------------------------------------------------------------------


	public function count_all_lang_list($where = array())
	{
		$this->{$this->db_group}->distinct();
		$this->{$this->db_group}->join($this->lang_table, $this->lang_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table, 'inner');
		$this->{$this->db_group}->join(
			$this->url_table. ' as url',
			$this->table.'.id_article = url.id_entity AND '.
			'('.
			'url.active = 1 AND '.
			"url.type = 'article' AND ".
			'url.lang = '. $this->lang_table .'.lang'.
			')',
			'left'
		);
		$this->{$this->db_group}->join(
			'page_article',
			$this->table.'.id_article = page_article.id_article',
			'left'
		);
		$this->{$this->db_group}->join(
			'page',
			'page.id_page = page_article.id_page',
			'left'
		);
		$this->{$this->db_group}->join(
			'page_lang',
			'page_lang.id_page = page.id_page AND article_lang.lang = page_lang.lang',
			'left'
		);

		$nb = parent::count($where);

		return $nb;
	}


	// ------------------------------------------------------------------------


	/** 
	 * Get article list with lang data
	 * Used by front-end to get the posts with lang data
	 *
	 * @param	array		$where
	 * @param	string		$lang
	 * @param	bool|string	$filter		SQL filter
	 * @param null $extend_filter
	 * @return	array					Array of articles
	 *
	 */
	public function get_lang_list($where = array(), $lang = NULL, $filter = FALSE, $extend_filter=NULL)
	{
		// Page_Article table
		$this->{$this->db_group}->select($this->parent_table.'.*', FALSE);
		$this->{$this->db_group}->select($this->parent_table.'.online as online_in_page', FALSE);
		$this->{$this->db_group}->join(
			$this->parent_table,
			$this->parent_table.'.id_article = ' .$this->table.'.id_article',
			'left'
		);

		// Page table
		// $this->{$this->db_group}->select('article_list_view, article_view');
		$this->{$this->db_group}->join(
			$this->page_table,
			$this->page_table.'.id_page = ' .$this->parent_table.'.id_page',
			'left'
		);

		// Menu table
		$this->{$this->db_group}->select('menu.id_menu, menu.name as menu_name');
		$this->{$this->db_group}->join(
			$this->menu_table,
			$this->menu_table.'.id_menu = ' .$this->page_table.'.id_menu',
			'left'
		);

		// Default ordering
		if ( empty($where['order_by']))
			$where['order_by'] = $this->parent_table.'.ordering ASC';

		// Correction on $where['id_page']
		if (is_array($where) && isset($where['id_page']) )
		{
			$where[$this->parent_table.'.id_page'] = $where['id_page'];
			unset($where['id_page']);
		}

		// Correction on $where['where_in']
		if (isset($where['where_in']))
		{
			foreach($where['where_in'] as $key => $value)
			{
				if ($key == 'id_page')
				{
					$where['where_in'][$this->parent_table.'.id_page'] = $value;
					unset($where['where_in']['id_page']);
				}
			}
		}

		// Published filter
		$this->filter_on_published(self::$publish_filter, $lang);

		// User's filter (tags)
		if ( $filter !== FALSE)	$this->_set_filter($filter);

		// Add the 'date' field to the query
		$this->{$this->db_group}->select('IF(article.logical_date !=0, article.logical_date, IF(article.publish_on !=0, article.publish_on, article.created )) AS date');

		// Add Type to query
		$this->{$this->db_group}->select($this->type_table.'.type, ' . $this->type_table.'.type_flag');
		$this->{$this->db_group}->join(
			$this->type_table,
			$this->parent_table.'.id_type = ' .$this->type_table.'.id_type',
			'left'
		);

		// Category
		if ( ! empty($where['id_category']))
		{
			$this->_add_category_filter_to_query($where['id_category']);
			unset($where['id_category']);
		}

		// Extend filters
		$this->_add_extend_filter_to_query($extend_filter, $where);

		// Base_model->get_lang_list()
		$articles = parent::get_lang_list($where, $lang);

		// log_message('error', print_r($this->last_query(), TRUE));

		$this->add_categories($articles, $lang);

		$this->add_tags($articles);

		return $articles;
	}


	// ------------------------------------------------------------------------


	public function get_orphan_articles($lang = NULL)
	{
		$data = array();
		if (is_null($lang)) $lang = Settings::get_lang('default');

		$sql = "
			select
				distinct a.*,
				pa.id_page
				from article a
			 left join page_article pa on pa.id_article = a.id_article
			 left join article_lang al on al.id_article = a.id_article and al.lang='".$lang."'
			 where pa.id_page is null
		";

		$query = $this->{$this->db_group}->query($sql);

		if ( $query->num_rows() > 0)
			$data = $query->result_array();

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get referring articles which have $id_article linked to them through the extend field $extend_name
	 * (Reverse search)
	 *
	 * @param $extend_name
	 * @param $id_article
	 * @param array $where
	 * @param null $lang
	 * @param bool $filter
	 * @param null $extend_filter
	 * @return array
	 */
	public function get_referring_articles_in_link_extend($extend_name, $id_article, $where=array(), $lang = NULL, $filter = FALSE, $extend_filter=NULL)
	{
		$data = array();

		$extend = self::$ci->extend_field_model->get_extend_definition_from_name(trim($extend_name), 'article');

		if ( ! empty($extend))
		{
			// join extend_fields efs on efs.id_extend_field = 16 and efs.parent='article' and efs.id_parent = article.id_article
			$this->{$this->db_group}->join(
				'extend_fields efs_ref',
				"efs_ref.id_extend_field = " . $extend['id_extend_field'] .
				" AND efs_ref.parent = 'article'" .
				" AND efs_ref.id_parent = article.id_article"
			);

			// where efs.content REGEXP '(article:(.*).1096)';
			$this->{$this->db_group}->where("efs_ref.content REGEXP '(article:(.*).".$id_article.")'", '', false);

			$data = $this->get_lang_list($where, $lang, $filter, $extend_filter);
		}

		return $data;
	}


	// ------------------------------------------------------------------------


	public function get_referring_articles_in_content_element_link_extend(
		$content_element_name, $extend_name, $id_article,
		$where=array(), $lang = NULL, $filter = FALSE, $extend_filter=NULL
	)
	{
		$data = array();

		self::$ci->load->model('element_definition_model', '', TRUE);

		$element = self::$ci->element_definition_model->get(array('name' => $content_element_name), $lang);
		$extend = self::$ci->extend_field_model->get_extend_definition_from_name(trim($extend_name), 'element');

		if ( ! empty($element) && ! empty($extend))
		{
			// join element e on e.id_parent = article.id_article and e.parent = 'article' and e.id_element_definition=2
			$this->{$this->db_group}->join(
				'element',
				"element.id_parent = article.id_article" .
				" and element.parent = 'article'" .
				" and element.id_element_definition=" . $element['id_element_definition']
			);

			// join extend_fields efs on efs.id_extend_field = 16 and efs.parent='article' and efs.id_parent = article.id_article
			$this->{$this->db_group}->join(
				'extend_fields efs_ref',
				"efs_ref.id_extend_field = " . $extend['id_extend_field'] .
				" AND efs_ref.parent = 'element'" .
				" AND efs_ref.id_parent = element.id_element"
			);

			// where efs.content REGEXP '(article:(.*).1096)';
			$this->{$this->db_group}->where("efs_ref.content REGEXP '(article:(.*).".$id_article.")'", '', false);
			$this->{$this->db_group}->where("page_article.main_parent = 1", '', false);

			$data = $this->get_lang_list($where, $lang, $filter, $extend_filter);
		}


		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get one article parent pages list
	 *
	 * @param 	int		$id_article
	 * @return	array
	 */
	public function get_pages_list($id_article)
	{
		$data = array();
	
		$this->{$this->db_group}->where($this->pk_name, $id_article);
		
		// Page table data
		$this->{$this->db_group}->join($this->page_table, $this->parent_table.'.id_'.$this->page_table.' = ' .$this->page_table.'.id_'.$this->page_table, 'left');
		
		// Lang data
		$this->{$this->db_group}->select($this->page_lang_table.'.*');
		$this->{$this->db_group}->join($this->page_lang_table, $this->page_lang_table.'.id_'.$this->page_table.' = ' .$this->page_table.'.id_'.$this->page_table, 'inner');			
		$this->{$this->db_group}->where($this->page_lang_table.'.lang', Settings::get_lang('default'));
		
		// Join table data
		$this->{$this->db_group}->select($this->page_table.'.*');
		$this->{$this->db_group}->select($this->parent_table.'.*');

		$query = $this->{$this->db_group}->get($this->parent_table);
		
		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
			$query->free_result();
		}
		
		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns article's context data for a given page
	 *
	 * @param	int      $id_article
	 * @param   int 	 $id_page
	 * @param 	string 	 $lang
	 * @return  array
	 */
	public function get_context($id_article, $id_page, $lang = NULL)
	{
		$data = array();
		
		$lang = (is_null($lang)) ? Settings::get_lang('default') : $lang;
		
		$this->{$this->db_group}->select($this->table.'.*');
		$this->{$this->db_group}->select($this->lang_table.'.*');
		$this->{$this->db_group}->select($this->parent_table.'.*');
		$this->{$this->db_group}->select('article_type.type_flag');

		$this->{$this->db_group}->join($this->lang_table, $this->table.'.'.$this->pk_name.' = ' .$this->lang_table.'.'.$this->pk_name, 'inner');			
		$this->{$this->db_group}->join($this->parent_table, $this->table.'.'.$this->pk_name.' = ' .$this->parent_table.'.'.$this->pk_name, 'inner');			
		$this->{$this->db_group}->join('article_type', 'article_type.id_type = page_article.id_type', 'left outer');			

		$this->{$this->db_group}->where(array($this->lang_table.'.lang' => $lang));
		$this->{$this->db_group}->where(array($this->table.'.'.$this->pk_name => $id_article, $this->parent_table.'.id_page' => $id_page));

		$query = $this->{$this->db_group}->get($this->table);

		if($query->num_rows() > 0)
		{
			$data = $query->row_array();
			$query->free_result();
		}

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all URLs of the article in the context of one parent page
	 *
	 * @param $id_article
	 * @param $id_page
	 *
	 * @return array
	 */
	public function get_urls_in_context($id_article, $id_page)
	{
		$urls = [];

		$collection = self::$ci->url_model->get_collection('article', $id_article);

		foreach($collection as $entity)
		{
			$path = explode('/', $entity['full_path_ids']);
			$entity_id_page = $path[count($path) -2];

			if ($entity_id_page == $id_page)
			{
				$entity['url'] = base_url() . $entity['lang'] . '/' . $entity['path'];
				$urls[$entity['lang']] = $entity;
			}
		}

		return $urls;
	}



	// ------------------------------------------------------------------------


	/**
	 * Returns all contexts for one article
	 *
	 * @param	string|int		$id_article	ID of one article
	 * @param	String			Lang code.
	 * @return	array			Array of contexts
	 */
	public function get_all_context($id_article = NULL, $lang = NULL)
	{
		if ( ! is_null($id_article))
		{
			$this->{$this->db_group}->where('id_article', $id_article);
		}

		$query = $this->{$this->db_group}->get($this->parent_table);

		return ($query->num_rows() > 0) ? $query->result_array() : array();
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Returns the main context for one article
	 *
	 * @param	int			$id_article
	 * @return	array		Article array, or one empty array if no article is found
	 */
	public function get_main_context($id_article)
	{
		$where = array(
			'id_article' => $id_article,
			'main_parent' => '1'
		);
		
		$this->{$this->db_group}->where($where);

		$query = $this->{$this->db_group}->get($this->parent_table);

		return ($query->num_rows() > 0) ? $query->row_array() : array();
	}
	
	
	// ------------------------------------------------------------------------

	
	/**
	 * Returns all contexts article's lang data as an array of articles.
	 *
	 * @param	Mixed		$id_article		ID of one article / Array of articles IDs
	 * @param	string		$lang			Lang code
	 * @return	array		Array of articles
	 */
	public function get_lang_contexts($id_article, $lang)
	{
		$this->{$this->db_group}->select($this->table.'.*');
		$this->{$this->db_group}->select($this->lang_table.'.*');
		$this->{$this->db_group}->select($this->parent_table.'.*');
		$this->{$this->db_group}->select('article_type.type_flag');

		$this->{$this->db_group}->join($this->lang_table, $this->table.'.'.$this->pk_name.' = ' .$this->lang_table.'.'.$this->pk_name);			
		$this->{$this->db_group}->join($this->parent_table, $this->table.'.'.$this->pk_name.' = ' .$this->parent_table.'.'.$this->pk_name);			
		$this->{$this->db_group}->join('article_type', 'article_type.id_type = page_article.id_type', 'left outer');			

		$this->{$this->db_group}->where(array($this->lang_table.'.lang' => $lang));
		
		if ( ! is_array($id_article) )
			$this->{$this->db_group}->where(array($this->table.'.'.$this->pk_name => $id_article));
		else
			$this->{$this->db_group}->where($this->table.'.'.$this->pk_name . ' in (' . implode(',', $id_article) . ')');

		$query = $this->{$this->db_group}->get($this->table);

		return ($query->num_rows() > 0) ? $query->result_array() : array();
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Add lang content to each article in the article list.
	 * This function is used for backend
	 *
	 * @param	Array	&$articles 		by ref. Array of articles
	 */
	public function add_lang_data(&$articles = array())
	{
		if ( ! empty($articles))
		{
			$ids = array();
			foreach($articles as $article)
			{
				$ids[] = $article['id_article'];
			}

			if ( ! empty($ids))
			{
				$this->{$this->db_group}->where('id_article in (' . implode(',' , $ids ) . ')' );
				$query = $this->{$this->db_group}->get('article_lang');

				$result = array();
				if($query->num_rows() > 0)
					$result = $query->result_array();

				foreach($articles as &$article)
				{
					$article['languages'] = array();

					foreach(Settings::get_languages() as $lang)
					{
						$lang_code = $lang['lang'];
						$article['languages'][$lang_code] = array();

						foreach($result as $row)
						{
							if ($row['id_article'] == $article['id_article'] && $row['lang'] == $lang_code)
								$article['languages'][$lang_code] = $row;
						}
					}
				}
			}
		}
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Add view logical name to the article list.
	 * This function is used for backend
	 *
	 * @param	Array	&$articles 		by ref. Array of articles
	 * @param	Array	$views			Array of views, as set in /themes/<the_theme>/config/views.php
	 */
	public function add_view_name(&$articles = array(), $views)
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
	 * Adds the 'categories' array to each passed article in the $artices array
	 *
	 * @param array			$articles
	 * @param null|string	$lang
	 */
	public function add_categories(&$articles = array(), $lang = NULL)
	{
		// Add Categories to each article
		$categories = $art_cat = array();

		$this->{$this->db_group}->join($this->category_lang_table, $this->category_table.'.id_category = ' .$this->category_lang_table.'.id_category', 'left');

		if ( ! is_null($lang))
			$this->{$this->db_group}->where($this->category_lang_table.'.lang', $lang);

		$query = $this->{$this->db_group}->get($this->category_table);

		if($query->num_rows() > 0)
		{
			$categories = $query->result_array();

			// Get categories articles table content
			$query = $this->{$this->db_group}->get($this->article_category_table);

			// table of links between articles and categories
			if($query->num_rows() > 0) $art_cat = $query->result_array();
		}

		// Add entry to each data array element
		foreach ($articles as $key => $article)
		{
			$articles[$key]['categories'] = array();

			if ( ! empty($categories))
			{
				foreach($art_cat as $cat)
				{
					if($articles[$key]['id_article'] == $cat['id_article'])
					{
						foreach($categories as $c)
						{
							if ($c['id_category'] == $cat['id_category'])
								$articles[$key]['categories'][] = $c;
						}
					}
				}
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * @param array $articles
	 */
	public function add_tags(&$articles = array())
	{
		// Add Tags to each article
		$tags = array();

		$articles_ids = array();
		foreach($articles as $article)
			$articles_ids[] = $article['id_article'];

		if ( ! empty($articles_ids))
		{
			$this->{$this->db_group}->join(
				$this->tag_join_table,
				$this->tag_join_table.'.id_tag = ' .$this->tag_table.'.id_tag', 'inner');

			$this->{$this->db_group}->where_in($this->tag_join_table.'.id_article', $articles_ids);

			$this->{$this->db_group}->select(
				$this->tag_table.'.id_tag, ' .
				$this->tag_join_table.'.id_article, ' .
				$this->tag_table.'.tag_name,' .
				$this->tag_table.'.tag_name as title'
			);

			$query = $this->{$this->db_group}->get($this->tag_table);

			if($query->num_rows() > 0)
				$tags = $query->result_array();
		}

		// Add entry to each data array element
		foreach ($articles as $key => $article)
		{
			$articles[$key]['tags'] = array();

			if ( ! empty($tags))
			{
				foreach($tags as $tag)
				{
					if($articles[$key]['id_article'] == $tag['id_article'])
					{
						$articles[$key]['tags'][] = $tag;
					}
				}
			}
		}
	}



	// ------------------------------------------------------------------------


	/**
	 * Saves the article context
	 *
	 * @param	array	$context_data
	 * @return	int
	 */
	public function save_context($context_data)
	{
		if ( ! empty($context_data['id_page']) && ! empty($context_data['id_article']));
		{
			$context_data = $this->clean_data($context_data, $this->parent_table);
		
			$this->{$this->db_group}->where('id_page', $context_data['id_page']);
			$this->{$this->db_group}->where('id_article', $context_data['id_article']);
			
			return $this->{$this->db_group}->update($this->parent_table, $context_data);
		}

		return 0;
	}
	
		
	// ------------------------------------------------------------------------


	/**
	 * Saves the given page as main parent for this article contexts
	 *
	 * @param	int		$id_article
	 * @param	int		$id_page
	 * @return 	int		Number of inserted / updated elements
	 *
	 */
	public function save_main_parent($id_article, $id_page)
	{
		$this->{$this->db_group}->where('id_article', $id_article);
		$this->{$this->db_group}->set('main_parent', '0');
		$this->{$this->db_group}->update($this->parent_table);
		
		$this->{$this->db_group}->where( array('id_article' => $id_article, 'id_page' => $id_page));
		$this->{$this->db_group}->set('main_parent', '1');
		
		$this->{$this->db_group}->update($this->parent_table);

		return $this->save_urls($id_article);
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves one article URLs paths
	 *
	 * @param	int		$id_article
	 * @return	int		Number of inserted / updated Urls
	 */
	public function save_urls($id_article)
	{
        // Models
		$CI =& get_instance();
        $CI->load->model(
            array(
                'url_model',
                'page_model'
            ), '', TRUE);
		
		$nb = 0;
		
		// Article main context
		$context = $this->get_main_context($id_article);

		if ( ! empty($context))
		{
			foreach($this->get_languages() as $l)
			{
				$parents_array = $CI->page_model->get_parent_array($context['id_page'], array(), $l['lang']);
				
				$article = $this->get(array('id_article'=>$id_article), $l['lang']);
				
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

				if ( ! empty($url) && isset($article['url']))
				{
					// Check if URL exists and correct it if necessary
					$url = implode('/', $url) . '/' . $article['url'];
					
					$path_ids[] = $id_article;
					$full_path_ids[] = $id_article;
					
					$entity = array(
						'type' => 'article',
						'id_entity' => $id_article,
						'url' => $url,
						'path_ids' => implode('/', $path_ids),
						'full_path_ids' => implode('/', $full_path_ids)
					);
					
					$nb = $CI->url_model->save_url($entity, $l['lang']);
				}
			}
		}		
		return $nb;
	}


	// ------------------------------------------------------------------------


	/**
	 * Rebuild all the Url of all / one article
	 * If no article id is given, rebuilds all the URLs
	 *
	 * @param	int		[$id_article]		Optional
	 * @return	int		Number of inserted / updated Urls
	 */
	public function rebuild_urls($id_article = NULL)
	{
		$nb = 0;

		if ( ! is_null($id_article))
		{
			$nb = $this->save_urls($id_article);
		}
		else
		{
			$articles = $this->get_list();
			
			foreach($articles as $article)
			{
				$nb += $this->save_urls($article['id_article']);
			}
		}
		
		return $nb;
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Unlink one article from one page
	 *
	 * @param	int		$id_article
	 * @param	int		$id_page
	 */
	public function unlink($id_article, $id_page)
	{
		$this->{$this->db_group}->where(array($this->parent_table.'.id_page'=>$id_page, $this->parent_table.'.id_article'=>$id_article));
		$nb =  $this->{$this->db_group}->delete($this->parent_table);
		
		// Correct "Main Parent"
		$this->correct_main_parent($id_article);
		
		// Updates URLs
		$this->rebuild_urls($id_article);
		
		return $nb;
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves the article
	 *
	 * @param 	array	$data		data table
	 * @param 	array	$lang_data	Lang depending data table
	 * @return	int		Articles saved ID
	 */
	public function save($data, $lang_data = array())
	{
		// New article : Created field
		if( ! $data['id_article'] OR $data['id_article'] == '')
			$data['created'] = $data['updated'] = date('Y-m-d H:i:s');
		// Existing article : Update date
		else
			$data['updated'] = date('Y-m-d H:i:s');

		// convert int values
		$data['id_page'] = (int)$data['id_page'];
		$data['id_article'] = (int)$data['id_article'];
		$data['id_content_type'] = ((empty($data['id_content_type'])) ? NULL : (int)$data['id_content_type']);
		$data['indexed'] = (int)$data['indexed'];
		$data['id_category'] = ((empty($data['id_category'])) ? NULL : (int)$data['id_category']);
		$data['flag'] = ((empty($data['flag'])) ? NULL : (int)$data['flag']);
		$data['has_url'] = (int)$data['has_url'];
		$data['priority'] = (int)$data['priority'];

		// Dates
		$data = $this->_set_dates($data);

		// Article saving
		return parent::save($data, $lang_data);
	}


	// ------------------------------------------------------------------------


	/**
	 * Calls all integrity corrections functions
	 *
	 * @param	array		$article
	 * @param	array		$article_lang
	 */
	public function correct_integrity($article, $article_lang)
	{
		$this->update_links($article, $article_lang);
	}


	// ------------------------------------------------------------------------


	/**
	 * Corrects the article's main parent
	 *
	 * @param	int		$id_article
	 * @return	int
	 */
	public function correct_main_parent($id_article)
	{
		$contexts = $this->get_all_context($id_article);
		
		if (count($contexts) == 1)
		{
			$this->{$this->db_group}->set('main_parent', '1');
			$this->{$this->db_group}->where('id_article', $id_article );
			$nb = $this->{$this->db_group}->update('page_article');
			
			// Updates URLs
			$this->rebuild_urls($id_article);
			
			return $nb;
		}
		return 0;
	}


	// ------------------------------------------------------------------------


	public function correct_internal_links($id_article, $id_old_page, $id_page)
	{
		// Update content links
		$old_link_code = '{{article:'.$id_old_page.'.'.$id_article.'}}';
		$new_link_code = '{{article:'.$id_page.'.'.$id_article.'}}';

		$this->{$this->db_group}->where('content like \'%' . $old_link_code . '%\'');
		$query = $this->{$this->db_group}->get($this->lang_table);

		if ($query->num_rows() > 0)
		{
			$articles = $query->result_array();

			foreach($articles as $article)
			{
				$content = $article['content'];

				$content = str_replace($old_link_code, $new_link_code, $content);
				$this->{$this->db_group}->where(
					array(
						'id_article' => $article['id_article'],
						'lang' => $article['lang']
					)
				);
				$this->{$this->db_group}->update($this->lang_table, array('content'=>$content));
			}
		}

		// Update internal links
		parent::update(
			array('link_id' => $id_old_page.'.'.$id_article),
			array('link_id' => $id_page.'.'.$id_article),
			$this->parent_table
		);
	}


	// ------------------------------------------------------------------------


	/**
	 * Updates all other articles / pages links when saving one article
	 *
	 * @param	array	$article
	 * @param	array	$article_lang
	 */
	public function update_links($article, $article_lang)
	{
		$id_article = 	$article['id_article'];
		$id_page = 	$article['id_page'];
		$rel = $id_page.'.'.$id_article;
		$article_lang = $article_lang[Settings::get_lang('default')];
		$link_name = 	($article_lang['title'] != '') ? $article_lang['title'] : $article['name'];
		
		// Update of pages which link to this article
		$this->{$this->db_group}->set('link', $link_name);
		$this->{$this->db_group}->where(
			array(
				'link_type' => 'article',
				'link_id' => $rel
			)
		);
		$this->{$this->db_group}->update('page');

		// Update of pages (lang table) which links to this article
		$sql = "update page_lang as pl
					inner join page as p on p.id_page = pl.id_page
					inner join article_lang as al on al.id_article = p.link_id
				set pl.link = al.url
				where p.link_type = 'article'
				and pl.lang = al.lang
				and p.link_id = " . $rel;

		$this->{$this->db_group}->query($sql);
		
		// Update of articles which link to this article
		$this->{$this->db_group}->set('link', $link_name);
		$this->{$this->db_group}->where(
			array(
				'link_type' => 'article',
				'link_id' => $rel
			)
		);
		$this->{$this->db_group}->update('page_article');

		// Update of articles (lang table) which link to this article
		/*
		$sql = "update article_lang as al
					inner join article as a on a.id_article = al.id_article
					inner join article_lang as a2 on a2.id_article = a.link_id
				set al.link = a2.url
				where a.link_type = 'article'
				and al.lang = a2.lang
				and a.link_id = " . $id_article;
		
		$this->{$this->db_group}->query($sql);
		*/

	}

	
	// ------------------------------------------------------------------------


	/**
	 * Adds a link between an article and a page
	 * Called :
	 * 	- when drag / drop a page as parent on one article
	 *  - when drag / drop an article to a page
	 * 	- when saving an article for the first time
	 *
	 * @param	int		$id_page
	 * @param	int		$id_article
	 * @param	Array	$context_data	Optional. Array of context data to insert to the join table.
	 * @return	boolean	TRUE is the link was set.
	 */
	public function link_to_page($id_page, $id_article, $context_data = array())
	{
		// If the article doesn't exists in the context of the page
		if ($this->exists(array('id_article' => $id_article, 'id_page' => $id_page), $this->parent_table) == FALSE)
		{
			$data = array(
				'id_article' => $id_article,
				'id_page' => $id_page
			);
						
			if ( ! empty($context_data) )
			{
				// Cleans the context data array by removing keys not corresponding to fields in context table
				$context_data = $this->clean_data($context_data, $this->parent_table);
				
				$data = array_merge($context_data, $data);				
			}
			
			// Ordering : If not set, set to the last
			if ( empty($data['ordering']))
			{
				$order_list = $this->get_lang_list(array('id_page' => $id_page));
				$data['ordering'] = count($order_list) + 1;
			}

			$this->{$this->db_group}->insert($this->parent_table, $data);
			
			// correct the Main Parent context data
			$this->correct_main_parent($id_article);
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	
	// ------------------------------------------------------------------------


	/** 
	 * Delete one article
	 * also delete all joined element from join tables
	 *
	 * @param	int 	$id_article
	 * @return 	int		Affected rows number
	 */
	private function delete_article($id_article)
	{
		$affected_rows = 0;
		
		// Check if article exists
		if( $this->exists(array($this->pk_name => $id_article)) )
		{
			// Article delete
			$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id_article)->delete($this->table);
			
			// Lang
			$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id_article)->delete($this->lang_table);
	
			// Linked medias
			$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id_article)->delete($this->table.'_media');
					
			// Categories
			$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id_article)->delete($this->table.'_'.$this->category_table);
			
			// Contexts
			$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id_article)->delete($this->parent_table);
			
			// URLs
			$where = array(
				'type' => 'article',
				'id_entity' => $id_article
			);
			$affected_rows += $this->{$this->db_group}->where($where)->delete('url');
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
	 * @param	Integer		$id_source	ID of the article to duplicate
	 * @param	Array		$data		Array of new fields (id_page, name, order, etc...)
	 * @param	String		$order 		Article Order in the defined page. Can be "first" or "last"
	 * @return	Integer		ID of the new article
	 */
	public function duplicate($id_source, $data, $order)
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
				'ordering' => $order,
				'main_parent' => '1'
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
				$this->{$this->db_group}->insert($this->parent_table, $article_context);

				// Medias
				$this->{$this->db_group}->where('id_article', $id_source);
				$query = $this->{$this->db_group}->get('article_media');
				
				if ( $query->num_rows() > 0)
				{
					$result = $query->result_array();

					foreach($result as & $arr)
					{
						$arr['id_article'] = $id_copy;
						$this->{$this->db_group}->insert('article_media', $arr);	
					}
				}				
				
				// Lang
				$this->{$this->db_group}->where('id_article', $id_source);
				$query = $this->{$this->db_group}->get('article_lang');

				if ( $query->num_rows() > 0)
				{
					$result = $query->result_array();
				
					foreach($result as & $arr)
					{
						$arr['id_article'] = $id_copy;
						
						// The URL for all languages is the new URL 
						$arr['url'] = $data['name'];
						
						$this->{$this->db_group}->insert('article_lang', $arr);
					}
				}
				
				// Categories
				$this->{$this->db_group}->where('id_article', $id_source);
				$query = $this->{$this->db_group}->get('article_category');

				if ( $query->num_rows() > 0)
				{
					$result = $query->result_array();
				
					foreach($result as & $arr)
					{
						$arr['id_article'] = $id_copy;
						$this->{$this->db_group}->insert('article_category', $arr);
					}
				}
				
				// Extended fields
				$extend_fields = $this->get_extend_fields_definition();

				// Extend fields IDs
				$efids = array();
				foreach($extend_fields as $ef)
					$efids[] = $ef['id_extend_field'];

				if ( !empty($efids))
				{
					$this->{$this->db_group}->where(array('id_parent'=>$id_source));
					$this->{$this->db_group}->where_in('id_extend_field', $efids);
					$query = $this->{$this->db_group}->get('extend_fields');


					if ( $query->num_rows() > 0)
					{
						$result = $query->result_array();
						foreach($result as & $arr)
						{
							$arr['id_extend_fields'] = '';
							$arr['id_parent'] = $id_copy;
							$this->{$this->db_group}->insert('extend_fields', $arr);
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
	 * @param	Integer		$id_page	ID of the parent page
	 * @param	Integer		$from 		Ordering value from which start the reordering
	 * @return 	void
	 */
	public function shift_article_ordering($id_page, $from = NULL)
	{
		$sql = 'UPDATE ' . $this->parent_table . ' SET ordering = ordering + 1 WHERE id_page=' .$id_page;
		
		if ( ! is_null($from))
		{
			$sql .= ' AND ordering >= ' . $from;
		}
		
		$this->{$this->db_group}->query($sql);
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the article ordering array from a givven page
	 *
	 * @param	Integer		$id_page
	 * @return	Array		Array of articles ID
	 */
	public function get_articles_ordering($id_page)
	{
		$articles = $this->get_lang_list(array('id_page' => $id_page), Settings::get_lang('default'), FALSE, FALSE, $this->parent_table.'.ordering ASC');
		
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
	 * @param	int			$id_page
	 * @param	int			$id_article
	 * @param	boolean		$new_status
	 * @return 	boolean		New status
	 */
	public function switch_online($id_page, $id_article=NULL, $new_status=NULL)
	{
		// Current status
		$article = $this->get_context($id_article, $id_page);

		$status = $article['online'];
	
		// New status
		$status = $new_status != NULL ? $new_status : ($status == 1 ? $status = 0 : $status = 1);

		// Save		
		$this->{$this->db_group}->where($this->pk_name, $id_article);
		$this->{$this->db_group}->where('id_page', $id_page);
		$this->{$this->db_group}->set('online', $status);
		$this->{$this->db_group}->update($this->parent_table);
		
		return $status;
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds the pagination filter to the articles get_lang_list() call
	 *
	 * @param	int	$pagination
	 * @param	int	$start_index
	 */
	public function add_pagination_filter($pagination, $start_index)
	{
		$this->{$this->db_group}->limit((int)$pagination);
		$this->{$this->db_group}->offset((int)$start_index);
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds the archives filter to the articles get_lang_list() call
	 *
	 * @param   int     $year
	 * @param   int     $month
	 */
	public function add_archives_filter($year, $month = NULL)
	{
		if ($year == '1970')
		{
			$this->{$this->db_group}->where(
				'(
					IF(
						article.logical_date !=0, article.logical_date,
						IF(
							article.publish_on !=0, article.publish_on,
							article.created
						)
					) = "0000-00-00 00:00:00"
				)'
			);
		}
		else if ( ! is_null($month))
		{
			// Compatibility with 'MONTH' SQL function : month < 10 without firts '0'
			$period = $year. (int) $month;
			$this->{$this->db_group}->where(
				'(
					IF (
						article.logical_date !=0, CONCAT(YEAR(article.logical_date), MONTH(article.logical_date)),
						IF (
							article.publish_on !=0, CONCAT(YEAR(article.publish_on), MONTH(article.publish_on)),
							CONCAT(YEAR(article.created), MONTH(article.created))
						)
					) = \''.$period.'\'
				)'
			);
		}
		else
		{
			$this->{$this->db_group}->where(
				'(
					IF(
						article.logical_date !=0, YEAR(article.logical_date),
						IF(
							article.publish_on !=0, YEAR(article.publish_on),
							YEAR(article.created)
						)
					) = \'' . $year .'\'
				)'
			);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds the category filter to the articles get_lang_list() call
	 *
	 * @param   int     $category
	 * @param   int     $lang
	 */
	public function add_category_filter($category, $lang)
	{
		$this->{$this->db_group}->join('article_category', $this->table.'.id_article = article_category.id_article', 'inner');
		$this->{$this->db_group}->join('category', 'category.id_category = article_category.id_category', 'inner');
		$this->{$this->db_group}->join('category_lang', 'category_lang.id_category = category.id_category', 'inner');

		$this->{$this->db_group}->where('category.name', $category);
		$this->{$this->db_group}->where('category_lang.lang', $lang);
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds the tag filter to the articles get_lang_list() call
	 *
	 * @param   string  $tag_name
	 */
	public function add_tag_filter($tag_name)
	{
		$this->{$this->db_group}->join('article_tag', $this->table.'.id_article = article_tag.id_article', 'inner');
		$this->{$this->db_group}->join('tag', 'tag.id_tag = article_tag.id_tag', 'inner');

		$this->{$this->db_group}->where('tag.tag_name', urldecode($tag_name));
	}


	// ------------------------------------------------------------------------


	/**
	 * Gets the list of archives with number of articles linked to.
	 *
	 * @param	array  		$where
	 * @param	string		$lang
	 * @param	bool   		$filter
	 * @param	bool   		$month
	 * @param	string 		$order_by
	 * @return	array
	 */
	public function get_archives_list($where=array(), $lang=NULL, $filter=FALSE, $month=FALSE, $order_by='period DESC')
	{
		$data = array();
	
		if ($month === TRUE)
		{
			$this->{$this->db_group}->select(
				'
				if(
					article.logical_date !=0, CONCAT(YEAR(article.logical_date), DATE_FORMAT(article.logical_date, "%m")),
					if(
						article.publish_on != 0, CONCAT(YEAR(article.publish_on), DATE_FORMAT(article.publish_on, "%m")),
						if(
							article.created !=0, CONCAT(YEAR(article.created), DATE_FORMAT(article.created, "%m")), "197001"
						)
					)
				) AS period, count(1) as nb
				',
				FALSE
			);
		}
		else
		{
			$this->{$this->db_group}->select(
				'
				if(
					article.logical_date !=0, YEAR(article.logical_date),
					if(
						article.publish_on != 0, YEAR(article.publish_on),
						if (
							article.created !=0, YEAR(article.created), "1970"
						)
					)
				) AS period, count(1) as nb',
				FALSE
			);
		}
		
		$this->{$this->db_group}->group_by('period');
		$this->{$this->db_group}->order_by($order_by);

		$this->{$this->db_group}->select($this->parent_table.'.id_page', FALSE);
		$this->{$this->db_group}->join(
			$this->parent_table,
			$this->parent_table.'.id_article = ' .$this->table.'.id_article',
			'inner'
		);

		// Lang data
		if ( ! is_null($lang))
		{
			$this->{$this->db_group}->join(
				$this->lang_table,
				$this->lang_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table,
				'inner'
			);
			$this->{$this->db_group}->where($this->lang_table.'.lang', $lang);
		}

		// Where ?
		$this->_process_where($where);

		// Filter on users filter
		if ( $filter !== FALSE)
			$this->_set_filter($filter);
		
		// The publish filter
		$this->filter_on_published(self::$publish_filter);

		$query = $this->{$this->db_group}->get($this->table);

		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
			$query->free_result();
		}
		
		return $data;
	}


	// ------------------------------------------------------------------------

	/**
	 * Returns the adjacent article.
	 *
	 * @param   array       $current
	 * @param   string	    $adjacent   'previous' or 'next'
	 * @return  null|array
	 */
	public function get_adjacent_article($current, $adjacent)
	{
		if ($adjacent == 'previous')
		{
			$this->{$this->db_group}->select_max('ordering');
			$this->{$this->db_group}->where('ordering <', $current['ordering']);
		}
		else
		{
			$this->{$this->db_group}->select_min('ordering');
			$this->{$this->db_group}->where('ordering >', $current['ordering']);
		}
		
		$this->{$this->db_group}->select('name');
		
		$this->{$this->db_group}->where('id_page', $current['id_page']);
		
		// The publish filter
		$this->filter_on_published(self::$publish_filter);
		
		$query = $this->{$this->db_group}->get($this->table);
		
		if ($query->num_rows() > 0)
			return $query->row_array();

		return NULL;
	} 
	

	// ------------------------------------------------------------------------

	/**
	 * Count the number of articles, based on given conditions
	 *
	 * @param array			$where
	 * @param null 			$lang
	 * @param null|string	$filter		SQL filter
	 * @return mixed
	 */
	public function count_articles($where=array(), $lang=NULL, $filter=NULL)
	{
		// only look at main table for count
		$this->{$this->db_group}->select('`'.$this->table.'`.*');

		// Filter on published
		$this->filter_on_published(self::$publish_filter, $lang);

		// Main join						
		$this->{$this->db_group}->join($this->parent_table, $this->parent_table.'.id_article = ' .$this->table.'.id_article', 'inner');

		// Lang data
		if ( ! is_null($lang))
		{
			$this->{$this->db_group}->join($this->lang_table, $this->lang_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table, 'inner');			
			$this->{$this->db_group}->where($this->lang_table.'.lang', $lang);
		}

		// Add Type to query
		$this->{$this->db_group}->join($this->type_table, $this->parent_table.'.id_type = ' .$this->type_table.'.id_type', 'left');

		// Process the $where array
		if (isset($where['order_by'])) unset($where['order_by']);
		$this->_process_where($where);

		// Filter on users filter
		if ( ! is_null($filter))
			$this->_set_filter($filter);

		$nb = $this->{$this->db_group}->count_all_results($this->table);

		return $nb;
	}


	// ------------------------------------------------------------------------


	public function init_article_urls($article, $lang=NULL)
	{
		$articles = $this->init_articles_urls(array($article), $lang);

		return $articles[0];
	}


	// ------------------------------------------------------------------------


	public function init_articles_urls($articles, $lang=NULL)
	{
		self::$ci->load->model('page_model');

		$lang = is_null($lang) ? Settings::get_lang('current') : $lang;

		// Page URL key to use
		$page_url_key = (config_item('url_mode') === 'short') ? 'url' : 'path';


		// Array of all articles IDs
		$articles_id = array();
		foreach($articles as $article)
			$articles_id[] = $article['id_article'];

		// Articles contexts of all articles
		$pages_context = self::$ci->page_model->get_lang_contexts($articles_id, $lang);

		// Add pages contexts data to articles
		foreach($articles as &$article)
		{
			$contexts = array();
			foreach($pages_context as $context)
			{
				if ($context['id_article'] == $article['id_article'])
					$contexts[] = $context;
			}

			$page = array_shift($contexts);

			// Get the context of the Main Parent
			if ( ! empty($contexts))
			{
				foreach($contexts as $context)
				{
					if ($context['main_parent'] == '1')
						$page = $context;
				}
			}

			// Basic article URL : its lang URL (without "http://")
			$url = $article['url'];

			// Link ?
			if ($article['link_type'] != '' )
			{
				// External
				if ($article['link_type'] === 'external')
				{
					$article['absolute_url'] = $article['link'];
				}

				// Email
				else if ($article['link_type'] === 'email')
				{
					$article['absolute_url'] = auto_link($article['link'], 'both', TRUE);
				}

				// Internal
				else
				{
					// Article
					if($article['link_type'] === 'article')
					{
						// Get the article to which this page links
						$rel = explode('.', $article['link_id']);
						$target_article = $this->get_context($rel[1], $rel[0], Settings::get_lang('current'));

						// Of course, only if not empty...
						if ( ! empty($target_article))
						{
							// Get the article's parent page
							$parent_page = self::$ci->page_model->get_by_id($rel[0], Settings::get_lang('current'));

							if ( ! empty($parent_page))
								$article['absolute_url'] = $parent_page[$page_url_key] . '/' . $target_article['url'];
						}
					}
					// Page
					else
					{
						$target_page = self::$ci->page_model->get_by_id($article['link_id'], Settings::get_lang('current'));

						// If target page is offline, 'path' is not set
						if ( isset($target_page[$page_url_key]))
							$article['absolute_url'] = $target_page[$page_url_key];
						else
							$article['absolute_url'] = '#';
					}

					$article['relative_url'] = $article['absolute_url'];
					$article['relative_lang_url'] = $article['absolute_url'];

					// Correct the URL : Lang + Base URL
					if ( count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1' )
					{
						$article['absolute_url'] =  Settings::get_lang('current'). '/' . $article['absolute_url'];
						$article['relative_lang_url'] = $article['absolute_url'];
					}

					$article['absolute_url'] = base_url() . $article['absolute_url'];
				}
			}
			// Standard URL
			else
			{
				$article['relative_url'] = $article['relative_lang_url'] = $page[$page_url_key] . '/' . $url;;

				if ( count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1' )
				{
					$article['relative_lang_url'] = Settings::get_lang('current') . '/' . $article['relative_url'];
					$article['absolute_url'] = base_url() . $article['relative_lang_url'];
				}
				else
				{
					$article['absolute_url'] = base_url() . $article['relative_url'];
				}
			}
		}

		return $articles;
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds Published filtering on articles get_lang_list() call
	 *
	 * @param   bool    $on
	 * @param   string  $lang
	 */
	protected function filter_on_published($on = TRUE, $lang = NULL)
	{
		if ($on === TRUE)
		{
			$this->{$this->db_group}->where($this->parent_table.'.online', '1');
	
			if ($lang !== NULL && count(Settings::get_online_languages()) > 1)
				$this->{$this->db_group}->where($this->lang_table.'.online', '1');		
	
			$this->{$this->db_group}->where('((article.publish_off > ', 'now()', FALSE);
			$this->{$this->db_group}->or_where('article.publish_off = ', '0)' , FALSE);
		
			$this->{$this->db_group}->where('(article.publish_on < ', 'now()', FALSE);
			$this->{$this->db_group}->or_where('article.publish_on = ', '0))' , FALSE);
		}	
	}


	// ------------------------------------------------------------------------


	/**
	 * Processes the condition array
	 *
	 * @param array     $where
	 */
	protected function _process_where($where=array(), $table = NULL)
	{
		if (isset($where['id_page']))
		{
			$where[$this->parent_table.'.id_page'] = $where['id_page'];
			unset($where['id_page']);
		}

		parent::_process_where($where);
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds all SQL conditions requested by the filter to the current request
	 *
	 * @param	String		$filter
	 * @return 	void
	 */
	private function _set_filter($filter = NULL)
	{
		if ( ! is_null($filter))
			$this->{$this->db_group}->where('('.$filter.')');
	}


	// ------------------------------------------------------------------------


	/**
	 * Set the correct dates to one article and return it
	 *
	 * @param   array	$data   Article array
	 * @return  array
	 */
	protected function _set_dates($data)
	{
		$data['publish_on'] = (isset($data['publish_on']) && $data['publish_on']) ? getMysqlDatetime($data['publish_on'], Settings::get('date_format')) : '0000-00-00';
		$data['publish_off'] = (isset($data['publish_off']) && $data['publish_off']) ? getMysqlDatetime($data['publish_off'], Settings::get('date_format')) : '0000-00-00';
		$data['logical_date'] = (isset($data['logical_date']) && $data['logical_date']) ? getMysqlDatetime($data['logical_date'], Settings::get('date_format')) : '0000-00-00';
		$data['comment_expire'] = (isset($data['comment_expire']) && $data['comment_expire']) ? getMysqlDatetime($data['comment_expire'], Settings::get('date_format')) : '0000-00-00';

		return $data;
	}


	protected function _add_extend_filter_to_query($filters, &$query_where=array())
	{
		$order_by = ! empty($query_where['order_by']) ? trim($query_where['order_by'], " ,") : NULL;
		$json = json_decode($filters, TRUE);

		if (empty($filters))
		{
			return;
		}

		// JSON filters
		if ($json != FALSE)
		{
			if (isset($json['extends'])) $json = array($json);

			foreach ($json as $idx_filter => $filter)
			{
				if ( ! empty($filter['extends']))
				{
					$where = str_replace('.gt', '>', $filter['expression']);
					$where = str_replace('.lt', '<', $where);
					$where = str_replace('.eq', '=', $where);
					$where = str_replace('.neq', '!=', $where);

					if (! is_array($filter['extends'])) $filter['extends'] = array($filter['extends']);

					foreach($filter['extends'] as $idx_extend => $extend_name)
					{
						$idx = $idx_filter . $idx_extend;

						$extend = self::$ci->extend_field_model->get_extend_definition_from_name(trim($extend_name));

						if ( ! is_null($extend))
						{
							$where = str_replace($extend['name'], 'efs_' . $idx . '.content', $where);
						}

						$this->{$this->db_group}->select('efs_' . $idx . '.content as extend_' . $extend['id_extend_field']);

						$this->{$this->db_group}->join(
							'extend_fields efs_' . $idx,
							"efs_" . $idx . ".id_extend_field = " . $extend['id_extend_field'] .
							" AND efs_" . $idx . ".parent = 'article'" .
							" AND efs_" . $idx . ".id_parent = article.id_article",
							'left'
						);

						// Order by
						if ( ! is_null($order_by))
						{
							$attrs = explode(',', $order_by);

							foreach($attrs as $attr_i => $attr)
							{
								if ( ! empty($attr))
								{
									$arr = preg_split("/[\s*]/i", trim($attr));

									if (trim(strtolower($arr[0])) === $extend['name'])
									{
										$_order = str_replace($arr[0], "efs_" . $idx . ".content", $attr);

										$this->{$this->db_group}->order_by($_order, NULL, FALSE);
										unset($attrs[$attr_i]);
									}
								}
								else
									unset($attrs[$attr_i]);
							}

							$order_by = implode(',', $attrs);
						}

						if (empty($order_by))
							unset($query_where['order_by']);
						else
							$query_where['order_by'] = $order_by;
					}

					$this->{$this->db_group}->where($where, '', false);
				}
			}
		}
		else
		{
			// trace('WTF !!! ');
			// trace($filters);

			$filters = ! is_array($filters) ? explode(';', $filters) : $filters;

			foreach ($filters as $idx => $filter)
			{
				$filter = str_replace('.gt', '>', $filter);
				$filter = str_replace('.lt', '<', $filter);
				$filter = str_replace('.eq', '=', $filter);
				$filter = str_replace('.neq', '!=', $filter);

				$matches = array();
				$test = preg_match("/(.*)([=<>])(.*)/", $filter, $matches);

				if ($test === 1)
				{
					$extend = self::$ci->extend_field_model->get_extend_definition_from_name(trim($matches[1]));

					if ( ! is_null($extend))
					{
						$where = str_replace(trim($matches[1]), 'efs_' . $idx . '.content', $matches[0]);

						$this->{$this->db_group}->select('efs_' . $idx . '.content as extend_' . $extend['id_extend_field']);

						$this->{$this->db_group}->join(
							'extend_fields efs_' . $idx,
							"efs_" . $idx . ".id_extend_field = " . $extend['id_extend_field'] .
							" AND efs_" . $idx . ".parent = 'article'" .
							" AND efs_" . $idx . ".id_parent = article.id_article",
							'left'
						);

						$this->{$this->db_group}->where($where, '', false);

						// Query Where
						if ( ! is_null($order_by))
						{
							$attrs = explode(',', $order_by);

							foreach($attrs as $attr_i => $attr)
							{
								if ( ! empty($attr))
								{
									$arr = preg_split("/[\s*]/i", trim($attr));

									if (trim(strtolower($arr[0])) === $extend['name'])
									{
										$_order = str_replace($arr[0], "efs_" . $idx . ".content", $attr);

										$this->{$this->db_group}->order_by($_order, NULL, FALSE);
										unset($attrs[$attr_i]);
									}
								}
								else
									unset($attrs[$attr_i]);
							}

							$order_by = implode(',', $attrs);
						}

						if (empty($order_by))
							unset($query_where['order_by']);
						else
							$query_where['order_by'] = $order_by;
					}
					else
					{
						unset($query_where['order_by']);
					}
				}
				else
				{
					// Savety : Remove order_by
					unset($query_where['order_by']);
				}
			}
		}
	}


	protected function _add_category_filter_to_query($id_category)
	{
		if ( ! empty($id_category))
		{
			$this->{$this->db_group}->join(
				$this->article_category_table,
				$this->article_category_table . '.id_article = ' . $this->table . '.id_article AND ' . $this->article_category_table . '.id_category =' . $id_category
			);
		}
	}
}
