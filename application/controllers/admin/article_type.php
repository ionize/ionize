<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Article Type Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

class Article_type extends MY_admin 
{
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('article_type_model', '', TRUE);
	}


	// ------------------------------------------------------------------------


	/**
	 * Displays the existing types list
	 *
	 */
	function index()
	{
		$this->output('type/article_panel');
	}


	// ------------------------------------------------------------------------


	/**
	 * Prints out the type list and form
	 * called by edition form window
	 *
	 * @param	string	parent. Element from which we edit the type list
	 * @param	string	parent ID
	 *
	 */
	function get_form($parent = FALSE, $id_parent = FALSE)
	{
		$this->article_type_model->feed_blank_template($this->template);
		
		// Pass the parent informations to the template
		$this->template['parent'] = $parent;
		$this->template['id_parent'] = $id_parent;
		
		$this->template['types'] = $this->article_type_model->get_list();

		$this->output('type/article');
	}


	// ------------------------------------------------------------------------


	function get_list()
	{
		// Feed new type form with blank data.
		$this->article_type_model->feed_blank_template($this->template);

		// Types list
		$this->template['types'] = $this->article_type_model->get_list(array('order_by' => 'ordering ASC'));

		$this->output('type/article_list');
	}

	// ------------------------------------------------------------------------	

	
	/**
	 * Get the select box of types
	 *
	 * @param	string	parent type. Can be 'article', 'page', etc.
	 * @param	string	parent ID. 	 
	 *
	 * @return string	HTML types select box
	 *
	 */
	function get_select($parent = FALSE, $id_parent = FALSE)
	{
		$this->load->model('article_model', '', TRUE);

		// Get data formed to feed the category select box
		$types = $this->article_type_model->get_types_select();
		
		// Get the current categories for the element
		$current_type = FALSE;
		
		if ($parent && $id_parent)
		{
			$article = $this->article_model->get_by_id($id_parent);
	
			if (!empty($article))
				$current_type = $article['id_type'];
		}
		
		// Outputs the categories form dropdown
		$this->xhr_output(form_dropdown('id_type', $types, $current_type, 'class="select"'));
	}

	
	// ------------------------------------------------------------------------


	/** 
	 * Edit one type
	 *
	 * @param	int		Category ID
	 * @param	string	parent. Element from which we edit the categories list
	 * @param	string	parent ID
	 *
	 */
	function edit($id, $parent = FALSE, $id_parent = FALSE)
	{

		$this->article_type_model->feed_template($id, $this->template);

		// Pass the parent informations to the template
		$this->template['parent'] = $parent;
		$this->template['id_parent'] = $id_parent;
		
		$this->template['types'] = $this->article_type_model->get_list();

		$this->output('type/article');
	}

	
	// ------------------------------------------------------------------------


	/**
	 * Saves one category
	 *
	 */
	function save()
	{
		if( $this->input->post('type') != '' ) {

			// If no ID (means new one) and this item already exists in DB : No save
			if ($this->input->post('id_type') == '' && $this->article_type_model->exists(array('type'=>url_title($this->input->post('type')))))
			{
				$this->error(lang('ionize_message_type_exists'));			
			}
			else
			{
				$this->_prepare_data();
	
				// Save data
				$this->id = $this->article_type_model->save($this->data);

				// Get data for answer
//				$data = $this->article_type_model->get($this->id);
	
				/*
				 * JSON Update array
				 * If parent is defined in form, the categories selectbox of the parent will be updated
				 *
				 */
				if ($this->input->post('parent') !='')
				{
					$this->update[] = array(
						'element' => 'article_types',
						'url' => 'admin/article_type/get_select/'.$this->input->post('parent').'/'.$this->input->post('id_parent')
					);
				}
				
				$this->callback = array(
					array(
						'fn' => 'ION.HTML',
						'args' => array('article_type/get_list', '', array('update' => 'articleTypesContainer'))
					),
					array(
						'fn' => 'ION.clearFormInput',
						'args' => array('form' => 'newTypeForm')
					)
				);
	
				$this->success(lang('ionize_message_article_type_saved'));
			}
		}
		else
		{
			$this->error(lang('ionize_message_article_type_not_saved'));			
		}
	}

		
	// ------------------------------------------------------------------------


	/**
	 * Deletes one type
	 *
	 * @param      $id				Type ID
	 * @param bool|string $parent	Parent table name. optional
	 * @param bool|int $id_parent	Parent ID. Optional
	 */
	function delete($id, $parent = FALSE, $id_parent = FALSE)
	{
		if ($id && $id != '')
		{
			if ($this->article_type_model->delete($id) > 0)
			{
				// Update all article and set id_type to NULL
				$this->article_type_model->update_article_after_delete($id);
				
				// Update array
				$this->update[] = array(
					'element' => 'article_types',
					'url' => admin_url() . 'article_type/get_select/' . $parent . '/' . $id_parent
				);
				
				// Remove deleted items from DOM
				$this->callback[] = array(
					'fn' => 'ION.deleteDomElements',
					'args' => array('.article_type' . $id)
				);
				
				// Answer prepare
				$this->id = $id;
				
				// Send answer				
				$this->success(lang('ionize_message_article_type_deleted'));
			}
			else
			{
				$this->error(lang('ionize_message_article_type_not_deleted'));
			}
		}
	}


	// ------------------------------------------------------------------------


	/** 
	 * Saves article types ordering
	 * 
	 */
	function save_ordering($parent = FALSE, $id_parent = FALSE)
	{
		$order = $this->input->post('order');
		
		if( $order !== FALSE )
		{
			// Saves the new ordering
			$this->article_type_model->save_ordering($order);
			
			// Update Array for JSON
			$this->update[] = array(
				'element' => 'article_types',
				'url' => admin_url() . 'article_type/get_select/' . $parent . '/' . $id_parent
			);
			
			// Answer
			$this->success(lang('ionize_message_operation_ok'));
		}
		else 
		{
			$this->error(lang('ionize_message_operation_nok'));
		}
	}

	
	// ------------------------------------------------------------------------


	/** 
	 * Prepare data before saving
	 *
	 */
	function _prepare_data($xhr = FALSE)
	{
		// Standard fields
		$fields = $this->db->list_fields('article_type');
		
		// Set the data to the posted value.
		foreach ($fields as $field)
			$this->data[$field] = $this->input->post($field);

		// Some safe !
		$this->data['type'] = url_title($this->data['type']);
	}
}
