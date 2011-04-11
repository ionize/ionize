<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.5
 */

// ------------------------------------------------------------------------

/**
 * Ionize Extend Fields Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Lang
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
		$this->table =		'extend_field';
		$this->pk_name 	=	'id_extend_field';
		
		// Stores the extends fields instances
		$this->elements_table =		'extend_fields';
	}


	function get_list($where = array())
	{
		$where['order_by'] = 'ordering ASC';
		return parent::get_list($where);
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the current extend fields and their values for one parent element
	 * Used by backend, as all the languages data are also got
	 *
	 * @param	string	parent name
	 * @param	int		parent ID
	 *
	 */
	function get_element_extend_fields($parent, $id_parent=null)
	{
		$data = array();
		$extend_fields = array();
		
		// Element extend fields
		$where = array('parent'=>$parent);
		$extend_fields = $this->get_list($where);
		
		// Current element extend field
		$this->db->where(array('extend_field.parent'=>$parent, $this->elements_table.'.id_parent' => $id_parent));
		$this->db->join($this->elements_table, $this->elements_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table, 'inner');			

		$query = $this->db->get($this->table);

		$result = array();
		if ( $query->num_rows() > 0)
			$result = $query->result_array();

		$langs = Settings::get_languages();
		$element_fields = $this->db->list_fields($this->elements_table);

		foreach($extend_fields as $k => &$extend_field)
		{
			// A not tranlated extend field...
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
	 * @param	int		Current parent element ID. Can be the page ID, the article ID...
	 * @param	Array	$_POST data array
	 *
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
				$this->db->where($where);
				$this->db->delete($this->elements_table);			
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
//					$data['parent'] = $parent;

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

						$data['content'] = $value;	

						// Update
						if( $this->exists($where, $this->elements_table))
						{
							$this->db->where($where);
							$this->db->update($this->elements_table, $data);
						}
						// Insert
						else
						{
							// Set the extend field element field ID
							$data[$this->pk_name] = $key[1];
							
							$this->db->insert($this->elements_table, $data);
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
	 */
	function delete_extend_fields($id)
	{
		$this->db->where('id_'.$this->table, $id);
		
		return $this->db->delete($this->elements_table);
	}
	
	
}

/* End of file extend_field_model.php */
/* Location: ./application/models/extend_field_model.php */