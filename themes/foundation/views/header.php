<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="<ion:language:code />" dir="<ion:language:dir />"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="<ion:language:code />" dir="<ion:language:dir />"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="<ion:language:code />" dir="<ion:language:dir />"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="<ion:language:code />" dir="<ion:language:dir />"> <!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />

	<!-- Set the viewport width to device width for mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="revisit-after" content="15 days" />
	
	<meta name="description" content="<ion:meta_description />" />
	<meta name="keywords" content="<ion:meta_keywords />" />
	<meta name="language" content="<ion:current_lang />" />

	<title><ion:meta_title /> | <ion:site_title /></title>
  
	<!-- Included CSS Files -->
	<link rel="stylesheet" href="<ion:theme_url />assets/stylesheets/foundation.min.css">
	<link rel="stylesheet" href="<ion:theme_url />assets/stylesheets/app.css">

    <script type="text/javascript" src="<ion:theme_url />javascripts/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="<ion:theme_url />javascripts/foundation.min.js"></script>
    <script type="text/javascript" src="<ion:theme_url />javascripts/jquery.foundation.topbar.js"></script>
    <script type="text/javascript" src="<ion:theme_url />javascripts/jquery.foundation.orbit.js"></script>
    <script type="text/javascript" src="<ion:theme_url />javascripts/jquery.foundation.forms.js"></script>
    <script type="text/javascript" src="<ion:theme_url />javascripts/app.js"></script>

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

	<ion:google_analytics />

</head>

<body>

	<!-- container -->
	<div class="container">

        <div class="contain-to-grid">

            <nav class="top-bar">
                <ul>
					<!--
						Website Logo
					-->
                    <li class="name"><h1><a href="<ion:home_url />"><ion:site_title /></a></h1></li>
                    <li class="toggle-topbar"><a href="#"></a></li>
                </ul>
                <section>

					<!--
						Language Selector
					-->
					<ion:languages tag="ul" class="right">

						<li <ion:language:is_active> class="active"</ion:language:is_active>>
							<a href="<ion:language:url />">
								<ion:language:code />
							</a>
						</li>

					</ion:languages>

					<!--
						Navigation Menu
					-->
                    <ul class="right">
                        <ion:navigation level="0"  active_class="active"  >
                            <li>
                                <a href="<ion:url />"><ion:title /></a>
                            </li>
                        </ion:navigation>
                    </ul>

                </section>
            </nav>
		</div>


