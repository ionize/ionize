<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usermanager_Functions {

	// Additional errors, that are added to the error-string besides form_validation-errors
	public $additional_err = array();
	public $additional_notices = array();
	public $additional_success = array();

	function __construct()
    {
		$this->_set_error_messages();
	}

	/*
	 * Main functions
	 * For input processing and output creation
	 */

	public function check_login_input()
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();

		$err = false;

		//$ci->form_validation->set_error_delimiters('<div class="error">', '</div>');

		foreach ($config['usermanager_login_model'] as $key => $val)
		{
			//echo $key,$val;
			if (($config['usermanager_email_as_username'] == true && $key != "username") || ($config['usermanager_email_as_username'] == false && $key != "email"))
				$ci->form_validation->set_rules($key, "lang:module_usermanager_field_".$key, $val);
		}
		if ($ci->form_validation->run())
		{
			if ($this->check_additional_errors())
			{
				if ($config['usermanager_display_login_errors'] === false)
				{
					unset($ci->form_validation->_error_array);
					$ci->form_validation->_error_array['login'] = lang("module_usermanager_error_bad_login_information");
				}
				return false;
			}
			return true;
		}
		else
		{
			$this->check_additional_errors();
			if ($config['usermanager_display_login_errors'] === false)
			{
				unset($ci->form_validation->_error_array);
				$ci->form_validation->_error_array['login'] = lang("module_usermanager_error_bad_login_information");
			}
			return false;
		}
	}

	/*public function prepare_login_output()
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();
		$ret = $this->_prep_ret();
		foreach ($config['usermanager_login_model'] as $key => $val)
		{
			$ret['fields'][$key] = $ci->input->post($key);
		}
		return $ret;
	}*/

	public function check_register_input()
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();

		$err = false;

		foreach ($config['usermanager_user_model'] as $key => $val)
		{
			if ($config['usermanager_email_as_username'] == false || ($config['usermanager_email_as_username'] == true && $key != "username"))
			{
				$ci->form_validation->set_rules($key, "lang:module_usermanager_field_".$key, $val['rules']);
				if ($val['special_field'] === "restricted")
					$this->_restricted($key);
				if ($val['special_field'] === "id_user")
					$this->_id_user($key);

				if (!($val['default_value'] === false))
					if ($ci->input->post($key) === false)
						if (!$val['special_field'] === "checkbox") // Because of Checkboxes
							$_POST[$key] = $val['default_value'];
				if ($ci->input->post($key) === "on" && $val['special_field'] === "checkbox")
					$_POST[$key] = "1";
			}
		}

		if ($ci->form_validation->run())
		{
			if ($this->check_additional_errors())
			{
				return false;
			}
			return true;
		}
		else
		{
			$this->check_additional_errors();
			return false;
		}
	}

	/*public function prepare_register_output()
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();
		$ret = $this->_prep_ret();
		foreach ($config['usermanager_user_model'] as $key => $val)
		{
			$ret['fields'][$key] = $val['default_value'] === false ? "" : $val['default_value'];
			$ret['fields'][$key] = !($ci->input->post("$key") === false) ? $ci->input->post($key) : $ret['fields'][$key];
			$ret['fields'][$key] = $ci->input->post("$key") === false && ($val['default_value'] === "1" || $val['default_value'] === 1) && !($ci->input->post("register") === "1") ? "1" : $ci->input->post("$key"); // Checkboxes
		}
		return $ret;
	}*/

	public function check_profile_input()
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();

		$err = false;

		foreach ($config['usermanager_user_model'] as $key => $val)
		{
			if ($config['usermanager_email_as_username'] == false || ($config['usermanager_email_as_username'] == true && $key != "username"))
			{
				if ($key != "password" && $key != "password2" || ($key == "password" && $ci->input->post('password')) || ($key == "password2" && $ci->input->post('password2')))
				{
					$ci->form_validation->set_rules($key, "lang:module_usermanager_field_".$key, $val['rules']);
					if ($val['special_field'] === "restricted")
						$this->_restricted($key);
					if ($val['special_field'] === "id_user")
						$this->_id_user($key);

					if (!($val['default_value'] === false))
						if ($ci->input->post($key) === false)
							if (!$val['special_field'] === "checkbox") // Because of Checkboxes
								$_POST[$key] = $val['default_value'];
					if ($ci->input->post($key) === false)
						$_POST[$key] = "";
					if ($ci->input->post($key) === "on" && $val['special_field'] === "checkbox")
						$_POST[$key] = "1";
				}
			}
		}

		if ($ci->form_validation->run())
		{
			if ($this->check_additional_errors())
			{
				return false;
			}
			return true;
		}
		else
		{
			$this->check_additional_errors();
			return false;
		}
	}

	/*public function prepare_profile_output()
	{
		$ret = $this->_prep_ret();
		return $ret;
	}*/

	/*public function prepare_profile_edit_output()
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();
		$ret = $this->_prep_ret();
		foreach ($config['usermanager_user_model'] as $key => $val)
		{
			$ret['fields'][$key] = "";
			$ret['fields'][$key] = isset($ret['user'][$key]) && $ret['user'][$key] && ($ci->input->post('edit') === "1" || strstr($val['rules'], 'max_length[1]') === false) ? $ret['user'][$key] : $ret['fields'][$key];
			if ($key === "password" || $key == "password2")
				$ret['fields'][$key] = "";
			$ret['fields'][$key] = !($ci->input->post("$key") === false) ? $ci->input->post($key) : $ret['fields'][$key];
		}
		return $ret;
	}*/

	// Everything, that is present, will be set. Regardless of which fields are required. Restricted fields will still be left out.
	public function check_random_fields_input()
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();

		$err = false;

		foreach ($config['usermanager_user_model'] as $key => $val)
		{
			if ($config['usermanager_email_as_username'] == false || ($config['usermanager_email_as_username'] == true && $key != "username"))
			{
				if (!($ci->input->post($key) === false))
				{
					$ci->form_validation->set_rules($key, "lang:module_usermanager_field_".$key, $val['rules']);
					if ($val['special_field'] === "restricted")
						$this->_restricted($key);
					if ($val['special_field'] === "id_user")
						$this->_id_user($key);
					if ($ci->input->post($key) === "on" && $val['special_field'] === "checkbox")
						$_POST[$key] = "1";
				}
			}
		}

		if ($ci->form_validation->run())
		{
			if ($this->check_additional_errors())
			{
				return false;
			}
			return true;
		}
		else
		{
			$this->check_additional_errors();
			return false;
		}
	}

	public function check_additional_errors()
	{
		$ci =  &get_instance();
		foreach ($this->additional_err as $key => $val)
			$ci->form_validation->_error_array[$key] = $val;
		if (sizeof($ci->form_validation->_error_array) > 0)
		{
			unset($this->additional_err);
			$this->additional_err = array();
			return true;
		}
		return false;
	}

	private function _set_error_messages()
	{
		$ci =  &get_instance();
		$ci->form_validation->set_message('required', lang('module_usermanager_error_required'));
		$ci->form_validation->set_message('isset', lang('module_usermanager_error_isset'));
		$ci->form_validation->set_message('valid_email', lang('module_usermanager_error_valid_email'));
		$ci->form_validation->set_message('valid_emails', lang('module_usermanager_error_valid_emails'));
		$ci->form_validation->set_message('valid_url', lang('module_usermanager_error_valid_url'));
		$ci->form_validation->set_message('valid_ip', lang('module_usermanager_error_valid_ip'));
		$ci->form_validation->set_message('min_length', lang('module_usermanager_error_min_length'));
		$ci->form_validation->set_message('max_length', lang('module_usermanager_error_max_length'));
		$ci->form_validation->set_message('exact_length', lang('module_usermanager_error_length'));
		$ci->form_validation->set_message('alpha', lang('module_usermanager_error_alpha'));
		$ci->form_validation->set_message('alpha_numeric', lang('module_usermanager_error_alpha_numeric'));
		$ci->form_validation->set_message('alpha_dash', lang('module_usermanager_error_alpha_dash'));
		$ci->form_validation->set_message('numeric', lang('module_usermanager_error_numeric'));
		$ci->form_validation->set_message('is_numeric', lang('module_usermanager_error_is_numeric'));
		$ci->form_validation->set_message('integer', lang('module_usermanager_error_integer'));
		$ci->form_validation->set_message('matches', lang('module_usermanager_error_matches'));
		$ci->form_validation->set_message('is_natural', lang('module_usermanager_error_is_natural'));
		$ci->form_validation->set_message('is_natural_no_zero', lang('module_usermanager_error_is_natural_no_zero'));
	}

	/*private function _prep_ret()
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();
		$this->check_additional_errors();
		return array( "admin_url" => base_url()."admin",
					  "profile_url" => base_url().$ci->settings->get_lang()."/".$config['usermanager_profile_url'],
					  "register_url" => base_url().$ci->settings->get_lang()."/".$config['usermanager_register_url'],
					  "login_url" => base_url().$ci->settings->get_lang()."/".$config['usermanager_login_url'],
					  "url" => (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
					  "user" => $ci->usermanager_user->get_current_user(),
					  "fields" => array(),
					  "login_field_name" => $config['usermanager_email_as_username'] ? "email" : "username",
					  "login_field_label" => $config['usermanager_email_as_username'] ? lang("module_usermanager_field_email") : lang("module_usermanager_field_username"),
					  "connect" => &$ci->connect,
				  	  "error" => (isset($ci->form_validation) && $ci->form_validation->error_string() ? $ci->form_validation->error_string() : ""));
	}*/

	/*
	 * Custom form_validation rules
	 */

	private function _restricted($val)
	{
		$ci =  &get_instance();
		if (!($ci->input->post($val) === false))
			$this->additional_err['restricted'] = lang("module_usermanager_error_restricted_field");
	}

	private function _id_user($val)
	{
		$_POST[$val] = "//USER_ID//";
	}
}
