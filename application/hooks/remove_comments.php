<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function remove_comments()
{
	$CI =& get_instance();
	$buffer = $CI->output->get_output();
	
	$buffer = preg_replace('#\s*<!--[^\[<>].*?(?<!!)-->#s', '', $buffer);
	
	$CI->output->set_output($buffer);
	$CI->output->_display();
}

