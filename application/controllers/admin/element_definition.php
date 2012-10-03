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
 * Ionize Element Definition Controller
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Controllers
 * @author		Ionize Dev Team
 */
class Element_definition extends MY_Admin {

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
	}


	// ------------------------------------------------------------------------
	
	
	/**
	 * Outputs the Definiton list
	 *
	 */
	function index()
	{
		$this->output('element_definitions');
	}
	

	// ------------------------------------------------------------------------
	
	
	/**
	 * Creates one Element Definition 
	 * Used by elements_definition_list view
	 *
	 */
	function create()
	{
		$data = $this->element_definition_model->feed_blank_template();
		$lang_data = $this->element_definition_model->feed_blank_lang_template();

		$data['id_element_definition'] = $this->element_definition_model->save($data, $lang_data);
		$data['fields'] = array();
		
		$this->template = array_merge($data, $lang_data);
		
		$html = $this->load->view('element_definition', $this->template, TRUE);

		$this->callback = array
		(
			array(
				'fn' => 'ION.HTML',
				'args' => array (
					'element_definition/get_element_definition_list',
					'',
					array
					(
						'update'=> 'elementContainer'
					)
				)	
			)
		);
		
		$this->response();
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Returns the element definition list
	 * Used to admin elements (elements_definition_list and element_definition views)
	 *
	 * XHR call
	 *
	 */
	function get_element_definition_list()
	{
		$elements = $this->element_definition_model->get_lang_list(array('order_by'=>'ordering ASC'), Settings::get_lang('default'));
		$elements_lang = $this->element_definition_model->get_lang();

		// Elements
		foreach($elements as &$element)
		{
			// Translated elements.
			$langs = array_values(array_filter($elements_lang, create_function('$row','return $row["id_element_definition"] == "'. $element['id_element_definition'] .'";')));
			
			foreach(Settings::get_languages() as $lang)
			{
				$element[$lang['lang']] = array_pop(array_filter($langs, create_function('$row','return $row["lang"] == "'. $lang['lang'] .'";')));
			}

			// Element's fields
			$element['fields'] = $this->extend_field_model->get_lang_list(array('id_element_definition' => $element['id_element_definition']), Settings::get_lang('default'));
			
			// Name of the field type ("checkbox", "input", ...)
			foreach($element['fields'] as &$field)
			{
				$field['type_name'] = self::$type_names[$field['type']];
			}
		}

		$this->template['elements'] = $elements;
		
		$this->output('element_definition_list');
	}
	

	// ------------------------------------------------------------------------
	
	
	/**
	 * Returns the list of definition for a given parent
	 * Used to build the tabs of elements definitions in parents panels (page, article)
	 *
	 * @returns String	JSON object of all definitions containing elements
	 *
	 */
	function get_definitions_from_parent()
	{
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');

		$definitions = $this->element_definition_model->get_definitions_from_parent($parent, $id_parent);

		$this->xhr_output(array_values($definitions));
		
	}


	// ------------------------------------------------------------------------
	
	
	/**
	 * Deletes one Element Definition
	 *
	 */
	function delete($id)
	{
		$cond = array('id_element_definition' => $id);
	
		// Fields from this element
		$fields = $this->extend_field_model->get_list($cond);

		// Instances of Elements using this definition
		$elements = $this->element_model->get_elements($cond);

		// No delete if used
		if ( ! empty($fields) OR  !empty($elements))
		{
			$this->error(lang('ionize_message_element_in_use'));			
		}
		// Delete
		else
		{
			$this->element_definition_model->delete($id);
			
			// Reload Elements definitions list
			$this->callback = array
			(
				array(
					'fn' => 'ION.HTML',
					'args' => array (
						'element_definition/get_element_definition_list',
						'',
						array
						(
							'update'=> 'elementContainer'
						)
					)	
				)
			);
			
			$this->success(lang('ionize_message_operation_ok'));
		}
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Changes the name of a content element definition
	 * XHR call
	 *
	 * @return 	Mixed	Success message
	 *
	 */
	function save_field()
	{
		$field = $this->input->post('field');
		$value = url_title($this->input->post('value'));
		$id = $this->input->post('id');
		$selector = $this->input->post('selector');
		
		if ($field && $value && $value != '')
		{
			$where = array('id_element_definition' => $id);
			$data = array($field => $value);

			// Check for name : must be unique !
			if ($field == 'name')
			{
				$element = $this->element_definition_model->get($data);
				if ( ! empty($element) && $element['id_element_definition'] != $id)
				{
					$this->callback = array
					(
						array(
							'fn' => 'ION.notification',
							'args' => array (
								'error',
								lang('ionize_message_element_definition_name_already_exists')
							)	
						),
					);
					
					$this->response();
				}
			}
			
			$id = $this->element_definition_model->update($where, $data);
			
			if ($id !== FALSE)
			{
				$this->callback = array
				(
					array(
						'fn' => 'ION.notification',
						'args' => array (
							'success',
							lang('ionize_message_content_element_saved')
						)	
					),
					array(
						'fn' => 'ION.HTML',
						'args' => array (
							'element_definition/get_element_definition_list',
							'',
							array
							(
								'update'=> 'elementContainer'
							)
						)	
					)
				);
			}
			else
			{
				$this->callback = array
				(
					array(
						'fn' => 'ION.notification',
						'args' => array (
							'error',
							lang('ionize_message_content_element_not_saved')
						)	
					)
				);
			
			}

			$this->response();
		}
	}
	
	
	/**
	 * Save title of a content element definition
	 * XHR call
	 *
	 * @return 	Mixed	Success message
	 *
	 */
	function save_lang_field()
	{
		$field = $this->input->post('field');
		$value = $this->input->post('value');
		$lang = $this->input->post('lang');
		$id = $this->input->post('id');
		$selector = $this->input->post('selector');

		if ($field && $value && $value != '')
		{
			$data = array('id_element_definition' => $id);
			
			$lang_data = array(	$lang => array($field => $value) );

			$id = $this->element_definition_model->save($data, $lang_data);

			if ($id !== FALSE)
			{
				$this->callback = array
				(
					array(
						'fn' => 'ION.notification',
						'args' => array (
							'success',
							lang('ionize_message_content_element_saved')
						)	
					),
					array(
						'fn' => 'ION.setHTML',
						'args' => array (
							$selector,
							$value
						)	
					)
					/*
					array(
						'fn' => 'ION.HTML',
						'args' => array (
							'element_definition/get_element_definition_list',
							'',
							array
							(
								'update'=> 'elementContainer'
							)
						)	
					)
					*/
				);
			}
			else
			{
				$this->callback = array
				(
					array(
						'fn' => 'ION.notification',
						'args' => array (
							'error',
							lang('ionize_message_content_element_not_saved')
						)	
					)
				);
			
			}
			$this->response();
		}
	}


	// ------------------------------------------------------------------------
	
	
	/**
	 * Saves the Element definition order
	 *
	 */
	function save_ordering()
	{
		$order = $this->input->post('order');
		
		if( $order !== FALSE )
		{
			// Saves the new ordering
			$this->element_definition_model->save_ordering($order);
			
			// Answer send
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
	 * Get the Elements list for adding an element to a parent
	 * 
	 * Called by view : element_add
	 *
	 */
	function get_element_list()
	{
		// Elements
		$elements = $this->element_definition_model->get_lang_list(array('name <>' => '', 'order_by' => 'ordering ASC'), Settings::get_lang() );
		
		$this->template['elements'] = '';
		foreach($elements as $key => &$element)
		{
			// Element's fields
			$element['fields'] = $this->extend_field_model->get_list(array('id_element_definition' => $element['id_element_definition']));
			
			foreach($element['fields'] as &$field)
			{
				$field['type_name'] = self::$type_names[$field['type']];
		                $field['label'] = $this->extend_field_model->get_label($field['id_extend_field']);
			}

			if (count($element['fields']) == 0)
				unset($elements[$key]);
		}
		
		$data['elements'] = $elements;
		$data['parent'] = $this->input->post('parent');
		$data['id_parent'] = $this->input->post('id_parent');
		
		$element_list = $this->load->view('element_list', $data, TRUE);
		
		echo($element_list);
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Get the detail of one Element (Add box)
	 *
	 */
	function get_element_detail()
	{
		$id_element = $this->input->post('id_element_definition');

		$element_definition = $this->element_definition_model->get(array('id_element_definition' => $id_element), Settings::get_lang('default') );
		
		// Element's fields
		$fields = $this->extend_field_model->get_lang_list(array('id_element_definition' => $id_element, 'order_by' =>'ordering ASC'), Settings::get_lang('default'));

		$fields_lang = $this->extend_field_model->get_lang();

			
		foreach($fields as &$field)
		{
			// Add the type name ("checkbox", etc.)
			$field['type_name'] = self::$type_names[$field['type']];

			foreach(Settings::get_languages() as $lang)
			{
				$langs = array_values(array_filter($fields_lang, create_function('$row','return $row["id_extend_field"] == "'. $field['id_extend_field'] .'";')));
				$field['langs'][$lang['lang']] = array_pop(array_filter($langs, create_function('$row','return $row["lang"] == "'. $lang['lang'] .'";')));
			}

		}

		$this->template['element_definition'] = $element_definition;
		$this->template['fields'] = array_values(array_filter($fields, create_function('$row', 'return $row["translated"] == 0;')));
		$this->template['lang_fields'] = array_values(array_filter($fields, create_function('$row', 'return $row["translated"] == 1;')));
		
		$this->template['parent'] = $this->input->post('parent');
		$this->template['id_parent'] = $this->input->post('id_parent');
		
		$this->output('element_detail');
	}
}

/* End of file element_definition.php */
/* Location: ./application/admin/controllers/element_definition.php */