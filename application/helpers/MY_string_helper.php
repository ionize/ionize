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


/* End of file MY_string_helper.php */
/* Location: ./application/helpers/MY_string_helper.php */