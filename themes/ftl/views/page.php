<html>
<head>
	<title>FTL tests</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<style>
		html{
			font-size: 0.9em;
		}
		pre {
			background: #f3f3f3;
			font-size: 0.9em;
			padding:10px;
			tab-size:4;
			-moz-tab-size: 4;
			-o-tab-size:   4;
		}
		pre * {
			line-height: 0.9em;
			margin:0; padding:0;
		}

		h2 {
			border-bottom: solid 1px #eee;
		}
		.red {
			color:#b00;
		}
		li a.active:after {
			content:" (is active)";
			color: #b00;
		}
		li a.my-active-class:after{
			content:" (is active)";
			font-size: 20px;
			color: #08b;
		}
		ul.boxes {
			list-style: none;
			margin: 0;padding: 0;
			overflow: hidden;
		}
		ul.boxes li {
			float: left;
			margin: 0 10px 10px 0;
			padding:5px;
			background-color: #f3f3f3;
		}
		ul.boxes li.first {
			float:none;
		}
		ul.boxes li.last {
			border-right: 5px solid #b00;
		}
		hr {
			border:none;
			border-bottom: 1px solid #ccc;
		}
	</style>
</head>

<body>

<!--
	Alternative : Put the header into one partial
	<:ion:partial view="header" />
-->

<!--
<pre>
<p>Pages IDs : 2, 3</p>
<p>Articles IDs : 10,20,30,40</p>
</pre>
-->

<?php
/*
 * Replace the called view with one of these test views
 *

// Site / Others
test_site_tags : 			keywords, meta, config item, etc. in all lang
test_tag_if :				Expression Test tag
test_tag_get :				Get any field from object, with DB field name


// Page
test_page_id

// Article, Article ID
test_article_id_1
test_article_id_2

test_content_iframe :		Content test : embedded iFrames from Youtube and Google maps
test_article_categories :	Categories from each article
test_article_user : 		User who wrote the article in Ionize
test_article_prev_next :	Previous / Next article builder
test_article_pagination : 	Pagination of articles

// Navigation
test_tag_navigation
test_tag_languages_menu
test_tag_tree_navigation

// Medias
test_tag_media_page :		Medias from pages
test_tag_media_articles :	Medias from articles

// Categories
test_tag_categories

// @TODO :
test_tag_archives :					Archives tag
test_categories_and_pagination : 	Pagination of articles withing categories
test_tag_medias_module : 			Medias within one module

test_tag_elements : 				Content Elements
test_tag_media_size
test_tag_subnavigation				Remove ???



test_temp : 				Temp dev's tests

*/
?>

<ion:partial view="test_tag_media_articles" />



