<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function disable_log()
{
	$_log =& load_class('Log');
	$_log->disable();
}

