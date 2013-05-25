<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.8
 *
 */


/**
 * Returns one CSV String from one array
 * 
 * @param	array	Array
 * @param	string	The delimiter - comma by default
 * @param	string	The newline character - \n by default
 * @param	string	The enclosure - double quote by default
 * @return	string
 *
 */
if ( ! function_exists('csv_from_array'))
{
	function csv_from_array($array, $delim = ",", $newline = "\n", $enclosure = '"')
	{
		$out = '';
		
		if ( ! empty($array))
		{
			// First generate the headings from the table column names
			$column_names = $array[0];
			foreach (array_keys($column_names) as $name)
			{
				$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $name).$enclosure.$delim;
			}
			
			$out = rtrim($out);
			$out .= $newline;
			
			// Next blast through the result array and build out the rows
			foreach ($array as $row)
			{
				foreach ($row as $item)
				{
					$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $item).$enclosure.$delim;			
				}
				$out = rtrim($out);
				$out .= $newline;
			}
		}
		return $out;
	}
	
}