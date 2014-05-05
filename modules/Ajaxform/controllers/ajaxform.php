<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajaxform extends My_Module
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->library('email');
	}

	/**
	 * Do nothing.
	 */
	public function index(){}


	public function post()
	{
		// Validation result
		$result = array(
			'validation' => FALSE
		);

		// Form name
		$form_name = $this->input->post('form_name');

		// Form settings
		$form = $this->_get_form_settings($form_name);

		// Do not validate the form if the form is not defined
		if (is_null($form))
		{
			$this->xhr_output(array());
		}

		// If rules are defined in the config file...
		if ( isset($form['fields']))
		{
			$fields = $form['fields'];

			// Get each field settings
			foreach ($fields as $field => $settings)
			{
				if (isset($settings['rules']))
				{
					$rules = $settings['rules'];
					$label = ! empty($settings['label']) ? 'lang:'.$settings['label'] : $field;

					// See : http://codeigniter.com/user_guide/libraries/form_validation.html#translatingfn
					$this->form_validation->set_rules($field, $label, $rules);

					// User's callback rules
					// Callbacks rules cannot be executed by CI_Form_validation()
					// They are supposed to be $CI methods and we are here out of the scope of $CI
					/*
					 * Not implemented for the moment
					 * @todo: execute_validation_callback() needs to be rewritten

					$rules_array = explode('|', $rules);

					foreach($rules_array as $rule)
					{
						if (substr($rule, 0, 9) == 'callback_')
						{
							$row = array(
								'field' => $field,
								'label' => $label,
								'rule' => $rule,
								'post' => $this->input->post($field),
							);
							$this->execute_validation_callback($row);
						}
					}
					*/
				}
			}

			// Check the rules
			$validation_passed = $this->form_validation->run();

			// Error
			if ( ! $validation_passed)
			{
				$result['title'] = lang('form_alert_error_title');
				$result['message'] = lang('form_alert_error_message');
				$result['errors'] = $this->form_validation->_error_array;
			}
			// Validation passed : Process the data
			else
			{
				// Supposed to send back one array with 'title' and 'message' indexes
				$result = $this->_process_data($form);

				$result['validation'] = TRUE;

				if ( ! isset($result['title']) && ! isset($result['message']))
				{
					$result['title'] = lang('form_alert_success_title');
					$result['message'] = lang('form_alert_success_message');
				}
			}
		}

		$this->xhr_output($result);
	}


	// ------------------------------------------------------------------------


	protected function _process_data($form)
	{
		// Process Class & method
		$process =  ! empty($form['process']) ? $form['process'] : NULL;

		// Load class
		if ( ! is_null($process))
		{
			$arr = explode('::', $process);

			// Load the library
			$class_name = $arr[0];
			$method_name = $arr[1];
			$this->load->library($class_name);

			// Execute the method
			if (method_exists($this->$class_name, $method_name))
			{
				return call_user_func($process, $form);
			}
		}
		// Default behavior : Send content by email
		else
		{
			$this->email->send_form_emails($form);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * @TODO : TO TEST and correct if needed !!!!!
	 *
	 * @param $row
	 *
	 */
	public static function execute_validation_callback($row)
	{
		$param = $callback = NULL;

		if (preg_match("/(.*?)\[(.*)\]/", $row['rule'], $match))
		{
			$callback	= $match[1];
			$param	= $match[2];
		}

		if ( ! is_null($callback) && method_exists('self', $callback))
		{
			$result = self::$callback($row['post'], $param);

			if ($result == FALSE)
			{
				$line = lang('form_error_' . $row['field']);
				$message = sprintf($line, lang($row['label']), $param);

				self::set_additional_error($row['field'], $message);
			}
		}
	}


	// ------------------------------------------------------------------------



	protected function _validate($form_name)
	{

	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the config settings for one form name
	 *
	 * @param $form_name
	 *
	 * @return null
	 */
	protected function _get_form_settings($form_name = NULL)
	{
		// Get forms settings
		$forms = config_item('forms');

		if (is_file($file = Theme::get_theme_path().'config/forms.php'))
		{
			include($file);

			if ( ! empty($config['forms']))
			{
				$forms = array_merge($forms, $config['forms']);
				unset($config);
			}
		}

		if ( ! is_null($form_name) && isset($forms[$form_name]))
			return $forms[$form_name];

		return NULL;
	}
}
