<?php

$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$base_url .= "://".$_SERVER['HTTP_HOST'];

header('Location: '.$base_url);
header('HTTP/1.1 301 Moved Permanently');
header('Status: 301 Moved Permanently');
header('Content-Type: text/html; charset=UTF-8');

$base_url = htmlspecialchars($base_url,ENT_QUOTES);

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>'."\n",
 '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n",
 '<html xmlns="http://www.w3.org/1999/xhtml">'."\n",
 '<head>'."\n",
 '<meta http-equiv="content-type" content="text/html; charset=UTF-8" />'."\n",
 '<meta http-equiv="refresh" content="0; url='.$base_url.'" />'."\n",
 '<title>Redirection</title>'."\n",
 '<meta name="robots" content="noindex,follow" />'."\n",
 '</head>'."\n",
 "\n",
 '<body>'."\n",
 '<p><a href="'.$base_url.'">Redirection</a></p>'."\n",
 '</body>'."\n",
 '</html>'."\n";

?>
