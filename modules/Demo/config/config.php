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
	'admin'=> TRUE,

	// Array of resource/role/permission/actions
	'permissions' => array
	(
		// Check permission
		// The resource is :
		// - Declared without prefix.
		// 	 Example : my_resource
		// - Prefixed by 'module/<module_folder>' (lowercase) when using Authority.
		//	 Example for the module "Demo" : Authority::can('write', 'module/demo/my_resource')
		//
		// Example of permission usage :
		// The user has "admins" role : Authority::can('write', 'module/demo/admin') will return TRUE
		// The user has "editors" role : Authority::can('write', 'module/demo/admin') will return FALSE
		// The user has "users" role : Authority::can('write', 'module/demo/my_resource') will return FALSE
		// The user has "editors" role : Authority::can('write', 'module/demo/my_resource') will return TRUE
		//
		'admin' => array(
			'admins' => array(
				'allow' => 'access'
			),
			'editors' => array(
				'allow' => 'access',
				'deny' => 'other_action'
			),
		),
		'my_resource' => array(
			'users' => array(
				'allow' => 'read'
			),
			'editors' => array(
				'allow' => 'read, write'
			),
		),
	),
);

return $config['module']['demo'];