<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
Script: FileManager.php
	MooTools FileManager - Backend for the FileManager Script

License:
	MIT-style license.

Copyright:
	Copyright (c) 2009 [Christoph Pojer](http://cpojer.net).

Dependencies:
	- Upload.php
	- Image.php
	- getId3 Library

Options:
	- directory: (string) The base directory to be used for the FileManger
	- baseURL: (string) Absolute URL to the FileManager files
	- assetBasePath: (string) The path to all images and swf files
	- id3Path: (string, optional) The path to the getid3.php file
	- mimeTypesPath: (string, optional) The path to the MimTypes.ini file.
	- dateFormat: (string, defaults to *j M Y - H:i*) The format in which dates should be displayed
	- upload: (boolean, defaults to *false*) Whether to allow uploads or not
	- destroy: (boolean, defaults to *false*) Whether to allow deletion of files or not
	- maxUploadSize: (integeter, defaults to *3145728* bytes) The maximum file size for upload in bytes
	- safe: (string, defaults to *true*) If true, disallows 
	- filter: (string) If specified, the mimetypes to be allowed (for display and upload).
		Example: image/ allows all Image Mimetypes
*/

require_once(FileManagerUtility::getPath() . '/Filemanager/Upload.php');
require_once(FileManagerUtility::getPath() . '/Filemanager/Image.php');
require_once(FileManagerUtility::getPath() . '/Filemanager/GetImage.php');

// Debug only
require_once(FileManagerUtility::getPath() . '/../helpers/trace_helper.php');


class Filemanager {
	
	protected $path = null;
	protected $length = null;
	protected $basedir = null;
	protected $basename = null;
	protected $options;
	protected $post;
	protected $get;
	protected $allowed_extensions = array();
	
	public function __construct($options)
	{
		$path = FileManagerUtility::getPath();
		
		$this->options = array_merge(array(
			'directory' => '../Demos/Files',
			'baseURL' => '',
			'assetBasePath' => '../Assets',
			'id3Path' => $path . '/getid3/getid3.php',
			'mimeTypesPath' => $path . '/Filemanager/MimeTypes.ini',
			'dateFormat' => 'j M Y - H:i',
			'maxUploadSize' => 1024 * 1024 * 3,
			'upload' => false,
			'destroy' => false,
			'safe' => true,
			'filter' => null,
			'thumbsDir' => '.thumbs',
			'thumbSize' => 120,
			'dirPerms' => 0755,
			'filePerms' => 0644
		), $options);

		$this->basedir = realpath($this->options['directory']);
		$this->basename = pathinfo($this->basedir, PATHINFO_BASENAME) . '/';
		$this->path = realpath($this->options['directory'] . '/../');
		$this->length = strlen($this->path);

		$this->allowed_extensions = array_merge(explode(",", Settings::get('media_type_picture')), explode(",", Settings::get('media_type_music')), explode(",", Settings::get('media_type_video')), explode(",", Settings::get('media_type_file')));

		
//		header('Expires: Fri, 01 Jan 1990 00:00:00 GMT');
//		header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
		
		$this->thumbDir = $this->basedir . '/' . $this->options['thumbsDir'];
        
        // Try to create / access the .thumbs dir
        try {
	        if (( ! is_dir($this->thumbDir) && ! @mkdir($this->thumbDir, $this->options['dirPerms']) ) 
    	    	|| ! is_readable($this->thumbDir) || ! FileManagerUtility::isDirWritable($this->thumbDir) )
        		throw new FileManagerException('notwritable');
        }
 		catch(FileManagerException $e)
		{
			echo json_encode(array
			(
				'status' => 0,
				'error' => 'notwritable',
				'sub.basedir' => $this->basedir
			));
			die();
		}
		
		$this->get = $_GET;
		$this->post = $_POST;
	}
	
	public function fireEvent($event){
		$event = $event ? 'on' . ucfirst($event) : null;
		if (!$event || !method_exists($this, $event)) $event = 'onView';
		
		$this->{$event}();
	}
	
	protected function onView()
	{
		$dir = $this->getDir(!empty($this->post['directory']) ? $this->post['directory'] : null);
		$files = ($files = glob($dir . '/*')) ? $files : array();
		
		if ($dir != $this->basedir) array_unshift($files, $dir . '/..');
		natcasesort($files);
		
		foreach ($files as $file)
		{
			$mime = $this->getMimeType($file);

			if ( is_file($file) && ( ! in_array(pathinfo($file, PATHINFO_EXTENSION), $this->allowed_extensions)) )
				continue;
			
			if ($this->options['filter'] && $mime != 'text/directory' && !FileManagerUtility::startsWith($mime, $this->options['filter']))
				continue;
			
			$out[is_dir($file) ? 0 : 1][] = array(
				'name' => pathinfo($file, PATHINFO_BASENAME),
				'date' => date($this->options['dateFormat'], filemtime($file)),
				'mime' => $this->getMimeType($file),
				'icon' => $this->getIcon($this->normalize($file)),
				'size' => filesize($file),
				'path' => $file
			);
		}
		
		echo json_encode(array(
			'path' => $this->getPath($dir),
			'dir' => array(
				'name' => pathinfo($dir, PATHINFO_BASENAME),
				'date' => date($this->options['dateFormat'], filemtime($dir)),
				'mime' => 'text/directory',
				'icon' => 'dir'
			),
			'files' => array_merge(!empty($out[0]) ? $out[0] : array(), !empty($out[1]) ? $out[1] : array())
		));
		die();
	}
	
	/**
	 * Get the detail of one file / folder
	 *
	 */
	protected function onDetail()
	{
		if (empty($this->post['directory']) || empty($this->post['file'])) return;
		
		$file = realpath($this->path . '/' . $this->post['directory'] . '/' . $this->post['file']);

		if (!$this->checkFile($file)) return;
		
		require_once($this->options['id3Path']);
		
		$url = $this->options['baseURL'] . $this->normalize(substr($file, strlen($this->path)+1));
		$mime = $this->getMimeType($file);
		$content = null;
		
		// Image
		if (FileManagerUtility::startsWith($mime, 'image/'))
		{
			// Display the thumb rather than the big picture
			if (file_exists($this->thumbDir . str_replace($this->basedir, '', $file)))
			{
				// $url = $this->options['baseURL'] . $this->normalize(substr($this->thumbDir . str_replace($this->basedir, '', $file), strlen($this->path)+1));
			}
			
			$size = getimagesize($file);
			$content = '<img src="' . $url . '" class="preview" alt="" />
				<dl>
					<dt>${width}</dt><dd>' . $size[0] . ' px</dd>
					<dt>${height}</dt><dd>' . $size[1] . ' px</dd>
				</dl>';
		}
		elseif (FileManagerUtility::startsWith($mime, 'text/') || $mime == 'application/x-javascript')
		{
			$filecontent = file_get_contents($file, null, null, 0, 300);
			if (!FileManagerUtility::isBinary($filecontent)) $content = '<div class="textpreview">' . nl2br(str_replace(array('$', "\t"), array('&#36;', '&nbsp;&nbsp;'), htmlentities($filecontent))) . '</div>';
		}
		elseif ($mime == 'application/zip')
		{
			$out = array(array(), array());
			$getid3 = new getID3();
			$getid3->Analyze($file);
			foreach ($getid3->info['zip']['files'] as $name => $size){
				$icon = is_array($size) ? 'dir' : $this->getIcon($name);
				$out[($icon == 'dir') ? 0 : 1][$name] = '<li><a><img src="' . $this->options['assetBasePath'] . '/Icons/' . $icon . '.png" alt="" /> ' . $name . '</a></li>';
			}
			natcasesort($out[0]);
			natcasesort($out[1]);
			$content = '<ul>' . implode(array_merge($out[0], $out[1])) . '</ul>';
		}
		elseif (FileManagerUtility::startsWith($mime, 'audio/'))
		{
			$getid3 = new getID3();
			$getid3->Analyze($file);
			
			$content = '
				<div class="object">
					<object type="application/x-shockwave-flash" data="' . $this->options['baseURL'] . 'themes/admin/flash/mp3Player/mp3player_simple.swf?mp3=' . rawurlencode($url) . '" width="224" height="20">
						<param name="wmode" value="transparent" />
						<param name="movie" value="' . $this->options['baseURL'] . 'themes/admin/flash/mp3Player/mp3player_simple.swf?mp3=' . rawurlencode($url) . '" />
					</object>
				</div>
				<dl>
					<dt>${title}</dt><dd>' . @$getid3->info['tags_html']['id3v2']['title'][0] . '</dd>
					<dt>${artist}</dt><dd>' . @$getid3->info['tags_html']['id3v2']['artist'][0] . '</dd>
					<dt>${album}</dt><dd>' . @$getid3->info['tags_html']['id3v2']['album'][0] . '</dd>
					<dt>${length}</dt><dd>' . @$getid3->info['playtime_string'] . '</dd>
					<dt>${bitrate}</dt><dd>' . @round($getid3->info['bitrate']/1000) . ' kbps</dd>
				</dl>';
		}
		elseif (FileManagerUtility::startsWith($mime, 'video/x-flv'))
		{
			$getid3 = new getID3();
			$getid3->Analyze($file);

			$content = '
				<div class="object">
					<object type="application/x-shockwave-flash" data="' . $this->options['baseURL'] . 'themes/admin/flash/mediaplayer/player.swf?file=' . rawurlencode($url) . '" width="170" height="145">
						<param name="wmode" value="transparent" />
						<param name="movie" value="' . $this->options['baseURL'] . 'themes/admin/flash/mediaplayer/player.swf?file=' . rawurlencode($url) . '" />
					</object>
				</div>
				
				<dl>
					<dt>${width}</dt><dd>' . @$getid3->info['video']['resolution_x'] . ' px</dd>
					<dt>${height}</dt><dd>' . @$getid3->info['video']['resolution_y'] . ' px</dd>
					<dt>${video_codec}</dt><dd>' . @$getid3->info['video']['codec'] . '</dd>
					<dt>${length}</dt><dd>' . @$getid3->info['playtime_string'] . '</dd>
				</dl>';
		}
		
		echo json_encode(array(
			'content' => $content ? $content : '<div class="margin">
				${nopreview}<br/><button value="' . $url . '">${download}</button>
			</div>'
		));

		die();
	}
	
	protected function onDestroy(){
		if (!$this->options['destroy'] || empty($this->post['directory']) || empty($this->post['file'])) return;
		
		$file = realpath($this->path . '/' . $this->post['directory'] . '/' . $this->post['file']);
		if (!$this->checkFile($file)) return;
		
		$this->unlink($file);
		
		echo json_encode(array(
			'content' => 'destroyed'
		));
		
		die();
	}
	
	protected function onCreate()
	{
		if (empty($this->post['directory']) || empty($this->post['file'])) return;
		
		$file = $this->getName($this->post['file'], $this->getDir($this->post['directory']));
		if (!$file) return;
		
		mkdir($file);
		
		$this->onView();
	}
	
	protected function onUpload()
	{
		try
		{
			// Check if Upload is enabled
			if (!$this->options['upload'])
				throw new FileManagerException('disabled');

			// The following is done by /application/controllers/admin/media.php
			// if (empty($this->get['directory']) || (function_exists('UploadIsAuthenticated') && !UploadIsAuthenticated($this->get)))
			//	throw new FileManagerException('authenticated');
			
			$dir = $this->getDir($this->post['directory']);

			$name = pathinfo((Upload::exists('Filedata')) ? $this->getName($_FILES['Filedata']['name'], $dir) : null, PATHINFO_FILENAME);

			// Move the file
			$file = Upload::move('Filedata', $dir . '/', array
			(
				'name' => $name,
				'extension' => $this->options['safe'] && $name && in_array(strtolower(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION)), array('exe', 'dll', 'php', 'php3', 'php4', 'php5', 'phps')) ? 'txt' : null,
				'size' => $this->options['maxUploadSize'],
				'mimes' => $this->getAllowedMimeTypes()
			));

			// Resize the picture if needed
			if (FileManagerUtility::startsWith(Upload::mime($file), 'image/') && !empty($this->post['resizeImages']))
			{
				$mh = $this->options['pictureMaxHeight'];
				$mw = $this->options['pictureMaxWidth'];
				
				if ($mw != FALSE OR $mh != FALSE)
				{
					$img = new Image($file);
					
					// Check the width
					$size = $img->getSize();
					if ($mw != FALSE && $size['width'] > $mw)
						$img->resize($mw)->save();
					
					// Check again, but the height
					$size = $img->getSize();
					if ($mh != FALSE && $size['height'] > $mh)
						$img->resize(null, $mh)->save();
				}
			}

			echo json_encode(array(
				'status' => 1,
				'name' => pathinfo($file, PATHINFO_BASENAME)
			));
			
			die();
		}
		catch(UploadException $e)
		{
			echo json_encode(array
			(
				'status' => 0,
				'error' => '${upload.' . $e->getMessage() . '}'
			));
			die();
		}
		catch(FileManagerException $e)
		{
			echo json_encode(array
			(
				'status' => 0,
				'error' => '${upload.' . $e->getMessage() . '}'
			));
			die();
		}
	}
	
	/* This method is used by both move and rename */
	protected function onMove()
	{
		if (empty($this->post['directory']) || empty($this->post['file'])) return;
		
		$rename = empty($this->post['newDirectory']) && !empty($this->post['name']);
		$dir = $this->getDir($this->post['directory']);
		$file = realpath($dir . '/' . $this->post['file']);
		
		$is_dir = is_dir($file);
		if (!$this->checkFile($file) || (!$rename && $is_dir))
			return;
		
		if ($rename || $is_dir){
			if (empty($this->post['name'])) return;
			$newname = $this->getName($this->post['name'], $dir);
			$fn = 'rename';
		}else{
			$newname = $this->getName(pathinfo($file, PATHINFO_FILENAME), $this->getDir($this->post['newDirectory']));
			$fn = !empty($this->post['copy']) ? 'copy' : 'rename';
		}
		
		if (!$newname) return;
		
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		if ($ext) $newname .= '.' . $ext;
		$fn($file, $newname);
		
		echo json_encode(array(
			'name' => pathinfo($this->normalize($newname), PATHINFO_BASENAME),
		));
	}

	/**
	 * Returns the thumbs of files from one folder
	 *
	 */
	protected function onFill()
	{
		$dir = $this->getDir(!empty($this->post['directory']) ? $this->post['directory'] : null);
		$files = ($files = glob($dir . '/*')) ? $files : array();
		
		if ($dir != $this->basedir) array_unshift($files, $dir . '/..');
		natcasesort($files);
		
		$out = array();
		
		foreach ($files as $file)
		{
			$mime = $this->getMimeType($file);
			$filename = pathinfo($file, PATHINFO_BASENAME);

			if ($mime != 'text/directory')
			{
				if ($this->options['filter'] && $mime != 'text/directory' && !FileManagerUtility::startsWith($mime, $this->options['filter']))
					continue;
				
				$out[] = array(
					'name' => pathinfo($file, PATHINFO_BASENAME),
					'date' => date($this->options['dateFormat'], filemtime($file)),
					'mime' => $this->getMimeType($file),
					'icon' => $this->getIcon($this->normalize($file)),
					'size' => filesize($file),
//					'thumb' => (FileManagerUtility::startsWith($mime, 'image/')) ?	$this->options['baseURL'] . $this->normalize(substr($this->thumbDir . str_replace($this->basedir, '', $file), strlen($this->path)+1)) : '',
//					'path' => $thumbfile = $this->thumbDir . str_replace($this->basedir, '', $file)
					'path' => $file
				);
				
			}
			
		}
		
		echo json_encode(array(
			'path' => $this->getPath($dir),
			'dir' => array(
				'name' => pathinfo($dir, PATHINFO_BASENAME),
				'date' => date($this->options['dateFormat'], filemtime($dir)),
				'mime' => 'text/directory',
				'icon' => 'dir'
			),
			'files' => $out
		));
	}
	
	
	/**
	 * Get the URL of one thumb
	 * If the thumb doesn't exists, try to create it.
	 *
	 */
	protected function onThumb()
	{
		$path = (!empty($this->post['path']) ? $this->post['path'] : null);
		
		if( ! is_null($path))
		{
			$thumbfile = $this->createThumb($path);
			
			$thumburl = $this->options['baseURL'] . $this->normalize(substr($thumbfile, strlen($this->path)+1));

			echo json_encode(array(
				'url' => $thumburl
			));
			
//			die();
		}
	}
	
	protected function onUploadUrl()
	{
	
		$image = new GetImage();
	
		foreach($_POST as $key => $url)
		{
			$options = array(
				'source' => $url,
				'save_to' => $this->basedir
			);
			$image->init($options);
			
			$get = $image->download('curl');
			
			trace($get);
		}
		



	
	}
	
	
	
	
	protected function unlink($file){
		$file = realpath($file);
		if ($this->basedir==$file || strlen($this->basedir)>=strlen($file))
			return;
		
		if (is_dir($file)){
			$files = glob($file . '/*');
			if (is_array($files))
				foreach ($files as $f)
					$this->unlink($f);
				
			rmdir($file);
		}else{
			try{ if ($this->checkFile($file)) unlink($file); }catch(Exception $e){}
		}
	}
	
	protected function getName($file, $dir){
		$files = array();
		foreach ((array)glob($dir . '/*') as $f)
			$files[] = pathinfo($f, PATHINFO_FILENAME);
		
		$pathinfo = pathinfo($file);
		$file = $dir . '/' . FileManagerUtility::pagetitle($pathinfo['filename'], $files).(!empty($pathinfo['extension']) ? '.' . $pathinfo['extension'] : null);
		
		return !$file || !FileManagerUtility::startsWith($file, $this->basedir) || file_exists($file) ? null : $file;
	}
	
	protected function getIcon($file)
	{
		if (FileManagerUtility::endsWith($file, '/..')) return 'dir_up';
		else if (is_dir($file)) return 'dir';
		
		$ext = pathinfo($file, PATHINFO_EXTENSION);

		return ($ext && file_exists(realpath($this->options['assetBasePath'] . '/Icons/' . $ext . '.png'))) ? $ext : 'default';
	}

	protected function getMimeType($file){
		return is_dir($file) ? 'text/directory' : Upload::mime($file);
	}
	
	protected function getDir($dir){
		$dir = realpath($this->path . '/' . (FileManagerUtility::startsWith($dir, $this->basename) ? $dir : $this->basename));
		return $this->checkFile($dir) ? $dir : $this->basedir;
	}
	
	protected function getPath($file){
		$file = $this->normalize(substr($file, $this->length));
		return substr($file, FileManagerUtility::startsWith($file, '/') ? 1 : 0);
	}


	protected function checkFile($file){
		$mimes = $this->getAllowedMimeTypes();
		$hasFilter = $this->options['filter'] && count($mimes);
		if ($hasFilter) array_push($mimes, 'text/directory');
		return !(!$file || !FileManagerUtility::startsWith($file, $this->basedir) || !file_exists($file) || ($hasFilter && !in_array($this->getMimeType($file), $mimes)));
	}
	
	protected function normalize($file){
		return preg_replace('/\\\|\/{2,}/', '/', $file);
	}
	
	protected function getAllowedMimeTypes(){
		$filter = $this->options['filter'];
		
		if (!$filter) return null;
		if (!FileManagerUtility::endsWith($filter, '/')) return array($filter);
		
		static $mimes;
		if (!$mimes) $mimes = parse_ini_file($this->options['mimeTypesPath']);
		
		foreach ($mimes as $mime)
			if (FileManagerUtility::startsWith($mime, $filter))
				$mimeTypes[] = strtolower($mime);
		
		return $mimeTypes;
	}
	
	protected function createThumb($file)
	{
		$filename = pathinfo($file, PATHINFO_BASENAME);
		$thumbfile = $this->thumbDir . str_replace($this->basedir, '', $file);
		$thumbpath = str_replace($filename, '', $thumbfile);

		if ( ! is_dir($thumbpath)) mkdir($thumbpath, $this->options['dirPerms'], true);

		if ( ! file_exists($thumbfile))
		{
			$img = new Image($file);
			
			$size = $img->getSize();
			
			if ($size['width'] > $this->options['thumbSize'] || $size['height'] > $this->options['thumbSize'])
				$resize = ($size['width'] > $size['height']) ? $img->resize($this->options['thumbSize'], null) : $img->resize(null, $this->options['thumbSize']);
			
			$img->save($thumbfile);
		}
		
		return($thumbfile);
	}

}

class FileManagerException extends Exception {}

/* Stripped-down version of some Styx PHP Framework-Functionality bundled with this FileBrowser. Styx is located at: http://styx.og5.net */
class FileManagerUtility {
	
	public static function endsWith($string, $look){
		return strrpos($string, $look)===strlen($string)-strlen($look);
	}
	
	public static function startsWith($string, $look){
		return strpos($string, $look)===0;
	}
	
	public static function pagetitle($data, $options = array()){
		static $regex;
		if (!$regex){
			$regex = array(
				explode(' ', 'Æ æ Œ œ ß Ü ü Ö ö Ä ä À Á Â Ã Ä Å &#260; &#258; Ç &#262; &#268; &#270; &#272; Ð È É Ê Ë &#280; &#282; &#286; Ì Í Î Ï &#304; &#321; &#317; &#313; Ñ &#323; &#327; Ò Ó Ô Õ Ö Ø &#336; &#340; &#344; Š &#346; &#350; &#356; &#354; Ù Ú Û Ü &#366; &#368; Ý Ž &#377; &#379; à á â ã ä å &#261; &#259; ç &#263; &#269; &#271; &#273; è é ê ë &#281; &#283; &#287; ì í î ï &#305; &#322; &#318; &#314; ñ &#324; &#328; ð ò ó ô õ ö ø &#337; &#341; &#345; &#347; š &#351; &#357; &#355; ù ú û ü &#367; &#369; ý ÿ ž &#378; &#380;'),
				explode(' ', 'Ae ae Oe oe ss Ue ue Oe oe Ae ae A A A A A A A A C C C D D D E E E E E E G I I I I I L L L N N N O O O O O O O R R S S S T T U U U U U U Y Z Z Z a a a a a a a a c c c d d e e e e e e g i i i i i l l l n n n o o o o o o o o r r s s s t t u u u u u u y y z z z'),
			);
			
			$regex[0][] = '"';
			$regex[0][] = "'";
		}
		
		$data = trim(substr(preg_replace('/(?:[^A-z0-9]|_|\^)+/i', '_', str_replace($regex[0], $regex[1], $data)), 0, 64), '_');
		return !empty($options) ? self::checkTitle($data, $options) : $data;
	}
	
	protected static function checkTitle($data, $options = array(), $i = 0){
		if (!is_array($options)) return $data;
		
		foreach ($options as $content)
			if ($content && strtolower($content) == strtolower($data.($i ? '_' . $i : '')))
				return self::checkTitle($data, $options, ++$i);
		
		return $data.($i ? '_' . $i : '');
	}
	
	public static function isBinary($str){
		$array = array(0, 255);
		for($i = 0; $i < strlen($str); $i++)
			if (in_array(ord($str[$i]), $array)) return true;
		
		return false;
	}
	
	public static function getPath(){
		static $path;
		return $path ? $path : $path = pathinfo(__FILE__, PATHINFO_DIRNAME);
	}
	
  	/** 
  	 * Checks if the given directory is really writable.
  	 * From kcFinder helper_dir.php
  	 * The standard PHP function is_writable() does not work properly on Windows servers
  	 * @author Pavel Tzonkov <pavelc@users.sourceforge.net>
     * @param string $dir
     * @return bool 
     */
	public static function isDirWritable($path){
		if (!is_dir($path))
		    return false;
		$i = 0;
		do {
		    $file = "$path/is_writable_" . md5($i++);
		} while (file_exists($file));
		if (!@touch($file))
		    return false;
		unlink($file);
		return true;
	}
}

class FileManagerUrl
{

	public static function check_url($url)
	{
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($c, CURLOPT_NOBODY, true);
		$output = @curl_exec($c);
		
		if($output !== FALSE)
		{
			$httpCode = curl_getinfo($c, CURLINFO_HTTP_CODE);
			
			trace($c);
			
			
			curl_close($c);		
			return $httpCode;
		}
		return FALSE;
	}


}

