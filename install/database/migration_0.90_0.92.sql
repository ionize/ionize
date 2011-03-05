--
-- Ionize 0.92 database migration
-- From 0.90 (or 0.91) to 0.92
-- 

ALTER TABLE page_lang ADD `online` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '1';

ALTER TABLE page ADD `group_FK` SMALLINT( 4 ) UNSIGNED NOT NULL;

ALTER TABLE article ADD `id_type` INT( 11 ) UNSIGNED NULL ;

CREATE TABLE IF NOT EXISTS article_type (
  id_type int(11) unsigned NOT NULL auto_increment,
  type varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (id_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS extend_field (
	id_extend_field INT(11) UNSIGNED NOT NULL auto_increment,
	name varchar(255) NOT NULL,
	label varchar(255) NOT NULL,
	type varchar(1) NOT NULL,
	description varchar(2000) DEFAULT '',
	parent varchar(50) NOT NULL,
  	ordering int(11) default 0,
	translated char(1) default '0',
	PRIMARY KEY  (id_extend_field),
	KEY i_extend_field_parent (parent)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci  AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS extend_fields (
	id_extend_fields INT(11) UNSIGNED NOT NULL auto_increment,
	id_extend_field INT(11) UNSIGNED NOT NULL,
	id_parent int(11) UNSIGNED NOT NULL,
	lang char(3) NOT NULL default '',
	content text,
	PRIMARY KEY  (id_extend_fields),
	KEY i_extend_fields_lang (lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci  AUTO_INCREMENT=1;
