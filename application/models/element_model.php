<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.5
 */

/**
 * Ionize Element Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Element
 * @author		Ionize Dev Team
 *
 */
class Element_model extends Base_model
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table = 'element';
		$this->pk_name = 'id_element';

		$this->definition_table = 'element_definition';
		$this->definition_pk_name = 'id_element_definition';

		$this->fields_table = 'extend_fields';
	}
	
	
	// ------------------------------------------------------------------------
	

	/**
	 * @param $where
	 *
	 * @return array
	 */
	public function get_elements($where)
	{
		$data = array();
		
		// Perform conditions from the $where array
		foreach(array('limit', 'offset', 'order_by', 'like') as $key)
		{
			if(isset($where[$key]))
			{
				call_user_func(array($this->{$this->db_group}, $key), $where[$key]);
				unset($where[$key]);
			}
		}

		$where = $this->correct_ambiguous_conditions($where, $this->table);

		if ( !empty ($where) )
			$this->{$this->db_group}->where($where);

		
		$this->{$this->db_group}->join($this->table, $this->table.'.'.$this->definition_pk_name.'='.$this->definition_table.'.'.$this->definition_pk_name );
		
		$query = $this->{$this->db_group}->get($this->definition_table);
		
		if ( $query->num_rows() > 0 )
			$data = $query->result_array();

		$query->free_result();

		return $data;
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Returns all the element fields from one element instance
	 *
	 * @param $id_element
	 *
	 * @return mixed
	 */
	public function get_element_fields($id_element)
	{
		self::$ci->load->model('extend_field_model');
		
		// Get the element
		$element = $this->get(array('id_element' => $id_element) );

		// Get fields definition for the asked element
		$cond = array(
			'parent' => 'element',
			'id_parent' => $element['id_element_definition'],
			'order_by' => 'ordering ASC'
		);
		$definitions_fields = self::$ci->extend_field_model->get_list($cond, 'extend_field');

		// Get fields instances
		$sql = "
			select
				extend_field.id_extend_field,
				extend_field.name,
				extend_field.type,
				extend_field.description,
				extend_fields.*
			from extend_fields
			join extend_field on extend_field.id_extend_field = extend_fields.id_extend_field
			where extend_fields.id_parent = ".$id_element."
			and extend_fields.parent = 'element'
			order by extend_field.ordering ASC
		";

		$query = $this->{$this->db_group}->query($sql);

		$result = array();
		if ( $query->num_rows() > 0)
			$result = $query->result_array();
		$query->free_result();

		// Feed each field with content for the element fields
		$langs = Settings::get_languages();

		foreach($definitions_fields as $key => &$df)
		{
			if ($df['translated'] == '1')
			{
				foreach($langs as $language)
				{
					$df[$language['lang']]['content'] = '';
				}
			}
					
			foreach($result as $row)
			{
				if ($row['id_extend_field'] == $df['id_extend_field'])
				{
					$df = array_merge($df, $row);
					
					if ($df['translated'] == '1')
					{
						foreach($langs as $language)
						{
							$lang = $language['lang'];
							
							if($row['lang'] == $lang)
							{
								$df[$lang]['content'] = $row['content'];
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
	 * @param      $parent
	 * @param      $id_parent
	 * @param      $lang
	 * @param bool $id_definition
	 * @param bool $id_element
	 *
	 * @return mixed
	 */
	public function get_fields_from_parent($parent, $id_parent, $lang, $id_definition = FALSE, $id_element = FALSE)
	{
		// Loads the element model if it isn't loaded
		if ( ! isset(self::$ci->element_definition_model)) self::$ci->load->model('element_definition_model');
		if ( ! isset(self::$ci->extend_field_model)) self::$ci->load->model('extend_field_model');

		// Get definitions
		$cond = array();

		if ($id_definition != FALSE)
			$cond['element_definition.id_element_definition'] = $id_definition;
		
		$definitions = self::$ci->element_definition_model->get_lang_list($cond, $lang);
		
		// Get definitions fields
		$cond = array(
			'parent' => 'element',
			'id_parent <>' => '0',
			'order_by' => 'ordering ASC'
		);
		if ($id_definition != FALSE)
			$cond['id_parent'] = $id_definition;

		$definitions_fields = self::$ci->extend_field_model->get_lang_list($cond, $lang);

		// Get Elements
		$cond = array('order_by' => 'element.ordering ASC' );
		
		if ($id_element) {
			$cond['id_element'] = $id_element;
		}
		else
		{
			$cond['parent'] = $parent;
			$cond['id_parent'] = $id_parent;
		}
		
		$elements = $this->get_elements($cond);

		// Get fields instances
		$where = "
			where extend_fields.id_parent in (
				select id_element from element
				where parent= '".$parent."'
				and id_parent= ".$id_parent."
			)
			and extend_fields.parent = 'element'
		";

		if ($id_element)
			$where = "
				where extend_fields.id_parent = ".$id_element."
				and extend_fields.parent = 'element'
			";
		
		$sql = 'select extend_field.*, extend_fields.*
				from extend_fields
				join extend_field on extend_field.id_extend_field = extend_fields.id_extend_field'
				.$where;
		$query = $this->{$this->db_group}->query($sql);

		$result = array();
		if ( $query->num_rows() > 0)
			$result = $query->result_array();
		$query->free_result();

		$langs = Settings::get_languages();
		$extend_fields_fields = $this->{$this->db_group}->list_fields('extend_fields');
		
		foreach($definitions as $key => &$definition)
		{
			$definition['elements'] = array();
			
			foreach($elements as $element)
			{
				// The element match a definition
				if ($element['id_element_definition'] == $definition['id_element_definition'])
				{
					$element['fields'] = array();

					// Extend fields
					foreach($definitions_fields as $df)
					{
						if ($df['id_parent'] == $definition['id_element_definition'])
						{
							$el = array_merge(array_fill_keys($extend_fields_fields, ''), $df);

							foreach($result as $row)
							{
								if ($row['id_parent'] == $element['id_element'] && $row['id_extend_field'] == $df['id_extend_field'])
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

												$el[$lang_code] = $row;
											}
										}
									}
									else
									{
										$el = array_merge($el, $row);
									}
								}
							}
							$element['fields'][$df['name']] = $el;
						}
					}
					$definition['elements'][] = $element;
				}
			}
			
			if (empty($definition['elements']))
				unset($definitions[$key]);
		}
		
		if (count($definitions) == 1)
			$definitions = array_shift($definitions);
		
		return $definitions;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all the fields definition from one element definition ID.
	 *
	 * @param $id_definition
	 */
	public function get_fields_from_definition_id($id_definition)
	{
		if ( ! isset(self::$ci->extend_field_model)) self::$ci->load->model('extend_field_model');

		$where = array(
			'parent' => 'element',
			'id_parent' => $id_definition
		);

		$fields = self::$ci->extend_field_model->get_lang_list($where, Settings::get_lang('current'));

		return $fields;
	}


	// ------------------------------------------------------------------------
	
	
	/**
	 * @param       $parent
	 * @param array $id_parent
	 * @param bool  $id_element
	 * @param       $id_element_definition
	 * @param       $post
	 *
	 * @return bool|int
	 */
	public function save($parent, $id_parent, $id_element = FALSE, $id_element_definition, $post)
	{
		// Insert the element, if needed
		if ( ! $id_element OR $this->exists(array('id_element' => $id_element), $this->table) == FALSE)
		{
			$ordering = $this->_get_ordering($post['ordering'], $parent, $id_parent, $id_element_definition);
		
			$element = array
			(
				'id_element_definition' => $id_element_definition,
				'parent' => $parent,
				'id_parent' => $id_parent,
				'ordering' => $ordering
			);
			$this->{$this->db_group}->insert('element', $element);
			$id_element = $this->{$this->db_group}->insert_id();

			// Reorder
			$this->_reorder($parent, $id_parent, $id_element_definition);
		}
		
		// Save fields
		$extend_fields = $this->get_list(
			array(
				'id_parent' => $id_element_definition,
				'parent' => 'element'
			),
			'extend_field'
		);

		foreach ($extend_fields as $extend_field)
		{
			// Link between extend_field, current parent and element
			$where = array(
				'id_extend_field' => $extend_field['id_extend_field'],
				'parent' => 'element',
				'id_parent' => $id_element,
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
					$data['parent'] = 'element';
					$data['id_parent'] = $id_element;

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
						if ($extend_field['type'] == '7')
							$value = str_replace('.', '-', $value);

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
		
		return $id_element;	
	}


	// ------------------------------------------------------------------------


	/**
	 * @param null $id
	 *
	 * @return int
	 */
	public function delete($id)
	{
		$affected_rows = 0;

		$element = $this->get(array('id_element' => $id));

		// Check if exists
		if( $this->exists(array($this->pk_name => $id)) )
		{
			// Element delete
			$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id)->delete($this->table);
			
			// Extend fields content delete
			$affected_rows += $this->{$this->db_group}->where(
				array(
					'id_parent'=> $id,
					'parent'=>'element'
				)
			)->delete($this->fields_table);

			$this->_reorder(
				$element['parent'],
				$element['id_parent'],
				$element['id_element_definition']
			);

		}
		
		return $affected_rows;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	public function copy($data)
	{
		$return = FALSE;
		
		// Get the existing element
		$element = $this->get(array('id_element' => $data['id_element']));

		// TODO : For each parent, setup if element is pur at first / last when copying
		$ordering = $this->_get_ordering('first', $data['parent'], $data['id_parent'], $element['id_element_definition']);
		
		// Alter and save a copy of the element
		$element['id_parent'] = $data['id_parent'];
		$element['parent'] = $data['parent'];
		$element['ordering'] = $ordering;
		
		unset($element['id_element']);

		$this->{$this->db_group}->insert('element', $element);
		$return = $id_element = $this->{$this->db_group}->insert_id();
		
		if ($id_element)
		{
			// Copy all fields
			$sql = 	"
				insert into extend_fields
				 (
					id_extend_field,
					parent,
					id_parent,
					lang,
					content,
					ordering,
					id_element
				)
				select
					id_extend_field,
					'element',
					".$id_element.",
					lang,
					content,
					ordering
				from extend_fields
				where id_element = ".$data['id_element']
			;

			$return = $this->{$this->db_group}->query($sql);
		}
		return $return;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $data
	 *
	 * @return int
	 */
	public function move($data)
	{
		$where = array
		(
			'id_element' => $data['id_element']
		);
		$data = array
		(
			'parent' => $data['parent'],
			'id_parent' => $data['id_parent']
		);

		return $this->update($where, $data);
	}


	// ------------------------------------------------------------------------
	

	/**
	 * Gets the element's ordering
	 *
	 * @param $place
	 * @param $parent
	 * @param $id_parent
	 * @param $id_element_definition
	 *
	 * @return int|string
	 */
	private function _get_ordering($place, $parent, $id_parent, $id_element_definition)
	{

		$ordering = '1';

		switch($place)
		{
			case 'first' :
				break;
			
			case 'last' :
			
				$cond = array
				(
					'id_element_definition' => $id_element_definition,
					'id_parent' => $id_parent,
					'parent' => $parent
				);
				$ordering = count($this->get_elements($cond)) + 1;
				
				break;
		}
		return $ordering;
	}

	private function _reorder($parent, $id_parent, $id_element_definition)
	{
		$cond = array(
			'parent' => $parent,
			'id_parent' => $id_parent,
			'id_element_definition' => $id_element_definition,
			'order_by' => 'element.ordering ASC, element.id_element DESC'
		);

		$elements = $this->get_elements($cond);

		foreach($elements as $key => $element)
		{
			$this->{$this->db_group}
				->where($this->pk_name,	$element['id_element'])
				->update($this->table, array('ordering' => $key+1)
			);
		}
	}
}
