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


	public function get_extends_by_groups()
	{
		$id_content_type = $this->input->post('id_content_type');

		$data = $this->content_type_model->get_extends_by_groups($id_content_type);

		$this->xhr_output($data);
	}


	// ------------------------------------------------------------------------


	public function link_extend_with_group()
	{
		$id_extend_field = $this->input->post('id_extend_field');
		$id_content_type_group = $this->input->post('id_content_type_group');

		$this->content_type_model->link_extend_with_group($id_extend_field, $id_content_type_group);

		$this->response();
	}


	// ------------------------------------------------------------------------


	public function unlink_extend_from_group()
	{
		$id_extend_field = $this->input->post('id_extend_field');
		$id_content_type_group = $this->input->post('id_content_type_group');

		$this->content_type_model->unlink_extend_from_group($id_extend_field, $id_content_type_group);

		$this->response();
	}


	// ------------------------------------------------------------------------


	public function save_extend_ordering()
	{
		$order = $this->input->post('order');
		$id_content_type_group = $this->input->post('id_content_type_group');

		$this->content_type_model->save_extend_ordering($order, $id_content_type_group);

		$this->response();
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
			$this->content_type_model->delete($id_content_type);

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
