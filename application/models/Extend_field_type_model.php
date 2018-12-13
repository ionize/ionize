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
 * Ionize Extend Field Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Extend Field
 * @author		Ionize Dev Team
 *
 */
class Extend_field_type_model extends Base_model
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		// Extend Fields definition tables
		$this->set_table('extend_field_type');
		$this->set_pk_name('id_extend_field_type');
	}


}
