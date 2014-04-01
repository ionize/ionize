<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.6
 */

/**
 * Ionize Item Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Item
 * @author		Ionize Dev Team
 *
 */
class Item_model extends Base_model
{
	/**
	 * Link table between items and parents
	 * @var string
	 */
	private static $_LK_TABLE = 'items';

	private static $_ITEMS = 'items';

	private static $_EXTEND_FIELDS = 'extend_fields';

	private static $_DEFINITION = 'item_definition';
	private static $_DEFINITION_LANG = 'item_definition_lang';

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('item');
		$this->set_pk_name('id_item');
		$this->set_lang_table('item_lang');
	}


	// ------------------------------------------------------------------------


	public function get_item($id_item)
	{
		$this->{$this->db_group}->join(
			self::$_DEFINITION,
			self::$_DEFINITION.'.id_item_definition = ' .$this->get_table().'.id_item_definition',
			'inner'
		);

		$this->{$this->db_group}->join(
			self::$_DEFINITION_LANG,
			self::$_DEFINITION_LANG.'.id_item_definition = ' .self::$_DEFINITION.'.id_item_definition'
			. " and lang='" .Settings::get_lang('default') . "'",
			'inner'
		);

		$data = $this->get_row_array(array('id_item' => $id_item));

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all definition with all nested items
	 * Each nested item has the array of its fields
	 *
	 * @param null $lang
	 *
	 * @return mixed
	 */
	public function get_definitions_with_items($lang=NULL)
	{
		self::$ci->load->model('item_definition_model', '', NULL);
		self::$ci->load->model('extend_field_model');
		self::$ci->load->model('extend_fields_model');

		if ( is_null($lang)) $lang = Settings::get_lang('default');

		// 1. Get definitions
		$definitions = self::$ci->item_definition_model->get_lang_list(array(),	$lang );

		// 2. Get items instances
		//	  Fantastic : The fields values are added to each item with the prefix "ion_" !
		$items = $this->get_lang_list(
			array('order_by' => 'ordering ASC'),
			$lang
		);

		// 3. Get fields
		$fields = self::$ci->extend_fields_model->get_detailled_lang_list(array('parent' => 'item'), $lang);

		// 3. Group data
		foreach ($definitions as &$definition)
		{
			$definition['items'] = array();

			foreach($items as $item)
			{
				if ($item['id_item_definition'] == $definition['id_item_definition'])
				{
					$item['fields'] = array();

					foreach($fields as $field)
					{
						if ($field['id_parent'] == $item['id_item'])
							$item['fields'][] = $field;
					}
					$definition['items'][] = $item;
				}
			}
		}

		return $definitions;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all items linked to one parent
	 * grouped by definition
	 *
	 * @param      $parent
	 * @param      $id_parent
	 * @param null $lang
	 * @param null $id_item_definition
	 *
	 * @return mixed
	 */
	public function get_parent_item_list($parent, $id_parent, $lang=NULL, $id_item_definition=NULL)
	{
		self::$ci->load->model('item_definition_model', '', NULL);
		self::$ci->load->model('extend_field_model');
		self::$ci->load->model('extend_fields_model');

		if ( is_null($lang)) $lang = Settings::get_lang('default');

		// 1. Get definitions
		$where = array();
		if ( ! is_null($id_item_definition))
			$where['id_item_definition'] = $id_item_definition;

		$definitions = self::$ci->item_definition_model->get_lang_list(array(),	$lang );


		// 2. Get Items instances linked to parent
		$this->{$this->db_group}->join(
			self::$_ITEMS,
			self::$_ITEMS.'.id_item = ' .$this->get_table().'.id_item',
			'inner'
		);

		$this->{$this->db_group}->where(array('parent' => $parent, 'id_parent' => $id_parent));

		$items = $this->get_lang_list(
			array('order_by' => self::$_ITEMS.'.ordering ASC'),
			$lang
		);

		// 3. Get all fields of all concerned item instance
		$sql = "
			select
				extend_fields.*,
				extend_field.name,
				extend_field.type,
				extend_field.description,
				extend_field.ordering,
				extend_field.value as default_value,
				extend_field.main
			from
				extend_fields
				inner join extend_field on extend_field.id_extend_field = extend_fields.id_extend_field
			where
				extend_fields.parent = 'item' and
 				extend_fields.id_parent in (
					select id_item from " . self::$_ITEMS . "
					where parent = '" . $parent . "'
					and id_parent = " . $id_parent . "
				)
				and (lang='' OR lang is null OR lang='".$lang."')
			";

		$query = $this->{$this->db_group}->query($sql);

		$fields = array();
		if ( $query->num_rows() > 0) $fields = $query->result_array();
		$query->free_result();

		// 4. Group data
		foreach ($definitions as &$definition)
		{
			$definition['items'] = array();

			foreach($items as $item)
			{
				if ($item['id_item_definition'] == $definition['id_item_definition'])
				{
					$item['fields'] = array();

					foreach($fields as $field)
					{
						if ($field['id_parent'] == $item['id_item'])
							$item['fields'][] = $field;
					}
					$definition['items'][] = $item;
				}
			}
		}

		return $definitions;
	}


	// ------------------------------------------------------------------------


	/**
	 * For one given item, returns all its fields values
	 * Used by backend to edit one item
	 *
	 * @param $id_item
	 *
	 * @return mixed
	 */
	public function get_item_fields($id_item)
	{
		self::$ci->load->model('extend_field_model');
		self::$ci->load->model('extend_fields_model');

		// Item : just for after
		$item = $this->get(array('id_item' => $id_item) );

		// Definitions Fields : Extend Field
		$definitions_fields = self::$ci->extend_field_model->get_list(
			array(
				'parent' => 'item',
				'id_parent' => $item['id_item_definition']
			)
		);

		// Fields
		$fields = self::$ci->extend_fields_model->get_detailled_lang_list(
			array(
				'parent' => 'item',
				'id_parent' => $id_item
			)
		);

		// Feed each field with content for the element fields
		$langs = Settings::get_languages();

		foreach($definitions_fields as &$df)
		{
			if ($df['translated'] == '1')
			{
				foreach($langs as $language)
				{
					$df[$language['lang']]['content'] = '';
				}
			}

			foreach($fields as $row)
			{
				if ($row['id_extend_field'] == $df['id_extend_field'])
				{
					$df = array_merge($df, $row);

					if ($df['translated'] == '1')
					{
						foreach($langs as $language)
						{
							$lang_code = $language['lang'];

							if($row['lang'] == $lang_code)
							{
								$df[$lang_code]['content'] = $row['content'];
							}
						}
					}
				}
			}
		}

		return $definitions_fields;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get item definitions with nested fields
	 *
	 * @param $id_item_definition
	 * @param $lang
	 *
	 * @return array
	 */
	public function get_list_from_definition($id_item_definition, $lang)
	{
		self::$ci->load->model('item_definition_model');
		self::$ci->load->model('extend_field_model');

		// Definitions Fields : Extend Field Types
		$definitions_fields = self::$ci->extend_field_model->get_lang_list(
			array(
				'parent' => 'item',
				'id_parent' => $id_item_definition
			),
			$lang
		);

		// Items Instances
		$items = $this->get_lang_list(
			array(
				'id_item_definition' => $id_item_definition,
				'order_by' => 'ordering ASC'
			),
			$lang
		);

		// Fields
		$sql = "
			select
				extend_fields.*
			from
				extend_fields
			where
				extend_fields.parent = 'item' and
 				extend_fields.id_parent in (
					select id_item from item
					where id_item_definition= ".$id_item_definition."
				)
			";
		$query = $this->{$this->db_group}->query($sql);

		$fields = array();
		if ( $query->num_rows() > 0) $fields = $query->result_array();
		$query->free_result();

		// Extend Fields DB fields
		$extend_fields_fields = $this->{$this->db_group}->list_fields('extend_fields');

		// Languages
		$langs = Settings::get_languages();

		// Feed each item with its fields
		foreach($items as &$item)
		{
			$item['fields'] = array();

			foreach($definitions_fields as $df)
			{
				// Create one empty field for each definition extend field, to have data, even empty
				$el = array_merge(array_fill_keys($extend_fields_fields, NULL), $df);
				$el['lang_data'] = array();

				foreach($fields as $row)
				{
					if ($row['id_parent'] == $item['id_item'] && $row['id_extend_field'] == $df['id_extend_field'])
					{
						if ($df['translated'] == '1')
						{
							foreach($langs as $language)
							{
								$lang_code = $language['lang'];

								if($row['lang'] == $lang_code)
								{
									if ($lang_code == $lang)
										$el = array_merge($el, $row);

									$el['lang_data'][$lang_code] = $row;
								}
							}
						}
						else
						{
							$el = array_merge($el, $row);
							$el['data'] = $row;
						}
					}
				}
				$item['fields'][$df['name']] = $el;
			}
		}

		return $items;
	}


	// ------------------------------------------------------------------------


	public function save($id_item_definition, $post)
	{
		$id_item = $post['id_item'];

		// Insert item
		if ( ! $id_item)
		{
			$ordering = $this->_get_ordering($post['ordering'], $id_item_definition);

			$item = array (
				'id_item_definition' => $id_item_definition,
				'ordering' => $ordering
			);

			$this->{$this->db_group}->insert('item', $item);
			$id_item = $this->{$this->db_group}->insert_id();

			// Reorder
			// $this->_reorder($parent, $id_parent, $id_element_definition);
		}

		// Save fields
		$extend_fields = $this->get_list(
			array(
				'parent' => 'item',
				'id_parent' => $id_item_definition,
			),
			'extend_field'
		);

		foreach ($extend_fields as $extend_field)
		{
			// Link between extend_field, current parent and element
			$where = array(
				'id_extend_field' => $extend_field['id_extend_field'],
				'id_parent' => $id_item,
				'parent' => 'item'
			);

			// Checkboxes : first clear values from DB as the var isn't in $_POST if no value is checked
			if ($extend_field['type'] == '4')
			{
				$this->{$this->db_group}->where($where);
				$this->{$this->db_group}->delete('extend_fields');
			}

			// Get the value from _POST values ($data) and feed the data array
			foreach ($post as $k => $value)
			{
				if (substr($k, 0, 2) == 'cf')
				{
					// Fill the extend field value with nothing : safe for checkboxes
					$data = array();
					$data['content'] = '';
					$data['lang'] = '';
					$data['id_parent'] = $id_item;
					$data['parent'] = 'item';

					// id of the extend field
					$key = explode('_', $k);

					// if language code is set, use it in the query
					if (isset($key[2]))
					{
						$where['lang'] = $data['lang'] = $key[2];
					}

					// If the extend field ID is set, we can safelly save...
					if (isset($key[1]) && $key[1] == $extend_field['id_extend_field'])
					{
						// if value is an array...
						if (is_array($value)) {	$value = implode(',', $value); }

						// If value is one date
						if ($extend_field['type'] == '7') $value = str_replace('.', '-', $value);

						$data['content'] = $value;

						// Update
						if( $this->exists($where, 'extend_fields'))
						{
							$this->{$this->db_group}->where($where);
							$this->{$this->db_group}->update('extend_fields', $data);
						}
						// Insert
						else
						{
							// Set the extend field element field ID
							$data['id_extend_field'] = $key[1];
							$this->{$this->db_group}->insert('extend_fields', $data);
						}
					}
				}
			}
		}
		return $id_item;

	}


	// ------------------------------------------------------------------------


	/**
	 * Links on item to one parent
	 *
	 * @param $id_item
	 * @param $parent
	 * @param $id_parent
	 */
	public function link_to_parent($id_item, $parent, $id_parent)
	{
		$data = array(
			'id_item' => $id_item,
			'parent' => $parent,
			'id_parent' => $id_parent,
		);

		$this->insert_ignore($data, self::$_ITEMS);
	}


	// ------------------------------------------------------------------------


	/**
	 * Links on item to one parent
	 *
	 * @param $id_item
	 * @param $parent
	 * @param $id_parent
	 */
	public function unlink_from_parent($id_item, $parent, $id_parent)
	{
		$where = array(
			'id_item' => $id_item,
			'parent' => $parent,
			'id_parent' => $id_parent,
		);

		parent::delete($where, self::$_ITEMS);
	}


	// ------------------------------------------------------------------------


	public function order_for_parent($parent, $id_parent, $items)
	{
		foreach($items as $key => $id_item)
		{
			$where = array(
				'parent' => $parent,
				'id_parent' => $id_parent,
				'id_item' => $id_item
			);

			$this->{$this->db_group}
				->where($where)
				->update(self::$_ITEMS, array('ordering' => $key+1));
		}
	}


	// ------------------------------------------------------------------------


	public function delete($id_item)
	{
		$affected_rows = 0;

		$item = $this->get($id_item);

		// Check if exists
		if( $this->exists(array($this->get_pk_name() => $id_item)) )
		{
			// Item
			$affected_rows = parent::delete(array($this->get_pk_name() => $id_item));

			// Extend fields content delete
			$affected_rows += parent::delete(
				array('id_parent' => $id_item, 'parent' => 'item'),
				self::$_EXTEND_FIELDS
			);

			// Links with content
			$affected_rows += parent::delete(
				array('id_item' => $id_item),
				self::$_ITEMS
			);


			$this->_reorder(
				$item['id_item_definition']
			);
		}

		return $affected_rows;
	}


	// ------------------------------------------------------------------------


	private function _get_ordering($place, $id_item_definition)
	{
		$ordering = '1';

		switch($place)
		{
			case 'first' :
				break;

			case 'last' :

				$where = array (
					'id_item_definition' => $id_item_definition
				);
				$ordering = count($this->get_list($where)) + 1;

				break;
		}
		return $ordering;
	}


	// ------------------------------------------------------------------------


	private function _reorder($id_item_definition)
	{
		$cond = array(
			'id_item_definition' => $id_item_definition,
			'order_by' => 'ordering ASC, id_item DESC'
		);

		$items = parent::get_list($cond);

		foreach($items as $key => $item)
		{
			$this->{$this->db_group}
				->where($this->pk_name,	$item['id_item'])
				->update($this->table, array('ordering' => $key+1)
			);
		}
	}

}
