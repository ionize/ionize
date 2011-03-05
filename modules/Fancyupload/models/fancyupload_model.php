<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.91
 */

// ------------------------------------------------------------------------

/**
 * Ionize Fancyupload Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Module Model
 * @author		Ionize Dev Team
 *
 */

class Fancyupload_model extends Base_model
{

	/**
	 * Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('module_emailrecord_emails');
		$this->set_pk_name('id_email');
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves one email
	 *
	 * @param		array		Email data array
	 * @returns		boolean		true if success, false if fails
	 *
	 */
	public function save($data)
	{
		// Save
		if ( ! $this->exists(array('email' => $data['email']), $this->table))
		{
			$this->db->insert($this->table, $data);
			
			return $this->db->insert_id();
		}
		
		return false;
	}
	


	/**
	 * Saves one email list and returns the number of items inserted
	 *
	 * @param	Array	Emails array
	 * @return	Int		Inserted elements
	 *
	 */
	function save_email_list($data, $confirmed, $group=false)
	{
		// Get all users in a table
		$users = array();
		$query = $this->db->get($this->table);

		if ($query->num_rows() > 0) 
		{
			foreach($query->result() as $row)
				$users[] = $row->email;
		}
		$query->free_result();

		$i = 0;
		foreach($data as $email)
		{
			if (!in_array($email, $users))
			{
				$this->db->set('email', $email);
				$this->db->set('confirmed', $confirmed);
				$this->db->set('join_date', date('Y-m-d H:i:s'));
				
				$this->db->insert($this->table);
				
				$id_user = $this->db->insert_id();

				/*				
				if ($group)
				{
					$this->attachUserToGroup($id_user, $group);
				}
				*/
				
				$i ++;
			}
		}

		return $i;
	}

}
/* End of file emailrecord_model.php */
/* Location: ./modules/EmailRecord/models/emailrecord_model.php */
