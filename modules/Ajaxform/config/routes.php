<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

$route['default_controller'] = "ajaxform";
$route[''] = 'ajaxform/index';
$route['(.*)'] = $route['default_controller'].'/$1';
$route['404_override'] = 'ajaxform';
