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
 * Ionize Permission Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Authority
 * @author		Ionize Dev Team
 *
 */

class rule_model extends Base_model
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'rule';
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $role
	 *
	 * @return array
	 */
	public function get_from_role($role)
	{
		$where = array(
			'id_role' => $role['id_role'],
			'id_user' => 0
		);

		$rules = $this->get_list($where);

		return $rules;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $user
	 *
	 * @return array
	 */
	public function get_from_user($user)
	{
		$rules = array();

		$role = $user->get_role();

		$this->{$this->db_group}->where(
			array(
				'id_role' => $role['id_role'],
				'id_user' => 0
			)
		);

		$role_rules = array();
		$this->{$this->db_group}->select('resource,actions,permission,id_element', FALSE);
		$query = $this->{$this->db_group}->get($this->get_table());
		if ( $query->num_rows() > 0 ) $role_rules = $query->result_array();

		$this->{$this->db_group}->where(
			array(
				'id_role' => 0,
				'id_user' => $user->getId()
			)
		);

		$user_rules = array();
		$this->{$this->db_group}->select('resource,actions,permission,id_element', FALSE);
		$query = $this->{$this->db_group}->get($this->get_table());
		if ( $query->num_rows() > 0 ) $user_rules = $query->result_array();

		// Process Role's rules
		foreach($role_rules as $rr)
		{
			$found = FALSE;
			foreach ($user_rules as $ur)
			{
				if ($rr['resource'] == $ur['resource'] && $rr['id_element'] == $ur['id_element'])
				{
					$found = TRUE;
					if ($ur['permission'] == 1)	$rules[] = $ur;
				}
			}
			if ( ! $found) $rules[] = $rr;
		}

		// Process User's Rules : Add missing to rules list
		foreach ($user_rules as $ur)
		{
			$found = FALSE;
			foreach($rules as $r)
			{
				if ($r['resource'] == $ur['resource'] && $r['id_element'] == $ur['id_element'])
					$found = TRUE;
			}
			if ( ! $found) $rules[] = $ur;
		}

		return $rules;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param null $type
	 *
	 * @return array
	 */
	public function get_from_type($type=NULL)
	{
		$where = NULL;

		if ( ! is_null($type))
		{
			$where = array("resource like '".$type."/%'");
		}

		return $this->get_list($where);
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns array of Roles IDs which have access to this resource
	 *
	 * HERE
	 * HERE
	 *
	 * @TODO : Be more detailed : Include actions.
	 * @param        $element
	 * @param        $element_id
	 * @param string $type
	 * @param int    $permission
	 *
	 * @return array
	 */
	public function get_element_role_ids($element, $element_id, $type='frontend', $permission=1)
	{
		$resource = $type . '/' . $element . '/' . $element_id;

		$where = array(
			'resource' => $resource,
			'permission' => $permission
		);

		return $this->get_group_concat_array(
			'id_role',
			$where
		);

	}


	// ------------------------------------------------------------------------


	/**
	 * @param $rules
	 *
	 * @return array
	 */
	public function format_rules($rules)
	{
		$data = array();
		foreach($rules as $rule)
		{
			$resource = $rule['resource'];
			$actions = explode(',', $rule['actions']);

			// TODO : Check roles priority...
			if ( ! in_array($resource, array_keys($data)))
			{
				$data[$resource] = array(
					'actions' => $actions,
					'permission' => $rule['permission']
				);
			}
		}
		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $id_role
	 */
	public function set_all_permissions($id_role)
	{
		$this->delete(array('id_role'=>$id_role));

		$data = array(
			'id_role' => $id_role,
			'resource' => 'all',
			'permission' => 1,
		);

		$this->insert($data);
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves rules for one element
	 *
	 * @param $resource
	 * @param $rules
	 */
	public function save_element_roles_rules($resource, $rules)
	{
		$data = array();

		if ( ! empty($rules))
		{
			foreach($rules as $id_role => $role_rules)
			{
				$resource_actions = array();

				foreach($role_rules as $rule)
				{
					$array = explode(':', $rule);
					$resource = $array[0];
					$action = isset($array[1]) ? $array[1] : NULL;

					// Resource / Actions array
					$actions = isset($resource_actions[$resource]) ? $resource_actions[$resource] : array();

					if ( ! is_null($action))
						$actions[]=$action;

					$resource_actions[$resource] = $actions;
				}
				foreach($resource_actions as $resource => $actions)
				{
					$data[] = array(
						'id_role' => $id_role,
						'resource' => $resource,
						'actions' => implode(',', $actions),
						'permission' => 1,
					);
				}
			}
		}

		$this->delete_element_roles_rules($resource);

		if ( ! empty($data))
		{
			$this->{$this->db_group}->insert_batch($this->get_table(), $data);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Save admin & module rules.
	 *
	 * @param $id_role
	 * @param $rules
	 * @param $type
	 */
	public function save_rules($id_role, $rules, $type)
	{
		self::$ci->load->model('resource_model', '', TRUE);

		$this->_delete_role_rules($id_role, $type);

		$data = $resource_actions = array();

		if ( ! empty($rules))
		{
			foreach($rules as $rule)
			{
				// Only if type is found
				if (strpos($rule, $type) === 0)
				{
					$array = explode(':', $rule);
					$resource = $array[0];
					$action = isset($array[1]) ? $array[1] : NULL;

					// Resource / Actions array
					$actions = isset($resource_actions[$resource]) ? $resource_actions[$resource] : array();

					if ( ! is_null($action))
						$actions[]=$action;

					$resource_actions[$resource] = $actions;
				}
			}
		}

		foreach($resource_actions as $resource => $actions)
		{
			$data[] = array(
				'id_role' => $id_role,
				'resource' => $resource,
				'actions' => implode(',', $actions),
				'permission' => 1,
			);
		}

		$all_resources = self::$ci->resource_model->get_all_resources();

		$this->_add_parent_resources_for_save($data, array_keys($resource_actions), $all_resources, $id_role);

		if ( ! empty($data))
		{
			$this->{$this->db_group}->insert_batch($this->get_table(), $data);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Deletes all rules concerning all roles for one resource,
	 * depending on the current User's level.
	 *
	 * @param       $resource
	 * 				ex : 'backend/page/8'
	 *
	 * @return mixed
	 */
	public function delete_element_roles_rules($resource)
	{
		// First get all roles_ids from roles under the current logged in user
		$role_ids = $this->get_group_concat_array(
			'id_role',
			array(
				'role_level <=' => User()->get('role_level'),
			),
			'role'
		);

		// Filter on roles
		if ( ! empty($role_ids))
			$this->{$this->db_group}->where_in('id_role', $role_ids);

		$this->{$this->db_group}->where('resource', $resource);

		return $this->{$this->db_group}->delete($this->get_table());
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $data
	 * @param $resources
	 * @param $all
	 * @param $id_role
	 */
	protected function _add_parent_resources_for_save(&$data, $resources, $all, $id_role)
	{
		$new_resources = array();

		foreach($resources as $resource)
		{
			foreach($all as $rec)
			{
				if ($resource == $rec['resource'] && ! is_null($rec['id_parent']))
				{
					foreach($all as $recParent)
					{
						if ($rec['id_parent'] == $recParent['id_resource'])
						{
							$new_resources[] = $recParent['resource'];
							$found = FALSE;
							foreach($data as $d)
							{
								if ($d['resource'] == $recParent['resource'])
								{
									$found = TRUE;
									break;
								}
							}
							if ( ! $found)
							{
								$data[] = array(
									'id_role' => $id_role,
									'resource' => $recParent['resource'],
									'actions' => '',
									'permission' => 1,
								);
							}
						}
					}
				}
			}
			$this->_add_parent_resources_for_save($data, $new_resources, $all, $id_role);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $id_role
	 * @param $type
	 *
	 * @return mixed
	 */
	protected function _delete_role_rules($id_role, $type)
	{
		$this->{$this->db_group}->where('id_role', $id_role);

		if ($type != 'all')
		{
			// Everything but starting with 'admin/' or 'module/'
			switch ($type)
			{
				case 'admin':
					$this->{$this->db_group}->where("substr(resource, 1, 5) = 'admin'");
					break;

				default:
					$this->{$this->db_group}->where("resource like '".$type."/%'");
					break;
			}
		}
		return $this->{$this->db_group}->delete($this->get_table());
	}
}
