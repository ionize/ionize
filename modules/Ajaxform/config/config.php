<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['module']['ajaxform'] = array
(
	'module' => "Ajaxform",
    'name' => "AjaxForm",
	'description' => "Send mail through Ajax",
	'author' => "Michel-Ange K.",
	'version' => "1.0",

	'uri' => 'ajaxform',
	'has_admin'=> FALSE,
	'has_frontend'=> TRUE,

	'resources' => array()
);

return $config['module']['ajaxform'];
