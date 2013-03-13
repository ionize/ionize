<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['module']['demo'] = array
(
	'module' => "Demo",
	'description' => "Demo module. Manage articles's authors.",
	'author' => "Partikule",
	'version' => "1.0",

	'install' => 'install.php',
	'migrate' => 'migrate.php',
	'uninstall' => 'uninstall.php',

	'uri' => 'author',
	'has_admin'=> TRUE,
);

return $config['module']['demo'];