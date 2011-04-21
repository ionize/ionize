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
	public static function move($file, $to, $options = null){
		if(!self::exists($file)) return false;

		$options = array_merge(array(
			'name' => null,
			'extension' => null,
			'maxsize' => null,
			'chmod' => 0777,
			'overwrite' => false,
			'mimes' => array(),
			'ext2mime_map' => null
		), (is_array($options) ? $options : array()));

		$file = $_FILES[$file];

		if($options['maxsize'] && $file['size'] > $options['maxsize'])
			throw new UploadException('size');

		$pathinfo = pathinfo($file['name']);
		if($options['extension']) $pathinfo['extension'] = $options['extension'];
		if(!$pathinfo['extension'])
			throw new UploadException('extension');

		if(count($options['mimes'])){
			$mime = self::mime($file['tmp_name'], array(
				'default' => $file['type'],
				'extension' => $pathinfo['extension'],
			), $options['ext2mime_map']);

			if(!$mime || !in_array($mime, $options['mimes']))
				throw new UploadException('extension');
		}

		$file['ext'] = strtolower($pathinfo['extension']);
		$file['base'] = basename($pathinfo['basename'], '.'.$pathinfo['extension']);

		$real = realpath($to);
		if(!$real) throw new UploadException('path');
		if(is_dir($real)) $to = $real.'/'.($options['name'] ? $options['name'] : $file['base']).'.'.$file['ext'];

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
	public static function exists($file){
		return isset($_FILES) && !empty($_FILES[$file]) && !empty($_FILES[$file]['name']) && !empty($_FILES[$file]['size']);
	}

	/**
	 * Returns (if possible) the mimetype of the given file
	 *
	 * @param string $file
	 * @param array $options
	 * @param array $ext2mimetype_arr optional externally specified array for mapping file extensions
	 *                                to mime types. May be specified as a temporary alternative to
	 *                                using the local MimeTypes.ini file.
	 */
	public static function mime($file, $options = null, $ext2mimetype_arr = null){
		$file = realpath($file);
		$options = array_merge(array(
				'default' => null,
				'extension' => strtolower(pathinfo($file, PATHINFO_EXTENSION)),
			), (is_array($options) ? $options : array()));

		$mime = null;
		$ini = error_reporting(0);
		if (function_exists('finfo_open') && $f = finfo_open(FILEINFO_MIME, getenv('MAGIC'))){
			$mime = finfo_file($f, $file);
			finfo_close($f);
		}
		error_reporting($ini);

		if(!$mime && in_array(strtolower($options['extension']), array('gif', 'jpg', 'jpeg', 'png'))){
			$image = @getimagesize($file);
			if($image !== false && !empty($image['mime']))
				$mime = $image['mime'];
		}

		if((!$mime || $mime=='application/octet-stream') && $options['extension']){
			if (!is_array($ext2mimetype_arr)){
				static $mimes;
				if(!$mimes) $mimes = parse_ini_file(pathinfo(__FILE__, PATHINFO_DIRNAME).'/MimeTypes.ini');
				$ext2mimetype_arr = $mimes;
			}

			if(!empty($ext2mimetype_arr[$options['extension']])) return $ext2mimetype_arr[$options['extension']];
		}

		if(!$mime && $options['default']) $mime = $options['default'];

		return $mime;
	}
}

class UploadException extends Exception {}

