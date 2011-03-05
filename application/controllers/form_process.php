<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.4
 */

// ------------------------------------------------------------------------

/**
 * Form processing example class
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Form processing
 * @author		Ionize Dev Team
 *
 * IMPORTANT :
 * This class is given as example.
 * You are strongly invited to rename and modify it for a production use. 
 * You are free to use or not the FTL library (Tags management). This example uses the tags.
 * 
 */


class Form_process extends Base_Controller 
{ 
	/** 
	 * Constructor
	 */	 
	function __construct() 
	{ 
		parent::__construct(); 
		
		// FTL Tag library 
		require_once APPPATH.'libraries/ftl/parser.php'; 
		require_once APPPATH.'libraries/ftl/arraycontext.php'; 
		 
		// Form tag manager 
		require_once APPPATH.'libraries/Tagmanager.php'; 
		require_once APPPATH.'libraries/Tagmanager/Form.php'; 
		 
		// FTL tags Context 
		$this->context = new FTL_ArrayContext(); 

		// Load the FTL form tags manager
		// See application/libraries/Tagmanager/Form.php for available tags.
		$f = new TagManager_Form($this);
		
		// Declare the global tags 
		$f->add_globals($this->context); 
		
		// Declare the Form tagamanger tags
		$f->add_tags($this->context); 	
	} 

	/** 
	 * Validates the contact form 
	 */ 
	function index() 
	{ 
		$this->load->helper(array('form', 'url')); 
		$this->load->library('form_validation'); 

		$this->form_validation->set_rules('name', lang('name'), 'required'); 
		
		// Delimiters for individual error messages : 
		// Put to nothing, task done by the FTL tagmanager 
		$this->form_validation->set_error_delimiters('', '');

		// FAILS 
		if ($this->form_validation->run() == FALSE) 
		{ 
			// If request cames from XHR, send just the form again 
			if ($this->is_xhr() === true) 
			{ 
				// The my_form view is located at : your_theme/views/forms/my_form
				$this->render('forms/my_form', $this->context); 
			} 
			 
			// Else, ensure the validation data are kept and 
			// redirect to the refering page 
			else 
			{ 
				// Put the validation_errors string message 
				// to the flash session data
				$this->session->set_flashdata('validation_errors', $this->form_validation->error_string()); 
			 
				// Put the form field data array to the flash session data
				$this->session->set_flashdata('field_data', $this->form_validation->_field_data); 
			 
				redirect($_SERVER['HTTP_REFERER']); 
			} 
		} 
		 
		// SUCCESS 
		else 
		{
			/*
			 * Here comes your form process code...
			 *
			 */
			
			// ...
			// ...
		
			/*
			 * Redirect to the success message page or render another view...
			 */
			// redirect('success_message_page_name');
			
			trace('Sucess, the form was sent');
		} 
	}
}