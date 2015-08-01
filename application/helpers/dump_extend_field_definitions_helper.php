<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dumps the complete extend field definitions as XML of MySql Queries
 *
 * @return string
 */
function get_extend_field_definitions()
{
	$xml = '<?xml version="1.0" ?>' . "\r\n";
	$xml .= "\r\n";

	$CI =& get_instance();

	$xml .= '<extend_fields>' . "\r\n";
	$xml .= "\r\n";

	$query = $CI->db->get('extend_field');

	$ignore_keys = array('id_extend_field');
	foreach ($query->result() as $row)
	{
		$xml .= '  <extend_field>' . "\r\n";
		$xml .= result_array_items_to_xml($row, $ignore_keys, '    ');
		$xml .= '  </extend_field>' . "\r\n";
		$xml .= "\r\n";
	}

	$xml .= '</extend_fields>' . "\r\n";

	return $xml;
}

/**
 * @param	stdClass	$result_array
 * @param	array		$ignore_keys
 * @param	string		$prefix
 * @return	string
 */
function result_array_items_to_xml($result_array, $ignore_keys = array(), $prefix = '')
{
	$xml = '';
	foreach($result_array as $key => $value)
	{
		$xml .= $prefix . "<$key>" . $value . "</$key>\r\n";
	}

	return $xml;
}

/* End of file dump_extend_field_definitions_helper.php */
/* Location: .application/helpers/dump_extend_field_definitions_helper.php */