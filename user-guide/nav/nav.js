function create_menu(basepath)
{
	var base = (basepath == 'null') ? '' : basepath;

	document.write(
		'<table cellpadding="0" cellspaceing="0" border="0" style="width:98%"><tr>' +
		'<td class="td" valign="top">' +

		'<ul>' +
		'<li><a href="'+base+'index.html">User Guide Home</a></li>' +	
		'<li><a href="'+base+'toc.html">Table of Contents Page</a></li>' +
		'</ul>' +	

		'<h3>Basic Info</h3>' +
		'<ul>' +
			'<li><a href="'+base+'general/requirements.html">Server Requirements</a></li>' +
			'<li><a href="'+base+'license.html">License Agreement</a></li>' +
			'<li><a href="'+base+'changelog.html">Change Log</a></li>' +
			'<li><a href="'+base+'general/credits.html">Credits</a></li>' +
		'</ul>' +	
		
		'<h3>Installation</h3>' +
		'<ul>' +
			'<li><a href="'+base+'installation/downloads.html">Downloading Ionize</a></li>' +
			'<li><a href="'+base+'installation/index.html">Installation Instructions</a></li>' +
			'<li><a href="'+base+'installation/troubleshooting.html">Troubleshooting</a></li>' +
		'</ul>' +
		
		'<h3>Introduction</h3>' +
		'<ul>' +
			'<li><a href="'+base+'overview/getting_started.html">Getting Started</a></li>' +
			'<li><a href="'+base+'overview/at_a_glance.html">Ionize at a Glance</a></li>' +
			'<li><a href="'+base+'overview/features.html">Ionize Features</a></li>' +
			'<li><a href="'+base+'overview/concepts.html">Main Concepts</a></li>' +
		'</ul>' +	

				
		'</td><td class="td_sep" valign="top">' +

		'<h3>Using Ionize & Editing Content</h3>' +
		'<ul>' +
			'<li><a href="'+base+'general/ionize_panel.html">Discover the Ionize Panel</a></li>' +
			'<li><a href="'+base+'general/quickstart.html">Quickstart : Ionize in 4 steps</a></li>' +
			'<li><a href="'+base+'general/users.html">Users and Groups</a></li>' +
			'<li><a href="'+base+'general/menus.html">Menus</a></li>' +
			'<li><a href="'+base+'general/pages.html">Pages</a></li>' +
			'<li><a href="'+base+'general/articles.html">Articles</a></li>' +
			'<li><a href="'+base+'general/media.html">Media : Files, pictures, videos...</a></li>' +
			'<li><a href="'+base+'general/static_translations.html">Static translations</a></li>' +
		'</ul>' +

		'<h3>Build the website</h3>' +
		'<ul>' +
			'<li><a href="'+base+'general/create_theme.html">Create the Theme</a></li>' +
			'<li><a href="'+base+'general/views.html">Introduction to Views</a></li>' +
			'<li><a href="'+base+'general/page_views.html">Page views</a></li>' +
			'<li><a href="'+base+'general/article_views.html">Article views</a></li>' +
			'<li><a href="'+base+'general/display_language_menu.html">Display the languages menu</a></li>' +
			'<li><a href="'+base+'general/display_navigation_menu.html">Display the navigation menu</a></li>' +
			'<li><a href="'+base+'general/article_types.html">Articles and Types</a></li>' +
			'<li><a href="'+base+'general/articles_from_another_page.html">Display articles from another page</a></li>' +
			// Here
			'<li><a href="'+base+'general/display_media.html">Display medias</a></li>' +
			'<li><a href="'+base+'general/extend_the_data_model.html">Extend the data model</a></li>' +
			'<li><a href="'+base+'general/php_in_views.html">Add PHP to views</a></li>' +
		'</ul>' +


		'</td><td class="td_sep" valign="top">' +

		'<h3>Tags Reference</h3>' +
		'<ul>' +
			'<li><a href="'+base+'tags/introduction_to_tags.html">Introduction to Tags</a></li>' +
			'<li><a href="'+base+'tags/general_tags.html">General Tags</a></li>' +
			'<li><a href="'+base+'tags/navigation_tags.html">Navigation Tags</a></li>' +
			'<li><a href="'+base+'tags/language_tags.html">Language Tags</a></li>' +
			'<li><a href="'+base+'tags/shared_tags.html">Shared Tags : Page, Article, Media</a></li>' +
			'<li><a href="'+base+'tags/article_tags.html">Article Tags</a></li>' +
			'<li><a href="'+base+'tags/media_tags.html">Media Tags</a></li>' +
			'<li><a href="'+base+'tags/special_tags.html">Special Tags</a></li>' +
		'</ul>' +

		'<h3>Additional Resources</h3>' +
		'<ul>' +
			'<li><a href="http://www.ionizecms.com/forum/">Community Forum</a></li>' +
			'<li><a href="http://community.ionizecms.com">Community Wiki</a></li>' +
		'</ul>' +	

		
		'</td><td class="td_sep" valign="top">' +

		'<h3>Develop with Ionize</h3>' +
		'<ul>' +
			'<li><a href="'+base+'development/introduction.html">Introduction to Ionize Admin</a></li>' +
//			'<li><a href="'+base+'development/ionize_controllers.html">Ionize Controllers</a></li>' +
//			'<li><a href="">Ionize Models</a></li>' +
//			'<li><a href="">Tag Manager library</a></li>' +
		'</ul>' +	


		
		'</td></tr></table>');
}