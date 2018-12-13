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
	function get_select($parent = NULL, $nothing_value = NULL)
	{
 		$data = array();
		
		if ( ! is_null($nothing_value))
			$data = array('' => $nothing_value);

		if ( ! is_null($parent))
			$this->{$this->db_group}->where('parent', $parent);

 		$this->{$this->db_group}->order_by('ordering', 'ASC');
			
		$query = $this->{$this->db_group}->get($this->table);

		if($query->num_rows() > 0)
		{
			$result = $query->result_array();
			
			foreach($result as $item)
			{
				$data[$item['id_type']] = $item['title'];
			}
		}			

		return $data;
	}


	// ------------------------------------------------------------------------



}
