<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| MIME TYPES
| -------------------------------------------------------------------
| This file contains an array of mime types.  
| It is used by the Media / Settings classes to identify allowed mimes
|
*/

$mimes_ionize = array(
	'picture' => array(
		'bmp'=>'image/bmp',
		'gif'=>'image/gif',
		'jpe'=>'image/jpeg',
		'jpeg'=>'image/jpeg',
		'jpg'=>'image/jpeg,*',
		'png'=>'image/png',
		'psd'=>'image/psd',
		'tif'=>'image/tiff',
		'tiff'=>'image/tiff',
	),
	'video'		=> array(
		'avi'=>'video/avi',
		'flv'=>'video/x-flv',
		'm1v'=>'video/mpeg',
		'm2v'=>'video/mpeg',
		'mkv'=>'video/x-matroska',
		'mov'=>'video/quicktime',
		'mpe'=>'video/mpeg',
		'mpeg'=>'video/mpeg',
		'mp4'=>'video/mp4',
		'mpg'=>'video/mpeg,*',
		'ogv'=>'video/ogv',
		'qt'=>'video/quicktime',
		'wmv'=>'video/x-ms-wmv',
		'asf'=>'video/x-ms-asf',
		'rm'=>'video/x-realvideo',
		'rmvb'=>'video/x-realvideo'	
	),
	'music'		=> array(
		'aif'=>'audio/x-aiff',
		'aifc'=>'audio/x-aiff',
		'aiff'=>'audio/x-aiff,*',
		'au'=>'audio/basic',
		'mid'=>'audio/midi,*',
		'midi'=>'audio/midi',
		'mp2'=>'audio/mpeg',
		'mp3'=>'audio/mpeg,*',
		'ra'=>'audio/x-realaudio',
		'ram'=>'audio/x-pn-realaudio',
		'rpm'=>'audio/x-pn-realaudio-plugin',
		'wav'=>'audio/x-wav'
	),
	'file'		=> array(
		'doc'		=>	'application/msword',
		'eps'		=>	'application/postscript',
		'exe'		=>	'application/octet-stream',
		'gtar'		=>	'application/x-gtar',
		'gz'		=>	'application/x-gzip',
		'iso'		=>	'application/x-isoview',
		'lha'		=>	'application/octet-stream',
		'lzh'		=>	'application/octet-stream',
		'pdf'		=>	'application/pdf',
		'pot'		=>	'application/mspowerpoint',
		'pps'		=>	'application/mspowerpoint',
		'ppt'		=>	'application/mspowerpoint, *',
		'ppz'		=>	'application/mspowerpoint',
		'ps'		=>	'application/postscript, *',
		'rar'		=>	'application/x-rar',
		'rtf'		=>	'text/rtf',
		'tar'		=>	'application/x-tar',
		'txt'		=>	'text/plain,*',
		'xlc'		=>	'application/vnd.ms-excel',
		'xll'		=>	'application/vnd.ms-excel',
		'xlm'		=>	'application/vnd.ms-excel',
		'xls'		=>	'application/vnd.ms-excel, *',
		'xlw'		=>	'application/vnd.ms-excel',
		'zip'		=>	'application/zip'
	)
);
	

/* End of file mimes_ionize.php */
/* Location: ./system/application/config/mimes_ionize.php */