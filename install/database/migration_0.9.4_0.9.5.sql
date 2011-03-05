--
-- Ionize 0.9.5 database migration
-- From 0.9.4 to 0.9.5
-- 

UPDATE page SET id_menu=1;
UPDATE settings SET content='0.9.5' where name='ionize_version';
ALTER TABLE `article` ADD `link_type` VARCHAR( 25 ) NULL COMMENT '''page'', ''article'' or NULL';
ALTER TABLE `article` ADD `link_id` BIGINT( 20 ) NULL ;
ALTER TABLE `article_lang` ADD `url` VARCHAR( 100 ) NOT NULL ;
ALTER TABLE `article_lang` ADD `link` VARCHAR( 255 ) NOT NULL  DEFAULT '';
ALTER TABLE `page` ADD `home` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `page` ADD `link_type` VARCHAR( 25 ) NULL COMMENT '''page'', ''article'' or NULL';
ALTER TABLE `page` ADD `link_id` BIGINT( 20 ) NULL ;
ALTER TABLE `page` CHANGE `group_FK` `id_group` SMALLINT( 4 ) UNSIGNED NOT NULL;	
ALTER TABLE `page_lang` ADD `url` VARCHAR( 100 ) NOT NULL ;
ALTER TABLE `page_lang` ADD `link` VARCHAR( 255 ) NOT NULL DEFAULT '';
update page_lang t1 set t1.url=(select t2.name from page t2 where t2.id_page = t1.id_page);
update article_lang t1 set t1.url=(select t2.name from article t2 where t2.id_article = t1.id_article);
ALTER TABLE `article_type` ADD `ordering` INT(11) default NULL;
ALTER TABLE `users` CHANGE `user_PK` `id_user` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `users` CHANGE `group_FK` `id_group` SMALLINT( 4 ) UNSIGNED NOT NULL;
ALTER TABLE `users` CHANGE `password` `password` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `users_meta` CHANGE `user_PK` `id_user` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE `user_groups` CHANGE `group_PK` `id_group` SMALLINT( 4 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `user_groups` CHANGE `title` `group_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `user_groups` CHANGE `group_FK` `id_group` SMALLINT( 4 ) UNSIGNED NOT NULL;
ALTER TABLE `login_tracker` CHANGE `ip_address_PK` `ip_address` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
	
	
update page as p
	inner join article as a on a.id_article = p.link_id
set p.link = a.name
where p.link_type = 'article'
and p.link != a.name;



update page_lang as pl
	inner join page as p on p.id_page = pl.id_page
	inner join article_lang as al on al.id_article = p.link_id
set pl.link = al.url
where p.link_type = 'article'
and pl.lang = al.lang;



update article as a1
	inner join article as a2 on a2.id_article = a1.link_id
set a1.link = a2.name
where a1.link_type = 'article'
and a1.link != a2.name;



update article_lang as al
	inner join article as a on a.id_article = al.id_article
	inner join article_lang as a2 on a2.id_article = a.link_id
set al.link = a2.url
where a.link_type = 'article'
and al.lang = a2.lang;



update page as p1
	inner join page as p2 on p2.id_page = p1.link_id
set p1.link = p2.name
where p1.link_type='page'
and p1.link != p2.name;	



update page_lang as pl
	inner join page as p on p.id_page = pl.id_page
	inner join page_lang as p2 on p2.id_page = p.link_id
set pl.link = p2.url
where p.link_type='page'
and pl.lang = p2.lang;



update article as a
	inner join page as p on p.id_page = a.link_id
set a.link = p.name
where a.link_type='page'
and a.link != p.name;



update article_lang as al
	inner join article as a on a.id_article = al.id_article
	inner join page_lang as p on p.id_page = a.link_id
set al.link = p.url
where a.link_type='page'
and al.lang = p.lang;
	

