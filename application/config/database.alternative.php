<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * database.php alternative configuration file
 *
 * Useful if you want to switch easily between development / production
 * environments without modify this file
 *
 * Set the environment in : /index.php
 *
 */

$active_group = 'default';
$active_record = TRUE;
$db['default'] = array();
switch (ENVIRONMENT)
{
	// Local server
	case 'development':
		$db['default']['hostname'] = '';
		$db['default']['username'] = '';
		$db['default']['password'] = '';
		$db['default']['database'] = '';
		$db['default']['dbdriver'] = 'mysqli';
		$db['default']['dbprefix'] = '';
		$db['default']['swap_pre'] = '';
		$db['default']['pconnect'] = FALSE;
		$db['default']['db_debug'] = TRUE;
		$db['default']['cache_on'] = FALSE;
		$db['default']['cachedir'] = '';
		$db['default']['char_set'] = 'utf8';
		$db['default']['dbcollat'] = 'utf8_unicode_ci';
		break;

	// Testing server
	case 'testing':
		$db['default']['hostname'] = '';
		$db['default']['username'] = '';
		$db['default']['password'] = '';
		$db['default']['database'] = '';
		$db['default']['dbdriver'] = 'mysqli';
		$db['default']['dbprefix'] = '';
		$db['default']['swap_pre'] = '';
		$db['default']['pconnect'] = FALSE;
		$db['default']['db_debug'] = TRUE;
		$db['default']['cache_on'] = FALSE;
		$db['default']['cachedir'] = '';
		$db['default']['char_set'] = 'utf8';
		$db['default']['dbcollat'] = 'utf8_unicode_ci';
		break;

	// Production server
	case 'production':
		$db['default']['hostname'] = '';
		$db['default']['username'] = '';
		$db['default']['password'] = '';
		$db['default']['database'] = '';
		$db['default']['dbdriver'] = 'mysqli';
		$db['default']['dbprefix'] = '';
		$db['default']['swap_pre'] = '';
		$db['default']['pconnect'] = FALSE;
		$db['default']['db_debug'] = FALSE;
		$db['default']['cache_on'] = FALSE;
		$db['default']['cachedir'] = '';
		$db['default']['char_set'] = 'utf8';
		$db['default']['dbcollat'] = 'utf8_unicode_ci';
		break;

	default:
		exit('database.php : The application environment is not set correctly.');
}
