<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Tag Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

class Tag extends MY_admin
{
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('tag_model', '', TRUE);
	}


	// ------------------------------------------------------------------------


	/**
	 * Displays the tags panel
	 *
	 */
	public function index()
	{
		$this->output('tag/index');
	}


	// ------------------------------------------------------------------------


	/**
	 * Updates one tag
	 *
	 */
	public function update()
	{
		$id = $this->input->post('id_tag');
		$value = $this->input->post('tag_name');
		$selector = $this->input->post('selector');

		if ($value != '')
		{
			$this->tag_model->update(array('id_tag' => $id), $this->input->post());

			$this->callback = array
			(
				array(
					'fn' => 'ION.notification',
					'args' => array (
						'success',
						lang('ionize_message_operation_ok')
					)
				),
				array(
					'fn' => 'ION.setHTML',
					'args' => array (
						$selector,
						$value
					)
				)
			);

		}

		$this->response();
	}


	// ------------------------------------------------------------------------


	public function add()
	{
		$tag = $this->input->post('tag_name');

		$this->tag_model->save($tag);

		$this->_reload_tag_panel();

		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	public function delete()
	{
		$id = $this->input->post('id');

		$this->tag_model->delete_all($id);

		$this->_reload_tag_panel();

		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 */
	public function save_list()
	{
		$this->tag_model->save_tag_list($this->input->post('tags'));

		$this->_reload_tag_panel();

		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	public function get_list()
	{
		$this->template['tags'] = $this->tag_model->get_list();

		$this->output('tag/list');
	}


	// ------------------------------------------------------------------------


	public function get_json_list()
	{
		$tags = array();
		$data = $this->tag_model->get_list($this->input->post('parent'), $this->input->post('id_parent'));

		// array(id, search_string, Tag displayed string, HTML in Autocomplete)
		foreach($data as $tag)
		{
			$tags[] = array($tag['id_tag'], strtolower($tag['tag_name']), $tag['tag_name'], $tag['tag_name']);
		}

		$this->xhr_output($tags);
	}


	// ------------------------------------------------------------------------


	/**
	 * Reloads the Tags panel
	 *
	 */
	private function _reload_tag_panel()
	{
		$this->reload(
			'mainPanel',
			admin_url(TRUE) . 'tag/index',
			lang('ionize_title_tags')
		);
	}
}
