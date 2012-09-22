<ion:partial view="header" />


<?php
/**
 * Extended Tags Examples
 *

// Site / Others


// Page


// Article, Article ID


// Pagination


// Navigation
extended_tree_navigation_tag_test

// Medias
extended_medias_page_tag_test_1 // Sizing Images
extended_medias_page_tag_test_2 // Medias From Page (Types : Picture, Video)
extended_medias_article_tag_test_2 // Medias From Article (Types : Picture, Video)

// Categories


// Archives



*/

/**
 * Tags Simple examples
 * Replace the view used in the below partial tag with the name of the view to test
 *

// Site / Others
test_site_tags : 			keywords, meta, config item, etc. in all lang
test_condition_tag_if :		Condition Test tag
test_condition :			Expression Test tag
test_tag_get :				Get any field from object, with DB field name
test_trace :				Output's one print_r() of one tag locals.

// Page

test_tag_breadcrumb

// Article, Article ID
test_articles_from_current_page		Articles from current page
test_articles_from_one_page			Articles from one given page
test_articles_from_parent_page		Articles from one choosen parent page
test_articles_from_website			Articles from whole website
test_articles_in_articles			Nested articles into articles tag
test_content_iframe :				Content test : embedded iFrames from Youtube and Google maps
test_article_categories :			Categories from each article
test_article_user : 				User who wrote the article in Ionize
test_article_prev_next :			Previous / Next article builder

// Pagination
test_pagination : 				Pagination of articles
test_pagination_archives : 		Pagination of articles withing archives
test_pagination_categories : 	Pagination of articles withing categories

// Navigation
test_tag_navigation
test_tag_languages_menu
test_tag_tree_navigation

// Medias
test_tag_media_page :		Medias from pages
test_tag_media_articles :	Medias from articles

// Categories
test_tag_categories : 		Categories listing

// Archives
test_tag_archives			Archives tag


// @TODO :
test_session :				Play with Session through Tags
test_cookies :				Play with cookies through Tags
test_tag_store : 					Store one value to reuse it in the same view
test_tag_medias_module : 			Medias within one module
test_tag_elements : 				Content Elements
test_tag_subnavigation				Remove ???

test_temp : 				Temp dev's tests

*/
?>

<ion:partial view="test_articles_from_website"  />
