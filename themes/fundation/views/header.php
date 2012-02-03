<!DOCTYPE html>

<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="<ion:current_lang />"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="<ion:current_lang />"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="<ion:current_lang />"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="<ion:current_lang />"> <!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />

	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width" />
	
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="revisit-after" content="15 days" />
	
	<meta name="description" content="<ion:meta_description />" />
	<meta name="keywords" content="<ion:meta_keywords />" />
	<meta name="language" content="<ion:current_lang />" />

	<title><ion:meta_title /> | <ion:site_title /></title>
  
	<!-- Included CSS Files -->
	<link rel="stylesheet" href="<ion:theme_url />assets/stylesheets/foundation.css">
	<link rel="stylesheet" href="<ion:theme_url />assets/stylesheets/app.css">

	<!--[if lt IE 9]>
		<link rel="stylesheet" href="<ion:theme_url />assets/stylesheets/ie.css">
	<![endif]-->


	<!-- IE Fix for HTML5 Tags -->
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Ionize JS Lang object -->
	<ion:jslang />

	<!-- if JS needs to get the theme URL, we give it to him -->
	<script type="text/javascript">
		var theme_url = '<ion:theme_url />';
	</script>

</head>

<body>

	<!-- container -->
	<div class="container">

		<div class="row">
			<div class="eight columns">

				<!-- Site title (logo in fact) -->
				<h1><ion:site_title /></h1>
				<p>Fundation Theme. Based on Fundation version 2.1.5 released on January 26, 2012</p>
			</div>

			<div class="four columns">

				<!-- Language menu -->
				<ion:languages tag="ul" class="languages">
					<li><a href="<ion:url />"><ion:name /></a></li>
				</ion:languages>

			</div>
			<hr />
		</div>
		
		<div class="row">
			<div class="twelve columns">
			
				<!-- Navigation -->
				<ion:navigation level="0" tag="div" class="row navigation" active_class="active" helper="false" >
					<a class="small blue button radius" href="<ion:url />"><ion:title /></a>
				</ion:navigation>
			
			</div>
		</div>
		


