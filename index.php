<?php

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
/*
switch ($_SERVER['HTTP_HOST'])
{
	case 'localhost':
	case '127.0.0.1':
		define('ENVIRONMENT','development');
		break;

	case "x.x.x.x":
	case "your.dev.server.tld":
		define('ENVIRONMENT','testing');
		break;

	case "y.y.y.y":
		define('ENVIRONMENT','pre-production');
		break;

	default:
		define('ENVIRONMENT','production');
}
*/
define('ENVIRONMENT','development');

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */

if (defined('ENVIRONMENT'))
{
	switch (ENVIRONMENT)
	{
		case 'development':
		case 'testing':
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
		break;
	
		case 'pre-production':
		case 'production':
			error_reporting(0);
		break;

		default:
			exit('index.php : The application environment is not set correctly.');
	}
}

/*
 *---------------------------------------------------------------
 * SYSTEM FOLDER NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" folder.
 * Include the path if the folder is not in the same  directory
 * as this file.
 *
 */
	$system_path = 'system';

/*
 *---------------------------------------------------------------
 * APPLICATION FOLDER NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * folder then the default one you can set its name here. The folder
 * can also be renamed or relocated anywhere on your server.  If
 * you do, use a full server path. For more info please see the user guide:
 * http://codeigniter.com/user_guide/general/managing_apps.html
 *
 * NO TRAILING SLASH!
 *
 */
	$application_folder = 'application';


/*
 *---------------------------------------------------------------
 * MODULES FOLDER NAME
 *---------------------------------------------------------------
 *
 * Ionize's modules folder
 *
 * NO TRAILING SLASH!
 *
 */
	$modules_folder = 'modules';


/*
 *---------------------------------------------------------------
 * PUBLIC DOC FOLDER
 *---------------------------------------------------------------
 *
 * Website public documents folder.
 *
 * This folder is the root public documents folder of the website.
 * Usefull only if the Ionize root folder is inside one other folder
 * from the document root folder, in case of using Ionize with one
 * other framework or CMS.
 *
 * Example : 
 * Main app folder : 	/public_html/
 * Ionize folder : 		/public_html/ionize/
 * 
 * If you want to store files (medias) outside from the ionize folder
 * the public doc folder needs to be set and will be different from the
 * FCPATH 
 *
 */
	$doc_folder = "";


/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here.  For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *
 * IMPORTANT:  If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller.  Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *
 * Un-comment the $routing array below to use this feature
 *
 */
	// The directory name, relative to the "controllers" folder.  Leave blank
	// if your controller is not in a sub-folder within the "controllers" folder
	// $routing['directory'] = '';

	// The controller class file name.  Example:  Mycontroller.php
	// $routing['controller'] = '';

	// The controller function you wish to be called.
	// $routing['function']	= '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 *
 */
	// $assign_to_config['name_of_config_item'] = 'value of config item';



// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

	// Set the current directory correctly for CLI requests
	if (defined('STDIN'))
	{
		chdir(dirname(__FILE__));
	}

	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).'/';
	}

	// ensure there's a trailing slash
	$system_path = rtrim($system_path, '/').'/';

	// Is the system path correct?
	if ( ! is_dir($system_path))
	{
		exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: ".pathinfo(__FILE__, PATHINFO_BASENAME));
	}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
	// The name of THIS file
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	// The PHP file extension
	define('EXT', '.php');

	// Path to the system folder
	define('BASEPATH', str_replace("\\", "/", $system_path));

	// Path to the front controller (this file)
	define('FCPATH', str_replace(SELF, '', __FILE__));

	// Name of the "system folder"
	define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

	// Path to the Ionize's modules folder
	define('MODPATH', realpath(FCPATH.$modules_folder).'/');

	// The path to the "application" folder
	if (is_dir($application_folder))
	{
		define('APPPATH', realpath($application_folder).'/');
	}
	else
	{
		if ( ! is_dir(BASEPATH.$application_folder.'/'))
		{
			exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);
		}

		define('APPPATH', realpath(BASEPATH.$application_folder).'/');
	}

	// Path to the public web folder
	if (realpath(FCPATH.$doc_folder) !== FALSE)
	{
		$doc_path = realpath(FCPATH.$doc_folder);
	}
	else
	{
		$doc_path = FCPATH;
	}

	$doc_path = rtrim($doc_path, '/').'/';

	if ( ! is_dir($doc_path))
		exit("Your document root folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);

	define('DOCPATH', $doc_path);


/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */
require_once BASEPATH.'core/CodeIgniter'.EXT;

/* End of file index.php */
/* Location: ./index.php */