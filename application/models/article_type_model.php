<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Article_type_model extends Base_model 
{

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'article_type';
		$this->pk_name 	=	'id_type';
	}


	// ------------------------------------------------------------------------


	/** 
	 * Gets list as array (id => name)
	 * 
	 */
	function get_types_select()
	{
		return $this->get_items_select($this->table, 'type', NULL, 'ordering ASC');
	}


	// ------------------------------------------------------------------------


	/**
	 * Update the article table after a type delete
	 *
	 * @param	int		type ID
	 *
	 */
	function update_article_after_delete($id_type)
	{
		$this->db->where($this->pk_name, $id_type);
		
		$this->db->set($this->pk_name, 'NULL');
		
		return $this->db->update('page_article');
	}



}

/* End of file category_model.php */
/* Location: ./application/models/category.php */