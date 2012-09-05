# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.9)
# Database: ionize_ftl
# Generation Time: 2012-09-05 10:33:38 +0000
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
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
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
  PRIMARY KEY (`id_article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `article` WRITE;
/*!40000 ALTER TABLE `article` DISABLE KEYS */;

INSERT INTO `article` (`id_article`, `name`, `author`, `updater`, `created`, `publish_on`, `publish_off`, `updated`, `logical_date`, `indexed`, `id_category`, `comment_allow`, `comment_autovalid`, `comment_expire`, `flag`, `has_url`)
VALUES
	(10,'404',NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',0,NULL,'0','0',NULL,0,1),
	(20,'welcome','','admin','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-05 13:28:23','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(30,'article-1','admin','admin','2012-09-04 12:45:34','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-05 12:00:30','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1),
	(40,'article-2','admin','admin','2012-09-04 12:45:44','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-05 10:47:58','0000-00-00 00:00:00',0,NULL,'0','0','0000-00-00 00:00:00',0,1);

/*!40000 ALTER TABLE `article` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table article_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `article_category`;

CREATE TABLE `article_category` (
  `id_article` int(11) unsigned NOT NULL,
  `id_category` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



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
	(20,'en','welcome-article-url','Title 20...','','','','<p>Content 20...</p>',NULL,NULL,1),
	(20,'fr','welcome-article-url',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),
	(30,'en','article-30','Title 30...','','','','<p>Content 30...</p>',NULL,NULL,1),
	(30,'fr','article-30',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),
	(40,'en','article-40','Title 40...','','','','<p>Content 40 ...</p>',NULL,NULL,1),
	(40,'fr','article-40',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1);

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



# Dump of table element_definition_lang
# ------------------------------------------------------------

DROP TABLE IF EXISTS `element_definition_lang`;

CREATE TABLE `element_definition_lang` (
  `id_element_definition` int(11) unsigned NOT NULL,
  `lang` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id_element_definition`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



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
	(2,1,'',30,'','Extend 1 Article 30 content...',0,0);

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
	(2,0,1,0,0,'welcome',0,0,1,1,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,1,NULL,NULL,NULL,NULL,'ordering','ASC','',NULL,'',0,5,0,5,NULL),
	(3,0,1,0,0,'test-page',1,0,1,0,'admin','admin','2012-09-04 12:45:10','0000-00-00 00:00:00','0000-00-00 00:00:00','2012-09-05 17:20:35','0000-00-00 00:00:00',1,0,'','',NULL,'','ordering','ASC','',NULL,'',0,5,0,5,0);

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
	(1,10,1,NULL,0,NULL,'','','',0),
	(2,20,1,NULL,0,NULL,'','','',0),
	(3,30,1,NULL,1,NULL,'','','',1),
	(3,40,1,NULL,2,NULL,'','','',1);

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
	('en',2,'welcome-url','','Welcome',NULL,'','',NULL,NULL,NULL,1),
	('fr',2,'welcome-url','',NULL,NULL,'','',NULL,NULL,NULL,1),
	('en',3,'test-page','','Test page','','','','','','',1),
	('fr',3,'test-page','','','','','','','','',1);

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
	(24,'site_title','My website','en');

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
	(9,3,'page',1,1,'en','test-page','3','3','2012-09-05 17:20:35'),
	(10,3,'page',1,1,'fr','test-page','3','3','2012-09-05 17:20:35'),
	(11,30,'article',1,1,'en','test-page/article-30','3/30','3/30','2012-09-05 17:20:35'),
	(12,30,'article',1,1,'fr','test-page/article-30','3/30','3/30','2012-09-05 17:20:35'),
	(13,40,'article',1,1,'en','test-page/article-40','3/40','3/40','2012-09-05 17:20:35'),
	(14,40,'article',1,1,'fr','test-page/article-40','3/40','3/40','2012-09-05 17:20:35');

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
	(1,1,'2012-09-04 11:45:56','2012-09-04 11:46:23','admin','Admin','',NULL,'0000-00-00 00:00:00',NULL,'OKg52nwiollKAlW7CQ==','admin@partikule.net','e404e23654682ad0');

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
