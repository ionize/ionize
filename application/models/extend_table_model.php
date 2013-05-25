<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Extend Table Controller
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Extend table management
 * @author		Ionize Dev Team
 *
 * Extends the data model by extending existing tables.
 *
 */
class Extend_table_model extends Base_model
{
	/**
	 * Constructor
	 *
	 * @access	public
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->load->dbforge();

	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Get the extend table fields definition for a given parent table
	 * One extend table is always postfixed by "_extend"
	 * One table can only have one extend table
	 *
	 * @param	String	Origin table name
	 *
	 * @return	Array	Array of fields description. Empty Array if no extend table found
	 *
	 */
	public function get_extend_table_fields($table)
	{
		$extend_table = $table . '_extend';
	
		if ($this->{$this->db_group}->table_exists($extend_table))
		{
			$fields = $this->{$this->db_group}->field_data($extend_table);
			
			// Filter the fields : Removes the primaries and foreign keys
			$fields = array_filter($fields, create_function('$row', 'return $row->primary_key != "1";'));

			$fields = array_filter($fields, create_function('$row', 'return $row->name != "id_'.$table.'";'));

			return $fields;
		}
		
		return array();
	}
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Returns one field definition for a given parent table
	 *
	 * @param	String	field name
	 * @param	String	Origin table name
	 *
	 * @return	Array	Field description object
	 *
	 */
	public function get_extend_table_field($name, $table)
	{
		$extend_table =  $table . '_extend';
		
		$fields = $this->{$this->db_group}->field_data($extend_table);
		
		foreach ($fields as $field)
		{
			if ($field->name == $name) return $field;
		}
		
		return FALSE;
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Save one extend table field
	 * Creates the extend table if it doesn't exists
	 *
	 * @param	String	Parent table name
	 * @param	Array	Extend field definition array
	 *
	 */
	public function save_extend_field($parent_table, $field)
	{
		// Extend table name
		$extend_table = $parent_table . '_extend';
		
		// Only add one field if the parent table exists
		if ($this->{$this->db_group}->table_exists($parent_table))
		{
			// Creates the extend table is it doesn't exists
			if ( ! $this->{$this->db_group}->table_exists($extend_table))
			{
				$this->create_extend_table($parent_table);
			}
			
			return $this->{$this->db_group}->forge->add_column($extend_table, $field);
		}
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Deletes one extend table field
	 *
	 */
	public function delete_extend_field()
	{
	
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * @param $parent_table		String	Parent table name
	 *
	 * @return bool
	 */
	private function create_extend_table($parent_table)
	{
		// Get the parent table fields infos
		$fields = $this->{$this->db_group}->field_data($parent_table);
		
		// Only get primary keys fileds info
		$fields = array_filter($fields, create_function('$row', 'return $row->primary_key == "1";'));
		
		if ( !empty($fields))
		{
			$keys = array();
			
			foreach($fields as $field)
			{
				$keys[$field->name] = array(
					'type' => $field->type,
					'constraint' => $field->max_length
				);
				
				// Add PRIMARY KEY of the parent table as KEY of the extend table
				$this->{$this->db_group}->forge->add_key($field->name);
			}
			
			// Add all keys to the table
			$this->{$this->db_group}->forge->add_field($keys);
			
			// Creates table IF NOT EXISTS
			return $this->{$this->db_group}->forge->create_table($parent_table.'_extend', TRUE);
		}
		
		return FALSE;
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Deletes one extend table field
	 *
	 */
	private function delete_extend_table()
	{
	
	}
}
