<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Demo module's Author Model
 * To avoid models collision, the models should be named like this :
 * <Module>_<Model name>_model
 *
 */

class Demo_author_model extends Base_model
{
	// Author tables
	protected $_author_table = 'module_demo_author';
	protected $_author_lang_table = 'module_demo_author_lang';

	// Link table between authors and parents (page, article)
	protected $_link_table = 'module_demo_links';


	/**
	 * Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		$this->set_table($this->_author_table);
		$this->set_lang_table($this->_author_lang_table);
		$this->set_pk_name('id_author');
		
		parent::__construct();
	}


	public function save($inputs)
	{
		// Arrays of data which will be saved
		$data = $data_lang = array();

		// Fields of the author table
		$fields = $this->list_fields();

		// Set the data to the posted value.
		foreach ($fields as $field)
			$data[$field] = $inputs[$field];

		$lang_fields = $this->list_fields($this->_author_lang_table);

		foreach(Settings::get_languages() as $language)
		{
			foreach ($lang_fields as $field)
			{
				if ($field != $this->pk_name && $field != 'lang')
				{
					$input_field = $field.'_'.$language['lang'];
					if ($inputs[$input_field] !== FALSE)
						$data_lang[$language['lang']][$field] = $inputs[$input_field];
				}
			}
		}

		return parent::save($data, $data_lang);
	}


	/**
	 * Deletes one Author
	 * and the corresponding lang data
	 *
	 * @param int 	$id
	 *
	 * @return int	Number of delete items in main table
	 *
	 */
	public function delete($id)
	{
		$nb_rows = parent::delete($id, $this->_author_table);

		if ($nb_rows > 0)
			parent::delete($id, $this->_author_lang_table);

		return $nb_rows;
	}


	public function get_linked_author($parent, $id_parent)
	{
		// Returned data
		$data = array();

		// Conditions
		$where = array(
			'parent' => $parent,
			'id_parent' => $id_parent,
			$this->_author_lang_table.'.lang' => Settings::get_lang('default')
		);

		$query = $this->{$this->db_group}
			->where($where)
			->order_by('ordering ASC')
			->join(
				$this->_author_table,
				$this->_author_table.'.id_author = ' . $this->_link_table.'.id_author',
				'left'
			)
			->join(
				$this->_author_lang_table,
				$this->_author_lang_table.'.id_author = ' . $this->_author_table.'.id_author',
				'left'
			)
			->get($this->_link_table)
		;

		if ( $query->num_rows() > 0 )
			$data = $query->result_array();

		return $data;
	}


	/**
	 * Creates one link between one parent and one author
	 *
	 * @param string		Parent code (article, page)
	 * @param int			Parent ID
	 * @param int			Author ID
	 *
	 * @return bool			TRUE if inserted, FALSE if the link already exists
	 *
	 */
	public function link_author_to_parent($parent, $id_parent, $id_author)
	{
		$data = array(
			'parent' => $parent,
			'id_parent' => $id_parent,
			'id_author' => $id_author
		);
		$this->db->where($data);

		$query = $this->{$this->db_group}
			->where($data)
			->get($this->_link_table);

		if ($query->num_rows() == 0)
		{
			$this->{$this->db_group}->insert($this->_link_table, $data);
			return TRUE;
		}
		return FALSE;
	}


	/**
	 * Deletes on link between one author and one parent
	 *
	 * @param string		Parent code (article, page)
	 * @param int			Parent ID
	 * @param int			Author ID
	 *
	 * @return int			Number of affected rows (1 or 0)
	 *
	 */
	public function unlink_author_from_parent($parent, $id_parent, $id_author)
	{
		$where = array(
			'parent' => $parent,
			'id_parent' => $id_parent,
			'id_author' => $id_author
		);

		return $this->{$this->db_group}->delete($this->_link_table, $where);
	}
}