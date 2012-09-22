<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

$route['default_controller'] = "demo";
$route['(.*)'] = "demo/index/$1";
$route[''] = 'demo/index';

