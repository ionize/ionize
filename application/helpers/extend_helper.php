<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if( ! function_exists('get_extend_selected_value'))
{
	function get_extend_selected_value($selected, $field_name, $parent)
	{
		$ci =& get_instance();

		$ci->load->model('extend_field_model');

		$extend = $ci->extend_field_model->get(array(
			'name' => $field_name,
			'parent' => $parent
		));

		if ( ! empty($extend) && ! empty($extend['value']))
		{
			$rows = preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n", $extend['value'])));
			$rows = explode("\n", $rows);

			$values = array();

			foreach($rows as $row)
			{
				$arr = explode(':', $row);
				$values[$arr[0]] = $arr[1];
			}

			if (isset($values[$selected])) return $values[$selected];
		}
		return '';
	}
}

