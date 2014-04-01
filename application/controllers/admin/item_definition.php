<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Item Definition Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.6
 */

class Item_definition extends MY_admin
{
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
				'item_definition_model',
				'extend_field_model'
			),
			'',
			TRUE
		);
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 */
	public function index()
	{
		$this->output('item/definition/index');
	}


	// ------------------------------------------------------------------------


	public function get()
	{
		$id_definition = $this->input->post('id_definition');

		$definition = array();

		if ($id_definition)
		{
			$definition = $this->item_definition_model->get(
				array($this->item_definition_model->get_pk_name() => $id_definition),
				Settings::get_lang('default')
			);
		}

		$this->xhr_output($definition);
	}


	// ------------------------------------------------------------------------


	public function get_list($mode=NULL)
	{
		$items = $this->item_definition_model->get_lang_list(
			array(
				'order_by' => 'title_definition ASC'
			),
			Settings::get_lang('default')
		);

		if ($mode == 'json')
		{
			$this->xhr_output($items);
		}
		else
		{
			$this->template['items'] = $items;

			$this->output('item/definition/list');
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Creation Form
	 * Basic definition data
	 *
	 */
	public function edit()
	{
		$id_definition = $this->input->post('id_item_definition');

		$definition = array();
		$this->item_definition_model->feed_blank_template($definition);
		$this->item_definition_model->feed_blank_lang_template($definition);

		// Existing
		if ($id_definition)
		{
			$definition = $this->item_definition_model->get(array(
				$this->item_definition_model->get_pk_name() => $id_definition)
			);

			$this->item_definition_model->feed_lang_template($id_definition, $definition);
		}

		$this->template['definition'] = $definition;

		$this->output('item/definition/edit');
	}


	// ------------------------------------------------------------------------


	/**
	 * Save
	 *
	 */
	public function save()
	{
		$post = $this->input->post();

		// Save data
		$this->item_definition_model->save($post, $post);

		// Reload List
		$this->_reload_definition_list();

		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	public function delete()
	{
		$id_item_definition = $this->input->post('id_item_definition');

		$this->item_definition_model->delete($id_item_definition);

		// Reload List
		$this->_reload_definition_list();

		// Back to Welcome
		$this->_reload_welcome();

		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	/**
	 * Edit the definition
	 * Displays the items list (main panel)
	 *
	 */
	public function detail()
	{
		$id_definition = $this->input->post('id_item_definition');

		// Default lang data : No Edit here
		$definition = $this->item_definition_model->get(
			array($this->item_definition_model->get_pk_name() => $id_definition),
			Settings::get_lang('default')
		);

		$this->template['definition'] = $definition;

		$this->output('item/definition/detail');
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns one definition fields list
	 *
	 *
	 */
	function get_field_list()
	{
		$fields = array();

		if (Authority::can('edit', 'admin/item/definition'))
		{
			$id_definition = $this->input->post('id_item_definition');

			$fields = $this->extend_field_model->get_lang_list(
				array(
					'parent' => 'item',
					'id_parent' => $id_definition
				),
				Settings::get_lang('default')
			);
		}

		//
		$this->template['id_item_definition'] = $id_definition;
		$this->template['fields'] = $fields;

		$this->output('item/definition/fields');
	}


	// ------------------------------------------------------------------------


	/**
	 * Must be called by XHR
	 * Called by definition Edition form Validation
	 *
	 * Returns 1 if true, 0 if false
	 *
	 */
	function check_exists()
	{
		$name = url_title($this->input->post('name'));

		$exists = $this->item_definition_model->check_exists(
			'name',
			$name,
			$this->input->post('id_item_definition')
		);

		$this->xhr_output($exists);
	}


	// ------------------------------------------------------------------------


	/**
	 * Reload definition list
	 *
	 */
	private function _reload_definition_list()
	{
		$this->callback[] =  array(
			'fn' => 'ION.HTML',
			'args' => array(
				'item_definition/get_list',
				'',
				array('update' => 'splitPanel_definition_pad')
			)
		);
	}


	// ------------------------------------------------------------------------


	/**
	 * Reload the Static Items Welcome screen
	 *
	 */
	private function _reload_welcome()
	{
		$this->callback[] =  array(
			'fn' => 'ION.HTML',
			'args' => array(
				'item/welcome',
				'',
				array('update' => 'splitPanel_mainPanel_pad')
			)
		);
	}
}
