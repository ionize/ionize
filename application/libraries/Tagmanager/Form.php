<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.9
 *
 */


/**
 * Form TagManager
 *
 */
class TagManager_Form extends TagManager
{
	// Additional errors, success
	public static $additional_errors = array();
	public static $additional_success = array();

	protected static $_inited = FALSE;

	/**
	 * Name of the form which post data
	 *
	 * @var null
	 */
	protected static $posting_form_name = NULL;

	/**
	 * Form validation result array.
	 * Do validation only once
	 *
	 * array(
	 * 		'form_name1' => TRUE,
	 * 		'form_name2 => 	FALSE
	 * );
	 *
	 * @var array
	 *
	 */
	protected static $fvr = array();


	protected static $input_selected_attributes = array
	(
		'radio' => 'checked="true"',
		'checkbox' => 'checked="true"',
		'select' => 'selected="true"',
	);


	public static $tag_definitions = array
	(
		'form' => 								'tag_form',
		'form:field' => 						'tag_expand',
		'form:radio' => 						'tag_expand',
		'form:checkbox' => 						'tag_expand',
		'form:select' => 						'tag_expand',
		'form:error' => 						'tag_expand',

	);


	// ------------------------------------------------------------------------


	/**
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_form(FTL_Binding $tag)
	{
		if ( ! isset(self::$ci->form_validation))
			self::$ci->load->library('form_validation');

		// Init once errors message
		if( ! self::$_inited)
		{
			self::_init_error_messages();
			self::$_inited = TRUE;

			// Posting form name
			if (self::$ci->input->post('form'))
				self::$posting_form_name = self::$ci->input->post('form');

			// Form fields fill / error callbacks
			$forms = config_item('forms');

			// Create dynamic tags regarding the declared forms
			foreach ($forms as $form => $settings)
			{
				self::$context->define_tag('form:'.$form, array(__CLASS__, 'tag_expand'));
				self::$context->define_tag('form:'.$form.':validation', array(__CLASS__, 'tag_expand'));
				self::$context->define_tag('form:'.$form.':validation:result', array(__CLASS__, 'tag_form_validation_result'));
				self::$context->define_tag('form:'.$form.':validation:success', array(__CLASS__, 'tag_form_validation_success'));
				self::$context->define_tag('form:'.$form.':validation:error', array(__CLASS__, 'tag_form_validation_error'));

				self::$context->define_tag('form:field:'.$form, array(__CLASS__, 'tag_expand'));
				self::$context->define_tag('form:radio:'.$form, array(__CLASS__, 'tag_expand'));
				self::$context->define_tag('form:checkbox:'.$form, array(__CLASS__, 'tag_expand'));
				self::$context->define_tag('form:select:'.$form, array(__CLASS__, 'tag_expand'));

				// Fields individual errors
				self::$context->define_tag('form:'.$form.':error', array(__CLASS__, 'tag_expand'));

				// Form refill after error / Fields individual errors : one tag / field
				if ( ! empty($settings['fields']))
				{
					foreach ($settings['fields'] as $field => $field_setting)
					{
						// Field Error string
						self::$context->define_tag('form:'.$form.':error:'. $field, array(__CLASS__, 'tag_form_error_value'));

						// One method / field type
						$type = empty($field_setting['type']) ? 'input' : $field_setting['type'];

						switch($type)
						{
							case 'radio':
								self::$context->define_tag('form:'.$form.':radio:'. $field, array(__CLASS__, 'tag_form_radio_value'));
								break;

							case 'checkbox':
								self::$context->define_tag('form:'.$form.':checkbox:'. $field, array(__CLASS__, 'tag_form_checkbox_value'));
								break;

							case 'select':
								self::$context->define_tag('form:'.$form.':select:'. $field, array(__CLASS__, 'tag_form_select_value'));
								break;

							default:
								self::$context->define_tag('form:'.$form.':field:'. $field, array(__CLASS__, 'tag_form_field_value'));
						}
					}
				}
			}
		}

		return $tag->expand();
	}


	// ------------------------------------------------------------------------


	/**
	 * Return one form field value
	 * Used to fill again the form after submit if validation fails.
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return mixed|string
	 *
	 * @usage	<ion:form:(form_name):field:(field_name) />
	 *
	 * 			Example with the form called 'register' and the input called 'firstname' :
	 * 			<ion:form:register:field:firstname />
	 */
	public static function tag_form_field_value(FTL_Binding $tag)
	{
		$form_name = $tag->getParent('field')->getParentName();

		// Try to get the "form" tag parent
		$data_parent = $tag->getParent('form')->getParent();

		// Default return value
		$default = $tag->getAttribute('default');

		if ( ! is_null($form_name))
		{
			// The form was posted
			if ($form_name == self::$posting_form_name)
			{
				return self::$ci->input->post($tag->name);
			}
			// No post data : try to get the field from from tag parent
			if (is_object($data_parent))
			{
				return $tag->getValue($tag->name, $data_parent->name);
			}
		}
		if (!is_null($default))
			return $default;

		return '';
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns 'checked="true"' if the radiobox has to be checked
	 *
	 * @param 	FTL_Binding $tag
	 *
	 * @return 	string
	 *
	 * @usage	In this example :
	 *			- the form name is "profile"
	 * 			- the radio name is "gender"
	 *			<input type="radio" name="gender" value="1" <ion:user:form:profile:radio:gender value="1" default="true" /> />
	 *			<input type="radio" name="gender" value="2" <ion:user:form:profile:radio:gender value="2" /> />
	 *			<input type="radio" name="gender" value="3" <ion:user:form:profile:radio:gender value="3" /> />
	 *
	 */
	public static function tag_form_radio_value(FTL_Binding $tag)
	{
		$form_name = $tag->getParent('radio')->getParentName();
		return self::get_form_selected_attribute($tag, $form_name, 'radio');
	}


	// ------------------------------------------------------------------------

	/**
	 * Returns 'checked="true"' if the checkbox has to be checked
	 *
	 * @param 	FTL_Binding $tag
	 *
	 * @return 	string
	 *
	 * @usage	In this example :
	 *			- the form name is "profile"
	 * 			- the checkboxes name is "gender"
	 *			<input type="checkbox" name="gender[]" value="1" <ion:user:form:profile:checkbox:gender value="1" default="true" /> />
	 *			<input type="checkbox" name="gender[]" value="2" <ion:user:form:profile:checkbox:gender value="2" /> />
	 *			<input type="checkbox" name="gender[]" value="3" <ion:user:form:profile:checkbox:gender value="3" /> />
	 *
	 */
	public static function tag_form_checkbox_value(FTL_Binding $tag)
	{
		$form_name = $tag->getParent('checkbox')->getParentName();
		return self::get_form_selected_attribute($tag, $form_name, 'checkbox');
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns 'selected="true"' if the option has to be selected
	 *
	 * @param 	FTL_Binding $tag
	 *
	 * @return 	string
	 *
	 * @usage	In this example :
	 *			- the form name is "profile"
	 * 			- the select name is "gender", it can be multiple
	 * 			<select type="select" name="gender[]" multiple="true">
	 *				<option value="1" <ion:user:form:profile:select:gender value='1' />>Male</option>
	 *				<option value="2" <ion:user:form:profile:select:gender value='2'  default='true'/>>Female</option>
	 *				<option value="3" <ion:user:form:profile:select:gender value='3' />>I don't know</option>
	 *			</select>
	 */
	public static function tag_form_select_value(FTL_Binding $tag)
	{
		$form_name = $tag->getParent('select')->getParentName();
		return self::get_form_selected_attribute($tag, $form_name, 'select');
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns, depending on the type of the field, the corresponding HTML tag attribute
	 * mandatory to decide if the data has to be selected or not.
	 *
	 * @param FTL_Binding $tag
	 * @param             $form_name
	 * @param             $type
	 *
	 * @return string
	 *
	 */
	protected static function get_form_selected_attribute(FTL_Binding $tag, $form_name, $type)
	{
		// Try to get the "form" tag parent
		$data_parent = $tag->getParent('form')->getParent();

		// Is this value the default one ?
		$default = $tag->getAttribute('default');

		// Value of the selectable (radio, select, checkbox)
		$value = $tag->getAttribute('value');

		if ( ! is_null($form_name))
		{
			$found_value = NULL;

			// The form was posted
			if ($form_name == self::$posting_form_name)
			{
				$found_value = self::$ci->input->post($tag->name);

				// Multiple data : checkboxes or select multiple
				if (is_array($found_value))
				{
					foreach($found_value as $val)
					{
						if ($value == $val)
						{
							$found_value = $val;
							break;
						}
					}
				}
			}
			else
			{
				// Try to get one stored value from the parent tag
				if (is_object($data_parent))
				{
					$found_value = $tag->getValue($tag->name, $data_parent->name);

					// Correct multiple data in the same field
					// This has no impact if IDs are stored in one relation table.
					if ($type == 'checkbox' OR $type == 'select')
					{
						$found_value = explode(',', $found_value);
						foreach ($found_value as $val)
						{
							if ($value == $val)
							{
								$found_value = $val;
								break;
							}
						}
					}
					// Correct if no value was found in the array
					if (is_array($found_value)) $found_value = NULL;
				}

				// If default is set, Set the default value if nothing was found
				if ( ! $found_value && ! is_null($default))
					$found_value = $value;
			}

			if ($value == $found_value)
				return self::$input_selected_attributes[$type];
		}
		return '';
	}


	// ------------------------------------------------------------------------


	/**
	 * Return one single form field error
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 * @usage	Output mode :
	 * 			<ion:form:form_name:error:field_name />
	 *
	 * 			Conditional mode :
	 * 			<ion:form:form_name:error:field_name is="true">
	 * 				... this will be displayed ...
	 * 			</ion:form:form_name:error:field_name>
	 *
	 * 			Example with the form called register and the input called 'firstname' :
	 * 			<ion:form:register:error:firstname is='true'>
	 * 				... this will be displayed ...
	 * 			</ion:form:register:error:firstname>
	 *
	 * 			Example which return the asked value in case of error :
	 * 			<ion:form:register:error:firstname is='true' return='class="error"'>
	 *
	 */
	public static function tag_form_error_value(FTL_Binding $tag)
	{
		$form_name = $tag->getParent('error')->getParentName();

		if ( ! is_null($form_name))
		{
			if ($form_name == self::$posting_form_name)
			{
				// Validate or get the validation result
				self::validate($form_name);

				if ( ! empty(self::$ci->form_validation->_error_array[$tag->name]))
				{
					if ($tag->getAttribute('is') === TRUE)
					{
						// Return the value asked by the "return" attribute
						if ( ! is_null($return = $tag->getAttribute('return')))
							return self::wrap($tag, $return);

						// or expand the tag
						return $tag->expand();
					}
					else
						return self::output_value($tag, self::$ci->form_validation->_error_array[$tag->name]);
				}
			}
		}
		return '';
	}


	// ------------------------------------------------------------------------


	/**
	 * @param 	FTL_Binding
	 *
	 * @return 	string
	 *
	 * @usage	<ion:form_name:validation:result [is="true/false"] >
	 * 				...
	 * 			</ion:form_name:validation:result>
	 *
	 */
	public static function tag_form_validation_result(FTL_Binding $tag)
	{
		$form_name = $tag->getParent('validation')->getParentName();
		$is = $tag->getAttribute('is', TRUE);

		// Validate only if the $form is posted
		if ($form_name == self::$posting_form_name)
		{
			$result = self::validate($form_name);
		}
		// Consider the validation fails (or wasn't done)
		else
		{
			$result = FALSE;
		}

		if ($result == $is)
			return self::wrap($tag, $tag->expand());

		return '';

	}


	// ------------------------------------------------------------------------


	/**
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 * @usage	<ion:form_name:validation:success [is="true/false"] >
	 * 				...
	 * 			</ion:form_name:validation:success>
	 */
	public static function tag_form_validation_success(FTL_Binding $tag)
	{
		$is = $tag->getAttribute('is');
		$key = $tag->getAttribute('key');

		// Old way : Needs the attribute form="form_name"
		// $form_name = self::_get_form_name($tag);

		// New way
		$form_name = $tag->getParent('validation')->getParentName();

		if (self::$posting_form_name == $form_name)
		{

			self::validate(self::$posting_form_name);

			// Expand mode
			if ( ! is_null($is))
			{
				// No key ask : Check the additional success array
				if (is_null($key))
				{
					if ( ! $is == empty(self::$additional_success))
						return self::wrap($tag, $tag->expand());
				}
				else
				{
					if ( ! $is == empty(self::$additional_success[$key]))
						return self::wrap($tag, $tag->expand());
				}
			}
			// Return mode
			else
			{
				if (is_null($key))
				{
					if ( ! empty(self::$additional_success))
					{
						$str = '';
						foreach (self::$additional_success as $val)
							$str .= "<span>".$val."</span>";

						return self::wrap($tag, $str);
					}
				}
				else
				{
					if ( ! empty(self::$additional_success[$key]))
					{
						return self::wrap($tag, self::$additional_success[$key]);
					}
				}
			}
		}

		return '';
	}


	// ------------------------------------------------------------------------


	/**
	 * Displays the complete error string
	 * (containing all errors)
	 *
	 * @param 	FTL_Binding
	 *
	 * @return 	string
	 *
	 * @usage	<ion:form:form_name:validation:error />
	 *
	 * 			Example with the form called 'profile' :
	 * 			<ion:form:profile:validation:error />
	 *
	 */
	public static function tag_form_validation_error(FTL_Binding $tag)
	{
		$is = $tag->getAttribute('is');
		$key = $tag->getAttribute('key');

		$form_name = $tag->getParent('validation')->getParentName();

		// Add potential additional errors to the CI validation error string
		$has_errors = self::check_additional_errors($form_name);

		if (self::$posting_form_name == $form_name)
		{
			self::validate(self::$posting_form_name);

			// Expand mode
			if ( ! is_null($is))
			{
				// Global errors check
				if (is_null($key))
				{
					if ( $is == $has_errors)
						return self::wrap($tag, $tag->expand());
				}
				// Check one given error
				else
				{
					if ( ! empty(self::$ci->form_validation->_error_array[$key]))
						return self::wrap($tag, $tag->expand());
				}
			}
			// Value return mode
			else
			{
				// Global errors check
				if (is_null($key))
				{
					// Remove the default <p> tag around each
					self::$ci->form_validation->set_error_delimiters('<span>', '</span>');
					if ($string = self::$ci->form_validation->error_string())
						return self::output_value($tag, $string);
				}
				else
				{
					if ( ! empty(self::$ci->form_validation->_error_array[$key]))
					{
						return self::output_value($tag, self::$ci->form_validation->_error_array[$key]);
					}
				}
			}
		}
		// No posted form : No errors
		if ($is == FALSE)
			return $tag->expand();

		return '';
	}


	// ------------------------------------------------------------------------


	/**
	 * Runs the Form validation of the given form name, as defined in the config file.
	 *
	 * @param	string	Form name
	 * @return	string	Expanded tag or not
	 *
	 */
	public static function validate($form_name)
	{
		// Load Validation because this method can be from outside the "<ion:form />" tag
		if ( ! isset(self::$ci->form_validation))
			self::$ci->load->library('form_validation');

		$form = self::get_form_settings($form_name);

		// Do not validate the form if the form is not defined
		if (is_null($form))
		{
			self::$fvr[$form_name] = FALSE;
			return FALSE;
		}

		// If rules are defined in the config file...
		if ( ! isset(self::$fvr[$form_name]) && isset($form['fields']))
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
					self::$ci->form_validation->set_rules($field, $label, $rules);

					// User's callback rules
					// Callbacks rules cannot be executed by CI_Form_validation()
					// They are supposed to be $CI methods and we are here out of the scope of $CI
					$rules_array = explode('|', $rules);

					foreach($rules_array as $rule)
					{
						if (substr($rule, 0, 9) == 'callback_')
						{
							$row = array(
								'field' => $field,
								'label' => $label,
								'rule' => $rule,
								'post' => self::$ci->input->post($field),
							);
							self::execute_validation_callback($row);
						}
					}
				}
			}

			// Check the rules
			if (self::$ci->form_validation->run())
			{
				if (self::check_additional_errors($form_name))
					self::$fvr[$form_name] = FALSE;
				else
					self::$fvr[$form_name] = TRUE;
			}
			else
				self::$fvr[$form_name] = FALSE;
		}
		// No fields defined but form declared : Validation OK
		if ( ! isset(self::$fvr[$form_name]))
			self::$fvr[$form_name] = TRUE;

		return self::$fvr[$form_name];
	}


	// ------------------------------------------------------------------------


	/**
	 * TO CHECK !!!!!
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

//		if ( method_exists($this, $callback))
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


	/**
	 * @param $key
	 * @param $val
	 *
	 */
	public static function set_additional_error($key, $val)
	{
		self::$additional_errors[$key] = $val;
	}


	// ------------------------------------------------------------------------


	/**
	 * Sets one additional succes message
	 *
	 * @param $key
	 * @param $val
	 *
	 */
	public static function set_additional_success($key, $val)
	{
		self::$additional_success[$key] = $val;
	}


	// ------------------------------------------------------------------------


	/**
	 * Checks the additional errors
	 * Orders the errors, as additional errors aren't in the fields config file order
	 *
	 * @param string	Form name
	 *
	 * @return bool
	 *
	 */
	public static function check_additional_errors($form_name)
	{
		// Add Additional errors to the CI validation errors array
		foreach (self::$additional_errors as $key => $val)
			self::$ci->form_validation->_error_array[$key] = $val;

		if (sizeof(self::$ci->form_validation->_error_array) > 0)
		{
			// Reorder errors regarding the $config definition
			// Additional errors does not respect the rules order...
			$fields = self::get_form_fields($form_name, TRUE);

			$result = array();
			foreach ($fields as $field => $settings)
			{
				if (isset(self::$ci->form_validation->_error_array[$field]))
					$result[$field] = self::$ci->form_validation->_error_array[$field];
			}
			// Add custom errors (not in form fields) at the end
			foreach (self::$ci->form_validation->_error_array as $key => $value)
				if ( ! in_array($key, array_keys($result)))
					$result[$key] = $value;

			self::$ci->form_validation->_error_array = $result;

			// Cleaning
			self::$additional_errors = array();
			return TRUE;
		}

		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the config settings for one form name
	 *
	 * @param $form_name
	 *
	 * @return null
	 */
	public static function get_form_settings($form_name)
	{
		$forms = config_item('forms');
		$form = isset($forms[$form_name]) ? $forms[$form_name] : NULL;

		return $form;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the array of form fields, as set in the forms config file
	 *
	 * @param string	$form_name
	 * @param bool 		$all
	 *
	 * @return array
	 *
	 */
	public static function get_form_fields($form_name, $all = FALSE)
	{
		$form = self::get_form_settings($form_name);

		$fields = array();

		if (is_null($form)) return $fields;

		if ($all == TRUE)
			$fields = array_keys($form['fields']);
		else
		{
			foreach ($form['fields'] as $key => $field)
				if (!isset($field['save']) OR $field['save'] != FALSE)
					$fields[] = $key;
		}

		return $fields;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the emails settings of one form, as set in the forms config file.
	 * @param $form_name
	 *
	 * @return array
	 *
	 */
	public static function get_form_emails($form_name)
	{
		$form = self::get_form_settings($form_name);

		$emails = array();

		if (is_null($form)) return $emails;

		$emails = ! empty($form['emails']) ? $form['emails'] : array();

		return $emails;
	}


	// ------------------------------------------------------------------------


	/**
	 * Init errors messages
	 * Replace the CI native error messages by the one set in
	 * language/xx/form_lang.php
	 *
	 */
	private static function _init_error_messages()
	{
		$rules = array(
			'required', 'isset', 'valid_email', 'valid_emails',
			'valid_url', 'valid_ip', 'min_length', 'max_length', 'exact_length',
			'alpha', 'alpha_numeric', 'alpha_dash', 'numeric', 'is_numeric',
			'integer', 'matches', 'is_natural', 'is_natural_no_zero'
		);

		foreach($rules as $rule)
			self::$ci->form_validation->set_message($rule, lang('form_error_' . $rule));
	}
}