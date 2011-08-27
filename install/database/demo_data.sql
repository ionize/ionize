/**
 * Ionize 0.9.7
 * "Demo" theme example data
 * These data are provided as demo content for the theme "Demo"
 *
 */


--
-- Base DATA
--
TRUNCATE TABLE `lang`;
INSERT INTO `lang` (`lang`, `name`, `online`, `def`, `ordering`) VALUES	('en','english','1','1',1);

UPDATE `setting` SET `content`='demo' WHERE `name`='theme';

DELETE FROM `setting` WHERE `name`='site_title';
INSERT INTO `setting` VALUES('', 'site_title', 'My Website', 'en');

INSERT INTO `setting` VALUES ('','thumb_430','width,430,,',NULL);
INSERT INTO `setting` VALUES ('','thumb_540','width,540,,',NULL);
INSERT INTO `setting` VALUES ('','thumb_150','width,150,true,true',NULL);
INSERT INTO `setting` VALUES ('','thumb_940','width,940,,',NULL);
INSERT INTO `setting` VALUES ('','thumb_280','width,280,,true',NULL);


-- 
-- Article
-- 
TRUNCATE `article`;
INSERT INTO `article` VALUES(1, '404', NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, NULL, '0', '0', NULL, 0);
INSERT INTO `article` VALUES(2, 'welcome-article-url', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-25 11:44:13', '0000-00-00 00:00:00', 0, 0, '', '', '0000-00-00 00:00:00', 0);
INSERT INTO `article` VALUES(4, 'home-example', '', '', '2011-07-05 18:27:14', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-25 20:10:56', '0000-00-00 00:00:00', 0, 0, '', '', '0000-00-00 00:00:00', 0);
INSERT INTO `article` VALUES(5, 'article-type', '', '', '2011-07-05 19:35:29', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-25 20:12:33', '0000-00-00 00:00:00', 0, 0, '', '', '0000-00-00 00:00:00', 0);
INSERT INTO `article` VALUES(6, 'footer-article', '', '', '2011-07-12 17:04:09', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-25 16:32:45', '0000-00-00 00:00:00', 0, 0, '', '', '0000-00-00 00:00:00', 0);
INSERT INTO `article` VALUES(7, 'footer-views', '', '', '2011-07-12 17:04:58', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-25 16:31:59', '0000-00-00 00:00:00', 0, 0, '', '', '0000-00-00 00:00:00', 0);
INSERT INTO `article` VALUES(8, 'follow-us', '', '', '2011-07-12 17:05:23', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-25 16:43:57', '0000-00-00 00:00:00', 0, 0, '', '', '0000-00-00 00:00:00', 0);
INSERT INTO `article` VALUES(9, 'examples-introduction', '', '', '2011-07-12 19:04:22', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-25 16:40:45', '0000-00-00 00:00:00', 1, 0, '', '', '0000-00-00 00:00:00', 0);
INSERT INTO `article` VALUES(10, 'one-blog-post', '', '', '2011-07-13 21:16:43', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-07-13 21:16:43', '0000-00-00 00:00:00', 1, 0, '', '', '0000-00-00 00:00:00', 0);
INSERT INTO `article` VALUES(11, 'another-post', '', '', '2011-07-13 23:33:48', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-07-14 00:49:22', '0000-00-00 00:00:00', 1, 0, '', '', '0000-00-00 00:00:00', 0);
INSERT INTO `article` VALUES(13, 'corporate-information', '', '', '2011-07-27 06:55:23', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-24 18:57:48', '0000-00-00 00:00:00', 0, 0, '', '', '0000-00-00 00:00:00', 0);
INSERT INTO `article` VALUES(31, 'picture-gallery', '', '', '2011-08-24 20:54:34', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-25 11:34:30', '0000-00-00 00:00:00', 1, 0, '', '', '0000-00-00 00:00:00', 0);
INSERT INTO `article` VALUES(32, 'for-developpers', '', '', '2011-08-25 11:43:39', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-25 11:44:49', '0000-00-00 00:00:00', 0, 0, '', '', '0000-00-00 00:00:00', 0);
INSERT INTO `article` VALUES(34, 'modules', '', '', '2011-08-25 16:45:04', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-25 20:13:38', '0000-00-00 00:00:00', 1, 0, '', '', '0000-00-00 00:00:00', 0);


-- 
-- Article_category
-- 
TRUNCATE `article_category`;
INSERT INTO `article_category` VALUES(10, 1);
INSERT INTO `article_category` VALUES(11, 1);
INSERT INTO `article_category` VALUES(11, 2);


-- 
-- Article_lang
-- 

TRUNCATE `article_lang`;
INSERT INTO `article_lang` VALUES(1, 'en', '404', '404', NULL, NULL, NULL, '<p>The content you asked was not found !</p>', NULL, NULL, 1);
INSERT INTO `article_lang` VALUES(2, 'en', 'welcome-article-url', 'Welcome to Ionize', '', '', NULL, '<p>For more information about building a website with Ionize, you can:</p>\n<ul>\n<li>Download &amp; read <a href="http://www.ionizecms.com">the Documentation</a></li>\n<li>Visit <a href="http://www.ionizecms.com/forum">the Community Forum</a></li>\n</ul>\n<p>Have fun !</p>', NULL, NULL, 1);
INSERT INTO `article_lang` VALUES(4, 'en', 'home-example', 'Some examples ?', '', '', NULL, '<p>The Examples page introduce more complex use of tags.</p>\n<p>Don''t hesitate to have a look at the "views" sources files.</p>\n<p>If you don''t know what''s a view, look at <a target="_blank" href="http://doc.ionizecms.com">the ionize documentation</a>.</p>', NULL, NULL, 1);
INSERT INTO `article_lang` VALUES(5, 'en', 'article-type', 'Article''s type', '', '', NULL, '<p>Articles "types" helps the webdesigner to define where will be displayed which article.</p>\n<p>This article has the type "one-fourth", that means it will be displayed just here.</p>\n<p>The three above articles have no type defined.<br />In this case, the Ionize article tag retrieves the articles "without type" and limits the retrieved number of articles to 3, so the design isn''t broken.</p>', NULL, NULL, 1);
INSERT INTO `article_lang` VALUES(6, 'en', 'footer-article', 'Footer article', '', '', NULL, '<p>This article has the view "Footer" and is located in the page called "Footer".<br />Views define how one content will be displayed.</p>', NULL, NULL, 1);
INSERT INTO `article_lang` VALUES(7, 'en', 'footer-views', 'More about views', '', '', NULL, '<p>Views are PHP files. They can be declared as "Page" or "Article" in Ionize. Then, they will be available when editing a page.</p>', NULL, NULL, 1);
INSERT INTO `article_lang` VALUES(8, 'en', 'follow-us', 'Follow us', '', '', NULL, '<p><a href="/rss/"><img src="/ionize-with_ci2/files/website/icon-round-rss.png" /></a> <img src="/ionize-with_ci2/files/website/icon-round-twitter.png" height="36" width="36" /> <img src="/ionize-with_ci2/files/website/icon-round-ionize.png" /> <img src="/ionize-with_ci2/files/website/icon-round-email.png" /> <br /> (The RSS icon links to the RSS module)</p>', NULL, NULL, 1);
INSERT INTO `article_lang` VALUES(9, 'en', 'examples-introduction', 'Examples introduction', '', '', NULL, '<p>These example data and the used template are very useful for webdesigners and developpers who want to create an Ionize based website.</p>\n<p>To see the code used to build these examples :</p>\n<ul>\n<li>Get the view name used for each example article in the Ionize Admin panel,</li>\n<li>Edit the corresponding view file &#40;in /themes/demo/views&#41;</li>\n</ul>\n<p>For more documentation, visit the documentation website : http://doc.ionizecms.com</p>', NULL, NULL, 1);
INSERT INTO `article_lang` VALUES(10, 'en', 'one-blog-post', 'One blog post', '', '', NULL, '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>\n<p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>\n<p>Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Typi non habent claritatem insitam; est usus legentis in iis qui facit eorum claritatem. Investigationes demonstraverunt lectores legere me lius quod ii legunt saepius. Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum.</p>\n<p>Mirum est notare quam littera gothica, quam nunc putamus parum claram, anteposuerit litterarum formas humanitatis per seacula quarta decima et quinta decima. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.</p>', NULL, NULL, 1);
INSERT INTO `article_lang` VALUES(11, 'en', 'another-post', 'Another post', '', '', NULL, '<p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>\n<p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>\n<p>Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Typi non habent claritatem insitam; est usus legentis in iis qui facit eorum claritatem. Investigationes demonstraverunt lectores legere me lius quod ii legunt saepius. Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum.</p>', NULL, NULL, 1);
INSERT INTO `article_lang` VALUES(13, 'en', 'corporate-information', 'Corporate Information', '', '', NULL, '<ul>\n<li><img class="left" src="/files/website/icon-home.png" />Ionize Corp.<br />5 design street,<br />10000 TheCity</li>\n<li><img class="left" src="/files/website/icon-phone.png" />From 7.00 AM to 7.00 PM at :<br />+44 01 02 03 04 05</li>\n<li><img class="left" src="/files/website/icon-email.png" />For all request :<br />our-email@our-domain.com</li>\n</ul>', NULL, NULL, 1);
INSERT INTO `article_lang` VALUES(31, 'en', 'picture-gallery', 'Picture Gallery Example', '', '', NULL, '<p>This is an article with pictures linked to.<br />This article uses the view called "Picture Gallery".<br />Location of the view file  : <strong>themes/demo/views/article_picture_gallery.php</strong></p>', NULL, NULL, 1);
INSERT INTO `article_lang` VALUES(32, 'en', 'for-developpers', 'For developpers', '', '', NULL, '<p>The last version of Ionize is always available on GitHub :</p>\n<p>http://www.github.com/ionize/ionize/</p>', NULL, NULL, 1);
INSERT INTO `article_lang` VALUES(34, 'en', 'modules', 'Modules', '', '', NULL, '<p>Out of the box, Ionize comes with 4 modules :</p>\n<h3>Search Module</h3>\n<p>This module looks in the whole website for articles containing the searched word.</p>\n<p>In the Demo Theme, you will find :</p>\n<ul>\n<li>the form and the call to this module in the view <strong>/themes/demo/header.php</strong></li>\n<li>the search result page in the view <strong>/themes/demo/page_search_result.php</strong></li>\n</ul>\n<p>To use it : </p>\n<ul>\n<li>Create a page, let''s say its URL is <strong>my-search-result</strong></li>\n<li>Set the URL of this page as action of the form containing the search field</li>\n<li>The search input text must have its name set to <strong>"realm"</strong></li>\n</ul>\n<h3>Simpleform module</h3>\n<p>Used to build simple forms for your website.</p>\n<p>The view <strong>/themes/demo/page_contact.php</strong> will help you building your form. This module also has an Admin panel, which display some help about installation.</p>\n<h3>RSS module</h3>\n<p>Creates RSS feeds in multiple languages.</p>\n<p>The Admin panel of this module let you drag''n''drop pages from which you want to make a syndication feed.</p>\n<p>The syndication URL is displayed and depends on the URL you define for the module at installation. Just add a link to this syndication URL, and that''s it !</p>\n<h3>User Manager module</h3>\n<p>This module is a complete set which let end users suscribe and connect to the website.</p>\n<p>Some features :</p>\n<ul>\n<li>Subscription form for users (highly configurable)</li>\n<li>Subscription confirmation by mail (account activation by user, by admin)</li>\n<li>Member panel for each user.</li>\n</ul>', NULL, NULL, 1);


-- 
-- Article_media
-- 
TRUNCATE `article_media`;
INSERT INTO `article_media` VALUES(2, 10, 1, NULL);
INSERT INTO `article_media` VALUES(4, 3, 1, NULL);
INSERT INTO `article_media` VALUES(10, 13, 2, NULL);
INSERT INTO `article_media` VALUES(11, 7, 1, NULL);
INSERT INTO `article_media` VALUES(31, 1, 4, NULL);
INSERT INTO `article_media` VALUES(31, 3, 5, NULL);
INSERT INTO `article_media` VALUES(31, 7, 6, NULL);
INSERT INTO `article_media` VALUES(31, 10, 1, NULL);
INSERT INTO `article_media` VALUES(31, 13, 2, NULL);
INSERT INTO `article_media` VALUES(32, 7, 2, NULL);


-- 
-- Article_type
-- 
TRUNCATE `article_type`;
INSERT INTO `article_type` VALUES (1,'three-fourth',0,'Type set to articles in three fourth blocs.',0);
INSERT INTO `article_type` VALUES (2,'one-fourth',0,'Type set to articles in one fourth blocs.',0);
INSERT INTO `article_type` VALUES (3,'intro',0,'Everywhere an intro article is needed...',0);


-- 
-- Category
-- 
TRUNCATE `category`;
INSERT INTO `category` VALUES (1,'web-design',0);
INSERT INTO `category` VALUES (2,'travel',0);


-- 
-- Category_lang
-- 

TRUNCATE `category_lang`;
INSERT INTO `category_lang` VALUES	(1,'en','Web Design','','');
INSERT INTO `category_lang` VALUES	(2,'en','Travel','','');


-- 
-- Media
-- 

TRUNCATE `media`;
INSERT INTO `media` VALUES(1, 'picture', 'IMG_8632.jpg', 'files/pictures/IMG_8632.jpg', 'files/pictures/', 'Michel-Ange Kuntz', '', '2007-02-25 17:02:29', '');
INSERT INTO `media` VALUES(2, 'picture', 'IMG_8643.jpg', 'files/pictures/IMG_8643.jpg', 'files/pictures/', '', '', '0000-00-00 00:00:00', '');
INSERT INTO `media` VALUES(3, 'picture', 'IMG_8963.jpg', 'files/pictures/IMG_8963.jpg', 'files/pictures/', 'Michel-Ange Kuntz', '', '0000-00-00 00:00:00', '');
INSERT INTO `media` VALUES(4, 'picture', 'IMG_9338.jpg', 'files/pictures/IMG_9338.jpg', 'files/pictures/', 'Michel-Ange Kuntz', '', '0000-00-00 00:00:00', '');
INSERT INTO `media` VALUES(7, 'picture', 'IMG_8447.jpg', 'files/pictures/IMG_8447.jpg', 'files/pictures/', 'Michel-Ange Kuntz', '', '0000-00-00 00:00:00', '');
INSERT INTO `media` VALUES(8, 'picture', 'IMG_9448.jpg', 'files/pictures/IMG_9448.jpg', 'files/pictures/', NULL, '', '0000-00-00 00:00:00', NULL);
INSERT INTO `media` VALUES(9, 'picture', 'IMG_8350.jpg', 'files/pictures/IMG_8350.jpg', 'files/pictures/', NULL, '', '0000-00-00 00:00:00', NULL);
INSERT INTO `media` VALUES(10, 'picture', 'IMG_8359.jpg', 'files/pictures/IMG_8359.jpg', 'files/pictures/', 'Michel-Ange Kuntz', '', '0000-00-00 00:00:00', '');
INSERT INTO `media` VALUES(11, 'picture', 'IMG_4475.jpg', 'files/pictures/IMG_4475.jpg', 'files/pictures/', NULL, '', '0000-00-00 00:00:00', NULL);
INSERT INTO `media` VALUES(12, 'picture', 'IMG_7634.jpg', 'files/pictures/IMG_7634.jpg', 'files/pictures/', NULL, '', '0000-00-00 00:00:00', NULL);
INSERT INTO `media` VALUES(13, 'picture', 'IMG_8645.jpg', 'files/pictures/IMG_8645.jpg', 'files/pictures/', 'Michel-Ange Kuntz', '', '0000-00-00 00:00:00', '');


-- 
-- Media_lang
-- 

TRUNCATE `media_lang`;
INSERT INTO `media_lang` VALUES('en', 1, 'Monywa, Burma', '', 'In the mountain of Monywa, more than two thousand caves were built in honor of Budha.');
INSERT INTO `media_lang` VALUES('en', 2, 'Monywa wall paints', '', 'These 17th centruey wall painting are very well preserved because the caves were forgivven for centuries.');
INSERT INTO `media_lang` VALUES('en', 3, 'Bagan temples', '', 'In the Bagan valley, some temples have marvelous 16th century fresco, sadly touchable by visitors.');
INSERT INTO `media_lang` VALUES('en', 4, 'Inle lake silk', '', 'The Inle lake silk is made from Lotus plant fibers.');
INSERT INTO `media_lang` VALUES('en', 7, 'Mandalay', '', 'One working day in the historical capital of Burma.');
INSERT INTO `media_lang` VALUES('en', 10, 'Mingun', '', 'Alot of young munks live in a monastery next to the Hsinbyume pagoda');
INSERT INTO `media_lang` VALUES('en', 13, 'Monywa fresco', '', 'In the 16th century, the maze of caves has been forgotten, preserving them until today.');


-- 
-- Menu
-- 
TRUNCATE `menu`;
INSERT INTO `menu` (`id_menu`, `name`, `title`, `ordering`) VALUES(1, 'main', 'Main menu', NULL);
INSERT INTO `menu` (`id_menu`, `name`, `title`, `ordering`) VALUES(2, 'system', 'System menu', NULL);


-- 
-- Page
-- 

TRUNCATE `page`;
INSERT INTO `page` VALUES(1, 0, 2, 0, '404', 0, 0, 1, 0, '0', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-24 22:51:41', '0000-00-00 00:00:00', 0, '404', '', '', '0', '0', '', NULL, '', 0, 0, 0, 5);
INSERT INTO `page` VALUES(2, 0, 1, 0, 'welcome-url', 1, 0, 1, 1, '0', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-07-05 19:50:40', '0000-00-00 00:00:00', 1, 'page_home', '0', '', '0', '0', '', '', '', 0, 0, 0, 5);
INSERT INTO `page` VALUES(3, 0, 1, 0, 'contact', 6, 0, 1, 0, '0', '', '2011-07-05 17:29:30', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-07-27 06:53:26', '0000-00-00 00:00:00', 1, 'page_contact', '0', '', '0', '0', '', NULL, '', 0, 0, 0, 5);
INSERT INTO `page` VALUES(4, 0, 1, 0, 'examples', 2, 0, 1, 0, '0', '', '2011-07-05 17:29:45', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-25 11:30:50', '0000-00-00 00:00:00', 1, '', '', '', '0', '0', '', '', '', 0, 0, 0, 5);
INSERT INTO `page` VALUES(6, 0, 1, 0, 'blog', 5, 0, 1, 0, '0', '', '2011-07-05 17:36:21', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-07-13 21:08:29', '0000-00-00 00:00:00', 1, 'page_blog', 'article_blog_post_list', 'article_blog_post_detail', '0', '0', '', NULL, '', 0, 0, 0, 5);
INSERT INTO `page` VALUES(8, 0, 2, 0, 'search-result', 1, 0, 1, 0, '0', '', '2011-07-12 16:33:29', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-07-12 16:54:38', '0000-00-00 00:00:00', 0, 'page_search_result', '0', '', '0', '0', '', NULL, '', 0, 0, 0, 5);
INSERT INTO `page` VALUES(9, 0, 2, 0, 'footer', 2, 0, 1, 0, '', '0', '2011-07-12 17:03:22', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '0', '0', '0', '0', '0', '', NULL, '', 0, 0, 0, 5);
INSERT INTO `page` VALUES(10, 4, 1, 4, 'picture-gallery', 2, 1, 1, 0, '0', '', '2011-07-14 00:35:27', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-25 11:29:48', '0000-00-00 00:00:00', 1, '', '', '', '0', '0', '', NULL, '', 0, 0, 0, 5);
INSERT INTO `page` VALUES(14, 4, 1, 0, 'modules', 1, 1, 1, 0, '0', '', '2011-08-25 11:26:51', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-08-25 16:44:37', '0000-00-00 00:00:00', 1, '', '', '', '0', '0', '', NULL, '', 0, 0, 0, 5);


--
-- Page_article
--

TRUNCATE `page_article`;
INSERT INTO `page_article` VALUES(1, 1, 1, '404', 0, NULL, '', '', '', 1);
INSERT INTO `page_article` VALUES(2, 2, 1, NULL, 2, NULL, '', '', '', 1);
INSERT INTO `page_article` VALUES(2, 4, 1, NULL, 3, NULL, '', '', '', 1);
INSERT INTO `page_article` VALUES(2, 5, 1, NULL, 5, 2, '', '', '', 1);
INSERT INTO `page_article` VALUES(2, 32, 1, NULL, 4, NULL, '', '', '', 1);
INSERT INTO `page_article` VALUES(3, 13, 1, NULL, 1, NULL, '', '', '', 1);
INSERT INTO `page_article` VALUES(4, 9, 1, NULL, 1, NULL, '', '', '', 1);
INSERT INTO `page_article` VALUES(6, 10, 1, NULL, 3, NULL, '', '', '', 1);
INSERT INTO `page_article` VALUES(6, 11, 1, NULL, 4, NULL, '', '', '', 1);
INSERT INTO `page_article` VALUES(9, 6, 1, 'article_footer', 1, NULL, '', '', '', 1);
INSERT INTO `page_article` VALUES(9, 7, 1, 'article_footer', 2, NULL, '', '', '', 1);
INSERT INTO `page_article` VALUES(9, 8, 1, 'article_footer', 3, NULL, '', '', '', 1);
INSERT INTO `page_article` VALUES(10, 31, 1, 'article_picture_gallery', 1, NULL, '', '', '', 1);
INSERT INTO `page_article` VALUES(14, 34, 1, NULL, 1, NULL, '', '', '', 1);


-- 
-- Page_lang
-- 
		
TRUNCATE `page_lang`;
INSERT INTO `page_lang` VALUES('en', 1, '404', '', '404', '', '', '', '', '', '', 1);
INSERT INTO `page_lang` VALUES('en', 2, 'welcome-url', '', 'Welcome', 'More about Ionize ?', '', '', '', '', '', 1);
INSERT INTO `page_lang` VALUES('en', 3, 'contact', '', 'Contact', '', '', '', '', '', '', 1);
INSERT INTO `page_lang` VALUES('en', 4, 'examples', '', 'Examples', '', 'Some examples', '', '', '', '', 1);
INSERT INTO `page_lang` VALUES('en', 6, 'blog', '', 'Blog', '', '', '', '', '', '', 1);
INSERT INTO `page_lang` VALUES('en', 8, 'search-result', '', 'Search Results', '', '', '', '', '', '', 1);
INSERT INTO `page_lang` VALUES('en', 9, 'footer', '', 'Footer', '', '', '', '', '', '', 1);
INSERT INTO `page_lang` VALUES('en', 10, 'picture-gallery', '', 'Picture Gallery', '', '', '', '', '', '', 1);
INSERT INTO `page_lang` VALUES('en', 14, 'modules', '', 'Modules', '', '', '', '', '', '', 1);


-- 
-- Contenu de la table page_media
-- 
TRUNCATE `page_media`;
INSERT INTO `page_media` VALUES(1, 1, 4);
INSERT INTO `page_media` VALUES(1, 3, 5);
INSERT INTO `page_media` VALUES(1, 7, 3);
INSERT INTO `page_media` VALUES(1, 10, 6);
INSERT INTO `page_media` VALUES(1, 13, 7);
INSERT INTO `page_media` VALUES(2, 1, 1);
INSERT INTO `page_media` VALUES(2, 3, 3);
INSERT INTO `page_media` VALUES(2, 7, 2);

