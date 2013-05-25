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

// User Library : To use in constructors
require_once APPPATH.'core/User.php';

// Modules Library
require_once APPPATH.'core/Modules.php';

// User Library : To use in constructors
require_once APPPATH.'core/Authority.php';

// Cache Library : Available everywhere
require_once APPPATH.'core/Cache.php';


$hook['post_controller_constructor'][] = array (
	'function'	=> 'init_folder_protection',
	'filename'	=> 'User.php',
	'filepath'	=> 'core'
);

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */