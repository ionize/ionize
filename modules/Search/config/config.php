<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['module']['search'] = array
(
	'module' => "Search",
    'name' => "Search Module",
	'description' => "Search the content of the site",
	'author' => "Partikule",
	'version' => "1.1",

	// to activate multilang searching, module don't use its url, so, anything is good except 'search'.
	// Instead, module use a call to a page with id 'search' which can be translated to any language. It's mandatory !
	'uri' => 'searching',
	'has_admin'=> FALSE,
	'has_frontend'=> FALSE,
);

return $config['module']['search'];