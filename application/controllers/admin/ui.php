<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * UI Builder
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

class Ui extends MY_Admin
{

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('ui_model', '', TRUE);
	}


	/**
	 * Return elements of a given type of one company's panel
	 * Ex : Tabs of 'contact' panel for the company 3
	 *
	 */
	public function get_panel_elements()
	{
		$id_company = $this->input->post('id_company');
		$panel = $this->input->post('panel');
		$type = $this->input->post('type') ?  $this->input->post('type') : NULL;

		$data = $this->ui_model->get_panel_elements($id_company, $panel, $type);

		$this->xhr_output($data);
	}


	/**
	 * Add one element of the given type to one panel
	 *
	 * @todo : Go further and remove the "one type" limitation by adding "type parents" concept.
	 *
	 */
	public function add_element()
	{
		$id_company = $this->input->post('id_company');
		$panel = $this->input->post('panel');
		$type = $this->input->post('type') ? $this->input->post('type') : NULL;
		$title = $this->input->post('title') ? $this->input->post('title') : '';
		$ordering = $this->input->post('ordering') || NULL;

		$id = $this->ui_model->create_element($id_company, $panel, $type, $title, $ordering);

		$element = $this->ui_model->get($id);

		$this->xhr_output($element);
	}


	public function update_element()
	{
		$post = $this->input->post();
		$id_ui_element = $this->input->post('id_ui_element');

		$this->ui_model->update($id_ui_element, $post);

		$this->success(lang('ionize_message_operation_ok'));
	}


	public function delete_element()
	{
		$id_ui_element = $this->input->post('id_ui_element');

		$this->ui_model->delete_element($id_ui_element);

		$this->success(lang('ionize_message_operation_ok'));
	}


	public function link_field_to_element()
	{
		$post = $this->input->post();

		$id_extend = $this->input->post('id_extend');
		$id_ui_element = $this->input->post('id_ui_element');

		$this->ui_model->link_extend_to_element($id_extend, $id_ui_element);

		$this->success(lang('ionize_message_operation_ok'));
	}


	public function unlink_field_from_element()
	{
		$id_extend = $this->input->post('id_extend');
		$id_ui_element = $this->input->post('id_ui_element');

		$this->ui_model->unlink_extend_from_element($id_extend, $id_ui_element);

		$this->success(lang('ionize_message_operation_ok'));
	}


	/**
	 * Returns one UI element linked extends
	 *
	 */
	public function get_element_fields()
	{
		$id_ui_element = $this->input->post('id_ui_element');

		$fields = $this->ui_model->get_element_fields($id_ui_element);

		$this->xhr_output($fields);
	}


	public function save_element_fields_ordering()
	{
		$id_ui_element = $this->input->post('id_ui_element');
		$order = $this->input->post('order');

		$this->ui_model->save_element_fields_ordering($id_ui_element, $order);

		$this->response();
	}





	public function save_ordering()
	{
		$order = $this->input->post('order');

		$this->ui_model->save_ordering($order);

		$this->response();
	}

}
