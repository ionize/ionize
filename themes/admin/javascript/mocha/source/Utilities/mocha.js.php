<?php

// Array of files to merge
$files = array(
	'Core/Core.js',
	'Window/Window.js',
	'Window/Modal.js',
//	'Window/Windows-from-html.js',
//	'Window/Windows-from-json.js',
//	'Window/Arrange-cascade.js',
//	'Window/Arrange-tile.js',
//	'Window/Tabs.js',
	'Layout/Layout.js'
//	'Layout/Dock.js',
//	'Layout/Workspaces.js'
);

// Get the path to your web directory
$docRoot = dirname(__FILE__);
while (preg_match('/\\\\/',$docRoot)) {$docRoot = preg_replace('/\\\\/','/',$docRoot);}
while (preg_match('/\/\//',$docRoot)) {$docRoot = preg_replace('/\/\//','/',$docRoot);}
$docRoot = preg_replace('/\/$/','',$docRoot);
$docRoot = preg_replace('/\/Utilities$/','',$docRoot);

// Merge code
$code = '';
foreach ($files as $file) {
	$code .= file_get_contents("$docRoot/$file");
}

$filename = "mocha.js";	 

// Send HTTP headers
header("Cache-Control: must-revalidate");
header("Content-Type: text/javascript");
header('Content-Length: '.strlen($code));
header("Content-Disposition: inline; filename=$filename");

// Output merged code
echo $code;

?>