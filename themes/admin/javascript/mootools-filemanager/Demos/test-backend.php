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
</head>
<body>
<div id="content" class="content">
	<div class="go_home">
		<a href="index.php" title="Go to the Demo index page"><img src="home_16x16.png"> </a>
	</div>

	<h1>FileManager Backend Tests</h1>

	<p>
	<a href="test-backend-basics.php">basic tests</a>
	</p>

	<p>
	<a href="test-backend-dirscan.php">dirscan tests</a>
	</p>

	<p>
	<a href="test-backend-detail.php">'detail' event tests</a>
	</p>

	<p>
	<a href="test-backend-view.php">'view' event tests</a>
	</p>
</div>
</body>
</html>


