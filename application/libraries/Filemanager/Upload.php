<?php
/**
 * Styx::Upload - Handles file uploads
 *
 * @package Styx
 * @subpackage Utility
 *
 * @license MIT-style License
 * @author Christoph Pojer <christoph.pojer@gmail.com>
 */

class Upload {
	
	/**
	 * Moves the uploaded file to the specified location. It throws a UploadException
	 * if anything goes wrong except for if the upload does not exist. This can be checked with {@link Upload::exists()}
	 *
	 * @param string $file
	 * @param string $to
	 * @param array $options
	 * @return bool|string Path to moved file or false if the specified upload does not exist
	 */
	public static function move($file, $to, $options = null)
	{
		if(!self::exists($file)) return false;
		
		$options = array_merge(array(
			'name' => null,
			'extension' => null,
			'size' => null,
			'chmod' => 0777,
			'overwrite' => false,
			'mimes' => array(),
		), $options);
		
		$file = $_FILES[$file];
		
		// File too big : UploadException
		if($options['size'] && $file['size']>$options['size'])
			throw new UploadException('size');
		
		// Check the extension
		$pathinfo = pathinfo($file['name']);
		if($options['extension']) $pathinfo['extension'] = $options['extension'];
		if(!$pathinfo['extension'])
			throw new UploadException('extension');
		
		if(count($options['mimes'])){
			$mime = self::mime($file['tmp_name'], array(
				'default' => $file['type'],
				'extension' => $pathinfo['extension'],
			));
			
			if(!$mime || !in_array($mime, $options['mimes']))
				throw new UploadException('extension');
		}
		
		$file['ext'] = strtolower($pathinfo['extension']);
		$file['base'] = basename($pathinfo['basename'], '.'.$pathinfo['extension']);
		
		$real = realpath($to);
		if(!$real) throw new UploadException('path');
		if(is_dir($real)) $to = $real.'/'.($options['name'] ? $options['name'] : $file['base']).'.'.$file['ext'];
		
		// Do not overwrite if not defined in option
		if(!$options['overwrite'] && file_exists($to))
			throw new UploadException('exists');
		
		if(!move_uploaded_file($file['tmp_name'], $to))
			throw new UploadException(strtolower($_FILES[$file]['error']<=2 ? 'size' : ($_FILES[$file]['error']==3 ? 'partial' : 'nofile')));
		
		chmod($to, $options['chmod']);
		
		return realpath($to);
	}
	
	/**
	 * Returns whether the Upload exists or not
	 *
	 * @param string $file
	 * @return bool
	 */
	public function exists($file)
	{
		return !(empty($_FILES[$file]['name']) || empty($_FILES[$file]['size']));
	}
	
	/**
	 * Returns (if possible) the mimetype of the given file
	 *
	 * @param string $file
	 * @param array $options
	 */
	public function mime($file, $options = array())
	{
		$file = realpath($file);
		$options = array_merge(array(
			'default' => null,
			'extension' => strtolower(pathinfo($file, PATHINFO_EXTENSION)),
		), $options);
		
		$mime = null;
		$ini = error_reporting(0);
		if (function_exists('finfo_open') && $f = finfo_open(FILEINFO_MIME, getenv('MAGIC'))){
			$mime = finfo_file($f, $file);
			finfo_close($f);
		}
		error_reporting($ini);
		
		if(!$mime && in_array(strtolower($options['extension']), array('gif', 'jpg', 'jpeg', 'png'))){
			$image = getimagesize($file);
			if(!empty($image['mime']))
				$mime = $image['mime'];
		}
		
		if(!$mime && $options['default']) $mime = $options['default'];
		
		if((!$mime || $mime=='application/octet-stream') && $options['extension']){
			static $mimes;
			if(!$mimes) $mimes = parse_ini_file(pathinfo(__FILE__, PATHINFO_DIRNAME).'/MimeTypes.ini');
			
			if(!empty($mimes[$options['extension']])) return $mimes[$options['extension']];
		}
		
		return $mime;
	}
	
}

class UploadException extends Exception {}