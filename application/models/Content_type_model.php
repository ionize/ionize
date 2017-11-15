<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.8
 */

/**
 * Ionize Content Type Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Content
 * @author		Ionize Dev Team
 *
 */
class Content_type_model extends Base_model
{
	private static $_TBL_CONTENT_TYPE_GROUP = 'content_type_group';
	private static $_TBL_CONTENT_TYPE_GROUP_ITEMS = 'content_type_group_items';


	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('content_type');
		$this->set_pk_name('id_content_type');
	}


	public function get_select($type = 'page')
	{
		$data = array('' => lang('ionize_label_select_no_type'));

		$this->{$this->db_group}->where('type', $type);

		$this->{$this->db_group}->order_by('name', 'ASC');

		$query = $this->{$this->db_group}->get($this->table);

		if($query->num_rows() > 0)
		{
			$result = $query->result_array();

			foreach($result as $item)
			{
				$data[$item['id_content_type']] = $item['name'];
			}
		}

		return $data;
	}


	public function update_name($id_content_type, $name)
	{
		return parent::update(
			array('id_content_type' => $id_content_type),
			array('name' => $name),
			$this->get_table()
		);
	}


	public function update_field($id_content_type, $field, $value)
	{
		return parent::update(
			array('id_content_type' => $id_content_type),
			array($field => $value),
			$this->get_table()
		);
	}


	public function delete($id_content_type)
	{
		$where = array('id_content_type' => $id_content_type);

		parent::delete($where);
		parent::update($where, array('id_content_type' => NULL), 'page');
		parent::update($where, array('id_content_type' => NULL), 'article');
	}


	public function get_groups_with_items($id_content_type)
	{
		$_extends = $_elements = array();

		$groups = $this->get_groups($id_content_type);

		// Extends
		$sql = "
			select
				e.*,
				et.type_name,
			  	ctg.id_content_type_group,
			  	ctg.name as content_type_group_name,
			  	ct.name as content_type_name
			from extend_field e
			join extend_field_type et on e.type = et.id_extend_field_type
			join content_type_group_items ctge on ctge.item='extend_field' and ctge.id_item = e.id_extend_field
			join content_type_group ctg on ctg.id_content_type_group = ctge.id_content_type_group
			join content_type ct on ct.id_content_type = ctg.id_content_type
			where ct.id_content_type = " . $id_content_type ."
			order by ctg.ordering ASC, ctge.ordering ASC
		";

		$query = $this->{$this->db_group}->query($sql);

		if ($query && $query->num_rows() > 0)
			$_extends = $query->result_array();

		// Content Elements
		$sql = "
			select
				e.*,
				el.*,
			  	ctg.id_content_type_group,
			  	ctg.name as content_type_group_name,
			  	ct.name as content_type_name
			from element_definition e
			join element_definition_lang el on el.id_element_definition = e.id_element_definition and el.lang='".Settings::get_lang('default')."'
			join content_type_group_items ctge on ctge.item='element' and ctge.id_item = e.id_element_definition
			join content_type_group ctg on ctg.id_content_type_group = ctge.id_content_type_group
			join content_type ct on ct.id_content_type = ctg.id_content_type
			where ct.id_content_type = " . $id_content_type ."
			order by ctg.ordering ASC, ctge.ordering ASC
		";

		$query = $this->{$this->db_group}->query($sql);

		if ($query && $query->num_rows() > 0)
			$_elements = $query->result_array();

		foreach($groups as $idx => $group)
		{
			$groups[$idx]['fields'] = array();
			$groups[$idx]['elements'] = array();

			foreach($_extends as $extend)
			{
				if ($group['id_content_type_group'] == $extend['id_content_type_group'])
				{
					$groups[$idx]['fields'][] = $extend;
				}
			}
			foreach($_elements as $element)
			{
				if ($group['id_content_type_group'] == $element['id_content_type_group'])
				{
					$groups[$idx]['elements'][] = $element;
				}
			}
		}

		return $groups;
	}


	public function get_extends_by_groups($id_content_type)
	{
		$result = array();

		$groups = $this->get_groups($id_content_type);

		$sql = "
			select
				e.*,
				et.type_name,
			  	ctg.id_content_type_group,
			  	ctg.name as content_type_group_name,
			  	ct.name as content_type_name
			from extend_field e
			join extend_field_type et on e.type = et.id_extend_field_type
			join content_type_group_items ctge on ctge.item='extend_field' and ctge.id_item = e.id_extend_field
			join content_type_group ctg on ctg.id_content_type_group = ctge.id_content_type_group
			join content_type ct on ct.id_content_type = ctg.id_content_type
			where ct.id_content_type = " . $id_content_type ."
			order by ctg.ordering ASC, ctge.ordering ASC
		";

		log_message('error', print_r($sql, TRUE));



		$query = $this->{$this->db_group}->query($sql);

		if ($query && $query->num_rows() > 0)
			$result = $query->result_array();

		foreach($groups as $idx => $group)
		{
			$groups[$idx]['fields'] = array();

			foreach($result as $extend)
			{
				if ($group['id_content_type_group'] == $extend['id_content_type_group'])
				{
					$groups[$idx]['fields'][] = $extend;
				}
			}
		}

		return $groups;
	}


	public function get_groups($id_content_type)
	{
		$where = array(
			'id_content_type' => $id_content_type,
			'order_by' => 'ordering ASC'
		);

		return parent::get_list($where, self::$_TBL_CONTENT_TYPE_GROUP);
	}


	public function add_group($id_content_type, $name)
	{
		$data = array(
			'id_content_type' => $id_content_type,
			'name' => $name,
			'ordering' => 0
		);

		return $this->insert_ignore($data, self::$_TBL_CONTENT_TYPE_GROUP);
	}


	public function update_group($id_content_type_group, $field, $value)
	{
		return parent::update(
			array('id_content_type_group' => $id_content_type_group),
			array($field => $value),
			self::$_TBL_CONTENT_TYPE_GROUP
		);
	}


	public function delete_group($id_content_type_group)
	{
		$where = array(
			'id_content_type_group' => $id_content_type_group
		);

		parent::delete($where, self::$_TBL_CONTENT_TYPE_GROUP_ITEMS);

		parent::delete($where, self::$_TBL_CONTENT_TYPE_GROUP);
	}


	public function link_extend_with_group($id_extend_field, $id_content_type_group)
	{
		$data = array(
			'item' => 'extend_field',
			'id_item' => $id_extend_field,
			'id_content_type_group' => $id_content_type_group,
		);

		return $this->insert_ignore($data, self::$_TBL_CONTENT_TYPE_GROUP_ITEMS);
	}
	

	public function link_item_with_group($item, $id_item, $id_content_type_group)
	{
		$data = array(
			'item' => $item,
			'id_item' => $id_item,
			'id_content_type_group' => $id_content_type_group,
		);

		return $this->insert_ignore($data, self::$_TBL_CONTENT_TYPE_GROUP_ITEMS);
	}


	public function unlink_item_from_group($item, $id_item, $id_content_type_group)
	{
		$where = array(
			'item' => $item,
			'id_item' => $id_item,
			'id_content_type_group' => $id_content_type_group,
		);

		return parent::delete($where, self::$_TBL_CONTENT_TYPE_GROUP_ITEMS);
	}


	/**
	 *
	 * @param $order
	 * @param $item			Can be 'extend_field' or 'element'
	 * @param $id_content_type_group
	 */
	public function save_item_ordering($order, $item, $id_content_type_group)
	{
		foreach($order as $rank => $id_item)
		{
			$where = array(
				'item' => $item,
				'id_item' => $id_item,
				'id_content_type_group' => $id_content_type_group
			);

			$this->update($where, array('ordering' => $rank), self::$_TBL_CONTENT_TYPE_GROUP_ITEMS);
		}
	}


	public function save_extend_ordering($order, $id_content_type_group)
	{
		foreach($order as $rank => $id_extend_field)
		{
			$where = array(
				'item' => 'extend_field',
				'id_item' => $id_extend_field,
				'id_content_type_group' => $id_content_type_group
			);

			$this->update($where, array('ordering' => $rank), self::$_TBL_CONTENT_TYPE_GROUP_ITEMS);
		}
	}
}
