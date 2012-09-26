<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model handling database interacions for the Connect library.
 */
//  CI 2.0 Compatibility
//if(!class_exists('CI_Model')) { class CI_Model extends Model {} }

class Connect_model extends CI_Model 
{
	public $error = false;
	
	/**
	 * The table to store users in.
	 *
	 * @var string
	 */
	public $users_table = 'users';
	
	/**
	 * The table to store groups in.
	 *
	 * @var string
	 */
	public $groups_table = 'user_groups';
	
	/**
	 * Users table's PK
	 *
	 * @var string
	 */
	public $users_pk = 'id_user';
	
	/**
	 * Groups table's PK
	 *
	 * @var string
	 */
	public $groups_pk = 'id_group';
	
	/**
	 * The table storing the access attempt data.
	 *
	 * @var string
	 */
	public $tracker_table = 'login_tracker';
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * Contructor
	 *
	 */
	function __construct()
    {
        parent::__construct();

		$this->load->config('connect');

		$this->users_table 	= config_item('users_table');
		$this->users_pk 	= config_item('users_table_pk');
		
		$this->groups_table = config_item('groups_table');
		$this->groups_pk 	= config_item('groups_table_pk');

    }
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * Finds a user.
	 *
	 * @param $identification An array or string that identifies the user
	 *        Like array('email' => 'the email') or just the username
	 * @return object
	 */
	public function find_user($identification, $with_group = TRUE)
	{
		if( ! is_array($identification))
		{
			$identification = array('username' => $identification);
		}

		// return false if there are no conditions
		if( ! $this->num_conds($identification))
		{
			$this->error = $this->connect->set_error_message('connect_parameter_error', 'Connect_model::find_user()');
		}

		/*
		 * Removed to be compliant with cond array containing value like :
		 * 'my_field !=' => 'my_value'
		 *
		*/
		$fields = $this->db->list_fields($this->users_table);

		foreach($identification as $key => $data)
		{
			if ( ! in_array($key, $fields))
				unset($identification[$key]);
		}

		array_merge($identification, array('limit' => 1));

		$result = $this->get_users($identification);

		if(empty($result))
			return NULL;

		$user = array_shift($result);
		
		if ($with_group == FALSE)
		{
			unset($user['group']);
		}

		return $user;
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * Finds an arbitrary amount of users.
	 * 
	 * @param  array  The conditions to filter by, also limit, offset and order by
	 *                limit, offset and order_by are sent to the ActiveRecord methods
	 *                with the same name
	 * @return array  Array with User_records in it (groups are also stored in the user record)
	 */
	public function get_users($cond = array())
	{
		foreach(array('limit', 'offset', 'order_by', 'like') as $key)
		{
			if(isset($cond[$key]))
			{
				call_user_func(array($this->db, $key), $cond[$key]);
				unset($cond[$key]);
			}
		}

		$cond = $this->correct_ambiguous_conditions($cond);
		
		$this->db->join($this->groups_table, $this->users_table.'.'.$this->groups_pk.' = '.$this->groups_table.'.'.$this->groups_pk, 'left');

		$query = $this->db->get_where($this->users_table, $cond);

		$result = array();

		foreach($query->result_array() as $row)
		{
			$result[] = $this->split_user_group($row);
		}
		
		return $result;
	}
	
	
	// --------------------------------------------------------------------
	
	
	public function count_users($cond = array())
	{
		if(isset($cond['like']))
		{
			$this->db->like($cond['like']);
			unset($cond['like']);
		}
		
		unset($cond['order_by']);
		
		$this->db->where($cond);
		
		$this->db->from($this->users_table);
		
		$this->db->join($this->groups_table, $this->users_table.'.'.$this->groups_pk.' = '.$this->groups_table.'.'.$this->groups_pk, 'left');

		return $this->db->count_all_results();
		
		
	}
	
	
	// --------------------------------------------------------------------
	
	
	public function save_user($user_data = array())
	{
		$fields = $this->db->list_fields($this->users_table);

		foreach($user_data as $key => $value)
			if  (! in_array($key, $fields))
				unset($user_data[$key]);

		return $this->db->insert($this->users_table, $user_data);
	}
	
	
	// --------------------------------------------------------------------


	public function update_user($user_data = array())
	{
		$fields = $this->db->list_fields($this->users_table);

		$id_user = $user_data[$this->users_pk];

		foreach($user_data as $key => $value)
			if  (! in_array($key, $fields))
				unset($user_data[$key]);

		$this->db->where($this->users_pk, $id_user);

		return $this->db->update($this->users_table, $user_data);
	}

	// --------------------------------------------------------------------


	public function delete_user($id_user)
	{
		$this->db->where($this->users_pk, $id_user);

		return $this->db->delete($this->users_table);
	}

	// --------------------------------------------------------------------
	
	
	/**
	 * Bans a user.
	 * 
	 * @param  int   The user id
	 * @return bool
	 */
	public function ban_user($user_id)
	{		
		// don't allow the current user to ban himself by id, let him use the direct method instead:
		// Access()->get_current_user()->ban();
		if($this->connect->get_current_user() && $this->connect->get_current_user()->user_id == $user_id)
		{
			$this->error = $this->connect->set_error_message('connect_cannot_ban_yourself');
		}
		
		$query->select($this->groups_pk)
			  ->from($this->groups_table)
			  ->where('slug', $this->connect->banned_user_group);
		
		return $this->db->update($this->users_table, array($this->groups_pk => $query), array($this->users_pk => $user_id), 1);
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * Finds a certain group.
	 * 
	 * @param  int|array  id or condition
	 * @return Group_record
	 */
	public function find_group($id)
	{
		if( ! is_array($id))
		{
			$id = array($this->groups_pk => $id);
		}

		$query = $this->db->get_where($this->groups_table, $id, 1);

		if( ! $query->num_rows())
		{
			return false;
		}

		return $query->row_array();
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * Finds an arbitary amount of groups.
	 * 
	 * @param  array  The conditions to filter by, also limit, offset and order by
	 *                limit, offset and order_by are sent to the IgnitedQuery methods
	 *                with the same name
	 * @return array  Array with Group_records in it
	 */
	public function get_groups($cond = array())
	{
		foreach(array('limit', 'offset', 'order_by') as $key)
		{
			if(isset($cond[$key]))
			{
				call_user_func_array(array($this->db, $key), (Array) $cond[$key]);
				unset($cond[$key]);
			}
		}
		
		$query = $this->db->get_where($this->groups_table, $cond);

		$result = $query->result_array();

		return $result;
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
			if( ! empty($row) && ! empty($key))
			{
				$num_conds++;
			}
		}
		
		return $num_conds;
	}
	
	
	// --------------------------------------------------------------------
	
	
	function check_duplicate($str, $type)
	{
		return $this->db->select('1', false)->where($type, $str)->get($this->users_table)->num_rows;
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * Splits the group and user data into separate objects, user->group = group object.
	 *
	 * @param $data the data
	 * @return object
	 */
	private function split_user_group($data)
	{
		$g_data = array();

		foreach(array($this->groups_pk, 'slug', 'level', 'group_name', 'description') as $col)
		{
			$g_data[$col] = $data[$col];
			unset($data[$col]);
		}

		$data[$this->groups_pk] = $g_data[$this->groups_pk];
		$data['group'] = $g_data;

		return $data;
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * Sets the group for a user.
	 * 
	 * @param  int|string|array  String = slug, int = group_id
	 * @return void
	 */
	public function set_group($user, $group = null)
	{		
		if(is_numeric($group))
		{
			$user[$this->groups_pk] = $group;
		}
		elseif(is_array($group))
		{
			$user[$this->groups_pk] = $group[$this->groups_pk];
		}
		else
		{
			if( ! empty($group) && $g = $this->find_group(array('slug' => $group)))
			{
				$user[$this->groups_pk] = $g[$this->groups_pk];
			}
			else
			{
				// just assign the lowest level of access, subquery
				$sql = "SELECT ".$this->groups_pk."
						FROM ".$this->groups_table."
						WHERE LEVEL = (
							SELECT max(LEVEL )
							FROM ".$this->groups_table.")";
						
				$group = $this->db->query($sql)->row_array();
				
				$user[$this->groups_pk] = $group[$this->groups_pk];
			}
		}
		
		$this->db->where($this->users_pk, $user[$this->users_pk])
				->update($this->users_table, array($this->groups_pk => $user[$this->groups_pk]));
				
		return $user[$this->groups_pk];
	}
	
	
	// --------------------------------------------------------------------
	
	
	/**
	 * Updates the last visit counter.
	 * 
	 * @param  string  Date string formatted like 'Y-m-d H:i:s'
	 * @return void
	 */
	public function update_last_visit($user, $date = false)
	{
		$last_visit = $date ? $date : date('Y-m-d H:i:s');
		
		return $this->db->where($this->users_pk, $user[$this->users_pk])
					->update($this->users_table, array('last_visit' => $last_visit));
	}
	
	
	// --------------------------------------------------------------------
	
	
	public function save_tracker($tracker)
	{
		// update : No client IP : Set it ! 
		if ( empty($tracker['ip_address']) )
		{
			$tracker['ip_address'] = $this->input->ip_address();
			return $this->db->insert($this->tracker_table, $tracker);
		}
		else
		{
			return $this->db->where('ip_address', $this->input->ip_address())
					->update($this->tracker_table, $tracker);
		}
	}


	function correct_ambiguous_conditions($array)
	{
		if (is_array($array))
		{
			foreach ($array as $key => $val)
			{
				if ($key == $this->users_pk)
				{
					unset($array[$key]);
					$key = $this->users_table.'.'.$key;
					$array[$key] = $val;
				}
				if ($key == $this->groups_pk)
				{
					unset($array[$key]);
					$key = $this->groups_table.'.'.$key;
					$array[$key] = $val;
				}
			}
			return $array;
		}
	}

}


/* End of file connect_model.php */
/* Location: ./application/libraries/connect_model.php */