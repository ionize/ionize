<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.7
 */

// ------------------------------------------------------------------------

/**
 * Ionize Element Field Controller
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Controllers
 * @author		Ionize Dev Team
 */
class Element_field extends MY_Admin 
{
	
	public function __construct()
	{
		parent::__construct();

//		$this->load->model('element_model', '', TRUE);
		$this->load->model('element_definition_model', '', TRUE);
		$this->load->model('extend_field_model', '', TRUE);
	}
	
	
	// ------------------------------------------------------------------------
	
	
	function index()
	{
		//
	}
	
	
	// ------------------------------------------------------------------------


	function create()
	{
		$id_element_definition = $this->input->post('id_element_definition');
		
		$this->extend_field_model->feed_blank_template($this->template);
		
		// Get the parent element
		$element = $this->element_definition_model->get( array('id_element_definition' => $id_element_definition) );
		
		$this->template['element'] = $element;
		$this->template['id_element_definition'] = $id_element_definition;
		
		$this->output('element_field');
	}
	
	
	// ------------------------------------------------------------------------


	/** 
	 * Edit one element field
	 *
	 */
	function edit()
	{
		$id_extend_field = $this->input->post('id_extend_field');

		$this->extend_field_model->feed_template($id_extend_field, $this->template);

		// Get the parent element
		$element = $this->element_definition_model->get( array('id_element_definition' => $this->template['id_element_definition']) );

		// Pass the parent informations to the template
		$this->template['element'] = $element;

		$this->output('element_field');
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
			if ($this->input->post('id_extend_field') == '' && $this->extend_field_model->exists(array('name'=>url_title($this->input->post('name')), 'id_element_definition'=> $this->input->post('id_element_definition'))))
			{
				$this->error(lang('ionize_message_element_field_name_exists'));			
			}
			else
			{
				$this->_prepare_data();
	
				// Save data
				$this->id = $this->extend_field_model->save($this->data);
	
				$this->callback = array
				(
					array(
						'fn' => 'ION.HTML',
						'args' => array (
							'element_definition/get_element_definition_list',
							'',
							array ( 'update'=> 'elementContainer' )
						)	
					)
				);
				$this->success(lang('ionize_message_element_field_saved'));
			}
		}
		else
		{
			$this->error(lang('ionize_message_element_field_not_saved'));			
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
		if ($this->extend_field_model->exists(array('id_extend_field'=>$id), 'extend_fields'))
		{
			$this->error(lang('ionize_message_item_used_by_data_no_delete'));
		}
	*/
		$this->extend_field_model->delete(array('id_extend_field'=>$id));
		
		$this->extend_field_model->delete(array('id_extend_field'=>$id), 'extend_fields');
		
		$this->callback = array
		(
				array(
					'fn' => 'ION.HTML',
					'args' => array (
						'element_definition/get_element_definition_list',
						'',
						array( 'update'=> 'elementContainer' )
					)	
				)
		);
	
		// Answer prepare
		$this->id = $id;

		// Send answer				
		$this->success(lang('ionize_message_element_field_deleted'));
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
	function save_ordering() {

		if( $order = $this->input->post('order') )
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
	}

}

/* End of file element_field.php */
/* Location: ./application/admin/controllers/element_field.php */