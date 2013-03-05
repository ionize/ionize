--
-- Ionize 1.0.0 database migration
-- From 0.9.9 to 1.0.0
--

update setting set content='1.0.0' where name = 'ionize_version';



ALTER TABLE media ADD provider varchar(50) NOT NULL DEFAULT  '';



-- ALTER TABLE media ADD path_hash varchar(100) NOT NULL DEFAULT  '';



CREATE TABLE api_key (
    id int(11) NOT NULL AUTO_INCREMENT,
    `key` varchar(40) NOT NULL,
    `level` int(2) NOT NULL,
    ignore_limits tinyint(1) NOT NULL DEFAULT '0',
    is_private tinyint(1) NOT NULL DEFAULT '0',
    ip_addresses text,
    date_created datetime,
    PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



CREATE TABLE api_log (
    id int(11) NOT NULL AUTO_INCREMENT,
    uri varchar(255) NOT NULL,
    method varchar(6) NOT NULL,
    params text,
    api_key varchar(40) NOT NULL,
    date_log datetime DEFAULT NULL,
    ip_address varchar(45) NOT NULL,
    authorized tinyint(1) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



CREATE TABLE event_log (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    status varchar(50) DEFAULT NULL,
    message text,
    id_user int(11) DEFAULT NULL,
    email varchar(255) DEFAULT NULL,
    date_log datetime DEFAULT NULL,
    ip_address varchar(45) DEFAULT NULL,
    seen tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DELETE FROM setting WHERE name='media_upload_mode';</query>
INSERT IGNORE INTO setting VALUES ('', 'upload_autostart', '1', '');</query>
INSERT IGNORE INTO setting VALUES ('', 'resize_on_upload', '1', '');</query>
INSERT IGNORE INTO setting VALUES ('', 'picture_max_width', '1200', '');</query>
INSERT IGNORE INTO setting VALUES ('', 'picture_max_height', '1200', '');</query>
INSERT IGNORE INTO setting VALUES ('', 'upload_mode', '', '');</query>


ALTER database ionize_099 default CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE article CONVERT TO CHARACTER SET utf8;
ALTER TABLE article_comment CONVERT TO CHARACTER SET utf8;
ALTER TABLE article_lang CONVERT TO CHARACTER SET utf8;
ALTER TABLE article_media CONVERT TO CHARACTER SET utf8;
ALTER TABLE article_type CONVERT TO CHARACTER SET utf8;
ALTER TABLE captcha CONVERT TO CHARACTER SET utf8;
ALTER TABLE category CONVERT TO CHARACTER SET utf8;
ALTER TABLE category_lang CONVERT TO CHARACTER SET utf8;
ALTER TABLE element CONVERT TO CHARACTER SET utf8;
ALTER TABLE element CONVERT TO CHARACTER SET utf8;
ALTER TABLE element_definition CONVERT TO CHARACTER SET utf8;
ALTER TABLE element_definition_lang CONVERT TO CHARACTER SET utf8;
ALTER TABLE extend_field CONVERT TO CHARACTER SET utf8;
ALTER TABLE extend_field_lang CONVERT TO CHARACTER SET utf8;
ALTER TABLE extend_fields CONVERT TO CHARACTER SET utf8;
ALTER TABLE ion_sessions CONVERT TO CHARACTER SET utf8;
ALTER TABLE lang CONVERT TO CHARACTER SET utf8;
ALTER TABLE login_tracker CONVERT TO CHARACTER SET utf8;
ALTER TABLE media CONVERT TO CHARACTER SET utf8;
ALTER TABLE media_lang CONVERT TO CHARACTER SET utf8;
ALTER TABLE menu CONVERT TO CHARACTER SET utf8;
ALTER TABLE module CONVERT TO CHARACTER SET utf8;
ALTER TABLE module_setting CONVERT TO CHARACTER SET utf8;
ALTER TABLE note CONVERT TO CHARACTER SET utf8;
ALTER TABLE page CONVERT TO CHARACTER SET utf8;
ALTER TABLE page_article CONVERT TO CHARACTER SET utf8;
ALTER TABLE page_lang CONVERT TO CHARACTER SET utf8;
ALTER TABLE page_media CONVERT TO CHARACTER SET utf8;
ALTER TABLE setting CONVERT TO CHARACTER SET utf8;
ALTER TABLE tag CONVERT TO CHARACTER SET utf8;
ALTER TABLE tracker CONVERT TO CHARACTER SET utf8;
ALTER TABLE type CONVERT TO CHARACTER SET utf8;
ALTER TABLE url CONVERT TO CHARACTER SET utf8;
ALTER TABLE user_groups CONVERT TO CHARACTER SET utf8;
ALTER TABLE users CONVERT TO CHARACTER SET utf8;
