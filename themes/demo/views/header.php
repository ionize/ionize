<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<ion:current_lang />" lang="<ion:current_lang />">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="<ion:current_lang />" />
	<title><ion:meta_title /> | <ion:site_title /></title>
	<meta name="description" content="<ion:meta_description />" />
	<meta name="keywords" content="<ion:meta_keywords />" />
	<meta name="language" content="<ion:current_lang />" />
	<meta http-equiv="imagetoolbar" content="no" />
	
	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/src/reset.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/src/ie.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/src/grid.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/src/typography.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/style.css" />

	<!-- Slimbox CSS -->
	<link rel="stylesheet" href="<ion:theme_url />javascript/slimbox/css/slimbox.css" />

	<!-- favicon -->
	<link rel="shortcut icon" href="<ion:theme_url />assets/images/favicon.ico" type="image/x-icon" />
	
	<!-- Mootools -->
	<script type="text/javascript" src="<ion:theme_url />javascript/mootools-core-1.3.2-full-nocompat-yc.js"></script>
	<script type="text/javascript" src="<ion:theme_url />javascript/mootools-more-1.3.2.1-yc.js"></script>
	<script type="text/javascript" src="<ion:theme_url />javascript/wall.js"></script>

	<script type="text/javascript" src="<ion:theme_url />javascript/slimbox/js/slimbox.js"></script>

	<!-- swfObject -->
	<script type="text/javascript" src="<ion:theme_url />flash/player/swfobject.js"></script>
		
	<!-- if JS needs to get the theme URL, we give it to him -->
	<script type="text/javascript">
		var theme_url = '<ion:theme_url />';
	</script>
	
	<!-- Translations availables for javascript (have a look at the produced code in firebug) -->
	<ion:jslang />

</head>

<body>
<!-- <center>Elapsed Time : {elapsed_time}</center> -->
<div id="container" class="container">

	<div id="header" class="span-22 prepend-1 append-1">

        <!-- Language Navigation
             View : <ul id="languages">
                        <li>....</li>
                    </ul>
        -->
        <ion:languages tag="ul" id="languages" />

		<div class="span-12">
			<div id="logo">
				<h1><ion:site_title /></h1>
				<a title="<ion:site_title />" href="<ion:base_url />"><ion:meta_title /></a>
			</div>
		</div>

		<div class="span-10 last searchform">

			<!-- Form using the "Search" module
				 Action : 	URL of the page containing the module's search result tags
				 Notes : 	The actionpage must not be called "search", because this is the module's default URI
				 			If you wish to change that, you need to add :
				 				<disable_controller>true</disable_controller>
				 			in the config.xml file of the module and to reinstall it through Ionize.
			-->
			<form method="post" action="<ion:base_url />search-result" class="singleinput right">
				<div><input name="realm" value="<ion:translation term="module_search_form" />" onblur="if (this.value == ''){this.value = '<ion:translation term="module_search_form" />'; }" onfocus="if (this.value == '<ion:translation term="module_search_form" />') {this.value = ''; }" type="text"/><input type="submit" name="submit" class="button_src" value=""/></div>
			</form>

		</div>
	
	</div>
    
    <div id="navigation" class="span-22 prepend-1 append-1">
		<ion:tree_navigation tag="ul" id="nav" class="sf-menu" active_class="current" last_class="nomargin" />
	</div>




