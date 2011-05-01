<?php

error_reporting(E_ALL | E_STRICT);

define('FILEMANAGER_CODE', true);


define('SITE_USES_ALIASES', 01);
define('DEVELOPMENT', 01);   // set to 01 / 1 to enable logging of each incoming event request.


require('FM-common.php');


/*
As AJAX calls cannot set cookies, we set up the session for the authentication demonstration right here; that way, the session cookie
will travel with every request.
*/
session_name('alt_session_name');
if (!session_start()) die('session_start() failed');


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>MooTools FileManager Backend Testground</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="demos.css" type="text/css" />

	<script type="text/javascript" src="mootools-core.js"></script>
	<script type="text/javascript" src="mootools-more.js"></script>

	<script type="text/javascript" src="../Source/FileManager.js"></script>
	<script type="text/javascript" src="../Source/Gallery.js"></script>
	<script type="text/javascript" src="../Source/Uploader/Fx.ProgressBar.js"></script>
	<script type="text/javascript" src="../Source/Uploader/Swiff.Uploader.js"></script>
	<script type="text/javascript" src="../Source/Uploader.js"></script>
	<script type="text/javascript" src="../Language/Language.en.js"></script>
	<script type="text/javascript" src="../Language/Language.de.js"></script>

	<script type="text/javascript">
		window.addEvent('domready', function() {

		});
	</script>
</head>
<body>
<div id="content" class="content">
	<div class="go_home">
		<a href="index.php" title="Go to the Demo index page"><img src="home_16x16.png"> </a>
	</div>

	<h1>FileManager Backend Tests</h1>

	<h2>Basic PHP tests</h2>
	<pre>
<?php


$browser = new FileManagerWithAliasSupport(array(
	'directory' => 'Files/',                   // relative paths: are relative to the URI request script path, i.e. dirname(__FILE__)
	'thumbnailPath' => 'Files/Thumbnails/',
	//'assetBasePath' => '../Assets',
	'chmod' => 0777,
	//'maxUploadSize' => 1024 * 1024 * 5,
	//'upload' => false,
	//'destroy' => false,
	//'create' => false,
	//'move' => false,
	//'download' => false,
	//'filter' => 'image/',
	'allowExtChange' => true,                  // allow file name extensions to be changed; the default however is: NO (FALSE)
	'UploadIsAuthorized_cb' => 'FM_IsAuthorized',
	'DownloadIsAuthorized_cb' => 'FM_IsAuthorized',
	'CreateIsAuthorized_cb' => 'FM_IsAuthorized',
	'DestroyIsAuthorized_cb' => 'FM_IsAuthorized',
	'MoveIsAuthorized_cb' => 'FM_IsAuthorized'

	// http://httpd.apache.org/docs/2.2/mod/mod_alias.html -- we only emulate the Alias statement. (Also useful for VhostAlias, BTW!)
	// Implementing other path translation features is left as an exercise to the reader:
	, 'Aliases' => array(
	//  '/c/lib/includes/js/mootools-filemanager/Demos/Files/alias' => "D:/xxx",
	//  '/c/lib/includes/js/mootools-filemanager/Demos/Files/d' => "D:/xxx.tobesorted",
	//  '/c/lib/includes/js/mootools-filemanager/Demos/Files/u' => "D:/websites-uploadarea",

	//  '/c/lib/includes/js/mootools-filemanager/Demos/Files' => "D:/experiment"
	)
));

echo "\n\n";
$settings = $browser->getSettings();
var_dump($settings);

?>
	</pre>
	<h2>Important server variables</h2>

	<p>$_SERVER['DOCUMENT_ROOT'] = '<?php echo $_SERVER['DOCUMENT_ROOT']; ?>'</p>
	<p>$_SERVER['SCRIPT_NAME'] = '<?php echo $_SERVER['SCRIPT_NAME']; ?>'</p>



	<h3>FM 'detail' output</h3>

	<pre>
<?php
$dump = '{}';
ob_start();
	$_POST = array();
	$_POST['directory'] = '/'; // '/bugger/';
	$_POST['file'] = '0[]~!@()-._  90826039_1_1_3.jpg';
	$_POST['mode'] = 'auto';
	$browser->fireEvent('detail');
$dump = ob_get_clean();

echo "input:\n";
var_dump($_POST);
echo "\n\noutput for event 'detail':\n\n";

$json = json_decode($dump);
var_dump($json);

?>
	<hr />


	<pre>
<?php
$dump = '{}';
ob_start();
	$_POST = array();
	$_POST['directory'] = '/'; // '/bugger/';
	$_POST['file'] = 'MO mc 5.jpg';
	$_POST['mode'] = 'direct';
	$browser->fireEvent('detail');
$dump = ob_get_clean();

echo "input:\n";
var_dump($_POST);
echo "\n\noutput for event 'detail':\n\n";

//echo "</pre><div>" . htmlentities($dump, ENT_NOQUOTES, 'UTF-8') . "</div><pre>\n";
//echo "\n\n";

$json = json_decode($dump);
var_dump($json);

?>
	<hr />

</div>
</body>
</html>
