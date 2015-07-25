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

if ( ! function_exists('mb_pathinfo'))
{
	function mb_pathinfo($filepath)
	{
		preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im',$filepath,$m);
		if($m[1]) $ret['dirname']=$m[1];
		if($m[2]) $ret['basename']=$m[2];
		if($m[5]) $ret['extension']=$m[5];
		if($m[3]) $ret['filename']=$m[3];
		return $ret;
	}
}

if ( ! function_exists('remove_directory'))
{
	function remove_directory($directory, $empty=FALSE)
	{
		if(substr($directory,-1) == '/') {
			$directory = substr($directory,0,-1);
		}

		if(!file_exists($directory) || !is_dir($directory)) {
			return FALSE;
		} elseif(!is_readable($directory)) {

			return FALSE;

		} else {

			$handle = opendir($directory);
			while (FALSE !== ($item = readdir($handle)))
			{
				if($item != '.' && $item != '..') {
					$path = $directory.'/'.$item;
					if(is_dir($path)) {
						remove_directory($path);
					}else{
						unlink($path);
					}
				}
			}
			closedir($handle);
			if($empty == FALSE)
			{
				if(!rmdir($directory))
				{
					return FALSE;
				}
			}
			return TRUE;
		}
	}
}