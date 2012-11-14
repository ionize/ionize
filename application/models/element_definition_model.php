<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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


	/**
	 * Returns the array of all definitions which have element defined for the given parent
	 *
	 *
	 */
	function get_definitions_from_parent($parent, $id_parent = FALSE)
	{
		// Loads the element model if it isn't loaded
		$CI =& get_instance();
		if (!isset($CI->element_model)) $CI->load->model('element_model');

		// Get definitions
		$definitions = $this->get_lang_list(array('order_by' => 'ordering ASC'), Settings::get_lang('default'));
	
		// Get Elements
		$where = array('parent' => $parent, 'order_by' => 'element.ordering ASC');
		if ($id_parent !== FALSE)
		{
			$where['id_parent'] = $id_parent;
		}
		$elements = $CI->element_model->get_elements($where);

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
	 *
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

/* End of file element_definition_model.php */
/* Location: ./application/models/element_definition_model.php */