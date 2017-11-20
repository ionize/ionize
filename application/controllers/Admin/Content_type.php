<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Content Type Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.6
 */

class Content_type extends MY_admin
{
	/** @var  Content_type_model */
	public $content_type_model;

	/** @var  Extend_field_model */
	public $extend_field_model;

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
				'content_type_model',
				'content_type_group_model',
				'extend_field_model'
			),
			'',
			TRUE
		);
	}


	// ------------------------------------------------------------------------


	public function get_list()
	{
		$items = $this->content_type_model->get_list(array('order_by' => 'name ASC'));

		$this->xhr_output($items);
	}


	// ------------------------------------------------------------------------


	public function get_groups_with_items()
	{
		$id_content_type = $this->input->post('id_content_type');

		$data = $this->content_type_model->get_groups_with_items($id_content_type);

		$this->xhr_output($data);
	}



	// ------------------------------------------------------------------------


	public function link_item_with_group()
	{
		$item = $this->input->post('item');
		$id_item = $this->input->post('id_item');
		$id_content_type_group = $this->input->post('id_content_type_group');

		$this->content_type_model->link_item_with_group($item, $id_item, $id_content_type_group);

		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	public function unlink_item_from_group()
	{
		$item = $this->input->post('item');
		$id_item = $this->input->post('id_item');
		$id_content_type_group = $this->input->post('id_content_type_group');

		$this->content_type_model->unlink_item_from_group($item, $id_item, $id_content_type_group);

		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	public function save_item_ordering()
	{
		$order = $this->input->post('order');
		$item = $this->input->post('item');
		$id_content_type_group = $this->input->post('id_content_type_group');

		$this->content_type_model->save_item_ordering($order, $item, $id_content_type_group);

		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	/**
	 * Save
	 *
	 */
	public function save()
	{
		$post = $this->input->post();

		try
		{
			$id_type = $this->content_type_model->save($post);

			$type = $this->content_type_model->get(array('id_content_type' => $id_type));

			$this->success(lang('ionize_message_operation_ok'), array('type' => $type));
		}
		catch(Exception $e)
		{
			$this->error($e->getMessage());
		}
	}


	// ------------------------------------------------------------------------


	public function delete()
	{
		$id_content_type = $this->input->post('id_content_type');

		try
		{
			$this->content_type_model->delete_content_type($id_content_type);

			$this->success(lang('ionize_message_operation_ok'));
		}
		catch(Exception $e)
		{
			$this->error($e->getMessage());
		}
	}


	// ------------------------------------------------------------------------


	public function update_name()
	{
		$id_content_type = $this->input->post('id_content_type');
		$name = $this->input->post('name');

		try
		{
			$this->content_type_model->update_name($id_content_type, $name);

			$this->response();
		}
		catch(Exception $e)
		{
			$this->error($e->getMessage());
		}
	}


	// ------------------------------------------------------------------------


	public function update_field()
	{
		$id_content_type = $this->input->post('id_content_type');
		$field = $this->input->post('field');
		$value = $this->input->post('value');

		try
		{
			$this->content_type_model->update_field($id_content_type, $field, $value);

			$this->response();
		}
		catch(Exception $e)
		{
			$this->error($e->getMessage());
		}
	}


	// ------------------------------------------------------------------------


	public function delete_group()
	{
		$id_content_type_group = $this->input->post('id_content_type_group');

		try
		{
			$this->content_type_model->delete_group($id_content_type_group);

			$this->success(lang('ionize_message_operation_ok'));
		}
		catch(Exception $e)
		{
			$this->error($e->getMessage());
		}
	}


	// ------------------------------------------------------------------------


	public function add_group()
	{
		$id_content_type = $this->input->post('id_content_type');
		$name = $this->input->post('name');

		try
		{
			$this->content_type_model->add_group($id_content_type, $name);

			$this->success(lang('ionize_message_operation_ok'));
		}
		catch(Exception $e)
		{
			$this->error($e->getMessage());
		}
	}


	// ------------------------------------------------------------------------


	public function update_group()
	{
		$id_content_type_group = $this->input->post('id_content_type_group');
		$field = $this->input->post('field');
		$value = $this->input->post('value');

		try
		{
			$this->content_type_model->update_group($id_content_type_group, $field, $value);
		}
		catch(Exception $e)
		{
			$this->error($e->getMessage());
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves Group ordering
	 *
	 */
	function save_group_ordering()
	{
		$order = $this->input->post('order');

		if( $order !== FALSE )
		{
			// Clear the cache
			Cache()->clear_cache();

			// Saves the new ordering
			$this->content_type_group_model->save_ordering($order);

			// Answer
			$this->success(lang('ionize_message_element_ordered'));
		}
		else
		{
			// Answer send
			$this->error(lang('ionize_message_operation_nok'));
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns one content type's fields list
	 *
	 *
	 */
	function get_field_list()
	{
		$fields = array();


		$this->xhr_output($fields);
	}


	// ------------------------------------------------------------------------


	/**
	 * Must be called by XHR
	 *
	 * Returns 1 if true, 0 if false
	 *
	 */
	function check_exists()
	{
		$name = url_title($this->input->post('name'));

		$exists = $this->content_type_model->check_exists(
			'name',
			$name,
			$this->input->post('id_content_type')
		);

		$this->xhr_output($exists);
	}
}
