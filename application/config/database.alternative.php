<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * database.php alternative configuration file
 *
 * Useful if you want to switch easily between development / production
 * environments without modify this file
 *
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
		$db['default']['dbdriver'] = 'mysql';
		$db['default']['dbprefix'] = '';
		$db['default']['swap_pre'] = '';
		$db['default']['pconnect'] = TRUE;
		$db['default']['db_debug'] = TRUE;
		$db['default']['cache_on'] = FALSE;
		$db['default']['cachedir'] = '';
		$db['default']['char_set'] = 'utf8';
		$db['default']['dbcollat'] = 'utf8_unicode_ci';
		break;

	// Test server
	case 'testing':
		$db['default']['hostname'] = '';
		$db['default']['username'] = '';
		$db['default']['password'] = '';
		$db['default']['database'] = '';
		$db['default']['dbdriver'] = 'mysql';
		$db['default']['dbprefix'] = '';
		$db['default']['swap_pre'] = '';
		$db['default']['pconnect'] = TRUE;
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
		$db['default']['dbdriver'] = 'mysql';
		$db['default']['dbprefix'] = '';
		$db['default']['swap_pre'] = '';
		$db['default']['pconnect'] = TRUE;
		$db['default']['db_debug'] = FALSE;
		$db['default']['cache_on'] = FALSE;
		$db['default']['cachedir'] = '';
		$db['default']['char_set'] = 'utf8';
		$db['default']['dbcollat'] = 'utf8_unicode_ci';
		break;

	default:
		exit('The application environment is not set correctly.');
}

/* End of file database.php */
/* Location: ./application/config/database.php */
