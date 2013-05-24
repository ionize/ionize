<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Form Helper
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 *
 */


// ------------------------------------------------------------------------


/**
 * Builds one form field from one DB field
 * see : Base_model::field_data()
 *
 * @access	public
 * @param	array	The DB field, as set by Base_model::field_data() :
 *					array(
 *			            'field' =>      'Field name'
 *					    'type' =>       'DB field type' (int, tinyint, varchar, ...)
 *					    'null' =>       TRUE / FALSE
 *					    'key' =>        'PRI|MUL'
 *					    'extra' =>      column extra
 *					    'comment' =>    column comment
 *					    'privileges' => column privileges
 *					    'length' =>     int / array (in case of ENUM)
 *					)
 * @param   string      Type of the field (select, multiselect, input, radio, checkbox, textarea, date, html)
 * @param   array       Array of values=>labels. Used for the type 'select', multiselect', 'radio', 'checkbox'
 * @return	string
 *
 */
if ( ! function_exists('form_build_field'))
{
	function form_build_field($field, $html_type=NULL, $data=NULL, $extra='')
	{
		$field_name = $field['field'];
		$field_type = $field['type'];
		$field_value = $field['value'];

		$extra = $extra . ' id="'.$field_name.'"';

		// Define the HTML type to use from DB field
		// Limited, as the DB types cannot be translated to HTML form types
		if (is_null($html_type))
		{
			switch ($field_type)
			{
				case 'tinyint':
				case 'smallint':
					// Checkbox (on / off)
					if ($field['length'] == 1)
					{
						$html_type = "checkbox";
					}
					// Input
					else
					{
						$html_type = "input";
					}
					break;

				case 'float':
				case 'int':
				case 'char':
				case 'tinytext':
					$html_type = "input";
					break;

				case 'varchar':
				case 'text':
				case 'longtext':
					if ($field_type == 'varchar' && $field['length'] < 255)
						$html_type = "input";
					elseif ($field_type == 'varchar' && $field['length'] < 3000)
						$html_type = "textarea";
					else
						$html_type = "html";

					break;

				case 'enum':
					$html_type = "radio";
					break;

				case 'date':
				case 'datetime':
					$html_type = "date";
					break;

				default:
					$html_type = "input";
			}
		}

		// No data provided
		if ( ! is_array($data))
		{
			$data = array();

			// Try to get the ENUM possible values
			if ($field_type == 'enum')
			{
				foreach($field['length'] as $enum)
					$data[$enum] = $enum;
			}
		}

		switch ($html_type)
		{
			// Select
			// $data is supposed to be an array of keys => displayed labels
			case 'select':
				$extra .= ' class="select"';
				return form_dropdown($field_name, $data, $field_value, $extra);
				break;

			case 'multiselect':
				$extra .= ' class="select multiselect"';
				$selected = explode(',', $field_value);
				$field_name = $field_name.'[]';
				return form_multiselect($field_name, $data, $selected, $extra);

				break;

			case 'input':
				$extra .= ' class="inputtext"';
				return form_input($field_name, $field_value, $extra);
				break;

			case 'textarea':
				$extra .= ' class="textarea"';
				return form_textarea($field_name, $field_value, $extra);
				break;

			// Text Editing textarea (tinyMCE in this case)
			case 'html':
				$extra .= ' class="tinyTextarea"';
				return form_textarea($field_name, $field_value, $extra);
				break;

			case 'checkbox':
				$extra = ' class="checkbox"';
				$str = '';
				$print_label = TRUE;

				// We consider the checkbox only validate one data, which should be set at 1 or 0.
				if (empty($data))
				{
					$data = array(1 => '');
					$print_label = FALSE;
				}

				foreach($data as $value => $label)
				{
					$id = 'c_'.uniqid();
					$extra2 = $extra . ' id="'.$id.'"';
					$checked = ($field_value == $value) ? TRUE : FALSE;
					if ($print_label === TRUE)
						$str .= form_label($label, $id);
					$str .= form_checkbox($field_name, $value, $checked, $extra2);
				}
				return $str;
				break;

			case 'radio':
				$extra = ' class="radio"';
				$str = '';
				$print_label = TRUE;

				// We consider the checkbox only validate one data, which should be set at 1 or 0.
				if (empty($data))
				{
					$data = array(1 => '');
					$print_label = FALSE;
				}

				foreach($data as $value => $label)
				{
					$id = 'r_'.uniqid();
					$extra2 = $extra . ' id="'.$id.'"';
					$checked = ($field_value == $value) ? TRUE : FALSE;
					if ($print_label === TRUE)
						$str .= form_label($label, $id);
					$str .= form_radio($field_name, $value, $checked, $extra2);
				}
				return $str;
				break;

			case 'date':
				$extra .= ' class="inputtext date"';

				if ($field['type'] == 'datetime')
					$field_value = humanize_mdate($field_value, Settings::get('date_format'). ' %H:%i:%s');
				else
					$field_value = humanize_mdate($field_value, Settings::get('date_format'));

				return form_input($field_name, $field_value, $extra);
				break;

			default:
				$extra .= ' class="inputtext"';
				return form_input($field_name, $field_value, $extra);
		}
	}
}
