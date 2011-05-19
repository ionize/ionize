<?php
/*
 * Script: FileManagerWithAliasSupport.php
 *   MooTools FileManager - Backend for the FileManager Script (with Alias path mapping support)
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
 *   FileManager Copyright (c) 2009-2011 [Christoph Pojer](http://cpojer.net)
 *   Backend: FileManager & FileManagerWithAliasSupport Copyright (c) 2011 [Ger Hobbelt](http://hobbelt.com)
 *
 * Dependencies:
 *   - Tooling.php
 *   - Image.class.php
 *   - getId3 Library
 *   - FileManager.php
 */

require(strtr(dirname(__FILE__), '\\', '/') . '/FileManager.php');


/**
 * Derived class for FileManager which is capable of handling Aliases as served through Apache's mod_alias or
 * mod_vhost_alias, PROVIDED you have set up the Alias translation table in the constructor: you must pass this table in the
 * $options array as a mapping array in the constructor.
 *
 * Options:
 *   -(all of the options of the FileManager class)
 *   -Aliases: (associative array of strings, where the 'key' URI path is to be transformed to the 'value' physical filesystem path.
 *
 * See Demos/FM-common.php::mkNewFileManager() for an example of an 'Aliases' path mapping set.
 */
class Filemanagerwithaliassupport extends FileManager
{
	protected $scandir_alias_lu_arr;

	public function __construct($options)
	{
		$this->scandir_alias_lu_arr = null;

		$options = array_merge(array(
			'Aliases' => null             // default is an empty Alias list.
		), (is_array($options) ? $options : array()));

		parent::__construct($options);

		/*
		 * Now process the Aliases array:
		 * it works as-is for transforming URI to FILE path, but we need
		 * to munch the list for scandir() to have fast access at the same info:
		 *
		 * here the goal is for scandir() to show the aliases as (fake) directory
		 * entries, hence we need to collect the aliases per parent directory:
		 */
		if (is_array($this->options['Aliases']))
		{
			$alias_arr = $this->options['Aliases'];

			// collect the set of aliases per parent directory: we need a fully set up options['URLpath4FileManagedDirTree'] for this now
			$scandir_lookup_arr = array();

			// NOTE: we can use any of the url2file_path methods here as those only use the raw [Aliases] array

			foreach($alias_arr as $uri => $file)
			{
				$isdir = !is_file($file);

				$p_uri = parent::getParentDir($uri);
				$a_name = basename($uri);

				// as scandir works with filesystem paths, convert this URI path to a filesystem path:
				$p_dir = $this->url_path2file_path($p_uri);
				$p_dir = self::enforceTrailingSlash($p_dir);

				if (!isset($scandir_lookup_arr[$p_dir]))
				{
					$scandir_lookup_arr[$p_dir] = array(array(), array());
				}
				$scandir_lookup_arr[$p_dir][!$isdir][] = /* 'alias' => */ $a_name;
			}

			$this->scandir_alias_lu_arr = $scandir_lookup_arr;
		}
	}

	/**
	 * @return array the FileManager options and settings.
	 */
	public function getSettings()
	{
		return array_merge(array(
			'scandir_alias_lu_arr' => $this->scandir_alias_lu_arr
		), parent::getSettings());
	}

	/**
	 * An augmented scandir() which will ensure any Aliases are included in the relevant
	 * directory scans; this makes the Aliases behave very similarly to actual directories.
	 */
	public function scandir($dir, $filemask, $see_thumbnail_dir, $glob_flags_or, $glob_flags_and)
	{
		$dir = self::enforceTrailingSlash($dir);

		// collect the real items first:
		$coll = parent::scandir($dir, $filemask, $see_thumbnail_dir, $glob_flags_or, $glob_flags_and);
		if ($coll === false)
			return $coll;

		$flags = GLOB_NODOTS | GLOB_NOHIDDEN | GLOB_NOSORT;
		$flags &= $glob_flags_and;
		$flags |= $glob_flags_or;

		// make sure we keep the guarantee that the '..' entry, when present, is the very last one, intact:
		$doubledot = array_pop($coll['dirs']);
		if ($doubledot !== null && $doubledot !== '..')
		{
			$coll['dirs'][] = $doubledot;
			$doubledot = null;
		}


		// we must check against thumbnail path again, as it MAY be an alias, itself!
		$tndir = null;
		if (!$see_thumbnail_dir)
		{
			$tn_uri = $this->options['URLpath4thumbnails'];
			$tnpath = $this->url_path2file_path($tn_uri);
			//if (FileManagerUtility::startswith($dir, $tnpath))
			//  return false;

			$tnparent = self::getParentDir($tnpath);
			$just_below_thumbnail_dir = FileManagerUtility::startswith($dir, $tnparent);

			if ($just_below_thumbnail_dir)
			{
				$tndir = basename(substr($tn_uri, 0, -1));
			}
		}


		// now see if we need to add any aliases as elements:
		if (isset($this->scandir_alias_lu_arr) && !empty($this->scandir_alias_lu_arr[$dir]))
		{
			$a_base = $this->scandir_alias_lu_arr[$dir];
			$d = $coll['dirs'];
			$f = $coll['files'];
			foreach($a_base[false] as $a_elem)
			{
				if (!in_array($a_elem, $d, true) && $tndir !== $a_elem
					&& (!($flags & GLOB_NOHIDDEN) || $a_elem[0] != '.') )
				{
					//$coll['special_indir_mappings'][1][] = array_push($coll['dirs'], $a_elem) - 1;
					$coll['dirs'][] = $a_elem;
				}
			}
			foreach($a_base[true] as $a_elem)
			{
				if (!in_array($a_elem, $f, true)
					&& (!($flags & GLOB_NOHIDDEN) || $a_elem[0] != '.') )
				{
					//$coll['special_indir_mappings'][0][] = array_push($coll['files'], $a_elem) - 1;
					$coll['files'][] = $a_elem;
				}
			}
		}

		// make sure we keep the guarantee that the '..' entry, when present, is the very last one, intact:
		if ($doubledot !== null)
		{
			$coll['dirs'][] = $doubledot;
		}

		return $coll;
	}




	/**
	 * Return the filesystem absolute path for the relative or absolute URI path.
	 *
	 * Takes the ['Aliases'] mapping array into account; it is processed from top to bottom a la mod_alias.
	 *
	 * Note: as it uses normalize(), any illegal path will throw an FileManagerException
	 *
	 * Returns a fully normalized filesystem absolute path.
	 */
	public function url_path2file_path($url_path)
	{
		$url_path = $this->rel2abs_url_path($url_path);

		$replaced_some = false;
		if (is_array($this->options['Aliases']))
		{
			$alias_arr = $this->options['Aliases'];

			foreach($alias_arr as $a_url => $a_path)
			{
				// if the uri path matches us (or at least our start), then apply the mapping.
				// Make sure to only match entire path elements:
				if (FileManagerUtility::startsWith($url_path . '/', $a_url . '/'))
				{
					$url_path = $a_path . substr($url_path, strlen($a_url));
					$replaced_some = true;
				}
			}
		}

		if (!$replaced_some)
		{
			$url_path = parent::url_path2file_path($url_path);
		}

		return $url_path;
	}

	/**
	 * Return the filesystem absolute path for the relative URI path or absolute LEGAL URI path.
	 *
	 * Note: as it uses normalize(), any illegal path will throw an FileManagerException
	 *
	 * Returns a fully normalized filesystem absolute path.
	 */
	public function legal_url_path2file_path($url_path)
	{
		$url_path = $this->rel2abs_legal_url_path($url_path);

		$url_path = substr($this->options['URLpath4FileManagedDirTree'], 0, -1) . $url_path;

		$path = $this->url_path2file_path($url_path);

		return $path;
	}
}

