--
-- Ionize 0.9.4 database migration
-- From 0.93 to 0.9.4
-- 


-- Ionize session table modification
ALTER TABLE ion_sessions CHANGE user_data user_data TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ;
ALTER TABLE ion_sessions CHANGE user_agent user_agent TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ;

-- Media lang
ALTER TABLE media_lang CHANGE alt alt VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

-- Extended fields modifications
ALTER TABLE extend_field CHANGE `value` `value` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE extend_field CHANGE description description VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;

-- User table modification
ALTER TABLE users CHANGE username username VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE users ADD salt VARCHAR(50) NOT NULL;

