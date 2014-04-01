<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Item Field Controller
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.6
 */

class Item_field extends MY_Admin
{


	public function __construct()
	{
		parent::__construct();

        // Models
        $this->load->model(
            array(
				'item_definition_model',
                'extend_field_model'
            ), '', TRUE);
	}
	
	
	// ------------------------------------------------------------------------
	
	
	function index(){}
	
	
	// ------------------------------------------------------------------------


/*	function create()
	{
		$id_item_definition = $this->input->post('id_item_definition');
		
		$this->extend_field_model->feed_blank_template($this->template);
		$this->extend_field_model->feed_blank_lang_template($this->template, Settings::get_lang('default'));
		
		// Get the parent item : for display
		$item = $this->item_definition_model->get(
			array('id_item_definition' => $id_item_definition),
			Settings::get_lang('default')
		);
		
		$this->template['item'] = $item;
		$this->template['id_item_definition'] = $id_item_definition;
		
		$this->output('item/definition/field');
	}*/


	// ------------------------------------------------------------------------


	/**
	 * Edit one item field
	 *
	 */
	function edit()
	{
		$id_item_definition = $this->input->post('id_item_definition');
		$id_extend_field = $this->input->post('id_extend_field');

		$this->extend_field_model->feed_template($id_extend_field, $this->template);
		$this->extend_field_model->feed_lang_template($id_extend_field, $this->template);

		// Get the parent element
		$item = $this->item_definition_model->get(
			array('id_item_definition' => $this->template['id_parent']),
			Settings::get_lang('default')
		);

		// Pass the parent informations to the template
		$this->template['item'] = $item;
		$this->template['id_item_definition'] = $id_item_definition;

		$this->output('item/definition/field');
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves one extend field definition based on $_POST data
	 *
	 */
	function save()
	{
		if( $this->input->post('name') != '' )
		{
			$id_item_definition = $this->input->post('id_item_definition');

			$exists = $this->extend_field_model->exists(
				array(
					'name' => url_title($this->input->post('name')),
					'parent' => 'item',
					'id_parent' => $id_item_definition
				)
			);

			// If no ID (means new one) and this item name already exists in DB : No save
			if ($this->input->post('id_extend_field') == '' && $exists)
			{
				$this->error(lang('ionize_message_element_field_name_exists'));
			}
			else
			{
				$post = $this->input->post();

                $translated = $this->input->post('translated');
                $post['translated'] = (empty($translated)) ? FALSE : TRUE;

				// Data correction
				$post['parent'] = 'item';
				$post['id_parent'] = $id_item_definition;
				$post['name'] = url_title($post['name']);

				// Save data
				$this->extend_field_model->save($post, $post);

/*				$this->callback = array
				(
					array(
						'fn' => 'ION.HTML',
						'args' => array (
							'item_definition/get_field_list',
							array( 'id_item_definition' => $id_item_definition),
							array ( 'update'=> 'itemFieldsContainer' )
						)
					)
				);*/
				$this->success(lang('ionize_message_item_field_saved'));
			}
		}
		else
		{
			$this->error(lang('ionize_message_item_field_not_saved'));
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Deletes one extend field
	 *
	 * @param	int 	Field ID
	 *
	 */
	function delete($id)
	{
		/*
		 * Check of data use should be implemented
		 *
		 * Minimum : Ask for delete confirmation
		 *
		 *
		if ($this->extend_field_model->exists(array('id_extend_field'=>$id), 'extend_fields'))
		{
			$this->error(lang('ionize_message_item_used_by_data_no_delete'));
		}
		*/
		$extend = $this->extend_field_model->get(array('id_extend_field' => $id));

		$item_definition = $this->item_definition_model->get(array('id_item_definition' => $extend['id_parent']));

		if ( ! empty($extend))
		{
			$this->extend_field_model->delete(array('id_extend_field'=>$id));
			$this->extend_field_model->delete(array('id_extend_field'=>$id), 'extend_field_lang');
			$this->extend_field_model->delete(array('id_extend_field'=>$id), 'extend_fields');

			$this->callback = array
			(
				// Fields list
				array(
					'fn' => 'ION.HTML',
					'args' => array (
						'item_definition/detail',
						array('id_item_definition' => $extend['id_parent']),
						array( 'update'=> 'splitPanel_mainPanel_pad' )
					)
				),
				/*
				// Fields list
				array(
					'fn' => 'ION.HTML',
					'args' => array (
						'item_definition/get_field_list',
						array('id_item_definition' => $extend['id_parent']),
						array( 'update'=> 'itemFieldsContainer' )
					)
				),
				// Instances list : reflect the new fields
				array(
					'fn' => 'ION.HTML',
					'args' => array (
						'item/get_list_from_definition',
						array( 'id_item_definition' => $item_definition['id_item_definition']),
						array ( 'update'=> 'itemInstancesContainer' )
					)
				)*/
			);

			// Send answer
			$this->success(lang('ionize_message_element_field_deleted'));
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
}
