<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 *
 */


// ------------------------------------------------------------------------


/**
 * Ionize Date Helpers
 *
 * @package		Ionize
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Ionize Dev Team
 *
 * These function should be replaced by more efficient one in the next version.
 * Mandatory to take in account multiple date format !
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


/** 
 * Days count between 2 dates
 * @note	Not used for the moment.
function getDaysBetween($debut, $fin) {

  $tDeb = explode(".", $debut);
  $tFin = explode(".", $fin);

  $diff = mktime(0, 0, 0, $tFin[1], $tFin[0], $tFin[2]) - 
		  mktime(0, 0, 0, $tDeb[1], $tDeb[0], $tDeb[2]);

  return(($diff / 86400)+1);

}


 */

/* End of file MY_date_helper.php */
/* Location: ./application/helpers/MY_date_helper.php */

