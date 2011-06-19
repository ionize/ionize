<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Simpleform_Validation {

	// Additional errors, that are added to the error-string besides form_validation-errors
	public $additional_err = array();
	public $additional_notices = array();
	public $additional_success = array();


	function __construct()
    {
		$ci =  &get_instance();
		
		if (!isset($ci->form_validation))
			$ci->load->library('form_validation');
	}

	public function has_errors($tag)
	{
		$ci =  &get_instance();

		$this->check_additional_errors();

		if (!isset($tag->attr['form_name']) || $tag->attr['form_name'] === $ci->input->post('form_name'))
		{
			if (isset($tag->attr['is_like']))
			{
				if ((isset($ci->form_validation) && $ci->form_validation->error_string()) && $tag->attr['is_like'] == '1')
					return $tag->expand();
					
				if (!(isset($ci->form_validation) && $ci->form_validation->error_string()) && $tag->attr['is_like'] == '1')
					return '';
					
				if ((isset($ci->form_validation) && $ci->form_validation->error_string()) && $tag->attr['is_like'] == '0')
					return '';
					
				if (!(isset($ci->form_validation) && $ci->form_validation->error_string()) && $tag->attr['is_like'] == '0')
					return $tag->expand();
			}
			else
			{
				if ((isset($ci->form_validation) && $ci->form_validation->error_string()))
					return $tag->expand();
				
				return '';
			}

		}
		if (isset($tag->attr['is_like']) && $tag->attr['is_like'] == '0')
			return $tag->expand();
		
		return '';
	}

	public function error_string($tag)
	{
		$ci =  &get_instance();
		
		$this->check_additional_errors();
		
		return (isset($ci->form_validation) && $ci->form_validation->error_string() ? $ci->form_validation->error_string() : "");
	}

	public function has_noticess($tag)
	{
		$ci =  &get_instance();
		
		if (!isset($tag->attr['form_name']) || $tag->attr['form_name'] === $ci->input->post('form_name'))
		{
			if (isset($tag->attr['is_like']))
			{
				if (!empty($this->additional_notices) && $tag->attr['is_like'] == '1')
					return $tag->expand();
				
				if (empty($this->additional_notices) && $tag->attr['is_like'] == '1')
					return '';
				
				if (!empty($this->additional_notices) && $tag->attr['is_like'] == '0')
					return '';
				
				if (empty($this->additional_notices) && $tag->attr['is_like'] == '0')
					return $tag->expand();
			}
			else
			{
				if (!empty($this->additional_notices))
					return $tag->expand();
				
				return '';
			}
		}
		if (isset($tag->attr['is_like']) && $tag->attr['is_like'] == '0')
			return $tag->expand();
		
		return '';
	}

	public function notice_string($tag)
	{
		$ret = '';
		
		if (!isset($this->additional_notices))
			return '';
		
		foreach ($this->additional_notices as $key => $val)
			$ret .= "<p>".$val."</p>";
		
		return $ret;
	}

	public function has_success($tag)
	{
		$ci =  &get_instance();

		if ( ! isset($tag->attr['form_name']) || $tag->attr['form_name'] === $ci->input->post('form_name'))
		{
			if (isset($tag->attr['is_like']))
			{
				if ( ! empty($this->additional_success) && $tag->attr['is_like'] == '1')
					return $tag->expand();
					
				if (empty($this->additional_success) && $tag->attr['is_like'] == '1')
					return '';
					
				if (!empty($this->additional_success) && $tag->attr['is_like'] == '0')
					return '';
					
				if (empty($this->additional_success) && $tag->attr['is_like'] == '0')
					return $tag->expand();
			}
			else
			{
				if (!empty($this->additional_success))
					return $tag->expand();
				return '';
			}
		}
		
		// Don't check...
		if (isset($tag->attr['is_like']) && $tag->attr['is_like'] == '0')
			return $tag->expand();
		
		return '';
	}

	public function success_string($tag)
	{
		$ret = '';
		
		if (!isset($this->additional_success))
			return '';
		
		foreach ($this->additional_success as $key => $val)
			$ret .= "<p>".$val."</p>";
		
		return $ret;
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
}
