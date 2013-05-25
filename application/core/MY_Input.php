<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ionize CMS
 *
 * Image Library
 * 
 * Extends the CodeIgniter CI_Image_lib by adding : 
 * - Some memory check before picture creation, to avoid fatal errors
 * - An unsharp library
 *
 * @package		Ionize
 * @category	Libraries
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 *
 */

class MY_Input extends CI_Input {


	/**
	* Replace a $_POST entry by another value
	*
	* @access	public
	* @param	string		The index key
	* @param	string		The new value
	* @return	boolean		true if success
	*
	*/
	function set_post($index, $value)
	{
		if ( ! empty($index))
		{
			$_POST[$index] = $value;
			
			return true;
		}
		return false;
	}


}
