<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Lang Helpers
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 *
 */


// ------------------------------------------------------------------------


/**
 * Fetches a language variable and optionally outputs a form label
 *
 * @access	public
 * @param	string	the language line
 * @param	string	the id of the form element
 * @return	string
 */
if( ! function_exists('lang'))
{
	function lang($key, $swap = NULL)
	{
		$CI =& get_instance();
		$line = $CI->lang->line($key, $swap);

		$line = (!$line) ? '#'.$key.'' : $line;
	
		return $line;
	}
}


/* End of file language_helper.php */
/* Location: ./application/helpers/MY_language_helper.php */