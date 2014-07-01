<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Date Helpers
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 *
 */


// ------------------------------------------------------------------------


/**
 * Checks if an MySQL datetime is a real date
 * @param	String		MySQL Datetime
 * @return	Boolean		true if ok, false if not.
 *
 */
if ( ! function_exists('isDate'))
{
	function isDate($mysqlDatetime)
	{
		return ($mysqlDatetime != '0000-00-00 00:00:00' && $mysqlDatetime != '') ? true : false;
	}
}

/**
 * Return DD.MM.YYYY HH:MM:SS from a MySQL datetime
 * @param	String		MySQL datetime as String
 * @return	String		French formatted datetime
 *
 */
if ( ! function_exists('getFrenchDatetime'))
{
	function getFrenchDatetime($mysqlDatetime)
	{
		if($mysqlDatetime != "0000-00-00 00:00:00" && $mysqlDatetime !="")
		{
			if (($timestamp = strtotime($mysqlDatetime)) == '-1')
			{
				return $mysqlDatetime;
			}
			else {
				return date("d.m.Y H:i:s", $timestamp);
			}
		}
		else {
			return "";
		}
	}
}

/**
 * MySQL datetime from a DD.MM.YYYY string
 * @param	String	French formatted datetime
 * @param	String	MySQL formatted datetime
 */
if ( ! function_exists('getMysqlDatetime'))
{
	function getMysqlDatetime($inputDate, $inputFormat='dd.mm.yyyy')
	{
		if ($inputDate !='')
		{
			$date = $time = '';
			if (strlen($inputDate) > 10)
			{
				list($date, $time) = explode(' ', $inputDate);
			}
			else
			{
				$date = $inputDate;
				$time = '00:00:00';
			}

			if ($inputFormat == '%d.%m.%Y')
			{
				list($day, $month, $year) = preg_split("/[\/.-]/", $date);
			}
			else if ($inputFormat == '%Y.%m.%d')
			{
				list($year, $month, $day) = preg_split("/[\/.-]/", $date);
			}
			else
				return date("Y-m-d H:i:s", strtotime($inputDate));

			return "$year-$month-$day $time";
		}
	}
}


if ( ! function_exists('humanize_mdate'))
{
	function humanize_mdate($mdate, $datestr = '%d.%m.%Y at %H:%i:%s')
	{
		if ($mdate != '' && $mdate != "0000-00-00 00:00:00")
		{
			$timestamp 	= strtotime($mdate);
			$datestr 	= str_replace('%\\', '', preg_replace("/([a-z]+?){1}/i", "\\\\\\1", $datestr));

			return date($datestr, $timestamp);
		}
	}
}

if ( ! function_exists('dateDiff'))
{
	function dateDiff($first, $second=NULL, $unit = 'day')
	{
		if (is_null($second)) $second = date('Y-m-d H:i:s');

		$first = strtotime($first);
		$second = strtotime($second);

		$subTime = $second - $first;

		if ($unit == 'year') return $subTime/(60*60*24*365);
		if ($unit == 'day') return intval(($subTime/(60*60*24)));
		if ($unit == 'hour') return intval(($subTime/(60*60)));
		if ($unit == 'min') return intval($subTime/60);
	}
}
if ( ! function_exists('lang_date'))
{
	function lang_date($date, $format='Y-m-d')
	{
		$date = strtotime($date);

		if ($date)
		{
			$segments = explode(' ', $format);

			foreach($segments as $key => $segment)
			{
				$tmp = (String) date($segment, $date);

				if (preg_match('/D|l|F|M/', $segment))
					$tmp = lang(strtolower($tmp));

				$segments[$key] = $tmp;
			}

			return implode(' ', $segments);
		}

		return '';
	}
}

