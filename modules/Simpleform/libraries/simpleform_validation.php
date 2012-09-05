<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Simpleform_Validation {

	public $CI;

	protected $_config;

	// Additional errors, that are added to the error-string besides form_validation-errors
	public $additional_err = array();
	public $additional_notices = array();
	public $additional_success = array();


	function __construct()
    {
		$this->CI =  &get_instance();
		
		if (!isset($this->CI->form_validation))
			$this->CI->load->library('form_validation');

		$config = array();
		include MODPATH . 'Simpleform/config/config.php';
		$this->_config = $config;

		$this->_init_error_messages();
	}

	public function has_errors($tag)
	{
		$this->check_additional_errors();

		if (!isset($tag->attr['form_name']) || $tag->attr['form_name'] === $this->CI->input->post('form_name'))
		{
			if (isset($tag->attr['is_like']))
			{
				if ((isset($this->CI->form_validation) && $this->CI->form_validation->error_string()) && $tag->attr['is_like'] == '1')
					return $tag->expand();
					
				if (!(isset($this->CI->form_validation) && $this->CI->form_validation->error_string()) && $tag->attr['is_like'] == '1')
					return '';
					
				if ((isset($this->CI->form_validation) && $this->CI->form_validation->error_string()) && $tag->attr['is_like'] == '0')
					return '';
					
				if (!(isset($this->CI->form_validation) && $this->CI->form_validation->error_string()) && $tag->attr['is_like'] == '0')
					return $tag->expand();
			}
			else
			{
				if ((isset($this->CI->form_validation) && $this->CI->form_validation->error_string()))
					return $tag->expand();
				
				return '';
			}

		}
		if (isset($tag->attr['is_like']) && $tag->attr['is_like'] == '0')
			return $tag->expand();
		
		return '';
	}

	public function error_string(FTL_Binding $tag)
	{
		$form_name = $tag->getAttribute('form_name');
		$this->check_additional_errors($form_name);
		
		return (isset($this->CI->form_validation) && $this->CI->form_validation->error_string() ? $this->CI->form_validation->error_string() : "");
	}

	public function has_notices(FTL_Binding $tag)
	{
		if (!isset($tag->attr['form_name']) || $tag->attr['form_name'] === $this->CI->input->post('form_name'))
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

	public function notice_string(FTL_Binding $tag)
	{
		$ret = '';
		
		if (!isset($this->additional_notices))
			return '';
		
		foreach ($this->additional_notices as $key => $val)
			$ret .= "<p>".$val."</p>";
		
		return $ret;
	}

	public function has_success(FTL_Binding $tag)
	{
		if ( ! isset($tag->attr['form_name']) || $tag->attr['form_name'] === $this->CI->input->post('form_name'))
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

	public function check_additional_errors($form_name = NULL)
	{
		foreach ($this->additional_err as $key => $val)
			$this->CI->form_validation->_error_array[$key] = $val;
		
		if (sizeof($this->CI->form_validation->_error_array) > 0)
		{
			// Reorder errors regarding the $config definition
			// Additional errors does not respect the rules order...
			$rules = $this->get_config_item('simpleform_' . $form_name);
			if ( ! is_null($rules))
			{
				$result = array();
				foreach ($rules as $key => $rule)
				{
					if (isset($this->CI->form_validation->_error_array[$key]))
						$result[$key] = $this->CI->form_validation->_error_array[$key];
				}
				$this->CI->form_validation->_error_array = $result;
			}

			// Cleaning
			unset($this->additional_err);
			$this->additional_err = array();
			return TRUE;
		}
		
		return FALSE;
	}

	public function set_additional_error($key, $val)
	{
		$this->additional_err[$key] = $val;
		return $this;
	}

	public function set_additional_success($key, $val)
	{
		$this->additional_success[$key] = $val;
		return $this;
	}

	private function _init_error_messages()
	{
		$rules = array(
			'required', 'isset', 'valid_email', 'valid_emails',
			'valid_url', 'valid_ip', 'min_length', 'max_length', 'exact_length',
			'alpha', 'alpha_numeric', 'alpha_dash', 'numeric', 'is_numeric',
			'integer', 'matches', 'is_natural', 'is_natural_no_zero'
		);
		foreach($rules as $rule)
			$this->CI->form_validation->set_message($rule, lang('module_simpleform_error_' . $rule));
	}

	/**
	 * Runs the Form validation of the given form name, as defined in the config file.
	 *
	 * $config['simpleform_xxx_fields'] = array( [rules] );
	 * xxx : must be the form name
	 *
	 */
	public function run($form_name)
	{
		// If rules are defined in the config file...
		if ($form_fields = $this->get_config_item('simpleform_' . $form_name))
		{
			// Set the rules
			foreach ($form_fields as $field_name => $rules)
			{
				$this->CI->form_validation->set_rules($field_name, "lang:module_simpleform_field_".$field_name, $rules);

				if (!($rules['default_value'] === FALSE))
					if ($this->CI->input->post($field_name) === FALSE)
						if (!$rules['special_field'] === 'checkbox') // Because of Checkboxes
							$_POST[$field_name] = $rules['default_value'];

				if ($this->CI->input->post($field_name) === FALSE)
					$_POST[$field_name] = '';

				if ($this->CI->input->post($field_name) === "on" && $rules['special_field'] === "checkbox")
					$_POST[$field_name] = '1';

				// User's callback rules
				// Callbacks rules cannot be executed by CI_Form_validation()
				// because they are supposed to be $CI methods and we are here out of the scope of $CI
				$rules_array = explode('|', $rules);
				foreach($rules_array as $rule)
				{
					if (substr($rule, 0, 9) == 'callback_')
					{
						$row = array(
							'field' => $field_name,
							'label' => 'module_simpleform_field_'.$field_name,
							'rule' => $rule,
							'postdata' => $this->CI->input->post($field_name)
						);
						$this->execute_callback($row);
					}
				}
			}

			// Check the rules
			if ($this->CI->form_validation->run())
			{
				if ($this->check_additional_errors())
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
	 * Called by $this->run()
	 *
	 * @param $row
	 *
	 */
	public function execute_callback($row)
	{
		$param = $callback = FALSE;
		if (preg_match("/(.*?)\[(.*)\]/", $row['rule'], $match))
		{
			$callback	= $match[1];
			$param	= $match[2];
		}

		if ( method_exists($this, $callback))
		{
			$result = $this->$callback($row['postdata'], $param);

			if ($result == FALSE)
			{
				$line = lang('module_simpleform_error_' . $row['field']);
				$message = sprintf($line, lang($row['label']), $param);

				$this->set_additional_error($row['field'], $message);
			}
		}
	}


	/**
	 * Return one module's config item or NULL if it is not set.
	 *
	 * @param $key
	 *
	 * @return mixed / null
	 *
	 */
	public function get_config_item($key)
	{
		if (isset($this->_config[$key]))
			return $this->_config[$key];

		return NULL;
	}


	// -----------------------------------------------------------------------------------------
	/**
	 * User's callback validation methods
	 * Put your validation method here
	 *
	 */


	/**
	 * If the value of the form is the form label, return FALSE
	 *
	 * @param String
	 * @param String	Translation item key
	 *
	 * @return boolean
	 *
	 */
	public function callback_label($post_data, $label_lang_key)
	{
		if ($post_data == lang($label_lang_key))
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 *
	 *
	 */
	private function callback_antispam($post_data)
	{
		if ($post_data != config_item('form_antispam_key'))
		{
			return FALSE;
		}
		return TRUE;
	}


}
