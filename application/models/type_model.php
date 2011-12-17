<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Type_model extends Base_model 
{

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'type';
		$this->pk_name 	=	'id_type';
	}


	// ------------------------------------------------------------------------


	/** 
	 * Gets list as array (id => name)
	 * 
	 */
	function get_types_select()
	{
		return $this->get_items_select($this->table, 'name', NULL, 'ordering ASC');
	}


	// ------------------------------------------------------------------------



}

/* End of file type_model.php */
/* Location: ./application/models/type_model.php */