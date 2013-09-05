<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Module Admin controller
*
*
*/
class Author extends Module_Admin
{
	/**
	* Constructor
	*
	* @access	public
	* @return	void
	*/
	public function construct()
	{
        // Models
        $this->load->model(
            array(
                'demo_author_model' => 'author_model',
                'page_model'
            ), '', TRUE);
	}


	/**
	 * Outputs the authors list
	 *
	 */
	public function get_list()
	{
		$conds = array(
			'order_by' => 'name ASC'
		);

		$this->template['authors'] = $this->author_model->get_list($conds);

		$this->output('admin/author_list');
	}





	/**
	 * Outputs the detail of one author
	 *
	 * @param	int		ID of the author
	 *
	 */
	public function get($id)
	{
		$where = array(
			'id_author' => $id
		);
		$this->template = $this->author_model->get($where);

		$this->author_model->feed_lang_template($id, $this->template);

		$this->output('admin/author_detail');
	}


	/**
	 * Displays the author form
	 *
	 */
	public function create()
	{
		$this->author_model->feed_blank_template($this->template);
		$this->author_model->feed_blank_lang_template($this->template);

		$this->output('admin/author_detail');
	}


	/**
	 * Saves one author
	 *
	 */
	public function save()
	{
		// The name must be set
		if ($this->input->post('name') != '')
		{
			$id_author = $this->author_model->save($this->input->post());

			// Update the authors list
			$this->update[] = array(
				'element' => 'moduleDemoAuthorsList',
				'url' => admin_url() . 'module/demo/author/get_list'
			);

			// Send the user a message
			$this->success(lang('ionize_message_operation_ok'));
		}
		else
		{
			// Send the user a message
			$this->error(lang('ionize_message_operation_nok'));
		}
	}

	/**
	 * Delete one author
	 *
	 */
	public function delete($id)
	{
		if ($this->author_model->delete($id) > 0)
		{
			// Update the authors list
			$this->update[] = array(
				'element' => 'moduleDemoAuthorsList',
				'url' => admin_url() . 'module/demo/author/get_list'
			);

			// Send the user a message
			$this->success(lang('ionize_message_operation_ok'));
		}
		else
		{
			// Send the user a message
			$this->error(lang('ionize_message_operation_nok'));
		}
	}


	/**
	 * Displays the list of linked authors
	 *
	 */
	public function get_linked_authors()
	{
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');

		$this->template['authors'] = array();
		$this->template['parent'] = $parent;
		$this->template['id_parent'] = $id_parent;

		if ($parent && $id_parent)
		{
			$this->template['authors'] = $this->author_model->get_linked_author($parent, $id_parent);
		}
		$this->output('admin/addons/article/authors');
	}


	/**
	 * Links one Author with one parent
	 *
	 */
	public function add_link()
	{
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');
		$id_author = $this->input->post('id_author');

		if ($this->author_model->link_author_to_parent($parent, $id_parent, $id_author))
		{
			// Set the callbacks
			$this->update_dom_linked_authors($parent, $id_parent, $id_author);

			// Send the user a message
			$this->success(lang('ionize_message_operation_ok'));
		}
		else
		{
			// Send the user a message
			$this->error(lang('module_demo_message_author_already_linked'));
		}
	}

	public function unlink()
	{
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');
		$id_author = $this->input->post('id_author');

		$this->author_model->unlink_author_from_parent($parent, $id_parent, $id_author);

		// Set the callbacks
		$this->update_dom_linked_authors($parent, $id_parent, $id_author);

		// Direct output, without message
		$this->response();
	}



	/**
	 * Send the callback to update the linked authors list
	 *
	 * @param string	Parent code
	 * @param int 		Parent ID
	 *
	 */
	protected function update_dom_linked_authors($parent, $id_parent)
	{
		$this->callback = array(
			array(
				'fn' => 'ION.HTML',
				'args' => array(
					'module/demo/author/get_linked_authors',
					array('parent' => $parent,'id_parent' => $id_parent),
					array('update' => 'demoAuthorsContainer')
				)
			)
		);
	}

}
