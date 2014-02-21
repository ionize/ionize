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

// ------------------------------------------------------------------------

/**
 * Ionize Extend Field Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Extend Field
 * @author		Ionize Dev Team
 *
 */
class Extend_field_model extends Base_model
{
	/**
	 * Type Names
	 * @var array
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
		'7' => 'Date & Time',
		'8' => 'Medias',
	);

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		// Stores the extend fields definition
		$this->set_table('extend_field');
		$this->set_pk_name('id_extend_field');
		$this->set_lang_table('extend_field_lang');
		
		// Stores the extends fields instances
		$this->elements_table =	'extend_fields';
	}


	// ------------------------------------------------------------------------


	/**
	 * @param array $where
	 * @param null  $lang
	 *
	 * @return array
	 */
	public function get_list($where = array(), $lang = NULL)
	{
		$where['order_by'] = 'ordering ASC';

		$this->{$this->db_group}->select(
			$this->get_table() . '.*,'
			. $this->get_lang_table() . '.label'
		);

		$this->{$this->db_group}->join(
			$this->get_lang_table(),
			$this->get_lang_table() . '.' . $this->get_pk_name() . ' = ' . $this->get_table() . '.' . $this->get_pk_name()
			. ' AND ' . $this->get_lang_table() . '.lang = \'' . Settings::get_lang('default') . '\'',
			'left'
		);

		$list = parent::get_list($where);

		// Add languages definition on each field
		foreach($list as &$field)
		{
			$field['type_name'] = self::$type_names[$field['type']];

			$field['langs'] = $this->get_lang(array('id_extend_field'=>$field['id_extend_field']));
		}

		return $list;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param array $where
	 * @param null  $lang
	 *
	 * @return array
	 */
	public function get_lang_list($where = array(), $lang = NULL)
	{
		$where['order_by'] = 'ordering ASC';

		$list = parent::get_lang_list($where, $lang);

		return $list;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $id_extend_field
	 *
	 * @return string
	 */
	public function get_label($id_extend_field)
	{
		if($id_extend_field != '')
		{
			$this->{$this->db_group}->select($this->get_lang_table() . '.label');
			$this->{$this->db_group}->from($this->get_table());
			$this->{$this->db_group}->join(
				$this->get_lang_table(),
				$this->get_table() . '.' . $this->get_pk_name() . ' = ' . $this->get_lang_table() . '.' . $this->get_pk_name(),
				'inner'
			);
			$this->{$this->db_group}->where($this->get_lang_table() . '.lang', Settings::get_lang('default'));
			$this->{$this->db_group}->where($this->get_table() . '.' . $this->pk_name, $id_extend_field);
			
			$label = $this->{$this->db_group}->get();
			$label = $label->row_array();
			
			return (!empty($label['label'])) ? $label['label'] : '';
		}
		return 'Need a "$id_extend_field"';
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the current extend fields and their values for one parent element
	 * Used by backend, as all the languages data are also got
	 *
	 * @param	string		parent name
	 * @param	null		parent ID
	 *
	 * @return 	array
	 *
	 */
	public function get_element_extend_fields($parent, $id_parent=NULL)
	{
		// Element extend fields
		$where = array('parent' => $parent);
		$extend_fields = $this->get_list($where);

		// Current element extend field
		$this->{$this->db_group}->where(
			array(
				'extend_field.parent' => $parent,
				$this->elements_table.'.id_parent' => $id_parent
			)
		);

		$this->{$this->db_group}->join(
			$this->elements_table,
			$this->elements_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table,
			'inner'
		);

		$query = $this->{$this->db_group}->get($this->get_table());

		$result = array();
		if ( $query->num_rows() > 0)
			$result = $query->result_array();

		$langs = Settings::get_languages();
		$element_fields = $this->{$this->db_group}->list_fields($this->elements_table);

		foreach($extend_fields as $k => &$extend_field)
		{
			// One not tranlated extend field...
			if ($extend_field['translated'] != '1')
			{
				// fill the base data with empty values
				$extend_field = array_merge(array_fill_keys($element_fields, ''), $extend_field);
			
				foreach($result as $row)
				{
					if($row['id_extend_field'] == $extend_field['id_extend_field'])
					{
						$extend_field = array_merge($extend_field , $row);
					}
				}
			}
			else
			{
				foreach($langs as $language)
				{
					// Lang code
					$lang = $language['lang'];
					
					// Feed lang key with blank array
					$extend_field[$lang] = array();
					$extend_field[$lang]['content'] = '';
					
					// Feeding of template languages elements
					foreach($result as $row)
					{
						if($row['id_extend_field'] == $extend_field['id_extend_field'] && $row['lang'] == $lang)
						{
							$extend_field[$lang] = $row;
						}
					}
				}
			}
		}

		return $extend_fields;
	}


	// ------------------------------------------------------------------------


	/**
	 * Return one extend field definition + value for one given parent
	 *
	 * @param $id_extend
	 * @param $parent
	 * @param $id_parent
	 * @param $lang
	 *
	 * @return array
	 */
	public function get_element_extend_field($id_extend, $parent, $id_parent, $lang=NULL)
	{
		$result = array();

		$where = array(
			$this->get_table().'.'.$this->get_pk_name() => $id_extend,
			'extend_field.parent' => $parent,
			$this->elements_table.'.id_parent' => $id_parent
		);

		if ( ! is_null($lang))
			$where[$this->elements_table.'.lang'] = $lang;
		else
			$where[$this->elements_table.'.lang'] = '';

		$this->{$this->db_group}->select(
			$this->get_table().'.*,'
			.$this->elements_table.'.id_parent,'
			.$this->elements_table.'.lang,'
			.$this->elements_table.'.content'
		);

		$this->{$this->db_group}->where($where);

		$this->{$this->db_group}->join(
			$this->elements_table,
			$this->elements_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table,
			'left'
		);

		$query = $this->{$this->db_group}->get($this->get_table());

		if ( $query->num_rows() > 0)
			$result = $query->row_array();

		return $result;
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves one parent's extend fields data
	 * All extend fields values are saved by this method
	 *
	 * @param $parent		Parent type
	 * @param $id_parent	Current parent element ID. Can be the page ID, the article ID...
	 * @param $post			$_POST data array
	 */
	public function save_data($parent, $id_parent, $post)
	{
		// Get all extends fields with this element OR kind of parent
		$extend_fields = (!empty($post['id_element_definition'])) ? $this->get_list(array('id_element_definition' => $post['id_element_definition'])) : $this->get_list(array('parent' => $parent));

		foreach ($extend_fields as $extend_field)
		{
			$id_extend = $extend_field[$this->get_pk_name()];

			// Link between extend_field and the current parent
			$where = array(
				$this->get_pk_name() => $id_extend,
				'id_parent' => $id_parent
			);
			
			// Checkboxes : first clear values from DB as the var isn't in $_POST if no value is checked
			// furthermore, make sure that if all checkbox values are unchecked, we do not fallback to the
			// default values, we do that by storing the special `-` value in the database. 
			$langs = Settings::get_languages();

			if ($extend_field['type'] == '4')
			{
				if ($this->exists($where, $this->elements_table))
				{
					$this->{$this->db_group}->where($where);
					$this->{$this->db_group}->update($this->elements_table, array('content' => '-'));
				}
				else
				{
					$data = array(
						'content' => '-',
						'lang' => '',
						'id_parent' => $id_parent,
						$this->pk_name => $id_extend,
					);

					if ($extend_field['translated'] != '1')
					{
						$this->{$this->db_group}->insert($this->elements_table, $data);
					}
					else
					{
						foreach ($langs as $language)
						{
							$data['lang'] = $language['lang'];
							$this->{$this->db_group}->insert($this->elements_table, $data);
						}
					}
				}
			}

			// Get the value from _POST values and feed the data array
			foreach ($post as $k => $value)
			{
				if (substr($k, 0, 2) == 'cf')
				{
					// id of the extend field
					$key = explode('_', $k);

					if (isset($key[1]) && $key[1] == $id_extend)
					{
						// if language code is set, use it in the query
						$lang=NULL;

						if (isset($key[2]))
							$lang = $key[2];

						$this->save_extend_field_value($id_extend, $parent, $id_parent, $value, $lang);
					}
				}
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds one value to one multiple values extend fields
	 * Values are coma separated in DB
	 *
	 * @param $id_extend
	 * @param $parent
	 * @param $id_parent
	 * @param $value
	 * @param $lang
	 *
	 * @return bool
	 */
	public function add_value_to_extend_field($id_extend, $parent, $id_parent, $value, $lang=NULL)
	{
		if( ! $id_extend)
		{
			log_message('error', print_r(get_class($this) . '->add_value_to_extend_field() : $id_extend is NULL', TRUE));
		}
		else
		{
			$content = array();

			$data = $this->get_element_extend_field($id_extend, $parent, $id_parent, $lang);

			// Check if $id_media already linked
			if ( ! empty($data))
			{
				$content = explode(',', $data['content']);

				if (in_array($value, $content))
					return FALSE;
			}

			$content[] = $value;

			$this->save_extend_field_value($id_extend, $parent, $id_parent, $content, $lang);

			return TRUE;
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Removes one value from one multiple values extend field
	 * Values are coma separated in DB
	 *
	 * @param $id_extend
	 * @param $parent
	 * @param $id_parent
	 * @param $value
	 * @param $lang
	 */
	public function delete_value_from_extend_field($id_extend, $parent, $id_parent, $value, $lang=NULL)
	{
		$data = $this->get_element_extend_field($id_extend, $parent, $id_parent, $lang);

		// Check if $id_media already linked
		if ( ! empty($data))
		{
			$content = explode(',', $data['content']);

			foreach($content as $key => $existing_value)
			{
				if ($existing_value == $value)
					unset($content[$key]);
			}

			$this->save_extend_field_value($id_extend, $parent, $id_parent, $content, $lang);
		}
	}

	// ------------------------------------------------------------------------


	/**
	 * @param $ordering
	 * @param $id_extend
	 * @param $parent
	 * @param $id_parent
	 * @param $value
	 */
/*	public function order_values_from_extend_field($ordering, $id_extend, $parent, $id_parent, $value)
	{
		$data = $this->get_element_extend_field($id_extend, $parent, $id_parent);

		// Check if $id_media already linked
		if ( ! empty($data))
		{
			$this->save_extend_field_value($id_extend, $id_parent, $ordering);
		}
	}*/

	// ------------------------------------------------------------------------


	/**
	 * Save one extend field value
	 *
	 * @param      $id_extend
	 * @param      $parent
	 * @param      $id_parent
	 * @param      $value
	 * @param null $lang
	 */
	public function save_extend_field_value($id_extend, $parent, $id_parent, $value, $lang=NULL)
	{
		// Extend field definition
		$extend_field = $this->get(array($this->get_pk_name() => $id_extend));

		if ( ! $lang) $lang = NULL;

		// Array ?
		if (is_array($value))
			$value = trim(implode(',', $value), ',');

		// Date
		if ($extend_field['type'] == '7') $value = str_replace('.', '-', $value);

		$data = array(
			$this->get_pk_name() => $id_extend,
			'parent' => $parent,
			'id_parent' => $id_parent,
			'content' => $value,
		);
		$where = array(
			$this->get_pk_name() => $id_extend,
			'parent' => $parent,
			'id_parent' => $id_parent
		);

		if ( ! is_null($lang))
			$where['lang'] = $lang;
		else
			$where['lang'] = '';


		// Update
		if( $this->exists($where, $this->elements_table))
		{
			$this->{$this->db_group}->where($where);
			$this->{$this->db_group}->update($this->elements_table, $data);
		}
		// Insert
		else
		{
			if ( ! is_null($lang)) $data['lang'] = $lang;
			$this->{$this->db_group}->insert($this->elements_table, $data);
		}

	}

	// ------------------------------------------------------------------------


	/**
	 * Delete all the extend fields elements corresponding to a extend field definition
	 * Can be very dangerous !
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function delete_extend_fields($id)
	{
		$this->{$this->db_group}->where('id_'.$this->table, $id);
		
		return $this->{$this->db_group}->delete($this->elements_table);
	}
}
