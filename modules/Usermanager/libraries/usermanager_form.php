<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usermanager_Form {

	function __construct()
    {
		$ci =  &get_instance();
		if (!isset($ci->form_validation))
			$ci->load->library('form_validation');
	}

	public function has_errors($tag)
	{
		$ci =  &get_instance();
		if (!isset($ci->usermanager_functions))
			$ci->load->library('usermanager_functions');
		$ci->usermanager_functions->check_additional_errors();
		if (!isset($tag->attr['form_name']) || $tag->attr['form_name'] === $ci->input->post('form_name'))
		{
			if (isset($tag->attr['is_like']))
			{
				if ((isset($ci->form_validation) && $ci->form_validation->error_string()) && $tag->attr['is_like'] == '1')
					return $tag->expand();
				if (!(isset($ci->form_validation) && $ci->form_validation->error_string()) && $tag->attr['is_like'] == '1')
					return "";
				if ((isset($ci->form_validation) && $ci->form_validation->error_string()) && $tag->attr['is_like'] == '0')
					return "";
				if (!(isset($ci->form_validation) && $ci->form_validation->error_string()) && $tag->attr['is_like'] == '0')
					return $tag->expand();
			}
			else
			{
				if ((isset($ci->form_validation) && $ci->form_validation->error_string()))
					return $tag->expand();
				return "";
			}

		}
		if (isset($tag->attr['is_like']) && $tag->attr['is_like'] == '0')
			return $tag->expand();
		return "";
	}

	public function error_string($tag)
	{
		$ci =  &get_instance();
		if (!isset($ci->usermanager_functions))
			$ci->load->library('usermanager_functions');
		$ci->usermanager_functions->check_additional_errors();
		return (isset($ci->form_validation) && $ci->form_validation->error_string() ? $ci->form_validation->error_string() : "");
	}

	public function has_noticess($tag)
	{
		$ci =  &get_instance();
		if (!isset($ci->usermanager_functions))
			$ci->load->library('usermanager_functions');
		if (!isset($tag->attr['form_name']) || $tag->attr['form_name'] === $ci->input->post('form_name'))
		{
			if (isset($tag->attr['is_like']))
			{
				if (!empty($ci->usermanager_functions->additional_notices) && $tag->attr['is_like'] == '1')
					return $tag->expand();
				if (empty($ci->usermanager_functions->additional_notices) && $tag->attr['is_like'] == '1')
					return "";
				if (!empty($ci->usermanager_functions->additional_notices) && $tag->attr['is_like'] == '0')
					return "";
				if (empty($ci->usermanager_functions->additional_notices) && $tag->attr['is_like'] == '0')
					return $tag->expand();
			}
			else
			{
				if (!empty($ci->usermanager_functions->additional_notices))
					return $tag->expand();
				return "";
			}
		}
		if (isset($tag->attr['is_like']) && $tag->attr['is_like'] == '0')
			return $tag->expand();
		return "";
	}

	public function notice_string($tag)
	{
		$ci =  &get_instance();
		if (!isset($ci->usermanager_functions))
			$ci->load->library('usermanager_functions');
		$ret = "";
		if (!isset($ci->usermanager_functions->additional_notices))
			return "";
		foreach ($ci->usermanager_functions->additional_notices as $key => $val)
			$ret .= "<p>".$val."</p>";
		return $ret;
	}

	public function has_success($tag)
	{
		$ci =  &get_instance();
		if (!isset($ci->usermanager_functions))
			$ci->load->library('usermanager_functions');
		if (!isset($tag->attr['form_name']) || $tag->attr['form_name'] === $ci->input->post('form_name'))
		{
			if (isset($tag->attr['is_like']))
			{
				if (!empty($ci->usermanager_functions->additional_success) && $tag->attr['is_like'] == '1')
					return $tag->expand();
				if (empty($ci->usermanager_functions->additional_success) && $tag->attr['is_like'] == '1')
					return "";
				if (!empty($ci->usermanager_functions->additional_success) && $tag->attr['is_like'] == '0')
					return "";
				if (empty($ci->usermanager_functions->additional_success) && $tag->attr['is_like'] == '0')
					return $tag->expand();
			}
			else
			{
				if (!empty($ci->usermanager_functions->additional_success))
					return $tag->expand();
				return "";
			}
		}
		if (isset($tag->attr['is_like']) && $tag->attr['is_like'] == '0')
			return $tag->expand();
		return "";
	}

	public function success_string($tag)
	{
		$ci =  &get_instance();
		if (!isset($ci->usermanager_functions))
			$ci->load->library('usermanager_functions');
		$ret = "";
		if (!isset($ci->usermanager_functions->additional_success))
			return "";
		foreach ($ci->usermanager_functions->additional_success as $key => $val)
			$ret .= "<p>".$val."</p>";
		return $ret;
	}

}
