<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Base Model
 * Extends the Model class and provides basic ionize model functionnalities
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Base model
 * @author		Ionize Dev Team
 *
 */

class Base_model extends CI_Model 
{
	/*
	 * Stores if this model is already loaded or not.
	 *
	 */ 
	protected static $_inited = false;

	/*
	 * Table name
	 *
	 */
	public $table = ''; 		// Table name

	/*
	 * Table primary key column name
	 *
	 */
	public $pk_name = '';
	
	/*
	 * Lang table of elements
	 * For example, "page" has a corresponding lang table called "page_lang"
	 *
	 */
	public $lang_table 	= '';

	/*
	 * Extended fields definition table
	 * This table contains definition of each extended field
	 *
	 */ 
	public $extend_field_table = 	'extend_field';

	/*
	 * Extended fields intances table.
	 * This table contains all the extended fields data
	 *
	 */
	public $extend_fields_table = 	'extend_fields';

	/*
	 * Extended fields prefix. Needs to be the same as the one defined in /models/base_model
	 *
	 */
	private $extend_field_prefix = 	'ion_';

	/*
	 * Stores if we already got or not the extended fields definition
	 * If we already got them, they don't need to be loaded once more...
	 *
	 */
	protected $got_extend_fields_def = false;
	
	/*
	 * Array of extended fields definition
	 * Contains all the extended fields definition for a type of data.
	 * "page" is a type of data.
	 */
	protected $extend_fields_def = array();
	

	public $limit 	= null;		// Query Limit
	public $offset 	= null;		// Query Offset

	/*
	 * Publish filter
	 * true : the content is filtered on online and published values (default)
	 * false : all content is returned
	 *
	 */
	protected static $publish_filter = true;

	
	/*
	 * Array of table names on which media can be linked
	 *
	 */
	protected $with_media_table = array('page', 'article');


	/*
	 * Array of table names on which content elements can be linked
	 *
	 */
	protected $with_elements = array('page', 'article');

	
	/*
	 * Elements definition table
	 * This table contains definition of each element
	 *
	 */ 
	public $element_definition_table = 		'element_definition';
	public $element_definition_lang_table = 	'element_definition_lang';

	/*
	 * Elements intances table.
	 * This table contains all the elements instances
	 *
	 */
	public $element_table = 		'element';

	/*
	 * Stores if we already got or not the elements definition
	 * If we already got them, they don't need to be loaded once more...
	 *
	 */
	protected $got_elements_def = false;
	
	/*
	 * Array of elements definition
	 * Contains all the elements definition.
	 */
	protected $elements_def = array();
	
	// ------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		if(self::$_inited)
		{
			return;
		}
		self::$_inited = true;

		$CI =& get_instance();
		
		// Unlock the publish filter (filter on publish status of each item)
		if (Connect()->is('editors'))
		{
			self::unlock_publish_filter();
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the model table name
	 *
	 */
	public function get_table()
	{
		return $this->table;
	}


	// ------------------------------------------------------------------------

	
	/** 
	 * Get one element
	 *
	 * @param	string		where array
	 * @param	string		Optional. Lang code
	 * @return	array		array of media
	 *
	 */
	function get($where, $lang = NULL) 
	{
		$data = array();

		if ( ! is_null($lang))
		{
			$this->db->select('t1.*, t2.*', false);
			$this->db->join($this->lang_table.' t2', 't2.'.$this->pk_name.' = t1.'.$this->pk_name, 'inner');
			$this->db->where('t2.lang', $lang);		
		}
		else
		{
			$this->db->select('t1.*', false);	
		}
	
		if ( is_array($where) )
		{
			foreach ($where as $key => $value)
			{
				$this->db->where('t1.'.$key, $value);
			}
		}
		else
		{
			$this->db->where('t1.'.$this->pk_name, $where);
		}
		
		$query = $this->db->get($this->table.' t1');

		if ( $query->num_rows() > 0)
		{
			$data = $query->row_array();
			$query->free_result();
				
			// Add medias to data array
			if (in_array($this->table, $this->with_media_table))
				$this->add_linked_media($data, $this->table, $lang);
			
		}
		
		return $data;
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Get a resultset Where
	 *
	 * @access	public
	 * @param 	array	An associative array
	 * @return	array	Result set
	 *
	 */
	public function get_where($where = null)
	{
		return $this->db->get_where($this->table, $where, $this->limit, $this->offset);
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Get all the records
	 *
	 * @access	public
	 * @return	array	Result set
	 *
	 */
	public function get_all($table = NULL)
	{
		$table = (!is_null($table)) ? $table : $this->table;
		
		$query = $this->db->get($table);
		
		return $query->result();
	}


	// ------------------------------------------------------------------------


	/**
	 * Get one row
	 *
	 * @access	public
	 * @param 	int		The result id
	 * @return	object	A row object
	 *
	 */
	public function get_row($id = NULL)
	{
		$this->db->where($this->pk_name, $id);
		$query = $this->db->get($this->table);
		
		return $query->row();
	}


	// ------------------------------------------------------------------------


	/**
	 * Get one row_array
	 *
	 * @access	public
	 * @param 	int		The result id
	 * @return	object	A row object
	 *
	 */
	public function get_row_array($id = NULL)
	{
		$this->db->where($this->pk_name, $id);
		$query = $this->db->get($this->table);
		
		return $query->row_array();
	}


	// ------------------------------------------------------------------------


	/**
	 * Get array of records
	 *
	 * @access	public
	 * @param 	array		An associative array
	 * @param 	string		table name. Optional.
	 * @return	array		Array of records
	 *
	 */
	function get_list($where = FALSE, $table = NULL)
	{
		$data = array();

		$table = (!is_null($table)) ? $table : $this->table;
		
		// Perform conditions from the $where array
		foreach(array('limit', 'offset', 'order_by', 'like') as $key)
		{
			if(isset($where[$key]))
			{
				call_user_func(array($this->db, $key), $where[$key]);
				unset($where[$key]);
			}
		}

		if ( !empty ($where) )
		{
			foreach($where as $cond => $value)
			{
				if (is_string($cond))
				{
					$this->db->where($cond, $value);
				}
				else
				{
					$this->db->where($value);
				}
			}
		}
//			$this->db->where($where, FALSE);


		$this->db->select($table.'.*');
		
		$query = $this->db->get($table);

		if ( $query->num_rows() > 0 )
			$data = $query->result_array();

		$query->free_result();
		
		return $data;
	}

	
	// ------------------------------------------------------------------------


	/** 
	 * Get element lang data (from lang table only)
	 *
	 * @param 	string	Element ID. Optional. If not set, returns all the lang table records
	 * @param	array	Arraylist of all translations rows
	 *  
	 */
	function get_lang($id = NULL)
	{
		$data = array();
		
		if ( ! is_null($id))
		{
			$this->db->where($this->pk_name, $id);
		}
		
		$query = $this->db->get($this->lang_table);
		
		if ( $query->num_rows() > 0 )
			$data = $query->result_array();
		
		$query->free_result();
		
		return $data;
	}


	// ------------------------------------------------------------------------


	/** Get post list with lang data
	 *  Used by front-end to get the elements list with lang data
	 *
	 *	@param	array	WHERE array
	 *	@param	string	Language code
	 *	@param	number	Limit to x records
	 *	@param	string	complete LIKE String
	 *	
	 *	@return	array	The complete arrayList of element, including medias
	 *
	 */
	function get_lang_list($where = FALSE, $lang = NULL)
	{
		$data = array();

		// Perform conditions from the $where array
		foreach(array('limit', 'offset', 'order_by', 'like') as $key)
		{
			if(isset($where[$key]))
			{
				call_user_func(array($this->db, $key), $where[$key]);
				unset($where[$key]);
			}
		}

		if (isset($where['where_in']))
		{
			foreach($where['where_in'] as $key => $value)
			{
				$this->db->where_in($key, $value);
			}
			unset($where['where_in']);
		}
		
		// Make sure we have only one time each element
		$this->db->distinct();

		// Lang data
		if ( ! is_null($lang))
		{
			$this->db->select($this->lang_table.'.*');
			$this->db->join($this->lang_table, $this->lang_table.'.'.$this->pk_name.' = ' .$this->table.'.'.$this->pk_name, 'inner');			
			$this->db->where($this->lang_table.'.lang', $lang);
		}

		// Main data select			
		$this->db->select($this->table.'.*', false);

		// Where ?
		if (is_array($where) )
		{
			$this->db->where($where);
		}
	
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

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get pages or articles from their lang URL
	 *
	 * @param 	Mixed	ID or array of IDs to exclude for the search
	 *
	 * @returns	Array	Array of elements
	 *
	 */
	function get_from_urls($urls, $excluded_id)
	{
		$data = array();
		
		// Main data select						
		$this->db->select($this->table.'.*', false);
		$this->db->join($this->lang_table, $this->lang_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table, 'inner');			
		$this->db->where_in($this->lang_table.'.url', $urls);
		
		// Add excluded IDs to the statement
		if ($excluded_id !='' && !is_array($excluded_id))
			$excluded_id = array($excluded_id);

		if ( !empty($excluded_id))
		{
			$this->db->where_not_in($this->lang_table.'.id_'.$this->table, $excluded_id);
		}
		
		
		$query = $this->db->get($this->table);

		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
			$query->free_result();
		}		
		
		return $data;
	}

	// ------------------------------------------------------------------------


	protected function get_extend_fields_definition()
	{
		if ($this->got_extend_fields_def == false)
		{
			$this->set_extend_fields_definition($this->table);
		}
		return $this->extend_fields_def;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the current linked childs items as a simple array from a N:M table
	 *
	 * @param	String		Items table name
	 * @param	String		Parent table name
	 * @param	Integer		Parent ID
	 * @param	String		Link table prefix. Default to ''
	 *
	 * @return	array		items keys simple array
	 *
	 */
	function get_joined_items_keys($items_table, $parent_table, $parent_id, $prefix='')
	{
		$data = array();
		
		// N to N table
		$link_table = $prefix.$parent_table.'_'.$items_table;
		
		// Items table primary key detection
		$fields = $this->db->list_fields($items_table);
		$items_table_pk = $fields[0];
		
		// Parent table primary key detection
		$fields = $this->db->list_fields($parent_table);
		$parent_table_pk = $fields[0];
		
		$this->db->where($parent_table_pk, $parent_id);
		$this->db->select($items_table_pk);
		$query = $this->db->get($link_table);

		foreach($query->result() as $row)
		{
			$data[] = $row->$items_table_pk;
		}
		
		return $data;
	}



	// ------------------------------------------------------------------------

	/**
	 * Returns the content of a link table based on conditions in this table
	 *
	 * @param	String		Parent table name
	 * @param	String		Child table name
	 * @param	Array		Array of conditions
	 * @param	int			Data from first or second table. Default 1
	 * @param	String		Link table prefix. Default to ''
	 *
	 * @return	array		Array of Hashtable
	 *
	 */
	function get_linked_items($first_table, $second_table, $cond, $join=1, $prefix = '')
	{
		$data = array();
		
		$second_pk_name = $this->get_pk_name($second_table);
		$first_pk_name = $this->get_pk_name($first_table);
		
		// N to N table
		$link_table = $prefix.$first_table.'_'.$second_table;

		// Correct ambiguous columns
		$cond = $this->correct_ambiguous_conditions($cond, $link_table);

		$this->db->from($link_table);
		$this->db->where($cond);

		if ($join == 2)
		{
			$this->db->join($second_table, $second_table.'.'.$second_pk_name.' = '.$link_table.'.'.$second_pk_name);
		}
		else
		{
			$this->db->join($first_table, $first_table.'.'.$first_pk_name.' = '.$link_table.'.'.$first_pk_name);
		}
		
		$query = $this->db->get();

		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
		}			
		
		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Same as get_linked_items, but considering the language code
	 *
	 * @param	String		Parent table name
	 * @param	String		Child table name
	 * @param	Array		Array of conditions
	 * @param	String		Lang code
	 * @param	String		Link table prefix. Default to ''
	 *
	 * @return	array		Array of Hashtable
	 *
	 */
	function get_linked_lang_items($parent_table, $child_table, $cond, $lang, $prefix = '')
	{
		$data = array();
		
		$child_pk_name = $this->get_pk_name($child_table);
		
		// N to N table
		$link_table = $prefix.$parent_table.'_'.$child_table;
		
		// Child lang table
		$child_lang_table = $child_table.'_lang';

		$this->db->from($link_table);
		$this->db->where($this->correct_ambiguous_conditions($cond,$link_table) );
		$this->db->where('lang', $lang);
		
		$this->db->join($child_table, $child_table.'.'.$child_pk_name.' = '.$link_table.'.'.$child_pk_name);
		$this->db->join($child_lang_table, $child_lang_table.'.'.$child_pk_name.' = '.$child_table.'.'.$child_pk_name);
		
		$query = $this->db->get();

		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
		}			
		
		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Gets items key and value as an associative array
	 *
	 * @param	string	Elements table name
	 * @param	string	Value field name
	 * @param	string	Zero value name. Usefull when feeding a selectbox
	 * @param	string	Orderby SQL string 
	 * @param	String	Glue between data if value is an array
	 *
	 */
	function get_items_select($items_table, $field, $nothing_value = NULL, $order_by = NULL, $glue="")
	{
		$data = array();
		
		// Add the Zero value item
		if ( ! is_null($nothing_value))
			$data = array('0' => $nothing_value);

		// Items table primary key detection
		$fields = $this->db->list_fields($items_table);
		$items_table_pk = $fields[0];

		// ORDER BY
		if ( ! is_null($order_by))
			$this->db->order_by($order_by);

		// Query
		$query = $this->db->get($items_table);

		foreach($query->result() as $row)
		{
			if (is_array($field))
			{
				$value = array();
				foreach($field as $f)
				{
					$value[] = $row->$f;
				}
				$data[$row->$items_table_pk] = implode($glue, $value);
			}
			else
			{
				$data[$row->$items_table_pk] = $row->$field;
			}
		}
		
		return $data;
	}


	// ------------------------------------------------------------------------


	function simple_search($term, $field, $limit)
	{
		$data = array();
		
		$this->db->like($this->table.'.'.$field, $term);

		$this->db->limit($limit);
		
		$this->db->select($this->pk_name.','.$field);
		
		$query = $this->db->get($this->table);

		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
		}
		
		return $data;	
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the first PK field nam found for the given table
	 *
	 */
	function get_pk_name($table)
	{
		$fields = $this->db->field_data($table);
		
		foreach ($fields as $field)
		{
			if ($field->primary_key)
			{
				return $field->name;
				break;
			}
		}
		return FALSE;
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Sets the current table
	 *
	 * @param string	table name
	 *
	 */
	public function set_table($table)
	{
		$this->table = $table;
	}


	// ------------------------------------------------------------------------


	/**
	 * Sets the current table pk
	 *
	 * @param string	table pk name
	 *
	 */
	public function set_pk_name($pk_name)
	{
		$this->pk_name = $pk_name;
	}


	// ------------------------------------------------------------------------


	/**
	 * Sets the current lang table name
	 *
	 * @param string	lang table name
	 *
	 */
	public function set_lang_table($table)
	{
		$this->lang_table = $table;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the elements definition and store them in the private property "elements_def"
	 *
	 * @param	String	Parent type
	 * @return	Array	Extend fields definition array
	 */
	protected function set_elements_definition($lang)
	{
		$CI =& get_instance();

		// Loads the model if it isn't loaded
		if (!isset($CI->element_definition_model))
			$CI->load->model('element_definition_model');
			
		// Get the extend fields definition if not already got
		if ($this->got_elements_def == false)
		{
			// Store the extend fields definition
			$this->elements_def = $CI->element_definition_model->get_lang_list(FALSE, $lang);
			
			// Set this to true so we don't get the extend field def a second time for an object of same kind
			$this->got_elements_def = true;
		}
	}

	// ------------------------------------------------------------------------


	/**
	 * Get the extend fields definition and store them in the private property "extend_fields_def"
	 *
	 * @param	String	Parent type
	 * @return	Array	Extend fields definition array
	 *
	 */
	protected function set_extend_fields_definition($parent)
	{
		$CI =& get_instance();

		// Loads the model if it isn't loaded
		if (!isset($CI->extend_field_model))
			$CI->load->model('extend_field_model');
			
		// Get the extend fields definition if not already got
		if ($this->got_extend_fields_def == false)
		{
			// Store the extend fields definition
			$this->extend_fields_def = $CI->extend_field_model->get_list(array('extend_field.parent' => $parent));
			
			// Set this to true so we don't get the extend field def a second time for an object of same kind
			$this->got_extend_fields_def = true;
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Save one element, including lang depending data
	 *
	 * @param 	array	Standard data table
	 * @param 	array	Lang depending data table. optional.
	 *
	 * @return 	int		Saved element ID
	 *
	 */
	function save($data, $dataLang = false) 
	{
		/*
		 * Base data save
		 */
	 	$data = $this->clean_data($data);

		$id = FALSE;

		// Insert
		if( ! isset($data[$this->pk_name]) || $data[$this->pk_name] == '' )
		{
			// Remove the ID so the generated SQL will be clean (no empty String insert in the table PK field)
			unset($data[$this->pk_name]);
			
			$this->db->insert($this->table, $data);
			$id = $this->db->insert_id();
		}
		// Update
		else
		{
			$this->db->where($this->pk_name, $data[$this->pk_name]);
			$this->db->update($this->table, $data);
			$id = $data[$this->pk_name];
		}

		/*
		 * Lang data save
		 */
		if ( ($dataLang !== false) && ( !empty($dataLang) ) )
		{
			foreach(Settings::get_languages() as $language)
			{
				foreach($dataLang as $lang => $data)
				{
					if($lang == $language['lang'])
					{
						$where = array(
									$this->pk_name => $id,
									'lang' => $lang
								  );
	
						// Update
						if( $this->exists($where, $this->lang_table))
						{
							$this->db->where($where);
							$this->db->update($this->lang_table, $data);
						}
						// Insert
						else
						{
							// Correct lang & pk field on lang data array
							$data['lang'] = $lang;
							$data[$this->pk_name] = $id;
							
							$this->db->insert($this->lang_table, $data);
						}
					}
				}
			}
		}
		return $id;
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves ordering for items in the current table or in the join table, depending on parent var.
	 *
	 * @param	mixed	String of coma separated new order or array of order
	 * @return	string	Coma separated order
	 *
	 */
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
				$parent_pk = $this->get_pk_name($parent);
				
				$this->db->where($parent.'_'.$this->table.'.'.$parent_pk, $id_parent);
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


	// ------------------------------------------------------------------------


	/**
	 * Save link between a parent and a child in a link table : N:N
	 * The parent table is supposed to be the current table
	 *
	 * @param	Mixed	Parent table PK value
	 * @param	String	Child table name
	 * @param	Mixed	Child table PK value
	 * @param	Array	Data to add to the link table
	 * @param	String	Link table prefix.
	 *
	 */
	function save_simple_link($parent_table, $id_parent, $child_table, $id_child, $context_data = array(), $prefix='')
	{
		$link_table = $prefix.$parent_table.'_'.$child_table;
	
		// PK fields
		$parent_pk_name = $this->get_pk_name($parent_table);
		$child_pk_name = $this->get_pk_name($child_table);
	
		if (FALSE == $this->exists(array($parent_pk_name => $id_parent, $child_pk_name => $id_child), $link_table) )
		{
			$data = array(
				$parent_pk_name => $id_parent,
				$child_pk_name => $id_child
			);
						
			if ( ! empty($context_data) )
			{
				// Cleans the context data array by removing data not in context table
				$context_data = $this->clean_data($context_data, $link_table);
				
				$data = array_merge($context_data, $data);				
			}
			
			$this->db->insert($link_table, $data);

			return TRUE;
		}
		
		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Join multiple items keys to a parent through a N:M table
	 *
	 * Items are consired as 'childs' and will be attached to a 'parent' through the join table.
	 * That means before saving, all rows with the 'parent ID' key will be deleted in the join table.
	 *
	 * Note: 	When attaching 'categories' to an 'article', the category array will be considered as 'child'
	 *			and the article as 'parent'.
	 *			That means the join table MUST be named 'parent_child'.
	 *			Example : ARTICLE_CATEGORY is the join table between articles and categories
	 *			In that case, the tables ARTICLE and the table CATEGORY MUST exist
	 *
	 * @param	string		parent table name.
	 * @param	int			parent ID
	 * @param	string		items table name
	 * @param	array		items to save. Simple array of keys.
	 * @param	String		Link table prefix. Default to ''
	 *
	 * @return	int		number of attached items
	 *
	 */
//	function join_items_keys_to($child_table, $items, $parent_table, $parent_id, $prefix)
	function save_multiple_links($parent_table, $parent_id, $child_table, $items, $prefix = '')
	{
		// N to N table
		$link_table = $prefix.$parent_table.'_'.$child_table;
		
		// PK fields
		$parent_pk_name = $this->get_pk_name($parent_table);
		$child_pk_name = $this->get_pk_name($child_table);


		// Delete existing link between items table and parent table
		$this->db->where($parent_pk_name, $parent_id);
		$this->db->delete($link_table);

		// nb inserted items
		$nb = 0;
		
		// Insert 
		if ( !empty($items) )
		{
			foreach($items as $item)
			{
				if($item != 0 && $item !== false)
				{
					$data = array(
					   $parent_pk_name => $parent_id,
					   $child_pk_name => $item
					);

					$this->db->insert($link_table, $data);
					$nb += 1;
				}
			}
		}
		
		return $nb;
	}


	// ------------------------------------------------------------------------


	/**
	 * Unlink one parent and one child
	 *
	 * TODO : Replace function "delete_joined_keys" by this 
	 *
	 * @param	Mixed	Parent table PK value
	 * @param	String	Child table name
	 * @param	Mixed	Child table PK value
	 * @param	String	Link table prefix.
	 *
	 */
	function delete_simple_link($parent_table, $id_parent, $child_table, $id_child, $prefix)
	{
		// N to N table
		$link_table = $prefix.$parent_table.'_'.$child_table;
		
		// PK fields
		$parent_pk_name = $this->get_pk_name($parent_table);
		$child_pk_name = $this->get_pk_name($child_table);

		$this->db->where($parent_pk_name, $id_parent);
		$this->db->where($child_pk_name, $id_child);

		return (int) $this->db->delete($link_table);
	}

	// ------------------------------------------------------------------------


	/**
	 * Add all media for one element to an array and returns this array
	 *
	 * @param	array	By ref. The array to add the media datas
	 * @param	string	parent name. Example : 'page', 'article', etc.
	 * @param	string	Lang code
	 *
	 */
	protected function add_linked_media(&$data, $parent, $lang = NULL)
	{
		// Select medias
		$this->db->select('*, media.id_media');
		$this->db->from('media,'. $parent .'_media');
		$this->db->where('media.id_media', $parent.'_media.id_media', false);
		$this->db->order_by($parent.'_media.ordering');

		if ( ! is_null($lang))
		{
			$this->db->join('media_lang', 'media.id_media = media_lang.id_media', 'left outer');
			$this->db->where('(media_lang.lang =\'', $lang.'\' OR media_lang.lang is null )', false);
		}
		
		$query = $this->db->get();

		$result = array();

		// Feed each media array
		if($query->num_rows() > 0)
		{
			$result = $query->result_array();
		}			

		// If the data array is a list of arrays
		if (isset($data[0]) && is_array($data[0]))
		{
			foreach($data as $k=>$el)
			{
//				$data[$k]['medias'] = array_values(array_filter($result, create_function('$row','return $row["'.$this->pk_name.'"] == "'. $el[$this->pk_name] .'";')));
				$data[$k]['medias'] = array();
				foreach($result as $row)
				{
					if ($row[$this->pk_name] == $el[$this->pk_name])
						$data[$k]['medias'][] = $row;
				}
				
				
				// Add extended fields values for each media
				// Needs to be improved as the extend fieldsdefinition loaded in $this->extend_fields_def are these from the table and not from the medias...
				// But this has no importance, it's just not clean.
				if ( ! empty($data[$k]['medias']))
					$this->add_extend_fields($data[$k]['medias'], 'media', $lang);
				
				// Add file extension to each media
				foreach($data[$k]['medias'] as &$media)
				{
					$media['extension'] = pathinfo($media['file_name'], PATHINFO_EXTENSION);
					$media['mime'] = get_mime_by_extension($media['file_name']);
				}
			}
		}
		// The data array is a hashtable
		else
		{
			// $data['medias'] = array_values(array_filter($result, create_function('$row','return $row["'.$this->pk_name.'"] == "'. $data[$this->pk_name] .'";')));
			$data['medias'] = array();
			foreach($result as $row)
			{
				if ($row[$this->pk_name] == $data[$this->pk_name])
					$data['medias'][] = $row;
			}
			
			if ( ! empty($data['medias']))
				$this->add_extend_fields($data['medias'], 'media', $lang);
			
			// Add file extension to each media
			foreach($data['medias'] as &$media)
			{
				$media['extension'] = pathinfo($media['file_name'], PATHINFO_EXTENSION);
				$media['mime'] = get_mime_by_extension($media['file_name']);
			}
		}
		
		$query->free_result();
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds to each element (page or article) the "urls" field, containing the URL for each language code
	 *
	 * @param	array	By ref. The array to add the urls datas
	 * @param	string	parent name. Example : 'page', 'article', etc.
	 */
	protected function add_lang_urls(&$data, $parent)
	{
		// Element ID
		$id = 'id_'.$parent;
		
		// Array of IDs to get.
		$ids = array();
		foreach($data as $element)
		{
			$ids[] = $element[$id];
		}
		
		if ( ! empty($ids))
		{
			$this->db->select($id .',' .$parent . '_lang.lang,' . $parent . '_lang.url');
			$this->db->where($id . ' in (' . implode(',' , $ids ) . ')' );
			$this->db->from($parent . '_lang');
		
			$query = $this->db->get();
	
			$result = array();
	
			// Feed each media array
			if($query->num_rows() > 0)
				$result = $query->result_array();
	
			$languages = Settings::get_languages();
			
			// data must be a list of arrays
			if (isset($data[0]) && is_array($data[0]))
			{
				foreach($data as $k => $el)
				{
					foreach($languages as $language)
					{
						foreach($result as $row)
						{
							if ($row[$id] == $el[$id] && $row['lang'] == $language['lang'])
							{
								$data[$k]['urls'][$row['lang']] = $row['url'];
							}
						}
						// $url = array_values(array_filter($result, create_function('$row','return ($row["id_'.$this->table.'"] == "'. $el['id_'.$this->table] .'" && $row["lang"] == "'.$language['lang'].'");')));
						// $url = (!empty($url[0])) ? $url[0]['url'] : '';
						// $data[$k]['urls'][$language['lang']] = $url;
					}
				}
			}
		}	
	}


	// ------------------------------------------------------------------------


	/**
	 * Add extended fields and their values if website settings allow it.
	 * 
	 * @param	Array	Data array. By ref.
	 * @param	String	Parent type. can be "page", "article", etc.
	 * @param	String	Lang code
	 *
	 */
	protected function add_elements(&$data, $parent, $lang)
	{	
		$CI =& get_instance();

		// Loads the model if it isn't loaded
		if (!isset($CI->element_definition_model))
			$CI->load->model('element_definition_model');


		// get the elements definition array
		$this->set_elements_definition($lang);

		// Get the elements ID to filter the SQL on...
		$ids = array();
		
		foreach ($data as $d)
		{
			$ids[] = $d['id_'.$parent];
		}
/*		
		// Get all definitions
		$definitions = $this->get_lang_list(array('order_by' => 'ordering ASC'), Settings::get_lang('default'));

		if ( ! is_null($lang))
		{
			$this->db->select($this->element_definition_lang_table.'.*');
			$this->db->join($this->element_definition_table, $this->element_definition_lang_table.'.id_'.$this->element_definition_table.' = ' .$this->element_definition_table.'.id_'.$this->element_definition_table, 'inner');			
			$this->db->order_by($this->element_definition_table.'ordering', 'ASC');
			$this->db->where($this->element_definition_lang_table.'.lang', $lang);

			$query = $this->db->get($this->element_definition_lang_table);

		}
*/
		$elements = $CI->element_definition_model->get_definitions_from_parent($parent);


		// trace($elements);


	}

	// ------------------------------------------------------------------------


	/**
	 * Add extended fields and their values if website settings allow it.
	 * 
	 * @param	Array	Data array. By ref.
	 * @param	String	Parent type. can be "page", "article", etc.
	 * @param	String	Lang code
	 *
	 */
	protected function add_extend_fields(&$data, $parent, $lang = NULL)
	{	
		// get the extend fields definition array
		$this->set_extend_fields_definition($this->table);
		
		// Get the elements ID to filter the SQL on...
		$ids = array();
		foreach ($data as $d)
		{
			if ( ! empty($d['id_'.$parent]))
				$ids[] = $d['id_'.$parent];
		}
		
		if ( ! empty($ids))
		{
			// Get the extend fields details, filtered on parents ID
			$this->db->where(array('extend_field.parent'=>$parent));
			$this->db->where_in($ids);
			$this->db->join($this->extend_fields_table, $this->extend_field_table.'.id_'.$this->extend_field_table.' = ' .$this->extend_fields_table.'.id_'.$this->extend_field_table, 'inner');			

			$query = $this->db->get($this->extend_field_table);

			$result = array();
			if ( $query->num_rows() > 0)
				$result = $query->result_array();
			
			// Filter the result by lang : Only returns the not translated data and the given language translated data
			// $result = array_filter($result,  create_function('$row','return ($row["lang"] == "'. $lang .'" || $row["lang"] == "" );'));
			$filtered_result = array();
			foreach($result as $res)
			{
				if ($res['lang'] == $lang || $res['lang'] == '' )
					$filtered_result[] = $res;
			}

			// Attach each extra field to the corresponding data array
			foreach ($data as &$d)
			{
				// Store the extend definition array
				// Not usefull for the moment.
				// Can be used for debugging
				// $d['_extend_fields_definition'] = $this->get_extend_fields_definition();
				
				// First set the extended fields of the data row to an empty value. So it exists...
				foreach ($this->extend_fields_def as $e)
				{
					$d[$this->extend_field_prefix.$e['name']] = '';
				}
				
				// Feeds the extended fields
				// Each extended field will be prefixed to avoid collision with standard fields names
				foreach ($result as $e)
				{
					if (empty($e['content']) && !empty($e['default_value']))
						$e['content'] = $e['default_value'];
				
					if ($d['id_'.$parent] == $e['id_parent'])
					{
						$d[$this->extend_field_prefix.$e['name']] = $e['content'];
					}
				}
			}			
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Join multiple items keys to a parent through a N:M table
	 *
	 * Items are consired as 'childs' and will be attached to a 'parent' through the join table.
	 * That means before saving, all rows with the 'parent ID' key will be deleted in the join table.
	 *
	 * Note: 	When attaching 'categories' to an 'article', the category array will be considered as 'child'
	 *			and the article as 'parent'.
	 *			That means the join table MUST be named 'parent_child'.
	 *			Example : ARTICLE_CATEGORY is the join table between articles and categories
	 *			In that case, the tables ARTICLE and the table CATEGORY MUST exist
	 *
	 * @param	string		items table name
	 * @param	string/array		items to save. Simple array of keys.
	 * @param	string		parent table name.
	 * @param	int			parent ID
	 *
	 * @return	int		number of attached items
	 *
	 */
	function join_items_keys_to($items_table, $items, $parent_table, $parent_id)
	{
		// N to N table
		$link_table = $parent_table.'_'.$items_table;
		
		// Items table primary key detection
		$fields = $this->db->list_fields($items_table);
		$items_table_pk = $fields[0];
		
		// Parent table primary key detection
		$fields = $this->db->list_fields($parent_table);
		$parent_table_pk = $fields[0];
		
		// Delete existing link between items table and parent table
		$this->db->where($parent_table_pk, $parent_id);
		$this->db->delete($link_table);

		// nb inserted items
		$nb = 0;
		
		// Insert 
		if ( !empty($items) )
		{
			foreach($items as $item)
			{
				if($item != 0 && $item !== false)
				{
					$data = array(
					   $parent_table_pk => $parent_id,
					   $items_table_pk => $item
					);

					$this->db->insert($link_table, $data);
					$nb += 1;
				}
			}
		}
		
		return $nb;
	}


	// ------------------------------------------------------------------------


	/**
	 * Deletes one join row between an item and its parent
	 *
	 * @param	string		items table name
	 * @param	int			item ID to delete
	 * @param	string		parent table name.
	 * @param	int			parent ID
	 *
	 * @return	int			number of affected rows
	 *
	 */
	function delete_joined_key($items_table, $item_key, $parent_table, $parent_id)
	{
		// N to N table
		$link_table = $parent_table.'_'.$items_table;
		
		// Items table primary key detection
		$fields = $this->db->list_fields($items_table);
		$items_table_pk = $fields[0];
		
		// Parent table primary key detection
		$fields = $this->db->list_fields($parent_table);
		$parent_table_pk = $fields[0];

		$this->db->where(array(
			$parent_table_pk => $parent_id,
			$items_table_pk => $item_key
		));

		return (int) $this->db->delete($link_table);
	}


	// ------------------------------------------------------------------------


	/**
	 * Set an item online / offline depending on its current status
	 *
	 * @param	int			item ID
	 *
	 * @return 	boolean		New status
	 *
	 */
	function switch_online($id)
	{
		// Current status
		$status = $this->get_row($id)->online;
	
		// New status
		($status == 1) ? $status = 0 : $status = 1;

		// Save		
		$this->db->where($this->pk_name, $id);
		$this->db->set('online', $status);
		$this->db->update($this->table);
		
		return $status;
	}


	// ------------------------------------------------------------------------


	/**
	 * Feed the template array with data for each field in the table
	 *
	 * @param	int		ID of the search element
	 * @param	array	By ref, the template array
	 *
	 */
	function feed_template($id, &$template)
	{
		$data = $this->get($id);

		foreach($data as $key=>$val)
		{
			$template[$key] = $val;
		}

	}


	// ------------------------------------------------------------------------


	/**
	 * Feed the template array with data for each field in language table
	 *
	 * @param	array	By ref, the template array
	 *
	 */
	function feed_lang_template($id, &$template)
	{
		// lang_table fields
		$fields = NULL;
		$rows = $this->get_lang($id);

		foreach(Settings::get_languages() as $language)
		{
			$lang = $language['lang'];
		
			// Feeding of template languages elements
			foreach($rows as $row)
			{
				if($row['lang'] == $lang)
				{
					$template[$lang] = $row;
				}
			}
			
			// Language not defined : Feed with blank data
			if( ! isset($template[$lang]))
			{
				// Get lang_table fields if we don't already have them
				if (is_null($fields))
					$fields = $this->db->list_fields($this->lang_table);

				foreach ($fields as $field)
				{
					if ($field != $this->pk_name)
						$template[$lang][$field] = '';
					else
						$template[$lang][$this->pk_name] = $id;
				}
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Feeds the template array with blank data for each field in the table
	 *
	 * @param	array	By ref, the template array
	 *
	 */
	function feed_blank_template(&$template = FALSE)
	{
		if ($template == FALSE) $template = array();

		$fields = $this->db->list_fields($this->table);

		$fields_data = $this->field_data($this->table);

		foreach ($fields as $field)
		{
			$field_data = array_values(array_filter($fields_data, create_function('$row', 'return $row["Field"] == "'. $field .'";')));
			$field_data = (isset($field_data[0])) ? $field_data[0] : false;

			$template[$field] = (isset($field_data['Default'])) ? $field_data['Default'] : '';
		}
		return $template;
	}


	// ------------------------------------------------------------------------


	/**
	 * Feed the template array with blank data for each field in language table
	 *
	 * @param	array	By ref, the template array
	 *
	 */
	function feed_blank_lang_template(&$template = FALSE)
	{
		if ($template == FALSE) $template = array();
	
		$fields = $this->db->list_fields($this->lang_table);

		$fields_data = $this->field_data($this->lang_table);
					
		foreach(Settings::get_languages() as $language)
		{
			$lang = $language['lang'];
			
			foreach ($fields as $field)
			{
				$field_data = array_values(array_filter($fields_data, create_function('$row', 'return $row["Field"] == "'. $field .'";')));
				$field_data = (isset($field_data[0])) ? $field_data[0] : false;
				
				$template[$lang][$field] = (isset($field_data['Default'])) ? $field_data['Default'] : '';
			}
		}
		return $template;
	}


	// ------------------------------------------------------------------------


	/** 
	 * Switch the publish filter off
	 * 
	 */
	public function unlock_publish_filter()
	{
		self::$publish_filter = false;
	}


	// ------------------------------------------------------------------------


	/**
	 * Insert a row
	 *
	 * @access	public
	 * @param 	array	An associative array of data
	 * @return	the last inserted id
	 *
	 */
	public function insert($data = null)
	{
		$data = $this->clean_data($data);
		
		$this->db->insert($this->table, $data);
		
		return $this->db->insert_id();
	}
	

	// ------------------------------------------------------------------------

	
	/**
	 * Update a row
	 *
	 * @access	public
	 *
	 * @param 	Mixed		Where condition. If single value, PK of the table
	 * @param 	array		An associative array of data
	 * @param 	String		Table name. If not set, current models table
	 *
	 * @return	int			Number of updated rows
	 *
	 */
	public function update($where = NULL, $data = NULL, $table = FALSE)
	{
		$table = (FALSE !== $table) ? $table : $this->table;
	
		if ( is_array($where) )
		{
			$this->db->where($where);
		}
		else
		{
			$pk_name = $this->get_pk_name($table);
			$this->db->where($pk_name, $where);
		}
	
		$this->db->update($table, $data);
		
		return (int) $this->db->affected_rows();
	}

	
	// ------------------------------------------------------------------------

	
	/**
	 * Delete a row
	 *
	 * @access	public
	 *
	 * @param 	int		Where condition. If single value, PK of the table
	 * @param 	String		Table name. If not set, current models table
	 *
	 * @return	int		Number of deleted rows
	 *
	 */
	public function delete($where = NULL, $table = FALSE)
	{
		$table = (FALSE !== $table) ? $table : $this->table;
		
		if ( is_array($where) )
		{
			$this->db->where($where);
		}
		else
		{
			$pk_name = $this->get_pk_name($table);
			$this->db->where($pk_name, $where);
		}
	
		$this->db->delete($table);
				
		return (int) $this->db->affected_rows();
	}

	
	// ------------------------------------------------------------------------

	
	/**
	 * Count all rows in a table or count all results from the current query
	 *
	 * @access	public
	 * @param	bool	true / false
	 * @return	int 	The number of all results
	 *
	 */
	public function count_all($results = false)
	{
		if($results !== false)
		{
			$query = $this->db->count_all_results($this->table);
		}
		else
		{
			$query = $this->db->count_all($this->table);
		}
		
		return (int) $query;
	}

	
	// ------------------------------------------------------------------------

	
	/**
	 * Empty table
	 *
	 * @access	public
	 * @return	void
	 *
	 */
	public function empty_table()
	{
		$this->db->empty_table($this->table);
	}

	
	// ------------------------------------------------------------------------

	
	/**
	 * Check if a record exists in a table
	 *
	 * @access	public
	 * @return	boolean
	 *
	 */
	public function exists($where = NULL, $table = NULL)
	{
		$table = ( ! is_null($table)) ? $table : $this->table ;
		
		$query = $this->db->get_where($table, $where);

		if ($query->num_rows() > 0) 
			return TRUE; 
		else
			return FALSE;
	}
		
	
	// ------------------------------------------------------------------------


	/**
	 * Returns the table fields array list
	 *
	 * @param	String		Table name
	 * @return	Array		Array of field names
	 *
	 */
	function field_data($table)
	{
		$query = $this->db->query("SHOW COLUMNS FROM " . $table);
	
		return $query->result_array();
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Removes from the data array the index which are not in the table
	 *
	 * @param	Array	The data array to clean
	 * @param	String	Reference table. $this->table if not set.
	 *
	 */
	function clean_data($data, $table = FALSE)
	{
		$cleaned_data = array();
	
		if ( ! empty($data))
		{
			$table = ($table !== FALSE) ? $table : $this->table;
			
			$fields = $this->db->list_fields($table);
			
			$fields = array_fill_keys($fields,'');
	
			$cleaned_data = array_intersect_key($data, $fields);
		}
		return $cleaned_data;
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Correct ambiguous target fields in SQL conditions
	 *
	 * @param	Array	condition array
	 * @param	String	Table name
	 *
	 * @return	Array	Corrected condition array
	 *
	 */
	function correct_ambiguous_conditions($array, $table)
	{
		if (is_array($array))
		{
		/*
			foreach ($array as $key => $val)
			{
				if ($key == $this->pk_name)
				{
					unset($array[$key]);
					$key = $this->table.'.'.$key;
					$array[$key] = $val;
				}
			}
		*/
			foreach ($array as $key => $val)
			{
				unset($array[$key]);
				$key = $table.'.'.$key;
				$array[$key] = $val;
			}

			return $array;
		}
	}
	
}


/* End of file base.php */
/* Location: ./application/models/base.php */