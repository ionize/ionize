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

	protected static $_MP3_ID3 = array('album', 'artist', 'title', 'year');

	protected static $_VIDEO_PROVIDERS = array('youtube', 'vimeo', 'dailymotion');

	protected static $_UNUSED_IGNORED_FILES;

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

        self::$_UNUSED_IGNORED_FILES = array(
            Settings::get('no_source_picture'),
            'index.html',
            'watermark.png'
        );

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
		$media_table = $parent.'_'.$this->get_table();
		
		if ($parent_pk !== FALSE)
		{
			// Select from media table
			$this->{$this->db_group}->order_by('ordering', 'ASC');
			$this->{$this->db_group}->select($this->get_table().'.*', FALSE);
			$this->{$this->db_group}->select($media_table.'.ordering, '.$media_table.'.lang_display', FALSE);
			
			// Limit to current parent ID
			$this->{$this->db_group}->where($media_table.'.'.$parent_pk, $id_parent);
			
			if ( ! is_null($type))
				$this->{$this->db_group}->where($this->get_table().'.type', $type);
			
			// Join to link table
			$this->{$this->db_group}->join($media_table, $this->get_table().'.'.$this->get_pk_name().'='.$media_table.'.'.$this->get_pk_name());

			$this->{$this->db_group}->select($media_table.'.ordering');

			$query = $this->{$this->db_group}->get($this->get_table());

			if($query->num_rows() > 0)
				$data = $query->result_array();
		}
		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all medias linked to one extend field
	 *
	 * @param      $id_extend
	 * @param      $parent
	 * @param      $id_parent
	 * @param null $lang
	 *
	 * @return array
	 */
	public function get_extend_media_list($id_extend, $parent, $id_parent, $lang=NULL)
	{
		$data = array();

		self::$ci->load->model('extend_field_model', '', true);

		if ( ! $lang) $lang=NULL;

		$extend = self::$ci->extend_field_model->get_element_extend_field($id_extend, $parent, $id_parent, $lang);

		if ( ! empty($extend))
		{
			$ids = strlen($extend['content']) > 0 ? explode(',', $extend['content']) : NULL;

			if ( ! empty($ids))
			{
				$this->{$this->db_group}->select($this->get_table().'.*', FALSE);

				$where = array(
					'where_in' => array($this->get_table().'.id_media' => $ids),
					'order_by' => "field(" . $this->get_table() . ".id_media, ".$extend['content'] . ")"
				);

				if ( ! is_null($lang))
				{
					$this->{$this->db_group}->select($this->get_lang_table().'.*', FALSE);

					$this->{$this->db_group}->join(
						$this->get_lang_table(),
						$this->get_lang_table().'.'.$this->get_pk_name().'='.$this->get_table().'.'.$this->get_pk_name().
						' AND ' . $this->get_lang_table().'.lang = \'' . $lang . '\'',
						'left'
					);
				}

				$data = parent::get_list($where, $this->get_table());
			}
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
			if (! is_array($val) && strpos($val, 'id_media') === 0)
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
	 * @param      $path		Complete relative path to the medium, including file name, including the "files" folder
	 * @param null $provider
	 *
	 * @return bool				TRUE if succeed, FALSE if errors
	 */
	public function insert_media($path, $provider=NULL)
	{
		if ($path)
		{
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

			$type = $this->get_type($file_name, $provider);

			$data = array(
				'type' => $type,
				'path' => $path,
				'file_name' => $file_name,
				'base_path' => $base_path,
				'provider' => ! is_null($provider) ? $provider : ''
			);

			// Update if exists
			$is_new = FALSE;
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
				$is_new = TRUE;
				$this->{$this->db_group}->insert($this->table, $data);
				$id = $this->{$this->db_group}->insert_id();
			}

			// Tag ID3 if MP3
			if ($type == 'music' && $this->is($path, 'mp3') && $is_new)
			{
				$data['id_media'] = $id;

				// Displayed datas
				$tags = $this->get_ID3($path);
				$data['copyright'] = $tags['artist'];
				$data['date'] = date('Y.m.d H:m:s', strtotime($tags['year']));

				// Title
				foreach(Settings::get_languages() as $lang)
				{
					$data[$lang['lang']]['title'] = $tags['title'];
					$data[$lang['lang']]['alt'] = $data[$lang['lang']]['description'] = $tags['artist'] . ' - ' . $tags['album'] . ' : ' . $tags['title'];
				}

				$this->save($data, $data);
			}

			return $id;
		}
		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Attach one medium to a parent
	 *
	 * @param	string	parent. Example : 'article', 'page'
	 * @param	string	Parent ID
	 * @param	string	Medium ID
	 * @return	boolean	TRUE if success, FALSE if error
	 *
	 */
	public function attach_media($parent, $id_parent, $id_media)
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

				$query = $this->{$this->db_group}->get($media_table);

				$ordering = 0;
				if ($query->num_rows() > 0)
				{
					$row =		$query->row();
					$ordering =	$row->ordering;
				}
				$ordering += 1;
				$this->{$this->db_group}->set('ordering', $ordering);
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

		}

		return FALSE;
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
		$tables = $this->{$this->db_group}->list_tables();
		$process_tables = array();
		$nb_affected_rows = 0;

		$left_joins = $wheres = '';

		foreach ($tables as $table)
		{
			if (substr($table, -6) == '_media')
			{
				$fields = $this->{$this->db_group}->field_data($table);

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

		// IDs of media linked to regular media tables
		// $sql = ' delete m from media m ' . $left_joins . ' where ' . $wheres;
		$medias = array();
		$sql = 'select m.id_media from media m ' . $left_joins . ' where ' . $wheres;
		$query = $this->{$this->db_group}->query($sql);
		if($query->num_rows() > 0)
			$medias = $query->result_array();

		if ( ! empty($medias))
		{
			// Remove medias used by Extend Fields from medias to delete
			$sql = "SET SESSION group_concat_max_len = 1000000;";
			$this->{$this->db_group}->query($sql);

			$sql = "
				select group_concat(content separator ',') as ids from extend_fields
				where id_extend_field in (
					select id_extend_field from extend_field where type=8
				)
			";

			$query = $this->{$this->db_group}->query($sql);
			$used_ids = '';
			if($query->num_rows() > 0) $used_ids = $query->row_array();

			if( ! empty($used_ids['ids']))
			{
				$used_ids = explode(',', $used_ids['ids']);

				foreach($medias as $key => $media)
				{
					if (in_array($media['id_media'], $used_ids))
						unset($medias[$key]);
				}
			}

			// Build the id of media to remove
			$media_ids = array();
			foreach($medias as $media)
			{
				$media_ids[] = $media['id_media'];
			}

			// Finally delete the concerned medias from media table
			if ( ! empty($media_ids))
			{
				$this->{$this->db_group}->where_in('id_media', $media_ids);
				$this->{$this->db_group}->delete('media');

				$nb_affected_rows = (int) $this->{$this->db_group}->affected_rows();

				$this->{$this->db_group}->where_in('id_media', $media_ids);
				$this->{$this->db_group}->delete('media_lang');
			}
		}

		return $nb_affected_rows;
	}


	// ------------------------------------------------------------------------


	/**
	 * Return the file type ('picture', 'music', 'video', 'file') regarding to its extension
	 *
	 * @param $filename
	 * @param $provider
	 *
	 * @return	string	media type
	 *
	 */
	public function get_type($filename, $provider = NULL)
	{
		$mimes_types = Settings::get_mimes_types();

		$file_extension = pathinfo($filename, PATHINFO_EXTENSION);

		foreach($mimes_types as $type => $extension)
		{
			$keys = array_keys($extension);
			if (in_array($file_extension, $keys))
				return $type;
		}

		if ( ! is_null($provider) && in_array($provider, self::$_VIDEO_PROVIDERS))
			return 'video';

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
	 * Returns array of unused media files
	 * (on the disk)
	 *
	 * @return array
	 */
	public function get_unused_files()
	{
		self::$ci->load->helper('number');

		$directory = new RecursiveDirectoryIterator(FCPATH.Settings::get('files_path'), FilesystemIterator::SKIP_DOTS);
		$fc_length = strlen(FCPATH);
		$unused_size = $total_size = $nb_total = 0;

		$tb_str = Settings::get('files_path').'/.thumbs';
		$tb_length = strlen($tb_str);

		$files = $paths = array();

		$media_paths = array();
		$medias = $this->get_all();

		foreach($medias as $media)
		{
			$media_paths[] = $media['path'];
		}

		foreach (new RecursiveIteratorIterator($directory) as $filename => $current)
		{
			$path = substr($filename, $fc_length);

			if (substr($path, 0, $tb_length) != $tb_str)
			{
				$nb_total += 1;
				$size = $current->getSize();
				$total_size += $size;

				if ( ! in_array($path, $media_paths))
				{
					$files[] = array(
						'path' => $path,
						'size' => byte_format($size, 1)
					);
					$unused_size += $size;
				}
			}
		}

		// Check for articles content
		foreach($files as $key => $file)
		{
			$sql = "select id_article from article_lang where content like '%" . $file['path'] . "%'";
			$query = $this->{$this->db_group}->query($sql);

			if($query->num_rows() > 0 || in_array(basename($file['path']), self::$_UNUSED_IGNORED_FILES))
			{
				unset($files[$key]);
				$nb_total -= 1;
				$unused_size -= filesize(DOCPATH . $file['path']);
			}
		}

		$return = array(
			'nb_total' => $nb_total,
			'size_total' => byte_format($total_size, 2),
			'files' => $files,
			'size' => byte_format($unused_size, 2)
		);

		return $return;
	}


	// ------------------------------------------------------------------------


	public function delete_files($files=array())
	{
		$nb = 0;

		foreach ($files as $path)
		{
			if (file_exists(FCPATH.$path))
			{
				@unlink(FCPATH.$path);
			}
		}

		return $nb;
	}


	// ------------------------------------------------------------------------


	public function get_media_space()
	{
		$directory= new RecursiveDirectoryIterator(FCPATH.Settings::get('files_path'));

		$result = array(
			'total' => 0,
			'nb_files' => 0
		);

		foreach (new RecursiveIteratorIterator($directory) as $filename=>$cur)
		{
			$size = $cur->getSize();
			$result['total'] += $size;
			$result['nb_files'] += 1;
		}

		return $result;
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

	/**
	 * @param $path
	 *
	 * @return array
	 */
	public function get_ID3($path)
	{
		$tags = array_fill_keys(self::$_MP3_ID3, '');

		if ( is_file(DOCPATH.$path) )
		{
			require_once(APPPATH.'libraries/getid3/getid3.php');

			// Initialize getID3 engine
			$getID3 = new getID3;

			// Analyze file and store returned data in $ThisFileInfo
			$id3 = $getID3->analyze(DOCPATH.$path);

			foreach(self::$_MP3_ID3 as $index)
			{
				$tags[$index] = ( ! empty($id3['tags_html']['id3v2'][$index][0])) ? $id3['tags_html']['id3v2'][$index][0] : '';
			}
		}

		return $tags;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $path
	 * @param $tags
	 *
	 * @return bool
	 */
	public function write_ID3($path, $tags)
	{
		if ( is_file(DOCPATH.$path) )
		{
			require_once(APPPATH.'libraries/getid3/getid3.php');

			$getID3 = new getID3;
			$getID3->setOption(array('encoding'=>'UTF-8'));
			getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'write.php', __FILE__, TRUE);

			$tagwriter = new getid3_writetags;
			$tagwriter->filename = $path;
			$tagwriter->tag_encoding = 'UTF-8';
			$tagwriter->tagformats = array('id3v1', 'id3v2.3');
			$tagwriter->overwrite_tags = TRUE;
			$tagwriter->tag_data = $tags;

			$tagwriter->WriteTags();

			if (!empty($tagwriter->warnings))
			{
				return FALSE;
			}
			return TRUE;
		}
		return FALSE;
	}

	// ------------------------------------------------------------------------


	/**
	 * @param $path
	 * @param $ext
	 *
	 * @return bool
	 */
	public function is($path, $ext)
	{
		if (pathinfo(DOCPATH.$path, PATHINFO_EXTENSION) == $ext)
			return TRUE;

		return FALSE;
	}


	// ------------------------------------------------------------------------


	private function _set_filter($filter = NULL)
	{
		if ( ! is_null($filter))
			$this->{$this->db_group}->where('('.$filter.')');
	}
}
