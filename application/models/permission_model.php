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
 * @category	Authorizations
 * @author		Ionize Dev Team
 *
 */
class permission_model extends Base_model
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'permission';
		$this->pk_name = 	'id';
	}


	// ------------------------------------------------------------------------


	/**
	 * Get roles permissions
	 *
	 * @param $roles
	 *
	 * @return array
	 */
	public function get_from_roles($roles)
	{
		$role_ids = array();
		foreach($roles as $role)
		{
			$role_ids[] = $role['id_role'];
		}

		$this->{$this->db_group}->where_in('id_role', $role_ids);

		return $this->get_list();
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $permissions
	 *
	 * @return array
	 */
	public function format_permissions($permissions)
	{
		$data = array();
		foreach($permissions as $permission)
		{
			$resource = $permission['resource'];
			$actions = explode(',', $permission['actions']);

			// TODO : Check roles priority...
			if ( ! in_array($resource, array_keys($data)))
			{
				$data[$resource] = array(
					'actions' => $actions,
					'permission' => $permission['permission']
				);
			}
		}
		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Save 'all' permission
	 *
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
	 * Saves permissions
	 *
	 * @param      $id_role
	 * @param      $permissions
	 * @param null $type
	 */
	public function save_permissions($id_role, $permissions, $type=NULL)
	{
		$this->_delete_role_permissions($id_role, $type);

		$data = $resource_actions = array();

		foreach($permissions as $permission)
		{
			$array = explode(':', $permission);
			$resource = $array[0];
			$action = isset($array[1]) ? $array[1] : NULL;

			// Resource / Actions array
			$actions = isset($resource_actions[$resource]) ? $resource_actions[$resource] : array();
			if ( ! is_null($action))
				$actions[]=$action;

			$resource_actions[$resource] = $actions;
		}

		foreach($resource_actions as $resource=>$actions)
		{
			$data[] = array(
				'id_role' => $id_role,
				'resource' => $resource,
				'actions' => implode(',', $actions),
				'permission' => 1,
			);
		}

		if ( ! empty($data))
		{
			$this->{$this->db_group}->insert_batch($this->get_table(), $data);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $id_role
	 * @param $type
	 *
	 * @return mixed
	 */
	protected function _delete_role_permissions($id_role, $type)
	{
		$this->{$this->db_group}->where('id_role', $id_role);

		// Everything but starting with 'admin/' or 'module/'
		switch ($type)
		{
			case NULL:
				$this->{$this->db_group}->where("substr(resource, 0, 6) != 'admin/'");
				$this->{$this->db_group}->where("substr(resource, 0, 7) != 'module/'");
				break;

			case 'admin':
				$this->{$this->db_group}->where("substr(resource, 0, 6) = 'admin/'");
				break;

			default:
				$this->{$this->db_group}->where("resource like = '".$type."/%'");
				break;
		}

		return $this->{$this->db_group}->delete($this->get_table());
	}
}
