<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

$route['default_controller'] = "session";
$route['(.*)'] = "session/index/$1";
$route[''] = 'session/index';

