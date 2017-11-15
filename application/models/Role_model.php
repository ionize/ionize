<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Role Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Authorizations
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
}
