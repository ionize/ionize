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
	/**
	 * Fields of the context table which can be NULL
	 * @var array
	 */
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

		log_message('debug', __CLASS__ . " Class Initialized");
	}


	// ------------------------------------------------------------------------


	/** 
	 * Get media list for one defined parent
	 *
	 * @param	string		Parent type. 'article', 'page'
	 * @param	int			Parent ID
	 * @param	string|null	Media type. Can be 'picture', 'music', 'video', 'file'
	 *
	 * @return	array		List of medias
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
	 * @param array $where
	 * @param null  $lang
	 * @param null  $filter
	 *
	 * @return array
	 */
	public function get_lang_list($where = array(), $lang = NULL, $filter = NULL)
	{
		// Correction on $where['id_media']
		if (is_array($where) && isset($where['id_media']) )
		{
			$where[$this->table.'.id_media'] = $where['id_media'];
			unset($where['id_media']);
		}

		// Correction on all non declared parent tables
		foreach ($where as $key => $val)
		{
			if (strpos($val, 'id_media') === 0)
			{
				$val = $this->table . '.' . $val;
				$where[$key] = $val;
			}
		}

		if ( ! is_null($filter))
			$this->_set_filter($filter);

		return parent::get_lang_list($where, $lang);
	}


	// ------------------------------------------------------------------------


	/**
	 * Inserts / Update a media into the media table.
	 * Updates the media if the media complete path already exists
	 *
	 * @param      $type		Medium type. Can be 'picture', 'music', 'video', 'file'
	 * @param      $path		Complete relative path to the medium, including file name, including the "files" folder
	 * @param null $provider
	 *
	 * @return bool				TRUE if succeed, FALSE if errors
	 */
	public function insert_media($type, $path, $provider=NULL)
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

			$data['type'] = 	 	$type;
			$data['path'] = 	 	$path;
			$data['file_name'] = 	$file_name;
			$data['base_path'] = 	$base_path;
			$data['provider'] = 	! is_null($provider) ? $provider : '';
			// $data['path_hash'] = 	hash(config_item('files_path_hash_method'), $path);

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

		if ($this->table_exists($media_table))
		{

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
			}
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
	 * @param	int		parent ID
	 * @param	string	media type. Optional.
	 *
	 * @return 	int		Affected rows
	 *
	 */
	public function detach_media_by_type($parent, $id_parent, $type = NULL)
	{
		// Parent PK , Media table
		$parent_pk = $this->get_pk_name($parent);
		$media_table = $parent.'_'.$this->table;

		// INNER JOIN on delete is not possible with CI Active Record.
		// So this request needs to be handly written
		$sql = 	' DELETE first from ' . $media_table . ' AS first';
		
		if ( ! is_null($type))
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
	 * Unlink all media of one given parent element
	 *
	 * @param $element
	 * @param $element_id
	 *
	 * @return int
	 *
	 */
	public function detach_all_media($element, $element_id)
	{
		// Parent PK , Media table
		$element_pk = $this->get_pk_name($element);
		$media_table = $element.'_'.$this->table;

		$nb_affected = $this->delete(
			array($element_pk => $element_id),
			$media_table
		);

		return $nb_affected;
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
	public function save($data, $lang_data)
	{
		// Dates
		$data['date'] = ($data['date']) ? getMysqlDatetime($data['date'], Settings::get('date_format')) : '0000-00-00';
		
		// Media saving
		return parent::save($data, $lang_data);
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves the media context data
	 * depending on the link between one parent and the media
	 * Exemple : table article_media
	 *
	 */
	public function save_context_data($post)
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


	// ------------------------------------------------------------------------


	/**
	 * @param $id
	 * @param $parent
	 * @param $id_parent
	 *
	 * @return array
	 */
	public function get_context_data($id, $parent, $id_parent)
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


	// ------------------------------------------------------------------------


	/**
	 * Cleans the media and the media lang table from unused medias
	 * Used by System tool
	 *
	 * @return int	Number of affected medias
	 *
	 */
	public function clean_table()
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


	// ------------------------------------------------------------------------


	/**
	 * Return the file type ('picture', 'music', 'video', 'file') regarding to its extension
	 *
	 * @param $filename
	 *
	 * @return	string	media type
	 *
	 */
	public function get_type($filename)
	{
		$mimes_types = Settings::get_mimes_types();

		$file_extension = pathinfo($filename, PATHINFO_EXTENSION);

		foreach($mimes_types as $type => $extension)
		{
			$keys = array_keys($extension);
			if (in_array($file_extension, $keys))
				return $type;
		}

		return 'file';
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $filename
	 *
	 * @return bool
	 */
	public function has_allowed_extension($filename)
	{
		$file_extension = pathinfo($filename, PATHINFO_EXTENSION);
		$extensions = Settings::get_allowed_extensions();

		if (in_array($file_extension, $extensions))
			return TRUE;

		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns array of broken medias
	 * @return array
	 */
	public function get_brokens()
	{
		$brokens = array();

		$medias = $this->get_all();

		foreach($medias as $media)
		{
			if ( empty($media['provider']) && ! file_exists(DOCPATH . $media['path']))
			{
				$brokens[] = $media;
			}
		}

		return $brokens;
	}


	// ------------------------------------------------------------------------


	/**
	 * Update media path and basename
	 * Updates articles content : replace media path
	 *
	 * @param      $old_path
	 * @param      $new_path
	 * @param bool $is_dir
	 */
	public function update_path($old_path, $new_path, $is_dir=FALSE)
	{
		$old_path = str_replace(FCPATH, '', $old_path);
		$new_path = str_replace(FCPATH, '', $new_path);

		if ( ! $is_dir)
		{
			// Basic update
			$this->update(
				array('path' => $old_path),
				array(
					'path' => $new_path,
					'file_name' => basename($new_path)
				)
			);

		}
		else
		{
			$sql = "
				update media
				set path = REPLACE(path, '".$old_path."', '".$new_path."')
			";
			$this->{$this->db_group}->query($sql);
		}

		// Articles
		$sql = "
				update article_lang
				set content = REPLACE(content, '".$old_path."', '".$new_path."')
			";
		$this->{$this->db_group}->query($sql);
	}


	// ------------------------------------------------------------------------


	/**
	 * Unlink pages and article from media which have the given path
	 * @param      $path
	 * @param bool $is_dir
	 */
	public function unlink_path($path, $is_dir=FALSE)
	{
		$path = str_replace(FCPATH, '', $path);

		if ($is_dir)
			$filter = "like '".$path."/%'";
		else
			$filter = "= '".$path."'";


		$sql = "
			delete from page_media where id_media in
			(
				select id_media from media where path ".$filter."
			)
		";
		$this->{$this->db_group}->query($sql);

		$sql = "
			delete from article_media where id_media in
			(
				select id_media from media where path ".$filter."
			)
		";
		$this->{$this->db_group}->query($sql);
	}


	// ------------------------------------------------------------------------


	/**
	 * Init all "path"_hash" from media table
	 *
	 * @return int
	 *
	 */
	public function init_hashes()
	{
		$nb = 0;

		$medias = $this->get_all();

		foreach($medias as $media)
		{
			$data = array(
				'path_hash' => hash(config_item('files_path_hash_method'), $media['path'])
			);
			$nb += $this->update($media['id_media'], $data);
		}

		return $nb;
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 * @param $string
	 *
	 * @return mixed
	 */
	public function parse_content_media_url($string)
	{
		$fp = Settings::get('files_path').'/';
		$src = 'src="' . $fp;
		$href = 'href="' . $fp;

		$src_base = 'src="/' . $fp;
		$href_base = 'href="/' . $fp;

		$string = str_replace($src_base, $src, $string);
		$string = str_replace($href_base, $href, $string);

		$string = str_replace($src, 'src="'.base_url().$fp, $string);
		$string = str_replace($href, 'href="'.base_url().$fp, $string);

		return $string;
	}




	// ------------------------------------------------------------------------


	private function _set_filter($filter = NULL)
	{
		if ( ! is_null($filter))
			$this->{$this->db_group}->where('('.$filter.')');
	}
}
