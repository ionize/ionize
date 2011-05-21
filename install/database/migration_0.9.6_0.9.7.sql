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
	
	
		ALTER TABLE `article_type` ADD `description` TEXT NOT NULL, ADD `type_flag` TINYINT(1) NOT NULL DEFAULT 0;
	

	
		ALTER TABLE `media` ADD `container` VARCHAR( 255 ) NOT NULL DEFAULT '';
	
	
	
		ALTER TABLE `page_article` ADD `link_type` VARCHAR( 25 ) NOT NULL DEFAULT '';
	
	
	
		ALTER TABLE `page_article` ADD `link_id` varchar(20) NOT NULL DEFAULT '';
	
	
		ALTER TABLE `page_article` ADD `link` VARCHAR( 255 ) NOT NULL DEFAULT '';	
	
	
		ALTER TABLE  `page_article` ADD  `main_parent` TINYINT( 1 ) NOT NULL DEFAULT 0;
	
	
		UPDATE page_article, article SET page_article.link = article.link WHERE page_article.id_article = article.id_article AND article.link_type != 'external';
	

	
		UPDATE page_article, article SET page_article.link_id = article.link_id WHERE page_article.id_article = article.id_article AND article.link_type != 'external';
	

	
		UPDATE page_article, article SET page_article.link_type = article.link_type WHERE page_article.id_article = article.id_article AND article.link_type != 'external';
	

	
		update page_article, page_lang, lang set page_article.link = page_lang.title
			where page_lang.id_page = page_article.link_id
			and page_lang.lang = lang.lang
			and lang.def='1'
			and page_article.`link_type` = 'page';
	

	
		update page_article, article_lang, lang set page_article.link = article_lang.title
			where article_lang.id_article = page_article.link_id
			and article_lang.lang = lang.lang
			and lang.def='1'
			and page_article.`link_type` = 'article';
	

		ALTER TABLE  `page` ADD  `id_subnav` INT( 11 ) NOT NULL DEFAULT 0;

		ALTER TABLE  `page_lang` ADD  `subnav_title` VARCHAR( 255 ) NOT NULL DEFAULT  '';

		update page set id_subnav = id_page;
	
		ALTER TABLE `article` DROP `link`, DROP `link_type`, DROP `link_id`, ADD logical_date datetime NOT NULL default '0000-00-00 00:00:00';
	
		ALTER TABLE `article_lang` DROP `link`;
	
		ALTER TABLE `page` CHANGE `link_id` `link_id` VARCHAR( 20 ) NOT NULL  DEFAULT '', ADD logical_date datetime NOT NULL default '0000-00-00 00:00:00';
	
		UPDATE page SET link_id='' WHERE link_id = '0';
	
		ALTER TABLE `article_type` ADD `type_flag` TINYINT( 1 ) NOT NULL DEFAULT 0;
	
		ALTER TABLE `category_lang` ADD `subtitle` VARCHAR( 255 ) NOT NULL ;
	
		DELETE FROM setting WHERE name='tinyblockformats';
	
		INSERT INTO setting VALUES ('', 'tinyblockformats', 'p,h2,h3,h4,h5,pre', '');
	
		DELETE FROM setting WHERE name='default_admin_lang';
	
		INSERT INTO setting VALUES ('', 'default_admin_lang', 'en', '');



	ALTER TABLE `article` COMMENT = '0.9.7';
	ALTER TABLE `article_category` COMMENT = '0.9.7';
	ALTER TABLE `article_comment` COMMENT = '0.9.7';
	ALTER TABLE `article_langv COMMENT = '0.9.7';
	ALTER TABLE `article_media` COMMENT = '0.9.7';
	ALTER TABLE `article_tag` COMMENT = '0.9.7';
	ALTER TABLE `article_type` COMMENT = '0.9.7';
	ALTER TABLE `captcha` COMMENT = '0.9.7';
	ALTER TABLE `category` COMMENT = '0.9.7';
	ALTER TABLE `category_lang` COMMENT = '0.9.7';
	ALTER TABLE `element` COMMENT = '0.9.7';
	ALTER TABLE `element_definition` COMMENT = '0.9.7';
	ALTER TABLE `element_definition_lang` COMMENT = '0.9.7';
	ALTER TABLE `extend_field` COMMENT = '0.9.7';
	ALTER TABLE `extend_fields` COMMENT = '0.9.7';
	ALTER TABLE `ion_sessions` COMMENT = '0.9.7';
	ALTER TABLE `lang` COMMENT = '0.9.7';
	ALTER TABLE `login_tracker` COMMENT = '0.9.7';
	ALTER TABLE `media` COMMENT = '0.9.7';
	ALTER TABLE `media_lang` COMMENT = '0.9.7';
	ALTER TABLE `menu` COMMENT = '0.9.7';
	ALTER TABLE `module` COMMENT = '0.9.7';
	ALTER TABLE `module_setting` COMMENT = '0.9.7';
	ALTER TABLE `note` COMMENT = '0.9.7';
	ALTER TABLE `page` COMMENT = '0.9.7';
	ALTER TABLE `page_article` COMMENT = '0.9.7';
	ALTER TABLE `page_lang` COMMENT = '0.9.7';
	ALTER TABLE `page_media` COMMENT = '0.9.7';
	ALTER TABLE `page_user_groups` COMMENT = '0.9.7';
	ALTER TABLE `setting` COMMENT = '0.9.7';
	ALTER TABLE `tag` COMMENT = '0.9.7';
	ALTER TABLE `user_groups` COMMENT = '0.9.7';
	ALTER TABLE `users` COMMENT = '0.9.7';
	ALTER TABLE `users_meta COMMENT = '0.9.7';




