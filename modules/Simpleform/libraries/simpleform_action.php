<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Simpleform_Action
{
	/*
	 * We need to invoke process_data once this class is created.
	 * But we can't use the constructor, as the tag won't be available there.
	 * So we need this pseudo_construct in order not to leak memory.
	 */
	private $pseudo_constructed = false;

	function __construct()
    {
		$ci =  &get_instance();

		if (!isset($ci->form_validation))
			$ci->load->library('form_validation');
    }

	
	public function pseudo_construct($tag)
	{
		if ($this->pseudo_constructed)
			return FALSE;
		
		$this->pseudo_constructed = true;

		$this->_set_error_messages();

		return $this->process_data($tag);
	}


	/*
	 * Tags
	 */
	public function help($tag)
	{
		$ci =  &get_instance();
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
		$ci =  &get_instance();

		if ( ! isset($ci->simpleform_validation))
			$ci->load->library('simpleform_validation');

		/**
		 * Check the rules defined in config.php for the given form name
		 * and proccess the form data.
		 *
		 * Here, it sends a mail with the form data.
		 *
		 */
		if ($ci->input->post('form_name') !== FALSE && config_item('simpleform_' . $ci->input->post('form_name')))
		{
			$form_name = $ci->input->post('form_name');
		
			if ($this->check_form($form_name))
			{
				try
				{
					// Config
					include MODPATH . 'Simpleform/config/config.php';

					// Email Lib
					if (!isset($ci->email))	$ci->load->library('email');
					
					// Email Title
					$title = isset($config['simpleform_' . $form_name . '_email_title']) ? lang($config['simpleform_' . $form_name . '_email_title']) : Settings::get("site_title");
					$ci->email->subject($title);
					
					// From : Standard website's email
					$ci->email->from(Settings::get("site_email"), Settings::get("site_title"));
					
					// To : As defined in config.php
					if (isset($config['simpleform_' . $form_name . '_email']))
					{
						$ci->email->to($config['simpleform_' . $form_name . '_email']);
					}
					else
					{
						throw new Exception(
							'SimpleForm module error : No destination Email set in "<b>'.MODPATH.'Simpleform/config/config.php</b>" for the form called : <b>'.$form_name.'</b><br/>'.
							'Please setup the config item : $config[\'simpleform_'.$form_name.'_email\']'
						);
						
					}
					
					// Email view
					if (file_exists(MODPATH.'Simpleform/views/'.$config['simpleform_' . $form_name . '_email_view'].EXT))
					{
						$ci->email->message($tag->parse_as_nested(file_get_contents(MODPATH.'Simpleform/views/'.$config['simpleform_' . $form_name . '_email_view'].EXT)));
// Email send is desactivated
// Uncomment these lines
// after config.php setup
						$ci->email->send();

						$ci->simpleform_validation->additional_success['profile'] = lang("module_usermanager_text_registered") . " <a href=''>".lang("module_usermanager_text_registered_here")."</a>.";
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
					$ci->simpleform_validation->additional_err['register'] = $e->getMessage();
				}
			}
		}

		$ci->simpleform_validation->check_additional_errors();
	}
	
	
	/**
	 * checks the rules for one form name, as defined in the config file.
	 *
	 * $config['simpleform_contact_fields'] = array( [rules] );
	 *
	 */
	public function check_form($form_name)
	{
		include MODPATH . 'Simpleform/config/config.php';
		
		$ci =  &get_instance();

		if ( ! isset($ci->simpleform_validation))
			$ci->load->library('simpleform_validation');

		// If rules are defined in the config file...
		if (isset($config['simpleform_' . $form_name]))
		{
			$form_fields = $config['simpleform_' . $form_name];

			// Set the rules
			foreach ($form_fields as $key => $val)
			{
				$ci->form_validation->set_rules($key, "lang:module_simpleform_field_".$key, $val);
				
				if (!($val['default_value'] === FALSE))
					if ($ci->input->post($key) === FALSE)
						if (!$val['special_field'] === 'checkbox') // Because of Checkboxes
							$_POST[$key] = $val['default_value'];
				
				if ($ci->input->post($key) === FALSE)
					$_POST[$key] = '';
				
				if ($ci->input->post($key) === "on" && $val['special_field'] === "checkbox")
					$_POST[$key] = '1';
				
				// AntiSpam
				if ($val == 'antispam')
				{
					$this->antispam($ci->input->post($key));
				}
			}

			// Check the rules
			if ($ci->form_validation->run())
			{
				if ($ci->simpleform_validation->check_additional_errors())
				{
					return FALSE;
				}
				return TRUE;
			}
			return FALSE;
		}
		return TRUE;
	}
	
	
	/**
	 * Called by check_form() 
	 * 
	 */
	private function antispam($str)
	{
		$ci =  &get_instance();

		if ($str != config_item('form_antispam_key'))
		{
			$ci->simpleform_validation->additional_err['spam'] = lang('module_simpleform_error_spam');
			return false;
		}
			return true;
	}	


	private function _set_error_messages()
	{
		$ci =  &get_instance();
		$ci->form_validation->set_message('required', lang('module_simpleform_error_required'));
		$ci->form_validation->set_message('isset', lang('module_simpleform_error_isset'));
		$ci->form_validation->set_message('valid_email', lang('module_simpleform_error_valid_email'));
		$ci->form_validation->set_message('valid_emails', lang('module_simpleform_error_valid_emails'));
		$ci->form_validation->set_message('valid_url', lang('module_simpleform_error_valid_url'));
		$ci->form_validation->set_message('valid_ip', lang('module_simpleform_error_valid_ip'));
		$ci->form_validation->set_message('min_length', lang('module_simpleform_error_min_length'));
		$ci->form_validation->set_message('max_length', lang('module_simpleform_error_max_length'));
		$ci->form_validation->set_message('exact_length', lang('module_simpleform_error_length'));
		$ci->form_validation->set_message('alpha', lang('module_simpleform_error_alpha'));
		$ci->form_validation->set_message('alpha_numeric', lang('module_simpleform_error_alpha_numeric'));
		$ci->form_validation->set_message('alpha_dash', lang('module_simpleform_error_alpha_dash'));
		$ci->form_validation->set_message('numeric', lang('module_simpleform_error_numeric'));
		$ci->form_validation->set_message('is_numeric', lang('module_simpleform_error_is_numeric'));
		$ci->form_validation->set_message('integer', lang('module_simpleform_error_integer'));
		$ci->form_validation->set_message('matches', lang('module_simpleform_error_matches'));
		$ci->form_validation->set_message('is_natural', lang('module_simpleform_error_is_natural'));
		$ci->form_validation->set_message('is_natural_no_zero', lang('module_simpleform_error_is_natural_no_zero'));
	}
}
