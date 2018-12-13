<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
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
 * @category	Model
 * @author		Ionize Dev Team
 *
 */

class Base_model extends CI_Model
{
	/*
	 * Stores if this model is already loaded or not.
	 *
	 */
	protected static $_inited = FALSE;


	public $db_group = 'default';


	public static $ci = NULL;

	/*
	 * Table name
	 *
	 */
	public $table = NULL; 		// Table name

	/*
	 * Table primary key column name
	 *
	 */
	public $pk_name = NULL;

	/*
	 * Lang table of elements
	 * For example, "page" has a corresponding lang table called "page_lang"
	 *
	 */
	public $lang_table 	= NULL;

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
	 * Array of extended fields definition
	 * Contains all the extended fields definition for a type of data.
	 * "page" is a type of data.
	 */
	protected $extend_fields_def = array();


	public $limit 	= NULL;		// Query Limit
	public $offset 	= NULL;		// Query Offset

	/*
	 * Publish filter
	 * true : the content is filtered on online and published values (default)
	 * false : all content is returned
	 *
	 */
	protected static $publish_filter = TRUE;


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
	protected $got_elements_def = FALSE;

	/*
	 * Array of elements definition
	 * Contains all the elements definition.
	 */
	protected $elements_def = array();

	/*
	 * Array of languages
	 *
	 */
	protected $_languages = NULL;


	/*
	 * Local store of list_fields results
	 *
	 */
	protected $_list_fields = array();

	/**
	 * Local store of SHOW FULL COLUMNS FROM results
	 * @var array
	 */
	protected $_fields_data = array();


	/**
	 * Table definition
	 * Used for pagination list. see: Base_model::get_pagination_list()
	 *
	 * @var array
	 */
	protected $_table_definition = array();


	// ------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		if (is_null($this->db_group))
		{
			$active_group = 'default';
			include(APPPATH . 'config/database.php');
			$this->db_group = $active_group;
		}

		$this->{$this->db_group} = $this->load->database($this->db_group, TRUE, TRUE);

		self::$ci =& get_instance();
		$segments = array_values(self::$ci->uri->segment_array());
		$seg_0 = array_shift($segments);
		$seg_1 = array_shift($segments);

		if (($seg_0 == config_item('admin_url')) || ($seg_1 == config_item('admin_url')) )
		{
			$this->{$this->db_group}->cache_delete_all();
			$this->{$this->db_group}->cache_off();
		}

		if(self::$_inited)
		{
			return;
		}

		self::$_inited = TRUE;

		// Unlock the publish filter (filter on publish status of each item)
		if (Authority::can('access', 'admin'))
		{
			self::unlock_publish_filter();
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the model table name
	 *
	 * @return	string
	 */
	public function get_table()
	{
		return $this->table;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the model lang table name
	 *
	 * @return	string
	 */
	public function get_lang_table()
	{
		return $this->lang_table;
	}


	// ------------------------------------------------------------------------

	/**
	 * @param	array $table_definition
	 */
	public function set_table_definition($table_definition)
	{
		$this->_table_definition = $table_definition;
	}


	// ------------------------------------------------------------------------

	/**
	 * @return array
	 */
	public function get_table_definition()
	{
		return $this->_table_definition;
	}


	// ------------------------------------------------------------------------


	/** 
	 * Get one element
	 *
	 * @param	string	$where		array or record ID of extending entity
	 * @param	string	$lang		Optional. Lang code
	 * @return	array of media
	 */
	public function get($where, $lang = NULL)
	{
		$data = array();

		if ( ! is_null($lang))
		{
			$this->{$this->db_group}->select($this->table.'.*,'.$this->lang_table.'.*', FALSE);
			$this->{$this->db_group}->join(
				$this->lang_table,
				$this->lang_table.'.'.$this->pk_name.' = '.$this->table.'.'.$this->pk_name,
				'inner'
			);
			$this->{$this->db_group}->where($this->lang_table.'.lang', $lang);
		}
		else
		{
			$this->{$this->db_group}->select($this->table.'.*', FALSE);
		}

		if ( is_array($where) )
		{
			$this->_process_where($where);
		}
		else
		{
			$this->{$this->db_group}->where($this->table.'.'.$this->pk_name, $where);
		}

		// ID
		if ($this->pk_name != NULL)
			$this->{$this->db_group}->select($this->table.'.'.$this->pk_name. ' as id', FALSE);

		$query = $this->{$this->db_group}->get($this->table);

		if ( $query->num_rows() > 0)
		{
			$data = $query->row_array();
			$query->free_result();

			// Add medias to data array
			if (in_array($this->table, $this->with_media_table))
				$this->add_linked_media($data, $this->table, $lang);

			// Add data for other languages
			if ($this->lang_table != null && ! is_array($where))
			{
				$data['languages'] = array();

				$fields = $this->list_fields($this->lang_table);

				$this->{$this->db_group}->where($this->lang_table.'.'.$this->pk_name, $where);
				$query = $this->{$this->db_group}->get($this->lang_table);

				$lang_data = array();

				if ( $query->num_rows() > 0)
					$lang_data = $query->result_array();

				foreach(Settings::get_languages() as $language)
				{
					$data['languages'][$language['lang']] = array_fill_keys($fields, NULL);

					foreach($lang_data as $row)
					{
						if ($row['lang'] == $language['lang'])
							$data['languages'][$language['lang']] = $row;
					}
				}
			}
		}

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get WHERE result set
	 *
	 * @access	public
	 * @param 	array	$where	An associative array
	 * @return	array	Result set
	 */
	public function get_where($where = NULL)
	{
		return $this->{$this->db_group}->get_where($this->table, $where, $this->limit, $this->offset);
	}


	// ------------------------------------------------------------------------


	/**
	 * Get all the records
	 *
	 * @param   string $table
	 * @return  mixed
	 */
	public function get_all($table = NULL)
	{
		$table = (!is_null($table)) ? $table : $this->table;

		$query = $this->{$this->db_group}->get($table);
		
		return $query->result_array();
	}


	// ------------------------------------------------------------------------


	/**
	 * Get one row
	 *
	 * @access	public
	 * @param 	int		$id     The result ID
	 * @return	A row object
	 */
	public function get_row($id = NULL)
	{
		$this->{$this->db_group}->where($this->pk_name, $id);
		$query = $this->{$this->db_group}->get($this->table);

		return $query->row();
	}


	// ------------------------------------------------------------------------


	/**
	 * Get one row_array
	 *
	 * @param   string  [$where]
	 * @param   string  [$table]
	 * @return  mixed
	 */
	public function get_row_array($where = NULL, $table = NULL)
	{
		$table = ($table == NULL) ? $this->table : $table;

		if ( is_array($where) )
		{
			// Perform conditions from the $where array
			foreach(array('limit', 'offset', 'order_by', 'like') as $key)
			{
				if(isset($where[$key]))
				{
					call_user_func(array($this->{$this->db_group}, $key), $where[$key]);
					unset($where[$key]);
				}
			}
			if (isset($where['where_in']))
			{
				foreach($where['where_in'] as $key => $value)
				{
					if ( ! empty($value))
						$this->{$this->db_group}->where_in($key, $value);
				}
				unset($where['where_in']);
			}

			$this->{$this->db_group}->where($where);
		}

		$query = $this->{$this->db_group}->get($table);

		return $query->row_array();
	}


	// ------------------------------------------------------------------------


	/**
	 * @param   string  		$field
	 * @param   array|string	[$where]
	 * @param   string  		[$table]
	 * @return  string
	 */
	public function get_group_concat($field, $where=array(), $table=NULL)
	{
		$table = is_null($table) ? $this->table : $table;

		$data = '';

		$this->_process_where($where, $table);

		$this->{$this->db_group}->select('group_concat(distinct '.$field.') as result');
		$query = $this->{$this->db_group}->get($table);

		if ( $query->num_rows() > 0)
		{
			$result = $query->row_array();
			if ( ! is_null($result['result']))
			{
				$data = $result['result'];
			}
		}

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param   string  		$field
	 * @param   array|string  	[$where]
	 * @param   string			[$table]
	 * @return	array
	 */
	public function get_group_concat_array($field, $where=array(), $table = NULL)
	{
		$result = $this->get_group_concat($field, $where, $table);

		if ($result != '')
			return explode(',', $result);

		return array();
	}


	// ------------------------------------------------------------------------


	/**
	 * Get array of records
	 *
	 * @access	public
	 * @param 	array		$where  An associative array
	 * @param 	string		$table  table name. Optional.
	 * @return	Array of records
	 */
	public function get_list($where = NULL, $table = NULL)
	{
		$data = array();

		$table = (!is_null($table)) ? $table : $this->table;

		// Perform conditions from the $where array
		foreach(array('limit', 'offset', 'order_by', 'like') as $key)
		{
			if(isset($where[$key]))
			{
				call_user_func(array($this->{$this->db_group}, $key), $where[$key]);
				unset($where[$key]);
			}
		}

		if (isset($where['where_in']))
		{
			foreach($where['where_in'] as $key => $value)
			{
				$this->{$this->db_group}->where_in($key, $value);
			}
			unset($where['where_in']);
		}


		if ( !empty ($where) )
		{
			foreach($where as $cond => $value)
			{
				if (is_string($cond))
				{
					$this->{$this->db_group}->where($cond, $value);
				}
				else
				{
					$this->{$this->db_group}->where($value);
				}
			}
		}

		// Add ID
		if ($table == $this->table && $this->pk_name != NULL)
			$this->{$this->db_group}->select($table.'.'.$this->pk_name. ' as id', FALSE);

		$this->{$this->db_group}->select($table.'.*', FALSE);

		// log_message('app', print_r($this->{$this->db_group}->get_compile_select() , TRUE));

		$query = $this->{$this->db_group}->get($table);

		// log_message('app', print_r($this->{$this->db_group}->last_query(), TRUE));

		if ( $query->num_rows() > 0 )
			$data = $query->result_array();

		$query->free_result();

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get one element lang data (from lang table only)
	 *
	 * @param null $where
	 * @param null $table
	 * @return array
	 */
	public function get_lang($where = NULL, $table=NULL)
	{
		$table = ( ! is_null($table)) ? $table : $this->lang_table;

		$data = $returned_data = array();
		
		if ($table != '')
		{
			if ( is_array($where))
			{
				// Perform conditions from the $where array
				foreach(array('limit', 'offset', 'order_by', 'like') as $key)
				{
					if(isset($where[$key]))
					{
						call_user_func(array($this->{$this->db_group}, $key), $where[$key]);
						unset($where[$key]);
					}
				}
				$this->{$this->db_group}->where($where);
			}
			elseif ( ! is_null($where))
			{
				$this->{$this->db_group}->where($this->pk_name, $where);
			}

			$query = $this->{$this->db_group}->get($table);

			if ( $query->num_rows() > 0 )
				$data = $query->result_array();

			$query->free_result();
		}

		// Set lang code as record index
		foreach($data as $lang_item)
			$returned_data[$lang_item['lang']] = $lang_item;


		// Safety against DB : Check if each existing language key has data
		$fields = $this->list_fields($table);
		$fields_data = $this->field_data($table);

		foreach(Settings::get_languages() as $language)
		{
			$lang = $language['lang'];

			if (empty($returned_data[$lang]))
			{
				foreach ($fields as $field)
				{
					$field_data = array_values(array_filter($fields_data, create_function('$row', 'return $row["field"] == "'. $field .'";')));
					$field_data = (isset($field_data[0])) ? $field_data[0] : FALSE;

					$returned_data[$lang][$field] = (isset($field_data['default'])) ? $field_data['default'] : '';
				}
			}
		}

		return $returned_data;
	}


	// ------------------------------------------------------------------------


	/** Get post list with lang data
	 *  Used by front-end to get the elements list with lang data
	 *
	 *	@param	array	[$where]		WHERE array
	 *	@param	string	[$lang]		Language code
	 *	@return	array	The complete arrayList of element, including medias
	 */
	public function get_lang_list($where = array(), $lang = NULL)
	{
		$data = array();

		$this->_process_where($where);

		// Make sure we have only one time each element
		$this->{$this->db_group}->distinct();

		// Add ID
		if ($this->pk_name != NULL)
			$this->{$this->db_group}->select($this->table.'.'.$this->pk_name. ' as id', FALSE);

		// Lang data
		if ( ! is_null($lang))
		{
			$this->{$this->db_group}->select($this->lang_table.'.*');
			$this->{$this->db_group}->join(
				$this->lang_table,
				$this->lang_table.'.'.$this->pk_name.' = ' .$this->table.'.'.$this->pk_name .
					' AND ' . $this->lang_table.'.lang = \'' . $lang . '\''
				,
				'left'
			);
			// $this->{$this->db_group}->where($this->lang_table.'.lang', $lang);
		}

		// Main data select
		$this->{$this->db_group}->select($this->table.'.*', FALSE);

		$query = $this->{$this->db_group}->get($this->table);

		// log_message('error', print_r($this->{$this->db_group}->last_query(), TRUE));
		// log_message('error', print_r('--------------------', TRUE));

		if($query && $query->num_rows() > 0)
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
		else if ( ! $query)
		{
			log_message('error', print_r('Error in the query : ', TRUE));
			log_message('error', print_r($this->last_query(), TRUE));
		}


//			log_message('error', print_r($this->last_query(), TRUE));

		return $data;
	}


	/**
	 * @param int   			$page
	 * @param array 			$filter
	 * @param int   			$nb_by_page
	 * @param array 			$sort		array(
	 * 											key => 'DESC'
	 * 										)
	 * @return array			Array(
	 *               				'items' => 	Array of filtered results, limited to the asked page
	 *               				'nb' => 	Total Nb of filtered results
	 *               			)
	 */
	public function get_pagination_list($page=1, $filter = array(), $nb_by_page=50, $sort=NULL, $addon=array())
	{
		if ( ! $filter) $filter = array();
		$sql_having = '';
		$sql_join = '';

		if (isset($this->_table_definition))
		{
			$table = $this->_table_definition['table'];
			$type = ! empty($this->_table_definition['type']) ? $this->_table_definition['type'] : $table;
			$pk_table = ! empty($this->_table_definition['pk']) ? $this->_table_definition['pk'] : 'id_' . $table;
		}
		else
		{
			$table = $this->get_table();
			$type = $table;
			$pk_table = 'id_' . $table;
		}

		$lang_table = $this->get_lang_table();

		// Returned results
		$result = array(
			'items' => array(),
			'nb' => 0,
			'nb_by_page' => $nb_by_page,
			'page' => $page,
			'filter' => $filter,
			'sort' =>  ! $sort ? array() : $sort,
			'extend_definitions' => $this->get_extend_fields_definition($type)
		);

		$sql_test = " GROUP_CONCAT( CONCAT('☼', ef.id_extend_field, '☼', LOWER(efs.content)) SEPARATOR '♫') AS test ";

		$page = $page - 1;
		$offset = $page * $nb_by_page;

		$pk_table = is_null($pk_table) ? $table : $pk_table;

		$flat_filter = $flat_filter_lang = $addon_filter = array();

		// Tables fields
		$fields = $this->list_fields($table);
		$fields_lang = $this->list_fields($table.'_lang');

		// Filter on flat fields
		foreach($filter as $key => $val)
		{
			if (in_array($key, $fields))
			{
				$flat_filter[$key] = $filter[$key];
				unset($filter[$key]);
			}
			else if ( ! empty($fields_lang) && in_array($key, $fields_lang))
			{
				$flat_filter_lang[$key] = $filter[$key];
				unset($filter[$key]);
			}
		}

		// Items values
		$sql = "
			SELECT
				t.*,
		";
		if ( ! is_null($lang_table))
			$sql .= "
				tl.*,
			";

		$sql .= "
				CONCAT(
					'{',
					 GROUP_CONCAT(
						distinct CONCAT(
							'\"',
							ef.id_extend_field,
							'\":\"',
							REPLACE(
								efs.content
								,
								'\"',
								'\\\\\"'
							),
							'\"'
						)
						SEPARATOR ','
					 ),
					'}'
				)
				as _extend_data
		";

		if ( ! empty($filter))
		{
			$sql .= ',' . $sql_test;
		}

		if ( ! empty($addon['select']))
		{
			$sql .= "," . implode(', ', $addon['select']);
		}

		$sql .= "
			FROM
				".$table." t
		";

		if ( ! is_null($lang_table))
			$sql .= "
				left join ".$table."_lang tl on tl.".$pk_table." = t.".$pk_table." and tl.lang='" . Settings::get_lang() . "'
			";

		$sql .="
				LEFT JOIN extend_field ef on ef.parent='".$type."'
				LEFT JOIN extend_fields efs
					ON efs.id_extend_field = ef.id_extend_field AND efs.parent = '".$type."' and efs.id_parent = t.".$pk_table;

		if ( ! empty($addon['join']))
		{
			foreach($addon['join'] as $key => $join)
			{
				$sql_join .= $join;

				if (isset($addon['filter']) && ! empty($addon['filter'][$key]))
				{
					if ( ! empty($filter[$key]))
					{
						$sql_join .= ' and ' . $addon['filter'][$key] . " in (" . $filter[$key] . ")";
						$addon_filter[] = " $key is not null ";
						unset($filter[$key]);
					}
				}
			}
		}

		$sql.= $sql_join;


		$sql .=	" WHERE 1 = 1 ";

		// Filter
		foreach($flat_filter as $key => $val)
		{
			if (is_array($val))
			{
				$sql .= " AND t.".$key." in('".implode("','", $val)."') ";
			}
			else
				$sql .= " AND t.".$key." like '%".$val."%' ";
		}
		foreach($flat_filter_lang as $key => $val)
		{
			$sql .= " AND tl.".$key." like '%".$val."%'	";
		}

		$sql .= "
				GROUP BY t.".$pk_table."
		";

		if ( ! empty($filter))
		{
			$sql_having = " HAVING ";
			$sql_having_and = "";

			$i = 0;

			foreach($filter as $key => $val)
			{
				if ($i > 0) $sql_having .= ' and ';

				$extend = $this->get_extend_field_definition($key, $type);

				if ( ! in_array($extend['html_element_type'], array('select', 'radio', 'checkbox')))
				{
					$sql_having .= " (SUBSTRING_INDEX(SUBSTRING_INDEX(test,'☼" . $key . "☼',-1),'♫',1) LIKE LOWER('%" . $val . "%')) ";
				}
				else
				{
					$val = explode(',', $val);
					$sql_having .= "(";

					foreach($val as $idx => $v)
					{
						$sql_having .= $idx > 0 ? ' OR ' : '';
						$sql_having .= " (SUBSTRING_INDEX(SUBSTRING_INDEX(test,'☼" . $key . "☼',-1),'♫',1) = '" . $v . "') ";
					}

					$sql_having .= ")";
				}
				$sql_having_and .= " AND test LIKE '%☼" . $key . "☼%' ";
				$i++;
			}
			$sql_having .= $sql_having_and;
		}

		foreach($addon_filter as $idx => $str)
		{
			if ( ! empty($sql_having)) $sql_having .= ' AND ';
			if (empty($sql_having) && $idx == 0) $sql_having .= ' HAVING ';
			$sql_having .= $str;
		}

		$sql .= $sql_having;

		// Sort
		if ( ! empty($sort))
		{
			$sql .= ' ORDER BY ';

			foreach($sort as $i => $s)
			{
				if ($i > 0) $sql .= ', ';
				$sql .= in_array($s['key'], $fields) ?
					't.'.$s['key'] . ' ' . $s['value'] :
					(
					in_array($s['key'], $fields_lang) ?
						'tl.'.$s['key'] . ' ' . $s['value'] :
						'ef_' . $s['key'] . ' ' . $s['value']
					);
			}
		}

		$sql .= " LIMIT ".$nb_by_page." OFFSET ".$offset;

//		log_message('error', print_r($sql, TRUE));


		$query = $this->{$this->db_group}->query($sql);

		// Main result
		if ($query->num_rows() > 0)
		{
			$result['items'] = $this->add_extend_fields_to_rows($query->result_array(), $type);
		}

		// Nb of results (without pagination)
		$sql_nb  = "
			SELECT count(1) as nb
			FROM (
				SELECT
					t.".$pk_table.",
					".$sql_test."
		";

		if ( ! empty($addon['select']))
		{
			$sql_nb .= "," . implode(', ', $addon['select']);
		}

		$sql_nb .= "
				FROM
					".$table." t
		";

		if ( ! is_null($lang_table))
			$sql_nb .= "
					left join ".$table."_lang tl on tl.".$pk_table." = t.".$pk_table." and tl.lang='" . Settings::get_lang() . "'
			";

		$sql_nb .= "
					left JOIN extend_field ef on ef.parent='".$type."'
					left JOIN extend_fields efs
						ON efs.id_extend_field = ef.id_extend_field AND efs.parent = '".$type."' and efs.id_parent = t.".$pk_table."
		";

		$sql_nb .= $sql_join;

		if( ! empty($filter))
		{
			$sql_nb .= "
				WHERE ef.id_extend_field IN (".implode(',', array_keys($filter)).")
			";
		}
		else
		{
			$sql_nb .= " WHERE 1 = 1 ";
		}

		foreach($flat_filter as $key => $val)
		{
			if (is_array($val))
			{
				$sql_nb .= " AND t.".$key." in('".implode("','", $val)."') ";
			}
			else
				$sql_nb .= " AND t.".$key." like '%".$val."%' ";
		}

		foreach($flat_filter_lang as $key => $val)
			$sql_nb .= " AND tl.".$key." like '%".$val."%'	";

		$sql_nb .= "GROUP BY t.".$pk_table;
		$sql_nb .= $sql_having;
		$sql_nb .= " ) as test;";

		$query = $this->{$this->db_group}->query($sql_nb);

		if ($query->num_rows() > 0)
		{
			$result_nb = $query->row_array();
			$result['nb'] = $result_nb['nb'];
		}

		return $result;
	}


	// ------------------------------------------------------------------------


	protected function _get_flat_table_filters($pk_table, $filter, $flat_filter, $flat_filter_lang)
	{
		$i = 0;
		$sql_filter = '';

		foreach($filter as $key => $val)
		{
			// Get Extend definition
			$extend = $this->get_extend_field_definition($key);

			// Can be : text, textarea, select, date, radio, checkbox, select-multiple, date-multiple, editor
			$html_element_type = ! empty($extend) ? $extend['html_element_type'] : 'input';
			$key_prefix = ! empty($extend) ? 'ef_' : '';

			$failover_to_standard = FALSE;

			$val = trim($val);

			if ($i == 0) $sql_filter .= ' where ';
			else $sql_filter .= ' and ';

			switch($html_element_type)
			{
				case 'input':
				case 'email':
				case 'text':
				case 'textarea':
				case 'editor':
					if (strpos($val, '%') === 0 || substr($val, -1) == '%')
					{
						$sql_filter .= $key_prefix . $key . " like '" . $val . "'";
					}
					else if (in_array(substr($val, 0, 1), array('<', '>', '=')))
					{
						$value = trim(substr($val, 1));

						if (filter_var($value, FILTER_VALIDATE_INT) !== FALSE)
						{
							$sql_filter .= $key_prefix . $key . $val;
						}
						else
						{
							// Try to split on space
							$parts = preg_split('/\s+/', $val);
							$tmp_sql_filter = '';

							foreach($parts as $idx => $part)
							{
								$ope = substr($part, 0, 1);
								$value = trim(substr($part, 1));

								if (
									in_array(substr($ope, 0, 1), array('<', '>', '='))
									&& filter_var($value, FILTER_VALIDATE_INT) !== FALSE
								)
								{
									if ($idx > 0) $tmp_sql_filter .= ' and ';
									$tmp_sql_filter .= $key . $ope . $value;
								}
								else
								{
									$failover_to_standard = TRUE;
								}
							}

							if ( ! $failover_to_standard)
								$sql_filter .= $tmp_sql_filter;
						}
					}
					else
						$failover_to_standard = TRUE;

					break;

				case 'select':
					$val = explode(',', $val);
					$val = "'" . implode("','", $val) . "'";
					$sql_filter .= $key_prefix . $key . " in (" . $val . ")";

					break;

				default:
					$failover_to_standard = TRUE;
			}

			if ($failover_to_standard)
				$sql_filter .= $key_prefix . $key . " = '" . addslashes($val) . "'";

			$i++;
		}

		return $sql_filter;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get pages or articles from their lang URL
	 *
	 * @param 	string|int|array	$urls	ID or array of IDs to exclude for the search
	 * @return 	Array	Array of elements
	 */
	public function get_from_urls($urls, $excluded_id)
	{
		$data = array();

		if ( ! is_array($urls))
			$urls = array_values(array($urls));

		// Main data select
		$this->{$this->db_group}->select($this->table.'.*', FALSE);
		$this->{$this->db_group}->join($this->lang_table, $this->lang_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table, 'inner');
		$this->{$this->db_group}->where_in($this->lang_table.'.url', $urls);

		// Add excluded IDs to the statement
		if ($excluded_id !='' && !is_array($excluded_id))
			$excluded_id = array($excluded_id);

		if ( !empty($excluded_id))
		{
			$this->{$this->db_group}->where_not_in($this->lang_table.'.id_'.$this->table, $excluded_id);
		}

		$query = $this->{$this->db_group}->get($this->table);

		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
			$query->free_result();
		}

		return $data;
	}


	// ------------------------------------------------------------------------


	public function get_extend_fields_definition($parent = NULL, $lang = NULL)
	{
		if ( ! isset($this->extend_fields_def[$parent]))
		{
			if ( ! isset(self::$ci->extend_field_model))
				self::$ci->load->model('extend_field_model');

			if (is_null($lang))
				$extends = self::$ci->extend_field_model->get_list(array('extend_field.parent' => $parent));
			else
				$extends = self::$ci->extend_field_model->get_lang_list(array('extend_field.parent' => $parent), $lang);

			// Set available options for select, radio, checkbox
			foreach($extends as $idx => $extend)
			{
				$extends[$idx] = $this->_add_options_to_extend_definition($extend);
			}

			// Local Store
			$this->extend_fields_def[$parent] = $extends;
		}

		return $this->extend_fields_def[$parent];
	}


	// ------------------------------------------------------------------------


	public function get_extend_field_definition($id_extend_field, $type=NULL, $lang=NULL)
	{
		$extend = NULL;

		if ( ! is_null($type))
		{
			$extends = $this->get_extend_fields_definition($type, $lang);

			foreach ($extends as $e) {
				if ($e['id_extend_field'] == $id_extend_field)
					$extend = $e;
			}
		}
		else
		{
			$sql = '
				SELECT
					e.*,
					t.*
				FROM extend_field e
				JOIN extend_field_type t on t.id_extend_field_type = e.type
				WHERE e.id_extend_field = ' . $id_extend_field;

			$query = $this->{$this->db_group}->query($sql);

			if ($query->num_rows() > 0)
			{
				$extend = $query->row_array();
				$extend = $this->_add_options_to_extend_definition($extend);
			}
		}

		return $extend;
	}


	// ------------------------------------------------------------------------


	private function _add_options_to_extend_definition($extend)
	{
		$extend['options'] = array();

		if (in_array($extend['html_element_type'], array('select', 'checkbox', 'radio')))
		{
			$options = explode("\n", $extend['value']);
			foreach($options as $option)
			{
				$arr = explode(':', $option);
				if (isset($arr[0]) && isset($arr[1]))
					$extend['options'][$arr[0]] = $arr[1];
			}
		}

		return $extend;
	}


	// ------------------------------------------------------------------------


	public function add_extend_fields_to_rows($data, $type)
	{
		foreach($data as $idx => $d)
		{
			// Add the key '_extends' to the row
			$data[$idx] = $this->_add_extend_fields_array_to_row($d, $type);

			if (isset($d['_extend_data']))
			{
				$_extend_data = json_decode($d['_extend_data'], 1);

				foreach ($_extend_data as $id_extend_field => $value) {
					$data[$idx] = $this->add_extend_fields_to_row($data[$idx], $id_extend_field, $value, $type);
				}
			}
		}

		return $data;
	}


	// ------------------------------------------------------------------------


	public function add_extend_fields_to_row($row, $id_extend_field, $value=NULL, $type=NULL, $lang=NULL)
	{
		$extend_value = array();

		$extend = $this->get_extend_field_definition($id_extend_field, $type, $lang);

		// Select, Checkbox, Radio
		if (in_array($extend['html_element_type'], array('select', 'checkbox', 'radio')))
		{
			$value = explode(',', $value);

			if (isset($extend['options']))
			{
				$extend_value = array();

				foreach($value as $val)
				{
					$extend_value[] = array(
						'value_displayed' => ! empty($extend['options'][$val]) ? $extend['options'][$val] : '',
						'value' => $val,
						'definition' => $extend
					);
				}
			}
		}
		else
		{
			$extend_value[] = array(
				'value_displayed' => $value,
				'value' => $value,
				'definition' => $extend
			);
		}

		$row['_extends'][$extend['name']] = $extend_value;

		return $row;
	}


	// ------------------------------------------------------------------------


	private function _add_extend_fields_array_to_row($row, $type)
	{
		if ( ! is_null($type) && ! isset($row['_extends']))
		{
			$row['_extends'] = array();

			$extend_definitions = $this->get_extend_fields_definition($type);

			foreach($extend_definitions as $e_def)
			{
				$row['_extends'][$e_def['name']] = array();
			}
		}

		return $row;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param	string 	$id_extend_field
	 * @param 	string 	$value
	 * @param	string	$type
	 * @param	string 	$lang
	 * @return	null|array|string
	 */
	public function get_extend_field_label($id_extend_field, $value=NULL, $type=NULL, $lang=NULL)
	{
		$label = NULL;

		$extend = $this->get_extend_field_definition($id_extend_field, $type, $lang);

		// Select, Checkbox, Radio
		if (in_array($extend['html_element_type'], array('select', 'checkbox', 'radio')))
		{
			$value = explode(',', $value);

			if (count($value) == 1)
				$label = $extend['options'][$value[0]];
			else
			{
				$label = array();

				foreach($value as $val)
				{

				}
			}

			$label = '';
		}
		else
		{
			$label = $value;
		}

		return $label;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the current linked child items as a simple array from a N:M table
	 *
	 * @param	String		$items_table	Items table name
	 * @param	String		$parent_table	Parent table name
	 * @param	Integer		$parent_id		Parent ID
	 * @param	String		$prefix			Link table prefix. Default to ''
	 * @return	array		items keys simple array
	 */
	public function get_joined_items_keys($items_table, $parent_table, $parent_id, $prefix='')
	{
		$data = array();

		// N to N table
		$link_table = $prefix.$parent_table.'_'.$items_table;

		// Items table primary key detection
		$fields = $this->list_fields($items_table);
		$items_table_pk = $fields[0];

		// Parent table primary key detection
		$fields = $this->list_fields($parent_table);
		$parent_table_pk = $fields[0];

		$this->{$this->db_group}->where($parent_table_pk, $parent_id);
		$this->{$this->db_group}->select($items_table_pk);
		$query = $this->{$this->db_group}->get($link_table);

		if($query != FALSE)
		{
			foreach($query->result() as $row)
			{
				$data[] = $row->$items_table_pk;
			}
		}

		return $data;
	}



	// ------------------------------------------------------------------------

	/**
	 * Returns the content of a link table based on conditions in this table
	 *
	 * @param	String		$first_table	Parent table name
	 * @param	String		$second_table	Child table name
	 * @param	Array		$conditions		Array of conditions
	 * @param	int			$join			Data from first or second table. Default 1
	 * @param	String		$prefix			Link table prefix. Default to ''
	 * @return	array		Array of Hashtable
	 */
	public function get_linked_items($first_table, $second_table, $conditions, $join=1, $prefix = '')
	{
		$data = array();

		$second_pk_name = $this->get_pk_name($second_table);
		$first_pk_name = $this->get_pk_name($first_table);

		// N to N table
		$link_table = $prefix.$first_table.'_'.$second_table;

		// Correct ambiguous columns
		$conditions = $this->correct_ambiguous_conditions($conditions, $link_table);

		$this->{$this->db_group}->from($link_table);
		$this->{$this->db_group}->where($conditions);

		if ($join == 2)
		{
			$this->{$this->db_group}->join($second_table, $second_table.'.'.$second_pk_name.' = '.$link_table.'.'.$second_pk_name);
		}
		else
		{
			$this->{$this->db_group}->join($first_table, $first_table.'.'.$first_pk_name.' = '.$link_table.'.'.$first_pk_name);
		}

		$query = $this->{$this->db_group}->get();

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
	 * @param	String		$parent_table	Parent table name
	 * @param	String		$child_table	Child table name
	 * @param	Array		$conditions		Array of conditions
	 * @param	String		$lang			Lang code
	 * @param	String		$prefix			Link table prefix. Default to ''
	 * @return	array		Array of Hashtable
	 */
	public function get_linked_lang_items($parent_table, $child_table, $conditions, $lang, $prefix = '')
	{
		$data = array();

		$child_pk_name = $this->get_pk_name($child_table);

		// N to N table
		$link_table = $prefix.$parent_table.'_'.$child_table;

		// Child lang table
		$child_lang_table = $child_table.'_lang';

		$this->{$this->db_group}->from($link_table);
		$this->{$this->db_group}->where($this->correct_ambiguous_conditions($conditions,$link_table) );
		$this->{$this->db_group}->where('lang', $lang);

		$this->{$this->db_group}->join($child_table, $child_table.'.'.$child_pk_name.' = '.$link_table.'.'.$child_pk_name);
		$this->{$this->db_group}->join($child_lang_table, $child_lang_table.'.'.$child_pk_name.' = '.$child_table.'.'.$child_pk_name);

		$query = $this->{$this->db_group}->get();

		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
		}			
		
		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param        $field
	 * @param array  $where
	 * @param null   $nothing_index
	 * @param null   $nothing_value
	 * @param null   $order_by
	 * @param string $glue
	 * @return array
	 */
	public function get_form_select($field, $where=array(), $nothing_index = NULL, $nothing_value = NULL, $order_by = NULL, $glue="")
	{
		$table = $this->get_table();
		if ( ! is_null($table))
			return $this->get_items_select($table, $field, $where, $nothing_index, $nothing_value, $order_by, $glue );

		return array();
	}


	// ------------------------------------------------------------------------


	/**
	 * Gets items key and value as an associative array
	 *
	 * @param        $items_table
	 * @param        $field				index of the field to get
	 * @param array  $where
	 * @param null   $nothing_index		Value to display fo "no value"
	 * @param null   $nothing_value
	 * @param null   $order_by
	 * @param string $glue
	 * @return array
	 */
	public function get_items_select($items_table, $field, $where=array(), $nothing_index = NULL, $nothing_value = NULL, $order_by = NULL, $glue="")
	{
		$data = array();
		
		// Add the Zero value item
		if ( ! is_null($nothing_value))
		{
			$nothing_index = (is_null($nothing_index)) ? 0 : $nothing_index;
			$data = array($nothing_index => $nothing_value);
		}

		// Items table primary key detection
		$fields = $this->list_fields($items_table);
		$items_table_pk = $fields[0];

		// ORDER BY
		if ( ! is_null($order_by))
			$this->{$this->db_group}->order_by($order_by);

		// WHERE
		if (is_array($where) && ! empty($where))
			$this->{$this->db_group}->where($where);

		// Query
		$query = $this->{$this->db_group}->get($items_table);

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


	/**
	 * @param        $field
	 * @param        $failover			Failover field (from original table)
	 * @param null   $lang
	 * @param array  $where
	 * @param null   $nothing_index
	 * @param null   $nothing_value
	 * @param null   $order_by
	 * @param string $glue
	 * @return array
	 */
	public function get_lang_form_select($field, $failover= NULL, $lang = NULL, $where=array(), $nothing_index = NULL, $nothing_value = NULL, $order_by = NULL, $glue="")
	{
		if (is_null($lang))
			$lang = Settings::get_lang();

		$table = $this->get_table();
		if ( ! is_null($table))
			return $this->get_lang_items_select($table, $field, $failover, $lang, $where, $nothing_index, $nothing_value, $order_by, $glue );

		return array();
	}


	// ------------------------------------------------------------------------


	/**
	 * Same as get_items_select() but taking a lang table field as label
	 *
	 * @param        $items_table
	 * @param        $field
	 * @param        $failover			Failover field (from original table)
	 * @param        $lang
	 * @param array  $where
	 * @param null   $nothing_index
	 * @param null   $nothing_value
	 * @param null   $order_by
	 * @param string $glue
	 * @return array
	 */
	public function get_lang_items_select($items_table, $field, $failover=NULL, $lang=NULL, $where=array(), $nothing_index = NULL, $nothing_value = NULL, $order_by = NULL, $glue="")
	{
		$data = array();
		$lang_table = $items_table.'_lang';

		// Add the Zero value item
		if ( ! is_null($nothing_value))
		{
			$nothing_index = (is_null($nothing_index)) ? 0 : $nothing_index;
			$data = array($nothing_index => $nothing_value);
		}

		// Items table primary key detection
		$fields = $this->list_fields($items_table);
		$items_table_pk = $fields[0];

		// Lang fields : Remove primary key in select
		$lang_fields = $this->list_fields($lang_table);
		foreach($lang_fields as $lf)
		{
			if ($lf != $items_table_pk)
				$this->{$this->db_group}->select($lang_table.'.'.$lf);
		}

		// ORDER BY
		if ( ! is_null($order_by))
			$this->{$this->db_group}->order_by($order_by);

		// Join Lang table
		$this->{$this->db_group}->join(
			$items_table.'_lang', $items_table.'_lang.'.$items_table_pk.'='.$items_table.'.'.$items_table_pk. ' AND ' . $items_table."_lang.lang='". $lang . "'",
			'left'
		);

		// WHERE
		if (is_array($where) && ! empty($where))
			$this->{$this->db_group}->where($where);

		// Query
		$this->{$this->db_group}->select($items_table.'.*');
		$query = $this->{$this->db_group}->get($items_table);

		if($query->num_rows() > 0)
		{
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
					$value = $row->$field;
					if (empty($value) && ! is_null($failover))
						$value = $row->{$failover};

					$data[$row->$items_table_pk] = $value;
				}
			}
		}
		return $data;
	}


	// ------------------------------------------------------------------------


	public function get_unique_name($name, $id_to_exclude=NULL, $table=NULL, $postfix = 1)
	{
		$this->load->helper('text_helper');

		$table = (!is_null($table)) ? $table : $this->table;

		$name = url_title(convert_accented_characters($name));

		$where = array('name' => $name);

		if (!is_null($id_to_exclude) && $id_to_exclude != FALSE)
			$where['id_'.$table.' !='] = $id_to_exclude;

		$exists = $this->exists($where);

		if ($exists)
		{
			if ($postfix > 1 OR (substr($name, -2, count($name) -2) == '-' && ((int) substr($name, -1)) != 0 ))
				$name = substr($name, 0, -2);

			$name = $name . '-' . $postfix;

			return $this->get_unique_name($name, $id_to_exclude, $table, $postfix + 1);
		}

		return $name;
	}


	// ------------------------------------------------------------------------


	public function get_unique_field($field, $value, $id_to_exclude=NULL, $table=NULL, $postfix = 1)
	{
		$this->load->helper('text_helper');

		$table = (!is_null($table)) ? $table : $this->table;

		$value = url_title(convert_accented_characters($value));

		$where = array($field => $value);

		if (!is_null($id_to_exclude) && $id_to_exclude != FALSE)
			$where['id_'.$table.' !='] = $id_to_exclude;

		$exists = $this->exists($where);

		if ($exists)
		{
			if ($postfix > 1 OR (substr($value, -2, count($value) -2) == '-' && ((int) substr($value, -1)) != 0 ))
				$value = substr($value, 0, -2);

			$value = $value . '-' . $postfix;

			return $this->get_unique_field($field, $value, $id_to_exclude, $table, $postfix + 1);
		}

		return $value;
	}


	// ------------------------------------------------------------------------


	public function get_keys_array($field, $where = array(), $table = NULL)
	{
		$result = array();

		$table = (!is_null($table)) ? $table : $this->table;

		if (isset($where['order_by']))
		{
			$field .= ' order by ' . $where['order_by'];
			unset($where['order_by']);
		}

		$this->_process_where($where, $table);

		$this->{$this->db_group}->select('group_concat('.$field." separator ';') as ids", FALSE);

		$query = $this->{$this->db_group}->get($table);

		$data = $query->row_array();

		if ( ! empty($data['ids']))
		{
			$result = explode(';', $data['ids']);
		}

		return $result;
	}


	// ------------------------------------------------------------------------


	/**
	 * Return the max value of one given field
	 *
	 * @param   string		    $field
	 * @param	string			$table
	 * @param	string|array	$where
	 * @return	int|bool
	 */
	public function get_max($field, $table=NULL, $where=NULL)
	{
		$table = ( ! is_null($table)) ? $table : $this->table ;

		$this->{$this->db_group}->select_max($field, 'maximum');

		if (! is_null($where))
		{
			$this->{$this->db_group}->where($where);
		}

		$query = $this->{$this->db_group}->get($table);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			return $row->maximum;
		}
		return FALSE;
	}


	// ------------------------------------------------------------------------


	public function simple_search($term, $field, $limit)
	{
		$data = array();

		$this->{$this->db_group}->like($this->table.'.'.$field, $term);

		$this->{$this->db_group}->limit($limit);

		$query = $this->{$this->db_group}->get($this->table);

		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
		}

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the first PK field name found for the given table
	 *
	 * @param	string			[$table]	if none given: return PK field name of this object
	 * @return	string|bool
	 */
	public function get_pk_name($table = NULL)
	{
		if (! is_null($table))
		{
			$fields = $this->{$this->db_group}->field_data($table);

			foreach ($fields as $field)
			{
				if ($field->primary_key)
				{
					return $field->name;
					break;
				}
			}
		}
		else
		{
			return $this->pk_name;
		}
		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Sets the current table
	 *
	 * @param string	$table	table name
	 */
	public function set_table($table)
	{
		$this->table = $table;
	}


	// ------------------------------------------------------------------------


	/**
	 * Sets the current table pk
	 *
	 * @param string	$pk_name	table pk name
	 */
	public function set_pk_name($pk_name)
	{
		$this->pk_name = $pk_name;
	}


	// ------------------------------------------------------------------------


	/**
	 * Sets the current lang table name
	 *
	 * @param	string	$table	lang table name
	 */
	public function set_lang_table($table)
	{
		$this->lang_table = $table;
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds the current table to the the which have one media table
	 *
	 * @usage	In models :
	 *			$this->set_with_media_table()
	 *
	 * @param string	$table	lang table name
	 */
	public function set_with_media_table($table = NULL)
	{
		if ( is_null($table))
			$table = $this->table;

		if ( ! in_array($table, $this->with_media_table))
			array_push($this->with_media_table, $table);
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the elements definition and store them in the private property "elements_def"
	 *
	 * @param	String	$lang	Parent type
	 * @return	Array	Extend fields definition array
	 */
	protected function set_elements_definition($lang)
	{
		$CI =& get_instance();

		// Loads the model if it isn't loaded
		if (!isset($CI->element_definition_model))
			$CI->load->model('element_definition_model');

		// Get the extend fields definition if not already got
		if ($this->got_elements_def == FALSE)
		{
			// Store the extend fields definition
			$this->elements_def = $CI->element_definition_model->get_lang_list(FALSE, $lang);

			// Set this to true so we don't get the extend field def a second time for an object of same kind
			$this->got_elements_def = TRUE;
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Save one element, including lang depending data
	 *
	 * @param 	array	$data		Standard data table
	 * @param 	array	$dataLang	Lang depending data table. optional.
	 * @return 	int		Saved element ID
	 */
	public function save($data, $dataLang = array())
	{
		/*
		 * Base data save
		 */
	 	$data = $this->clean_data($data);

		// Insert
		if( ! isset($data[$this->pk_name]) || $data[$this->pk_name] == '' )
		{
			// Remove the ID so the generated SQL will be clean (no empty String insert in the table PK field)
			unset($data[$this->pk_name]);

			$this->{$this->db_group}->insert($this->table, $data);
			$id = $this->{$this->db_group}->insert_id();
		}
		// Update or Insert
		else
		{
			$where = array($this->pk_name => $data[$this->pk_name]);

			// Update
			if( $this->exists($where, $this->table))
			{
				$this->{$this->db_group}->where($this->pk_name, $data[$this->pk_name]);
				$this->{$this->db_group}->update($this->table, $data);
			}
			// Insert
			else
			{
				$this->{$this->db_group}->insert($this->table, $data);
			}

			$id = $data[$this->pk_name];
		}

		/*
		 * Lang data save
		 */
		if ( ! empty($dataLang) )
		{
			$dataLang = $this->clean_data_lang($dataLang);

			foreach(Settings::get_languages() as $language)
			{
				foreach($dataLang as $lang => $data)
				{
					if ( ! is_string($lang))
						$lang = isset($data['lang']) ? $data['lang'] : 0;

					if($lang === $language['lang'])
					{
						$where = array(
							$this->pk_name => $id,
							'lang' => $lang
						);
	
						// Update
						if ( ! empty($data))
						{
							if( $this->exists($where, $this->lang_table))
							{
								$this->{$this->db_group}->where($where);
								$this->{$this->db_group}->update($this->lang_table, $data);
							}
							// Insert
							else
							{
								// Correct lang & pk field on lang data array
								$data['lang'] = $lang;
								$data[$this->pk_name] = $id;

								$this->{$this->db_group}->insert($this->lang_table, $data);
							}
						}
					}
				}
			}
		}
		return $id;
	}


	// ------------------------------------------------------------------------


	public function save_lang_data($id_item, $data, $lang_table = NULL)
	{
		$lang_table = (!is_null($lang_table)) ? $lang_table : $this->lang_table;

		$fields = $this->list_fields($lang_table);

		$rows = array();

		foreach($fields as $field)
		{
			if (isset($data[$field]) && is_array($data[$field]) )
			{
				foreach($data[$field] as $lang=>$value)
				{
					$rows[$lang][$field] = $value;
				}
			}
		}

		foreach($rows as $lang => $row)
		{
			$row[$this->pk_name] = $id_item;

			$row['lang'] = $lang;

			$where = array(
				$this->pk_name => $id_item,
				'lang' => $lang
			);

			// Update
			if( $this->exists($where, $lang_table))
			{
				$this->{$this->db_group}->where($where);
				$this->{$this->db_group}->update($lang_table, $row);
			}
			// Insert
			else
			{
				// Correct lang & pk field on lang data array
				$data['lang'] = $lang;
				$data[$this->pk_name] = $id_item;

				$this->{$this->db_group}->insert($lang_table, $row);
			}
		}

		return $id_item;
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves ordering for items in the current table or in the join table, depending on parent var.
	 *
	 * @param	mixed	String of coma separated new order or array of order
	 * @return	string	Coma separated order
	 */
	public function save_ordering($ordering, $parent = FALSE, $id_parent = FALSE)
	{
		if ( ! is_array($ordering))
		{
			$ordering = explode(',', $ordering);
		}
		$new_order = '';
		$i = 1;

		while (list ($rank, $id) = each ($ordering))
		{
			$this->{$this->db_group}->where($this->pk_name, $id);
			$this->{$this->db_group}->set('ordering', $i++);

			// If parent table is defined, save ordering in the join table
			if ($parent !== FALSE)
			{
				$parent_pk = $this->get_pk_name($parent);

				$this->{$this->db_group}->where($parent.'_'.$this->table.'.'.$parent_pk, $id_parent);
				$this->{$this->db_group}->update($parent.'_'.$this->table);
			}
			else
			{
				$this->{$this->db_group}->update($this->table);
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
	public function save_simple_link($parent_table, $id_parent, $child_table, $id_child, $context_data = array(), $prefix='')
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

			$this->{$this->db_group}->insert($link_table, $data);

			return TRUE;
		}

		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Join multiple items keys to a parent through a N:M table
	 *
	 * Items are considered as 'children' and will be attached to a 'parent' through the join table.
	 * That means before saving, all rows with the 'parent ID' key will be deleted in the join table.
	 *
	 * Note: 	When attaching 'categories' to an 'article', the category array will be considered as 'child'
	 *			and the article as 'parent'.
	 *			That means the join table MUST be named 'parent_child'.
	 *			Example : ARTICLE_CATEGORY is the join table between articles and categories
	 *			In that case, the tables ARTICLE and the table CATEGORY MUST exist
	 *
	 * @param	string		$parent_table	parent table name.
	 * @param	int			$parent_id		parent ID
	 * @param	string		$child_table	items table name
	 * @param	array		$item_ids		items to be saved. Simple array of values.
	 * @param	String		$prefix			Link table prefix. Default to ''
	 * @return	int			Number of attached items
	 */
	public function save_multiple_links($parent_table, $parent_id, $child_table, $item_ids, $prefix = '')
	{
		// N to N table
		$link_table = $prefix.$parent_table.'_'.$child_table;

		// PK fields
		$parent_pk_name = $this->get_pk_name($parent_table);
		$child_pk_name	= $this->get_pk_name($child_table);

		// Delete existing link between items table and parent table
		$this->{$this->db_group}->where($parent_pk_name, $parent_id);
		$this->{$this->db_group}->delete($link_table);

		// nb inserted items
		$nb = 0;

		// INSERT
		if ( !empty($item_ids) )
		{
			foreach($item_ids as $item_id)
			{
				$item_id = (int) $item_id;
				if($item_id > 0 )
				{
					$nb+= $this->{$this->db_group}->insert($link_table, array(
						$parent_pk_name	=> $parent_id,
						$child_pk_name	=> $item_id
					)) !== false ? 1 : 0;
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
	 * @param   string  $parent_table
	 * @param   string  $id_parent     Parent table PK value
	 * @param   string  $child_table   Child table name
	 * @param   string  $id_child      Child table PK value
	 * @param   string  $prefix        Link table prefix
	 * @return  int
	 */
	public function delete_simple_link($parent_table, $id_parent, $child_table, $id_child, $prefix='')
	{
		// N to N table
		$link_table = $prefix.$parent_table.'_'.$child_table;

		// PK fields
		$parent_pk_name = $this->get_pk_name($parent_table);
		$child_pk_name = $this->get_pk_name($child_table);

		$this->{$this->db_group}->where($parent_pk_name, $id_parent);
		$this->{$this->db_group}->where($child_pk_name, $id_child);

		return (int) $this->{$this->db_group}->delete($link_table);
	}

	// ------------------------------------------------------------------------


	/**
	 * Add all media for one element to an array and returns this array
	 *
	 * @param	array	By ref. The array to add the media datas
	 * @param	string	parent name. Example : 'page', 'article', etc.
	 * @param	string	Lang code
	 */
	protected function add_linked_media(&$data, $parent, $lang = NULL)
	{
		if (empty($data)) return;

		// Limit medias to parents
		$ids = array();
		foreach ($data as $d)
		{
			if ( ! empty($d[$this->pk_name]))
				$ids[] = $d[$this->pk_name];
		}

		// Select medias
		$this->{$this->db_group}->select('*, media.id_media, media.id_media as id');
		$this->{$this->db_group}->from('media,'. $parent .'_media');
		$this->{$this->db_group}->where('media.id_media', $parent.'_media.id_media', FALSE);

		if ( ! empty($ids) && ! empty($this->pk_name))
			$this->{$this->db_group}->where_in($parent . '_media.' . $this->pk_name, $ids);

		$this->{$this->db_group}->order_by($parent.'_media.ordering');

		if ( ! is_null($lang))
		{
			$this->{$this->db_group}->join('media_lang', 'media.id_media = media_lang.id_media', 'left outer');
			$this->{$this->db_group}->where('(media_lang.lang =\'', $lang.'\' OR media_lang.lang is null )', FALSE);
		}

		$query = $this->{$this->db_group}->get();

		$result = array();

		if($query && $query->num_rows() > 0)
			$result = $query->result_array();

		// If the data array is one simple array
		$data_is_simple_array = FALSE;
		if (! isset($data[0]) OR ! is_array($data[0]))
		{
			$data_is_simple_array = TRUE;
			$data = array(0 => $data);
		}

		foreach($data as $k=>$el)
		{
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

				if (in_array($media['extension'], array('jpg', 'jpeg', 'gif', 'png')))
				{
					if (file_exists($media['path']) && strpos($media['path'], ' ') === FALSE)
					{
						$d = @getimagesize(base_url() . $media['path']);
						$media['size'] = array(
							'width' => $d['0'],
							'height' => $d['1'],
						);
					}
					else
					{
						log_message('error', print_r('Size set to 0 : Spaces in picture path or file not found : ' . $media['path'], TRUE));

						$media['size'] = array(
							'width' => 0,
							'height' => 0,
						);
					}
				}
			}
		}

		if ($data_is_simple_array)
			$data = $data[0];

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

		// If the data array is one simple array
		$data_is_simple_array = FALSE;
		if (! isset($data[0]) OR ! is_array($data[0]))
		{
			$data_is_simple_array = TRUE;
			$data = array(0 => $data);
		}

		// Array of IDs to get.
		$ids = array();
		foreach($data as $element)
		{
			$ids[] = $element[$id];
		}

		if ( ! empty($ids))
		{
			$this->{$this->db_group}->select($id .', lang, online, url, title, subtitle');
			$this->{$this->db_group}->where($id . ' in (' . implode(',' , $ids ) . ')' );
			$this->{$this->db_group}->from($parent . '_lang');

			$query = $this->{$this->db_group}->get();

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
					// New approach
					$data[$k]['languages'] = array();

					foreach($languages as $language)
					{
						$data[$k]['languages'][$language['lang']] = array(
							'lang' => $language['lang'],
							'online' => 0,
							'url' => NULL
						);

						foreach($result as $row)
						{
							if ($row[$id] == $el[$id] && $row['lang'] == $language['lang'])
							{
								// New approach
								$data[$k]['languages'][$language['lang']] = $row;

								// Old approach
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

		if ($data_is_simple_array)
			$data = $data[0];
	}


	// ------------------------------------------------------------------------


	/**
	 * Add extended fields and their values if website settings allow it.
	 *
	 * @param	Array	Data array. By ref.
	 * @param	String	Parent type. can be "page", "article", etc.
	 * @param	String	Lang code
	 */
	protected function add_extend_fields(&$data, $parent, $lang = NULL)
	{
		// get the extend fields definition array
		$efd = $this->get_extend_fields_definition($parent);

		if ( ! empty($efd) )
		{
			// If the data array is one simple array
			$data_is_simple_array = FALSE;
			if (! isset($data[0]) OR ! is_array($data[0]))
			{
				$data_is_simple_array = TRUE;
				$data = array(0 => $data);
			}

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
				$this->{$this->db_group}->where(array('extend_fields.parent' => $parent));
				$this->{$this->db_group}->where_in('extend_fields.id_parent', $ids);
				$this->{$this->db_group}->join(
					$this->extend_fields_table,
					$this->extend_field_table.'.id_'.$this->extend_field_table.' = ' .$this->extend_fields_table.'.id_'.$this->extend_field_table,
					'inner'
				);

				$query = $this->{$this->db_group}->get($this->extend_field_table);

				$extend_fields = array();
				if ( $query->num_rows() > 0)
					$extend_fields = $query->result_array();


				// Filter the result by lang : Only returns the not translated data and the given language translated data
				$filtered_result = array();
				foreach($extend_fields as $res)
				{
					if ($res['lang'] == $lang || $res['lang'] == '' )
						$filtered_result[] = $res;
				}

				// Attach each extend field to the corresponding data array
				foreach ($data as &$d)
				{
					// Store the extend definition array
					// Not usefull for the moment.
					// Can be used for debugging
					// $d['_extend_fields_definition'] = $this->get_extend_fields_definition($parent);

					// First set the extend fields of the data row to the default value. So it exists...
					foreach ($efd as $e)
					{
						if ($e['id_parent'] == 0 OR $d['id_'.$parent] == $e['id_parent'])
							$d[$this->extend_field_prefix.$e['name']] = $e['default_value'];
					}

					// Feeds the extend fields
					// Each extend field will be prefixed to avoid collision with standard fields names
					foreach ($filtered_result as $e)
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

			if ($data_is_simple_array)
				$data = $data[0];
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
	 * @return	int		number of attached items
	 */
	public function join_items_keys_to($items_table, $items, $parent_table, $parent_id)
	{
		// N to N table
		$link_table = $parent_table.'_'.$items_table;

		// Items table primary key detection
		$fields = $this->list_fields($items_table);
		$items_table_pk = $fields[0];

		// Parent table primary key detection
		$fields = $this->list_fields($parent_table);
		$parent_table_pk = $fields[0];

		// Delete existing link between items table and parent table
		$this->{$this->db_group}->where($parent_table_pk, $parent_id);
		$this->{$this->db_group}->delete($link_table);

		// nb inserted items
		$nb = 0;

		// Insert
		if ( !empty($items) )
		{
			foreach($items as $item)
			{
				if($item != 0 && $item !== FALSE)
				{
					$data = array(
					   $parent_table_pk => $parent_id,
					   $items_table_pk => $item
					);

					$this->{$this->db_group}->insert($link_table, $data);
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
	 * @return	int			number of affected rows
	 */
	public function delete_joined_key($items_table, $item_key, $parent_table, $parent_id)
	{
		// N to N table
		$link_table = $parent_table.'_'.$items_table;

		// Items table primary key detection
		$fields = $this->list_fields($items_table);
		$items_table_pk = $fields[0];

		// Parent table primary key detection
		$fields = $this->list_fields($parent_table);
		$parent_table_pk = $fields[0];

		$this->{$this->db_group}->where(array(
			$parent_table_pk => $parent_id,
			$items_table_pk => $item_key
		));

		return (int) $this->{$this->db_group}->delete($link_table);
	}


	// ------------------------------------------------------------------------


	/**
	 * Set an item online / offline depending on its current status
	 *
	 * @param	int			item ID
	 * @return 	boolean		New status
	 */
	public function switch_online($id)
	{
		// Current status
		$status = $this->get_row($id)->online;

		// New status
		($status == 1) ? $status = 0 : $status = 1;

		// Save
		$this->{$this->db_group}->where($this->pk_name, $id);
		$this->{$this->db_group}->set('online', $status);
		$this->{$this->db_group}->update($this->table);

		return $status;
	}


	// ------------------------------------------------------------------------


	/**
	 * Feed the template array with data for each field in the table
	 *
	 * @param	int		ID of the search element
	 * @param	array	By ref, the template array
	 */
	public function feed_template($id, &$template)
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
	 * @param   string|int  $id
	 * @param   array       &$template
	 */
	public function feed_lang_template($id, &$template)
	{
		// lang_table fields
		$fields = NULL;
		$rows = $this->get_lang($id);

		$template['languages'] = array();

		foreach($this->get_languages() as $language)
		{
			$lang = $language['lang'];

			$template['languages'][$lang] = array();

			// Feeding of template languages elements
			foreach($rows as $row)
			{
				if(isset($row['lang']) && $row['lang'] == $lang)
				{
					$template['languages'][$lang] = $row;
				}
			}

			// Language empty : Feed with blank data
			if( empty($template['languages'][$lang]))
			{
				// Get lang_table fields if we don't already have them
				if (is_null($fields))
					$fields = $this->list_fields($this->lang_table);

				// If no fields here, no lang table exists, so feed nothing
				if ($fields)
				{
					foreach ($fields as $field)
					{
						if ($field != $this->pk_name)
							$template['languages'][$lang][$field] = '';
						else
							$template['languages'][$lang][$this->pk_name] = $id;
					}
				}
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Feeds the template array with blank data for each field in the table
	 *
	 * @param	array   &$data	By ref, the template array
	 * @return  array
	 */
	public function feed_blank_template(&$data = array())
	{
		$fields = $this->list_fields($this->table);

		$fields_data = $this->field_data($this->table);

		foreach ($fields as $field)
		{
			$field_data = array_values(array_filter($fields_data, create_function('$row', 'return $row["field"] == "'. $field .'";')));
			$field_data = (isset($field_data[0])) ? $field_data[0] : FALSE;

			$data[$field] = (isset($field_data['default'])) ? $field_data['default'] : '';
		}
		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Feed the template array with blank data for each field in language table
	 *
	 * @param   array|bool    $template  By ref, the template array
	 * @return  array|bool
	 */
	public function feed_blank_lang_template(&$template = FALSE)
	{
		if ($template == FALSE) $template = array();

		$fields = $this->list_fields($this->lang_table);

		$fields_data = $this->field_data($this->lang_table);

		$template['languages'] = array();

		foreach(Settings::get_languages() as $language)
		{
			$lang = $language['lang'];

			foreach ($fields as $field)
			{
				$field_data = array_values(array_filter($fields_data, create_function('$row', 'return $row["field"] == "'. $field .'";')));
				$field_data = (isset($field_data[0])) ? $field_data[0] : FALSE;

				$template['languages'][$lang][$field] = (isset($field_data['default'])) ? $field_data['default'] : '';
			}
		}
		return $template;
	}


	// ------------------------------------------------------------------------


	public function get_lang_data($id=NULL, $table=NULL)
	{
		$data = array();

		$table = ( ! is_null($table)) ? $table : $this->lang_table;

		if ( ! is_null($id))
			$this->feed_lang_template($id, $data);
		else
			$this->feed_blank_lang_template($data);

		return $data;

	}


	// ------------------------------------------------------------------------


	/**
	 * Switch the publish filter off
	 */
	public function unlock_publish_filter()
	{
        $uri_string_to_array = explode('/', preg_replace("|^\/?|", '/', self::$ci->uri->uri_string));

        if( ! in_array(config_item('admin_url'), $uri_string_to_array))
		{
			// Settings::get('display_front_offline_content') not available here
			$this->{$this->db_group}->where('name', 'display_front_offline_content');
			$query = $this->{$this->db_group}->get('setting');
			$result = $query->row_array();

			if ( Authority::can('access', 'admin') && ( ! empty($result['content']) && $result['content'] == '1'))
			{
				self::$publish_filter = FALSE;
			}
		}
		else
			self::$publish_filter = FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Insert a row
	 *
	 * @access	public
	 * @param 	array	$data	An associative array of data
	 * @return	int		the last inserted ID
	 */
	public function insert($data = NULL, $table = FALSE)
	{
		$table = (FALSE !== $table) ? $table : $this->table;

		$data = $this->clean_data($data, $table);

		$this->{$this->db_group}->insert($table, $data);
		
		return $this->{$this->db_group}->insert_id();
	}


	// ------------------------------------------------------------------------


	public function insert_ignore($data = NULL, $table = FALSE)
	{
		$table = (FALSE !== $table) ? $table : $this->table;

		$data = $this->clean_data($data, $table);

		$insert_query = $this->{$this->db_group}->insert_string($table, $data);
		$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
		$inserted = $this->{$this->db_group}->query($insert_query);

		return $inserted;
	}


	// ------------------------------------------------------------------------


	/**
	 * Update a row
	 *
	 * @access	public
	 *
	 * @param 	Mixed		$where  Where condition. If single value, PK of the table
	 * @param 	array		$data   An associative array of data
	 * @param 	string		$table  Table name. If not set, current models table
	 * @return	int			Number of updated rows
	 */
	public function update($where = NULL, $data = NULL, $table = FALSE)
	{
		$table = (FALSE !== $table) ? $table : $this->table;

		$data = $this->clean_data($data, $table);

		if ( is_array($where) )
		{
			$this->{$this->db_group}->where($where);
		}
		else
		{
			$pk_name = $this->get_pk_name($table);
			$this->{$this->db_group}->where($pk_name, $where);
		}

		$this->{$this->db_group}->update($table, $data);

		return (int) $this->{$this->db_group}->affected_rows();
	}


	// ------------------------------------------------------------------------


	/**
	 * Delete row(s)
	 *
	 * @param	array 	[$where]	Where condition. If single value, PK of the table
	 * @param	string	[$table]
	 * @return	int					Affected rows
	 */
	public function delete($where = NULL, $table = NULL)
	{
		$table = ! empty($table) ? $table : $this->table;

		if ( is_array($where) )
		{
			$this->_process_where($where, $table);
		}
		else
		{
			$pk_name = $this->get_pk_name($table);
			$this->{$this->db_group}->where($pk_name, $where);
		}

		$this->{$this->db_group}->delete($table);

		return (int) $this->{$this->db_group}->affected_rows();
	}


	// ------------------------------------------------------------------------


	/**
	 * Count all rows corresponding to the passed conditions
	 *
	 * @param array $where
	 * @param null  $table
	 * @return int
	 */
	public function count($where = array(), $table = NULL)
	{
		$table = (!is_null($table)) ? $table : $this->table;

		// only look at main table for count
		$this->{$this->db_group}->select('`'.$table.'`.*');

		// Perform conditions from the $where array
		foreach(array('limit', 'offset', 'order_by', 'like') as $key)
		{
			if(isset($where[$key]))
			{
				call_user_func(array($this->{$this->db_group}, $key), $where[$key]);
				unset($where[$key]);
			}
		}

		if (isset($where['where_in']))
		{
			foreach($where['where_in'] as $key => $value)
			{
				$this->{$this->db_group}->where_in($key, $value);
			}
			unset($where['where_in']);
		}

		if ( is_array($where) && ! empty($where) )
		{
			$this->{$this->db_group}->where($where);
		}

		$query = $this->{$this->db_group}->count_all_results($table);

		return (int) $query;
	}


	// ------------------------------------------------------------------------


	/**
	 * Count all rows in a table or count all results from the current query
	 *
	 * @access	public
	 * @param	bool	true / false
	 * @return	int 	The number of all results
	 */
	public function count_all($results = FALSE)
	{
		// only look at main table for count
		$this->{$this->db_group}->select('`'.$this->table.'`.*');

		if($results !== FALSE)
		{
			$query = $this->{$this->db_group}->count_all_results($this->table);
		}
		else
		{
			$query = $this->{$this->db_group}->count_all($this->table);
		}

		return (int) $query;
	}


	// ------------------------------------------------------------------------


	/**
	 * Count items based on filter
	 *
	 * @param   array   $where
	 * @param   null    $table
	 * @return  mixed
	 */
	public function count_where($where = array(), $table =NULL)
	{
		$table = ( ! is_null($table)) ? $table : $this->get_table();

		// only look at main table for count
		$this->{$this->db_group}->select('`'.$table.'`.*');

		if (isset($where['order_by']))
			unset($where['order_by']);

		if(isset($where['like']))
		{
			$this->{$this->db_group}->like($where['like']);
			unset($where['like']);
		}

		if ( ! empty ($where) )
		{
			foreach($where as $cond => $value)
			{
				if (is_string($cond))
				{
					$this->{$this->db_group}->where($cond, $value);
				}
				else
				{
					$this->{$this->db_group}->where($value);
				}
			}
		}

		$this->{$this->db_group}->from($table);

		return $this->{$this->db_group}->count_all_results();
	}


	// ------------------------------------------------------------------------


	/**
	 * Empty table
	 *
	 * @access	public
	 * @return	void
	 */
	public function empty_table()
	{
		$this->{$this->db_group}->empty_table($this->table);
	}


	// ------------------------------------------------------------------------


	/**
	 * Checks if one table is empty, based on given conditions
	 *
	 * @param null $where
	 * @param null $table
	 * @return bool
	 */
	public function is_empty($where = NULL, $table = NULL)
	{
		$table = ! is_null($table) ? $table : $this->table;

		if ( is_array($where) )
		{
			$this->_process_where($where, $table);
		}

		$query = $this->{$this->db_group}->get($table);

		if($query->num_rows() > 0)
		{
			return FALSE;
		}

		return TRUE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Check if a record exists in a table
	 *
	 * @access	public
	 * @param	array	$where      conditions
	 * @param	string	$table      table name
	 * @return	boolean
	 */
	public function exists($where = NULL, $table = NULL)
	{
		$table = ( ! is_null($table)) ? $table : $this->table ;

		$query = $this->{$this->db_group}->get_where($table, $where, FALSE);

		return $query->num_rows() > 0;
	}


	// --------------------------------------------------------------------


	/**
	 * Checks if one element with a given field name already exists
	 * in the DB.
	 *
	 * @param      $field			Field to test on
	 * @param      $value			Value of the field to test on
	 * @param null $element_id		Optional element ID (in case of change of the field value)
	 * @param null $table
	 * @return bool
	 */
	public function check_exists($field, $value, $element_id = NULL, $table = NULL)
	{
		$item = $this->get_row_array(array($field => $value), $table);

		if ( ! is_null($element_id) && $element_id != FALSE)
		{
			$pk_name = $this->get_pk_name($table);

			if ( ! empty($item) && $item[$pk_name] != $element_id)
				return TRUE;
		}
		else
		{
			if ( ! empty($user))
				return TRUE;
		}
		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the table's fields array list
	 *
	 * array(
	 *      'field' =>      'Field name'
	 *      'type' =>       'DB field type' (int, tinyint, varchar, ...)
	 *      'null' =>       TRUE / FALSE
	 *      'key' =>        'PRI|MUL'
	 *      'extra' =>      column extra
	 *      'comment' =>    column comment
	 *      'privileges' => column privileges
	 *      'length' =>     int / array (in case of ENUM)
	 * )
	 *
	 * @param	String		Table name
	 * @param   Boolean     With / Without primary key. Default FALSE : without.
	 * @return	Array		Array of fields data
	 */
	public function field_data($table=NULL, $with_pk = FALSE)
	{
		$data = array();

		$table = ( ! is_null($table)) ? $table : $this->table ;

		$idx = $this->db_group.'_'.$table . ($with_pk ? '_1' : '');


		if (isset($this->_fields_data[$idx]))
			return $this->_fields_data[$idx];

		if ( ! $this->table_exists($table)) return NULL;

		$query = $this->{$this->db_group}->query('SHOW FULL COLUMNS FROM ' . $table);

		$fields = $query->result_array();

		foreach($fields as $key => $field)
		{
			if ($with_pk === FALSE)
			{
				if ($field['Field'] == $this->pk_name)
					continue;
			}

			// keys to lowercase
			$field = array_change_key_case($field, CASE_LOWER);
			$name = $field['field'];
			$data[$name] = $field;

			// isolate the DB type (remove size)

			$type = preg_split ("/[\s()]+/", $field['type']);

			if ($type[0] == 'enum')
			{
				$enum_values = preg_replace("/[enum'()]+/", '', $field['type']);
				$type[1] = explode(',', $enum_values);
			}

			$data[$name] = array_merge(
				$data[$name],
				array(
					'type' =>   $type[0],
					'null' =>   $field['null'] == 'YES' ? TRUE : FALSE,
					'length' => isset($type[1]) ? $type[1] : NULL,
					'value' =>  NULL
				)
			);
		}

		$this->_fields_data[$idx] = $data;

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Check for a table field
	 *
	 * @param	String		Table name
	 * @param null $table
 	 * @return	Boolean		True if the field is found
	 */
	public function has_field($field, $table = NULL)
	{
		$table = ( ! is_null($table)) ? $table : $this->table ;

		$fields = $this->list_fields($table);

		if (in_array($field, $fields)) return TRUE;

		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Removes the index which are not in the table from the data array
	 *
	 * @param	array	$data		The data array to be cleaned
	 * @param	bool 	$table		Reference table. $this->table if not set.
	 * @return	array
	 */
	public function clean_data($data, $table = FALSE)
	{
		$cleaned_data = array();

		if ( ! empty($data))
		{
			$table = ($table !== FALSE) ? $table : $this->table;

			$fields = $this->list_fields($table);

			$fields = array_fill_keys($fields,'');

			$cleaned_data = array_intersect_key($data, $fields);
		}
		foreach($cleaned_data as $key=>$row)
		{
			if (is_array($row))
				unset($cleaned_data[$key]);
		}
		return $cleaned_data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Cleans one data
	 *
	 * @param      $data		The data array to clean
	 * @param bool $table		Reference table. $this->table if not set.
	 * @return array
	 */
	public function clean_data_lang($data, $table = FALSE)
	{
		$table = ($table !== FALSE) ? $table : $this->lang_table;

		$fields = $this->list_fields($table);

		$cleaned_data = array();

		foreach(Settings::get_languages() as $language)
		{
			$lang = $language['lang'];

			// Was done before : Prepare in controller or model => do not alter
			if (isset($data[$lang]))
			{
				$cleaned_data[$lang] = $data[$lang];
			}
			else
			{
				$cleaned_data[$lang] = array();

				foreach($fields as $field)
				{
					if (isset($data[$field.'_'.$lang]))
					{
						$cleaned_data[$lang][$field] = $data[$field.'_'.$lang];
					}
				}
			}
		}

		return $cleaned_data;
	}


	// ------------------------------------------------------------------------


	/**
	 *	Reorders the ordering field correctly after unlink of one element
	 *
	 *	SET @rank=0;
	 *	SET @rank=0;
	 *	update table set ordering = @rank:=@rank+1
	 *	where ...
	 *	ORDER BY ordering ASC;
	 */
	public function reorder($table = NULL, $where = array())
	{
		$table = ( ! is_null($table)) ? $table : $this->table ;

		if ($this->has_field('ordering', $table))
		{
			$query = $this->{$this->db_group}->query('SET @rank=0');

			// Perform conditions from the $where array
			foreach(array('limit', 'offset', 'order_by', 'like') as $key)
			{
				if(isset($where[$key]))
				{
					call_user_func(array($this->{$this->db_group}, $key), $where[$key]);
					unset($where[$key]);
				}
			}

			$this->{$this->db_group}->order_by('ordering ASC');
			$this->{$this->db_group}->set('ordering', '@rank:=@rank+1', FALSE);
			$this->{$this->db_group}->where($where);

			return $this->{$this->db_group}->update($table);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Correct ambiguous target fields in SQL conditions
	 *
	 * @param	Array	condition array
	 * @param	String	Table name
	 * @return	Array	Corrected condition array
	 */
	public function correct_ambiguous_conditions($array, $table)
	{
		if (is_array($array))
		{
			foreach ($array as $key => $val)
			{
				unset($array[$key]);
				$key = $table.'.'.$key;
				$array[$key] = $val;
			}

			return $array;
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * 	Required method to get the database group for THIS model
	 */
	public function get_database_group()
	{
		return $this->db_group;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get languages from LANG table
	 *
	 * @return	The lang array
	 */
	public function get_languages()
	{
		// Local store of languages
		if ( is_null($this->_languages))
			$this->_languages = $this->{$this->db_group}->from('lang')->order_by('ordering', 'ASC')->get()->result_array();

		return $this->_languages;
	}


	// ------------------------------------------------------------------------


	/**
	 * List fields from one table of the current DB group
	 * and stores the result locally.
	 *
	 * @param	string
	 * @return	Array	List of table fields
	 */
	public function list_fields($table = NULL)
	{
		$table = ( ! is_null($table)) ? $table : $this->table ;

		if (isset($this->_list_fields[$this->db_group.'_'.$table]))
			return $this->_list_fields[$this->db_group.'_'.$table];

		if ( ! $this->table_exists($table)) return NULL;

		$this->_list_fields[$this->db_group.'_'.$table] = $this->{$this->db_group}->list_fields($table);

		return $this->_list_fields[$this->db_group.'_'.$table];
	}


	// ------------------------------------------------------------------------


	/**
	 * Processes the query condition array
	 * @param      $where
	 * @param null $table
	 */
	protected function _process_where($where, $table=NULL)
	{
		$table = ! empty($table) ? $table : $this->table;

		// Perform conditions from the $where array
		if ( ! empty($where) && is_array($where) )
		{
			foreach(array('limit', 'offset', 'like') as $key)
			{
				if(isset($where[$key]))
				{
					call_user_func(array($this->{$this->db_group}, $key), $where[$key]);
					unset($where[$key]);
				}
			}

			if (isset($where['order_by']))
			{
				$this->{$this->db_group}->order_by(trim($where['order_by'], ','), NULL, FALSE);
				unset($where['order_by']);
			}

			if (isset($where['where_in']))
			{
				foreach($where['where_in'] as $key => $values)
				{
					$processed = FALSE;
					foreach($values as $k => $value)
					{
						if (strtolower($value) === 'null')
						{
							unset($values[$k]);
							$this->{$this->db_group}->where('(' . $key . " in ('".implode("','", $values)."') OR ".$key.' IS NULL)', NULL, FALSE);
							$processed = TRUE;
							break;
						}
					}
					if ( ! $processed)
						$this->{$this->db_group}->where_in($key, $values, FALSE);
				}
				unset($where['where_in']);
			}

//			$protect = TRUE;

			foreach ($where as $field => $value)
			{
				$protect = ! (in_array(substr($field, -3), array(' in', ' is')));
				if ( $protect)
					$protect = ! (substr($field, -5) == ' like');

				if (is_string($field))
				{
					if ($value == 'NULL' && is_string($value))
					{
						$this->{$this->db_group}->where($field. ' IS NULL', NULL, FALSE);
					}
					elseif($field == 'RAW')
					{
						$this->{$this->db_group}->where($value, NULL, FALSE);
					}
					else
					{
						if (strpos($field, '.') > 0)
						{
							$this->{$this->db_group}->where($field, $value, $protect);
						}
						else
						{
							$this->{$this->db_group}->where($table.'.'.$field, $value, $protect);
						}
					}
				}
				else
				{
					$this->{$this->db_group}->where($value);
				}
			}
		}
	}

	public function last_query()
	{
		return $this->{$this->db_group}->last_query();
	}

	/**
	 * @param   string  $table
	 * @return  boolean
	 */
	public function table_exists($table)
	{
		return $this->{$this->db_group}->table_exists($table);
	}

}
