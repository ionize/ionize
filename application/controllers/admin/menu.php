<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.5
 */

// ------------------------------------------------------------------------

/**
 * Ionize Menu Controller
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Menu management
 * @author		Ionize Dev Team
 *
 */

class Menu extends MY_admin
{

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->base_model->set_table('menu');
		$this->base_model->set_pk_name('id_menu');
	}


	// ------------------------------------------------------------------------


	/**
	 * Shows the existing menus
	 *
	 */
	function index()
	{

		$menus = $this->base_model->get_list($where = false, $orderby = 'ordering ASC');

		$this->template['menus'] = $menus;

		$this->output('menu');
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves a new menu
	 *
	 */
	function save()
	{
		if( $this->input->post('name_new') != "" && $this->input->post('title_new') != "" )
		{
			$data = array(
						'name' => $this->input->post('name_new'),
						'title' => $this->input->post('title_new')
					);

			// Save to DB
			if ($this->base_model->exists( array( 'name' => $this->input->post('name_new') ) ) )
			{
				$this->base_model->update($this->input->post('name_new'), $data);
			}
			else
			{
				$this->base_model->insert($data);
			}
			
			// UI panel to update after saving
			$this->update = array(
				array(
					'element' => 'mainPanel',
					'url' => admin_url() . 'menu'
				),
				array(
					'element' => 'structurePanel',
					'url' => admin_url() . 'core/get_structure'
				)	
			);
			
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
	 * Updates all the existing menus
	 *
	 */
	function update()
	{
		$menus = $this->base_model->get_list($where = false, $orderby = 'ordering ASC');

		foreach($menus as $menu)
		{
			// Update existing menus
			$data = array(
				'name' =>		$this->input->post('name_'.$menu['id_menu']),
				'title' =>		$this->input->post('title_'.$menu['id_menu'])
			);

			if (($menu['name'] != $data['name']) && $this->base_model->exists( array( 'name' =>  $data['name'] ) ) )
			{
				trace($menu['name']);
				trace($data['name']);
				$this->error(lang('ionize_message_menu_already_exists'));
			}
			
			$this->base_model->update($menu['id_menu'], $data);
			
		}

		// UI update panels
		$this->update = array(
			array(
				'element' => 'mainPanel',
				'url' => admin_url() . 'menu'
			),
			array(
				'element' => 'structurePanel',
				'url' => admin_url() . 'core/get_structure'
			)	
		);

		$this->success(lang('ionize_message_menu_updated'));
	}


	// ------------------------------------------------------------------------


	/** 
	 * Saves ordering
	 * 
	 */
	function save_ordering() {

		if( $order = $this->input->post('order') )
		{
			// Saves the new ordering
			$this->base_model->save_ordering($order);
			
			// UI update panels
			$this->update = array(
				array(
					'element' => 'structurePanel',
					'url' => admin_url() . 'core/get_structure'
				)	
			);

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
	function delete($id)
	{
		$affected_rows = $this->base_model->delete($id);

		if ($affected_rows > 0)
		{
			$this->id = $id;
			
			// UI panel to update after saving
			$this->update = array(
				array(
					'element' => 'mainPanel',
					'url' => admin_url() . 'menu'
				),
				array(
					'element' => 'structurePanel',
					'url' => admin_url() . 'core/get_structure'
				)	
			);
			
			// Answer send
			$this->success(lang('ionize_message_menu_deleted'));
		}
		else
		{
			// Answer send
			$this->error(lang('ionize_message_menu_not_deleted'));			
		}
	}

}


/* End of file menu.php */
/* Location: ./application/controllers/admin/menu.php */