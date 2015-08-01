<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dumps the complete extend field definitions as XML of MySql Queries
 *
 * @return string
 */
function get_content_configurations_xml()
{
	$CI =& get_instance();

	$xml = '<?xml version="1.0" ?>' . "\r\n";
	$xml .= "\r\n";

	// article types
	$xml .= get_xml_from_table_records($CI, 'article_type', array(), '  ');
	$xml .= "\r\n";

	// content elements
	$xml .= get_xml_from_table_records($CI, 'element', array(), '  ');
	$xml .= "\r\n";
	$xml .= get_xml_from_table_records($CI, 'element_definition', array(), '  ');
	$xml .= "\r\n";
	$xml .= get_xml_from_table_records($CI, 'element_definition_lang', array(), '  ');
	$xml .= "\r\n";

	// extend fields
	$xml .= get_xml_from_table_records($CI, 'extend_field', array(), '  ');
	$xml .= "\r\n";
	$xml .= get_xml_from_table_records($CI, 'extend_field_lang', array(), '  ');
	$xml .= "\r\n";
	$xml .= get_xml_from_table_records($CI, 'extend_field_type', array(), '  ');
	$xml .= "\r\n";

	return $xml;
}

/**
 * @param        $CI
 * @param        $table_name
 * @param array  $ignore_keys
 * @param string $prefix
 * @return string
 */
function get_xml_from_table_records($CI, $table_name, $ignore_keys = array(), $prefix = '')
{
	$xml = "<{$table_name}s>\r\n";
	$xml .= "\r\n";

	$query = $CI->db->get($table_name);

	foreach( $query->result() as $row ) {
		$xml .= $prefix . "<{$table_name}>\r\n";
		$xml .= result_array_items_to_xml( $row, $ignore_keys, $prefix . '  ' );
		$xml .= $prefix . "</{$table_name}>\r\n";
		$xml .= "\r\n";
	}

	$xml .= "</{$table_name}s>\r\n";

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

/* End of file dump_content_definitions_helper.php */
/* Location: .application/helpers/dump_content_definitions_helper.php */