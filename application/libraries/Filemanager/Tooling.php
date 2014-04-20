<?php
/*
 * Script: Tooling.php
 *   MooTools FileManager - Backend for the FileManager Script - Support Code
 *
 * Authors:
 *  - Christoph Pojer (http://cpojer.net) (author)
 *  - James Ehly (http://www.devtrench.com)
 *  - Fabian Vogelsteller (http://frozeman.de)
 *  - Ger Hobbelt (http://hebbut.net)
 *
 * License:
 *   MIT-style license.
 *
 * Copyright:
 *   Copyright (c) 2011 [Christoph Pojer](http://cpojer.net)
 */

if( ! function_exists('fnmatch')) {

	function fnmatch($pattern, $string) {
		return preg_match("#^".strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.'))."$#i", $string);
	}

}


if (!function_exists('safe_glob'))
{
	/**#@+
	 * Extra GLOB constant for safe_glob()
	 */
	if (!defined('GLOB_NODIR'))       define('GLOB_NODIR',256);
	if (!defined('GLOB_PATH'))        define('GLOB_PATH',512);
	if (!defined('GLOB_NODOTS'))      define('GLOB_NODOTS',1024);
	if (!defined('GLOB_RECURSE'))     define('GLOB_RECURSE',2048);
	if (!defined('GLOB_NOHIDDEN'))    define('GLOB_NOHIDDEN',4096);
	/**#@-*/


	/**
	 * A safe empowered glob().
	 *
	 * Function glob() is prohibited on some server (probably in safe mode)
	 * (Message "Warning: glob() has been disabled for security reasons in
	 * (script) on line (line)") for security reasons as stated on:
	 * http://seclists.org/fulldisclosure/2005/Sep/0001.html
	 *
	 * safe_glob() intends to replace glob() using readdir() & fnmatch() instead.
	 * Supported flags: GLOB_MARK, GLOB_NOSORT, GLOB_ONLYDIR
	 * Additional flags: GLOB_NODIR, GLOB_PATH, GLOB_NODOTS, GLOB_RECURSE, GLOB_NOHIDDEN
	 * (not original glob() flags)
	 *
	 * @author BigueNique AT yahoo DOT ca
	 * @updates
	 * - 080324 Added support for additional flags: GLOB_NODIR, GLOB_PATH,
	 *   GLOB_NODOTS, GLOB_RECURSE
	 * - [i_a] Added support for GLOB_NOHIDDEN, split output in directories and files subarrays
	 */
	function safe_glob($pattern, $flags = 0)
	{
		$split = explode('/', strtr($pattern, '\\', '/'));
		$mask = array_pop($split);
		$path = implode('/', $split);

		if (($dir = @opendir($path)) !== false)
		{
			$dirs = array();
			$files = array();
			while(($file = readdir($dir)) !== false)
			{
				// HACK/TWEAK: PHP5 and below are completely b0rked when it comes to international filenames   :-(
				//             --> do not show such files/directories in the list as they won't be accessible anyway!
				//
				// The regex charset is limited even within the ASCII range, due to    http://en.wikipedia.org/wiki/Filename#Comparison%5Fof%5Ffile%5Fname%5Flimitations
				// Although the filtered characters here are _possible_ on UNIX file systems, they're severely frowned upon.
				if (preg_match('/[^ -)+-.0-;=@-\[\]-{}~]/', $file))  // filesystem-illegal characters are not part of the set:   * > < ? / \ |
				{
					// simply do NOT list anything that we cannot cope with.
					// That includes clearly inaccessible files (and paths) with non-ASCII characters:
					// PHP5 and below are a real mess when it comes to handling Unicode filesystems
					// (see the php.net site too: readdir / glob / etc. user comments and the official
					// notice that PHP will support filesystem UTF-8/Unicode only when PHP6 is released.
					//
					// Big, fat bummer!
					continue;
				}
				//$temp = unpack("H*",$file);
				//echo 'hexdump of filename = ' . $temp[1] . ' for filename = ' . $file . "<br>\n";

				$filepath = $path . '/' . $file;
				$isdir = is_dir($filepath);

				// Recurse subdirectories (GLOB_RECURSE); speedup: no need to sort the intermediate results
				if (($flags & GLOB_RECURSE) && $isdir && !($file == '.' || $file == '..'))
				{
					$subsect = safe_glob($filepath . '/' . $mask, $flags | GLOB_NOSORT);
					if (is_array($subsect))
					{
						if (!($flags & GLOB_PATH))
						{
							$dirs = array_merge($dirs, array_prepend($subject['dirs'], $file . '/'));
							$files = array_merge($files, array_prepend($subject['files'], $file . '/'));
						}
					}
				}
				// Match file mask
				if (fnmatch($mask, $file))
				{
					if ( ( (!($flags & GLOB_ONLYDIR)) || $isdir )
					  && ( (!($flags & GLOB_NODIR)) || !$isdir )
					  && ( (!($flags & GLOB_NODOTS)) || !($file == '.' || $file == '..') )
					  && ( (!($flags & GLOB_NOHIDDEN)) || ($file[0] != '.' || $file == '..')) )
					{
						if ($isdir)
						{
							$dirs[] = ($flags & GLOB_PATH ? $path . '/' : '') . $file . (($flags & GLOB_MARK) ? '/' : '');
						}
						else
						{
							$files[] = ($flags & GLOB_PATH ? $path . '/' : '') . $file;
						}
					}
				}
			}
			closedir($dir);
			if (!($flags & GLOB_NOSORT))
			{
				sort($dirs);
				sort($files);
			}
			return array('dirs' => $dirs, 'files' => $files);
		}
		else
		{
			return false;
		}
	}
}

/*
 * http://www.php.net/manual/en/function.image-type-to-extension.php#77354
 * -->
 * http://www.php.net/manual/en/function.image-type-to-extension.php#79688
 */
if (!function_exists('image_type_to_extension'))
{
	function image_type_to_extension($type, $dot = true)
	{
		$e = array(1 => 'gif', 'jpeg', 'png', 'swf', 'psd', 'bmp', 'tiff', 'tiff', 'jpc', 'jp2', 'jpf', 'jb2', 'swc', 'aiff', 'wbmp', 'xbm');

		// We are expecting an integer.
		$t = (int)$type;
		if (!$t)
		{
			trigger_error('invalid IMAGETYPE_XXX(' . $type . ') passed to image_type_to_extension()', E_USER_NOTICE);
			return null;
		}
		if (!isset($e[$t]))
		{
			trigger_error('unidentified IMAGETYPE_XXX(' . $type . ') passed to image_type_to_extension()', E_USER_NOTICE);
			return null;
		}

		return ($dot ? '.' : '') . $e[$t];
	}
}
