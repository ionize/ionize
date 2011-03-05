<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Extend Fields Controller
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Extend fields management
 * @author		Ionize Dev Team
 *
 * Extends the data model by adding personal fields.
 * These fields definition are stored in the table "extend_field"
 *
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


	/**
	 * Index
	 *
	 */
	function index()
	{
		$this->output('extend_fields');
		
	}


	// ------------------------------------------------------------------------


	/**
	 * Prints out the empty extend field form
	 * called by edition form window
	 *
	 * @param	string	parent. Element from which we edit the categories list
	 * @param	string	parent ID
	 *
	 */
	function get_form($parent = FALSE, $id_parent = FALSE)
	{
		$this->extend_field_model->feed_blank_template($this->template);
		
		// Pass the parent informations to the template
		$this->template['parent'] = $parent;
		$this->template['id_parent'] = $id_parent;
		
		$this->output('extend_field');
	}

	
	// ------------------------------------------------------------------------


	/** 
	 * Edit one extend field
	 *
	 * @param	int		extend field ID
	 * @param	string	parent. Element from which we edit the categories list
	 *
	 */
	function edit($id, $parent = FALSE, $id_parent = FALSE)
	{
		$this->extend_field_model->feed_template($id, $this->template);

		// Pass the parent informations to the template
		$this->template['parent'] = $parent;
		$this->template['id_parent'] = $id_parent;
		
		$this->output('extend_field');
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all the extend fields for one kind of parent
	 *
	 * @param	String		Parent type. Can be 'article', 'page', etc.
	 * @return 	Array		Array of extend fields
	 *
	 */
	function get_extend_fields($parent = FALSE)
	{
		// Get data formed to feed the category select box
		$where = array(
			'parent' => $parent
		);
		
		// Returns the extends list ordered by 'ordering' 
		return $this->extend_field_model->get_list($where, 'ordering ASC');
	}

	
	// ------------------------------------------------------------------------


	/**
	 * Outputs the extend fields table
	 *
	 * @param 		String		Page type. Can be 'page, 'article', etc...
	 * @returns		String		HTML table of extended fields
	 *							See /themes/admin/extend_fields_table.php for output view
	 */
	function get_element_extend_fields_table($parent = FALSE)
	{
		if ($parent !== FALSE)
		{
			$this->template['extend_fields'] = $this->get_extend_fields($parent);
			$this->template['parent'] = $parent;
			
	    	$this->output('extend_fields_table');
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
			if ($this->input->post('id_extend_field') == '' && $this->extend_field_model->exists(array('name'=>url_title($this->input->post('name')), 'parent'=> $this->input->post('parent'))))
			{
				$this->error(lang('ionize_message_extend_field_name_exists'));			
			}
			else
			{
				$this->_prepare_data();
	
				// Save data
				$this->id = $this->extend_field_model->save($this->data);
	
				/*
				 * JSON Update array
				 * If parent is defined in form, the categories selectbox of the parent will be updated
				 *
				 */
				if ($this->input->post('parent') !='')
				{
					$this->update[] = array(
						'element' => 'extend_fields_'.$this->input->post('parent'),
						'url' =>  'extend_field/get_element_extend_fields_table/'.$this->input->post('parent')
					);
				}
				
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
	 * Deletes one extend field
	 *
	 * @param	int 	Category ID
	 * @param	string 	Parent table name. optional
	 * @param	int 	Parent ID. Optional
	 */
	function delete($id, $parent=false)
	{
		if ($id && $id != '')
		{
			if ($this->extend_field_model->delete($id) > 0)
			{
				// Delete all the extend fields objects from cutom_fields table
				$this->extend_field_model->delete_extend_fields($id);
				
				// Update array
				if ( $parent !== false)
				{
					$this->update[] = array(
						'element' => 'extend_fields_table',
						'url' =>  'extend_field/get_element_extend_fields_table/'.$parent
					);
				}
			
				// Answer prepare
				$this->id = $id;

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


/* End of file extend_field.php */
/* Location: ./application/controllers/admin/extend_field.php */