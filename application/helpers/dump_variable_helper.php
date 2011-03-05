<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Created by Martin Wernståhl on 2009-05-01.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Dumps the content of a variable into correct PHP.
 *
 * Attention!
 * Cannot handle objects!
 *
 * Usage:
 * <code>
 * $str = '$variable = ' . dump_variable($variable);
 * </code>
 *
 * @param  mixed
 * @param  int
 * @return str
 */
function dump_variable($data, $indent = 0)
{
	$ind = str_repeat("\t", $indent);
	$str = '';

	switch(gettype($data))
	{
		case 'boolean':
			$str .= $data ? 'true' : 'false';
			break;

		case 'integer':
		case 'double':
			$str .= $data;
			break;

		case 'string':
			$str .= "'". addcslashes($data, '\'\\') . "'";
			break;

		case 'array':
			$str .= "array(\n";

			$t = array();
			foreach($data as $k => $v)
			{
				$s = '';
				if( ! is_numeric($k))
				{
					$s .= $ind . "\t'".addcslashes($k, '\'\\')."' => ";
				}

				$s .= dump_variable($v, $indent + 1);

				$t[] = $s;
			}

			$str .= implode(",\n", $t) . "\n" . $ind . "\t)";
			break;

		default:
			$str .= 'NULL';
	}

	return $str . ($indent ? '' : ';');
}

function dp($data, $indent = 0)
{
	return dump_variable($data, $indent);
}

/* End of file dump_variable_helper.php */
/* Location: .application/helpers/dump_variable_helper.php */