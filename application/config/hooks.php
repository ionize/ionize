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

$hook['post_controller_constructor'] = array (
										'function'	=> 'init_connect',
										'filename'	=> 'Connect.php',
										'filepath'	=> 'libraries'
										);
/* End of file hooks.php */
/* Location: ./system/application/config/hooks.php */