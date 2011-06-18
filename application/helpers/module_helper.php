<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('get_modules_addons'))
{
	function get_modules_addons($type, $placeholder)
	{
		$CI =& get_instance();
		
		return $CI->get_modules_addons($type, $placeholder);
	}
}
