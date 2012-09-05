<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Simpleform_Action
{
	var $CI;

	/*
	 * We need to invoke process_data once this class is created.
	 * But we can't use the constructor, as the tag won't be available there.
	 * So we need this pseudo_construct in order not to leak memory.
	 *
	 */
	private $pseudo_constructed = FALSE;

	function __construct()
    {
		$this->CI =  &get_instance();

		if (!isset($this->CI->form_validation))
			$this->CI->load->library('form_validation');
    }

	
	public function pseudo_construct($tag)
	{
		if ($this->pseudo_constructed)
			return FALSE;
		
		$this->pseudo_constructed = TRUE;

		$this->CI =  &get_instance();

		$config = array();
		include MODPATH . 'Simpleform/config/config.php';
		$this->_config = $config;

		return $this->process_data($tag);
	}


	/*
	 * Tags
	 */
	public function help($tag)
	{
		$output = $tag->parse_as_nested(file_get_contents(MODPATH.'Simpleform/views/tag_help'.EXT));
		return $output;
	}


	/*
	 * This function is called every time Simpleform is used. It checks whether there is something to save or something to do
	 * All this is done at the first place in order to make every tag render the right thing.
	 * If this wasn't used like that, some tags would display wrong data, cause things would be displayed, before they're saved.
	 *
	 */
	public function process_data($tag)
	{
		if ( ! isset($this->CI->simpleform_validation))
			$this->CI->load->library('simpleform_validation');

		/**
		 * Check the rules defined in config.php for the given form name
		 * and proccess the form data.
		 *
		 * Here, it sends a mail with the form data.
		 *
		 */
		if ($this->CI->input->post('form_name') !== FALSE && config_item('simpleform_' . $this->CI->input->post('form_name')))
		{
			$form_name = $this->CI->input->post('form_name');
		
			if ($this->CI->simpleform_validation->run($form_name))
			{
				try
				{
					// Email Lib
					if (!isset($this->CI->email)) $this->CI->load->library('email');
					
					// Email Subject
					$email_subject = $this->get_config_item('simpleform_' . $form_name . '_email_subject');
					$email_subject = ! is_null($email_subject) ? $email_subject : Settings::get("site_title");
					$this->CI->email->subject($email_subject);
					
					// From : Standard website's email
					$this->CI->email->from(Settings::get("site_email"), Settings::get("site_title"));
					
					// To : As defined in config.php
					$to = $this->get_config_item('simpleform_' . $form_name . '_email');
					if ( ! is_null($to))
					{
						$this->CI->email->to($to);
					}
					else
					{
						throw new Exception(
							'SimpleForm module error : No destination Email set in "<b>'.MODPATH.'Simpleform/config/config.php</b>" for the form called : <b>'.$form_name.'</b><br/>'.
							'Please setup the config item : $config[\'simpleform_'.$form_name.'_email\']'
						);
						
					}
					
					// Email view
					$view_file = MODPATH.'Simpleform/views/' . $this->get_config_item('simpleform_' . $form_name . '_email_view').EXT;
					if (file_exists($view_file))
					{
						$this->CI->email->message($tag->parse_as_nested(file_get_contents($view_file)));
// Email send is desactivated
// Uncomment these lines
// after config.php setup
						if ($this->CI->email->send())
						{
							$this->CI->simpleform_validation->set_additional_success(
								'email',
								lang('module_simpleform_text_success')
							);
						}
					}
					else
					{
						throw new Exception(
							'SimpleForm module error : No Email View set in "<b>'.MODPATH.'Simpleform/config/config.php</b>" for the form called : <b>'.$form_name.'</b><br/>'.
							'Please setup the config item : $config[\'simpleform_'.$form_name.'_mail_view\']'
						);
					}
				}
				catch(Exception $e)
				{
					$this->CI->simpleform_validation->set_additional_error('form', $e->getMessage());
				}
			}
		}

		$this->CI->simpleform_validation->check_additional_errors();
	}

	public function get_config_item($key)
	{
		if (isset($this->_config[$key]))
			return $this->_config[$key];

		return NULL;
	}

}
