<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.5
 *
 */


// ------------------------------------------------------------------------


/**
 * Ionize String Helpers
 *
 * @package		Ionize
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Ionize Dev Team
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


/* End of file MY_string_helper.php */
/* Location: ./application/helpers/MY_string_helper.php */