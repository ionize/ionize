<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

// load the file here, to allow use of connect() in constructor
require_once APPPATH.'libraries/Connect.php';


$hook['post_controller_constructor'][] = array (
	'function'	=> 'init_connect',
	'filename'	=> 'Connect.php',
	'filepath'	=> 'libraries'
);

$hook['post_controller_constructor'][] = array(
	'class'    => 'Cache',
	'function' => 'post_controller_constructor_cache',
	'filename' => 'Cache.php',
	'filepath' => 'libraries',
	'params'   => array()
);

$hook['cache_override'] = array(
	'class' => 'Cache',
	'function' => 'display_cache_override',
	'filename' => 'Cache.php',
	'filepath' => 'libraries',
	'params' => array()
);


/* End of file hooks.php */
/* Location: ./system/application/config/hooks.php */