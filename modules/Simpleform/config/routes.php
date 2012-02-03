<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

$route['default_controller'] = "simpleform";

$route['(.*)'] = $route['default_controller'].'/$1'; 

$route[''] = $route['default_controller'].'/index'; 

