<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['module']['demo'] = array
(
	'module' => "Demo",
    'name' => "Demo Module",
	'description' => "Demo module. Manage articles's authors.",
	'author' => "Partikule",
	'version' => "1.0",

	/*
	'install' => 'install.php',
	'migrate' => 'migrate.php',
	'uninstall' => 'uninstall.php',
	*/

	'uri' => 'author',
	'has_admin'=> TRUE,

	// Array of resources
	// These resources will be added to the role's management panel
	// to allow / deny actions on them.
	'resources' => array(
		/*
		 * Default added resource : 'module/<module_key>'
		 *
		 * Default resource corresponding rule : 'access'
		 * Usage : Authority::can('access', 'module/<module_key>')
		 *
		 * Actions based rules (Added with this config file) :
		 * '<resource_key>' => array(
		 *		'title' => 'Resource title',
		 *		'actions' => '<action_key_1>, <action_key_2>, <action_key_3>',
		 *		'description' => 'Description of the resource in the role panel'
		 * )
		 * Usage : Authority::can('<action_key_1>', 'module/<module_key>/<resource_key>')
		 */
		// Authority::can('access', 'module/demo/admin')
		'admin' => array(
			'title' => 'Demo Module Administration'
		),
		'do_something' => array(
			'title' => 'Do something',						// Title of the resource
			'actions' => 'action_key_1,action_key_2',		// Can be 'edit', 'eat_cheese', what you want
		),
		'language/translate' => array(
			'parent' => 'language',
			'title' => 'Translate',
			'actions' => '',
		),
	),

);

return $config['module']['demo'];