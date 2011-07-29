<?php 
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.7
 */

class System_check
{
	/**
	 * Checks the config settings
	 *
	 */
	function check_config()
	{
		error_reporting(1);
		
		$check = array
		(
			// PHP version >= 5
			'php version' => (version_compare(substr(phpversion(), 0, 3), '5.2', '>=')) ? 'OK' : 'Error',
	
			// MySQL support
			'mysql support'  => (function_exists('mysql_connect')) ? 'OK' : 'Error',
			
			// Files upload
			'file upload' => (ini_get('file_uploads')) ? 'OK' : 'Error',
			
			// GD lib
			'GD lib' => (function_exists('imagecreatetruecolor')) ? 'OK' : 'Error'		
		);
		
		if (function_exists('apache_get_modules'))
		{
			$check['mod_rewrite'] = (in_array('mod_rewrite', @apache_get_modules())) ? 'OK' : 'Error';
		}
		else
		{
			$check['mod_rewrite'] = "Can't be tested";
		}
		
		if ($check['file upload'] == 'OK')
		{
			$check['file Max Size'] = ini_get('upload_max_filesize');
		}
		

		self::out($check);
	}



	// --------------------------------------------------------------------


	static function out($arr)
	{
		$str = '';		
		foreach($arr as $key => $value)
		{
			$result = ($value == 'OK') ? '<strong style="color:#0a0;">'.$value.'</strong>' : '<strong style="color:#b00;">'.$value.'</strong>';
			$str .= '<li>' . $key . ' : ' . $result . '</li>';
		}
		
		$str = '<html>
				<head>
					<style>
						body{
							font-family: Arial, sans-serif;
							padding:10px;
							margin:0;
						}
						ul{
							margin:0;
						}
					</style>
				</head>
				<body>
				<h1>ionize server check</h1>
				<ul>'.$str.'<ul>
				</body>
				</html>';
		
		echo $str;
	}
	
}

System_check::check_config();

?>