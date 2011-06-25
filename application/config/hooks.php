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

// Connect Library : To use in constructors
require_once APPPATH.'libraries/Connect.php';

// Cache Library : Available everywhere
require_once APPPATH.'libraries/Cache.php';


$hook['post_controller_constructor'][] = array (
	'function'	=> 'init_connect',
	'filename'	=> 'Connect.php',
	'filepath'	=> 'libraries'
);

/*
$hook['display_override'][] = array(
	'class' => '',
	'function' => 'remove_comments',
	'filename' => 'remove_comments.php',
	'filepath' => 'hooks'
	);
*/



/*
$hook['display_override'][] = array(
	'class' => '',
	'function' => 'compress',
	'filename' => 'compress.php',
	'filepath' => 'hooks'
	);
/*
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
*/


/* End of file hooks.php */
/* Location: ./application/config/hooks.php */