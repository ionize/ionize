<?php

$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$base_url .= "://".$_SERVER['HTTP_HOST'];
$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']); 
$base_url = str_replace('/install', '', $base_url);

/*
 * CI base data
 *
 */
$system_folder = "../system";
$application_folder = '../application';

if (strpos($system_folder, '/') === FALSE)
{
	if (function_exists('realpath') AND @realpath(dirname(__FILE__)) !== FALSE)
	{
		$system_folder = realpath(dirname(__FILE__)).'/'.$system_folder;
	}
}
else
{
	$system_folder = str_replace("\\", "/", $system_folder); 
}

/*
 * CI constant
 *
 */
define('EXT', '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));
define('BASEPATH', $system_folder.'/');
define('ROOTPATH', str_replace("\\", "/", realpath(dirname($system_folder))) . '/');
define('BASEURL', $base_url);

if (is_dir($application_folder))
{
	define('APPPATH', $application_folder.'/');
}
else
{
	if ($application_folder == '')
	{
		$application_folder = 'application';
	}

	define('APPPATH', BASEPATH.$application_folder.'/');
}



/*
 * CI classes include
 *
 */

require('../system/database/DB.php');
require('../system/codeigniter/Common.php');
require('../application/config/constants.php');
require('../application/config/ionize.php');
require_once('../application/libraries/finder/finder.php');		// So My_Language can output CI errors.

// Access class
// require(APPPATH.'libraries/access/Access.php');

// Installer class
if (file_exists('./class/installer.php'))
{

	require './class/installer.php';

	$installer = new Installer();


	/*
	 * Helpers
	 *
	 */
	require('./helpers/language_helper.php');
	require(BASEPATH.'helpers/email_helper.php');
	
	
	/*
	 * GET step
	 *
	 */
	$step = 'checkconfig';
	
	if (is_array($_GET) && isset($_GET['step']))
		$step = ($_GET['step']) ? $_GET['step'] : 'checkconfig' ;
	
	/*
	 * Actions
	 *
	 */
	switch($step)
	{
		case 'checkconfig' :
			$installer->check_config();
			break;
		
		case 'database' :
			$installer->configure_database();
			break;
			 
		case 'user' :
			$installer->configure_user();
			break;
			 
		case 'data' :
			$installer->install_data();
			break;
			 
		case 'finish' :
			$installer->finish();
			break;
			
		case 'migrate' :
			$installer->migrate();
			break;
			 
		case 'settings' :
			$installer->settings();
			break;
			 
	}
}
?>