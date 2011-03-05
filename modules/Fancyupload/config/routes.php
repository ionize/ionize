<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

$route['default_controller'] = "fancyupload";

// fancyupload/function
$route['(.*)'] = $route['default_controller'].'/$1'; 

// fancyupload => fancyupload/index
$route[''] = $route['default_controller'].'/index'; 


/* End of file routes.php */
/* Location: modules/Fancyupload/config/routes.php */ 
