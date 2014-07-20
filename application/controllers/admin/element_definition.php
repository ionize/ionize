<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Element Definition Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.7
 */

class Element_definition extends MY_Admin {

	/*
	 * Type Names
	 *
	 */
/*	public static $type_names = array
	(
		'1' => 'Input',
		'2' => 'Textarea',
		'3' => 'Textarea + Editor',
		'4' => 'Checkbox',
		'5' => 'Radio',
		'6' => 'Select',
		'7' => 'Date & Time',
		'8' => 'Medias',
	);*/



	// ------------------------------------------------------------------------
	
	
	public function __construct()
	{
		parent::__construct();

        // Models
        $this->load->model(
            array(
                'element_model',
                'element_definition_model',
                'extend_field_model'
            ), '', TRUE);
	}


	// ------------------------------------------------------------------------
	
	
	/**
	 * Outputs the Definiton list
	 *
	 */
	function index()
	{
		$this->output('element/definition/index');
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
		
		$html = $this->load->view('element/definition', $this->template, TRUE);

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
		$elements = $this->element_definition_model->get_lang_list(
			array('order_by'=>'ordering ASC'),
			Settings::get_lang('default')
		);

		$elements_lang = $this->element_definition_model->get_all('element_definition_lang');

		// Elements
		foreach($elements as &$element)
		{
			$element['languages'] = array();

			// Translated elements.
			foreach(Settings::get_languages() as $lang)
			{
				$element['languages'][$lang['lang']] = array('title' => '');
				foreach($elements_lang as $ld)
				{
					if ($ld['id_element_definition'] == $element['id_element_definition'] && $ld['lang'] == $lang['lang'])
						$element['languages'][$lang['lang']] = $ld;
				}
			}

			// Element's fields
			$element['fields'] = $this->extend_field_model->get_list(
				array(
					'parent' => 'element',
					'id_parent' => $element['id_element_definition']
				),
				Settings::get_lang('default')
			);
		}

		$this->template['elements'] = $elements;
		
		$this->output('element/definition/list');
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
		// Fields from this element
		$cond = array(
			'parent' => 'element',
			'id_parent' => $id
		);
		$fields = $this->extend_field_model->get_list($cond);

		// Instances of Elements using this definition
		$cond = array('id_element_definition' => $id);
		$elements = $this->element_model->get_elements($cond);

		// No delete if used
		if ( ! empty($fields) OR ! empty($elements))
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
		$elements = $this->element_definition_model->get_lang_list(
			array('name <>' => '', 'order_by' => 'ordering ASC'),
			Settings::get_lang('default')
		);
		
		$this->template['elements'] = '';
		foreach($elements as $key => &$element)
		{
			// Element's fields
			$element['fields'] = $this->extend_field_model->get_list(
				array(
					'parent' => 'element',
					'id_parent' => $element['id_element_definition']
				)
			);

			if (count($element['fields']) == 0)
				unset($elements[$key]);
		}
		
		$data['elements'] = $elements;
		$data['parent'] = $this->input->post('parent');
		$data['id_parent'] = $this->input->post('id_parent');
		
		$element_list = $this->load->view('element/list', $data, TRUE);
		
		echo($element_list);
		die();
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Get the detail of one Element (Add box)
	 *
	 */
	function get_element_detail()
	{
		$id_element = $this->input->post('id_element_definition');

		$element_definition = $this->element_definition_model->get(
			array('id_element_definition' => $id_element),
			Settings::get_lang('default')
		);

		// Element's fields definition
		$fields = $this->extend_field_model->get_list(
			array(
				'parent' => 'element',
				'id_parent' => $id_element,
				'order_by' =>'ordering ASC'
			)
		);

		$this->template['element_definition'] = $element_definition;

		$this->template['fields'] = $fields;
		$lang_fields = array_values(array_filter($fields, create_function('$row', 'return $row["translated"] == 1;')));
		$this->template['lang_fields'] = $lang_fields;

		// Check for langs fields different from
		$has_lang_fields = FALSE;
		foreach($lang_fields as $lf)
		{
			if ($lf['type'] != 8) $has_lang_fields = TRUE;
		}

		// Check for Media type
		$has_media_fields = FALSE;
		foreach ($fields as $f)
			if ($f['type'] == 8) $has_media_fields = TRUE;
		$this->template['has_media_fields'] = $has_media_fields;

		$this->template['has_lang_fields'] = $has_lang_fields;
		$this->template['parent'] = $this->input->post('parent');
		$this->template['id_parent'] = $this->input->post('id_parent');
		
		$this->output('element/detail');
	}
}
