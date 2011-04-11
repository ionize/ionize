--
-- Ionize 0.9.7 database migration
-- From 0.9.6 to 0.9.7
--


CREATE TABLE IF NOT EXISTS element (
  id_element int(11) unsigned NOT NULL auto_increment,
  id_element_definition int(11) unsigned NOT NULL,
  parent varchar(50) NOT NULL,
  id_parent int(11) NOT NULL,
  ordering int(11) NOT NULL default '1',
  PRIMARY KEY  (id_element)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

	
CREATE TABLE IF NOT EXISTS element_definition (
  id_element_definition int(11) unsigned NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  description text,
  ordering int(11) not null default 0,
  PRIMARY KEY  (id_element_definition)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS element_definition_lang (
  id_element_definition int(11) unsigned NOT NULL,
  lang varchar(3) NOT NULL,
  title varchar(255) NOT NULL default '',
  PRIMARY KEY  (id_element_definition, lang)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `extend_field` 
	ADD `global` tinyint( 1 ) UNSIGNED NOT NULL DEFAULT '0',
	ADD `id_element_definition` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0',
	ADD `block` VARCHAR( 50 ) NOT NULL DEFAULT '';

ALTER TABLE `extend_fields` 
	ADD `ordering` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0',
	ADD `id_element` INT( 11 ) UNSIGNED NOT NULL;

ALTER TABLE `article_type` ADD `description` TEXT NOT NULL;

ALTER TABLE `media` ADD `container` VARCHAR( 255 ) NOT NULL DEFAULT '';

ALTER TABLE `page_article` ADD `link_type` VARCHAR( 25 ) NOT NULL DEFAULT '';

ALTER TABLE `page_article` ADD `link_id` varchar(20) NOT NULL DEFAULT '';

ALTER TABLE `page_article` ADD `link` VARCHAR( 255 ) NOT NULL DEFAULT '';	

UPDATE page_article, article SET page_article.link=article.link WHERE page_article.id_article = article.id_article;

UPDATE page_article, article SET page_article.link_id=article.link_id WHERE page_article.id_article = article.id_article;

UPDATE page_article, article SET page_article.link_type=article.link_type WHERE page_article.id_article = article.id_article;

ALTER TABLE `article` 
	DROP `link`, 
	DROP `link_type`, 
	DROP `link_id`,
	ADD logical_date datetime NOT NULL default '0000-00-00 00:00:00';

ALTER TABLE `article_lang` DROP `link`;

ALTER TABLE `page` CHANGE `link_id` `link_id` VARCHAR( 20 ) NOT NULL DEFAULT '', ADD logical_date datetime NOT NULL default '0000-00-00 00:00:00';

UPDATE page SET link_id='' WHERE link_id = '0';
	
ALTER TABLE `article_type` ADD `type_flag` TINYINT( 1 ) NOT NULL DEFAULT 0;

ALTER TABLE `category_lang` ADD `subtitle` VARCHAR( 255 ) NOT NULL  default '';