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

		$this->{$this->db_group}->select('users.*');
		$this->{$this->db_group}->select(implode(',', $select));
		$this->{$this->db_group}->join($jt, $jt.'.id_group = '.$this->table . '.id_group');

		return  parent::get($where);
	}


	/**
	 * Returns the users list.
	 *
	 */
	function get_list($where = NULL)
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


	// ------------------------------------------------------------------------

	/**
	 * @param      $email
	 * @param null $id_user
	 *
	 * @return bool
	 *
	 */
	public function user_with_same_email_exists($email, $id_user = NULL)
	{
		$user = $this->get(array('email' => $email));

		if ( ! is_null($id_user) && $id_user != FALSE)
		{
			if ( ! empty($user) && $user['id_user'] != $id_user)
				return TRUE;
		}
		else
		{
			if ( ! empty($user))
				return TRUE;
		}
		return FALSE;
	}

}
/* End of file users_model.php */
/* Location: ./application/models/users_model.php */