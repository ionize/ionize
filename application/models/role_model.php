<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Role Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	ACL
 * @author		Ionize Dev Team
 *
 */

class role_model extends Base_model
{
	/**
	 * Link table between user and role
	 *
	 * @var string
	 */
	static $USER_ROLE_TABLE = 'user_role';


	// --------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'role';
		$this->pk_name = 	'id_role';
	}


	// --------------------------------------------------------------------
	/*
	public function get_user_roles($id_user)
	{
		$data = array();

		$this->{$this->db_group}->where(self::$USER_ROLE_TABLE.'.id_user', $id_user);

		$this->{$this->db_group}->join(
			self::$USER_ROLE_TABLE,
			self::$USER_ROLE_TABLE.'.id_role = ' .$this->get_table().'.id_role',
			'inner'
		);

		$query = $this->{$this->db_group}->get($this->get_table());

		if ( $query->num_rows() > 0)
			$data = $query->result_array();

		return $data;
	}
	*/

}
