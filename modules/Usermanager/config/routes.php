<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

$route['default_controller'] = "usermanager";
$route['(.*)'] = "usermanager/index/$1";
$route[''] = 'usermanager/index';

