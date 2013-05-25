<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 * Rule Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

class Rule extends MY_Admin
{
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('rule_model', '', TRUE);
	}


	// ------------------------------------------------------------------------


	/**
	 * Do nothing.
	 *
	 */
	public function index(){}


	// ------------------------------------------------------------------------


	/**
	 * Creation Form
	 * Return rules array as JSON
	 *
	 */
	public function get_all()
	{
		$type = $this->input->post('type');

		if ( ! $type)
			$type = NULL;

		$rules = $this->rule_model->get_from_type($type);

		if ($this->is_xhr())
		{
			$data = array('rules' => $rules);
			$this->xhr_output($data);
		}
	}
}
