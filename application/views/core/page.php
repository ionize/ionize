<!DOCTYPE html>
<html lang="<ion:current_lang />">
<head>

	<title><ion:meta_title /> | <ion:site_title /></title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="<ion:meta_description />" />
	<meta name="keywords" content="<ion:meta_keywords />" />
	
</head>

<body>

<!-- Language menu -->
<ion:languages tag="ul">
    <li><a href="<ion:language:url />"><ion:language:name /></a></li>
</ion:languages>


<!-- Navigation -->
<ion:navigation level="0" tag="ul" class="navigation" active_class="active" />

<!-- Page title -->
<ion:page:title tag="h2" />


<!-- Articles -->
<div id="content">

    <ion:page>

        <ion:articles>

            <ion:article:title />

            <ion:article:content />

            <!-- Articles linked pictures -->
            <ion:article:medias type="picture">

                <img src="<ion:media:src />" />

            </ion:article:medias>


		</ion:articles>

    </ion:page>

</div>


</body>
</html>
