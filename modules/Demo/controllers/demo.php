<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.6
 */

// ------------------------------------------------------------------------

/**
 * Demo Module Controller
 *
 * @author		Ionize Dev Team
 *
 * @usage		Have a look at the readme.txt file
 *
 *
 */


class Demo extends Base_Controller 
{

	// ------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}


	// ------------------------------------------------------------------------

	/**
	 * Just do nothing.
	 * 
	 *
	 */
	function index()
	{
		echo('Demo Module Index');
	}

	
	// ------------------------------------------------------------------------


	
}