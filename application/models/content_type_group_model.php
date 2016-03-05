<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.9
 */

/**
 * Ionize Content Type Group Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Content
 * @author		Ionize Dev Team
 *
 */
class Content_type_group_model extends Base_model
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('content_type_group');
		$this->set_pk_name('id_content_type_group');
	}
}
