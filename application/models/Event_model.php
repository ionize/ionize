<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize Event Model
 *
 * @package		Ionize
 * @author		Partikule
 * @link		http://www.partikule.net
 * @since		Version 1.0.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Event Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Event
 * @author		Ionize Dev Team
 *
 */
class Event_model extends Base_model
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table =	'event_log';
	}
}
