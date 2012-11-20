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
 * Ionize Element Controller
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Controllers
 * @author		Ionize Dev Team
 */
class Element extends MY_Admin {

	/*
	 * Type Names
	 *
	 */
	public static $type_names = array
	(
		'1' => 'Input',
		'2' => 'Textarea',
		'3' => 'Textarea + Editor',
		'4' => 'Checkbox',
		'5' => 'Radio',
		'6' => 'Select',
		'7' => 'Date & Time'
	);



	// ------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		$this->load->model('element_model', '', TRUE);
		$this->load->model('element_definition_model', '', TRUE);
		$this->load->model('extend_field_model', '', TRUE);
		$this->load->helper('text_helper');
	}


	// ------------------------------------------------------------------------


	function index()
	{
		// Do nothing.
	}
	

	// ------------------------------------------------------------------------

	
	/**
	 * Returns the elements list based on the given element definition and parent.
	 * called by ION.getContentElements
	 *
	 */
	function get_elements_from_definition()
	{
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');
		$id_element_definition = $this->input->post('id_element_definition');
		
		$this->template['definition'] = $this->element_model->get_fields_from_parent($parent, $id_parent, Settings::get_lang('default'), $id_element_definition);
		$this->template['parent'] = $parent;
		$this->template['id_parent'] = $id_parent;
		
		$this->output('element_content_list');
	}
	

	// ------------------------------------------------------------------------

	
	/**
	 * Deletes one content element
	 *
	 */
	function delete($id_element)
	{
		$element = $this->element_model->get($id_element);
		
		if ( ! empty($element))
		{
			// Delete the element	
			$affected_rows = $this->element_model->delete($id_element);
			
			if ($affected_rows > 0)
			{
				$this->id = $id_element;
			
				// Reload Elements definitions list
				$this->callback = array
				(
					array(
						'fn' => 'ION.updateContentTabs',
						'args' => array
						(
							$element['parent'],
							$element['id_parent']
						)
					),
				);


				// Deletes the tab if the element defintion has no elements
				// Not implemented yet...
				/*
				// Check if the element definition has some elements...
				$elements = $this->element_model->get_elements(array('id_element_definition' => $element['id_element_definition'], 'parent' => $element['parent'], 'id_parent' => $element['id_parent']) );

				if ( empty($elements))
				{
					array_push(
						$this->callback,
						array(
							'fn' => 'ION.deleteTab',
							'args' => $element['id_element_definition']
						)
					);
				}
				*/
				
				$this->success(lang('ionize_message_operation_ok'));					
			}
		}
	
		$this->error(lang('ionize_message_operation_nok'));					
	}
	
	
	// ------------------------------------------------------------------------
	
	
	function save_ordering($parent, $id_parent)
	{
		$order = $this->input->post('order');
		
		if( $order !== FALSE )
		{
			// Clear the cache
			Cache()->clear_cache();

			// Saves the new ordering
			$this->element_model->save_ordering($order);

			$this->callback = array
			(
				array(
					'fn' => 'ION.updateContentTabs',
					'args' => array
					(
						$parent,
						$id_parent
					)
				),
			);

			// Answer
			$this->success(lang('ionize_message_element_ordered'));
		}
		else 
		{
			// Answer send
			$this->error(lang('ionize_message_operation_nok'));
		}
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Saves an element instance to a parent
	 *
	 */
	function save()
	{
		$id_element = $this->input->post('id_element');
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');

		if (!empty($parent) && !empty($id_parent))
		{
			// Clear the cache
			Cache()->clear_cache();

			$id_element_definition = $this->input->post('id_element_definition');

			// $element_definition = $this->element_definition_model->get(array('id_element_definition' => $id_element_definition) );
			
			// Save Element and extend fields
			$this->element_model->save($parent, $id_parent, $id_element, $id_element_definition, $_POST);
			
			// Get Elements
			$this->callback = array
			(
				array(
					'fn' => 'ION.updateContentTabs',
					'args' => array
					(
						$parent,
						$id_parent
					)
				),
			);
			
			$this->response();
		}
		else
		{
			$this->callback = array
			(
				array(
					'fn' => 'ION.notification',
					'args' => array
					(
						'error',
						lang('ionize_message_element_cannot_be_added_to_parent')
					)
				),
			);
		}
		$this->response();
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Edits an instance of element to a parent
	 *
	 */
	function edit()
	{
		$id_element = $this->input->post('id_element');
		
		// Element
		$element = $this->element_model->get(array('id_element' => $id_element) );

		// Element definition
		$element_definition = $this->element_definition_model->get(array('id_element_definition' => $element['id_element_definition']), Settings::get_lang('default') );

		// Element's fields
		$element_fields = $this->element_model->get_element_fields($id_element);

		$this->template['element'] = $element;
		$this->template['element_definition'] = $element_definition;
		$this->template['fields'] = array_values(array_filter($element_fields, create_function('$row', 'return $row["translated"] == 0;')));
		$this->template['lang_fields'] = array_values(array_filter($element_fields, create_function('$row', 'return $row["translated"] == 1;')));

		$this->template['parent'] = $element['parent'];
		$this->template['id_parent'] = $element['id_parent'];
		$this->template['ordering'] = $element['ordering'];
		$this->template['id_element'] = $id_element;
		
		$this->output('element_detail');

	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Display the Element list container
	 *
	 */
	function add_element()
	{
		$this->template['parent'] = $this->input->post('parent');
		$this->template['parent'] = $this->input->post('parent');
		$this->template['id_parent'] = $this->input->post('id_parent');

		$this->output('element_add');
	}
	

	// ------------------------------------------------------------------------


	function link_element()
	{
		$result = FALSE;

		$id_element = $this->input->post('id_element');
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');
		$old_parent = $this->input->post('old_parent');
		$old_id_parent = $this->input->post('old_id_parent');
		$copy = $this->input->post('copy');					// TRUE / FALSE : is the element copied from one block to another ?

		$where = array(
			'id_element' => $id_element,
			'parent' => $parent,
			'id_parent' => $id_parent
		);
		
		$message = lang('ionize_message_element_moved');
		
		// Copy
		if ($copy !== FALSE)
		{
			$result = $this->element_model->copy($where);
			$message = lang('ionize_message_element_copied');
		}
		// Move
		else
		{
			$result = $this->element_model->move($where);	
		}
		
		
		if ($result !== FALSE)
		{
			$this->callback = array
			(
				array(
					'fn' => 'ION.getContentElements',
					'args' => array
					(
						$old_parent, $old_id_parent
					)
				),
				array(
					'fn' => 'ION.notification',
					'args' => array
					(
						'success',
						$message
					)
				)
			);
		}

		$this->response();
	
	}


	// ------------------------------------------------------------------------

	
}

/* End of file element.php */
/* Location: ./application/admin/controllers/element.php */