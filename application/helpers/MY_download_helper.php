<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function force_download($filename = '', $data = false, $enable_partial = true, $speedlimit = 0)
{
	if ($filename == '')
	{
		return FALSE;
	}

	if($data === false && !file_exists($filename))
		return FALSE;

	// Try to determine if the filename includes a file extension.
	// We need it in order to set the MIME type
	if (FALSE === strpos($filename, '.'))
	{
		return FALSE;
	}

	// Grab the file extension
	$x = explode('.', $filename);
	$extension = end($x);

	// Load the mime types
	@include(APPPATH.'config/mimes'.EXT);

	// Set a default mime if we can't find it
	if ( ! isset($mimes[$extension]))
	{
		if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
			$UserBrowser = "Opera";
		elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
			$UserBrowser = "IE";
		else
			$UserBrowser = '';

		$mime = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
	}
	else
	{
		$mime = (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
	}

	$size = $data === false ? filesize($filename) : strlen($data);

	if($data === false)
	{
		$info = pathinfo($filename);
		$name = $info['basename'];
	}
	else
	{
		$name = $filename;
	}

	// Clean data in cache if exists
	if (ob_get_length()) @ob_end_clean();

	// Check for partial download
	if(isset($_SERVER['HTTP_RANGE']) && $enable_partial)
	{
		list($a, $range) = explode("=", $_SERVER['HTTP_RANGE']);
		list($fbyte, $lbyte) = explode("-", $range);

		if(!$lbyte)
			$lbyte = $size - 1;

		$new_length = $lbyte - $fbyte;

		header("HTTP/1.1 206 Partial Content", true);
		header("Content-Length: $new_length", true);
		header("Content-Range: bytes $fbyte-$lbyte/$size", true);
	}
	else
	{
		header("Content-Length: " . $size);
	}

	// Common headers
	header('Content-Type: ' . $mime, true);
	header('Content-Disposition: attachment; filename="' . $name . '"', true);
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT", true);
	header('Accept-Ranges: bytes', true);
	header("Cache-control: private", true);
	header('Pragma: private', true);

	// Open file
	if($data === false) {
		$file = fopen($filename, 'r');

		if(!$file)
			return FALSE;
	}

	// Cut data for partial download
	if(isset($_SERVER['HTTP_RANGE']) && $enable_partial)
		if($data === false)
			fseek($file, $range);
		else
			$data = substr($data, $range);

	// Disable script time limit
	@set_time_limit(0);

	// Check for speed limit or file optimize
	if($speedlimit > 0 || $data === false)
	{
		if($data === false)
		{
			$chunksize = $speedlimit > 0 ? $speedlimit * 1024 : 512 * 1024;

			while(!feof($file) and (connection_status() == 0))
			{
				$buffer = fread($file, $chunksize);
				echo $buffer;
				flush();

				if($speedlimit > 0)
					sleep(1);
			}

			fclose($file);
		}
		else
		{
			$index = 0;
			$speedlimit *= 1024; //convert to kb

			while($index < $size and (connection_status() == 0))
			{
				$left = $size - $index;
				$buffersize = min($left, $speedlimit);

				$buffer = substr($data, $index, $buffersize);
				$index += $buffersize;

				echo $buffer;
				flush();
				sleep(1);
			}
		}
	}
	else
	{
		echo $data;
	}
}