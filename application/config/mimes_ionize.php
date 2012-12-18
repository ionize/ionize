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
		'bmp' =>	'image/bmp',
		'gif' =>	'image/gif',
		'jpe' =>	array('image/jpeg', 'image/pjpeg'),
		'jpeg' =>	array('image/jpeg', 'image/pjpeg'),
		'jpg' =>	array('image/jpeg', 'image/pjpeg'),
		'png' =>	array('image/png',  'image/x-png'),
		'psd' =>	'image/psd',
		'tif' =>	'image/tiff',
		'tiff' =>	'image/tiff',
	),
	'video'	=> array(
		'avi' =>	'video/avi',
		'flv' =>	'video/x-flv',
		'm1v' =>	'video/mpeg',
		'm2v' =>	'video/mpeg',
		'mkv' =>	'video/x-matroska',
		'mov' =>	'video/quicktime',
		'mpe' =>	'video/mpeg',
		'mpeg' =>	'video/mpeg',
		'mp4' =>	'video/mp4',
		'mpg' =>	'video/mpeg',
		'ogv' =>	'video/ogv',
		'qt' =>		'video/quicktime',
		'wmv' =>	'video/x-ms-wmv',
		'asf' =>	'video/x-ms-asf',
		'rm' =>		'video/x-realvideo',
		'rmvb' =>	'video/x-realvideo',
	),
	'music'	=> array(
		'aif' =>	'audio/x-aiff',
		'aifc' =>	'audio/x-aiff',
		'aiff' =>	'audio/x-aiff',
		'au' =>		'audio/basic',
		'mid' =>	'audio/midi',
		'midi' =>	'audio/midi',
		'mp2' =>	'audio/mpeg',
		'mp3' => 	array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
		'ra' =>		'audio/x-realaudio',
		'ram' =>	'audio/x-pn-realaudio',
		'rpm' =>	'audio/x-pn-realaudio-plugin',
		'wav' =>	'audio/x-wav',
	),
	'file'	=> array(
		'csv' =>	array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel'),
		'doc' =>	array('application/msword','application/octet-stream'),
		'docx' =>	'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'eps' =>	'application/postscript',
		'exe' =>	array('application/octet-stream', 'application/x-msdownload'),
		'gtar' =>	'application/x-gtar',
		'gz' =>		'application/x-gzip',
		'iso' =>	'application/x-isoview',
		'lha' =>	'application/octet-stream',
		'lzh' =>	'application/octet-stream',
		'pdf' =>	array('application/pdf', 'application/x-download'),
		'php' =>	array('application/x-httpd-php','text/php','application/octet-stream'),
		'pot' =>	'application/mspowerpoint',
		'pps' =>	'application/mspowerpoint',
		'ppt' =>	array('application/mspowerpoint', 'application/vnd.ms-powerpoint'),
		'ppz' =>	'application/mspowerpoint',
		'ps' =>		'application/postscript',
		'rar' =>	'application/x-rar',
		'rtf' =>	'text/rtf',
		'tar' =>	'application/x-tar',
		'txt' =>	array('text/plain', 'text/x-log'),
		'xlc' =>	'application/vnd.ms-excel',
		'xll' =>	'application/vnd.ms-excel',
		'xlm' =>	'application/vnd.ms-excel',
		'xls' =>	array('application/excel', 'application/vnd.ms-excel', 'application/msexcel'),
		'xlw' =>	'application/vnd.ms-excel',
		'zip' =>	array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
	)
);
	

/* End of file mimes_ionize.php */
/* Location: ./system/application/config/mimes_ionize.php */