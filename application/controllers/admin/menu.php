<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 * Menu Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.5
 */

class Menu extends MY_admin
{

	/**
	 * Frontend / Backend Authority actions
	 * @var array
	 */
	protected static $_AUTHORITY_BACKEND_ACTIONS = array();


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
                'menu_model',
                'rule_model',
                'resource_model'
            ), '', TRUE);
	}


	// ------------------------------------------------------------------------


	/**
	 * Menus list
	 *
	 */
	function index()
	{
		$menus = $this->menu_model->get_list(array('order_by' => 'ordering ASC'));

		foreach($menus as &$menu)
		{
			$backend_roles_resources = $this->resource_model->get_element_roles_resources(
				'menu',
				$menu['id_menu'],
				self::$_AUTHORITY_BACKEND_ACTIONS,
				'backend'
			);

			$menu['backend_roles_resources'] = $backend_roles_resources;
			$menu['backend_role_ids'] = $this->rule_model->get_element_role_ids('menu', $menu['id_menu'], 'backend');
		}

		$this->template['menus'] = $menus;

		$this->output('menu/index');
	}


	// ------------------------------------------------------------------------


	/**
	 * New Menu
	 *
	 */
	public function create()
	{
		$this->output('menu/menu');
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves a new menu
	 *
	 */
	public function save()
	{
		if( $this->input->post('name') != '' && $this->input->post('title') != '' )
		{
			$data = array(
				'name' => url_title($this->input->post('name'), 'underscore'),
				'title' => $this->input->post('title')
			);

			$this->menu_model->save($data);

			// UI panel to update after saving
			$this->_update_panels();

			// Answer send
			$this->success(lang('ionize_message_menu_saved'));
		}
		else
		{
			$this->error(lang('ionize_message_menu_not_saved'));
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Update one menu
	 *
	 */
	public function update()
	{
		$id = $this->input->post('id_menu');

		if ($id)
		{
			$this->menu_model->update($id, $this->input->post());

			if (Authority::can('access', 'admin/menu/permissions/backend'))
			{
				$resource = 'backend/menu/' . $id;
				$this->rule_model->save_element_roles_rules($resource, $this->input->post('backend_rule'));
			}
		}

		// UI update panels
		$this->_update_panels();

		$this->success(lang('ionize_message_menu_updated'));
	}


	// ------------------------------------------------------------------------


	/** 
	 * Saves ordering
	 * 
	 */
	public function save_ordering()
	{
		$order = $this->input->post('order');
		
		if( $order !== FALSE )
		{
			// Saves the new ordering
			$this->menu_model->save_ordering($order);
			
			// UI update panels
			$this->_update_panels();

			// Answer send
			$this->success(lang('ionize_message_menu_ordered'));
		}
		else 
		{
			// Answer send
			$this->error(lang('ionize_message_operation_nok'));
		}
	}


	// ------------------------------------------------------------------------


	/** 
	 * Delete a menu
	 *
	 * @param	string		menu ID
	 *
	 */
	public function delete($id)
	{
		$affected_rows = $this->menu_model->delete($id);

		if ($affected_rows > 0)
		{
			$this->_update_panels();

			// Answer send
			$this->success(lang('ionize_message_menu_deleted'));
		}
		else
		{
			// Answer send
			$this->error(lang('ionize_message_menu_not_deleted'));			
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Updates panels
	 *
	 */
	protected function _update_panels()
	{
		$this->update = array(
			array(
				'element' => 'mainPanel',
				'url' => admin_url() . 'menu/index'
			),
			array(
				'element' => 'structurePanel',
				'url' => admin_url() . 'tree'
			)
		);
	}
}
