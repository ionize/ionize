/**
 * Ionize 1.0.6.x
 * "Demo" theme example data
 *
 * These data are provided as demo content for the theme "Demo"
 * Do not remove the separator after each query :
 * it is used as SQL request separator by the installer
 *
 */


--
-- Base data
--
UPDATE setting SET content='foundation5' WHERE name='theme';
--#--
DELETE FROM setting WHERE name='site_title';
--#--
INSERT INTO setting VALUES('', 'site_title', 'Ionize CMS Demo', 'en');
--#--


-- 
-- Article
-- 
TRUNCATE article;
--#--

INSERT INTO article (id_article, name, author, updater, created, publish_on, publish_off, updated, logical_date, indexed, id_category, comment_allow, comment_autovalid, comment_expire, flag, has_url)
VALUES
	(1,'404','demo','demo','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-10 09:18:48','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(4,'welcome-to-ionize','demo','demo','2012-11-17 12:48:59','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-11-20 17:05:20','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(5,'article-1','demo','michelange','2012-11-17 13:58:21','0000-00-00 00:00:00','0000-00-00 00:00:00','2014-04-06 09:33:24','0000-00-00 00:00:00',1,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(6,'article-2','demo','demo','2012-11-17 13:58:41','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 15:47:57','0000-00-00 00:00:00',1,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(7,'article-3','demo','demo','2012-11-17 13:59:04','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 15:52:39','0000-00-00 00:00:00',1,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(8,'article-4','demo','demo','2012-11-17 13:59:25','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 15:52:46','0000-00-00 00:00:00',1,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(9,'article-5','demo','demo','2012-11-17 14:10:38','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 15:52:52','0000-00-00 00:00:00',1,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(10,'article-6','demo','demo','2012-11-17 14:17:28','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 15:52:58','0000-00-00 00:00:00',1,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(11,'easy-edition','demo','demo','2012-11-21 10:44:50','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-11-21 10:44:50','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(12,'multilingual','demo','demo','2012-11-21 10:46:28','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-11-21 10:46:28','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(13,'userfriendly','demo','demo','2012-11-21 10:46:50','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-11-21 10:46:50','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(14,'template-system','demo','demo','2012-11-21 10:47:12','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-06 11:20:03','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(15,'about-us','demo','demo','2012-11-21 11:42:13','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-27 01:15:33','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(16,'footer-resources','demo','demo','2012-11-21 11:52:42','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 16:33:05','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(17,'service-1','demo','demo','2012-12-06 14:34:27','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-27 01:15:00','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(18,'service-2','demo','demo','2012-12-06 14:38:25','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 17:00:37','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(19,'service-3','demo','demo','2012-12-06 14:38:44','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 17:00:47','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(20,'service-4','demo','demo','2012-12-06 14:39:08','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 17:00:54','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(24,'contact-informations','demo','michelange','2012-12-07 10:01:46','0000-00-00 00:00:00','0000-00-00 00:00:00','2014-04-05 12:20:46','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(26,'our-location','demo','demo','2012-12-07 10:07:56','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 20:04:28','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(27,'whats-ionize','demo','demo','2012-12-21 16:27:54','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 16:27:54','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(28,'can-i-help','demo','demo','2012-12-21 16:30:01','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 16:30:10','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(29,'send-us-a-message','demo','demo','2012-12-21 17:04:43','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 17:04:43','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(30,'not-logged-in','demo','demo','2012-12-22 10:54:43','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-22 10:54:43','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(31,'hello','demo','demo','2012-12-22 11:15:04','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-22 11:42:04','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(32,'401',NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',0,NULL,'0','0',NULL,0,1),
	(33,'403',NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',0,NULL,'0','0',NULL,0,1);
--#--


-- 
-- Article_category
-- 
TRUNCATE article_category;
--#--

INSERT INTO article_category (id_article, id_category)
VALUES
	(4,1),
	(5,1),
	(5,2),
	(6,2);
--#--


-- 
-- Article_lang
-- 
TRUNCATE article_lang;
--#--

INSERT INTO article_lang (id_article, lang, url, title, subtitle, meta_title, content, meta_keywords, meta_description, online)
VALUES
	(1,'en','404','Can\'t find requested page','','','<p>Maecenas massa. varius non accumsan nec, commodo vitae felis! Quisque luctus, lorem vel elementum aliquam, lorem nulla dignissim velit, id placerat libero ipsum eget sapien. Cras erat risus, pellentesque ut auctor quis, fringilla vel elit. Cras nisl dolor, vulputate eget molestie ut, sollicitudin non dui.</p><h4>Reasons</h4><ul><li>Lorem ipsum dolor sit amet</li><li>Consectetur adipiscing elit</li><li>Nulla volutpat aliquam velit<ul><li>Phasellus iaculis neque</li><li>Purus sodales ultricies</li><li>Vestibulum laoreet porttitor sem</li><li>Ac tristique libero volutpat at</li></ul></li><li>Faucibus porta lacus fringilla vel</li></ul>','','',1),
	(4,'en','the-power-of-php','The power of PHP','','','<p>The Ionize CMS uses CodeIgniter, a powerful and lightweight PHP framework. For CodeIgniter developpers, starting developping on Ionize will be really easy as building a CI application !</p>','','',1),
	(5,'en','article-1','One blog post','','','<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>\n<p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>\n<p>Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Typi non habent claritatem insitam. est usus legentis in iis qui facit eorum claritatem. Investigationes demonstraverunt lectores legere me lius quod ii legunt saepius. Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum.</p>\n<p>Mirum est notare quam littera gothica, quam nunc putamus parum claram, anteposuerit litterarum formas humanitatis per seacula quarta decima et quinta decima. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.</p>','','',1),
	(6,'en','article-2','Another blog post','','','<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p><p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p><p>Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Typi non habent claritatem insitam. est usus legentis in iis qui facit eorum claritatem. Investigationes demonstraverunt lectores legere me lius quod ii legunt saepius. Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum.</p><p>Mirum est notare quam littera gothica, quam nunc putamus parum claram, anteposuerit litterarum formas humanitatis per seacula quarta decima et quinta decima. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.</p>','','',1),
	(7,'en','article-3','We got something...','','','<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p><p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p><p>Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Typi non habent claritatem insitam. est usus legentis in iis qui facit eorum claritatem. Investigationes demonstraverunt lectores legere me lius quod ii legunt saepius. Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum.</p><p>Mirum est notare quam littera gothica, quam nunc putamus parum claram, anteposuerit litterarum formas humanitatis per seacula quarta decima et quinta decima. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.</p>','','',1),
	(8,'en','article-4','Say it differently','','','<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p><p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p><p>Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Typi non habent claritatem insitam. est usus legentis in iis qui facit eorum claritatem. Investigationes demonstraverunt lectores legere me lius quod ii legunt saepius. Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum.</p><p>Mirum est notare quam littera gothica, quam nunc putamus parum claram, anteposuerit litterarum formas humanitatis per seacula quarta decima et quinta decima. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.</p>','','',1),
	(9,'en','article-5','10 incredible items !','','','<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p><p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p><p>Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Typi non habent claritatem insitam. est usus legentis in iis qui facit eorum claritatem. Investigationes demonstraverunt lectores legere me lius quod ii legunt saepius. Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum.</p><p>Mirum est notare quam littera gothica, quam nunc putamus parum claram, anteposuerit litterarum formas humanitatis per seacula quarta decima et quinta decima. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.</p>','','',1),
	(10,'en','article-6','Send mail to your friends !','','','<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p><p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p><p>Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Typi non habent claritatem insitam. est usus legentis in iis qui facit eorum claritatem. Investigationes demonstraverunt lectores legere me lius quod ii legunt saepius. Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum.</p><p>Mirum est notare quam littera gothica, quam nunc putamus parum claram, anteposuerit litterarum formas humanitatis per seacula quarta decima et quinta decima. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.</p>','','',1),
	(11,'en','easy-edition','Easy edition','','','<p>Copy / paste content from any word processing software. Inline links are automatically converted to hyperlinks. Emails are safely encoded to avoid spam.</p>',NULL,NULL,1),
	(12,'en','multilingual','Multilingual','','','<p>You can create as much languages as you need for your website. Every content can be translated : posts, static elements in templates, media data, etc.</p>',NULL,NULL,1),
	(13,'en','userfriendly','Userfriendly','','','<p>Your website structure is logical. Managing elements such as pages, articles or medias is easily done by drag\'n\'drop!</p>',NULL,NULL,1),
	(14,'en','template-system','Template System','','','<p>Each page or article can have a dedicated template, templates can be embeded in each others. The tag language of Ionize is simple and fully documented.</p>','','',1),
	(15,'en','about-us-1','About Us','','','<p>Duis diam tortor, suscipit sed varius id, dictum interdum tortor. Vivamus vel sapien vitae metus aliquet vehicula. Cras nec odio a dui sagittis semper? Nullam non luctus nisl. Cras ante ante, elementum a porta sit amet, aliquet in felis. Cras dictum metus non felis fermentum in mattis nisl dignissim. Suspendisse suscipit diam id ipsum elementum sed commodo massa ullamcorper. Curabitur tincidunt enim at ipsum aliquam a sagittis eros vulputate. Etiam elementum gravida ipsum eget congue. Pellentesque tempus facilisis odio, at porta nibh pulvinar vel.</p><p>Donec rutrum lectus eget enim aliquet in sollicitudin elit vestibulum. Sed iaculis mi quis ipsum congue elementum. Proin turpis urna, rutrum id vehicula et, cursus vel purus. Proin tincidunt, odio sed congue ultricies. Risus erat rhoncus leo, in fringilla elit libero vel metus. Sed adipiscing, orci vitae iaculis laoreet, ante nibh facilisis sem, ac pulvinar nunc risus sit amet nisl.</p>','','',1),
	(16,'en','footer-resources','Online resources','','','<p><strong>Community</strong> : Ionize\'s forum<br /><strong>Project hosting</strong> : Ionize on github</p><p><strong>Last version</strong> : Download Ionize</p><p><strong>Contact</strong> : team@ionizecms.com</p>','','',1),
	(17,'en','some-of-our-services','What we do for you','','','<p>Suspendisse nec erat lacus? Morbi pharetra elit ac nibh ornare a vulputate urna dictum?</p><p>Vestibulum eu justo sit amet nulla adipiscing imperdiet. Nullam venenatis tortor nec mauris viverra at rutrum lacus ultricies. Sed condimentum aliquet congue! Aenean sed justo sapien.</p><p>Fusce ut turpis mauris. Phasellus ac felis arcu, semper rhoncus nisi? Quisque tortor nisl; convallis et varius vel, ullamcorper sit amet neque. Sed semper aliquam rutrum.</p><p>Phasellus ac nisl et quam laoreet interdum ultrices vel dui. Mauris et urna sed tortor molestie blandit quis vel sapien. Cras tempus sollicitudin magna eu faucibus. Nulla dapibus pharetra dapibus.</p>','','',1),
	(18,'en','service-1','Service 1','','','<p>Lorem ipsum dolor sit amet consectetur adipiscing elit Nulla volutpat aliquam velit</p><p>Phasellus iaculis neque purus sodales ultricies vestibulum laoreet porttitor sem ac tristique libero volutpat at</p><p>Faucibus porta lacus fringilla vel.</p>','','',1),
	(19,'en','service-2','Service 2','','','<p>Lorem ipsum dolor sit amet consectetur adipiscing elit Nulla volutpat aliquam velit</p><p>Phasellus iaculis neque purus sodales ultricies vestibulum laoreet porttitor sem ac tristique libero volutpat at</p><p>Faucibus porta lacus fringilla vel.</p>','','',1),
	(20,'en','service-3','Service 3','','','<p>Lorem ipsum dolor sit amet consectetur adipiscing elit Nulla volutpat aliquam velit</p><p>Phasellus iaculis neque purus sodales ultricies vestibulum laoreet porttitor sem ac tristique libero volutpat at</p><p>Faucibus porta lacus fringilla vel.</p>','','',1),
	(24,'en','contact-informations','Contact Informations','','','<p><strong>Ionize CMS</strong><br /> This is our street<br />AABBCC In This City</p>\n<p><strong>Tel</strong> : +33 012345678<strong><br />Email : </strong>team@ionizecms.com</p>','','',1),
	(26,'en','our-location','Our location','','','<p><iframe width=\"100%\" height=\"250\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" src=\"https://maps.google.fr/maps?f=q&amp;source=s_q&amp;hl=fr&amp;geocode=&amp;aq=&amp;sll=48.876161,2.377124&amp;sspn=0.007296,0.018175&amp;gl=fr&amp;g=villa+Marcel+Lods&amp;ie=UTF8&amp;hq=&amp;ll=48.876161,2.377124&amp;spn=0.001824,0.004544&amp;t=m&amp;z=16&amp;output=embed\"></iframe></p>','','',1),
	(27,'en','whats-ionize','What\'s Ionize ?','','','<p>Ionize is an Open Source content management system created by webdesigners for webdesigners.</p><p>Originally <a title=\"Ionize\'s development agence internet\" href=\"http://www.ionizecms.com/en/about\">created by Partikule and Toopixel</a> for their clients, Ionize is today an OpenSource project, so everybody can build easy to maintain websites.</p>',NULL,NULL,1),
	(28,'en','can-i-help','Can I help ?','','','<p>Because talent is nothing without involvement, we are looking for motivated coders and webdesigners to join the project team.</p><p>You have a module idea ?<br />You want to make some improvement ?<br />You wants to get involved in a promising CMS ?</p>','','',1),
	(29,'en','send-us-a-message','Send us a message !','','','',NULL,NULL,1),
	(30,'en','not-logged-in','Not logged in','','','',NULL,NULL,1),
	(31,'en','hello','Hello','','','<p>Welcome to your account management page.</p>','','',1),
	(32,'en','401','401','Please login',NULL,'<p>Please login to see this content.</p>',NULL,NULL,1),
	(33,'en','403','403','Forbidden',NULL,'<p>This content is forbidden.</p>',NULL,NULL,1);
--#--


-- 
-- Article_media
-- 
TRUNCATE article_media;
--#--

INSERT INTO article_media (id_article, id_media, online, ordering, url, lang_display)
VALUES
	(4,3,1,1,NULL,NULL),
	(5,18,1,1,NULL,NULL),
	(6,6,1,3,NULL,NULL),
	(6,7,1,4,NULL,NULL),
	(6,8,1,2,NULL,NULL),
	(8,3,1,1,NULL,NULL),
	(9,8,1,1,NULL,NULL),
	(10,4,1,1,NULL,NULL),
	(15,5,1,2,NULL,NULL),
	(17,3,1,1,NULL,NULL);
--#--

--
-- Article Tag
--
TRUNCATE article_tag;
--#--

INSERT INTO article_tag (id_article, id_tag)
VALUES
	(5,1),
	(5,3),
	(6,2),
	(6,4),
	(7,1),
	(8,4),
	(9,4),
	(10,1),
	(14,0);
--#--

-- 
-- Article_type
-- 
TRUNCATE article_type;
--#--

INSERT INTO article_type (id_type, type, ordering, description, type_flag)
VALUES
	(4,'bloc',0,'',5),
	(5,'not-logged-in',0,'',1),
	(6,'logged-in',0,'',4);
--#--


-- 
-- Category
-- 
TRUNCATE category;
--#--

INSERT INTO category (id_category, name, ordering)
VALUES
	(1,'ionize',0),
	(2,'website',0);
--#--


--
-- Category_lang
-- 
TRUNCATE category_lang;
--#--

INSERT INTO category_lang (id_category, lang, title, subtitle, description)
VALUES
	(1,'en','Ionize CMS','',''),
	(2,'en','Website','','');
--#--


-- 
-- Static Items
-- 
TRUNCATE item;
--#--

INSERT INTO item (id_item, id_item_definition, name, description, ordering)
VALUES
	(2,1,NULL,NULL,1);
--#--


--
-- Static Items Definitions
--
TRUNCATE item_definition;
--#--

INSERT INTO item_definition (id_item_definition, name, description)
VALUES
	(1,'flag','Article\'s Flags');
--#--


--
-- Static Items Definition Lang
--
TRUNCATE item_definition_lang;
--#--

INSERT INTO item_definition_lang (id_item_definition, lang, title_definition, title_item)
VALUES
	(1,'en','Flags','Flag');


--
-- Media
--
TRUNCATE media;
--#--

INSERT INTO media (id_media, type, file_name, path, base_path, copyright, provider, date, link, square_crop)
VALUES
	(3,'picture','screenshot_ionize_dashboard.jpg','files/screenshot_ionize_dashboard.jpg','files/',NULL,NULL,'0000-00-00 00:00:00',NULL,'m'),
	(4,'picture','IMG_8359.jpg','files/pictures/IMG_8359.jpg','files/pictures/','',NULL,'0000-00-00 00:00:00','','m'),
	(5,'picture','IMG_8447.jpg','files/pictures/IMG_8447.jpg','files/pictures/','',NULL,'0000-00-00 00:00:00','','m'),
	(6,'picture','IMG_8632.jpg','files/pictures/IMG_8632.jpg','files/pictures/','',NULL,'0000-00-00 00:00:00','','m'),
	(7,'picture','IMG_8645.jpg','files/pictures/IMG_8645.jpg','files/pictures/','',NULL,'0000-00-00 00:00:00','','m'),
	(8,'picture','IMG_8963.jpg','files/pictures/IMG_8963.jpg','files/pictures/',NULL,NULL,'0000-00-00 00:00:00',NULL,'m');
--#--


-- 
-- Media_lang
-- 
TRUNCATE media_lang;
--#--

INSERT INTO media_lang (lang, id_media, title, alt, description)
VALUES
	('en',4,'Mingun','Alot of young munks live in a monastery next to the Hsinbyume pagoda','Alot of young munks live in a monastery next to the Hsinbyume pagoda'),
	('en',5,'Mandalay','One working day in the historical capital of Burma.','One working day in the historical capital of Burma.'),
	('en',6,'Monywa, Burma','In the mountain of Monywa, more than two thousand caves were built in honor of Budha.','In the mountain of Monywa, more than two thousand caves were built in honor of Budha.'),
	('en',7,'Monywa wall paints','These 17th centruey wall painting are very well preserved because the caves were forgivven for centuries.','These 17th centruey wall painting are very well preserved because the caves were forgivven for centuries.');
--#--


-- 
-- Menu
-- 
TRUNCATE menu;
--#--

INSERT INTO menu (id_menu, name, title, ordering)
VALUES
	(1,'main','Main menu',NULL),
	(2,'system','System menu',NULL);
--#--



-- 
-- Page
-- 
TRUNCATE page;
--#--

INSERT INTO page (id_page, id_parent, id_menu, id_type, id_subnav, name, ordering, level, online, home, author, updater, created, publish_on, publish_off, updated, logical_date, appears, has_url, view, view_single, article_list_view, article_view, article_order, article_order_direction, link, link_type, link_id, pagination, priority, used_by_module, deny_code)
VALUES
	(1,0,2,0,0,'404',0,0,1,0,'demo','demo','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 14:42:05','0000-00-00 00:00:00',0,1,'','','','','ordering','ASC','',NULL,'',0,5,0,NULL),
	(2,0,1,0,0,'welcome',1,0,1,1,'demo','michelange','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2014-04-05 10:16:24','0000-00-00 00:00:00',1,1,'page_home','','','','ordering','ASC','',NULL,'',0,5,0,'404'),
	(3,0,1,0,0,'about-ionize-cms',5,0,1,0,'demo','demo','2012-11-17 09:42:18','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-06 16:27:42','0000-00-00 00:00:00',1,1,'pages/page_standard','pages/page_standard',NULL,'','ordering','ASC','',NULL,'',0,5,0,NULL),
	(4,0,1,0,0,'contact',6,0,1,0,'demo','demo','2012-11-17 09:42:35','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 14:34:59','0000-00-00 00:00:00',1,1,'page_contact','','','','ordering','ASC','',NULL,'',0,5,0,NULL),
	(6,0,1,0,0,'blog',2,0,1,0,'demo','demo','2012-11-17 13:57:58','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 14:07:43','0000-00-00 00:00:00',1,1,'page_blog','page_blog_post','','','ordering','ASC','',NULL,'',3,5,0,NULL),
	(8,0,1,0,0,'services',4,0,1,0,'demo','demo','2012-11-20 22:12:25','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-21 16:53:44','0000-00-00 00:00:00',1,1,'','','','','ordering','ASC','',NULL,'',0,5,0,NULL),
	(9,0,2,0,0,'footer',1,0,1,0,'demo','demo','2012-11-21 11:52:24','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-11-21 11:52:30','0000-00-00 00:00:00',0,1,'','',NULL,'','ordering','ASC','',NULL,'',0,5,0,NULL),
	(10,0,1,0,0,'my-account',7,0,1,0,'demo','demo','2012-12-21 20:11:47','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-12-22 10:53:57','0000-00-00 00:00:00',1,1,'page_my_account','',NULL,'','ordering','ASC','',NULL,'',0,5,0,NULL),
	(11,0,2,0,0,'401',0,0,1,0,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',0,1,NULL,NULL,NULL,NULL,'ordering','ASC','',NULL,'',0,5,NULL,NULL),
	(12,0,2,0,0,'403',0,0,1,0,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',0,1,NULL,NULL,NULL,NULL,'ordering','ASC','',NULL,'',0,5,NULL,NULL);
--#--


--
-- Page_article
--
TRUNCATE page_article;
--#--

INSERT INTO page_article (id_page, id_article, online, view, ordering, id_type, link_type, link_id, link, main_parent)
VALUES
	(1,1,1,NULL,0,NULL,'','','',1),
	(2,11,1,NULL,4,0,'page','8','Services',1),
	(2,12,1,NULL,3,0,'page','8','Services',1),
	(2,13,1,NULL,2,0,'page','8','Services',1),
	(2,14,1,NULL,1,0,'page','8','Services',1),
	(3,15,1,NULL,1,NULL,'','','',1),
	(4,24,1,NULL,2,4,'','','',1),
	(4,26,1,NULL,3,4,'','','',1),
	(4,29,1,NULL,1,NULL,'','','',1),
	(6,5,1,NULL,1,NULL,'','','',1),
	(6,6,1,NULL,2,NULL,'','','',1),
	(6,7,1,NULL,3,NULL,'','','',1),
	(6,8,1,NULL,4,NULL,'','','',1),
	(6,9,1,NULL,5,NULL,'','','',1),
	(6,10,1,NULL,6,0,'','','',1),
	(8,17,1,NULL,1,NULL,'','','',1),
	(8,18,1,NULL,2,4,'','','',1),
	(8,19,1,NULL,3,4,'','','',1),
	(8,20,1,NULL,4,4,'','','',1),
	(9,16,1,NULL,3,NULL,'','','',1),
	(9,27,1,NULL,1,NULL,'','','',1),
	(9,28,1,NULL,2,NULL,'','','',1),
	(10,30,1,NULL,2,5,'','','',1),
	(10,31,1,NULL,1,6,'','','',1),
	(11,32,1,NULL,0,NULL,'','','',0),
	(12,33,1,NULL,0,NULL,'','','',0);
--#--


-- 
-- Page_lang
-- 
		
TRUNCATE page_lang;
--#--

INSERT INTO page_lang (lang, id_page, url, link, title, subtitle, nav_title, subnav_title, meta_title, meta_description, meta_keywords, online)
VALUES
	('en',1,'404','','404','Can\'t find requested page !','','','','','',1),
	('en',2,'home','','Ionize CMS','the most professional \n& friendly CMS ever made.','Home','','Welcome To Ionize CMS','','',1),
	('en',3,'about-ionize-cms','','About Us','Some info about us !','','','','','',1),
	('en',4,'contact','','Contact','Find us, send us one message','','','','','',1),
	('en',6,'blog','','Blog','Interesting stories ','','','','','',1),
	('en',8,'services','','Services','The very great services we can provide to make you happy','','','','','',1),
	('en',9,'footer','','Footer','','','','','','',1),
	('en',10,'my-account','','My account','','','','','','',1),
	('en',11,'401','','401','Login needed','','',NULL,NULL,NULL,1),
	('en',12,'403','','403','Forbidden','','',NULL,NULL,NULL,1);
--#--


-- 
-- Page_media
-- 
TRUNCATE page_media;
--#--

INSERT INTO page_media (id_page, id_media, online, ordering, lang_display)
VALUES
	(2,4,1,13,NULL),
	(2,5,1,10,NULL),
	(2,6,1,1,NULL),
	(2,7,1,11,NULL),
	(2,8,1,12,NULL);
--#--


--
-- Tag
--
TRUNCATE tag;
--#--

INSERT INTO tag (id_tag, tag_name)
VALUES
	(1,'Content Management'),
	(2,'development'),
	(3,'company'),
	(4,'services');
--#--


--
-- URL
--
TRUNCATE url;
--#--

INSERT INTO url (id_url, id_entity, type, canonical, active, lang, path, path_ids, full_path_ids, creation_date)
VALUES
	(596,4,'article',1,1,'en','home/the-power-of-php','2/4','2/4','2012-12-23 01:05:34'),
	(601,1,'page',1,1,'en','404','1','1','2012-12-28 19:56:07'),
	(602,1,'article',1,1,'en','404/404','1/1','1/1','2012-12-28 19:56:07'),
	(603,2,'page',1,1,'en','home','2','2','2012-12-28 19:56:07'),
	(604,11,'article',1,1,'en','home/easy-edition','2/11','2/11','2012-12-28 19:56:07'),
	(605,12,'article',1,1,'en','home/multilingual','2/12','2/12','2012-12-28 19:56:07'),
	(606,13,'article',1,1,'en','home/userfriendly','2/13','2/13','2012-12-28 19:56:07'),
	(607,14,'article',1,1,'en','home/template-system','2/14','2/14','2012-12-28 19:56:07'),
	(608,3,'page',1,1,'en','about-ionize-cms','3','3','2012-12-28 19:56:07'),
	(609,15,'article',1,1,'en','about-ionize-cms/about-us-1','3/15','3/15','2012-12-28 19:56:07'),
	(610,4,'page',1,1,'en','contact','4','4','2012-12-28 19:56:07'),
	(611,24,'article',1,1,'en','contact/contact-informations','4/24','4/24','2012-12-28 19:56:07'),
	(612,26,'article',1,1,'en','contact/our-location','4/26','4/26','2012-12-28 19:56:07'),
	(613,29,'article',1,1,'en','contact/send-us-a-message','4/29','4/29','2012-12-28 19:56:07'),
	(614,6,'page',1,1,'en','blog','6','6','2012-12-28 19:56:07'),
	(615,5,'article',1,1,'en','blog/article-1','6/5','6/5','2012-12-28 19:56:07'),
	(616,6,'article',1,1,'en','blog/article-2','6/6','6/6','2012-12-28 19:56:07'),
	(617,7,'article',1,1,'en','blog/article-3','6/7','6/7','2012-12-28 19:56:07'),
	(618,8,'article',1,1,'en','blog/article-4','6/8','6/8','2012-12-28 19:56:07'),
	(619,9,'article',1,1,'en','blog/article-5','6/9','6/9','2012-12-28 19:56:07'),
	(620,10,'article',1,1,'en','blog/article-6','6/10','6/10','2012-12-28 19:56:07'),
	(621,8,'page',1,1,'en','services','8','8','2012-12-28 19:56:07'),
	(622,17,'article',1,1,'en','services/some-of-our-services','8/17','8/17','2012-12-28 19:56:07'),
	(623,18,'article',1,1,'en','services/service-1','8/18','8/18','2012-12-28 19:56:07'),
	(624,19,'article',1,1,'en','services/service-2','8/19','8/19','2012-12-28 19:56:07'),
	(625,20,'article',1,1,'en','services/service-3','8/20','8/20','2012-12-28 19:56:07'),
	(626,9,'page',1,1,'en','footer','9','9','2012-12-28 19:56:07'),
	(627,16,'article',1,1,'en','footer/footer-resources','9/16','9/16','2012-12-28 19:56:07'),
	(628,27,'article',1,1,'en','footer/whats-ionize','9/27','9/27','2012-12-28 19:56:07'),
	(629,28,'article',1,1,'en','footer/can-i-help','9/28','9/28','2012-12-28 19:56:07'),
	(630,10,'page',1,1,'en','my-account','10','10','2012-12-28 19:56:07'),
	(631,30,'article',1,1,'en','my-account/not-logged-in','10/30','10/30','2012-12-28 19:56:07'),
	(632,31,'article',1,1,'en','my-account/hello','10/31','10/31','2012-12-28 19:56:07');
--#--
