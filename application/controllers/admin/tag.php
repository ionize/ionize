<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize, creative CMS Article Controller
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Tag
 * @author		Ionize Dev Team
 *
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

	/*
	public function create()
	{
		$this->tag_model->feed_blank_template($this->template);
		// $this->tag_model->feed_blank_lang_template($this->template);

		$this->template['categories'] = $this->category_model->get_list(array('order_by'=>'ordering ASC'));

		$this->output('tag/tag');

	}
	*/


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
		// Categories list
		$this->template['tags'] = $this->tag_model->get_list(array('order_by'=>'tag ASC'));

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
		$this->callback[] = array(
			'fn' => 'ION.contentUpdate',
			'args' => array(
				'element' => 'mainPanel',
				'url' => 'tag/index',
				'title' => lang('ionize_title_tags'),
			)
		);
	}
}
