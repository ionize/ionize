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

		log_message('debug', __CLASS__ . " Class Initialized");
	}


	// ------------------------------------------------------------------------


	/** 
	 * Gets list as array (id => name)
	 * 
	 */
	public function get_types_select()
	{
		return $this->get_items_select($this->table, 'type', NULL, 'ordering ASC');
	}


	// ------------------------------------------------------------------------


	/**
	 * Update the article table after a type delete
	 *
	 * @param	int		type ID
	 *
	 * @return	int		Number of updated items
	 *
	 */
	public function update_article_after_delete($id_type)
	{
		$this->{$this->db_group}->where($this->pk_name, $id_type);
		
		$this->{$this->db_group}->set($this->pk_name, 'NULL');
		
		return $this->{$this->db_group}->update('page_article');
	}
}

/* End of file article_type_model.php */
/* Location: ./application/models/article_type_model.php */