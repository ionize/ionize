<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| URI routing
|--------------------------------------------------------------------------
|
| Re-map URI requests related to RSS module.
|
*/
$route['default_controller'] 		= 'rss';
$route['(.*)'] 						= $route['default_controller'].'/$1';
$route[''] 							= $route['default_controller'].'/index';

/* End of file routes.php */
/* Location: ./modules/RSS/config/routes.php */
