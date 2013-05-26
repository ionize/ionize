<!DOCTYPE html>
    <html lang="<ion:language:code />" dir="<ion:language:dir />">
    <head>
        <meta charset="utf-8">
        <title><ion:meta_title /> | <ion:site_title /></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="<ion:meta_description />" />
        <meta name="keywords" content="<ion:meta_keywords />" />
        <meta name="author" content="IonizeCMS Team" />

        <!-- Styles -->
        <link href='http://fonts.googleapis.com/css?family=Cutive+Mono|Anaheim|Merienda' rel='stylesheet' type='text/css'>
        <link href="<ion:theme_url />assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="<ion:theme_url />assets/css/bootstrap-responsive.min.css" rel="stylesheet">
        <link href="<ion:theme_url />assets/css/app.css" rel="stylesheet">
        <link href="<ion:theme_url />assets/css/fancybox.css" rel="stylesheet">

        <!-- Load Jquery Javascript Library -->
        <script src="<ion:theme_url />assets/js/jquery-1.8.2.min.js"></script>
        <script src="<ion:theme_url />assets/js/bootstrap.min.js"></script>

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- Fav and touch icons -->
        <link rel="shortcut icon" href="<ion:theme_url />assets/ico/favicon.ico">
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<ion:theme_url />assets/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<ion:theme_url />assets/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<ion:theme_url />assets/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="<ion:theme_url />assets/ico/apple-touch-icon-57-precomposed.png">
    </head>
    <body>
        <header>

            <div class="navbar navbar-inverse navbar-fixed-top">

                <div class="navbar navbar-inverse navbar-fixed-top">
                    <div class="navbar-inner">
                        <div class="container">
                            <a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </a>
                            <a href="<ion:home_url />" class="brand"><ion:site_title /></a>
                            <div class="nav-collapse collapse">
                                <ion:tree_navigation tag="ul" class="nav" active_class="active" />

                                <!-- Language Navigation -->
                                <!--
								<ion:languages tag="ul" active="active" class="nav languages">
									<li<ion:language:is_active> class="active"</ion:language:is_active>>
									<a href="<ion:language:url />" title="<ion:language:name />">
										<img src="<ion:theme_url />assets/images/flags/flag_<ion:language:id />.png" alt="<ion:language:name />" />
									</a>
									</li>
								</ion:languages>
								-->
							</div>
                        </div>
                    </div>
                </div>
            </div>

        </header>