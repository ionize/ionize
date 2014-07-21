<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 * Role Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

class Role extends MY_Admin
{
	public $current_role = NULL;

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

        // Models
        $this->load->model(
            array(
                'role_model',
                'resource_model',
                'rule_model',
                'user_model'
            ), '', TRUE);

		// Current connected user level
		$this->current_role = User()->get_role();
	}


	// ------------------------------------------------------------------------


	/**
	 * Do nothing.
	 *
	 */
	public function index(){}


	// ------------------------------------------------------------------------


	/**
	 * Creation Form
	 *
	 */
	public function create()
	{
		// Get roles list filtered on level <= current_user level
		$roles = $this->role_model->get_list();
		$this->template['roles'] = array_filter($roles, array($this, '_filter_roles'));
				
		$this->output('role/create');
	}


	// ------------------------------------------------------------------------


	/**
	 * Edit
	 *
	 */
	public function edit()
	{
		$role = $this->role_model->get($this->input->post('id_role'));
		$this->template['role'] = $role;

		// Get roles list
		// TODO: Filter roles on level ?
		$roles = $this->role_model->get_list();
		$this->template['roles'] = array_filter($roles, array($this, '_filter_roles'));

		// All Admin Resources
		$resources = $this->resource_model->get_tree();
		$this->template['json_resources'] = json_encode($resources, TRUE);

		// All Modules Resources
		$modules_resources = Modules()->get_resources();
		$resources = $this->resource_model->build_resources_tree($modules_resources);
		$this->template['json_modules_resources'] = json_encode($resources, TRUE);

		// Role's permissions
		$rules = $this->rule_model->get_list(array('id_role'=> $role['id_role']));
		$this->template['has_all'] = $this->_has_all_permissions($rules);
		$this->template['json_rules'] = json_encode($rules, TRUE);

		$this->output('role/edit');
	}


	// ------------------------------------------------------------------------


	/**
	 * List
	 *
	 */
	public function get_list()
	{
		$roles = $this->role_model->get_list();
		$this->template['roles'] = array_filter($roles, array($this, '_filter_roles'));

		$this->output('role/list');
	}


	// ------------------------------------------------------------------------


	/**
	 * Save
	 *
	 */
	public function save()
	{
		if ($this->input->post('role_level') > $this->current_role['role_level'])
		{
			$this->error(lang('ionize_message_role_no_save_level_too_high'));
		}
		else
		{
			// Save basics
			if (Authority::can('edit', 'admin/role/permissions'))
			{
				$id_role = $this->role_model->save($this->input->post());
			}
			else
				$id_role = $this->input->post('id_role');

			// Only saves permissions if Role ID
			if (
				$id_role &&
				(
					Authority::can('access', 'admin/role/permissions') OR
					Authority::can('access', 'admin/modules/permissions')
				)
			)
			{
				$this->_save_rules($id_role);
			}


			// Reload role
			$this->_reload_role($id_role);

			// Success message
			$this->success(lang('ionize_message_role_saved'));
		}
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Delete
	 * 
	 */
	public function delete()
	{
		$id_role = $this->input->post('id_role');

		// Safe : Do not delete Role linked to users
		$nb_users = $this->user_model->count(array('user.id_role' => $id_role));

		if ($nb_users > 0)
		{
			$this->error(lang('ionize_message_role_no_delete_users_linked'));
		}
		else
		{
			$affected_rows = $this->role_model->delete($id_role);

			if ($affected_rows > 0)
			{
				// Update role list panel
				$this->_reload_role_list();

				$this->success(lang('ionize_message_role_deleted'));
			}
			else
			{
				$this->error(lang('ionize_message_role_not_deleted'));
			}
		}
	}


	// ------------------------------------------------------------------------


	private function _save_rules($id_role)
	{
		// Posted rules & permission level
		$rules = $this->input->post('rules');
		$permission_level = $this->input->post('permission_level');

		// Admin Permissions
		if (Authority::can('access', 'admin/role/permissions'))
		{

			if ($permission_level)
			{
				// Delete all permissions and add 'all' one
				if ($permission_level == 'all')
				{
					$this->rule_model->set_all_permissions($id_role);
				}
				// Save custom permissions
				else
				{
					$this->rule_model->save_rules($id_role, $rules, 'admin');
				}
			}
		}

		// Modules Permissions
		if ($permission_level != 'all' && Authority::can('access', 'admin/modules/permissions'))
		{
			$this->rule_model->save_rules($id_role, $rules, 'module');
		}

	}


	// ------------------------------------------------------------------------


	/**
	 * Roles filter callback function
	 * @param $row
	 *
	 * @return bool
	 */
	public function _filter_roles($row)
	{
		return ($row['role_level'] <= $this->current_role['role_level']) ? TRUE : FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $permissions
	 *
	 * @return bool
	 */
	private function _has_all_permissions($permissions)
	{
		if ( ! empty($permissions))
		{
			foreach($permissions as $permission)
			{
				if ($permission['resource'] == 'all')
					return TRUE;
			}
		}
		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Reloads the Role List
	 *
	 */
	private function _reload_role_list()
	{
		// Save options : as callback
		$this->callback[] = array(
			'fn' => 'ION.HTML',
			'args' => array(
				'role/get_list',
				'',
				array(
					'update'=> 'roleContainer'
				)
			)
		);
	}


	// ------------------------------------------------------------------------


	/**
	 * Reloads one Role
	 *
	 * @param $id_role
	 */
	private function _reload_role($id_role)
	{
		// Save options : as callback
		$this->callback[] = array(
			'fn' => 'ION.HTML',
			'args' => array(
				'role/edit',
				array('id_role' => $id_role),
				array(
					'update'=> 'roleContainer'
				)
			)
		);
	}
}
