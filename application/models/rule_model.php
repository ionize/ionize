<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
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


	public function get_from_role($role)
	{
		$this->{$this->db_group}->where_in('id_role', $role['id_role']);

		return $this->get_list();
	}


	// ------------------------------------------------------------------------


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

		$this->add_parent_resources_for_save($data, array_keys($resource_actions), $all_resources, $id_role);

		if ( ! empty($data))
		{
			$this->{$this->db_group}->insert_batch($this->get_table(), $data);
		}
	}


	protected function add_parent_resources_for_save(&$data, $resources, $all, $id_role)
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
			$this->add_parent_resources_for_save($data, $new_resources, $all, $id_role);
		}
	}


	// ------------------------------------------------------------------------


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
