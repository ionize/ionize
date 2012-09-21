# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.9)
# Database: ionize_ftl
# Generation Time: 2012-09-21 06:36:33 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table article
# ------------------------------------------------------------

DROP TABLE IF EXISTS `article`;

CREATE TABLE `article` (
  `id_article` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updater` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `publish_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_off` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL,
  `logical_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `indexed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_category` int(11) unsigned DEFAULT NULL,
  `comment_allow` char(1) COLLATE utf8_unicode_ci DEFAULT '0',
  `comment_autovalid` char(1) COLLATE utf8_unicode_ci DEFAULT '0',
  `comment_expire` datetime DEFAULT NULL,
  `flag` smallint(1) DEFAULT '0',
  `has_url` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id_article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `article` WRITE;
/*!40000 ALTER TABLE `article` DISABLE KEYS */;

INSERT INTO `article` (`id_article`, `name`, `author`, `updater`, `created`, `publish_on`, `publish_off`, `updated`, `logical_date`, `indexed`, `id_category`, `comment_allow`, `comment_autovalid`, `comment_expire`, `flag`, `has_url`, `code`)
VALUES
	(10,'404',NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',0,NULL,'0','0',NULL,0,1,'404'),
	(20,'welcome3','','admin','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-08 09:24:21','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,'welcome'),
	(30,'article-30','admin','admin','2012-09-04 12:45:34','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-21 13:54:48','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,'article-1'),
	(40,'article-40','admin','admin','2012-09-04 12:45:44','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-09 21:47:59','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,'article-2'),
	(41,'iframe','admin','admin','2012-09-08 09:25:02','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-08 09:40:13','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,''),
	(42,'article-50','admin',NULL,'2012-09-09 13:41:33','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-09 13:41:33','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,''),
	(43,'article-60','admin','admin','2012-09-09 17:06:34','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-09 17:10:04','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,''),
	(44,'article-70','admin','admin','2012-09-09 21:31:44','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-09 21:32:20','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,''),
	(45,'article-80','admin','admin','2012-09-09 21:44:13','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-09 21:46:18','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,''),
	(46,'post-1','admin','admin','2012-09-21 09:47:29','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-21 10:07:34','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,''),
	(47,'',NULL,NULL,'2012-09-21 09:48:34','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-21 09:48:34','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,''),
	(48,'',NULL,NULL,'2012-09-21 09:48:39','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-21 09:48:39','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,''),
	(49,'',NULL,NULL,'2012-09-21 09:48:46','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-21 09:48:46','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,''),
	(50,'post-2','admin','admin','2012-09-21 10:00:51','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-21 10:24:59','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,''),
	(51,'post-3','admin','admin','2012-09-21 10:01:15','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-21 10:25:08','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,''),
	(52,'post-4','admin','admin','2012-09-21 10:25:41','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-21 10:25:51','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,''),
	(53,'post-5','admin','admin','2012-09-21 10:26:03','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-21 10:26:11','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1,'');

/*!40000 ALTER TABLE `article` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table article_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `article_category`;

CREATE TABLE `article_category` (
  `id_article` int(11) unsigned NOT NULL,
  `id_category` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `article_category` WRITE;
/*!40000 ALTER TABLE `article_category` DISABLE KEYS */;

INSERT INTO `article_category` (`id_article`, `id_category`)
VALUES
	(20,3),
	(41,2),
	(40,1),
	(42,1),
	(43,3),
	(45,3),
	(30,3);

/*!40000 ALTER TABLE `article_category` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table article_comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `article_comment`;

CREATE TABLE `article_comment` (
  `id_article_comment` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_article` int(11) unsigned NOT NULL DEFAULT '0',
  `author` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `site` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `ip` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(3) unsigned DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'If comment comes from admin, set to 1',
  PRIMARY KEY (`id_article_comment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table article_lang
# ------------------------------------------------------------

DROP TABLE IF EXISTS `article_lang`;

CREATE TABLE `article_lang` (
  `id_article` int(11) unsigned NOT NULL DEFAULT '0',
  `lang` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subtitle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `summary` longtext COLLATE utf8_unicode_ci,
  `content` longtext COLLATE utf8_unicode_ci,
  `meta_keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `online` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_article`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `article_lang` WRITE;
/*!40000 ALTER TABLE `article_lang` DISABLE KEYS */;

INSERT INTO `article_lang` (`id_article`, `lang`, `url`, `title`, `subtitle`, `meta_title`, `summary`, `content`, `meta_keywords`, `meta_description`, `online`)
VALUES
	(10,'en','404','404',NULL,NULL,NULL,'<p>The content you asked was not found !</p>',NULL,NULL,1),
	(10,'fr','404',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),
	(20,'en','welcome-article-url','Title 20... et hop','','','','<p>Content Article 20...</p>','','',1),
	(20,'fr','welcome-article-url','Titre Article 20... et hop','','','','','','',1),
	(30,'en','article-30','Title Article 30...','','','<p>Summary article 30</p>','<p>Content Article 30...</p>','','',1),
	(30,'fr','article-30','Titre Article 30...','','','','<p>Contenu Article 30...</p>','','',1),
	(40,'en','article-40','Title Article 40...','','Window Title from Article 40...','','<p>Content Article 40 ...</p>','Keyword Article 40...','English Description Article 40....',1),
	(40,'fr','article-40','Titre Article 40...','','','','<p>Contenu Article 40...</p>','Mot clé Article 40...','Description française Article 40...',1),
	(41,'en','iframe-in-content','iFrame in Content','','','','<p>iFrame YouTube video :</p>\n<p><iframe width=\"600\" height=\"450\" src=\"http://youtu.be/GUEZCxBcM78\" frameborder=\"0\"></iframe></p>\n<p>Google Map :</p>\n<p><iframe width=\"425\" height=\"350\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" src=\"https://www.google.com/maps?f=q&amp;source=s_q&amp;hl=fr&amp;geocode=&amp;q=Seremban+Negeri+Sembilan+Malaysia+permata+1&amp;aq=&amp;sll=2.723983,101.947645&amp;sspn=0.087448,0.153637&amp;ie=UTF8&amp;hq=&amp;hnear=Jalan+Permata+1,+Taman+Permata,+70200+Seremban,+Negeri+Sembilan,+Malaisie&amp;t=m&amp;z=14&amp;ll=2.728023,101.92695&amp;output=embed\"></iframe><br /><a href=\"https://www.google.com/maps?f=q&amp;source=embed&amp;hl=fr&amp;geocode=&amp;q=Seremban+Negeri+Sembilan+Malaysia+permata+1&amp;aq=&amp;sll=2.723983,101.947645&amp;sspn=0.087448,0.153637&amp;ie=UTF8&amp;hq=&amp;hnear=Jalan+Permata+1,+Taman+Permata,+70200+Seremban,+Negeri+Sembilan,+Malaisie&amp;t=m&amp;z=14&amp;ll=2.728023,101.92695\" style=\"color: #0000ff; text-align: left;\">Agrandir le plan</a></p>','','',1),
	(41,'fr','iframe-in-content','iFrame in Content','','','','<p>iFrame YouTube video :</p>\n<p><iframe width=\"600\" height=\"450\" src=\"http://youtu.be/GUEZCxBcM78\" frameborder=\"0&quot;\"></iframe></p>\n<p>Google Map :</p>\n<p><iframe width=\"425\" height=\"350\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" src=\"https://www.google.com/maps?f=q&amp;source=s_q&amp;hl=fr&amp;geocode=&amp;q=Seremban+Negeri+Sembilan+Malaysia+permata+1&amp;aq=&amp;sll=2.723983,101.947645&amp;sspn=0.087448,0.153637&amp;ie=UTF8&amp;hq=&amp;hnear=Jalan+Permata+1,+Taman+Permata,+70200+Seremban,+Negeri+Sembilan,+Malaisie&amp;t=m&amp;z=14&amp;ll=2.728023,101.92695&amp;output=embed\"></iframe><br /><a href=\"https://www.google.com/maps?f=q&amp;source=embed&amp;hl=fr&amp;geocode=&amp;q=Seremban+Negeri+Sembilan+Malaysia+permata+1&amp;aq=&amp;sll=2.723983,101.947645&amp;sspn=0.087448,0.153637&amp;ie=UTF8&amp;hq=&amp;hnear=Jalan+Permata+1,+Taman+Permata,+70200+Seremban,+Negeri+Sembilan,+Malaisie&amp;t=m&amp;z=14&amp;ll=2.728023,101.92695\" style=\"color: #0000ff; text-align: left;\">Agrandir le plan</a></p>','','',1),
	(42,'en','article-50','Title Article 50...','','','','<p>Content Article 50...</p>',NULL,NULL,1),
	(42,'fr','article-50','Titre Article 50...','','','','<p>Contenu Article 50...</p>',NULL,NULL,1),
	(43,'en','article-60','Title Article 60...','','','','<p>Content Article 60...</p>','','',1),
	(43,'fr','article-60','Titre Article 60...','','','','<p>Contenu Article 60...</p>','','',1),
	(44,'en','article-70','Title Article 70...','','','','<p>Content Article 70...</p>','','',1),
	(44,'fr','article-70','Titre Article 70...','','','','<p>Contenu Article 70...</p>','','',1),
	(45,'en','article-80','Title Article 80...','','','','<p>Content Article 80...</p>','','',1),
	(45,'fr','article-80','Titre Article 80...','','','','<p>Contenu Article 80...</p>','','',1),
	(46,'en','post-1','Blog post 1','','','','<p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>\n\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>\n<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.</p>\n','','',1),
	(46,'fr','post-1','Blog post 1','','','','<p>Le <strong>Lorem Ipsum</strong> est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l\'imprimerie depuis les années 1500, quand un peintre anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n\'a pas fait que survivre cinq siècles, mais s\'est aussi adapté à la bureautique informatique, sans que son contenu n\'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.</p>\n<p>On sait depuis longtemps que travailler avec du texte lisible et contenant du sens est source de distractions, et empêche de se concentrer sur la mise en page elle-même. L\'avantage du Lorem Ipsum sur un texte générique comme \'Du texte. Du texte. Du texte.\' est qu\'il possède une distribution de lettres plus ou moins normale, et en tout cas comparable avec celle du français standard. De nombreuses suites logicielles de mise en page ou éditeurs de sites Web ont fait du Lorem Ipsum leur faux texte par défaut, et une recherche pour \'Lorem Ipsum\' vous conduira vers de nombreux sites qui n\'en sont encore qu\'à leur phase de construction. Plusieurs versions sont apparues avec le temps, parfois par accident, souvent intentionnellement (histoire d\'y rajouter de petits clins d\'oeil, voire des phrases embarassantes).</p>\n<p>Contrairement à une opinion répandue, le Lorem Ipsum n\'est pas simplement du texte aléatoire. Il trouve ses racines dans une oeuvre de la littérature latine classique datant de 45 av. J.-C., le rendant vieux de 2000 ans. Un professeur du Hampden-Sydney College, en Virginie, s\'est intéressé à un des mots latins les plus obscurs, consectetur, extrait d\'un passage du Lorem Ipsum, et en étudiant tous les usages de ce mot dans la littérature classique, découvrit la source incontestable du Lorem Ipsum. Il provient en fait des sections 1.10.32 et 1.10.33 du \"De Finibus Bonorum et Malorum\" (Des Suprêmes Biens et des Suprêmes Maux) de Cicéron. Cet ouvrage, très populaire pendant la Renaissance, est un traité sur la théorie de l\'éthique. Les premières lignes du Lorem Ipsum, \"Lorem ipsum dolor sit amet...\", proviennent de la section 1.10.32.</p>','','',1),
	(50,'en','post-2','Blog post 2','','','','<p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>\n<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.</p>','','',1),
	(50,'fr','post-2','Blog post 2','','','','<p>Le <strong>Lorem Ipsum</strong> est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l\'imprimerie depuis les années 1500, quand un peintre anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n\'a pas fait que survivre cinq siècles, mais s\'est aussi adapté à la bureautique informatique, sans que son contenu n\'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.</p>\n<p>On sait depuis longtemps que travailler avec du texte lisible et contenant du sens est source de distractions, et empêche de se concentrer sur la mise en page elle-même. L\'avantage du Lorem Ipsum sur un texte générique comme \'Du texte. Du texte. Du texte.\' est qu\'il possède une distribution de lettres plus ou moins normale, et en tout cas comparable avec celle du français standard. De nombreuses suites logicielles de mise en page ou éditeurs de sites Web ont fait du Lorem Ipsum leur faux texte par défaut, et une recherche pour \'Lorem Ipsum\' vous conduira vers de nombreux sites qui n\'en sont encore qu\'à leur phase de construction. Plusieurs versions sont apparues avec le temps, parfois par accident, souvent intentionnellement (histoire d\'y rajouter de petits clins d\'oeil, voire des phrases embarassantes).</p>\n<p>Contrairement à une opinion répandue, le Lorem Ipsum n\'est pas simplement du texte aléatoire. Il trouve ses racines dans une oeuvre de la littérature latine classique datant de 45 av. J.-C., le rendant vieux de 2000 ans. Un professeur du Hampden-Sydney College, en Virginie, s\'est intéressé à un des mots latins les plus obscurs, consectetur, extrait d\'un passage du Lorem Ipsum, et en étudiant tous les usages de ce mot dans la littérature classique, découvrit la source incontestable du Lorem Ipsum. Il provient en fait des sections 1.10.32 et 1.10.33 du \"De Finibus Bonorum et Malorum\" (Des Suprêmes Biens et des Suprêmes Maux) de Cicéron. Cet ouvrage, très populaire pendant la Renaissance, est un traité sur la théorie de l\'éthique. Les premières lignes du Lorem Ipsum, \"Lorem ipsum dolor sit amet...\", proviennent de la section 1.10.32.</p>','','',1),
	(51,'en','post-3','Blog post 3','','','','<p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>\n<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.</p>','','',1),
	(51,'fr','post-3','Blog post 3','','','','<p>Le <strong>Lorem Ipsum</strong> est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l\'imprimerie depuis les années 1500, quand un peintre anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n\'a pas fait que survivre cinq siècles, mais s\'est aussi adapté à la bureautique informatique, sans que son contenu n\'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.</p>\n<p>On sait depuis longtemps que travailler avec du texte lisible et contenant du sens est source de distractions, et empêche de se concentrer sur la mise en page elle-même. L\'avantage du Lorem Ipsum sur un texte générique comme \'Du texte. Du texte. Du texte.\' est qu\'il possède une distribution de lettres plus ou moins normale, et en tout cas comparable avec celle du français standard. De nombreuses suites logicielles de mise en page ou éditeurs de sites Web ont fait du Lorem Ipsum leur faux texte par défaut, et une recherche pour \'Lorem Ipsum\' vous conduira vers de nombreux sites qui n\'en sont encore qu\'à leur phase de construction. Plusieurs versions sont apparues avec le temps, parfois par accident, souvent intentionnellement (histoire d\'y rajouter de petits clins d\'oeil, voire des phrases embarassantes).</p>\n<p>Contrairement à une opinion répandue, le Lorem Ipsum n\'est pas simplement du texte aléatoire. Il trouve ses racines dans une oeuvre de la littérature latine classique datant de 45 av. J.-C., le rendant vieux de 2000 ans. Un professeur du Hampden-Sydney College, en Virginie, s\'est intéressé à un des mots latins les plus obscurs, consectetur, extrait d\'un passage du Lorem Ipsum, et en étudiant tous les usages de ce mot dans la littérature classique, découvrit la source incontestable du Lorem Ipsum. Il provient en fait des sections 1.10.32 et 1.10.33 du \"De Finibus Bonorum et Malorum\" (Des Suprêmes Biens et des Suprêmes Maux) de Cicéron. Cet ouvrage, très populaire pendant la Renaissance, est un traité sur la théorie de l\'éthique. Les premières lignes du Lorem Ipsum, \"Lorem ipsum dolor sit amet...\", proviennent de la section 1.10.32.</p>','','',1),
	(52,'en','post-4','Blog post 4','','','','<p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>\n<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.</p>','','',1),
	(52,'fr','post-4','Blog post 4','','','','<p>Le <strong>Lorem Ipsum</strong> est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l\'imprimerie depuis les années 1500, quand un peintre anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n\'a pas fait que survivre cinq siècles, mais s\'est aussi adapté à la bureautique informatique, sans que son contenu n\'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.</p>\n<p>On sait depuis longtemps que travailler avec du texte lisible et contenant du sens est source de distractions, et empêche de se concentrer sur la mise en page elle-même. L\'avantage du Lorem Ipsum sur un texte générique comme \'Du texte. Du texte. Du texte.\' est qu\'il possède une distribution de lettres plus ou moins normale, et en tout cas comparable avec celle du français standard. De nombreuses suites logicielles de mise en page ou éditeurs de sites Web ont fait du Lorem Ipsum leur faux texte par défaut, et une recherche pour \'Lorem Ipsum\' vous conduira vers de nombreux sites qui n\'en sont encore qu\'à leur phase de construction. Plusieurs versions sont apparues avec le temps, parfois par accident, souvent intentionnellement (histoire d\'y rajouter de petits clins d\'oeil, voire des phrases embarassantes).</p>\n<p>Contrairement à une opinion répandue, le Lorem Ipsum n\'est pas simplement du texte aléatoire. Il trouve ses racines dans une oeuvre de la littérature latine classique datant de 45 av. J.-C., le rendant vieux de 2000 ans. Un professeur du Hampden-Sydney College, en Virginie, s\'est intéressé à un des mots latins les plus obscurs, consectetur, extrait d\'un passage du Lorem Ipsum, et en étudiant tous les usages de ce mot dans la littérature classique, découvrit la source incontestable du Lorem Ipsum. Il provient en fait des sections 1.10.32 et 1.10.33 du \"De Finibus Bonorum et Malorum\" (Des Suprêmes Biens et des Suprêmes Maux) de Cicéron. Cet ouvrage, très populaire pendant la Renaissance, est un traité sur la théorie de l\'éthique. Les premières lignes du Lorem Ipsum, \"Lorem ipsum dolor sit amet...\", proviennent de la section 1.10.32.</p>','','',1),
	(53,'en','post-5','Blog post 5','','','','<p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>\n<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>\n<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.</p>','','',1),
	(53,'fr','post-5','Blog post 5','','','','<p>Le <strong>Lorem Ipsum</strong> est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l\'imprimerie depuis les années 1500, quand un peintre anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n\'a pas fait que survivre cinq siècles, mais s\'est aussi adapté à la bureautique informatique, sans que son contenu n\'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.</p>\n<p>On sait depuis longtemps que travailler avec du texte lisible et contenant du sens est source de distractions, et empêche de se concentrer sur la mise en page elle-même. L\'avantage du Lorem Ipsum sur un texte générique comme \'Du texte. Du texte. Du texte.\' est qu\'il possède une distribution de lettres plus ou moins normale, et en tout cas comparable avec celle du français standard. De nombreuses suites logicielles de mise en page ou éditeurs de sites Web ont fait du Lorem Ipsum leur faux texte par défaut, et une recherche pour \'Lorem Ipsum\' vous conduira vers de nombreux sites qui n\'en sont encore qu\'à leur phase de construction. Plusieurs versions sont apparues avec le temps, parfois par accident, souvent intentionnellement (histoire d\'y rajouter de petits clins d\'oeil, voire des phrases embarassantes).</p>\n<p>Contrairement à une opinion répandue, le Lorem Ipsum n\'est pas simplement du texte aléatoire. Il trouve ses racines dans une oeuvre de la littérature latine classique datant de 45 av. J.-C., le rendant vieux de 2000 ans. Un professeur du Hampden-Sydney College, en Virginie, s\'est intéressé à un des mots latins les plus obscurs, consectetur, extrait d\'un passage du Lorem Ipsum, et en étudiant tous les usages de ce mot dans la littérature classique, découvrit la source incontestable du Lorem Ipsum. Il provient en fait des sections 1.10.32 et 1.10.33 du \"De Finibus Bonorum et Malorum\" (Des Suprêmes Biens et des Suprêmes Maux) de Cicéron. Cet ouvrage, très populaire pendant la Renaissance, est un traité sur la théorie de l\'éthique. Les premières lignes du Lorem Ipsum, \"Lorem ipsum dolor sit amet...\", proviennent de la section 1.10.32.</p>','','',1);

/*!40000 ALTER TABLE `article_lang` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table article_media
# ------------------------------------------------------------

DROP TABLE IF EXISTS `article_media`;

CREATE TABLE `article_media` (
  `id_article` int(11) unsigned NOT NULL DEFAULT '0',
  `id_media` int(11) unsigned NOT NULL DEFAULT '0',
  `online` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ordering` int(11) unsigned DEFAULT '9999',
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lang_display` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_article`,`id_media`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `article_media` WRITE;
/*!40000 ALTER TABLE `article_media` DISABLE KEYS */;

INSERT INTO `article_media` (`id_article`, `id_media`, `online`, `ordering`, `url`, `lang_display`)
VALUES
	(30,2,1,2,NULL,NULL),
	(30,4,1,1,NULL,NULL),
	(30,6,1,1,NULL,NULL),
	(30,7,1,2,NULL,NULL),
	(40,1,1,1,NULL,NULL);

/*!40000 ALTER TABLE `article_media` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table article_tag
# ------------------------------------------------------------

DROP TABLE IF EXISTS `article_tag`;

CREATE TABLE `article_tag` (
  `id_article` int(11) unsigned NOT NULL,
  `id_tag` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_article`,`id_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table article_type
# ------------------------------------------------------------

DROP TABLE IF EXISTS `article_type`;

CREATE TABLE `article_type` (
  `id_type` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ordering` int(11) DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `type_flag` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table captcha
# ------------------------------------------------------------

DROP TABLE IF EXISTS `captcha`;

CREATE TABLE `captcha` (
  `id_captcha` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `answer` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lang` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `hash` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id_captcha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id_category` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ordering` int(11) DEFAULT '0',
  PRIMARY KEY (`id_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;

INSERT INTO `category` (`id_category`, `name`, `ordering`)
VALUES
	(1,'art-contemporain',2),
	(2,'design',3),
	(3,'fashion',1);

/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table category_lang
# ------------------------------------------------------------

DROP TABLE IF EXISTS `category_lang`;

CREATE TABLE `category_lang` (
  `id_category` int(11) unsigned NOT NULL DEFAULT '0',
  `lang` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `subtitle` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_category`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `category_lang` WRITE;
/*!40000 ALTER TABLE `category_lang` DISABLE KEYS */;

INSERT INTO `category_lang` (`id_category`, `lang`, `title`, `subtitle`, `description`)
VALUES
	(1,'en','Contemporary Art','',''),
	(1,'fr','Art contemporain','',''),
	(2,'en','Design','',''),
	(2,'fr','Design','',''),
	(3,'en','Fashion','',''),
	(3,'fr','Mode','','');

/*!40000 ALTER TABLE `category_lang` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table element
# ------------------------------------------------------------

DROP TABLE IF EXISTS `element`;

CREATE TABLE `element` (
  `id_element` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_element_definition` int(11) unsigned NOT NULL,
  `parent` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id_parent` int(11) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_element`),
  KEY `idx_element_id_element_definition` (`id_element_definition`),
  KEY `idx_element_id_parent` (`id_parent`),
  KEY `idx_element_parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table element_definition
# ------------------------------------------------------------

DROP TABLE IF EXISTS `element_definition`;

CREATE TABLE `element_definition` (
  `id_element_definition` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_element_definition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `element_definition` WRITE;
/*!40000 ALTER TABLE `element_definition` DISABLE KEYS */;

INSERT INTO `element_definition` (`id_element_definition`, `name`, `description`, `ordering`)
VALUES
	(1,'toto','',0);

/*!40000 ALTER TABLE `element_definition` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table element_definition_lang
# ------------------------------------------------------------

DROP TABLE IF EXISTS `element_definition_lang`;

CREATE TABLE `element_definition_lang` (
  `id_element_definition` int(11) unsigned NOT NULL,
  `lang` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id_element_definition`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `element_definition_lang` WRITE;
/*!40000 ALTER TABLE `element_definition_lang` DISABLE KEYS */;

INSERT INTO `element_definition_lang` (`id_element_definition`, `lang`, `title`)
VALUES
	(1,'en',''),
	(1,'fr','');

/*!40000 ALTER TABLE `element_definition_lang` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table extend_field
# ------------------------------------------------------------

DROP TABLE IF EXISTS `extend_field`;

CREATE TABLE `extend_field` (
  `id_extend_field` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `parent` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ordering` int(11) DEFAULT '0',
  `translated` char(1) COLLATE utf8_unicode_ci DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci,
  `default_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `global` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `parents` varchar(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `id_element_definition` int(11) unsigned NOT NULL DEFAULT '0',
  `block` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id_extend_field`),
  KEY `idx_extend_field_parent` (`parent`),
  KEY `idx_extend_field_id_element_definition` (`id_element_definition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `extend_field` WRITE;
/*!40000 ALTER TABLE `extend_field` DISABLE KEYS */;

INSERT INTO `extend_field` (`id_extend_field`, `name`, `type`, `description`, `parent`, `ordering`, `translated`, `value`, `default_value`, `global`, `parents`, `id_element_definition`, `block`)
VALUES
	(1,'extend1','1','','article',0,'0','','Extend 1 default content...',0,'0',0,'0');

/*!40000 ALTER TABLE `extend_field` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table extend_field_lang
# ------------------------------------------------------------

DROP TABLE IF EXISTS `extend_field_lang`;

CREATE TABLE `extend_field_lang` (
  `id_extend_field` int(11) unsigned NOT NULL,
  `lang` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_extend_field`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `extend_field_lang` WRITE;
/*!40000 ALTER TABLE `extend_field_lang` DISABLE KEYS */;

INSERT INTO `extend_field_lang` (`id_extend_field`, `lang`, `label`)
VALUES
	(1,'en','Extend 1');

/*!40000 ALTER TABLE `extend_field_lang` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table extend_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `extend_fields`;

CREATE TABLE `extend_fields` (
  `id_extend_fields` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_extend_field` int(11) unsigned NOT NULL,
  `parent` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `id_parent` int(11) unsigned NOT NULL,
  `lang` char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `content` text COLLATE utf8_unicode_ci,
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  `id_element` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_extend_fields`),
  KEY `idx_extend_fields_id_parent` (`id_parent`),
  KEY `idx_extend_fields_lang` (`lang`),
  KEY `idx_extend_fields_id_extend_field` (`id_extend_field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `extend_fields` WRITE;
/*!40000 ALTER TABLE `extend_fields` DISABLE KEYS */;

INSERT INTO `extend_fields` (`id_extend_fields`, `id_extend_field`, `parent`, `id_parent`, `lang`, `content`, `ordering`, `id_element`)
VALUES
	(1,1,'',20,'','Extend 1 content...',0,0),
	(2,1,'',30,'','Extend 1 Article 30 content...',0,0),
	(3,1,'',40,'','Extend 1 default content...',0,0),
	(4,1,'',41,'','Extend 1 default content...',0,0),
	(5,1,'',42,'','Extend 1 default content...',0,0),
	(6,1,'',43,'','Extend 1 default content...',0,0),
	(7,1,'',44,'','Extend 1 default content...',0,0),
	(8,1,'',45,'','Extend 1 default content...',0,0),
	(9,1,'',46,'','Extend 1 default content...',0,0),
	(10,1,'',50,'','Extend 1 default content...',0,0),
	(11,1,'',51,'','Extend 1 default content...',0,0),
	(12,1,'',52,'','Extend 1 default content...',0,0),
	(13,1,'',53,'','Extend 1 default content...',0,0);

/*!40000 ALTER TABLE `extend_fields` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ion_sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ion_sessions`;

CREATE TABLE `ion_sessions` (
  `session_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `user_agent` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table lang
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lang`;

CREATE TABLE `lang` (
  `lang` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `online` char(1) COLLATE utf8_unicode_ci DEFAULT '0',
  `def` char(1) COLLATE utf8_unicode_ci DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `lang` WRITE;
/*!40000 ALTER TABLE `lang` DISABLE KEYS */;

INSERT INTO `lang` (`lang`, `name`, `online`, `def`, `ordering`)
VALUES
	('en','english','1','1',1),
	('fr','Français','1','0',2);

/*!40000 ALTER TABLE `lang` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table login_tracker
# ------------------------------------------------------------

DROP TABLE IF EXISTS `login_tracker`;

CREATE TABLE `login_tracker` (
  `ip_address` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `first_time` int(11) unsigned NOT NULL,
  `failures` tinyint(2) unsigned DEFAULT NULL,
  PRIMARY KEY (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table media
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media`;

CREATE TABLE `media` (
  `id_media` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'file_name',
  `path` varchar(500) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Complete path to the medium, including media file name, excluding host name',
  `base_path` varchar(500) COLLATE utf8_unicode_ci NOT NULL COMMENT 'medium folder base path, excluding host name',
  `copyright` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `container` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `date` datetime NOT NULL COMMENT 'Medium date',
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Link to a resource, attached to this medium',
  `square_crop` enum('tl','m','br') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'm',
  PRIMARY KEY (`id_media`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;

INSERT INTO `media` (`id_media`, `type`, `file_name`, `path`, `base_path`, `copyright`, `container`, `date`, `link`, `square_crop`)
VALUES
	(1,'picture','IMG_8359.jpg','files/pictures/IMG_8359.jpg','files/pictures/','','','0000-00-00 00:00:00','','m'),
	(2,'picture','IMG_8447.jpg','files/pictures/IMG_8447.jpg','files/pictures/','','','0000-00-00 00:00:00','','m'),
	(3,'picture','IMG_8632.jpg','files/pictures/IMG_8632.jpg','files/pictures/',NULL,'','0000-00-00 00:00:00',NULL,'m'),
	(4,'picture','IMG_8963.jpg','files/pictures/IMG_8963.jpg','files/pictures/','','','0000-00-00 00:00:00','','m'),
	(5,'picture','IMG_8645.jpg','files/pictures/IMG_8645.jpg','files/pictures/',NULL,'','0000-00-00 00:00:00',NULL,'m'),
	(6,'video','wall_e_headphones_vignette.mp4','files/wall_e_headphones_vignette.mp4','files/',NULL,'','0000-00-00 00:00:00',NULL,'m'),
	(7,'video','watch?v=d4RiUy23e9s','http://www.youtube.com/watch?v=d4RiUy23e9s','http://www.youtube.com/',NULL,'','0000-00-00 00:00:00',NULL,'m');

/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table media_lang
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_lang`;

CREATE TABLE `media_lang` (
  `lang` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `id_media` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alt` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id_media`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `media_lang` WRITE;
/*!40000 ALTER TABLE `media_lang` DISABLE KEYS */;

INSERT INTO `media_lang` (`lang`, `id_media`, `title`, `alt`, `description`)
VALUES
	('en',1,'One burman munk','',''),
	('fr',1,'Moine birman','',''),
	('en',2,'hop','',''),
	('fr',2,'','',''),
	('en',4,'L&quot;elephant','',''),
	('fr',4,'','',''),
	('en',6,'Local Video From Page','','MP4 File Format'),
	('en',7,'Video From Youtube','','Video added from Page - Videos - Add Video Url section');

/*!40000 ALTER TABLE `media_lang` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `menu`;

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_menu`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;

INSERT INTO `menu` (`id_menu`, `name`, `title`, `ordering`)
VALUES
	(1,'main','Main menu',NULL),
	(2,'system','System menu',NULL);

/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table module
# ------------------------------------------------------------

DROP TABLE IF EXISTS `module`;

CREATE TABLE `module` (
  `id_module` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `with_admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `version` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ordering` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `info` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_module`),
  KEY `i_module_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table module_setting
# ------------------------------------------------------------

DROP TABLE IF EXISTS `module_setting`;

CREATE TABLE `module_setting` (
  `id_module_setting` int(11) NOT NULL AUTO_INCREMENT,
  `id_module` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Setting name',
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Setting content',
  `lang` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_module_setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table note
# ------------------------------------------------------------

DROP TABLE IF EXISTS `note`;

CREATE TABLE `note` (
  `id_note` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_note`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table page
# ------------------------------------------------------------

DROP TABLE IF EXISTS `page`;

CREATE TABLE `page` (
  `id_page` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) unsigned NOT NULL DEFAULT '0',
  `id_menu` int(11) unsigned NOT NULL DEFAULT '0',
  `id_type` smallint(2) NOT NULL DEFAULT '0',
  `id_subnav` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ordering` int(11) unsigned DEFAULT '0',
  `level` int(11) unsigned NOT NULL DEFAULT '0',
  `online` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `home` tinyint(1) NOT NULL DEFAULT '0',
  `author` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updater` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `publish_on` datetime NOT NULL,
  `publish_off` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `logical_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `appears` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `has_url` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `view` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Page view',
  `view_single` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Single Article Page view',
  `article_list_view` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Article list view for each article linked to this page',
  `article_view` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Article detail view for each article linked to this page',
  `article_order` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ordering' COMMENT 'Article order in this page. Can be "ordering", "date"',
  `article_order_direction` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ASC',
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Link to internal / external resource',
  `link_type` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '''page'', ''article'' or NULL',
  `link_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pagination` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Pagination use ?',
  `pagination_nb` tinyint(1) unsigned NOT NULL DEFAULT '5' COMMENT 'Article number per page',
  `id_group` smallint(4) unsigned NOT NULL,
  `priority` int(1) unsigned NOT NULL DEFAULT '5' COMMENT 'Page priority',
  `used_by_module` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_page`),
  KEY `idx_page_id_parent` (`id_parent`),
  KEY `idx_page_level` (`level`),
  KEY `idx_page_menu` (`id_menu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `page` WRITE;
/*!40000 ALTER TABLE `page` DISABLE KEYS */;

INSERT INTO `page` (`id_page`, `id_parent`, `id_menu`, `id_type`, `id_subnav`, `name`, `ordering`, `level`, `online`, `home`, `author`, `updater`, `created`, `publish_on`, `publish_off`, `updated`, `logical_date`, `appears`, `has_url`, `view`, `view_single`, `article_list_view`, `article_view`, `article_order`, `article_order_direction`, `link`, `link_type`, `link_id`, `pagination`, `pagination_nb`, `id_group`, `priority`, `used_by_module`)
VALUES
	(1,0,2,0,0,'404',0,0,1,0,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',0,1,NULL,NULL,NULL,NULL,'ordering','ASC','',NULL,'',0,5,0,5,NULL),
	(2,0,1,0,0,'welcome',0,0,1,1,NULL,'admin','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-10 18:27:00','0000-00-00 00:00:00',1,1,'','',NULL,'','ordering','ASC','',NULL,'',0,5,0,5,0),
	(3,4,1,0,0,'test-page',1,1,1,0,'admin','admin','2012-09-04 12:45:10','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-10 16:47:02','0000-00-00 00:00:00',1,1,'','',NULL,'','ordering','ASC','',NULL,'',3,5,0,5,0),
	(4,0,1,0,0,'thats-my-page',2,0,1,0,'admin','admin','2012-09-08 10:06:20','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-10 18:27:24','0000-00-00 00:00:00',1,1,'','',NULL,'','ordering','ASC','',NULL,'',0,5,0,5,0),
	(5,0,1,0,0,'play-with-views',3,0,1,0,'admin','admin','2012-09-21 08:06:44','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-21 10:43:01','0000-00-00 00:00:00',1,1,'page_blog_view','page_blog_view_post',NULL,'','ordering','ASC','',NULL,'',0,5,0,5,0);

/*!40000 ALTER TABLE `page` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table page_article
# ------------------------------------------------------------

DROP TABLE IF EXISTS `page_article`;

CREATE TABLE `page_article` (
  `id_page` int(11) unsigned NOT NULL,
  `id_article` int(11) unsigned NOT NULL,
  `online` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `view` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ordering` int(11) DEFAULT '0',
  `id_type` int(11) unsigned DEFAULT NULL,
  `link_type` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `main_parent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_page`,`id_article`),
  KEY `idx_page_article_id_type` (`id_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `page_article` WRITE;
/*!40000 ALTER TABLE `page_article` DISABLE KEYS */;

INSERT INTO `page_article` (`id_page`, `id_article`, `online`, `view`, `ordering`, `id_type`, `link_type`, `link_id`, `link`, `main_parent`)
VALUES
	(1,10,1,NULL,0,NULL,'','','',1),
	(2,20,1,NULL,1,NULL,'','','',1),
	(2,41,1,NULL,2,NULL,'','','',1),
	(3,43,1,'0',1,0,'','','',1),
	(3,44,1,'0',2,0,'','','',1),
	(3,45,1,'0',3,0,'','','',1),
	(4,30,1,NULL,1,NULL,'','','',1),
	(4,40,1,NULL,2,NULL,'','','',1),
	(4,42,1,NULL,3,NULL,'','','',1),
	(5,46,1,'0',1,0,'','','',1),
	(5,50,1,'0',2,0,'','','',1),
	(5,51,1,'0',3,0,'','','',1),
	(5,52,1,'0',4,0,'','','',1),
	(5,53,1,'0',5,0,'','','',1);

/*!40000 ALTER TABLE `page_article` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table page_lang
# ------------------------------------------------------------

DROP TABLE IF EXISTS `page_lang`;

CREATE TABLE `page_lang` (
  `lang` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `id_page` int(11) unsigned NOT NULL DEFAULT '0',
  `url` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subtitle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nav_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `subnav_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `meta_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `online` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_page`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `page_lang` WRITE;
/*!40000 ALTER TABLE `page_lang` DISABLE KEYS */;

INSERT INTO `page_lang` (`lang`, `id_page`, `url`, `link`, `title`, `subtitle`, `nav_title`, `subnav_title`, `meta_title`, `meta_description`, `meta_keywords`, `online`)
VALUES
	('en',1,'404','','404',NULL,'','',NULL,NULL,NULL,1),
	('fr',1,'404','',NULL,NULL,'','',NULL,NULL,NULL,1),
	('en',2,'welcome-url','','Welcome','','','','','','',1),
	('fr',2,'welcome-url','','Bienvenue','','','','','','',1),
	('en',3,'test-page','','Test page','','','','Window Title Page 3...','English Description Page 3....','Keyword Page 3',1),
	('fr',3,'page-de-test','','Page de test','','','','Titre fenêtre Page 3','Description française page 3...','Mot clé Page 3',1),
	('en',4,'thats-my-page','','That\'s my page','','','','','','',1),
	('fr',4,'thats-my-page','','Ma page','','','','','','',1),
	('en',5,'play-with-views','','Play with views','','','','','','',1),
	('fr',5,'page-avec-vues','','Page avec vues','','','','','','',1);

/*!40000 ALTER TABLE `page_lang` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table page_media
# ------------------------------------------------------------

DROP TABLE IF EXISTS `page_media`;

CREATE TABLE `page_media` (
  `id_page` int(11) unsigned NOT NULL DEFAULT '0',
  `id_media` int(11) unsigned NOT NULL DEFAULT '0',
  `online` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ordering` int(11) unsigned DEFAULT '9999',
  `lang_display` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_page`,`id_media`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `page_media` WRITE;
/*!40000 ALTER TABLE `page_media` DISABLE KEYS */;

INSERT INTO `page_media` (`id_page`, `id_media`, `online`, `ordering`, `lang_display`)
VALUES
	(2,1,1,1,NULL),
	(2,2,1,2,NULL),
	(2,3,1,3,NULL),
	(2,4,1,5,NULL),
	(2,5,1,4,NULL),
	(2,6,1,2,NULL),
	(2,7,1,1,NULL),
	(3,1,1,2,NULL),
	(3,2,1,3,NULL),
	(3,3,1,1,NULL);

/*!40000 ALTER TABLE `page_media` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table page_user_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `page_user_groups`;

CREATE TABLE `page_user_groups` (
  `id_page` int(11) unsigned NOT NULL DEFAULT '0',
  `ig_group` smallint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_page`,`ig_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table setting
# ------------------------------------------------------------

DROP TABLE IF EXISTS `setting`;

CREATE TABLE `setting` (
  `id_setting` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `lang` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id_setting`),
  UNIQUE KEY `idx_unq_setting` (`name`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `setting` WRITE;
/*!40000 ALTER TABLE `setting` DISABLE KEYS */;

INSERT INTO `setting` (`id_setting`, `name`, `content`, `lang`)
VALUES
	(1,'website_email','',''),
	(2,'files_path','files',''),
	(3,'theme','ftl',''),
	(4,'theme_admin','admin',''),
	(5,'google_analytics','',''),
	(6,'filemanager','mootools-filemanager',''),
	(7,'show_help_tips','1',''),
	(8,'display_connected_label','',''),
	(9,'texteditor','tinymce',''),
	(10,'media_thumb_size','120',''),
	(11,'tinybuttons1','pdw_toggle,|,bold,italic,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,|,bullist,numlist,|,link,unlink,image,|,spellchecker',''),
	(12,'tinybuttons2','fullscreen, undo,redo,|,pastetext,selectall,removeformat,|,media,charmap,hr,blockquote,|,template,|,codemirror',''),
	(13,'tinybuttons3','tablecontrols',''),
	(14,'displayed_admin_languages','en',''),
	(15,'date_format','%Y.%m.%d',''),
	(16,'force_lang_urls','0',''),
	(17,'tinyblockformats','p,h2,h3,h4,h5,pre,div',''),
	(18,'article_allowed_tags','h2,h3,h4,h5,h6,em,img,iframe,table,object,thead,tbody,tfoot,tr,th,td,param,embed,map,p,a,ul,ol,li,br,b,strong',''),
	(19,'filemanager_file_types','gif,jpe,jpeg,jpg,png,flv,mpeg,mpg,mp3,pdf',''),
	(20,'default_admin_lang','en',''),
	(21,'ionize_version','0.9.8',''),
	(22,'media_upload_mode','single',''),
	(23,'no_source_picture','default.png',''),
	(24,'site_title','My website','en'),
	(25,'meta_keywords','keyword 1, keyword2','en'),
	(26,'meta_description','Website description','en'),
	(27,'meta_keywords','mot clé 1, mot clé 2','fr'),
	(28,'meta_description','Description du site','fr'),
	(29,'site_title','Mon site web','fr');

/*!40000 ALTER TABLE `setting` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tag
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tag`;

CREATE TABLE `tag` (
  `id_tag` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table type
# ------------------------------------------------------------

DROP TABLE IF EXISTS `type`;

CREATE TABLE `type` (
  `id_type` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `parent` char(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(3000) DEFAULT NULL,
  `ordering` smallint(6) NOT NULL,
  `view` varchar(50) DEFAULT NULL COMMENT 'view',
  `flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table url
# ------------------------------------------------------------

DROP TABLE IF EXISTS `url`;

CREATE TABLE `url` (
  `id_url` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_entity` int(11) unsigned NOT NULL,
  `type` varchar(10) NOT NULL,
  `canonical` smallint(1) NOT NULL DEFAULT '0',
  `active` smallint(1) NOT NULL DEFAULT '0',
  `lang` varchar(3) NOT NULL,
  `path` varchar(255) NOT NULL DEFAULT '',
  `path_ids` varchar(50) DEFAULT NULL,
  `full_path_ids` varchar(50) DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`id_url`),
  KEY `idx_url_type` (`type`),
  KEY `idx_url_active` (`active`),
  KEY `idx_url_lang` (`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `url` WRITE;
/*!40000 ALTER TABLE `url` DISABLE KEYS */;

INSERT INTO `url` (`id_url`, `id_entity`, `type`, `canonical`, `active`, `lang`, `path`, `path_ids`, `full_path_ids`, `creation_date`)
VALUES
	(33,1,'page',1,1,'en','404','1','1','2012-09-06 13:21:22'),
	(34,1,'page',1,1,'fr','404','1','1','2012-09-06 13:21:22'),
	(35,10,'article',1,1,'en','404/404','1/10','1/10','2012-09-06 13:21:22'),
	(36,10,'article',1,1,'fr','404/404','1/10','1/10','2012-09-06 13:21:22'),
	(249,2,'page',1,1,'en','welcome-url','2','2','2012-09-10 18:27:00'),
	(250,2,'page',1,1,'fr','welcome-url','2','2','2012-09-10 18:27:00'),
	(251,20,'article',1,1,'en','welcome-url/welcome-article-url','2/20','2/20','2012-09-10 18:27:00'),
	(252,20,'article',1,1,'fr','welcome-url/welcome-article-url','2/20','2/20','2012-09-10 18:27:00'),
	(253,41,'article',1,1,'en','welcome-url/iframe-in-content','2/41','2/41','2012-09-10 18:27:00'),
	(254,41,'article',1,1,'fr','welcome-url/iframe-in-content','2/41','2/41','2012-09-10 18:27:00'),
	(255,4,'page',1,1,'en','thats-my-page','4','4','2012-09-10 18:27:23'),
	(256,4,'page',1,1,'fr','thats-my-page','4','4','2012-09-10 18:27:23'),
	(257,3,'page',1,1,'en','thats-my-page/test-page','4/3','4/3','2012-09-10 18:27:23'),
	(258,3,'page',1,1,'fr','thats-my-page/page-de-test','4/3','4/3','2012-09-10 18:27:24'),
	(259,30,'article',1,0,'en','thats-my-page/test-page/article-30','4/3/30','4/3/30','2012-09-10 18:27:24'),
	(260,30,'article',1,0,'fr','thats-my-page/page-de-test/article-30','4/3/30','4/3/30','2012-09-10 18:27:24'),
	(261,40,'article',1,0,'en','thats-my-page/test-page/article-40','4/3/40','4/3/40','2012-09-10 18:27:24'),
	(262,40,'article',1,0,'fr','thats-my-page/page-de-test/article-40','4/3/40','4/3/40','2012-09-10 18:27:24'),
	(263,42,'article',1,0,'en','thats-my-page/test-page/article-50','4/3/42','4/3/42','2012-09-10 18:27:24'),
	(264,42,'article',1,0,'fr','thats-my-page/page-de-test/article-50','4/3/42','4/3/42','2012-09-10 18:27:24'),
	(265,43,'article',1,1,'en','thats-my-page/test-page/article-60','4/3/43','4/3/43','2012-09-10 18:27:24'),
	(266,43,'article',1,1,'fr','thats-my-page/page-de-test/article-60','4/3/43','4/3/43','2012-09-10 18:27:24'),
	(267,44,'article',1,1,'en','thats-my-page/test-page/article-70','4/3/44','4/3/44','2012-09-10 18:27:24'),
	(268,44,'article',1,1,'fr','thats-my-page/page-de-test/article-70','4/3/44','4/3/44','2012-09-10 18:27:24'),
	(269,45,'article',1,1,'en','thats-my-page/test-page/article-80','4/3/45','4/3/45','2012-09-10 18:27:24'),
	(270,45,'article',1,1,'fr','thats-my-page/page-de-test/article-80','4/3/45','4/3/45','2012-09-10 18:27:24'),
	(289,5,'page',1,1,'en','play-with-views','5','5','2012-09-21 10:43:01'),
	(290,5,'page',1,1,'fr','page-avec-vues','5','5','2012-09-21 10:43:01'),
	(291,46,'article',1,1,'en','play-with-views/post-1','5/46','5/46','2012-09-21 10:43:01'),
	(292,46,'article',1,1,'fr','page-avec-vues/post-1','5/46','5/46','2012-09-21 10:43:01'),
	(293,50,'article',1,1,'en','play-with-views/post-2','5/50','5/50','2012-09-21 10:43:01'),
	(294,50,'article',1,1,'fr','page-avec-vues/post-2','5/50','5/50','2012-09-21 10:43:01'),
	(295,51,'article',1,1,'en','play-with-views/post-3','5/51','5/51','2012-09-21 10:43:01'),
	(296,51,'article',1,1,'fr','page-avec-vues/post-3','5/51','5/51','2012-09-21 10:43:01'),
	(297,52,'article',1,1,'en','play-with-views/post-4','5/52','5/52','2012-09-21 10:43:01'),
	(298,52,'article',1,1,'fr','page-avec-vues/post-4','5/52','5/52','2012-09-21 10:43:01'),
	(299,53,'article',1,1,'en','play-with-views/post-5','5/53','5/53','2012-09-21 10:43:01'),
	(300,53,'article',1,1,'fr','page-avec-vues/post-5','5/53','5/53','2012-09-21 10:43:01'),
	(305,30,'article',1,1,'en','thats-my-page/article-30','4/30','4/30','2012-09-21 13:24:20'),
	(306,30,'article',1,1,'fr','thats-my-page/article-30','4/30','4/30','2012-09-21 13:24:20'),
	(307,40,'article',1,1,'en','thats-my-page/article-40','4/40','4/40','2012-09-21 13:24:23'),
	(308,40,'article',1,1,'fr','thats-my-page/article-40','4/40','4/40','2012-09-21 13:24:23'),
	(309,42,'article',1,1,'en','thats-my-page/article-50','4/42','4/42','2012-09-21 13:24:26'),
	(310,42,'article',1,1,'fr','thats-my-page/article-50','4/42','4/42','2012-09-21 13:24:26');

/*!40000 ALTER TABLE `url` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_groups`;

CREATE TABLE `user_groups` (
  `id_group` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `level` int(11) DEFAULT NULL,
  `slug` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `group_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` tinytext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id_group`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `user_groups` WRITE;
/*!40000 ALTER TABLE `user_groups` DISABLE KEYS */;

INSERT INTO `user_groups` (`id_group`, `level`, `slug`, `group_name`, `description`)
VALUES
	(1,10000,'super-admins','Super Admins',''),
	(2,5000,'admins','Admins',''),
	(3,1000,'editors','Editors',''),
	(4,100,'users','Users',''),
	(5,50,'pending','Pending',''),
	(6,10,'guests','Guests',''),
	(7,-10,'banned','Banned',''),
	(8,-100,'deactivated','Deactivated','');

/*!40000 ALTER TABLE `user_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id_user` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_group` smallint(4) unsigned NOT NULL,
  `join_date` timestamp NULL DEFAULT NULL,
  `last_visit` timestamp NULL DEFAULT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `screen_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birth_date` datetime NOT NULL,
  `gender` smallint(1) DEFAULT NULL COMMENT '1: Male, 2 : Female',
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  KEY `id_group` (`id_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id_user`, `id_group`, `join_date`, `last_visit`, `username`, `screen_name`, `firstname`, `lastname`, `birth_date`, `gender`, `password`, `email`, `salt`)
VALUES
	(1,1,'2012-09-04 11:45:56','2012-09-08 09:23:49','admin','Admin','',NULL,'0000-00-00 00:00:00',NULL,'OKg52nwiollKAlW7CQ==','admin@partikule.net','e404e23654682ad0');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users_meta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users_meta`;

CREATE TABLE `users_meta` (
  `id_user` int(11) unsigned NOT NULL,
  `newsletter` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
