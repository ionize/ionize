<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * Ionize Lang Helpers
 *
 * @package		Ionize
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Ionize Dev Team
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
	function lang($key, $id = '')
	{
		$CI =& get_instance();
		$line = $CI->lang->line($key);

		$line = (!$line) ? '#'.$key.'' : $line;
	
		if ($id != '')
		{
			$line = '<label for="'.$id.'">'.$line."</label>";
		}
	
		return $line;
	}
}


/* End of file language_helper.php */
/* Location: ./application/helpers/MY_language_helper.php */