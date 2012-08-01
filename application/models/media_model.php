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
 * Ionize Media Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Media management
 * @author		Ionize Dev Team
 *
 */

class Media_model extends Base_model 
{
	// Fields of the context table which can be NULL
	private $_context_null_allowed = array(
		'lang_display'
	);
	

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'media';
		$this->pk_name = 	'id_media';
		$this->lang_table = 'media_lang';
	}


	// ------------------------------------------------------------------------


	/** 
	 * Get media list for one defined parent
	 *
	 * @param	string	Media type. Can be 'picture', 'music', 'video', 'file'
	 * @param	string	parent. Example : 'article', 'page'
	 * @param	string	Parent ID
	 *
	 */
	public function get_list($parent, $id_parent, $type=NULL)
	{	
		$data = array();
		
		// Parent PK , Media table
		$parent_pk = $this->get_pk_name($parent);
		$media_table = $parent.'_'.$this->table;
		
		if ($parent_pk !== FALSE)
		{
			// Select from media table
			$this->{$this->db_group}->order_by('ordering', 'ASC');
			$this->{$this->db_group}->select($this->table.'.*', FALSE);
			$this->{$this->db_group}->select($media_table.'.ordering, '.$media_table.'.lang_display', FALSE);
			
			// Limit to current parent ID
			$this->{$this->db_group}->where($media_table.'.'.$parent_pk, $id_parent);
			
			if ( ! is_null($type))
				$this->{$this->db_group}->where($this->table.'.type', $type);
			
			// Join to link table
			$this->{$this->db_group}->join($media_table, $this->table.'.'.$this->pk_name.'='.$media_table.'.'.$this->pk_name);

			$this->{$this->db_group}->select($media_table.'.ordering');

			$query = $this->{$this->db_group}->get($this->table);

			if($query->num_rows() > 0)
				$data = $query->result_array();
		}
		return $data;
	}


	// ------------------------------------------------------------------------


	/** 
	 * Inserts / Update a media into the media table.
	 * Updates the media if the media complete path already exists
	 * 
	 * @param	string	Medium type. Can be 'picture', 'music', 'video', 'file'
	 * @param	string	Complete path to the medium, including file name.
	 * @return	boolean	TRUE if succeed, FALSE if errors
	 *
	 */
	function insert_media($type, $path)
	{
		if ($path) {

			// If no '/' in the path...
			if(strpos($path, '/') === FALSE)
			{
				$file_name =  $path;
				$base_path = '';
			}
			else 
			{
				$file_name = substr( strrchr($path, '/') ,1 );
				$base_path = str_replace($file_name, '', $path);
			}

			$data['type'] = 	 $type;
			$data['path'] = 	 $path;
			$data['file_name'] = $file_name;
			$data['base_path'] = $base_path;
			
			// Update if exists
			$query = $this->get_where(array('path'=>$path));
			if( $query->num_rows() > 0)
			{
				$medium = $query->row_array();
				$this->{$this->db_group}->where('path', $path);
				$this->{$this->db_group}->update($this->table, $data);
				$id = $medium['id_media'];
			}
			// Insert
			else
			{
				$this->{$this->db_group}->insert($this->table, $data);
				$id = $this->{$this->db_group}->insert_id();
			}
			return $id;
		}
		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Attach one medium to a parent
	 *
	 * @param	string	Media type. Can be 'picture', 'video', etc.
	 * @param	string	parent. Example : 'article', 'page'
	 * @param	string	Parent ID
	 * @param	string	Medium ID
	 * @return	boolean	TRUE if success, FALSE if error
	 *
	 */
	public function attach_media($type, $parent, $id_parent, $id_media)
	{
		// Parent PK , Media table
		$parent_pk = $this->get_pk_name($parent);
		$media_table = $parent.'_'.$this->table;
	
		// Get the media ordering value, regarding to the type
		if ($this->{$this->db_group}->field_exists('ordering', $media_table))
		{
			$this->{$this->db_group}->select_max('ordering');
			$this->{$this->db_group}->join('media', 'media.id_media = '.$media_table.'.id_media');
			$this->{$this->db_group}->where($parent_pk, $id_parent);
			$this->{$this->db_group}->where('media.type', $type);

			$query = $this->{$this->db_group}->get($media_table);

			if ($query->num_rows() > 0)
			{	
				$row =		$query->row();
				$ordering =	$row->ordering;
			}
			else 
			{
				$ordering = 0;
			}
			$this->{$this->db_group}->set('ordering', $ordering += 1);
		}
		
		$this->{$this->db_group}->where('id_media', $id_media);
		$this->{$this->db_group}->where($parent_pk, $id_parent);

		$query = $this->{$this->db_group}->get($media_table);

		if ($query->num_rows() == 0) {

			$this->{$this->db_group}->set('id_media', $id_media);
			$this->{$this->db_group}->set($parent_pk, $id_parent);

			$this->{$this->db_group}->insert($media_table);

			return TRUE;
		}
		return FALSE;
	}

	
	// ------------------------------------------------------------------------	


	/**
	 * Detach all media from a parent depending on the type
	 * If no type, all media attached to this parent will be deleted
	 *
	 * @param 	string	parent type. Ex 'page', 'article'
	 * @param	string	parent ID
	 * @param	string	media type. Optional.
	 *
	 */
	function detach_media_by_type($parent, $id_parent, $type = FALSE)
	{
		// Parent PK , Media table
		$parent_pk = $this->get_pk_name($parent);
		$media_table = $parent.'_'.$this->table;

		/* INNER JOIN on delete is not possible with CI Active Record.
		 * So this request needs to be handly written
		 */
		$sql = 	' DELETE first from ' . $media_table . ' AS first';
		
		if ($type)
		{
			$sql .= ' INNER JOIN ' . $this->table . ' AS second WHERE first.id_media = second.id_media ';
			$sql .= ' AND second.type = \'' . $type . '\'';
			$sql .= ' AND first.'.$parent_pk.' = '.$id_parent;
		}
		else
		{
			$sql .= ' WHERE first.' . $parent_pk . ' = ' . $id_parent;
		}
		
		$this->{$this->db_group}->query($sql);
		
		return (int) $this->{$this->db_group}->affected_rows();		
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves one media data
	 *
	 * @param	array	standard data array
	 * @param	array	lang data array
	 *
	 * @return	string	Inserted / Updated media ID
	 */
	function save($data, $lang_data)
	{
		// Dates
		$data['date'] = ($data['date']) ? getMysqlDatetime($data['date'], Settings::get('date_format')) : '0000-00-00';
		
		// Media saving
		return parent::save($data, $lang_data);
	}
	
	/**
	 * Saves the media context data
	 * depending on the link between one parent and the media
	 * Exemple : table article_media
	 *
	 */
	function save_context_data($post)
	{
		if ( ! empty($post['parent']))
		{
			$data = array();
			$link_table = $post['parent'].'_media';
			$fields = $this->{$this->db_group}->list_fields($link_table);
			
			foreach ($fields as $field)
			{
				if ( ! empty($post[$field]) )
					$data[$field] = $post[$field];
				else
				{
					if (in_array($field, $this->_context_null_allowed))
					$data[$field] = NULL;
				}
			}
			if ( ! empty($data) )
			{
				$this->{$this->db_group}->where('id_'.$post['parent'], $post['id_parent']);
				$this->{$this->db_group}->where('id_media', $post['id_media']);
				$this->{$this->db_group}->update($link_table, $data);
			}
		}
	}
	
	
	function get_context_data($id, $parent, $id_parent)
	{
		$data = array();
		
		$link_table = $parent.'_media';
		$parent_pk = 'id_'.$parent;
		
		$this->{$this->db_group}->where($link_table.'.'.$parent_pk, $id_parent);
		$this->{$this->db_group}->where('id_media', $id);

		$query = $this->{$this->db_group}->get($link_table);
	
		if($query->num_rows() > 0)
			$data = $query->row_array();

		return $data;
	}


	/**
	 * Cleans the media and the media lang table from unused medias
	 * Used by System tool
	 *
	 * @return int	Number of affected medias
	 *
	 */
	function clean_table()
	{
		$tables = $this->db->list_tables();
		$process_tables = array();

		$left_joins = $wheres = '';

		foreach ($tables as $table)
		{
			if (substr($table, -6) == '_media')
			{
				// $fields = $this->db->list_fields($table);
				$fields = $this->db->field_data($table);

				// First pass
				foreach ($fields as $field)
				{
					if ($field->name == 'id_media')
					{
						$process_tables[$table] = array('name' => $table, 'pk'=> NULL);
						break;
					}
				}
				// Second pass
				foreach ($fields as $field)
				{
					if ($field->name != 'id_media' && $field->primary_key == 1)
					{
						if (isset($process_tables[$table]))
						{
							$process_tables[$table]['pk'] = $field->name;
							break;
						}
					}
				}
			}
		}

		$i = 0;
		foreach($process_tables as $key => $table)
		{
			$left_joins .= ' left join ' . $table['name'] . ' on ' . $table['name'] . '.id_media = m.id_media';
			if ($i > 0)	$wheres .= ' and ';
			$wheres .= ' ' . $table['name'] . '.' . $table['pk'] . ' is NULL ';
			$i++;
		}

		// Media
		$sql = ' delete m from media m ' . $left_joins . ' where ' . $wheres;
		$this->{$this->db_group}->query($sql);

		// Returned : Number of deleted media rows
		$nb_affected_rows = (int) $this->{$this->db_group}->affected_rows();

		// Media_Lang
		$sql = ' delete m from media_lang m ' . $left_joins . ' where ' . $wheres;
		$this->{$this->db_group}->query($sql);

		return $nb_affected_rows;
	}


	function get_brokens()
	{
		$brokens = array();

		$medias = $this->get_all();

		foreach($medias as $media)
		{
			if ( ! file_exists(DOCPATH . $media->path))
			{
				$brokens[] = $media;
			}
		}

		return $brokens;
	}
}


/* End of file media_model.php */
/* Location: ./application/models/media_model.php */