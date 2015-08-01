<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dumps the complete extend field definitions as MySql Queries
 *
 * @return string
 */
function get_content_configurations()
{
	$CI =& get_instance();

	$sql = '';

	// article types
	$sql .= get_insert_query_from_table_records($CI, 'article_type');
	$sql .= "\r\n";

	// content elements
	$sql .= get_insert_query_from_table_records($CI, 'element');
	$sql .= "\r\n";
	$sql .= get_insert_query_from_table_records($CI, 'element_definition');
	$sql .= "\r\n";
	$sql .= get_insert_query_from_table_records($CI, 'element_definition_lang');
	$sql .= "\r\n";

	// extend fields
	$sql .= get_insert_query_from_table_records($CI, 'extend_field');
	$sql .= "\r\n";
	$sql .= get_insert_query_from_table_records($CI, 'extend_field_lang');
	$sql .= "\r\n";
	$sql .= get_insert_query_from_table_records($CI, 'extend_field_type');
	$sql .= "\r\n";

	// static items
	$sql .= get_insert_query_from_table_records($CI, 'item');
	$sql .= "\r\n";
	$sql .= get_insert_query_from_table_records($CI, 'item_definition');
	$sql .= "\r\n";
	$sql .= get_insert_query_from_table_records($CI, 'item_definition_lang');
	$sql .= "\r\n";
	$sql .= get_insert_query_from_table_records($CI, 'item_lang');
	$sql .= "\r\n";
	$sql .= get_insert_query_from_table_records($CI, 'items');
	$sql .= "\r\n";

	return $sql;
}

/**
 * @param        $CI
 * @param        $table_name
 * @param array  $ignore_keys
 * @param string $prefix
 * @return string
 */
function get_insert_query_from_table_records($CI, $table_name, $addCreate = true)
{
	$query	= $CI->db->get($table_name);
	$records= $query->result_array();

	$sql  = $addCreate ? get_create_query_by_table_name($CI, $table_name) : '';

	if( count($records) > 0 ) {
		$sql .= "INSERT INTO `$table_name` VALUES\r\n";

		$lastRowIndex = count($records) - 1;
		foreach($records as $indexRecord => $record) {
			$record = array_values((array) $record);

			$sql .= '  (';

			$lastValueIndex = count($record) - 1;
			foreach($record as $indexValue => $value) {
				$sql .= is_numeric($value) ? $value : ($CI->db->escape($value) );
				$sql .= $indexValue < $lastValueIndex ? ', ' : '';
			}
			$sql .= ")" . (($indexRecord < $lastRowIndex) ? ", \r\n" : ';');
		}
		$sql .= "\r\n\r\n";
	}

	echo $sql;

	return $sql;
}

function get_create_query_by_table_name($CI, $table)
{

	$query	= $CI->db->query('SHOW CREATE TABLE ' . $table);
	$result	= $query->result_array();

	return
		"DROP TABLE IF EXISTS `$table`;\r\n" . $result[0]['Create Table'] . ";\r\n\r\n";
}

/* End of file dump_content_definitions_helper.php */
/* Location: .application/helpers/dump_content_definitions_helper.php */