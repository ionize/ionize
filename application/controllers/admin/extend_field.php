<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Extend Fields Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

class Extend_field extends MY_admin
{
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('extend_field_model', '', TRUE);
	}


	// ------------------------------------------------------------------------
	// Extend definition methods
	// ------------------------------------------------------------------------


	/**
	 * Index
	 *
	 */
	function index()
	{
		$this->output('extend/index');
	}


	// ------------------------------------------------------------------------


	/**
	 * Prints out the empty extend field form
	 * called by edition form window
	 *
	 * @param	mixed	parent. Element from which we edit the categories list
	 * @param	mixed	parent ID
	 *
	 */
	function get_form($parent = FALSE, $id_parent = FALSE)
	{
		$this->extend_field_model->feed_blank_template($this->template);
		$this->extend_field_model->feed_blank_lang_template($this->template);
		
		// Pass the parent informations to the template
		$this->template['parent'] = $parent;
		$this->template['id_parent'] = $id_parent;
		
		$this->output('extend/field');
	}

	
	// ------------------------------------------------------------------------


	/** 
	 * Edit one extend field
	 *
	 * @param	int		extend field ID
	 * @param	mixed	parent. Element from which we edit the categories list
	 * @param	mixed	id_parent. Element ID
	 *
	 */
	function edit($id, $parent = FALSE, $id_parent = FALSE)
	{
		$this->extend_field_model->feed_template($id, $this->template);
		$this->extend_field_model->feed_lang_template($id, $this->template);

		$this->output('extend/field');
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all the extend fields for one kind of parent
	 * Used by Admin panel to display the extend fields list
	 * Called by XHR by views/extend_fields.php
	 *
	 * @param	String		Parent type. Can be 'article', 'page', etc.
	 * @return 	Array		Array of extend fields
	 *
	 */
	function get_extend_fields($parent = NULL)
	{
		// Get data formed to feed the category select box
		$where = array(
			'order_by' => 'ordering ASC',
			'id_element_definition' => '0'
		);
		
		if ( ! is_null($parent))
		{
			$where['parent'] = $parent;
		}
		$where['where_in'] = array('parent'=> array('article','page','media'));

		// Returns the extends list ordered by 'ordering' 
		$extend_fields = $this->extend_field_model->get_lang_list($where, Settings::get_lang('default'));

		// Get the parents
		$parents = array();
		foreach($extend_fields as $extend)
		{
			if ( ! in_array($extend['parent'], $parents))
				$parents[] = $extend['parent'];
		}

		$this->template['parent'] = ( ! is_null($parent)) ? $parent : FALSE;
		$this->template['parents'] = $parents;
		$this->template['extend_fields'] = $extend_fields;
		
    	$this->output('extend/list');
	}


	// ------------------------------------------------------------------------


	public function set_main()
	{
		$id_extend = $this->input->post('id_extend_field');
		$extend = $this->extend_field_model->get(array('id_extend_field' => $id_extend));

		if ( ! empty($extend) && ! empty($extend['parent']) && ! empty($extend['id_parent']))
		{
			$where = array(
				'parent' => $extend['parent'],
				'id_parent' => $extend['id_parent']
			);

			// Set all 'main' values to 0
			$this->extend_field_model->update($where, array('main' => 0));

			$data = array(
				'id_extend_field' => $id_extend,
				'main' => 1,
			);

			$id = $this->extend_field_model->save($data);

			if ($id)
				$this->success(lang('ionize_message_extend_field_saved'));
			else
				$this->error(lang('ionize_message_extend_field_not_saved'));
		}
		else
		{
			$this->error(lang('ionize_message_extend_field_not_found'));
		}
	}

	// ------------------------------------------------------------------------


	/**
	 * Saves one extend field definition based on $_POST data
	 *
	 */
	function save()
	{
		if( $this->input->post('name') != '' ) {

			// If no ID (means new one) and this item name already exists in DB : No save
			if (
				$this->input->post('id_extend_field') == ''
				&& $this->extend_field_model->exists(
					array('name'=>url_title($this->input->post('name')), 'parent'=> $this->input->post('parent'))
				)
			)
			{
				$this->error(lang('ionize_message_extend_field_name_exists'));			
			}
			else
			{
				$this->_prepare_data();
	
				// Save data
				$this->id = $this->extend_field_model->save($this->data, $this->lang_data);
	
				$this->update[] = array(
					'element' => 'extend_fields',
					'url' =>  'extend_field/get_extend_fields'
				);

				$this->success(lang('ionize_message_extend_field_saved'));
			}
		}
		else
		{
			$this->error(lang('ionize_message_extend_field_not_saved'));			
		}
	}

		
	// ------------------------------------------------------------------------


	/**
	 * Deletes one extend field definition
	 * @table : extend_field
	 *
	 * @param	int 	Category ID
	 * @param	string 	Parent table name. optional
	 * @param	int 	Parent ID. Optional
	 */
	function delete($id)
	{
		if ($id && $id != '')
		{
			if ($this->extend_field_model->delete($id) > 0)
			{
				// Extend Field lang table
				$this->extend_field_model->delete(array('id_extend_field'=>$id), 'extend_field_lang');

				// Delete all the extend fields objects from extend_fields table
				$this->extend_field_model->delete_extend_fields($id);
				
				// Update array
				$this->update[] = array(
					'element' => 'extend_fields',
					'url' =>  'extend_field/get_extend_fields'
				);
			
				// Send answer				
				$this->success(lang('ionize_message_extend_field_deleted'));
			}
			else
			{
				$this->error(lang('ionize_message_extend_field_not_deleted'));
			}
		}
	}
	
	
	// ------------------------------------------------------------------------


	/** 
	 * Saves extending fields ordering
	 * 
	 * @param	String		Parent type
	 *
	 * @return	String		Success or error message
	 * 
	 */
	function save_ordering()
	{
		$order = $this->input->post('order');
		
		if( $order !== FALSE )
		{
			// Saves the new ordering
			$this->extend_field_model->save_ordering($order);
			
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
	function _prepare_data() 
	{
		// Standard fields
		$fields = $this->db->list_fields('extend_field');
		
		// Set the data to the posted value.
		foreach ($fields as $field)
			$this->data[$field] = $this->input->post($field);

		// Some safe !
		$this->data['name'] = url_title($this->data['name']);
		
		// Lang data
		$this->lang_data = array();

		$fields = $this->db->list_fields('extend_field_lang');

		foreach(Settings::get_languages() as $language)
		{
			foreach ($fields as $field)
			{
				if ($this->input->post($field.'_'.$language['lang']) !== false)
				{
					$content = $this->input->post($field.'_'.$language['lang']);
					
					$this->lang_data[$language['lang']][$field] = $content;
				}
			}
		}
	}


	// ------------------------------------------------------------------------
	// Extend instances methods
	// ------------------------------------------------------------------------


}
