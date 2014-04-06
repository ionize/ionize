<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 1.02
 */

// ------------------------------------------------------------------------

/**
 * Search Module Controller
 *
 * @author		Ionize Dev Team
 *
 * @usage		Have a look at the readme.txt file
 *
 *
 */


class Search extends My_Module
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
	 */
	function index()
	{
		// just do nothing.
	}

	
	// ------------------------------------------------------------------------


	/**
	 * Example of pure PHP result
	 * Useful when calling the function through XHR
	 *
	 * @notice : 	The "result_pure_php" view has in this case no access to the Ionize tags,
	 *				because the Ionize Tags library is not loaded, so the tags will not be parsed.
	 * 
	 * @usage	In your view :
	 *			<form method="post" action="<ion:base_url lang="true" />search/find_pure_php">
	 *
	 */
	function find_pure_php()
	{
		// Get the posted term to search
		$realm = $this->input->post('realm');

		if ( ! empty($realm))
		{
			// Loads the module search model
			$this->load->model('search_model', '', true);
			
			// Get all articles from DB and feed the Ionize template data array.
			// $this->template is available in Ionize's Base controller and is used to send data to views.
			// For more informations, see /application/libraries/MY_Controller.php
			$this->template['articles'] = $this->search_model->get_articles($realm);

			// Outputs the result with the view : /modules/Search/views/results_pure_php.
			$this->output('results_pure_php');
		}
	}
	
}