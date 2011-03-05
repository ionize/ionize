<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( ! function_exists('trace'))
{
	function trace($var)
	{
		echo('<pre>');
		print_r($var);
		echo('</pre>');
	
	}
}
