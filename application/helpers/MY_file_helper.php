<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * File Helper
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 *
 */


// ------------------------------------------------------------------------

if ( ! function_exists('glob_recursive'))
{
	function glob_recursive($pattern, $flags = 0)
	{
		$files = glob($pattern, $flags);
		$dirs = glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT);

		if( is_array($files) && ! empty($dirs) ) {

			foreach ($dirs as $dir)
			{
				$_files = glob_recursive($dir.'/'.basename($pattern), $flags);
				if(is_array($_files))
					$files = array_merge($files, $_files);
			}
		}
		return $files;
	}
}