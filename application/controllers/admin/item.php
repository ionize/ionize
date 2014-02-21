<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Item Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.6
 */

class Item extends MY_admin
{
	/**
	 * Type names
	 * @var array
	 */
	private static $_TYPE_NAMES = array
	(
		'1' => 'Input',
		'2' => 'Textarea',
		'3' => 'Textarea + Editor',
		'4' => 'Checkbox',
		'5' => 'Radio',
		'6' => 'Select',
		'7' => 'Date & Time',
		'8' => 'Medias',
	);

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
				'item_model',
				'item_definition_model',
				'extend_field_model'
			), '', TRUE
		);

		// Helper
		$this->load->helper('text_helper');
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 */
	public function index()
	{
		$this->output('item/index');
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 */
	public function welcome()
	{
		$this->output('item/welcome');
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all definitions with their items
	 * XHR output
	 *
	 * View is full built with JS
	 *
	 */
	public function get_definitions_with_items()
	{
		$items = $this->item_model->get_definitions_with_items();

		$this->xhr_output($items);
	}


	// ------------------------------------------------------------------------


	public function get_parent_item_list()
	{
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');

		$items = $this->item_model->get_parent_item_list($parent, $id_parent, $lang=NULL);

		$this->xhr_output($items);
	}


	// ------------------------------------------------------------------------


	/**
	 * Add item windows
	 * Purpose : Create one item (backend)
	 * At this state, the item isn't added
	 *
	 */
	public function add_item()
	{
		$id_definition = $this->input->post('id_item_definition');

		$item_definition = $this->item_definition_model->get(
			array('id_item_definition' => $id_definition),
			Settings::get_lang('default')
		);

		// Element's fields
		$fields = $this->extend_field_model->get_lang_list(
			array(
				'parent' => 'item',
				'id_parent' => $id_definition,
				'order_by' =>'ordering ASC'
			),
			Settings::get_lang('default')
		);

		$fields_lang = $this->extend_field_model->get_lang();

		foreach($fields as &$field)
		{
			// Add the type name ("checkbox", etc.)
			$field['type_name'] = self::$_TYPE_NAMES[$field['type']];

			foreach(Settings::get_languages() as $lang)
			{
				$langs = array_values(array_filter($fields_lang, create_function('$row','return $row["id_extend_field"] == "'. $field['id_extend_field'] .'";')));
				$field['langs'][$lang['lang']] = array_pop(array_filter($langs, create_function('$row','return $row["lang"] == "'. $lang['lang'] .'";')));
			}
		}

		$this->template['item_definition'] = $item_definition;
		$this->template['fields'] = $fields;

		$lang_fields = array_values(array_filter($fields, create_function('$row', 'return $row["translated"] == 1;')));
		$this->template['lang_fields'] = $lang_fields;

		// Check for langs fields different from
		$has_lang_fields = FALSE;
		foreach($lang_fields as $lf)
			if ($lf['type'] != 8) $has_lang_fields = TRUE;
		$this->template['has_lang_fields'] = $has_lang_fields;

		// Check for Media type
		$has_media_fields = FALSE;
		foreach ($fields as $f)
			if ($f['type'] == 8) $has_media_fields = TRUE;
		$this->template['has_media_fields'] = $has_media_fields;

		$this->output('item/instance/edit');
	}


	// ------------------------------------------------------------------------


	/**
	 * Edit one Item
	 *
	 */
	public function edit()
	{
		$id_item = $this->input->post('id_item');

		// Item
		$item = $this->item_model->get(array('id_item' => $id_item) );

		// Element definition
		$item_definition = $this->item_definition_model->get(
			array('id_item_definition' => $item['id_item_definition']),
			Settings::get_lang('default')
		);

		// Element's fields instances
		$item_fields = $this->item_model->get_item_fields($id_item);

		$this->template['item'] = $item;
		$this->template['item_definition'] = $item_definition;
		$this->template['fields'] = $item_fields;

		$lang_fields = array_values(array_filter($item_fields, create_function('$row', 'return $row["translated"] == 1;')));
		$this->template['lang_fields'] = $lang_fields;

		// Check for langs fields different from
		$has_lang_fields = FALSE;
		foreach($lang_fields as $lf)
			if ($lf['type'] != 8) $has_lang_fields = TRUE;
		$this->template['has_lang_fields'] = $has_lang_fields;

		// Check for Media type
		$has_media_fields = FALSE;
		foreach ($item_fields as $f)
			if ($f['type'] == 8) $has_media_fields = TRUE;
		$this->template['has_media_fields'] = $has_media_fields;


		$this->template['ordering'] = $item['ordering'];
		$this->template['id_item'] = $id_item;

		$this->output('item/instance/edit');
	}


	// ------------------------------------------------------------------------


	/**
	 * Save one item instance
	 *
	 */
	public function save()
	{
		$id_item_definition = $this->input->post('id_item_definition');

		$posted_id_item = $this->input->post('id_item');

		// Save Element and extend fields
		$id_item = $this->item_model->save($id_item_definition, $_POST);

		$this->callback = array
		(
			// Refresh Instances Container (Static Items main panel)
			array(
				'fn' => 'ION.HTML',
				'args' => array (
					'item/get_list_from_definition',
					array( 'id_item_definition' => $id_item_definition),
					array ( 'update'=> 'itemInstancesContainer' )
				)
			),
			// Refresh List (Link Window, in content)
			// @todo : manage these events directly from JS
			array(
				'fn' => 'staticItemManager.getItemListContent'
			),
			array(
				'fn' => 'staticItemManager.getParentItemList'
			),
		);

		// Re-open the window
		if ($this->input->post('reload') && ! $posted_id_item)
		{
			$this->callback[] = array(
				'fn' => 'ION.formWindow',
				'args' => array (
					'item' . $id_item,
					'itemForm' . $id_item,
					'ionize_title_edit_item',
					'item/edit',
					array( 'width' => 600, 'height'=>350),
					array ( 'id_item'=> $id_item )
				)
			);
		}

		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	public function delete($id_item)
	{
		$item = $this->item_model->get($id_item);

		if ( ! empty($item))
		{
			// Delete the element
			$affected_rows = $this->item_model->delete($id_item);

			if ($affected_rows > 0)
			{
				// Reload Elements definitions list
				$this->callback = array
				(
					array(
						'fn' => 'ION.HTML',
						'args' => array (
							'item/get_list_from_definition',
							array( 'id_item_definition' => $item['id_item_definition']),
							array ( 'update'=> 'itemInstancesContainer' )
						)
					)
				);
				$this->success(lang('ionize_message_operation_ok'));
			}
		}

		$this->error(lang('ionize_message_operation_nok'));

	}


	// ------------------------------------------------------------------------


	/**
	 * Get items which belongs to one item definition
	 *
	 */
	public function get_list_from_definition()
	{
		$id_item_definition = $this->input->post('id_item_definition');

		// Items, with extend fields content
		$items = $this->item_model->get_list_from_definition(
			$id_item_definition,
			Settings::get_lang('default')
		);

		$this->template['items'] = $items;
		$this->template['id_item_definition'] = $id_item_definition;

		$this->output('item/definition/item/list');
	}


	// ------------------------------------------------------------------------


	public function link_to_parent()
	{
		$id_item = $this->input->post('id_item');
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');

		if ($id_item && $parent && $id_parent)
			$this->item_model->link_to_parent($id_item, $parent, $id_parent);

		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	public function unlink_from_parent()
	{
		$id_item = $this->input->post('id_item');
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');

		if ($id_item && $parent && $id_parent)
			$this->item_model->unlink_from_parent($id_item, $parent, $id_parent);

		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	public function order_for_parent()
	{
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');
		$order = $this->input->post('order');

		$this->item_model->order_for_parent($parent, $id_parent, $order);

		$this->success(lang('ionize_message_operation_ok'));
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
			$this->item_model->save_ordering($order);

			// Answer
			$this->success(lang('ionize_message_operation_ok'));
		}
		else
		{
			$this->error(lang('ionize_message_operation_nok'));
		}
	}

}
