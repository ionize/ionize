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
 * Ionize Extend Fields Model
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
			$this->get_lang_table() . '.' . $this->get_pk_name() . ' = ' . $this->get_table() . '.' . $this->get_pk_name() . ' AND '
			. $this->get_lang_table() . '.lang = \'' . Settings::get_lang('default') . '\'',
			'left'
		);

		return parent::get_list($where, $lang);
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
		return parent::get_lang_list($where, $lang);
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $id_extend_field
	 *
	 * @return string
	 */
	public function get_label($id_extend_field)
	{
		if($id_extend_field != '') {
			$this->{$this->db_group}->select($this->get_lang_table() . '.label');
			$this->{$this->db_group}->from($this->get_table());
			$this->{$this->db_group}->join($this->get_lang_table(), $this->get_table() . '.' . $this->get_pk_name() . ' = ' . $this->get_lang_table() . '.' . $this->get_pk_name(), 'inner');
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
	function get_element_extend_fields($parent, $id_parent=NULL)
	{
		// Element extend fields
		$where = array('parent'=>$parent);
		$extend_fields = $this->get_list($where);

		// Current element extend field
		$this->{$this->db_group}->where(array('extend_field.parent'=>$parent, $this->elements_table.'.id_parent' => $id_parent));
		$this->{$this->db_group}->join($this->elements_table, $this->elements_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table, 'inner');			

		$query = $this->{$this->db_group}->get($this->table);

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
	 * Saves extend fields data
	 *
	 * @param $parent	Parent type
	 * @param $id		Current parent element ID. Can be the page ID, the article ID...
	 * @param $data		$_POST data array
	 */
	function save_data($parent, $id, $data)
	{
		// Get all extends fields with this element OR kind of parent
		$extend_fields = (!empty($data['id_element_definition'])) ? $this->get_list(array('id_element_definition' => $data['id_element_definition'])) : $this->get_list(array('parent' => $parent));

		foreach ($extend_fields as $extend_field)
		{
			// Link between extend_field and the current parent
			$where = array(
				$this->pk_name => $extend_field[$this->pk_name],
				'id_parent' => $id
			);
			
			// Checkboxes : first clear values from DB as the var isn't in $_POST if no value is checked
			if ($extend_field['type'] == '4')
			{
				$this->{$this->db_group}->where($where);
				$this->{$this->db_group}->delete($this->elements_table);			
			}
			
			// Get the value from _POST values and feed the data array
			foreach ($_POST as $k => $value)
			{
				if (substr($k, 0, 2) == 'cf')
				{
					// Fill the extend field value with nothing : safe for checkboxes
					$data = array();
					$data['content'] = '';
					$data['lang'] = '';
					$data['id_parent'] = $id;

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
						if (is_array($value))
						{
							$value = implode(',', $value);
						}

						// If value is one date
						if ($extend_field['type'] == '7')
							$value = str_replace('.', '-', $value);

						$data['content'] = $value;	

						// Update
						if( $this->exists($where, $this->elements_table))
						{
							$this->{$this->db_group}->where($where);
							$this->{$this->db_group}->update($this->elements_table, $data);
						}
						// Insert
						else
						{
							// Set the extend field element field ID
							$data[$this->pk_name] = $key[1];
							
							$this->{$this->db_group}->insert($this->elements_table, $data);
						}
					}
				}
			}
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
	function delete_extend_fields($id)
	{
		$this->{$this->db_group}->where('id_'.$this->table, $id);
		
		return $this->{$this->db_group}->delete($this->elements_table);
	}
}
