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
		$check = array
		(
			// PHP version >= 5
			'php version' => version_compare(substr(phpversion(), 0, 3), '5.2', '>='),
	
			// MySQL support
			'mysql support'  => function_exists('mysql_connect'),
			
			// Files upload
			'file upload' => ini_get('file_uploads'),
			
			// GD lib
			'GD lib' => function_exists('imagecreatetruecolor'),
			
			// Mod_rewrite
			'mod_rewrite' => in_array('mod_rewrite', @apache_get_modules())
		);

		self::output($check);
	}



	// --------------------------------------------------------------------


	function output($arr)
	{
		$str = '';		
		foreach($arr as $key => $value)
		{
			$result = ($value) ? '<strong style="color:#0a0;">OK</strong>' : '<strong style="color:#b00;">Error</strong>';
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

