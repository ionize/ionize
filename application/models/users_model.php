<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.4
 */

// ------------------------------------------------------------------------

/**
 * Ionize Users Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Users
 * @author		Ionize Dev Team
 *
 */

class Users_model extends Base_model 
{

	public $group_table = 'user_groups';


	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'users';
		$this->pk_name = 	'id_user';

		log_message('debug', __CLASS__ . " Class Initialized");
	}


	function get($where)
	{
		$jt = $this->group_table;

		$select = array(
			'level',
			'slug',
			'group_name',
			'description',
		);

		foreach($select as &$field)	$field = $jt.'.'.$field;

		$this->{$this->db_group}->select(implode(',', $select));
		$this->{$this->db_group}->join($jt, $jt.'.id_group = '.$this->table . '.id_group');

		return  parent::get($where);
	}


	/**
	 * Returns the users list.
	 *
	 */
	function get_list()
	{
		$data = array(); 
	
		// Standard users data
		$this->{$this->db_group}->select();

		$this->{$this->db_group}->order_by('screen_name', 'ASC');

		$query = $this->{$this->db_group}->get($this->table);
		
		if ( $query->num_rows() > 0 )
			$data = $query->result_array();
				
		return $data;
	}


	// ------------------------------------------------------------------------
	

	/**
	 * Deletes one user
	 *
	 *
	 */
	function delete($id)
	{
		$affected_rows = 0;
		
		// Check if element exists
		if( $this->exists(array($this->pk_name => $id)) )
		{
			// User delete
			$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id)->delete($this->table);
		}
		return $affected_rows;	
	}
}
/* End of file users_model.php */
/* Location: ./application/models/users_model.php */