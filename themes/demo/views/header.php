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
	
	<!-- Blueprint framework
	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/screen.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/ie.css" />
	<link rel="stylesheet" type="text/css" media="print" href="<ion:theme_url />assets/css/print.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/demo.css" />
	-->

	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/src/reset.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/src/ie.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/src/grid.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/src/typography.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/demo.css" />

	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/s3slider.css" />
	
	<!-- This Demo Theme CSS 
	<link rel="stylesheet" type="text/css" media="screen" href="<ion:theme_url />assets/css/demo.css" />-->
	
	<!-- favicon -->
	<link rel="shortcut icon" href="<ion:theme_url />assets/images/favicon.ico" type="image/x-icon" />
	
	<!-- jQuery -->
	<script type="text/javascript" src="<ion:theme_url />javascript/jquery-1.5.1.min.js"></script>

	<!-- Cufon -->
	<script type="text/javascript" src="<ion:theme_url />javascript/cufon-yui.js"></script>
	<script type="text/javascript" src="<ion:theme_url />javascript/Quicksand_Book_400.font.js"></script>
	<script type="text/javascript" src="<ion:theme_url />javascript/Quicksand_Light_300.font.js"></script>

	<!--
	<script type="text/javascript" src="<ion:theme_url />javascript/Khmer_UI_400-Khmer_UI_700.font.js"></script>
	<script type="text/javascript" src="<ion:theme_url />javascript/Century_Gothic_400-Century_Gothic_700-Century_Gothic_italic_400-Century_Gothic_italic_700.font.js"></script>
	-->

	<!-- swfObject -->
	<script type="text/javascript" src="<ion:theme_url />flash/player/swfobject.js"></script>
		
	<!-- jQuery plugins -->
	<script type="text/javascript" src="<ion:theme_url />javascript/jquery.easing.1.1.js"></script>
	<script type="text/javascript" src="<ion:theme_url />javascript/s3Slider.js"></script>
	<script type="text/javascript" src="<ion:theme_url />javascript/jquery.lavalamp.js"></script>
	<!--
	<script type="text/javascript" src="<ion:theme_url />javascript/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<script type="text/javascript" src="<ion:theme_url />javascript/jquery.highlight-1.1.js"></script>
	<link rel="stylesheet" type="text/css" href="<ion:theme_url />javascript/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
	-->

	<!-- This Demo Theme JS -->
	<script type="text/javascript" src="<ion:theme_url />javascript/demo.js"></script>
	
	<!-- if JS needs to get the theme URL, we give it to him -->
	<script type="text/javascript">
		var theme_url = '<ion:theme_url />';
	</script>
	
	<!-- Translations availables for javascript (have a look at the produced code in firebug) -->
	<ion:jslang />


</head>

<body>



<div id="container" class="container">

	<div id="header" class="span-22 prepend-1 append-1">

		<div class="span-12">
			<div id="logo">
				<h1><ion:site_title /></h1>
				<a title="<ion:site_title />" href="<ion:base_url />"><ion:meta_title /></a>
			</div>
		</div>
		
		<div class="span-10 last">
            <ul class="icons">
                <li><a href="#"><img src="<ion:theme_url />assets/images/i-email.png" alt=""  /></a></li>
                <li><a href="#"><img src="<ion:theme_url />assets/images/i-fb.png" alt=""  /></a></li>
                <li><a href="#"><img src="<ion:theme_url />assets/images/i-rss.png" alt=""  /></a></li>
                <li><a href="#"><img src="<ion:theme_url />assets/images/i-twitter.png" alt=""  /></a></li>
            </ul>
<!--
			<form method="post" action="<ion:base_url />recherche" id="search">
				<p>
					<label for="searchstring"><ion:translation term="form_search" /></label>
					<input type="text" id="searchstring" name="realm" value="" class="searchstring" alt="<ion:translation term="form_search" />" />
					<input type="submit" name="submit" value="" class="searchsubmit" alt="<ion:translation term="form_search_button" />" />
				</p>
			</form>

-->
		</div><!-- end #top-right -->
	</div>
    
    <div id="navigation" class="span-22 prepend-1 append-1">
		<ion:tree_navigation tag="ul" id="nav" class="sf-menu" active_class="current" last_class="nomargin" />
	</div>




