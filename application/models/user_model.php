<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize User Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	User
 * @author		Ionize Dev Team
 *
 */

class User_model extends Base_model
{
	/**
	 * Role table
	 * @var string
	 */
	static $ROLE_TABLE = 'role';

	/**
	 * The table storing the access attempt data.
	 * @var string
	 */
	public $tracker_table = 'login_tracker';


	// --------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		parent::__construct();

		$this->load->config('user');

		$this->set_table(config_item('user_table'));
		$this->set_pk_name(config_item('user_table_pk'));
	}


	// --------------------------------------------------------------------


	/**
	 * Finds a user.
	 *
	 * @param 	$identification 	Array or string that identifies the user
	 *        						Like array('email' => 'the email') or just the email
	 * @return 	array
	 *
	 */
	public function find_user($identification)
	{
		if( ! is_array($identification))
		{
			$identification = array('email' => $identification);
		}

		// FALSE if no conditions
		if( ! $this->num_conds($identification))
			return FALSE;

		$fields = $this->{$this->db_group}->list_fields($this->table);

		foreach($identification as $key => $data)
		{
			if ( ! in_array($key, $fields))
				unset($identification[$key]);
		}

		$identification = array_merge($identification, array('limit' => 1));

		$user = $this->get_user($identification);

		if( empty($user) )
			return NULL;

		return $user;
	}


	// --------------------------------------------------------------------


	/**
	 * Finds one user
	 * Used by $this->find_user()
	 *
	 * @param  array  Conditions to filter by, also limit, offset and order by
	 *                limit, offset and order_by are sent to the ActiveRecord methods
	 *                with the same name
	 * @return array
	 *
	 */
	public function get_user($where = array())
	{
		$result = array();

		foreach(array('limit', 'offset', 'order_by', 'like') as $key)
		{
			if(isset($where[$key]))
			{
				call_user_func(array($this->{$this->db_group}, $key), $where[$key]);
				unset($where[$key]);
			}
		}

		$this->{$this->db_group}->select('user.*');
		$this->_join_role();

		$query = $this->{$this->db_group}->get_where($this->table, $where);

		if ( $query->num_rows() > 0)
			$result = $query->row_array();

		return $result;
	}


	// --------------------------------------------------------------------


	/**
	 * @param array $where
	 *
	 * @return array
	 */
	public function get_list_with_role($where = array())
	{
		$this->_join_role();

		return parent::get_list($where);
	}


	// --------------------------------------------------------------------


	/**
	 * @param array $where
	 *
	 * @return int
	 */
	public function count($where=array())
	{
		unset($where['limit']);
		unset($where['offset']);

		$this->_join_role();

		$nb = parent::count($where);

		return $nb;
	}


	// --------------------------------------------------------------------


	/**
	 * @param      $user
	 * @param null $role_code
	 *
	 * @return int|null|the
	 */
	public function save($user, $role_code = NULL)
	{
		$user = $this->_clean_user_data($user);

		$id_user = NULL;

		if ( ! empty($user[$this->pk_name]))
		{
			$db_user = $this->find_user(array($this->pk_name => $user[$this->pk_name]));

			if ( ! empty($db_user))
			{
				$id_user = $db_user[$this->pk_name];
				$this->{$this->db_group}->where($this->pk_name, $id_user);
				$this->{$this->db_group}->update($this->table, $user);
			}
		}
		else
		{
			$id_user = $this->insert($user);
		}

		// Set user's role
		if ( ! is_null($role_code) && ! is_null($id_user))
		{
			$this->set_role($id_user, $role_code);
		}

		return $id_user;
	}


	// --------------------------------------------------------------------


	/**
	 * @param $id_user
	 * @param $role_code
	 *
	 * @return bool
	 */
	public function set_role($id_user, $role_code)
	{
		self::$ci->load->model('role_model', '', TRUE);

		$role = self::$ci->role_model->get(array('role_code' => $role_code));

		if ( ! empty($role))
		{
			$this->{$this->db_group}->where($this->pk_name, $id_user);
			$this->{$this->db_group}->update($this->table, array('id_role'=> $role['id_role']));

			return TRUE;
		}
		return FALSE;
	}


	// --------------------------------------------------------------------


	/**
	 * @param null $id_user
	 *
	 * @return int
	 */
	public function delete($id_user)
	{
		$this->{$this->db_group}->where($this->pk_name, $id_user);

		return $this->{$this->db_group}->delete($this->table);
	}


	// --------------------------------------------------------------------


	/**
	 * Counts the identification values because empty may enable fetching of any user -
	 * a potential security vulnerability.
	 *
	 * @param  mixed
	 * @return int
	 */
	private function num_conds($conds = array())
	{
		$num_conds = 0;

		foreach((Array) $conds as $key => $row)
		{
			if( ! empty($key) && ! empty($row))
				$num_conds++;
		}

		return $num_conds;
	}


	// --------------------------------------------------------------------


	/**
	 * Updates the last visit counter.
	 *
	 * @param      $user
	 * @param bool $date
	 *
	 * @return mixed
	 */
	public function update_last_visit($user, $date = FALSE)
	{
		$last_visit = $date ? $date : date('Y-m-d H:i:s');

		return $this->{$this->db_group}->where($this->pk_name, $user[$this->pk_name])
			->update($this->table, array('last_visit' => $last_visit));
	}


	// --------------------------------------------------------------------


	/**
	 * @param $tracker
	 *
	 * @return mixed
	 */
	public function save_tracker($tracker)
	{
		// update : No client IP : Set it !
		if ( empty($tracker['ip_address']) )
		{
			$tracker['ip_address'] = $this->input->ip_address();
			return $this->{$this->db_group}->insert($this->tracker_table, $tracker);
		}
		else
		{
			return $this->{$this->db_group}->where('ip_address', $this->input->ip_address())
				->update($this->tracker_table, $tracker);
		}
	}


	// --------------------------------------------------------------------


	/**
	 * @return null
	 */
	public function getPkName()
	{
		return $this->pk_name;
	}


	// --------------------------------------------------------------------


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


	// ------------------------------------------------------------------------


	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	private function _clean_user_data($data)
	{
		$fields = $this->{$this->db_group}->list_fields($this->table);

		foreach($data as $key => $value)
			if  (! in_array($key, $fields))
				unset($data[$key]);

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Joins user's an roles tables
	 */
	private function _join_role()
	{
		$this->{$this->db_group}->select('role_code, role_name, role_description, role_level');

		$this->{$this->db_group}->join(
			self::$ROLE_TABLE,
			self::$ROLE_TABLE.'.id_role = ' . $this->get_table() . '.id_role',
			'left'
		);
	}
}
