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
 * Event Model
 *
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

/* End of file category_model.php */
/* Location: ./application/models/category_model.php */