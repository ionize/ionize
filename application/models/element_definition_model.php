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
 * Ionize Element Definition Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Element
 * @author		Ionize Dev Team
 *
 */
class Element_definition_model extends Base_model
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table = 'element_definition';
		$this->pk_name = 'id_element_definition';
		$this->lang_table = 'element_definition_lang';

		$this->element_table = 'element';
		
	}
	
	
	// ------------------------------------------------------------------------


	public function get($where, $lang = NULL)
	{
		$lang = is_null($lang) ? Settings::get_lang('default') : $lang;

		$this->{$this->db_group}->select($this->table.'.*,'.$this->lang_table.'.title,'.$this->lang_table.'.lang', FALSE);

		$this->{$this->db_group}->join(
			$this->lang_table,
			$this->lang_table.'.'.$this->pk_name.' = '.$this->table.'.'.$this->pk_name . ' and ' . $this->lang_table.".lang ='".$lang."'",
			'left'
		);

		foreach ($where as $key => $value)
		{
			$this->{$this->db_group}->where($this->table.'.'.$key, $value);
		}

		$query = $this->{$this->db_group}->get($this->table);

		$data = $query->row_array();

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the array of all definitions which have element defined for the given parent
	 *
	 * @param      $parent
	 * @param bool $id_parent
	 *
	 * @return array
	 */
	function get_definitions_from_parent($parent, $id_parent = FALSE)
	{
		// Loads the element model if it isn't loaded
		if ( ! isset(self::$ci->element_model)) self::$ci->load->model('element_model');

		// Get definitions
		$definitions = $this->get_lang_list(
			array('order_by' => 'ordering ASC'),
			Settings::get_lang('default')
		);
	
		// Get Elements
		$where = array(
			'parent' => $parent,
			'order_by' => 'element.ordering ASC'
		);

		if ($id_parent !== FALSE)
			$where['id_parent'] = $id_parent;

		$elements = self::$ci->element_model->get_elements($where);

		// Add elements to definition
		foreach($definitions as $key => $definition)
		{
			$found = FALSE;
			
			foreach($elements as $element)
			{
				// The element match a definition
				if ($element['id_element_definition'] == $definition['id_element_definition'])
				{
					$found = TRUE;
				}
			}
			if ($found == FALSE)
				unset($definitions[$key]);
		}
		
		return $definitions;
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Deletes one Element Definition
	 *
	 * @param null $id
	 *
	 * @return int
	 */
	function delete($id)
	{
		$affected_rows = 0;
		
		// Article delete
		$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id)->delete($this->table);
			
		// Lang
		$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id)->delete($this->lang_table);
		
		return $affected_rows;
	}
}
