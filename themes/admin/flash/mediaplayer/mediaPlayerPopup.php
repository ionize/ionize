<?php
$flvName =	$HTTP_GET_VARS["flvName"];
$title =	substr($flvName, 0, strpos($flvName, "~"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<style>
body {
	padding:0;
	margin:0;
}
</style>

<script type='text/javascript' src='swfobject.js'></script>

</head>

<body onLoad="window.focus();">

	<div id="video"></div>

	<script type='text/javascript'>
		var s1 = new SWFObject('./player.swf','player','640','500','9');
		s1.addParam('allowfullscreen','true');
		s1.addParam('allowscriptaccess','always');
		
		s1.addParam('flashvars','file=<?=$HTTP_GET_VARS["flvName"]?>');
		
		s1.write('video');
	</script>

</body>
</html>