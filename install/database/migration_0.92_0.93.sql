--
-- Ionize 0.93 database migration
-- From 0.92 to 0.93
-- 

-- Add fields to extend_field
ALTER TABLE extend_field ADD `value` varchar(3000) NULL;
ALTER TABLE extend_field ADD `default_value` varchar(255) NULL;

-- Replace subtitle1 by subtitle
ALTER TABLE `article_lang` CHANGE `subtitle1` `subtitle` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `page_lang` CHANGE `subtitle1` `subtitle` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ;

-- Replace subtitle2 by meta_title
ALTER TABLE `article_lang` CHANGE `subtitle2` `meta_title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `page_lang` CHANGE `subtitle2` `meta_title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ;

