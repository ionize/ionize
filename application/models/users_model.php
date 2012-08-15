<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.4
 */

// ------------------------------------------------------------------------

/**
 * Ionize Users Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Users
 * @author		Ionize Dev Team
 *
 */

class Users_model extends Base_model 
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'users';
		$this->pk_name = 	'id_user';
		$this->meta_table = 'users_meta';
	}


	/**
	 * Returns the users list, including the selected meta fields.
	 *
	 */
	function get_list($fields=array())
	{
		$data = array(); 
	
		// Standard users data
		$this->{$this->db_group}->select('username, screen_name, email, join_date, last_visit');

		// Meta data
		if( ! empty($fields))
		{
			foreach($fields as $field)
			{
				$this->{$this->db_group}->select($this->meta_table.'.'.$field);
			}
			
			$this->{$this->db_group}->join($this->meta_table, $this->table.'.'.$this->pk_name.' = ' .$this->table.'.'.$this->pk_name, 'left');
		}

		$this->{$this->db_group}->order_by('screen_name', 'ASC');

		$query = $this->{$this->db_group}->get($this->table);
		
		if ( $query->num_rows() > 0 )
			$data = $query->result_array();
				
		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns one user's meta data
	 *
	 * @param	Int		User's ID
	 *
	 * @return	Array	User's meta data associative array
	 *
	 */
	function get_meta($id = NULL)
	{
		$fields = $this->field_data($this->meta_table);

		if ( ! is_null($id))
		{
			$this->{$this->db_group}->where($this->pk_name, $id);

			$query = $this->{$this->db_group}->get($this->meta_table);

			if ( $query->num_rows() > 0 )
			{
				$metas = $query->row_array();

				if ( ! empty($metas))
				{
					foreach($fields as $key => &$field)
						$field['value'] = $metas[$field['field']];
				}
			}
		}

		return $fields;
	}


	// ------------------------------------------------------------------------
	
	
	/**
	 * Returns the users_meta fields
	 *
	 * @return	Array	Meta fields name
	 *
	 */	
	function get_meta_fields()
	{
		$data = array();
		
		$query = $this->{$this->db_group}->query("SHOW COLUMNS FROM " . $this->meta_table);
		$fields = $query->result_array();

		foreach($fields as $field)
		{
			if ($field['Field'] != $this->pk_name)
			{
				$data[] = $field['Field'];
			}
		}
		
		return $data;
	}


	// ------------------------------------------------------------------------
	
	
	/**
	 * Saves the users_meta
	 * First filters the metas in the $_POST array
	 * Note : The controller could do this stuff, but as this task is repetitive...
	 *
	 * @param	Array	The $_POST array
	 *
	 */	
	function save_meta($id_user, $data)
	{
		// Existing meta fields
		$fields = $this->get_meta_fields();
		
		// Data array to insert / update in the DB
		$metas = array();
		
		// Feed the data array
		foreach($fields as $field)
		{
			$metas[$field] = ( ! empty($data[$field])) ? $data[$field] : '' ;
		}
	
		// Insert
		if ($this->exists(array($this->pk_name => $id_user),$this->meta_table ) == false)
		{
			$metas[$this->pk_name] = $id_user;
			$this->{$this->db_group}->insert($this->meta_table, $metas);
		}
		else
		{
			$this->{$this->db_group}->where($this->pk_name, $id_user);
			$this->{$this->db_group}->update($this->meta_table, $metas);
		}
	}
	

	// ------------------------------------------------------------------------
	

	/**
	 * Deletes one user
	 *
	 *
	 */
	function delete($id)
	{
		$affected_rows = 0;
		
		// Check if element exists
		if( $this->exists(array($this->pk_name => $id)) )
		{
			// User delete
			$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id)->delete($this->table);
			
			// User's meta delete
			$affected_rows += $this->{$this->db_group}->where($this->pk_name, $id)->delete($this->meta_table);
		}
		return $affected_rows;	
	}
}
/* End of file users_model.php */
/* Location: ./application/models/users_model.php */