<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

$route['default_controller'] = "simpleform";
$route['(.*)'] = "simpleform/index/$1";
$route[''] = 'simpleform/index';

