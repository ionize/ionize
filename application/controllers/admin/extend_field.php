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

		// Models
		$this->load->model(
			array(
				'extend_field_model',
				'extend_field_type_model',
			),
			'',
			TRUE
		);
	}


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


	public function get_extend_types($format=NULL)
	{
		$extend_types = $this->extend_field_type_model->get_list(
			array('active' => 1)
		);

		if ($format == 'json')
		{
			$this->xhr_output($extend_types);
		}
		else
		{
			// ... nothing
		}
	}


	// ------------------------------------------------------------------------


	function create()
	{
		// Pre-defined parent : No parent select in this case
		$parent = $this->input->post('parent');

		$this->extend_field_model->feed_blank_template($this->template);
		$this->extend_field_model->feed_blank_lang_template($this->template);
		
		// Limit to one parent type ?
		$this->template['limit_to_parent'] = FALSE;
		if ($parent)
		{
			$this->template['limit_to_parent'] = $parent;
			$this->template['id_parent'] = $this->input->post('id_parent');
		}

		// Available parents
		$parents = $this->extend_field_model->get_parents();
		$this->template['parents'] = $parents;

		// Types Select
		$extend_types_select = $this->extend_field_type_model->get_form_select(
			'type_name',
			array('active' => 1)
		);

		$this->template['type_select'] = form_dropdown(
			'type',
			$extend_types_select,
			$this->template['type'],
			'id=type'.$this->template['id_extend_field'].' class="select"'
		);

		// Extend Types details
		$this->template['extend_types'] = json_encode($this->extend_field_type_model->get_list(), TRUE);
		
		$this->output('extend/field');
	}

	
	// ------------------------------------------------------------------------


	/** 
	 * Edit one extend field
	 *
	 */
	function edit()
	{
		$id = $this->input->post('id_extend_field');

		// Pre-defined parent : No parent select in this case
		$parent = $this->input->post('parent');

		if ($id)
		{
			$this->extend_field_model->feed_template($id, $this->template);
			$this->extend_field_model->feed_lang_template($id, $this->template);
		}
		else
		{
			$this->extend_field_model->feed_blank_template($this->template);
			$this->extend_field_model->feed_blank_lang_template($this->template);
		}

		// Limit to one parent type ?
		$this->template['limit_to_parent'] = FALSE;
		if ($parent)
		{
			$this->template['limit_to_parent'] = $parent;
		}

		// Available parents
		$parents = $this->extend_field_model->get_parents();
		$this->template['parents'] = $parents;

		// Types
		$extend_types_select = $this->extend_field_type_model->get_form_select('type_name');

		$this->template['type_select'] = form_dropdown(
			'type',
			$extend_types_select,
			$this->template['type'],
			'id=type'.$this->template['id_extend_field'].' class="select"'
		);

		// Extend Types details
		$this->template['extend_types'] = json_encode($this->extend_field_type_model->get_list());

		$this->output('extend/field');
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all the extend fields (definitions) for one kind of parent
	 * Used by Admin panel to display the extend fields list
	 * Called by XHR by admin/views/extend/index.php
	 *
	 * @param	String		Parent type. Can be 'article', 'page', etc.
	 *
	 */
	function get_extend_fields()
	{
		$mode = $this->input->post('mode');

		// Get data formed to feed the category select box
		$where = array(
			'parent !=' => 'element'
		);
		
		$order_by = $this->input->post('order_by');
		if ($order_by)
			$where['order_by'] = $order_by;

		// Limit to one parent type : Useful for limited lists
		$parent = $this->input->post('parent');
		if ( $parent) $where['parent'] = $parent;

		// Limit to several parents
		$parents = $this->input->post('parents');
		if ($parents)
		{
			$parents = explode(',', $parents);
			$where['where_in'] = array('parent' => $parents);
		}

		// Returns the extends list ordered by 'ordering' 
		$extend_fields = $this->extend_field_model->get_lang_list($where, Settings::get_lang('default'));

		if ($mode == 'json')
		{
			$this->xhr_output($extend_fields);
		}
		else
		{
			// Get the parents
			$parents = array();
			foreach($extend_fields as $extend)
			{
				if ( ! in_array($extend['parent'], $parents))
					$parents[] = $extend['parent'];
			}
	
			$this->template['parent'] = $parent ? $parent : FALSE;
			$this->template['parents'] = $parents;
			$this->template['extend_fields'] = $extend_fields;
			
	    	$this->output('extend/list');
		}
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
			if ($this->input->post('id_extend_field') == '')
			{
				$where = array(
					'name'=>url_title($this->input->post('name')),
					'parent'=> $this->input->post('parent')
				);
				if ($this->input->post('id_parent'))
					$where['id_parent'] = $this->input->post('id_parent');

				if ($this->extend_field_model->exists($where))
					$this->error(lang('ionize_message_extend_field_name_exists'));
			}

			$this->_prepare_data();

			// Save data
			$this->id = $this->extend_field_model->save($this->data, $this->lang_data);

			$this->update[] = array(
				'element' => 'extend_fields',
				'url' =>  'extend_field/get_extend_fields'
			);

			$this->success(lang('ionize_message_extend_field_saved'));
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
	 * Get Extend Definitions in the context of one parent
	 *
	 * @example		Get the Extend available for the parent 'contact' and
	 * 				linked to the context 'page with ID 3'
	 *
	 * @param 		null 		$mode. Return format.
	 *
	 * @receives	context		Parent Context. Ex : Page, Article, Company
	 * 				id_context	Parent Context ID
	 * 				parent		Extend Parent type. Ex : Page, Article, Contact
	 *
	 */
	public function get_context_list($mode=NULL)
	{
		$context = $this->input->post('context');
		$id_context = $this->input->post('id_context');
		$parent = $this->input->post('parent');

		$items = $this->extend_field_model->get_context_list($context, $id_context, $parent);

		if ($mode == 'json')
		{
			$this->xhr_output($items);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Get Extend Definitions linked to one parent type
	 *
	 */
	public function get_parent_list()
	{
		$parent = $this->input->post('parent');

		$items = $this->extend_field_model->get_list(array('parent' => $parent));

		$this->xhr_output($items);
	}


	// ------------------------------------------------------------------------


	public function get_extend_instance($mode=NULL)
	{
		$id_extend = $this->input->post('id_extend');
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');
		$lang = $this->input->post('lang');

		$extend = $this->extend_field_model->get_element_extend_field($id_extend, $parent, $id_parent, $lang);

		if ($mode == 'json')
		{
			$this->xhr_output($extend);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Get Parent's extends instances
	 *
	 * @param null $mode
	 */
	public function get_instances_list($mode=NULL)
	{
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');
		$id_field_parent = $this->input->post('id_field_parent');

		$items = $this->extend_field_model->get_element_extend_fields($parent, $id_parent, $id_field_parent);

		if ($mode == 'json')
		{
			$this->xhr_output($items);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Get Context's extends instances
	 *
	 * @param null $mode
	 */
	public function get_context_instances_list($mode=NULL)
	{
		$context = $this->input->post('context');
		$id_context = $this->input->post('id_context');
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');

		$items = $this->extend_field_model->get_context_instances_list($context, $id_context, $parent, $id_parent);

		if ($mode == 'json')
		{
			$this->xhr_output($items);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Links one extend field to one logical parent element
	 * (ex : page, article, company)
	 * This extend field will then only be available in the context of its parent element
	 *
	 */
	public function link_to_context()
	{
		$id_extend_field = $this->input->post('id_extend_field');
		$context = $this->input->post('context');
		$id_context = $this->input->post('id_context');

		$this->extend_field_model->link_to_context($id_extend_field, $context, $id_context);

		// Send answer
		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	/**
	 * UnLinks one extend field from his logical parent element context
	 */
	public function unlink_from_context()
	{
		$id_extend_field = $this->input->post('id_extend_field');
		$context = $this->input->post('context');
		$id_context = $this->input->post('id_context');

		$this->extend_field_model->unlink_from_context($id_extend_field, $context, $id_context);

		// Send answer
		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------
	// Extend List management
	// ------------------------------------------------------------------------


	public function remove_value_from_extend_field()
	{
		$value = $this->input->post('value');
		$id_extend = $this->input->post('id_extend');
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');
		$lang = $this->input->post('lang') ? $this->input->post('lang') : NULL;

		$this->extend_field_model->delete_value_from_extend_field(
			$id_extend,
			$parent,
			$id_parent,
			$value,
			$lang
		);

		$this->response();
	}


	// ------------------------------------------------------------------------


	public function get_extend_link_list($mode=NULL)
	{
		$id_extend = $this->input->post('id_extend');
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');
		$lang = $this->input->post('lang') ? $this->input->post('lang') : NULL;

		$items = $this->extend_field_model->get_extend_link_list($id_extend, $parent, $id_parent, $lang);

		if ($mode == 'json')
		{
			$this->xhr_output($items);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Add one value to one "List type" Extend.
	 * Used by Link
	 *
	 */
	public function add_value_to_extend_field()
	{
		$value = $this->input->post('value');
		$id_extend = $this->input->post('id_extend');
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');
		$lang = $this->input->post('lang') ? $this->input->post('lang') : NULL;

		if ($id_extend && $parent && $id_parent)
		{
			$this->extend_field_model->add_value_to_extend_field(
				$id_extend,
				$parent,
				$id_parent,
				$value,
				$lang
			);
		}
		else
		{
			log_message('error', print_r(get_class($this) . '->add_value_to_extend_field() : Some value missing ($id_extend, $parent, $id_parent)', TRUE));
		}

		$this->response();
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves media order for one parent
	 *
	 */
	public function save_extend_ordering()
	{
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');
		$id_extend = $this->input->post('id_extend');
		$lang = $this->input->post('lang');

		$value = $this->input->post('order');

		if( $value !== FALSE )
		{
			$this->extend_field_model->save_extend_field_value($id_extend, $parent, $id_parent, $value, $lang);

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
		$this->data = $this->input->post();

		// Some safe !
		$this->data['name'] = url_title($this->data['name']);
		$this->data['translated'] = $this->input->post('translated');

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
}
