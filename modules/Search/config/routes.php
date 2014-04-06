<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

$route['default_controller'] = "search";

// search/function
$route['(.*)'] = $route['default_controller'].'/$1'; 

// search => search/index
$route[''] = $route['default_controller'].'/index'; 

