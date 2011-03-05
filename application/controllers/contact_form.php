<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.90
 */

// ------------------------------------------------------------------------

/**
 * Contact_Form Class
 *
 *
 */
class Contact_form extends Base_Controller
{
	/**
	 * Constructor
	 *
	 */	
	function __construct()
	{
		parent::__construct();
		
		// FTL Template lib
		require_once APPPATH.'libraries/ftl/parser.php';
		require_once APPPATH.'libraries/ftl/arraycontext.php';
		
		// Form tag manager
		require_once APPPATH.'libraries/Tagmanager.php';
		require_once APPPATH.'libraries/Tagmanager/Form.php';
		
		// Context
		$this->context = new FTL_ArrayContext();

		$f = new TagManager_Form($this);
		$f->add_globals($this->context);
		$f->add_tags($this->context);
	}


	/**
	 * Validates the contact form
	 *
	 */
	function index()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
			
		$this->form_validation->set_rules('username', lang('form_name'), 'required');
		$this->form_validation->set_rules('email', lang('form_email'), 'required|valid_email');
		$this->form_validation->set_rules('message', lang('form_message'), 'required');
		$this->form_validation->set_rules('check', 'check', 'callback_antispam');

		// Personal rules messages
		// Look into my_theme/language/form_validation_lang.php
		
		// Delimiters for individual error messages : Put to nothing, task of the FTL tag
		$this->form_validation->set_error_delimiters('', '');


		// FAILS
		if ($this->form_validation->run() == FALSE)
		{
			// If request cames from XHR, send just the form again
			if ($this->is_xhr() === true)
			{
				$this->render('articles/contact_form', $this->context);
			}
			
			// Else, ensure the validation data are kept and redirect to the refering page
			else
			{
				// Put the validation_errors string message to the flash session
				$this->session->set_flashdata('validation_errors', $this->form_validation->error_string());
			
				// Put the form field data array to the flash session
				$this->session->set_flashdata('field_data', $this->form_validation->_field_data);
			
				redirect($_SERVER['HTTP_REFERER']);
			}
		}
		
		// SUCCESS
		else
		{
			$this->send_mail();

			// Render the message
			$this->render('articles/contact_form_success', $this->context);
		}
	}


	/**
	 * Anti spam
	 * If the field "name" is empty (not filled by javascript), message that javascript is needed
	 * Ensure that most of the bots could not use the form
	 * 
	 * Called by : 	$this->form_validation->set_rules('check', 'check', 'callback_antispam');
	 * See CodeIgniter documentation for more info about that : http://codeigniter.com/user_guide//user_guide/libraries/form_validation.html#validationrules

	 *
	 */
	function antispam($str)
	{
		if ($str != config_item('form_antispam_key'))
		{
			$this->form_validation->set_message('antispam', lang('contact_form_javascript_needed'));
			return false;
		}
		else
		{
			return true;
		}
	}	


	/**
	 * Send the mails
	 */
	function send_mail()
	{
		/*
		 * Send a mail to the Website email
		 *
		 */
		$this->load->library('email');
		
		// From
		$this->email->from(Settings::get('site_email'), Settings::get('site_title'));
		
		// To
		$this->email->to(Settings::get('site_email'));
		
		// Subject
		$this->email->subject(lang('form_contact_adminmail_subject'));
		
		// Message
		$patterns = array('/%username/', '/%email/','/%message/');
		$replace = array($this->input->post('username'), $this->input->post('email'), $this->input->post('message'));
		$message = preg_replace($patterns, $replace, lang('form_contact_adminmail_message'));
		
		$this->email->message($message);

		// Mail send
		$this->email->send();


		/*
		 * Send a mail to the visitor
		 *
		 */
		// From
		$this->email->from(Settings::get('site_email'), Settings::get('site_title'));
		
		// To
		$this->email->to($this->input->post('email'));
				
		// Subject
		$this->email->subject(lang('form_contact_usermail_subject'));

		// Message
		$message = preg_replace($patterns, $replace, lang('form_contact_usermail_message'));
		
		$this->email->message($message);

		// Mail send
		$this->email->send();
	}
}



/* End of file page.php */
/* Location: ./application/controllers/contact_form.php */