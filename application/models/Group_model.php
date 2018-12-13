<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Group Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Users ang Groups
 * @author		Ionize Dev Team
 *
 */
class Group_model extends Base_model
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'user_groups';
		$this->pk_name = 	'id_group';
	}
}
