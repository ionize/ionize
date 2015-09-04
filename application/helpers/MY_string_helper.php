<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * String Helpers
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.5
 *
 */


// ------------------------------------------------------------------------


/*
 * HTML encode special characters Helper
 *
 * @access	public
 * @param	string
 * @return	string	HTML encoded string
 *
 */
if ( ! function_exists('to_entities'))
{
	function to_entities($string)
	{
		return htmlentities($string, ENT_QUOTES, config_item('charset'));
	}
}


// ------------------------------------------------------------------------


/*
 * HTML encode special characters Helper
 *
 * @access	public
 * @param	string
 * @return	string	HTML encoded string
 *
 */
if ( ! function_exists('to_specialchars'))
{
	function to_specialchars($string)
	{
		return htmlspecialchars($string, ENT_QUOTES, config_item('charset'));
	}
}


// ------------------------------------------------------------------------


/*
 * Strip tags in a String or an array
 *
 * @access	public
 * @param	string / array
 * @return	string	HTML encoded string
 *
 */
if ( ! function_exists('strip_html'))
{
	function strip_html(&$data)
	{
		if ( ! is_array($data))
		{
			$data = strip_tags($data);
		}
		else
		{
			foreach($data as &$v)
			{
				if (is_array($v))
				{
					strip_html($v);
				}
				else
				{
					$v = strip_tags($v);
				}
			}
		}
	}
}


// ------------------------------------------------------------------------


/*
 * Replace first occurrence of substring
 *
 * @access	public
 */
if ( ! function_exists('replace_first')) {

	/**
	 * @param	String	$search		string to be replaced
	 * @param	String	$replace	string to used for replacement
	 * @param	String	$string		the source string to do the replacement inside
	 * @return	String	The string with the replacement done
	 */
	function replace_first($string, $search, $replace = '')
	{
		if( strlen( $search ) > 0 ) {
			$pos = strpos( $string, $search );
			if( is_int( $pos ) ) {
				$len = strlen( $search );

				return substr_replace( $string, $replace, $pos, $len );
			}
		}

		return $string;
	}
}


// ------------------------------------------------------------------------


/*
 * Replace last occurrence of substring
 *
 * @access	public
 */
if ( ! function_exists('replace_last')) {

	/**
	 * @param	String	$search
	 * @param	String	$replace
	 * @param	String	$subject
	 * @return	String
	 */
	function replace_last($search, $replace, $subject)
	{
		$pos = strrpos($subject, $search);

		if ($pos !== false) {
			$subject = substr_replace($subject, $replace, $pos, strlen($search));
		}

		return $subject;
	}
}


/* End of file MY_string_helper.php */
/* Location: ./application/helpers/MY_string_helper.php */
